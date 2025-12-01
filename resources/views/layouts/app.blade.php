<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BRMS') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-900 font-sans text-slate-100 antialiased">
        @php
            $appName = config('app.name', 'BRMS');
            $pendingCertificateRequests = auth()->check() && auth()->user()->canManageRecords()
                ? \App\Models\CertificateRequest::where('status', \App\Enums\CertificateStatus::Pending)->count()
                : 0;
            $pendingAccountVerifications = auth()->check() && auth()->user()->canManageRecords()
                ? \App\Models\RegistrationRequest::where('status', \App\Enums\VerificationStatus::Pending)->count()
                : 0;
            $recordsActive = request()->routeIs([
                'residents.*',
                'resident-records.*',
                'households.*',
                'household-records.*',
            ]);
            $accountsActive = request()->routeIs([
                'accounts.*',
            ]);
        @endphp
        <div class="flex min-h-screen bg-slate-900">
            <!-- Sidebar overlay for mobile -->
            <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm transition-opacity lg:hidden" style="display: none;" aria-hidden="true"></div>
            
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r border-slate-800 bg-slate-900/40 px-6 py-8 backdrop-blur transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:fixed">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">{{ $appName }}</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Barangay Residency Management</p>
                    </div>
                    <img src="{{ asset('images/barangay-logo.png') }}" alt="Barangay Logo" class="h-16 w-16 object-contain shrink-0">
                </div>
                <nav class="mt-8 space-y-1 overflow-y-auto pr-1 text-sm font-medium" style="max-height: calc(100vh - 220px);">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('certificates.index') }}" class="flex items-center justify-between gap-2 rounded px-3 py-2 {{ request()->routeIs('certificates.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Certificates</span>
                        </div>
                        @if($pendingCertificateRequests > 0)
                            <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">{{ $pendingCertificateRequests }}</span>
                        @endif
                    </a>
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('verifications.index') }}" class="flex items-center justify-between gap-2 rounded px-3 py-2 {{ request()->routeIs('verifications.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Verifications</span>
                            </div>
                            @if($pendingAccountVerifications > 0)
                                <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">{{ $pendingAccountVerifications }}</span>
                            @endif
                        </a>
                    @endif
                    @if(auth()->user()?->canManageRecords())
                        @if(auth()->user()?->isAdmin())
                            <div class="rounded px-3 py-2 {{ $accountsActive ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                                <button type="button" data-sidebar-accordion data-target="accountsMenu" data-storage="accounts" data-force-open="{{ $accountsActive ? '1' : '0' }}" class="flex w-full items-center gap-2 text-left text-sm font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="flex-1">User Management</span>
                                    <svg data-sidebar-chevron class="h-4 w-4 transition-transform" aria-hidden="true" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div id="accountsMenu" class="mt-2 space-y-1 {{ $accountsActive ? '' : 'hidden' }}">
                                    <a href="{{ route('accounts.residents.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs('accounts.residents.*') ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">Resident Accounts</a>
                                    <a href="{{ route('accounts.officials.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs('accounts.officials.*') ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">Staff Accounts</a>
                                </div>
                            </div>
                        @endif
                        <div class="rounded px-3 py-2 {{ $recordsActive ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                            <button type="button" data-sidebar-accordion data-target="recordsMenu" data-storage="records" data-force-open="{{ $recordsActive ? '1' : '0' }}" class="flex w-full items-center gap-2 text-left text-sm font-medium">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span class="flex-1">Records</span>
                                <svg data-sidebar-chevron class="h-4 w-4 transition-transform" aria-hidden="true" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div id="recordsMenu" class="mt-2 space-y-1 {{ $recordsActive ? '' : 'hidden' }}">
                                <a href="{{ route('residents.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs(['residents.*', 'resident-records.*']) ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">Residents</a>
                                <a href="{{ route('households.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs(['households.*', 'household-records.*']) ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">Households</a>
                            </div>
                        </div>
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('backups.index') }}" class="flex items-center gap-2 rounded px-3 py-2 {{ request()->routeIs('backups.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                                Backup & Restore
                            </a>
                        @endif
                        <a href="{{ route('activity-logs.index') }}" class="flex items-center gap-2 rounded px-3 py-2 {{ request()->routeIs('activity-logs.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Activity Logs
                        </a>
                    @endif
                    
                    <!-- Profile & Logout Container -->
                    <div class="mt-3 space-y-1 border-t border-slate-200 pt-3 dark:border-slate-700">
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 rounded px-3 py-2 {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/50 dark:hover:text-white' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-400 dark:hover:bg-rose-950/30 dark:hover:text-rose-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>
                <div class="mt-8 text-xs text-slate-500 dark:text-slate-400">
                    Logged in as <span class="font-semibold text-slate-700 dark:text-white">{{ ucfirst(auth()->user()->role->value) }}</span>
                </div>
            </aside>
            <div class="flex flex-1 flex-col lg:ml-64">
                <header class="sticky top-0 z-40 flex items-center justify-between border-b border-slate-800 bg-slate-900/80 px-3 py-3 shadow-sm backdrop-blur sm:px-4 sm:py-4 lg:px-8">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button type="button" id="menuToggle" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-700 bg-slate-800 text-slate-300 transition hover:text-white focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-slate-400 lg:hidden" aria-label="Open menu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <p class="hidden text-xs font-medium uppercase tracking-wide text-slate-400 sm:block">Dashboard</p>
                            <div class="flex items-baseline gap-1.5 sm:mt-1 sm:gap-2">
                                <h1 class="truncate text-base font-semibold text-white sm:text-lg">{{ $appName }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('profile.show') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-700 bg-slate-800 text-slate-300 transition hover:text-white focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-slate-400" aria-label="View profile">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    </div>
                </header>
                <main class="flex-1 px-3 py-4 sm:px-4 sm:py-6 lg:px-10 lg:py-8">
                    @if(session('status'))
                        <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-2 text-sm text-rose-700">
                            <ul class="list-disc pl-4">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-sidebar-accordion]').forEach((toggle) => {
                    const targetId = toggle.getAttribute('data-target');
                    if (!targetId) {
                        return;
                    }

                    const menu = document.getElementById(targetId);
                    if (!menu) {
                        return;
                    }

                    const storageKey = 'brms-sidebar-' + (toggle.getAttribute('data-storage') ?? targetId);
                    const forceOpen = toggle.getAttribute('data-force-open') === '1';
                    const stored = localStorage.getItem(storageKey);
                    const icon = toggle.querySelector('[data-sidebar-chevron]');

                    const setState = (isOpen) => {
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        menu.classList.toggle('hidden', !isOpen);
                        if (icon) {
                            icon.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                        }
                    };

                    let isOpen;
                    if (forceOpen) {
                        isOpen = true;
                    } else if (stored !== null) {
                        isOpen = stored === '1';
                    } else {
                        isOpen = false;
                    }

                    setState(isOpen);

                    toggle.addEventListener('click', () => {
                        isOpen = !isOpen;
                        setState(isOpen);
                        localStorage.setItem(storageKey, isOpen ? '1' : '0');
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
