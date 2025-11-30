@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Data imports</p>
        <h1 class="text-xl font-semibold text-white">Barangay household records</h1>
        <p class="text-sm text-slate-400">Upload CSV or Excel files to version barangay-wide household datasets and keep an auditable timeline.</p>
    </div>
    <a href="{{ route('households.index') }}" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-800">Back to households</a>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-800 bg-slate-800/50 p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-white">Upload barangay record</h2>
        <p class="mt-1 text-xs text-slate-400">Accepted formats: CSV, XLS, XLSX (max 20 MB). Files are stored privately and versioned automatically.</p>
        <form method="POST" action="{{ route('household-records.store') }}" class="mt-4 space-y-4" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-300">File</label>
                <input type="file" name="file" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white file:mr-4 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-100" accept=".csv,.xls,.xlsx" />
            </div>
            <button class="w-full rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">Upload &amp; version</button>
        </form>
        <hr class="my-4 border-slate-700">
        <p class="text-xs text-slate-400">Need a fresh template? Download a CSV snapshot of the currently encoded households.</p>
        <a href="{{ route('household-records.template') }}" class="mt-2 inline-flex w-full items-center justify-center rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-900">Download template</a>
    </div>
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 shadow-sm">
            <div class="flex items-start justify-between gap-3 border-b border-slate-700 px-4 py-3">
                <div>
                    <h2 class="text-sm font-semibold text-white">Latest uploaded record</h2>
                    @if($recordPreview['record'])
                        <p class="text-xs text-slate-400">Version v{{ $recordPreview['record']->version }} • Uploaded {{ $recordPreview['record']->created_at->diffForHumans() }} by {{ $recordPreview['record']->uploader?->name ?? 'Unknown' }}</p>
                    @else
                        <p class="text-xs text-slate-400">No uploads yet. Download the template, populate it, then upload to keep an auditable history.</p>
                    @endif
                </div>
                @if($recordPreview['record'])
                    <a href="{{ route('household-records.download', $recordPreview['record']) }}" class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">Download file</a>
                @endif
            </div>
            <div class="p-4">
                @if(!$recordPreview['record'])
                    <p class="text-sm text-slate-400">Once a CSV or Excel file is uploaded, its first 25 non-empty rows will appear here for quick review.</p>
                @elseif($recordPreview['error'])
                    <div class="rounded border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-sm text-amber-100">
                        {{ $recordPreview['error'] }}
                    </div>
                @elseif(empty($recordPreview['rows']))
                    <p class="text-sm text-slate-400">The uploaded file is empty. Upload an updated copy to see its contents here.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-900/50 text-left uppercase tracking-wide text-slate-400">
                                <tr>
                                    @foreach($recordPreview['headers'] as $header)
                                        <th class="px-3 py-2">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recordPreview['rows'] as $row)
                                    <tr class="border-t border-slate-700 text-slate-200">
                                        @foreach($row as $value)
                                            <td class="px-3 py-2">{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($recordPreview['truncated'])
                        <p class="mt-2 text-xs text-slate-400">Preview limited to first 25 rows. Download the file for the full dataset.</p>
                    @endif
                @endif
            </div>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 shadow-sm">
            <div class="border-b border-slate-700 px-4 py-3">
                <h2 class="text-sm font-semibold text-white">Upload history</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Version</th>
                            <th class="px-4 py-3">File name</th>
                            <th class="px-4 py-3">Uploaded by</th>
                            <th class="px-4 py-3">Size</th>
                            <th class="px-4 py-3">Uploaded at</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recordHistory as $record)
                            <tr class="border-t border-slate-700">
                                <td class="px-4 py-3 font-semibold text-white">v{{ $record->version }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-100">{{ $record->original_name }}</p>
                                    <p class="text-xs text-slate-400">{{ strtoupper($record->mime_type ?? 'N/A') }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $record->uploader?->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-slate-300">
                                    {{ $record->file_size ? number_format($record->file_size / 1024, 2) . ' KB' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $record->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('household-records.download', $record) }}" class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">Download</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">No household record uploads yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-700 px-4 py-3">
                {{ $recordHistory->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
