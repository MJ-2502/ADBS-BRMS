@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Resident reference</p>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">{{ $resident->full_name }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-mono">{{ $resident->reference_id }}</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('residents.edit', $resident) }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Edit</a>
        <form method="POST" action="{{ route('residents.destroy', $resident) }}" onsubmit="return confirm('Archive resident?')">
            @csrf
            @method('DELETE')
            <button class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Archive</button>
        </form>
    </div>
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
        <h2 class="text-base font-semibold text-slate-800 dark:text-white">Profile</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-700">
                <dt class="text-slate-500 dark:text-slate-400">Purok</dt>
                <dd class="font-medium text-slate-800 dark:text-white">{{ $resident->purok ?? '—' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-700">
                <dt class="text-slate-500 dark:text-slate-400">Years of residency</dt>
                <dd class="font-medium text-slate-800 dark:text-white">{{ $resident->years_of_residency }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-slate-700">
                <dt class="text-slate-500 dark:text-slate-400">Status</dt>
                <dd class="font-medium text-slate-800 dark:text-white">{{ str($resident->residency_status)->headline() }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500 dark:text-slate-400">Contact</dt>
                <dd class="font-medium text-slate-800 dark:text-white">{{ $resident->contact_number ?? '—' }}</dd>
            </div>
        </dl>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-800/50">
        <h2 class="text-base font-semibold text-slate-800 dark:text-white">Certificate history</h2>
        <div class="mt-4 space-y-3 text-sm">
            @forelse($resident->certificateRequests as $request)
                <div class="rounded-xl border border-slate-100 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/30">
                    <p class="font-medium text-slate-800 dark:text-white">{{ $request->certificate_type->label() }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $request->reference_no }} • {{ $request->created_at->format('M d, Y') }}</p>
                    <span class="mt-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $request->status->badgeColor() }}">{{ str($request->status->value)->headline() }}</span>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">No requests yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
