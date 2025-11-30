@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Create resident account</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Link an encoded resident to a portal login.</p>
    </div>
    <a href="{{ route('accounts.residents.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Back to list</a>
</div>

@if($availableResidents->isEmpty())
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-200">
        All recorded residents already have accounts. Add a new resident first, then return here to provision access.
    </div>
@else
    @php($selectedResidentId = old('resident_id', $selectedResident?->id))
    @php($prefillResidentLabel = old('resident_lookup', $selectedResident ? $selectedResident->full_name . ' (Ref ' . $selectedResident->reference_id . ')' : ''))
    @php($prefillEmail = old('email', $selectedResident?->email))
    @php($prefillPhone = old('phone', $selectedResident?->contact_number))
    @php($prefillAddress = old('address_line', $selectedResident?->address_line))
    @php($prefillPurok = old('purok', $selectedResident?->purok))
    @php($residentLookupOptions = $availableResidents->map(function ($residentOption) {
        $metaBits = array_filter([
            $residentOption->address_line,
            $residentOption->purok ? 'Purok ' . $residentOption->purok : null,
        ]);

        return [
            'id' => (string) $residentOption->id,
            'label' => $residentOption->full_name . ' (Ref ' . $residentOption->reference_id . ')',
            'meta' => implode(' • ', $metaBits),
        ];
    }))
    <form method="POST" action="{{ route('accounts.residents.store') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
        @csrf
        <div data-resident-picker class="space-y-2" data-resident-options='@json($residentLookupOptions)'>
            <label for="resident_lookup" class="text-xs font-medium text-slate-500 dark:text-slate-300">Resident to link</label>
            <div class="relative">
                <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:ring-2 focus-within:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 17.5l-3.75-3.75m.416-4.583a5.833 5.833 0 11-11.666 0 5.833 5.833 0 0111.666 0z" />
                    </svg>
                    <input type="text" name="resident_lookup" id="resident_lookup" data-resident-search-input value="{{ $prefillResidentLabel }}" placeholder="Search by name or reference" autocomplete="off" class="w-full bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:outline-none dark:text-slate-100" required>
                </div>
                <input type="hidden" name="resident_id" value="{{ $selectedResidentId }}" data-resident-id-field>
                <div data-resident-results class="absolute left-0 right-0 top-full z-20 mt-1 hidden max-h-60 overflow-auto rounded-lg border border-slate-200 bg-white text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900"></div>
            </div>
            <p class="text-[11px] uppercase tracking-wide text-slate-400">Start typing to search for an encoded resident.</p>
            <p data-resident-empty class="hidden text-xs text-rose-500">No residents match your search.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Email</label>
                <input type="email" name="email" value="{{ $prefillEmail }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Temporary password</label>
                <input type="text" name="password" value="{{ old('password') }}" placeholder="Minimum 8 characters" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Contact number</label>
                <input type="text" name="phone" value="{{ $prefillPhone }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Purok</label>
                <input type="text" name="purok" value="{{ $prefillPurok }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Address</label>
            <textarea name="address_line" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ $prefillAddress }}</textarea>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Account status</label>
            <select name="is_active" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="1" @selected(old('is_active', '1') === '1')>Active</option>
                <option value="0" @selected(old('is_active') === '0')>Disabled</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('accounts.residents.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Create account</button>
        </div>
    </form>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buildIndex = (residents) => residents.map((resident) => ({
            ...resident,
            search: `${resident.label} ${(resident.meta ?? '')}`.toLowerCase(),
        }));

        document.querySelectorAll('[data-resident-picker]').forEach((picker) => {
            const input = picker.querySelector('[data-resident-search-input]');
            const hiddenField = picker.querySelector('[data-resident-id-field]');
            const resultsPanel = picker.querySelector('[data-resident-results]');
            const emptyState = picker.querySelector('[data-resident-empty]');

            if (!input || !hiddenField || !resultsPanel) {
                return;
            }

            let residents = [];
            try {
                const parsed = JSON.parse(picker.getAttribute('data-resident-options') ?? '[]');
                residents = buildIndex(Array.isArray(parsed) ? parsed : []);
            } catch (error) {
                residents = [];
            }

            const hideResults = () => resultsPanel.classList.add('hidden');
            const showResults = () => {
                if (resultsPanel.childElementCount > 0) {
                    resultsPanel.classList.remove('hidden');
                }
            };

            const renderResults = (items) => {
                resultsPanel.innerHTML = '';
                items.forEach((resident) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.dataset.residentOption = resident.id;
                    option.className = 'flex w-full flex-col gap-0.5 px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800';
                    option.innerHTML = `<span class="font-medium">${resident.label}</span>` +
                        (resident.meta ? `<span class="text-xs text-slate-500 dark:text-slate-400">${resident.meta}</span>` : '');
                    resultsPanel.appendChild(option);
                });
            };

            const updateResults = () => {
                const query = input.value.trim().toLowerCase();
                const matches = (query.length === 0 ? residents : residents.filter((resident) => resident.search.includes(query))).slice(0, 12);

                if (matches.length === 0) {
                    resultsPanel.innerHTML = '';
                    hideResults();
                    if (emptyState) {
                        emptyState.classList.toggle('hidden', query.length === 0);
                    }
                    return;
                }

                if (emptyState) {
                    emptyState.classList.add('hidden');
                }

                renderResults(matches);
                showResults();
            };

            const clearSelection = () => {
                hiddenField.value = '';
            };

            const setSelection = (resident) => {
                hiddenField.value = resident.id;
                input.value = resident.label;
                hideResults();
            };

            input.addEventListener('input', () => {
                clearSelection();
                updateResults();
            });

            input.addEventListener('focus', () => {
                updateResults();
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    hideResults();
                }
            });

            resultsPanel.addEventListener('click', (event) => {
                const option = event.target.closest('[data-resident-option]');
                if (!option) {
                    return;
                }

                const resident = residents.find((item) => item.id === option.dataset.residentOption);
                if (resident) {
                    setSelection(resident);
                }
            });

            document.addEventListener('click', (event) => {
                if (!picker.contains(event.target)) {
                    hideResults();
                }
            });
        });
    });
</script>
@endpush
