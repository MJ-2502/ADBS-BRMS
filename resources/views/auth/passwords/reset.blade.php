@extends('layouts.guest')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white">Set a new password</h2>
    <p class="mt-1 text-sm text-slate-400">Make sure your new password is at least 8 characters long.</p>
</div>

@if($errors->any())
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-700 bg-rose-900/20 px-4 py-3 text-sm text-rose-300 animate-slide-in">
        <i class="fas fa-exclamation-circle mt-0.5 text-rose-500"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-300">Email Address</label>
        <div class="input-group">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" value="{{ old('email', $email) }}" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-3 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="you@example.com" required>
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-300">New password</label>
        <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-3 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Minimum 8 characters" required>
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-300">Confirm password</label>
        <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password_confirmation" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-3 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Re-enter new password" required>
        </div>
    </div>

    <button type="submit" class="w-full rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 py-3.5 text-base font-semibold text-white shadow-lg transition-all duration-200 hover:shadow-xl hover:from-emerald-600 hover:to-sky-600 focus:outline-none focus:ring-4 focus:ring-emerald-200">
        <i class="fas fa-key mr-2"></i>
        Update password
    </button>
</form>

<p class="mt-6 text-center text-sm text-slate-400">
    <a href="{{ route('login') }}" class="font-semibold text-emerald-400 hover:text-emerald-300">Back to sign in</a>
</p>
@endsection
