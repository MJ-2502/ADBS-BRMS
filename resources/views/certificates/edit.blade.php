@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Edit certificate request</h1>
<form method="POST" action="{{ route('certificates.update', $certificate) }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-6">
    @csrf
    @method('PUT')
    @if(auth()->user()->canManageRecords())
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="text-sm font-medium text-slate-300">Resident first name</label>
                <input type="text" name="resident_first_name" value="{{ old('resident_first_name', $certificate->resident?->first_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-300">Middle initial</label>
                @php($middleInitial = $certificate->resident?->middle_name ? substr($certificate->resident->middle_name, 0, 1) : '')
                <input type="text" name="resident_middle_initial" value="{{ old('resident_middle_initial', $middleInitial) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" maxlength="2">
            </div>
            <div>
                <label class="text-sm font-medium text-slate-300">Last name</label>
                <input type="text" name="resident_last_name" value="{{ old('resident_last_name', $certificate->resident?->last_name) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
            </div>
        </div>
    @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Certificate type</label>
            <select id="certificate_type_edit" name="certificate_type" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
                <option value="">Select type</option>
                @foreach($certificateTypeOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('certificate_type', $certificate->certificate_type->value) === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-400">Fee updates when you change the certificate type.</p>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-300">Purpose</label>
            <input type="text" name="purpose" value="{{ old('purpose', $certificate->purpose) }}" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" required>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-300">Remarks</label>
            <textarea name="remarks" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white" rows="3">{{ old('remarks', $certificate->remarks) }}</textarea>
        </div>
        @include('certificates.partials.fee-preview', [
            'fees' => $fees,
            'certificateTypeOptions' => $certificateTypeOptions,
            'fieldId' => 'certificate_type_edit',
        ])
    </div>
    @include('certificates.partials.details-fields', [
        'schemas' => $formSchemas ?? [],
        'selectFieldId' => 'certificate_type_edit',
        'values' => old('details', $certificate->payload ?? []),
    ])
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('certificates.show', $certificate) }}" class="text-sm text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Save changes</button>
    </div>
</form>
@endsection
