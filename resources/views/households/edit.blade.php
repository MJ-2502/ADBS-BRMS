@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Edit household</h1>
<form method="POST" action="{{ route('households.update', $household) }}" class="mt-6 space-y-5 rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
    @csrf
    @method('PUT')
    <div>
        <label class="text-sm font-medium text-slate-300">Household number</label>
        <input type="text" name="household_number" value="{{ old('household_number', $household->household_number) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white" required>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-300">Address</label>
        <input type="text" name="address_line" value="{{ old('address_line', $household->address_line) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $household->purok) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Zone</label>
            <input type="text" name="zone" value="{{ old('zone', $household->zone) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Head of household</label>
            <input type="text" name="head_name" value="{{ old('head_name', $household->head_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Contact number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $household->contact_number) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white" placeholder="09XXXXXXXXX">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Members count</label>
            <input type="number" name="members_count" min="0" value="{{ old('members_count', $household->members_count) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Notes</label>
            <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">{{ old('notes', $household->notes) }}</textarea>
        </div>
    </div>
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('households.index') }}" class="text-sm font-medium text-slate-400 hover:text-slate-200">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600">Update household</button>
    </div>
</form>
@endsection
