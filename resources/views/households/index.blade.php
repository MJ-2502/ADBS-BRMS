@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-semibold text-white">Households</h1>
        <p class="text-sm text-slate-400">Manage encoded households across all puroks.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('household-records.index') }}" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-800">Import</a>
        <a href="{{ route('households.create') }}" class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600">Add household</a>
    </div>
</div>

<form method="GET" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Household number, head, or address" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm bg-slate-900 text-white" />
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Purok</label>
        <select name="purok" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm bg-slate-900 text-white">
            <option value="">All</option>
            @foreach($purokOptions as $purok)
                <option value="{{ $purok }}" @selected(($filters['purok'] ?? '') === $purok)>{{ $purok }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Zone</label>
        <select name="zone" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm bg-slate-900 text-white">
            <option value="">All</option>
            @foreach($zoneOptions as $zone)
                <option value="{{ $zone }}" @selected(($filters['zone'] ?? '') === $zone)>{{ $zone }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="flex-1 rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">Filter</button>
        <a href="{{ route('households.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="mt-8 overflow-hidden rounded-2xl border border-slate-800 bg-slate-800/50">
    <div class="flex items-center justify-between border-b border-slate-700 px-4 py-3">
        <a href="{{ route('household-records.template') }}" class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">Download</a>
        <p class="text-xs text-slate-400">{{ $households->total() }} total</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr>
                    <th class="px-4 py-3">Household Number</th>
                    <th class="px-4 py-3">Head</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Address</th>
                    <th class="px-4 py-3">Members</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($households as $household)
                    <tr class="border-t border-slate-700">
                        <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $household->household_number }}</td>
                        <td class="px-4 py-3 text-slate-200">{{ $household->head_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-200">{{ $household->contact_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-200">{{ $household->address_line }}</td>
                        <td class="px-4 py-3 text-slate-200">{{ $household->members_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('households.edit', $household) }}" class="text-sky-400 hover:text-sky-300">Edit</a>
                            <form method="POST" action="{{ route('households.destroy', $household) }}" class="inline" onsubmit="return confirm('Delete household?')">
                                @csrf
                                @method('DELETE')
                                <button class="ml-2 text-rose-400 hover:text-rose-300">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">
    {{ $households->links() }}
</div>
@endsection
