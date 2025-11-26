<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\RegistrationRequest;
use App\Models\Resident;
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

        $pendingRequests = RegistrationRequest::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, $keyword): void {
                $keyword = '%' . $keyword . '%';
                $query->where(function ($subQuery) use ($keyword): void {
                    $subQuery->where('first_name', 'like', $keyword)
                        ->orWhere('last_name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('purok', 'like', $keyword);
                });
            })
            ->orderByRaw("FIELD(status, 'pending','rejected','approved')")
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('verifications.index', [
            'requests' => $pendingRequests,
            'filters' => $filters,
            'statuses' => VerificationStatus::cases(),
        ]);
    }

    public function approve(Request $request, RegistrationRequest $registrationRequest): RedirectResponse
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        abort_if($registrationRequest->status === VerificationStatus::Approved, 400, 'Already approved');

        $user = User::create([
            'name' => trim($registrationRequest->first_name . ' ' . $registrationRequest->last_name),
            'email' => $registrationRequest->email,
            'password' => $registrationRequest->password,
            'role' => UserRole::Resident->value,
            'phone' => $registrationRequest->contact_number,
            'purok' => $registrationRequest->purok,
            'address_line' => $registrationRequest->address_line,
            'verification_status' => VerificationStatus::Approved->value,
            'verification_notes' => $data['notes'] ?? null,
            'verification_proof_path' => $registrationRequest->proof_document_path,
            'is_active' => true,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        Resident::create([
            'user_id' => $user->id,
            'first_name' => $registrationRequest->first_name,
            'last_name' => $registrationRequest->last_name,
            'email' => $registrationRequest->email,
            'contact_number' => $registrationRequest->contact_number,
            'address_line' => $registrationRequest->address_line,
            'purok' => $registrationRequest->purok,
            'years_of_residency' => $registrationRequest->years_of_residency,
            'residency_status' => 'active',
        ]);

        $registrationRequest->update([
            'status' => VerificationStatus::Approved->value,
            'review_notes' => $data['notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'user_id' => $user->id,
        ]);

        $this->activityLogger->log('verification.approved', 'Registration request approved', [
            'registration_request_id' => $registrationRequest->id,
            'user_id' => $user->id,
        ]);

        return back()->with('status', $user->name . " has been approved and activated.");
    }

    public function reject(Request $request, RegistrationRequest $registrationRequest): RedirectResponse
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $registrationRequest->update([
            'status' => VerificationStatus::Rejected->value,
            'review_notes' => $data['notes'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $this->activityLogger->log('verification.rejected', 'Registration request rejected', [
            'registration_request_id' => $registrationRequest->id,
        ]);

        return back()->with('status', $registrationRequest->full_name . " was rejected.");
    }

    public function downloadProof(Request $request, RegistrationRequest $registrationRequest)
    {
        $this->ensureStaff($request);

        abort_if(!$registrationRequest->proof_document_path || !Storage::disk('local')->exists($registrationRequest->proof_document_path), 404);

        return Storage::disk('local')->download($registrationRequest->proof_document_path, basename($registrationRequest->proof_document_path));
    }
}
