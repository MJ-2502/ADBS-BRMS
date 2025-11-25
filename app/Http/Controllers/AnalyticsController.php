<?php

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Models\CertificateRequest;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function summary(): JsonResponse
    {
        $populationByAge = Resident::select(DB::raw('FLOOR(DATEDIFF(NOW(), birthdate) / 365) as age'), DB::raw('COUNT(*) as total'))
            ->whereNotNull('birthdate')
            ->groupBy('age')
            ->orderBy('age')
            ->get();

        $requestsPerStatus = CertificateRequest::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $topDocuments = CertificateRequest::select('certificate_type', DB::raw('COUNT(*) as total'))
            ->groupBy('certificate_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'population_by_age' => $populationByAge,
            'requests_per_status' => $requestsPerStatus,
            'top_documents' => $topDocuments,
            'pending_count' => $requestsPerStatus[CertificateStatus::Pending->value] ?? 0,
        ]);
    }
}
