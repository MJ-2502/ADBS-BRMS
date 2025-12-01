@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Account review</p>
        <h1 class="text-xl font-semibold text-white">Resident verifications</h1>
    </div>
</div>

<form method="GET" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-400">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, email, or reference" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm bg-slate-800 text-white" />
    </div>
    <div>
        <label class="text-xs font-medium uppercase tracking-wide text-slate-400">Status</label>
        <select name="status" class="mt-1 w-full rounded-lg border border-slate-700 px-3 py-2 text-sm bg-slate-800 text-white">
            <option value="">All</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="flex-1 rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">Filter</button>
        <a href="{{ route('verifications.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="mt-6 flex items-center justify-between">
    <div class="text-xs text-slate-400">
        Select requests to perform bulk actions.
        <span id="selected-count" class="ml-2 rounded bg-slate-700 px-2 py-1 text-[10px] font-semibold text-slate-200">0 selected</span>
    </div>
    <form method="POST" action="{{ route('verifications.bulk-destroy') }}" id="bulk-delete-form" class="flex items-center gap-2">
        @csrf
        <button type="submit" id="bulk-delete-btn" class="rounded-lg bg-rose-700 px-3 py-2 text-xs font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50 hover:bg-rose-600" disabled
            onclick="return confirm('Delete all selected requests? This cannot be undone.');">
            Delete selected
        </button>
    </form>
</div>
    <div class="mt-2 overflow-hidden rounded-2xl border border-slate-800 bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
            <tr>
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-sky-500 focus:ring-sky-600" />
                </th>
                <th class="px-4 py-3">Applicant</th>
                <th class="px-4 py-3">Submitted</th>
                <th class="px-4 py-3">Residency</th>
                <th class="px-4 py-3">Proof</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Notes</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($requests as $requestRecord)
                <tr class="hover:bg-slate-800/60">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="ids[]" value="{{ $requestRecord->id }}" form="bulk-delete-form" class="row-checkbox h-4 w-4 rounded border-slate-600 bg-slate-800 text-sky-500 focus:ring-sky-600" />
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-white">{{ $requestRecord->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $requestRecord->email }}</p>
                        <p class="text-xs text-slate-400">{{ $requestRecord->contact_number ?? 'No contact number' }}</p>
                        @php
                            $matchingResident = \App\Models\Resident::query()
                                ->whereNull('user_id')
                                ->where('first_name', $requestRecord->first_name)
                                ->where('last_name', $requestRecord->last_name)
                                ->first();
                        @endphp
                        @if($matchingResident)
                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-sky-500/20 px-2 py-0.5 text-[10px] font-semibold text-sky-300">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Resident record exists
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-300">{{ $requestRecord->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 text-slate-300">
                        <p class="text-xs">{{ $requestRecord->address_line ?? 'No address' }}</p>
                        <p class="text-xs text-slate-500">{{ $requestRecord->purok ?? 'No purok' }}</p>
                        <p class="text-xs text-slate-500">{{ $requestRecord->years_of_residency }} yrs</p>
                    </td>
                    <td class="px-4 py-3">
                        @if($requestRecord->proof_document_path)
                            <a href="{{ route('verifications.proof', $requestRecord) }}" class="inline-flex items-center rounded border border-slate-600 px-2 py-1 text-xs font-semibold text-slate-200 hover:bg-slate-700">Download</a>
                        @else
                            <span class="text-slate-400">No file</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php($status = $requestRecord->status)
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status->badgeClasses() }}">{{ $status->label() }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-300 text-xs">{{ $requestRecord->review_notes ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="space-y-2">
                            @if($status === \App\Enums\VerificationStatus::Pending)
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('verifications.approve', $requestRecord) }}">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <button type="button" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700 toggle-reject" data-target="reject-panel-{{ $requestRecord->id }}">Reject</button>
                                </div>
                                <div id="reject-panel-{{ $requestRecord->id }}" class="mt-2 hidden">
                                    <form method="POST" action="{{ route('verifications.reject', $requestRecord) }}" class="flex flex-col gap-1">
                                        @csrf
                                        <textarea name="notes" rows="2" placeholder="Rejection reason" class="w-full rounded-lg border border-rose-700 px-2 py-1 text-xs bg-slate-900 text-white" required></textarea>
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 cancel-reject" data-target="reject-panel-{{ $requestRecord->id }}">Cancel</button>
                                            <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Confirm Reject</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <p class="text-right text-xs text-slate-400">No actions available</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-400">No verification requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $requests->links() }}
</div>

@push('scripts')
<script>
    (function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = () => Array.from(document.querySelectorAll('.row-checkbox'));
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const selectedCount = document.getElementById('selected-count');

        function updateBulkBtn() {
            const anyChecked = checkboxes().some(cb => cb.checked);
            bulkBtn.disabled = !anyChecked;
            if (selectedCount) {
                const count = checkboxes().filter(cb => cb.checked).length;
                selectedCount.textContent = `${count} selected`;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes().forEach(cb => cb.checked = selectAll.checked);
                updateBulkBtn();
            });
        }

        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('row-checkbox')) {
                if (!e.target.checked && selectAll) {
                    selectAll.checked = false;
                }
                updateBulkBtn();
            }
        });

        updateBulkBtn();

        // Toggle reject panels
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.toggle-reject');
            const cancel = e.target.closest('.cancel-reject');
            if (btn) {
                const id = btn.getAttribute('data-target');
                const panel = document.getElementById(id);
                if (panel) panel.classList.toggle('hidden');
            }
            if (cancel) {
                const id = cancel.getAttribute('data-target');
                const panel = document.getElementById(id);
                if (panel) panel.classList.add('hidden');
            }
        });
    })();
</script>
@endpush
@endsection
