@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('register.submit') }}" class="space-y-4" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium text-slate-600">First name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">Last name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium text-slate-600">Password</label>
            <input type="password" name="password" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">Confirm Password</label>
            <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600">Contact Number</label>
        <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium text-slate-600">Years of residency</label>
            <input type="number" name="years_of_residency" min="0" value="{{ old('years_of_residency', 1) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600">Purok</label>
            <input type="text" name="purok" value="{{ old('purok') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600">Address</label>
        <textarea name="address_line" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">{{ old('address_line') }}</textarea>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600">Proof of residency <span class="text-rose-500">*</span></label>
        <input type="file" name="proof_document" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 w-full rounded-lg border border-dashed border-slate-300 px-3 py-2 text-sm" required>
        <p class="mt-1 text-xs text-slate-500">Upload a clear scan of your barangay ID, utility bill, or any document that proves you live within the barangay. Max 5 MB.</p>
    </div>
    <div class="flex items-center justify-between text-sm">
        <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-700">Already registered?</a>
    </div>
    <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">Create account</button>
</form>
@if($errors->any())
    <div class="mt-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        {{ $errors->first() }}
    </div>
@endif
@endsection
