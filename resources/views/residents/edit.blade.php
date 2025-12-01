@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Edit resident</h1>
<form method="POST" action="{{ route('residents.update', $resident) }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-6">
    @csrf
    @method('PUT')
    <div class="grid gap-4 sm:grid-cols-4">
        <div>
            <label class="text-sm font-medium text-slate-300">First name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $resident->first_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Middle name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Last name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $resident->last_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Suffix</label>
            <input type="text" name="suffix" value="{{ old('suffix', $resident->suffix) }}" placeholder="Jr., Sr., III" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-slate-300">Birthdate</label>
            <input type="date" name="birthdate" value="{{ old('birthdate', optional($resident->birthdate)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Gender</label>
            <select name="gender" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Select</option>
                <option value="male" {{ old('gender', $resident->gender) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $resident->gender) == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Civil status</label>
            <select name="civil_status" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Select</option>
                <option value="single" {{ old('civil_status', $resident->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                <option value="married" {{ old('civil_status', $resident->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                <option value="widowed" {{ old('civil_status', $resident->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="separated" {{ old('civil_status', $resident->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
            </select>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Occupation</label>
            <input type="text" name="occupation" value="{{ old('occupation', $resident->occupation) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $resident->religion) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Education</label>
            <select name="education" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Select</option>
                <option value="Elementary" {{ old('education', $resident->education) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                <option value="High School" {{ old('education', $resident->education) == 'High School' ? 'selected' : '' }}>High School</option>
                <option value="College" {{ old('education', $resident->education) == 'College' ? 'selected' : '' }}>College</option>
                <option value="Graduate" {{ old('education', $resident->education) == 'Graduate' ? 'selected' : '' }}>Graduate</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Years of residency</label>
            <input type="number" name="years_of_residency" value="{{ old('years_of_residency', $resident->years_of_residency) }}" min="0" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Contact number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $resident->contact_number) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Email</label>
            <input type="email" name="email" value="{{ old('email', $resident->email) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Address</label>
            <input type="text" name="address_line" value="{{ old('address_line', $resident->address_line) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $resident->purok) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Household</label>
            <select name="household_id" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="">Unassigned</option>
                @foreach($households as $household)
                    <option value="{{ $household->id }}" @selected(old('household_id', $resident->household_id) == $household->id)>{{ $household->household_number }} - {{ $household->address_line }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Residency status</label>
            <select name="residency_status" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
                <option value="active" {{ old('residency_status', $resident->residency_status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('residency_status', $resident->residency_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="flex items-center gap-2 text-sm font-medium text-slate-300">
                <input type="checkbox" name="is_voter" value="1" {{ old('is_voter', $resident->is_voter) ? 'checked' : '' }} class="rounded border-slate-700 bg-slate-900 text-emerald-500">
                Registered voter
            </label>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Voter precinct</label>
            <input type="text" name="voter_precinct" value="{{ old('voter_precinct', $resident->voter_precinct) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-300">Remarks</label>
        <textarea name="remarks" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">{{ old('remarks', $resident->remarks) }}</textarea>
    </div>
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('residents.show', $resident) }}" class="text-sm text-slate-400 hover:text-white">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Update resident</button>
    </div>
</form>
@endsection
