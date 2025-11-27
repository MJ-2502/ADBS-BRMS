@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Accounts</p>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Edit official account</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Update staff credentials or deactivate access for barangay admins or clerks.</p>
    </div>
    <a href="{{ route('accounts.officials.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Back to list</a>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('accounts.officials.update', $official) }}" class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-800/50">
        @csrf
        @method('PUT')
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Full name</label>
                <input type="text" name="name" value="{{ old('name', $official->name) }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email', $official->email) }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 mt-4">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Role</label>
                <select name="role" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $official->role->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Temporary password</label>
                <input type="text" name="password" value="{{ old('password') }}" placeholder="Leave blank to keep" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 mt-4">
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $official->phone) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Purok</label>
                <input type="text" name="purok" value="{{ old('purok', $official->purok) }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
        </div>
        <div class="mt-4">
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Address</label>
            <textarea name="address_line" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('address_line', $official->address_line) }}</textarea>
        </div>
        <div class="mt-4">
            <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Status</label>
            <select name="is_active" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="1" @selected(old('is_active', $official->is_active) == true)>Active</option>
                <option value="0" @selected(old('is_active', $official->is_active) == false)>Disabled</option>
            </select>
        </div>
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('accounts.officials.index') }}" class="text-sm text-slate-500 dark:text-slate-400">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600">Save changes</button>
        </div>
    </form>
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-100">
        <h2 class="text-base font-semibold">Danger zone</h2>
        <p class="mt-2">Deleting this official immediately revokes dashboard access. This cannot be undone.</p>
        <form method="POST" action="{{ route('accounts.officials.destroy', $official) }}" class="mt-4" onsubmit="return confirm('Delete this official account? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="w-full rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete account</button>
        </form>
    </div>
</div>
@endsection
