@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
    @csrf
    <div>
        <label class="text-sm font-medium text-slate-600">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base focus:border-sky-500 focus:ring-sky-500" required>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600">Password</label>
        <input type="password" name="password" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base focus:border-sky-500 focus:ring-sky-500" required>
    </div>
    <div class="flex items-center justify-between text-sm">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-600">
            Remember me
        </label>
        <a href="{{ route('register') }}" class="text-sky-600 hover:text-sky-700">Register</a>
    </div>
    <button type="submit" class="w-full rounded-lg bg-sky-600 py-2.5 text-base font-semibold text-white shadow hover:bg-sky-700">Sign in</button>
</form>
@if(session('status'))
    <div class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
        {{ session('status') }}
    </div>
@endif
@if($errors->any())
    <div class="mt-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        {{ $errors->first() }}
    </div>
@endif
@endsection
