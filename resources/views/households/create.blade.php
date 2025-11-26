@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-slate-800 dark:text-white">Add household</h1>
<form method="POST" action="{{ route('households.store') }}" class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
    @csrf
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Household number <span class="text-xs text-slate-400">(CSV column: household_number)</span></label>
        <input type="text" name="household_number" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Head of household <span class="text-xs text-slate-400">(head_name)</span></label>
            <input type="text" name="head_name" value="{{ old('head_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Contact number <span class="text-xs text-slate-400">(contact_number)</span></label>
            <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" placeholder="09XXXXXXXXX">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Purok <span class="text-xs text-slate-400">(purok)</span></label>
            <input type="text" name="purok" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Zone <span class="text-xs text-slate-400">(zone)</span></label>
            <input type="text" name="zone" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Address line <span class="text-xs text-slate-400">(address_line)</span></label>
        <input type="text" name="address_line" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Members count <span class="text-xs text-slate-400">(members_count)</span></label>
            <input type="number" name="members_count" min="0" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" value="0">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Notes <span class="text-xs text-slate-400">(notes)</span></label>
            <textarea name="notes" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" rows="2"></textarea>
        </div>
    </div>
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('households.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Save household</button>
    </div>
</form>
@endsection
