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
                    <p class="text-xs uppercase tracking-wide text-white/70">Latest request</p>
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
    @else
        <div class="rounded-3xl border border-slate-200 bg-linear-to-r from-slate-900 via-slate-800 to-slate-700 px-6 py-6 text-white shadow-lg dark:border-slate-700">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/70">Barangay overview</p>
                    <h1 class="mt-2 text-3xl font-semibold">Welcome back, {{ $user->name }}.</h1>
                    <p class="mt-3 text-sm text-white/80">Monitor certificates, residents, and households at a glance. Use the quick actions below to jump into the most common admin tasks.</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('residents.index') }}" class="inline-flex items-center rounded-full bg-white/95 px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-white">Manage residents</a>
                        <a href="{{ route('certificates.index') }}" class="inline-flex items-center rounded-full border border-white/40 px-4 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/10">Review requests</a>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-inner">
                        <p class="text-xs uppercase tracking-wide text-white/80">Requests today</p>
                        <p class="mt-3 text-3xl font-semibold">{{ number_format($stats['requests_today']) }}</p>
                        <p class="mt-1 text-xs text-white/70">Filed since midnight</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-inner">
                        <p class="text-xs uppercase tracking-wide text-white/80">Pending queue</p>
                        <p class="mt-3 text-3xl font-semibold">{{ number_format($stats['pending_requests']) }}</p>
                        <p class="mt-1 text-xs text-white/70">Awaiting processing</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($user->canManageRecords())
        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Residents</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700/60 dark:text-slate-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.127a9.022 9.022 0 003.475-.668A2.25 2.25 0 0019.5 16.35v-.109a4.5 4.5 0 00-2.035-3.757l-.318-.21a1.012 1.012 0 01-.441-.838v-.346a5.25 5.25 0 10-10.5 0v.346c0 .339-.164.656-.441.838l-.318.21A4.5 4.5 0 003 16.24v.109a2.25 2.25 0 001.025 1.93A9.015 9.015 0 007.5 19.13m7.5-.003a9.06 9.06 0 01-7.5.003" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['residents']) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Active resident profiles</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Households</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-700/60 dark:text-slate-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['households']) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Registered household records</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending requests</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-500 dark:bg-amber-500/10 dark:text-amber-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6v6a.75.75 0 00.75.75H18m3 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['pending_requests']) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Awaiting barangay approval</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Requests today</p>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-sky-500 dark:bg-sky-500/10 dark:text-sky-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25M3 18.75A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75M3 18.75V10.5A2.25 2.25 0 015.25 8.25h13.5A2.25 2.25 0 0121 10.5v8.25" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['requests_today']) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Submitted in the last 24h</p>
            </div>
        </div>
    @else
        @php($topCertificate = $certificateMix->sortByDesc('total')->first())
        @php($certificateSum = $certificateMix->sum('total'))
        <div class="grid gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending requests</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['pending_requests']) }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Waiting for the barangay office</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex h-2 w-2 rounded-full bg-amber-400"></span>
                    Updated {{ now()->format('M d, h:i A') }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Requests today</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['requests_today']) }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Filed within the last 24 hours</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex h-2 w-2 rounded-full bg-sky-400"></span>
                    {{ now()->format('F j, Y') }}
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-800/60">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Certificate mix</p>
                @if($topCertificate)
                    <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $topCertificate->certificate_type instanceof \App\Enums\CertificateType ? $topCertificate->certificate_type->label() : str($topCertificate->certificate_type)->headline() }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Most requested type</p>
                    <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        @foreach($certificateMix->sortByDesc('total')->take(3) as $row)
                            <li class="flex items-center justify-between">
                                <span>{{ $row->certificate_type instanceof \App\Enums\CertificateType ? $row->certificate_type->label() : str($row->certificate_type)->headline() }}</span>
                                <span class="font-semibold">{{ $row->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No requests yet.</p>
                @endif
                <div class="mt-4 text-xs text-slate-500">
                    Total requests: <span class="font-semibold text-slate-800 dark:text-white">{{ number_format($certificateSum) }}</span>
                </div>
            </div>
        </div>
    @endif

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
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Recent activity</h2>
                <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 sm:hidden dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900" data-recent-activity-toggle aria-controls="recentActivityPanel" aria-expanded="true">
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

    @if($user->canManageRecords())
        <div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Residents by Purok</h2>
                @php($maxPurok = $populationByPurok->max('total') ?: 1)
                <div class="mt-3 sm:mt-4 space-y-3 text-sm">
                    @forelse($populationByPurok as $row)
                        @php($widthPercent = ($row->total / $maxPurok) * 100)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $row->purok }}</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row->total }}</span>
                            </div>
                            <div class="h-6 w-full overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-700/30">
                                <div class="h-full rounded-lg bg-linear-to-r from-sky-500 to-emerald-500 transition-all duration-500" style="width: {{ $widthPercent }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 dark:text-slate-400">No data yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
                <h2 class="text-sm font-semibold text-slate-800 sm:text-base dark:text-white">Certificate mix</h2>
                @php($maxCert = $certificateMix->max('total') ?: 1)
                <div class="mt-3 sm:mt-4 space-y-3 text-sm">
                    @forelse($certificateMix as $row)
                        @php($widthPercent = ($row->total / $maxCert) * 100)
                        @php($label = $row->certificate_type instanceof \App\Enums\CertificateType ? $row->certificate_type->label() : str($row->certificate_type)->headline())
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row->total }}</span>
                            </div>
                            <div class="h-6 w-full overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-700/30">
                                <div class="h-full rounded-lg bg-linear-to-r from-amber-500 to-orange-500 transition-all duration-500" style="width: {{ $widthPercent }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 dark:text-slate-400">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
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
</script>
@endpush
