@extends('layouts.app')

@section('content')
<div class="grid gap-4 sm:gap-6">
    <div class="grid gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard-stat label="Residents" :value="$stats['residents']" />
        <x-dashboard-stat label="Households" :value="$stats['households']" />
        <x-dashboard-stat label="Pending Requests" :value="$stats['pending_requests']" />
        <x-dashboard-stat label="Requests Today" :value="$stats['requests_today']" />
    </div>

    <div class="grid gap-4 sm:gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 lg:col-span-2 dark:border-slate-800 dark:bg-slate-800/50">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Latest certificate requests</h2>
                <a href="{{ route('certificates.index') }}" class="text-xs sm:text-sm text-sky-600 dark:text-sky-400">View all</a>
            </div>
            <div class="mt-3 sm:mt-4 space-y-2 sm:space-y-3 text-sm">
                @forelse($recentCertificates as $request)
                    <div class="flex flex-col gap-2 rounded-xl border border-slate-100 px-3 py-2.5 sm:flex-row sm:items-center sm:justify-between sm:px-4 sm:py-3 dark:border-slate-700 dark:bg-slate-900/30">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-slate-800 dark:text-white">{{ $request->resident?->full_name ?? 'N/A' }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $request->certificate_type->label() }} • {{ $request->reference_no }}</p>
                        </div>
                        <span class="inline-flex shrink-0 self-start rounded-full px-2.5 py-0.5 text-xs font-semibold sm:px-3 sm:py-1 {{ $request->status->badgeColor() }}">
                            {{ str($request->status->value)->headline() }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No requests yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Recent activity</h2>
            <div class="mt-3 sm:mt-4 space-y-2 sm:space-y-3 text-sm">
                @foreach($recentActivities as $log)
                    <div class="rounded-xl border border-slate-100 px-3 py-2.5 sm:px-4 sm:py-3 dark:border-slate-700 dark:bg-slate-900/30">
                        <p class="font-medium text-slate-800 dark:text-white">{{ $log->event }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $log->created_at->diffForHumans() }} • {{ $log->user?->name }}</p>
                        @if($log->description)
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $log->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Residents by Purok</h2>
            <div class="mt-3 sm:mt-4 space-y-2 text-sm">
                @forelse($populationByPurok as $row)
                    <div class="flex items-center justify-between dark:text-slate-200">
                        <span>{{ $row->purok }}</span>
                        <span class="font-semibold">{{ $row->total }}</span>
                    </div>
                @empty
                    <p class="text-slate-500 dark:text-slate-400">No data yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Certificate mix</h2>
            <div class="mt-3 sm:mt-4 space-y-2 text-sm">
                @forelse($certificateMix as $row)
                    <div class="flex items-center justify-between dark:text-slate-200">
                        <span>
                            {{
                                $row->certificate_type instanceof \App\Enums\CertificateType
                                    ? $row->certificate_type->label()
                                    : str($row->certificate_type)->headline()
                            }}
                        </span>
                        <span class="font-semibold">{{ $row->total }}</span>
                    </div>
                @empty
                    <p class="text-slate-500 dark:text-slate-400">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
