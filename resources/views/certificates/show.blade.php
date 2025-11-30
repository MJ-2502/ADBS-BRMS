@extends('layouts.app')

@section('content')
@php($user = auth()->user())
@php($editableStatuses = [\App\Enums\CertificateStatus::Pending->value, \App\Enums\CertificateStatus::ForReview->value])
@php($ownsRequest = $certificate->requested_by === $user->id)
@php($isEditableState = in_array($certificate->status->value, $editableStatuses, true))
@php($canEdit = $user->canManageRecords() || ($ownsRequest && $isEditableState))
@php($requiresDetails = $certificate->requiresAdditionalDetails())
@php($detailsComplete = $certificate->detailsAreComplete())
@php($detailSchema = ($formSchemas ?? [])[$certificate->certificate_type->value] ?? null)
@php($detailValues = $certificate->payload ?? [])

<div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Certificate reference</p>
        <h1 class="text-xl sm:text-2xl font-semibold text-white">{{ $certificate->reference_no }}</h1>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between gap-2">
                <span class="text-slate-400">Resident</span>
                <span class="font-medium text-white text-right">{{ $certificate->resident?->full_name }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-400">Type</span>
                <span class="font-medium text-white text-right">{{ $certificate->certificate_type->label() }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-400">Purpose</span>
                <span class="font-medium text-white text-right">{{ $certificate->purpose }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-400">Fee</span>
                <span class="font-medium text-white text-right">₱ {{ number_format($certificate->fee ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-slate-400">Status</span>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $certificate->status->badgeColor() }}">{{ str($certificate->status->value)->headline() }}</span>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-3">
            @if($canEdit)
                <a href="{{ route('certificates.edit', $certificate) }}" class="inline-flex items-center rounded-lg border border-emerald-500/40 px-4 py-2 text-sm font-semibold text-emerald-300 hover:bg-emerald-500/10">Edit</a>
            @endif
            @if($user->canManageRecords() || ($ownsRequest && $isEditableState))
                <form method="POST" action="{{ route('certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate request?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-500/40 px-4 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-500/10">Delete</button>
                </form>
            @endif
        </div>
    </div>

    @if($requiresDetails || !empty($detailValues))
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Additional details</h2>
            @if(!$detailsComplete)
                <p class="mt-2 text-sm text-amber-300">Details have not been provided for this request.</p>
            @else
                <dl class="mt-4 space-y-3 text-sm">
                    @php($rendered = 0)
                    @foreach($detailSchema['fields'] ?? [] as $field)
                        @php($value = $detailValues[$field['name']] ?? null)
                        @continue(is_null($value))
                        @php($rendered++)
                        <div class="flex flex-col rounded-xl border border-slate-700 p-3">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $field['label'] ?? str($field['name'])->headline() }}</dt>
                            <dd class="text-sm font-medium text-white">{{ $value }}</dd>
                        </div>
                    @endforeach
                    @if($rendered === 0)
                        @foreach($detailValues as $key => $value)
                            <div class="flex flex-col rounded-xl border border-slate-700 p-3">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">{{ str($key)->headline() }}</dt>
                                <dd class="text-sm font-medium text-white">{{ $value }}</dd>
                            </div>
                        @endforeach
                    @endif
                </dl>
                @if($certificate->details_submitted_at)
                    <p class="mt-2 text-xs text-slate-400">
                        Submitted {{ $certificate->details_submitted_at->format('F d, Y h:i A') }} by {{ $certificate->detailsSubmitter?->name ?? $certificate->requester?->name ?? 'resident' }}
                    </p>
                @endif
            @endif
        </div>
    @endif

    @if($user->canManageRecords())
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Update status</h2>
            <form method="POST" action="{{ route('certificates.status', $certificate) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-slate-300">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white" required>
                        @foreach(\App\Enums\CertificateStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($certificate->status === $status)>{{ str($status->value)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-300">Remarks</label>
                    <textarea name="remarks" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base bg-slate-900 text-white" rows="3">{{ old('remarks', $certificate->remarks) }}</textarea>
                </div>
                <button class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">Save changes</button>
            </form>
        </div>
    @endif
</div>
@endsection
