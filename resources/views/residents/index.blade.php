@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search residents" class="rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400">
        <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Search</button>
    </form>
    <a href="{{ route('residents.create') }}" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">Add resident</a>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Reference</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Purok</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Verification</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($residents as $resident)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3 font-mono text-xs dark:text-slate-300">{{ $resident->reference_id }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $resident->full_name }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $resident->purok ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ str($resident->residency_status)->headline() }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @php($verification = $resident->user?->verification_status)
                        @if($verification)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $verification->badgeClasses() }}">{{ $verification->label() }}</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('residents.show', $resident) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $residents->links() }}
</div>
@endsection
