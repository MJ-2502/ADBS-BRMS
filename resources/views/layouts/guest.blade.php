<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BRMS') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-slide-in {
                animation: slideIn 0.4s ease-out;
            }
            .input-group {
                position: relative;
            }
            .input-icon {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                pointer-events: none;
            }
            .input-with-icon {
                padding-left: 38px;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:py-12">
            <div class="w-full max-w-md">
                <!-- Header Card -->
                <div class="mb-6 text-center animate-slide-in">
                    <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center">
                        <img src="{{ asset('images/barangay-logo.png') }}" alt="Barangay Logo" class="h-full w-full object-contain">
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">Barangay Residency<br/>Management System</h1>
                    <p class="mt-2 text-sm text-slate-600">Digital residency and certification workflows</p>
                </div>

                <!-- Main Content Card -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl animate-slide-in sm:p-8" style="animation-delay: 0.1s;">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
