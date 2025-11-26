@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Certificate requests</h1>
    <a href="{{ route('certificates.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Request certificate</a>
</div>

<form method="GET" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Reference or resident" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
    </div>
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</label>
        <select name="status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            <option value="">All</option>
            @foreach($statuses as $status)
                <option value="{{ $status['value'] }}" @selected(($filters['status'] ?? '') === $status['value'])>{{ $status['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Type</label>
        <select name="type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            <option value="">All certificate types</option>
            @foreach($certificateTypes as $type)
                <option value="{{ $type['value'] }}" @selected(($filters['type'] ?? '') === $type['value'])>{{ $type['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Apply</button>
        <a href="{{ route('certificates.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Reference</th>
                <th class="px-4 py-3">Resident</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Purpose</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $request->reference_no }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $request->resident?->full_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $request->certificate_type->label() }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $request->purpose }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $request->status->badgeColor() }}">{{ str($request->status->value)->headline() }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('certificates.show', $request) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No certificate requests yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $requests->links() }}
</div>
@endsection
