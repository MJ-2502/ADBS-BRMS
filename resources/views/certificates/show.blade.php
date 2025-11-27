@extends('layouts.app')

@section('content')
@php($user = auth()->user())
@php($editableStatuses = [\App\Enums\CertificateStatus::Pending->value, \App\Enums\CertificateStatus::ForReview->value])
@php($ownsRequest = $certificate->requested_by === $user->id)
@php($isEditableState = in_array($certificate->status->value, $editableStatuses, true))
@php($canEdit = $user->canManageRecords() || ($ownsRequest && $isEditableState))

<div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Certificate reference</p>
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-800 dark:text-white">{{ $certificate->reference_no }}</h1>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">Resident</span>
                <span class="font-medium text-slate-800 dark:text-white text-right">{{ $certificate->resident?->full_name }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">Type</span>
                <span class="font-medium text-slate-800 dark:text-white text-right">{{ $certificate->certificate_type->label() }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">Purpose</span>
                <span class="font-medium text-slate-800 dark:text-white text-right">{{ $certificate->purpose }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">Fee</span>
                <span class="font-medium text-slate-800 dark:text-white text-right">₱ {{ number_format($certificate->fee ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-500 dark:text-slate-400">Status</span>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $certificate->status->badgeColor() }}">{{ str($certificate->status->value)->headline() }}</span>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-3">
            @if($canEdit)
                <a href="{{ route('certificates.edit', $certificate) }}" class="inline-flex items-center rounded-lg border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-500/40 dark:text-emerald-300 dark:hover:bg-emerald-500/10">Edit</a>
            @endif
            @if($user->canManageRecords() || ($ownsRequest && $isEditableState))
                <form method="POST" action="{{ route('certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate request?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-500/10">Delete</button>
                </form>
            @endif
        </div>
        @if($certificate->pdf_path)
            <a href="{{ route('certificates.download', $certificate) }}" class="mt-4 inline-flex items-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">Download PDF</a>
        @endif
    </div>

    @if($user->canManageRecords())
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Update status</h2>
            <form method="POST" action="{{ route('certificates.status', $certificate) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
                        @foreach(\App\Enums\CertificateStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($certificate->status === $status)>{{ str($status->value)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Remarks</label>
                    <textarea name="remarks" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base dark:border-slate-700 dark:bg-slate-900 dark:text-white" rows="3">{{ old('remarks', $certificate->remarks) }}</textarea>
                </div>
                <button class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">Save changes</button>
            </form>
        </div>
    @endif
</div>
@endsection
