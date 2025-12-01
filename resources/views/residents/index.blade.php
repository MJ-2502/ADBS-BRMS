@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-semibold text-white">Residents</h1>
        <p class="text-sm text-slate-400">Manage registered residents across all puroks.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('resident-records.index') }}" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-800">Import</a>
        <a href="{{ route('residents.create') }}" class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600">Add resident</a>
    </div>
</div>

<form method="GET" class="mt-6 grid gap-4 rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, purok, or reference" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white placeholder-slate-400">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Link status</label>
        <select name="link" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
            <option value="">All</option>
            <option value="linked" @selected(($filters['link'] ?? '') === 'linked')>Linked to account</option>
            <option value="unlinked" @selected(($filters['link'] ?? '') === 'unlinked')>Not linked</option>
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Purok</label>
        <select name="purok" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 bg-slate-900 text-white">
            <option value="">All puroks</option>
            @foreach($purokOptions as $purok)
                <option value="{{ $purok }}" @selected(($filters['purok'] ?? '') === $purok)>{{ $purok }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="flex-1 rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">Apply</button>
        <a href="{{ route('residents.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-800 bg-slate-800/50">
    <div class="flex items-center justify-between border-b border-slate-700 px-4 py-3">
        <a href="{{ route('resident-records.template') }}" class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">Download</a>
        <p class="text-xs text-slate-400">{{ $residents->total() }} total</p>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
            <tr>
                <th class="px-4 py-3">Reference</th>
                <th class="px-4 py-3">Resident</th>
                <th class="px-4 py-3">Household</th>
                <th class="px-4 py-3">Birthdate</th>
                <th class="px-4 py-3">Purok</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3">Verification</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($residents as $resident)
                <tr class="border-t border-slate-700">
                    <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $resident->reference_id }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-white">{{ $resident->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ str($resident->residency_status)->headline() }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $resident->household?->household_number ? 'HH-' . $resident->household->household_number : 'Unassigned' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ optional($resident->birthdate)?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-200">{{ $resident->purok ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $resident->contact_number ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php($verification = $resident->user?->verification_status)
                        @if($verification)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $verification->badgeClasses() }}">{{ $verification->label() }}</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('residents.show', $resident) }}" class="text-sky-400 hover:text-sky-300">View</a>
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
