@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-slate-800 dark:text-white">Request certificate</h1>
<form method="POST" action="{{ route('certificates.store') }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
    @csrf
    @if(auth()->user()->canManageRecords())
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Resident</label>
            <select name="resident_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                <option value="">Select resident</option>
                @foreach($residents as $resident)
                    <option value="{{ $resident->id }}" @selected(old('resident_id') == $resident->id)>{{ $resident->full_name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Certificate type</label>
            <select id="certificate_type_create" name="certificate_type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                <option value="">Select type</option>
                @foreach($certificateTypeOptions as $option)
                    <option value="{{ $option['value'] }}" @selected(old('certificate_type') === $option['value'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Fees are applied automatically based on the selected certificate.</p>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Purpose</label>
            <input type="text" name="purpose" value="{{ old('purpose') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Remarks</label>
        <textarea name="remarks" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('remarks') }}</textarea>
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
        <a href="{{ route('certificates.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Submit request</button>
    </div>
</form>
@endsection
