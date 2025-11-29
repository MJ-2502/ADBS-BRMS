@extends('layouts.app')

@section('content')
@php($canManageAccounts = auth()->user()?->canManageAccounts())

<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Staff accounts</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Provision and manage administrator or clerk logins for barangay staff.</p>
    </div>
    @if($canManageAccounts)
        <a href="{{ route('accounts.officials.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Create account</a>
    @endif
</div>

<form method="GET" class="mt-6 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
    <select name="role" class="rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        <option value="">All roles</option>
        @foreach($roles as $role)
            <option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
        @endforeach
    </select>
    <button class="rounded-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Apply</button>
</form>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Created</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($officials as $official)
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800 dark:text-white">{{ $official->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $official->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-200">{{ $official->role->label() }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $official->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $official->is_active ? 'Active' : 'Disabled' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $official->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                    @php($defaultAdminEmail = config('app.auto_admin.email'))
                                    @php($isProtected = $defaultAdminEmail && strcasecmp($official->email, $defaultAdminEmail) === 0)
                                    @if($canManageAccounts)
                                        <div class="inline-flex items-center gap-2">
                                            @if($isProtected)
                                                <span class="text-xs text-slate-400">Immutable</span>
                                            @else
                                                <a href="{{ route('accounts.officials.edit', $official) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Edit</a>
                                                <form method="POST" action="{{ route('accounts.officials.destroy', $official) }}" class="inline" onsubmit="return confirm('Delete this staff account?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">View only</span>
                                    @endif
                                </td>
                    </tr>
                @empty
                    <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No staff accounts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-700">
            {{ $officials->links() }}
        </div>
</div>
@endsection
