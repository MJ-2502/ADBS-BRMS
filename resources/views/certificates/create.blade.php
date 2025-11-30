@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Request certificate</h1>
<form method="POST" action="{{ route('certificates.store') }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-6">
    @csrf
    @if(auth()->user()->canManageRecords())
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="text-sm font-medium text-slate-300">Resident first name</label>
                <input type="text" name="resident_first_name" value="{{ old('resident_first_name') }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" placeholder="e.g. Juan" required>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-300">Middle initial</label>
                <input type="text" name="resident_middle_initial" value="{{ old('resident_middle_initial') }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" placeholder="e.g. D" maxlength="2">
            </div>
            <div>
                <label class="text-sm font-medium text-slate-300">Last name</label>
                <input type="text" name="resident_last_name" value="{{ old('resident_last_name') }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" placeholder="e.g. Cruz" required>
            </div>
        </div>
        <p class="text-xs text-slate-400">We will match the resident using the exact spelling you enter. Middle initial is optional but helps narrow the search.</p>
    @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Certificate type</label>
            <select id="certificate_type_create" name="certificate_type" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
                <option value="">Select type</option>
                @foreach($certificateTypeOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('certificate_type') === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-400">Fees are applied automatically based on the selected certificate.</p>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Purpose</label>
            <input type="text" name="purpose" value="{{ old('purpose') }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-300">Remarks</label>
        <textarea name="remarks" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">{{ old('remarks') }}</textarea>
    </div>
    @include('certificates.partials.details-fields', [
        'schemas' => $formSchemas ?? [],
        'selectFieldId' => 'certificate_type_create',
        'values' => old('details', []),
    ])
    @include('certificates.partials.fee-preview', [
        'fees' => $fees,
        'certificateTypeOptions' => $certificateTypeOptions,
        'fieldId' => 'certificate_type_create',
    ])
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('certificates.index') }}" class="text-sm text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Submit request</button>
    </div>
</form>
@endsection
