<?php

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Models\ActivityLog;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $now = now();

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
            'requests_today' => (clone $certificateQuery)->whereDate('created_at', $now->toDateString())->count(),
        ];

        $recentCertificates = (clone $certificateQuery)
            ->with(['resident', 'requester'])
            ->latest()
            ->limit(5)
            ->get();

        $recentActivities = $user->canManageRecords()
            ? ActivityLog::with('user')->latest()->limit(8)->get()
            : ActivityLog::with('user')->where('user_id', $user->id)->latest()->limit(8)->get();

        $driver = DB::getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $monthlyRangeMonths = 6;
        $monthlyRangeStart = $now->copy()->subMonths($monthlyRangeMonths - 1)->startOfMonth();

        $monthlyRequests = (clone $certificateQuery)
            ->selectRaw("{$monthExpression} as month")
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>=', $monthlyRangeStart)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $dailyExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m-%d', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM-DD')",
            default => "DATE_FORMAT(created_at, '%Y-%m-%d')",
        };

        $dailyDaysToDisplay = 30;
        $weeklyWeeksToDisplay = 12;

        $dailyRangeEnd = $now->copy()->endOfDay();
        $dailyRangeStart = $dailyRangeEnd->copy()->subDays($dailyDaysToDisplay - 1)->startOfDay();

        $weeklyRangeEnd = $now->copy()->endOfWeek();
        $weeklyRangeStart = $weeklyRangeEnd->copy()->subWeeks($weeklyWeeksToDisplay - 1)->startOfWeek();

        $seriesRangeStart = $dailyRangeStart->lessThan($weeklyRangeStart)
            ? $dailyRangeStart->copy()
            : $weeklyRangeStart->copy();

        $dailyRequestRows = (clone $certificateQuery)
            ->selectRaw("{$dailyExpression} as day_key")
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>=', $seriesRangeStart)
            ->groupByRaw($dailyExpression)
            ->orderBy('day_key')
            ->get();

        $dailyTotalsByDate = $dailyRequestRows->mapWithKeys(fn ($row) => [$row->day_key => (int) $row->total]);

        $dailySeries = collect();
        $dailyCursor = $dailyRangeStart->copy();
        while ($dailyCursor->lessThanOrEqualTo($dailyRangeEnd)) {
            $labelDate = $dailyCursor->copy();
            $dateKey = $labelDate->toDateString();
            $dailySeries->push([
                'label' => $labelDate->format('M d'),
                'value' => $dailyTotalsByDate[$dateKey] ?? 0,
                'year' => $labelDate->year,
                'month' => $labelDate->format('m'),
                'raw' => $dateKey,
            ]);
            $dailyCursor->addDay();
        }

        $weeklySeries = collect();
        $weeklyCursor = $weeklyRangeStart->copy();
        while ($weeklyCursor->lessThanOrEqualTo($weeklyRangeEnd)) {
            $weekStart = $weeklyCursor->copy();
            $weekEnd = $weeklyCursor->copy()->endOfWeek();

            $value = 0;
            $weekDayCursor = $weekStart->copy();
            while ($weekDayCursor->lessThanOrEqualTo($weekEnd)) {
                $value += $dailyTotalsByDate[$weekDayCursor->toDateString()] ?? 0;
                $weekDayCursor->addDay();
            }

            $weeklySeries->push([
                'label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'value' => $value,
                'year' => $weekStart->year,
                'month' => $weekStart->format('m'),
                'raw' => $weekStart->toDateString(),
            ]);

            $weeklyCursor->addWeek();
        }

        $monthlyTotals = $monthlyRequests->mapWithKeys(fn ($row) => [$row->month => (int) $row->total]);
        $monthlyRangeEnd = $now->copy()->startOfMonth();
        $monthlySeries = collect();
        $monthlyCursor = $monthlyRangeStart->copy();

        while ($monthlyCursor->lessThanOrEqualTo($monthlyRangeEnd)) {
            $monthKey = $monthlyCursor->format('Y-m');
            $monthlySeries->push([
                'label' => $monthlyCursor->format('M Y'),
                'value' => $monthlyTotals[$monthKey] ?? 0,
                'year' => $monthlyCursor->year,
                'month' => $monthlyCursor->format('m'),
                'raw' => $monthKey,
            ]);
            $monthlyCursor->addMonth();
        }

        $certificateRequestSeries = [
            'daily' => $dailySeries->values()->all(),
            'weekly' => $weeklySeries->values()->all(),
            'monthly' => $monthlySeries->values()->all(),
        ];

        $availableYears = $dailySeries->pluck('year')
            ->merge($monthlySeries->pluck('year'))
            ->unique()
            ->sort()
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$now->year]);
        }

        $certificateAnalyticsMonths = collect(range(1, 12))->map(function ($month) {
            $date = Carbon::createFromFormat('!m', (string) $month);
            return [
                'value' => str_pad((string) $month, 2, '0', STR_PAD_LEFT),
                'label' => $date->format('F'),
            ];
        })->all();

        $certificateAnalyticsDefaults = [
            'year' => $availableYears->last(),
            'month' => 'all',
            'timeframe' => 'monthly',
        ];

        return view('dashboard', [
            'stats' => $stats,
            'recentCertificates' => $recentCertificates,
            'recentActivities' => $recentActivities,
            'monthlyRequests' => $monthlyRequests,
            'certificateRequestSeries' => $certificateRequestSeries,
            'certificateAnalyticsYears' => $availableYears->all(),
            'certificateAnalyticsMonths' => $certificateAnalyticsMonths,
            'certificateAnalyticsDefaults' => $certificateAnalyticsDefaults,
        ]);
    }
}
