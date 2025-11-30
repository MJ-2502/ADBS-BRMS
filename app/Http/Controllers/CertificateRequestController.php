<?php

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Http\Requests\Certificate\UpdateCertificateRequest;
use App\Http\Requests\Certificate\UpdateCertificateStatusRequest;
use App\Models\CertificateFee;
use App\Models\CertificateRequest;
use App\Models\Resident;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CertificateRequestController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = CertificateRequest::with(['resident', 'requester', 'approver'])
            ->when(!$user->canManageRecords(), function ($builder) use ($user): void {
                $residentId = $user->residentProfile?->id;
                $builder->where(function ($query) use ($user, $residentId): void {
                    $query->where('requested_by', $user->id);
                    if ($residentId) {
                        $query->orWhere('resident_id', $residentId);
                    }
                });
            })
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($builder) => $builder->where('certificate_type', $request->string('type')))
            ->when($request->filled('search'), function ($builder) use ($request): void {
                $keyword = '%' . $request->string('search') . '%';
                $builder->where(function ($query) use ($keyword): void {
                    $query->where('reference_no', 'like', $keyword)
                        ->orWhereHas('resident', function ($residentQuery) use ($keyword): void {
                            $residentQuery->where('first_name', 'like', $keyword)
                                ->orWhere('last_name', 'like', $keyword);
                        });
                });
            })
            ->latest();

        return view('certificates.index', [
            'requests' => $query->paginate(15)->withQueryString(),
            'certificateTypes' => CertificateType::options(),
            'statuses' => collect(CertificateStatus::cases())->map(fn ($status) => ['value' => $status->value, 'label' => str($status->value)->headline()])->all(),
            'filters' => $request->only(['search', 'status', 'type']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('certificates.create', [
            'fees' => CertificateFee::feeMap(),
            'formSchemas' => config('certificate_forms'),
        ]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $details = $data['details'] ?? [];
        unset($data['details']);

        $data['resident_id'] = $this->resolveResidentId($data, $user);
        unset($data['resident_first_name'], $data['resident_middle_initial'], $data['resident_last_name']);
        $data['fee'] = CertificateFee::amountFor($data['certificate_type']);
        $data['requested_by'] = $user->id;
        $data['payload'] = empty($details) ? null : $details;

        if (!empty($details)) {
            $data['details_submitted_by'] = $user->id;
            $data['details_submitted_at'] = now();
        }

        $certificateRequest = CertificateRequest::create($data);

        $this->activityLogger->log('certificate.created', 'Certificate requested', [
            'certificate_request_id' => $certificateRequest->id,
        ]);

        return redirect()->route('certificates.index')->with('status', 'Request submitted.');
    }

    public function show(CertificateRequest $certificate): View
    {
        return view('certificates.show', [
            'certificate' => $certificate->load(['resident', 'requester', 'approver', 'detailsSubmitter']),
            'formSchemas' => config('certificate_forms'),
        ]);
    }

    public function edit(Request $request, CertificateRequest $certificate): View
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureEditableByUser($certificate, $user);

        return view('certificates.edit', [
            'certificate' => $certificate->load('resident'),
            'fees' => CertificateFee::feeMap(),
            'formSchemas' => config('certificate_forms'),
        ]);
    }

    public function update(UpdateCertificateRequest $request, CertificateRequest $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureEditableByUser($certificate, $user);

        $data = $request->validated();
        $details = $data['details'] ?? [];
        unset($data['details']);

        $updates = [
            'certificate_type' => $data['certificate_type'],
            'purpose' => $data['purpose'],
            'remarks' => $data['remarks'] ?? null,
            'fee' => CertificateFee::amountFor($data['certificate_type']),
            'resident_id' => $this->resolveResidentId($data, $user),
            'payload' => empty($details) ? null : $details,
            'details_submitted_by' => empty($details) ? null : $user->id,
            'details_submitted_at' => empty($details) ? null : now(),
        ];

        $certificate->update($updates);

        $this->activityLogger->log('certificate.updated_details', 'Certificate details updated', [
            'certificate_request_id' => $certificate->id,
        ]);

        return redirect()->route('certificates.show', $certificate)->with('status', 'Certificate request updated.');
    }

    public function updateStatus(UpdateCertificateStatusRequest $request, CertificateRequest $certificate): RedirectResponse
    {
        abort_unless($request->user()->canManageRecords(), 403);

        $data = $request->validated();

        if ($data['status'] === CertificateStatus::Released->value && !$certificate->detailsAreComplete()) {
            return back()
                ->withErrors(['status' => 'Resident-submitted details are required before releasing the certificate.'])
                ->withInput();
        }

        $updates = [
            'status' => $data['status'],
            'remarks' => $data['remarks'],
        ];

        if ($data['status'] === CertificateStatus::Approved->value) {
            $updates['approved_by'] = $request->user()->id;
            $updates['approved_at'] = now();
        }

        if ($data['status'] === CertificateStatus::Released->value) {
            $updates['released_at'] = now();
        }

        $certificate->update($updates);

        $this->activityLogger->log('certificate.updated', 'Certificate status updated', [
            'certificate_request_id' => $certificate->id,
            'status' => $data['status'],
        ]);

        return redirect()->route('certificates.show', $certificate)->with('status', 'Certificate updated.');
    }

    public function destroy(Request $request, CertificateRequest $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureDeletableByUser($certificate, $user);

        $certificate->delete();

        $this->activityLogger->log('certificate.deleted', 'Certificate request deleted', [
            'certificate_request_id' => $certificate->id,
        ]);

        return redirect()->route('certificates.index')->with('status', 'Certificate request deleted.');
    }

    private function resolveResidentId(array $data, User $user): int
    {
        if ($user->canManageRecords()) {
            $firstName = trim((string) ($data['resident_first_name'] ?? ''));
            $lastName = trim((string) ($data['resident_last_name'] ?? ''));
            $middleInitial = trim((string) ($data['resident_middle_initial'] ?? ''));

            if ($firstName === '' || $lastName === '') {
                throw ValidationException::withMessages([
                    'resident_first_name' => 'Resident first and last name are required.',
                ]);
            }

            $query = Resident::query()
                ->whereRaw('LOWER(first_name) = ?', [mb_strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [mb_strtolower($lastName)]);

            if ($middleInitial !== '') {
                $initial = mb_strtolower(mb_substr($middleInitial, 0, 1));
                $query->where(function ($inner) use ($initial): void {
                    $inner->whereNull('middle_name')
                        ->orWhereRaw('LOWER(LEFT(middle_name, 1)) = ?', [$initial]);
                });
            }

            $matches = $query->get();

            if ($matches->isEmpty()) {
                throw ValidationException::withMessages([
                    'resident_first_name' => 'No resident matched the provided name. Check the spelling and try again.',
                ]);
            }

            if ($matches->count() > 1) {
                throw ValidationException::withMessages([
                    'resident_first_name' => 'Multiple residents matched the provided name. Please include the middle initial or update the resident profile to be more specific.',
                ]);
            }

            return $matches->first()->id;
        }

        $resident = $user->residentProfile;
        abort_if(!$resident, 403, 'No resident profile found.');

        return $resident->id;
    }

    private function ensureEditableByUser(CertificateRequest $certificate, User $user): void
    {
        if ($user->canManageRecords()) {
            return;
        }

        abort_unless($certificate->requested_by === $user->id, 403);
        abort_unless($this->isEditableStatus($certificate), 403, 'Request can no longer be edited.');
    }

    private function ensureDeletableByUser(CertificateRequest $certificate, User $user): void
    {
        if ($user->canManageRecords()) {
            return;
        }

        abort_unless($certificate->requested_by === $user->id, 403);
        abort_unless($this->isEditableStatus($certificate), 403, 'Request can no longer be deleted.');
    }

    private function isEditableStatus(CertificateRequest $certificate): bool
    {
        return in_array($certificate->status, [CertificateStatus::Pending, CertificateStatus::ForReview], true);
    }

}
