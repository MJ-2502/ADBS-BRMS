@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Edit resident account</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Update login details or revoke access for {{ $resident->full_name }}.</p>
    </div>
    <a href="{{ route('accounts.residents.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Back to list</a>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('accounts.residents.update', $resident) }}" class="lg:col-span-2 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
        @csrf
        @method('PUT')
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Resident</label>
            <p class="mt-1 text-base font-semibold text-slate-800 dark:text-white">{{ $resident->full_name }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Reference {{ $resident->reference_id }}</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email', $resident->user?->email) }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Password</label>
                <input type="text" name="password" value="{{ old('password') }}" placeholder="Leave blank to keep" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Contact number</label>
                <input type="text" name="phone" value="{{ old('phone', $resident->contact_number) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Purok</label>
                <input type="text" name="purok" value="{{ old('purok', $resident->purok) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Address</label>
            <textarea name="address_line" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('address_line', $resident->address_line) }}</textarea>
        </div>
        @php($currentStatus = old('is_active', ($resident->user?->is_active ?? true) ? '1' : '0'))
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Account status</label>
            <select name="is_active" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="1" @selected($currentStatus === '1')>Active</option>
                <option value="0" @selected($currentStatus === '0')>Disabled</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('accounts.residents.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Save changes</button>
        </div>
    </form>
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-100">
        <h2 class="text-base font-semibold">Danger zone</h2>
        <p class="mt-2">Deleting this account removes portal access but keeps the resident record intact.</p>
        <form method="POST" action="{{ route('accounts.residents.destroy', $resident) }}" class="mt-4" onsubmit="return confirm('Delete this resident account? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="w-full rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete account</button>
        </form>
    </div>
</div>
@endsection
