@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Resident reference</p>
        <h1 class="text-2xl font-semibold text-white">{{ $resident->full_name }}</h1>
        <p class="text-sm text-slate-400 font-mono">{{ $resident->reference_id }}</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('residents.edit', $resident) }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">Edit</a>
        <form method="POST" action="{{ route('residents.destroy', $resident) }}" onsubmit="return confirm('Archive resident?')">
            @csrf
            @method('DELETE')
            <button class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Archive</button>
        </form>
    </div>
</div>
@php($age = $resident->birthdate ? $resident->birthdate->age : null)

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Personal details</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Birthdate</dt>
                <dd class="text-right font-medium text-white">
                    @if($resident->birthdate)
                        {{ $resident->birthdate->format('M d, Y') }}
                        @if($age)
                            <span class="text-xs text-slate-400">• {{ $age }} yrs</span>
                        @endif
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Gender</dt>
                <dd class="text-right font-medium text-white">{{ $resident->gender ? str($resident->gender)->headline() : '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Civil status</dt>
                <dd class="text-right font-medium text-white">{{ $resident->civil_status ? str($resident->civil_status)->headline() : '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Education</dt>
                <dd class="text-right font-medium text-white">{{ $resident->education ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Occupation</dt>
                <dd class="text-right font-medium text-white">{{ $resident->occupation ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Religion</dt>
                <dd class="text-right font-medium text-white">{{ $resident->religion ?? '—' }}</dd>
            </div>
        </dl>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Residency & household</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Household</dt>
                <dd class="text-right font-medium text-white">
                    @if($resident->household)
                        HH-{{ $resident->household->household_number }}
                    @else
                        Unassigned
                    @endif
                </dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Address</dt>
                <dd class="text-right font-medium text-white">{{ $resident->address_line ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Purok</dt>
                <dd class="text-right font-medium text-white">{{ $resident->purok ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Years of residency</dt>
                <dd class="text-right font-medium text-white">{{ $resident->years_of_residency ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Residency status</dt>
                <dd class="text-right font-medium text-white">{{ str($resident->residency_status)->headline() }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Voter</dt>
                <dd class="text-right font-medium text-white">
                    {{ $resident->is_voter === null ? 'Unknown' : ($resident->is_voter ? 'Registered' : 'Not registered') }}
                    @if($resident->voter_precinct)
                        <span class="block text-xs text-slate-400">Precinct {{ $resident->voter_precinct }}</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Contacts</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Contact number</dt>
                <dd class="text-right font-medium text-white">{{ $resident->contact_number ?? '—' }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-400">Email</dt>
                <dd class="text-right font-medium text-white">{{ $resident->email ?? '—' }}</dd>
            </div>
        </dl>
        <div class="mt-6 border-t border-slate-700 pt-4">
            <h3 class="text-sm font-semibold text-white">Linked Account</h3>
            @if($resident->user)
                <div class="mt-2 rounded-lg border border-slate-700 bg-slate-900/40 p-3">
                    <p class="text-sm font-medium text-white">{{ $resident->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $resident->user->email ?? $resident->user->phone }}</p>
                    <p class="text-xs text-slate-500">Role: {{ str($resident->user->role->value)->headline() }}</p>
                    @if(auth()->user()?->isAdmin())
                        <form method="POST" action="{{ route('residents.unlink', $resident) }}" class="mt-2" onsubmit="return confirm('Unlink this account from the resident record?')">
                            @csrf
                            <button class="text-xs font-semibold text-rose-400 hover:text-rose-300">Unlink account</button>
                        </form>
                    @endif
                </div>
            @else
                <p class="mt-2 text-xs text-slate-400">No account linked</p>
                @if(auth()->user()?->isAdmin())
                    <button type="button" onclick="document.getElementById('link-account-form-{{ $resident->id }}').classList.toggle('hidden')" class="mt-2 text-xs font-semibold text-sky-400 hover:text-sky-300">Link existing account</button>
                    <form id="link-account-form-{{ $resident->id }}" method="POST" action="{{ route('residents.link', $resident) }}" class="mt-2 hidden space-y-2">
                        @csrf
                        <select name="user_id" class="w-full rounded-lg border border-slate-700 px-2 py-1 text-xs bg-slate-900 text-white" required>
                            <option value="">Select account...</option>
                            @foreach(\App\Models\User::where('role', \App\Enums\UserRole::Resident)->whereDoesntHave('resident')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email ?? $user->phone }})</option>
                            @endforeach
                        </select>
                        <button class="w-full rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700">Link</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Notes & remarks</h2>
        <div class="mt-4 rounded-xl border border-slate-700 bg-slate-900/40 p-4 text-sm text-slate-200">
            @if($resident->remarks)
                {{ $resident->remarks }}
            @else
                <span class="text-slate-400">No remarks recorded.</span>
            @endif
        </div>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Certificate history</h2>
        <div class="mt-4 space-y-3 text-sm">
            @forelse($resident->certificateRequests as $request)
                <div class="rounded-xl border border-slate-700 px-4 py-3 bg-slate-900/30">
                    <p class="font-medium text-white">{{ $request->certificate_type->label() }}</p>
                    <p class="text-xs text-slate-400">{{ $request->reference_no }} • {{ $request->created_at->format('M d, Y') }}</p>
                    <span class="mt-1 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $request->status->badgeColor() }}">{{ str($request->status->value)->headline() }}</span>
                </div>
            @empty
                <p class="text-slate-400">No requests yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
