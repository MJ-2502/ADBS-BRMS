@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Resident accounts</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Manage digital portal access for encoded residents.</p>
    </div>
    <a href="{{ route('accounts.residents.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Create account</a>
</div>

<form method="GET" class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, reference, or email" class="flex-1 min-w-[200px] rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
    <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        <option value="">All statuses</option>
        @foreach($statusOptions as $value => $label)
            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="rounded-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Apply</button>
    <a href="{{ route('accounts.residents.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">Reset</a>
</form>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Resident</th>
                <th class="px-4 py-3">Account</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Proof</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
                @php($user = $account->user)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800 dark:text-white">{{ $account->full_name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Reference {{ $account->reference_id }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <p>{{ $user?->email ?? '—' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user?->name ?? 'Unlinked' }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->contact_number ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <p>{{ $account->address_line ?? '—' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $account->purok ? 'Purok ' . $account->purok : 'No purok set' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $user?->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">{{ $user?->is_active ? 'Active' : 'Disabled' }}</span>
                            @if($user?->verification_status)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $user->verification_status->badgeClasses() }}">{{ $user->verification_status->label() }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($user?->verification_proof_path)
                            <a href="{{ route('accounts.residents.proof', $account) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900">Download</a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('accounts.residents.edit', $account) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Edit</a>
                            <form method="POST" action="{{ route('accounts.residents.destroy', $account) }}" class="inline" onsubmit="return confirm('Remove this resident account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No resident accounts yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-700">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
