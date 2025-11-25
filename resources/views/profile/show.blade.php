@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-slate-800 dark:text-white">My profile</h1>
<form method="POST" action="{{ route('profile.update') }}" class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
    @csrf
    @method('PUT')
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Purok</label>
            <input type="text" name="purok" value="{{ old('purok', $user->purok) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Address</label>
        <input type="text" name="address_line" value="{{ old('address_line', $user->address_line) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
    </div>
    <div class="flex items-center justify-end gap-3">
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Update profile</button>
    </div>
</form>
@if($user->api_token)
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
        <h2 class="text-base font-semibold text-slate-800 dark:text-white">API token</h2>
        <p class="mt-2 font-mono text-xs text-slate-500 dark:text-slate-400">****** stored securely. Generate a new token to view it.</p>
    </div>
@endif
<div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-800/50">
    <h2 class="text-base font-semibold text-slate-800 dark:text-white">Generate API token</h2>
    <form id="token-form" method="POST" action="{{ route('profile.token') }}" class="mt-4 space-y-3">
        @csrf
        <div>
            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Confirm password</label>
            <input type="password" name="password" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>
        </div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">Generate token</button>
        <div id="token-output" class="hidden rounded border border-emerald-200 bg-emerald-50 px-4 py-2 font-mono text-xs text-emerald-700"></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('token-form')?.addEventListener('submit', async function (event) {
        event.preventDefault();
        const form = event.currentTarget;
        const output = document.getElementById('token-output');
        output.classList.add('hidden');
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ password: form.password.value }),
        });

        if (!response.ok) {
            alert('Unable to generate token. Check password.');
            return;
        }

        const data = await response.json();
        output.textContent = data.token;
        output.classList.remove('hidden');
    });
</script>
@endpush
