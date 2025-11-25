<?php

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Http\Requests\Certificate\UpdateCertificateStatusRequest;
use App\Models\CertificateRequest;
use App\Models\Resident;
use App\Services\ActivityLogger;
use App\Services\CertificatePdfService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateRequestController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly CertificatePdfService $certificatePdfService
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
        $user = $request->user();
        $residents = $user->canManageRecords()
            ? Resident::orderBy('last_name')->get()
            : Resident::where('user_id', $user->id)->get();

        return view('certificates.create', [
            'residents' => $residents,
        ]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (!$user->canManageRecords()) {
            $resident = $user->residentProfile;
            abort_if(!$resident, 403, 'No resident profile found.');
            $data['resident_id'] = $resident->id;
        }

        $data['requested_by'] = $user->id;
        $certificateRequest = CertificateRequest::create($data);

        $this->activityLogger->log('certificate.created', 'Certificate requested', [
            'certificate_request_id' => $certificateRequest->id,
        ]);

        return redirect()->route('certificates.index')->with('status', 'Request submitted.');
    }

    public function show(CertificateRequest $certificate): View
    {
        return view('certificates.show', [
            'certificate' => $certificate->load(['resident', 'requester', 'approver']),
        ]);
    }

    public function update(UpdateCertificateStatusRequest $request, CertificateRequest $certificate): RedirectResponse
    {
        abort_unless($request->user()->canManageRecords(), 403);

        $data = $request->validated();
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

        if ($data['status'] === CertificateStatus::Released->value) {
            $pdfPath = $this->certificatePdfService->generate($certificate->fresh());
            $certificate->update(['pdf_path' => $pdfPath]);
        }

        $this->activityLogger->log('certificate.updated', 'Certificate status updated', [
            'certificate_request_id' => $certificate->id,
            'status' => $data['status'],
        ]);

        return redirect()->route('certificates.show', $certificate)->with('status', 'Certificate updated.');
    }

    public function download(Request $request, CertificateRequest $certificate)
    {
        abort_if(!$certificate->pdf_path, 404);

        if (!$request->user()->canManageRecords()) {
            $allowedIds = collect([$certificate->requested_by, $certificate->resident?->user_id])->filter()->all();
            abort_unless(in_array($request->user()->id, $allowedIds, true), 403);
        }

        return response()->download(storage_path('app/' . $certificate->pdf_path));
    }
}
