<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BRMS') }}</title>
        <script>
            (() => {
                try {
                    const storedTheme = localStorage.getItem('brms-theme');
                    if (storedTheme === 'dark') {
                        document.documentElement.classList.add('dark');
                        document.documentElement.dataset.theme = 'dark';
                    }
                } catch (error) {
                    console.debug('Theme detection failed', error);
                }
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">
        @php
            $appName = config('app.name', 'BRMS');
            $pendingCertificateRequests = auth()->check() && auth()->user()->canManageRecords()
                ? \App\Models\CertificateRequest::where('status', \App\Enums\CertificateStatus::Pending)->count()
                : 0;
            $pendingAccountVerifications = auth()->check() && auth()->user()->canManageRecords()
                ? \App\Models\User::where('role', \App\Enums\UserRole::Resident)
                    ->where('verification_status', \App\Enums\VerificationStatus::Pending)
                    ->count()
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
        <div class="flex min-h-screen bg-slate-50 dark:bg-slate-900">
            <!-- Sidebar overlay for mobile -->
            <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm transition-opacity lg:hidden" style="display: none;" aria-hidden="true"></div>
            
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r border-slate-200 bg-white/90 px-6 py-8 backdrop-blur transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:fixed dark:border-slate-800 dark:bg-slate-900/40">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">{{ $appName }}</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Barangay Residency Management</p>
                    </div>
                    <img src="{{ asset('images/barangay-logo.png') }}" alt="Barangay Logo" class="h-16 w-16 object-contain shrink-0">
                </div>
                <nav class="mt-8 space-y-1 overflow-y-auto pr-1 text-sm font-medium" style="max-height: calc(100vh - 220px);">
                    <a href="{{ route('dashboard') }}" class="flex items-center rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('certificates.index') }}" class="flex items-center rounded px-3 py-2 {{ request()->routeIs('certificates.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Certificates</a>
                    @if(auth()->user()?->canManageRecords())
                        <div class="rounded px-3 py-2 {{ $accountsActive ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">
                            <button type="button" data-sidebar-accordion data-target="accountsMenu" data-storage="accounts" data-force-open="{{ $accountsActive ? '1' : '0' }}" class="flex w-full items-center justify-between text-left text-xs font-semibold uppercase tracking-wide">
                                <span>Accounts</span>
                                <svg class="h-3.5 w-3.5 transition-transform" aria-hidden="true" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div id="accountsMenu" class="mt-2 space-y-1 {{ $accountsActive ? '' : 'hidden' }}">
                                <a href="{{ route('accounts.residents.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs('accounts.residents.*') ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Resident Accounts</a>
                                <a href="{{ route('accounts.officials.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs('accounts.officials.*') ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Official Accounts</a>
                            </div>
                        </div>
                        <div class="rounded px-3 py-2 {{ $recordsActive ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">
                            <button type="button" data-sidebar-accordion data-target="recordsMenu" data-storage="records" data-force-open="{{ $recordsActive ? '1' : '0' }}" class="flex w-full items-center justify-between text-left text-xs font-semibold uppercase tracking-wide">
                                <span>Records</span>
                                <svg class="h-3.5 w-3.5 transition-transform" aria-hidden="true" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div id="recordsMenu" class="mt-2 space-y-1 {{ $recordsActive ? '' : 'hidden' }}">
                                <a href="{{ route('residents.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs(['residents.*', 'resident-records.*']) ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Residents</a>
                                <a href="{{ route('households.index') }}" class="flex items-center rounded px-3 py-2 pl-4 text-sm font-medium {{ request()->routeIs(['households.*', 'household-records.*']) ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Households</a>
                            </div>
                        </div>
                        <a href="{{ route('backups.index') }}" class="flex items-center rounded px-3 py-2 {{ request()->routeIs('backups.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Backups</a>
                        <a href="{{ route('activity-logs.index') }}" class="flex items-center rounded px-3 py-2 {{ request()->routeIs('activity-logs.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Activity Logs</a>
                        <a href="{{ route('verifications.index') }}" class="flex items-center justify-between rounded px-3 py-2 {{ request()->routeIs('verifications.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                            <span>Verifications</span>
                            @if($pendingAccountVerifications > 0)
                                <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">{{ $pendingAccountVerifications }}</span>
                            @endif
                        </a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="flex items-center rounded px-3 py-2 {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800/70 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center rounded px-3 py-2 text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Logout</button>
                    </form>
                </nav>
                <div class="mt-8 text-xs text-slate-500 dark:text-slate-400">
                    Logged in as <span class="font-semibold text-slate-700 dark:text-white">{{ auth()->user()->name }}</span>
                </div>
            </aside>
            <div class="flex flex-1 flex-col lg:ml-64">
                <header class="sticky top-0 z-40 flex items-center justify-between border-b border-slate-200 bg-white/90 px-3 py-3 shadow-sm backdrop-blur sm:px-4 sm:py-4 lg:px-8 dark:border-slate-800 dark:bg-slate-900/80">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button type="button" id="menuToggle" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:text-slate-900 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-slate-400 lg:hidden dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-white" aria-label="Open menu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <p class="hidden text-xs font-medium uppercase tracking-[0.2em] text-slate-500 sm:block dark:text-slate-400">Dashboard</p>
                            <div class="flex items-baseline gap-1.5 sm:mt-1 sm:gap-2">
                                <h1 class="truncate text-base font-semibold text-slate-900 sm:text-lg dark:text-white">{{ $appName }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('certificates.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:text-slate-900 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-white" aria-label="View certificate requests">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.657a2 2 0 001.414-1.414l.343-1.372a8.001 8.001 0 00-5.514-9.59 2 2 0 10-3.2 0 8.001 8.001 0 00-5.514 9.59l.343 1.372a2 2 0 001.414 1.414L7 18a3 3 0 006 0l1.857-.343z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 21a3 3 0 006 0" />
                            </svg>
                            @if($pendingCertificateRequests > 0)
                                <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-semibold text-white">
                                    {{ $pendingCertificateRequests }}
                                </span>
                            @endif
                        </a>
                        <button type="button" id="themeToggle" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:text-slate-900 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-white" aria-label="Toggle theme">
                            <svg class="h-5 w-5 text-amber-400 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m0-11.314l1.414 1.414m11.314 11.314l1.414 1.414M12 7a5 5 0 000 10 5 5 0 000-10z" />
                            </svg>
                            <svg class="hidden h-5 w-5 text-slate-200 dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </button>
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
                    const icon = toggle.querySelector('svg');

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
