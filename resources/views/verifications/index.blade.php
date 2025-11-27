@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Account review</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Resident verifications</h1>
    </div>
</div>

<form method="GET" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, email, or reference" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
    </div>
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</label>
        <select name="status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            <option value="">All</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Filter</button>
        <a href="{{ route('verifications.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Applicant</th>
                <th class="px-4 py-3">Submitted</th>
                <th class="px-4 py-3">Residency</th>
                <th class="px-4 py-3">Proof</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Notes</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $requestRecord)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800 dark:text-white">{{ $requestRecord->full_name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $requestRecord->email }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $requestRecord->contact_number ?? 'No contact number' }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $requestRecord->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <p class="text-xs">{{ $requestRecord->address_line ?? 'No address' }}</p>
                        <p class="text-xs text-slate-500">{{ $requestRecord->purok ?? 'No purok' }}</p>
                        <p class="text-xs text-slate-500">{{ $requestRecord->years_of_residency }} yrs</p>
                    </td>
                    <td class="px-4 py-3">
                        @if($requestRecord->proof_document_path)
                            <a href="{{ route('verifications.proof', $requestRecord) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Download</a>
                        @else
                            <span class="text-slate-400">No file</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php($status = $requestRecord->status)
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status->badgeClasses() }}">{{ $status->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-500 dark:text-slate-300 text-xs">{{ $requestRecord->review_notes ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($status === \App\Enums\VerificationStatus::Pending)
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('verifications.approve', $requestRecord) }}" class="flex items-center justify-end gap-2">
                                    @csrf
                                    <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('verifications.reject', $requestRecord) }}" class="flex flex-col gap-1">
                                    @csrf
                                    <textarea name="notes" rows="2" placeholder="Rejection reason" class="w-full rounded-lg border border-rose-200 px-2 py-1 text-xs dark:border-rose-700 dark:bg-slate-900 dark:text-white" required></textarea>
                                    <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Reject</button>
                                </form>
                            </div>
                        @else
                            <p class="text-right text-xs text-slate-400">No actions available</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No verification requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $requests->links() }}
</div>
@endsection
