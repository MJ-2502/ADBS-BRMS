@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Create resident account</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Link an encoded resident to a portal login.</p>
    </div>
    <a href="{{ route('accounts.residents.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Back to list</a>
</div>

@if($availableResidents->isEmpty())
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
        All recorded residents already have accounts. Add a new resident first, then return here to provision access.
    </div>
@else
    @php($selectedResidentId = old('resident_id', $selectedResident?->id))
    @php($prefillEmail = old('email', $selectedResident?->email))
    @php($prefillPhone = old('phone', $selectedResident?->contact_number))
    @php($prefillAddress = old('address_line', $selectedResident?->address_line))
    @php($prefillPurok = old('purok', $selectedResident?->purok))
    <form method="POST" action="{{ route('accounts.residents.store') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
        @csrf
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Resident to link</label>
            <select name="resident_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                <option value="">Select resident</option>
                @foreach($availableResidents as $residentOption)
                    <option value="{{ $residentOption->id }}" @selected($selectedResidentId == $residentOption->id)>{{ $residentOption->full_name }} (Ref {{ $residentOption->reference_id }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Email</label>
                <input type="email" name="email" value="{{ $prefillEmail }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Temporary password</label>
                <input type="text" name="password" value="{{ old('password') }}" placeholder="Minimum 8 characters" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Contact number</label>
                <input type="text" name="phone" value="{{ $prefillPhone }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Purok</label>
                <input type="text" name="purok" value="{{ $prefillPurok }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Address</label>
            <textarea name="address_line" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ $prefillAddress }}</textarea>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Account status</label>
            <select name="is_active" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="1" @selected(old('is_active', '1') === '1')>Active</option>
                <option value="0" @selected(old('is_active') === '0')>Disabled</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('accounts.residents.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Create account</button>
        </div>
    </form>
@endif
@endsection
