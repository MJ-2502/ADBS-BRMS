@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Edit resident</h1>
<form method="POST" action="{{ route('residents.update', $resident) }}" class="mt-6 grid gap-5 rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
    @csrf
    @method('PUT')
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-slate-300">First name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $resident->first_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Middle name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Last name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $resident->last_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Household</label>
            <select name="household_id" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
                <option value="">Unassigned</option>
                @foreach($households as $household)
                    <option value="{{ $household->id }}" @selected(old('household_id', $resident->household_id) == $household->id)>{{ $household->household_number }} - {{ $household->address_line }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Birthdate</label>
            <input type="date" name="birthdate" value="{{ old('birthdate', optional($resident->birthdate)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $resident->purok) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Years of residency</label>
            <input type="number" name="years_of_residency" min="0" value="{{ old('years_of_residency', $resident->years_of_residency) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-300">Address</label>
        <textarea name="address_line" rows="2" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white">{{ old('address_line', $resident->address_line) }}</textarea>
    </div>
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('residents.show', $resident) }}" class="text-sm font-medium text-slate-400 hover:text-slate-200">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600">Update resident</button>
    </div>
</form>
@endsection
