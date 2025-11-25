<?php

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Models\ActivityLog;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'residents' => Resident::count(),
            'households' => Household::count(),
            'pending_requests' => CertificateRequest::where('status', CertificateStatus::Pending)->count(),
            'requests_today' => CertificateRequest::whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentCertificates = CertificateRequest::with(['resident', 'requester'])
            ->latest()
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')->latest()->limit(8)->get();

        $populationByPurok = Resident::select('purok', DB::raw('count(*) as total'))
            ->whereNotNull('purok')
            ->groupBy('purok')
            ->orderBy('purok')
            ->get();

        $certificateMix = CertificateRequest::select('certificate_type', DB::raw('count(*) as total'))
            ->groupBy('certificate_type')
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'recentCertificates' => $recentCertificates,
            'recentActivities' => $recentActivities,
            'populationByPurok' => $populationByPurok,
            'certificateMix' => $certificateMix,
        ]);
    }
}
