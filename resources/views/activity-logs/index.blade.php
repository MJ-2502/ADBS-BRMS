@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-slate-800 dark:text-white">Activity logs</h1>
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Event</th>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $log->event }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $log->description ?? '—' }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection
