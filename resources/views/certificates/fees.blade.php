@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-white">Certificate fees</h1>
<p class="mt-2 text-sm text-slate-400">Set the standard fee for each certificate type. These amounts automatically apply to every request.</p>

<form method="POST" action="{{ route('certificates.fees.update') }}" class="mt-6 space-y-4">
    @csrf
    @method('PUT')
    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-800/50">
        <table class="w-full text-sm">
            <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr>
                    <th class="px-4 py-3">Certificate type</th>
                    <th class="px-4 py-3">Fee</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $type)
                    <tr class="border-t border-slate-700">
                        <td class="px-4 py-3 font-medium text-white">{{ $type->label() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400">₱</span>
                                <input type="number" name="fees[{{ $type->value }}]" min="0" step="0.01" value="{{ number_format($fees[$type->value] ?? 0, 2, '.', '') }}" class="w-32 rounded-lg border border-slate-700 px-3 py-2 text-right bg-slate-900 text-white">
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-end">
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Save fees</button>
    </div>
</form>
@endsection
