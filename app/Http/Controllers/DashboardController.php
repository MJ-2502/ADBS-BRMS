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
        $user = auth()->user();

        $certificateQuery = CertificateRequest::query();

        if (!$user->canManageRecords()) {
            $residentId = $user->residentProfile?->id;
            $certificateQuery->where(function ($query) use ($user, $residentId): void {
                $query->where('requested_by', $user->id);
                if ($residentId) {
                    $query->orWhere('resident_id', $residentId);
                }
            });
        }

        $residentRecordQuery = Resident::query()->whereNull('archived_at');
        $residentRecordCount = (clone $residentRecordQuery)->whereNull('user_id')->count();

        $stats = [
            'residents' => $user->canManageRecords()
                ? $residentRecordCount
                : ($user->residentProfile && is_null($user->residentProfile->archived_at) ? 1 : 0),
            'households' => $user->canManageRecords() ? Household::count() : 0,
            'pending_requests' => (clone $certificateQuery)->where('status', CertificateStatus::Pending)->count(),
            'requests_today' => (clone $certificateQuery)->whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentCertificates = (clone $certificateQuery)
            ->with(['resident', 'requester'])
            ->latest()
            ->limit(5)
            ->get();

        $recentActivities = $user->canManageRecords()
            ? ActivityLog::with('user')->latest()->limit(8)->get()
            : ActivityLog::with('user')->where('user_id', $user->id)->latest()->limit(8)->get();

        $populationByPurok = $user->canManageRecords()
            ? (clone $residentRecordQuery)
                ->select('purok', DB::raw('count(*) as total'))
                ->whereNull('user_id')
                ->whereNotNull('purok')
                ->groupBy('purok')
                ->orderBy('purok')
                ->get()
            : collect();

        $certificateMix = (clone $certificateQuery)
            ->select('certificate_type', DB::raw('count(*) as total'))
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
