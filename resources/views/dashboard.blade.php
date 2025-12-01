@extends('layouts.app')

@section('content')
@php($user = auth()->user())
@php($latestResidentRequest = !$user->canManageRecords() ? $recentCertificates->first() : null)

<div class="grid gap-4 sm:gap-6">
    @if(!$user->canManageRecords())
        <div class="rounded-3xl border border-emerald-100 bg-linear-to-r from-emerald-500 via-emerald-400 to-sky-500 px-6 py-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-wide text-emerald-50/90">Welcome back</p>
                    <h1 class="mt-1 text-2xl font-semibold">Hi, {{ $user->name }}!</h1>
                    <p class="mt-2 text-sm text-emerald-50">Track your certificate requests, submit a new one, or view released documents in one tap.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('certificates.create') }}" class="inline-flex items-center rounded-full bg-white/95 px-4 py-2 text-sm font-semibold text-emerald-600 shadow-sm transition hover:bg-white">Request certificate</a>
                        <a href="{{ route('certificates.index') }}" class="inline-flex items-center rounded-full border border-white/40 px-4 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/10">View my requests</a>
                    </div>
                </div>
                <div class="rounded-2xl bg-white/15 p-4 text-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70">Latest request</p>
                    @if($latestResidentRequest)
                        <p class="mt-2 text-lg font-semibold">{{ $latestResidentRequest->certificate_type->label() }}</p>
                        <p class="text-sm text-white/80">Reference: {{ $latestResidentRequest->reference_no }}</p>
                        <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $latestResidentRequest->status->badgeColor() }} bg-white/20 text-white">{{ str($latestResidentRequest->status->value)->headline() }}</span>
                    @else
                        <p class="mt-2 text-sm text-white/80">No requests yet. Start by submitting a new certificate request.</p>
                    @endif
                </div>
            </div>
        </div>

        @if($user->resident)
            <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">My Resident Profile</h2>
                    <span class="rounded-full bg-sky-500/20 px-3 py-1 text-xs font-semibold text-sky-300">Linked</span>
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <p class="text-xs text-slate-400">Reference ID</p>
                        <p class="mt-1 font-mono text-sm text-white">{{ $user->resident->reference_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Purok</p>
                        <p class="mt-1 text-sm text-white">{{ $user->resident->purok ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Years of Residency</p>
                        <p class="mt-1 text-sm text-white">{{ $user->resident->years_of_residency ?? 0 }} years</p>
                    </div>
                    @if($user->resident->household)
                        <div>
                            <p class="text-xs text-slate-400">Household</p>
                            <p class="mt-1 text-sm text-white">HH-{{ $user->resident->household->household_number }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-400">Residency Status</p>
                        <p class="mt-1 text-sm text-white">{{ str($user->resident->residency_status)->headline() }}</p>
                    </div>
                    @if($user->resident->is_voter)
                        <div>
                            <p class="text-xs text-slate-400">Voter Status</p>
                            <p class="mt-1 text-sm text-white">Registered{{ $user->resident->voter_precinct ? ' - Precinct ' . $user->resident->voter_precinct : '' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="rounded-3xl border border-slate-700 bg-linear-to-r from-slate-900 via-slate-800 to-slate-700 px-6 py-6 text-white shadow-lg">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70">Barangay overview</p>
                    <h1 class="mt-2 text-3xl font-semibold">Welcome back, {{ $user->name }}.</h1>
                    <p class="mt-3 text-sm text-white/80">Monitor certificates, residents, and households at a glance. Use the quick actions below to jump into the most common admin tasks.</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('residents.index') }}" class="inline-flex items-center rounded-full bg-white/95 px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-white">Manage residents</a>
                        <a href="{{ route('certificates.index') }}" class="inline-flex items-center rounded-full border border-white/40 px-4 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/10">Review requests</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($user->canManageRecords())
        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Residents</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-700/60 text-slate-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-white">{{ number_format($stats['residents']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Active resident</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Households</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-white">{{ number_format($stats['households']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Registered household</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pending requests</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/10 text-amber-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6v6a.75.75 0 00.75.75H18m3 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-white">{{ number_format($stats['pending_requests']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Awaiting barangay approval</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Requests today</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/10 text-sky-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25M3 18.75A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75M3 18.75V10.5A2.25 2.25 0 015.25 8.25h13.5A2.25 2.25 0 0121 10.5v8.25" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-white">{{ number_format($stats['requests_today']) }}</p>
                <p class="mt-1 text-sm text-slate-400">Submitted in the last 24h</p>
            </div>
        </div>
    @else
        @php($totalRequests = $monthlyRequests->sum('total'))
        @php($peakMonth = $monthlyRequests->sortByDesc('total')->first())
        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pending requests</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($stats['pending_requests']) }}</p>
                <p class="mt-2 text-sm text-slate-400">Waiting for the barangay office</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex h-2 w-2 rounded-full bg-amber-400"></span>
                    Updated {{ now()->format('M d, h:i A') }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Requests today</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($stats['requests_today']) }}</p>
                <p class="mt-2 text-sm text-slate-400">Filed within the last 24 hours</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex h-2 w-2 rounded-full bg-sky-400"></span>
                    {{ now()->format('F j, Y') }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-800/60 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Monthly trend</p>
                @if($peakMonth)
                    <p class="mt-3 text-2xl font-semibold text-white">{{ \Carbon\Carbon::createFromFormat('Y-m', $peakMonth->month)->format('M Y') }}</p>
                    <p class="text-sm text-slate-400">Peak month with {{ $peakMonth->total }} requests</p>
                    <ul class="mt-4 space-y-2 text-sm text-slate-300">
                        @foreach($monthlyRequests->sortByDesc('total')->take(3) as $row)
                            <li class="flex items-center justify-between">
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</span>
                                <span class="font-semibold">{{ $row->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No requests yet.</p>
                @endif
                <div class="mt-4 text-xs text-slate-500">
                    Last 6 months: <span class="font-semibold text-slate-800 dark:text-white">{{ number_format($totalRequests) }}</span> requests
                </div>
            </div>
        </div>
    @endif

    @if($user->canManageRecords())
        @php($analyticsYears = $certificateAnalyticsYears ?? [now()->year])
        @php($analyticsMonths = $certificateAnalyticsMonths ?? [])
        @php($analyticsDefaults = $certificateAnalyticsDefaults ?? ['year' => now()->year, 'month' => 'all', 'timeframe' => 'monthly'])
        <div class="rounded-3xl border border-slate-900/70 bg-slate-950/95 p-4 text-slate-100 shadow-2xl sm:p-6">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Analytics</p>
                        <h2 class="mt-1 text-2xl font-semibold text-white">Certificate Requests Overview</h2>
                        <p class="mt-1 text-sm text-slate-400">Spot demand spikes using timeframe, year, and month filters.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex rounded-full border border-slate-800 bg-slate-900/40 p-1 text-xs font-semibold shadow-inner shadow-slate-900" data-analytics-timeframe-group>
                            @php($defaultTimeframe = $analyticsDefaults['timeframe'] ?? 'monthly')
                            <button type="button" data-analytics-timeframe="daily" class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $defaultTimeframe === 'daily' ? 'bg-sky-500 text-white shadow shadow-sky-500/40' : 'text-slate-400 hover:text-white/80' }}">Daily</button>
                            <button type="button" data-analytics-timeframe="weekly" class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $defaultTimeframe === 'weekly' ? 'bg-sky-500 text-white shadow shadow-sky-500/40' : 'text-slate-400 hover:text-white/80' }}">Weekly</button>
                            <button type="button" data-analytics-timeframe="monthly" class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $defaultTimeframe === 'monthly' ? 'bg-sky-500 text-white shadow shadow-sky-500/40' : 'text-slate-400 hover:text-white/80' }}">Monthly</button>
                        </div>
                        <div class="rounded-2xl border border-slate-800/90 bg-slate-900/70 px-4 py-2 text-right shadow-lg shadow-sky-900/30">
                            <p class="text-[0.65rem] uppercase tracking-wide text-slate-500">Total</p>
                            <p class="text-3xl font-semibold text-white" data-analytics-summary-value>--</p>
                            <p class="text-xs text-slate-400" data-analytics-summary-label>Awaiting data</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <label class="flex flex-col text-xs font-semibold text-slate-400">
                        Year
                        <select data-analytics-year class="mt-1 rounded-xl border border-slate-800/80 bg-slate-900/70 px-3 py-2 text-sm text-white shadow-inner shadow-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                            @foreach($analyticsYears as $year)
                                <option value="{{ $year }}" @selected($year === ($analyticsDefaults['year'] ?? now()->year))>{{ $year }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col text-xs font-semibold text-slate-400">
                        Month
                        <select data-analytics-month class="mt-1 rounded-xl border border-slate-800/80 bg-slate-900/70 px-3 py-2 text-sm text-white shadow-inner shadow-slate-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                            <option value="all" @selected(($analyticsDefaults['month'] ?? 'all') === 'all')>All months</option>
                            @foreach($analyticsMonths as $monthOption)
                                <option value="{{ $monthOption['value'] }}" @selected(($analyticsDefaults['month'] ?? 'all') === $monthOption['value'])>{{ $monthOption['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div>
                    <div class="relative h-72 w-full overflow-hidden rounded-2xl border border-slate-900/60 bg-slate-950/60">
                        <canvas id="certificateRequestsOverviewChart" aria-label="Certificate Requests Overview"></canvas>
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500" data-analytics-empty hidden>No certificate activity for this filter yet.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid gap-4 sm:gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white sm:text-base">Latest certificate requests</h2>
                <a href="{{ route('certificates.index') }}" class="text-xs sm:text-sm text-sky-400">View all</a>
            </div>
            <div class="mt-3 sm:mt-4 space-y-2 sm:space-y-3 text-sm">
                @forelse($recentCertificates as $request)
                    <div class="flex flex-col gap-2 rounded-xl border border-slate-700 bg-slate-900/30 px-3 py-2.5 sm:flex-row sm:items-center sm:justify-between sm:px-4 sm:py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-white">{{ $request->resident?->full_name ?? 'N/A' }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $request->certificate_type->label() }} • {{ $request->reference_no }}</p>
                        </div>
                        <span class="inline-flex shrink-0 self-start rounded-full px-2.5 py-0.5 text-xs font-semibold sm:px-3 sm:py-1 {{ $request->status->badgeColor() }}">
                            {{ str($request->status->value)->headline() }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No requests yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white sm:text-base">Recent activity</h2>
                <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-300 transition hover:bg-slate-900 sm:hidden" data-recent-activity-toggle aria-controls="recentActivityPanel" aria-expanded="true">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                    </svg>
                    <span data-state="expanded">Hide</span>
                    <span data-state="collapsed" class="hidden">Show</span>
                </button>
            </div>
            <div id="recentActivityPanel" class="mt-3 sm:mt-4">
                @include('dashboard.partials.recent-activity-list')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" integrity="sha384-Sse/HDqcypGpyTDpvZOJNnG0TT3feGQUkF9H+mnRvic+LjR+K1NhTt8f51KIQ3v3" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('[data-recent-activity-toggle]');
        const panel = document.getElementById('recentActivityPanel');
        if (!toggle || !panel) {
            return;
        }

        const expandedLabel = toggle.querySelector('[data-state="expanded"]');
        const collapsedLabel = toggle.querySelector('[data-state="collapsed"]');
        const mediaQuery = window.matchMedia('(min-width: 640px)');

        const syncDesktopState = () => {
            if (mediaQuery.matches) {
                panel.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                if (expandedLabel && collapsedLabel) {
                    expandedLabel.classList.remove('hidden');
                    collapsedLabel.classList.add('hidden');
                }
            }
        };

        toggle.addEventListener('click', () => {
            if (mediaQuery.matches) {
                return;
            }
            const isHidden = panel.classList.toggle('hidden');
            const expanded = !isHidden;
            toggle.setAttribute('aria-expanded', expanded.toString());
            if (expandedLabel && collapsedLabel) {
                expandedLabel.classList.toggle('hidden', !expanded);
                collapsedLabel.classList.toggle('hidden', expanded);
            }
        });

        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', syncDesktopState);
        } else if (mediaQuery.addListener) {
            mediaQuery.addListener(syncDesktopState);
        }

        syncDesktopState();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const analyticsSeries = @json($certificateRequestSeries ?? ['daily' => [], 'weekly' => [], 'monthly' => []]);
        const analyticsDefaults = @json($certificateAnalyticsDefaults ?? ['year' => now()->year, 'month' => 'all', 'timeframe' => 'monthly']);
        const monthLabelMap = @json(collect($certificateAnalyticsMonths ?? [])->pluck('label', 'value'));

        const canvas = document.getElementById('certificateRequestsOverviewChart');
        if (typeof Chart === 'undefined' || !canvas) {
            return;
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) {
            return;
        }

        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.offsetHeight || 280);
        gradient.addColorStop(0, 'rgba(56, 189, 248, 0.4)');
        gradient.addColorStop(1, 'rgba(15, 23, 42, 0)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        data: [],
                        label: 'Requests',
                        borderColor: '#38bdf8',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#f472b6',
                        pointBorderWidth: 0,
                        pointHoverBorderWidth: 2,
                        pointHoverBorderColor: '#ffffff',
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart',
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        borderColor: '#38bdf8',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label(context) {
                                const value = context.parsed.y ?? 0;
                                return `${value.toLocaleString()} requests`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.15)',
                        },
                        ticks: {
                            color: '#94a3b8',
                            maxRotation: 0,
                            font: {
                                family: 'Inter, ui-sans-serif, system-ui',
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                        },
                        ticks: {
                            color: '#94a3b8',
                            callback(value) {
                                return Number.isInteger(value) ? value : null;
                            },
                        },
                    },
                },
            },
        });

        const timeframeButtons = document.querySelectorAll('[data-analytics-timeframe]');
        const yearSelect = document.querySelector('[data-analytics-year]');
        const monthSelect = document.querySelector('[data-analytics-month]');
        const summaryValue = document.querySelector('[data-analytics-summary-value]');
        const summaryLabel = document.querySelector('[data-analytics-summary-label]');
        const emptyState = document.querySelector('[data-analytics-empty]');

        const state = {
            timeframe: analyticsDefaults.timeframe || 'monthly',
            year: yearSelect ? yearSelect.value : analyticsDefaults.year,
            month: monthSelect ? monthSelect.value : (analyticsDefaults.month || 'all'),
        };

        const capitalize = (value) => value.charAt(0).toUpperCase() + value.slice(1);

        const formatSummaryLabel = () => {
            const timeframeLabel = capitalize(state.timeframe);
            if (!state.year) {
                return timeframeLabel;
            }
            if (state.month === 'all') {
                return `${timeframeLabel} • ${state.year}`;
            }
            const monthName = monthLabelMap[state.month] || state.month;
            return `${timeframeLabel} • ${monthName} ${state.year}`;
        };

        const setActiveTimeframe = () => {
            timeframeButtons.forEach((button) => {
                const value = button.getAttribute('data-analytics-timeframe');
                const isActive = value === state.timeframe;
                button.classList.toggle('bg-sky-500', isActive);
                button.classList.toggle('text-white', isActive);
                button.classList.toggle('shadow', isActive);
                button.classList.toggle('shadow-sky-500/40', isActive);
                button.classList.toggle('text-slate-400', !isActive);
            });
        };

        const updateChart = () => {
            const dataset = analyticsSeries[state.timeframe] || [];
            const filtered = dataset.filter((point) => {
                const matchesYear = state.year ? Number(point.year) === Number(state.year) : true;
                const matchesMonth = state.month === 'all' ? true : point.month === state.month;
                return matchesYear && matchesMonth;
            });

            if (filtered.length === 0) {
                chart.data.labels = [];
                chart.data.datasets[0].data = [];
                chart.update();
                if (emptyState) {
                    emptyState.classList.remove('hidden');
                }
                if (summaryValue) {
                    summaryValue.textContent = '0';
                }
                if (summaryLabel) {
                    summaryLabel.textContent = formatSummaryLabel();
                }
                return;
            }

            if (emptyState) {
                emptyState.classList.add('hidden');
            }

            chart.data.labels = filtered.map((point) => point.label);
            chart.data.datasets[0].data = filtered.map((point) => point.value);
            chart.update();

            const total = filtered.reduce((sum, point) => sum + Number(point.value || 0), 0);
            if (summaryValue) {
                summaryValue.textContent = total.toLocaleString();
            }
            if (summaryLabel) {
                summaryLabel.textContent = formatSummaryLabel();
            }
        };

        timeframeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const nextTimeframe = button.getAttribute('data-analytics-timeframe');
                if (!nextTimeframe || nextTimeframe === state.timeframe) {
                    return;
                }
                state.timeframe = nextTimeframe;
                setActiveTimeframe();
                updateChart();
            });
        });

        if (yearSelect) {
            yearSelect.addEventListener('change', (event) => {
                state.year = event.target.value;
                updateChart();
            });
        }

        if (monthSelect) {
            monthSelect.addEventListener('change', (event) => {
                state.month = event.target.value;
                updateChart();
            });
        }

        setActiveTimeframe();
        updateChart();
    });
</script>
@endpush
