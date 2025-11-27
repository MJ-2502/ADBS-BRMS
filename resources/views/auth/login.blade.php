@extends('layouts.guest')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Welcome Back</h2>
    <p class="mt-1 text-sm text-slate-500">Sign in to access your account</p>
</div>

@if(session('status'))
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 animate-slide-in">
        <i class="fas fa-check-circle mt-0.5 text-emerald-500"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 animate-slide-in">
        <i class="fas fa-exclamation-circle mt-0.5 text-rose-500"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
    @csrf
    
    <!-- Email Field -->
    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-700">Email Address</label>
        <div class="input-group">
            <i class="fas fa-envelope input-icon"></i>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-3 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                placeholder="you@example.com"
                required
            >
        </div>
    </div>

    <!-- Password Field -->
    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-700">Password</label>
        <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-3 pr-12 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                placeholder="Enter your password"
                required
            >
            <button 
                type="button" 
                onclick="togglePassword()" 
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
            >
                <i id="toggleIcon" class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <!-- Remember & Register -->
    <div class="flex items-center justify-between text-sm">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input 
                type="checkbox" 
                name="remember" 
                value="1" 
                class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-200 transition-all"
            >
            <span class="text-slate-600">Remember me</span>
        </label>
        <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
            Create account
        </a>
    </div>

    <!-- Submit Button -->
    <button 
        type="submit" 
        class="w-full rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 py-3.5 text-base font-semibold text-white shadow-lg transition-all duration-200 hover:shadow-xl hover:from-emerald-600 hover:to-sky-600 focus:outline-none focus:ring-4 focus:ring-emerald-200"
    >
        <i class="fas fa-sign-in-alt mr-2"></i>
        Sign In
    </button>
</form>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
