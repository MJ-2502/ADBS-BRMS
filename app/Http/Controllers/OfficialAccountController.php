<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Requests\Account\StoreOfficialAccountRequest;
use App\Http\Requests\Account\UpdateOfficialAccountRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class OfficialAccountController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()?->canManageRecords(), 403);

        $filters = $request->only('search', 'role');

        $officials = User::query()
            ->whereIn('role', UserRole::staffRoles())
            ->when($filters['role'] ?? null, fn (Builder $query, string $role): Builder => $query->where('role', $role))
            ->when($filters['search'] ?? null, function (Builder $query, string $keyword): void {
                $like = '%' . $keyword . '%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('accounts.officials', [
            'officials' => $officials,
            'filters' => $filters,
            'roles' => [UserRole::Admin, UserRole::Clerk],
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('accounts.officials-create', [
            'roles' => [UserRole::Admin, UserRole::Clerk],
        ]);
    }

    public function store(StoreOfficialAccountRequest $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'purok' => $data['purok'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'verification_status' => VerificationStatus::Approved,
            'is_active' => true,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $this->activityLogger->log('accounts.official.created', 'Staff account created', [
            'official_id' => $user->id,
            'role' => $user->role->value,
        ]);

        return redirect()->route('accounts.officials.index')->with('status', $user->name . ' account added.');
    }

    public function edit(Request $request, User $official): View
    {
        $this->ensureStaff($request, $official);

        return view('accounts.officials-edit', [
            'official' => $official,
            'roles' => [UserRole::Admin, UserRole::Clerk],
        ]);
    }

    public function update(UpdateOfficialAccountRequest $request, User $official): RedirectResponse
    {
        $this->ensureStaff($request, $official);

        $data = $request->validated();

        $official->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'purok' => $data['purok'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        if (!empty($data['password'])) {
            $official->password = $data['password'];
        }

        $official->save();

        $this->activityLogger->log('accounts.official.updated', 'Staff account updated', [
            'official_id' => $official->id,
        ]);

        return redirect()->route('accounts.officials.index')->with('status', $official->name . ' account updated.');
    }

    public function destroy(Request $request, User $official): RedirectResponse
    {
        $this->ensureStaff($request, $official);

        abort_if($official->id === $request->user()->id, 403, 'You cannot delete the account currently in use.');

        $official->delete();

        $this->activityLogger->log('accounts.official.deleted', 'Staff account deleted', [
            'official_id' => $official->id,
        ]);

        return redirect()->route('accounts.officials.index')->with('status', 'Staff account removed.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->canManageAccounts(), 403);
    }

    private function ensureStaff(Request $request, User $official): void
    {
        $this->ensureAdmin($request);

        abort_if(!in_array($official->role->value, UserRole::staffRoles(), true), 404);

        $defaultAdminEmail = Config::get('app.auto_admin.email');
        if ($defaultAdminEmail && strcasecmp($official->email, $defaultAdminEmail) === 0) {
            abort(403, 'Default admin credentials cannot be modified.');
        }
    }
}
