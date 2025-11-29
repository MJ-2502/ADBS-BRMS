<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Requests\Account\StoreResidentAccountRequest;
use App\Http\Requests\Account\UpdateResidentAccountRequest;
use App\Models\RegistrationRequest;
use App\Models\Resident;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResidentAccountController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    private function ensureStaff(Request $request): void
    {
        abort_unless($request->user()?->canManageRecords(), 403);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->canManageAccounts(), 403);
    }

    public function index(Request $request): View
    {
        $this->ensureStaff($request);

        $filters = $request->only('search', 'status');

        $accounts = Resident::with('user')
            ->whereNotNull('user_id')
            ->when($filters['search'] ?? null, function (Builder $query, string $keyword): void {
                $like = '%' . $keyword . '%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('reference_id', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('purok', 'like', $like)
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('email', 'like', $like));
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                $status = strtolower($status);
                if ($status === 'active') {
                    $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', true));
                } elseif ($status === 'inactive') {
                    $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', false));
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(12)
            ->withQueryString();

        return view('accounts.residents', [
            'accounts' => $accounts,
            'filters' => $filters,
            'statusOptions' => [
                'active' => 'Active',
                'inactive' => 'Disabled',
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        $availableResidents = Resident::whereNull('user_id')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $selectedResident = null;
        if ($request->filled('resident')) {
            $selectedResident = $availableResidents->firstWhere('id', (int) $request->input('resident'));
        }

        return view('accounts.residents-create', [
            'availableResidents' => $availableResidents,
            'selectedResident' => $selectedResident,
        ]);
    }

    public function store(StoreResidentAccountRequest $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validated();

        $resident = Resident::whereNull('user_id')->findOrFail($data['resident_id']);

        $user = null;

        DB::transaction(function () use ($request, $data, $resident, &$user): void {
            $user = User::create([
                'name' => $resident->full_name,
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::Resident,
                'phone' => $data['phone'] ?? null,
                'purok' => $data['purok'] ?? $resident->purok,
                'address_line' => $data['address_line'] ?? $resident->address_line,
                'verification_status' => VerificationStatus::Approved,
                'is_active' => (bool) $data['is_active'],
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            $resident->update([
                'user_id' => $user->id,
                'email' => $data['email'],
                'contact_number' => $data['phone'] ?? $resident->contact_number,
                'address_line' => $data['address_line'] ?? $resident->address_line,
                'purok' => $data['purok'] ?? $resident->purok,
            ]);
        });

        $this->activityLogger->log('accounts.resident.created', 'Resident account created', [
            'resident_id' => $resident->id,
            'user_id' => $user?->id,
        ]);

        return redirect()->route('accounts.residents.index')->with('status', $resident->full_name . ' account created.');
    }

    public function edit(Request $request, Resident $resident): View
    {
        $this->ensureAdmin($request);

        abort_unless($resident->user, 404);

        return view('accounts.residents-edit', [
            'resident' => $resident->load('user'),
        ]);
    }

    public function update(UpdateResidentAccountRequest $request, Resident $resident): RedirectResponse
    {
        $this->ensureAdmin($request);

        $user = $resident->user;

        abort_unless($user, 404);

        $data = $request->validated();

        $user->fill([
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'purok' => $data['purok'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'is_active' => (bool) $data['is_active'],
        ]);

        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $resident->fill([
            'email' => $data['email'],
            'contact_number' => $data['phone'] ?? $resident->contact_number,
            'address_line' => $data['address_line'] ?? $resident->address_line,
            'purok' => $data['purok'] ?? $resident->purok,
        ])->save();

        $this->activityLogger->log('accounts.resident.updated', 'Resident account updated', [
            'resident_id' => $resident->id,
        ]);

        return redirect()->route('accounts.residents.index')->with('status', $resident->full_name . ' account updated.');
    }

    public function destroy(Request $request, Resident $resident): RedirectResponse
    {
        $this->ensureAdmin($request);

        $user = $resident->user;

        abort_unless($user, 404);

        DB::transaction(function () use ($resident, $user): void {
            RegistrationRequest::where('user_id', $user->id)->update(['user_id' => null]);

            $resident->update(['user_id' => null]);

            $user->delete();
        });

        $this->activityLogger->log('accounts.resident.deleted', 'Resident account removed', [
            'resident_id' => $resident->id,
        ]);

        return redirect()->route('accounts.residents.index')->with('status', 'Resident account removed.');
    }

    public function downloadProof(Request $request, Resident $resident)
    {
        $this->ensureStaff($request);

        $user = $resident->user;

        abort_if(!$user?->verification_proof_path || !Storage::disk('local')->exists($user->verification_proof_path), 404);

        return Storage::disk('local')->download($user->verification_proof_path, basename($user->verification_proof_path));
    }
}
