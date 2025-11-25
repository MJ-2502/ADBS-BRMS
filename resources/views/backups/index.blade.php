@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Backups</h1>
    <form method="POST" action="{{ route('backups.store') }}" onsubmit="return confirm('Start manual backup?')">
        @csrf
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Run backup</button>
    </form>
</div>
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">File</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Operator</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($backups as $backup)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3 font-mono text-xs dark:text-slate-300">{{ $backup->file_path ?: 'pending' }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $backup->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($backup->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($backup->status) }}</span>
                    </td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $backup->operator?->name ?? '—' }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $backup->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        @if($backup->status === 'completed')
                            <a href="{{ route('backups.download', $backup) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Download</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $backups->links() }}
</div>
@endsection
