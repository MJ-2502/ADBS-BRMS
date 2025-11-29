@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Create staff account</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Provision administrator or clerk credentials with full dashboard access for staff.</p>
    </div>
    <a href="{{ route('accounts.officials.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Back to list</a>
</div>

<form method="POST" action="{{ route('accounts.officials.store') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Role</label>
            <select name="role" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Temporary password</label>
            <input type="text" name="password" value="{{ old('password') }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white" placeholder="Minimum 8 characters">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Address</label>
        <textarea name="address_line" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('address_line') }}</textarea>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('accounts.officials.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Create account</button>
    </div>
</form>
@endsection
