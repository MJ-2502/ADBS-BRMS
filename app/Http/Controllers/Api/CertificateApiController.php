<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Models\CertificateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = CertificateRequest::with('resident:id,first_name,last_name')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($requests);
    }

    public function store(StoreCertificateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (!$user->canManageRecords()) {
            $resident = $user->residentProfile;
            abort_if(!$resident, 422, 'No resident profile linked to your account.');
            $data['resident_id'] = $resident->id;
        }

        $data['requested_by'] = $user->id;
        $certificate = CertificateRequest::create($data);

        return response()->json($certificate->load('resident'), 201);
    }
}
