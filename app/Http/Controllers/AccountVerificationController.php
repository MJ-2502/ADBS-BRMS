<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountVerificationController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    protected function ensureStaff(Request $request): void
    {
        abort_unless($request->user()?->canManageRecords(), 403);
    }

    public function index(Request $request): View
    {
        $this->ensureStaff($request);

        $filters = $request->only('status', 'search');

        $pendingUsers = User::with('residentProfile')
            ->where('role', UserRole::Resident)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('verification_status', $status))
            ->when($filters['search'] ?? null, function ($query, $keyword): void {
                $keyword = '%' . $keyword . '%';
                $query->where(function ($subQuery) use ($keyword): void {
                    $subQuery->where('name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhereHas('residentProfile', function ($residentQuery) use ($keyword): void {
                            $residentQuery->where('reference_id', 'like', $keyword)
                                ->orWhere('purok', 'like', $keyword);
                        });
                });
            })
            ->orderByRaw("FIELD(verification_status, 'pending','rejected','approved')")
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('verifications.index', [
            'users' => $pendingUsers,
            'filters' => $filters,
            'statuses' => VerificationStatus::cases(),
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaff($request);
        abort_unless($user->role === UserRole::Resident, 403);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'verification_status' => VerificationStatus::Approved->value,
            'verification_notes' => $data['notes'] ?? null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $this->activityLogger->log('verification.approved', 'Account verification approved', [
            'user_id' => $user->id,
        ]);

        return back()->with('status', $user->name . " has been approved.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->ensureStaff($request);
        abort_unless($user->role === UserRole::Resident, 403);

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'verification_status' => VerificationStatus::Rejected->value,
            'verification_notes' => $data['notes'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $this->activityLogger->log('verification.rejected', 'Account verification rejected', [
            'user_id' => $user->id,
        ]);

        return back()->with('status', $user->name . " was rejected.");
    }

    public function downloadProof(Request $request, User $user)
    {
        $this->ensureStaff($request);
        abort_unless($user->role === UserRole::Resident, 403);

        abort_if(!$user->verification_proof_path || !Storage::disk('local')->exists($user->verification_proof_path), 404);

        return Storage::disk('local')->download($user->verification_proof_path, basename($user->verification_proof_path));
    }
}
