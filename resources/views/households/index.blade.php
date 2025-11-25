@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Households</h1>
    <a href="{{ route('households.create') }}" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">Add household</a>
</div>
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-800/50">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
                <th class="px-4 py-3">Number</th>
                <th class="px-4 py-3">Head</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Members</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($households as $household)
                <tr class="border-t border-slate-100 dark:border-slate-700">
                    <td class="px-4 py-3 font-mono text-xs dark:text-slate-300">{{ $household->household_number }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $household->head_name ?? '—' }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $household->address_line }}</td>
                    <td class="px-4 py-3 dark:text-slate-200">{{ $household->members_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('households.edit', $household) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">Edit</a>
                        <form method="POST" action="{{ route('households.destroy', $household) }}" class="inline" onsubmit="return confirm('Delete household?')">
                            @csrf
                            @method('DELETE')
                            <button class="ml-2 text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $households->links() }}
</div>
@endsection
