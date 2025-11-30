@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Add resident</h1>
<form method="POST" action="{{ route('residents.store') }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-6">
    @csrf
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-slate-300">First name</label>
            <input type="text" name="first_name" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Middle name</label>
            <input type="text" name="middle_name" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Last name</label>
            <input type="text" name="last_name" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Household</label>
            <select name="household_id" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Unassigned</option>
                @foreach($households as $household)
                    <option value="{{ $household->id }}">{{ $household->household_number }} - {{ $household->address_line }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Birthdate</label>
            <input type="date" name="birthdate" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Gender</label>
            <select name="gender" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-slate-300">Purok</label>
            <input type="text" name="purok" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Contact number</label>
            <input type="text" name="contact_number" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Years of residency</label>
            <input type="number" name="years_of_residency" value="0" min="0" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-300">Address</label>
        <input type="text" name="address_line" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
    </div>
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('residents.index') }}" class="text-sm text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Save</button>
    </div>
</form>
@endsection
