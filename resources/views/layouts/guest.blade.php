<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BRMS') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-linear-to-br from-sky-50 via-white to-emerald-50">
        <div class="flex min-h-screen items-center justify-center px-4 py-6 sm:py-10">
            <div class="w-full max-w-md rounded-2xl border border-white/60 bg-white/80 p-6 shadow-xl backdrop-blur sm:p-8">
                <h1 class="text-center text-xl font-semibold text-slate-800 sm:text-2xl">Barangay Residency Management System</h1>
                <p class="mt-2 text-center text-sm text-slate-500">Digital residency and certification workflows</p>
                <div class="mt-6">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
