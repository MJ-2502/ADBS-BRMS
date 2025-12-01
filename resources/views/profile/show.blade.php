@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-slate-800 dark:text-white">My profile</h1>
<form method="POST" action="{{ route('profile.update') }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
    @csrf
    @method('PUT')
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $user->purok) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Address</label>
        <input type="text" name="address_line" value="{{ old('address_line', $user->address_line) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
    </div>
    <div class="flex items-center justify-end gap-3">
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Update profile</button>
    </div>
</form>
@endsection
