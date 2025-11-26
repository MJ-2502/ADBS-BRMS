<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdRecordFile;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HouseholdRecordController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function index(): View
    {
        $latestRecord = HouseholdRecordFile::with('uploader')->latest()->first();
        $recordHistory = HouseholdRecordFile::with('uploader')
            ->latest()
            ->paginate(10, ['*'], 'record_page');

        return view('households.records', [
            'recordPreview' => $this->buildRecordPreview($latestRecord),
            'recordHistory' => $recordHistory,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:20480'],
        ]);

        $file = $validated['file'];
        $disk = 'local';
        $nextVersion = (int) (HouseholdRecordFile::max('version') ?? 0) + 1;
        $timestamp = now()->format('Ymd-His');
        $sanitizedName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $storedName = $timestamp . '-' . ($sanitizedName ?: 'household-record') . '.' . $extension;
        $storagePath = $file->storeAs('household-records/v' . $nextVersion, $storedName, $disk);

        $record = HouseholdRecordFile::create([
            'version' => $nextVersion,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $storagePath,
            'disk' => $disk,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        $mergeSummary = $this->mergeIntoHouseholds($record);

        $this->activityLogger->log('household.record.uploaded', 'Uploaded household record file', [
            'record_id' => $record->id,
        ]);

        $statusMessage = 'Household record uploaded successfully.';
        if ($mergeSummary['processed'] > 0) {
            $statusMessage .= sprintf(
                ' Imported %d rows (%d created, %d updated, %d skipped).',
                $mergeSummary['processed'],
                $mergeSummary['created'],
                $mergeSummary['updated'],
                $mergeSummary['skipped']
            );
        } elseif ($mergeSummary['error']) {
            $statusMessage .= ' ' . $mergeSummary['error'];
        }

        return back()->with('status', $statusMessage);
    }

    public function download(HouseholdRecordFile $householdRecord): StreamedResponse|RedirectResponse
    {
        if (!Storage::disk($householdRecord->disk)->exists($householdRecord->storage_path)) {
            return back()->withErrors(['record' => 'Stored file is missing. Please upload a new copy.']);
        }

        return Storage::disk($householdRecord->disk)->download(
            $householdRecord->storage_path,
            $householdRecord->original_name
        );
    }

    public function template(): StreamedResponse
    {
        $filename = 'households-template-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Household Number',
            'Head Name',
            'Contact Number',
            'Purok',
            'Address',
            'Members Count',
        ];
        $households = Household::withCount('residents')->orderBy('household_number')->get([
            'household_number',
            'head_name',
            'contact_number',
            'purok',
            'address_line',
            'members_count',
        ]);

        return response()->streamDownload(function () use ($headers, $households): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($households as $household) {
                fputcsv($handle, [
                    $household->household_number,
                    $household->head_name,
                    $household->contact_number,
                    $household->purok,
                    $household->address_line,
                    $household->members_count ?? $household->residents_count ?? 0,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return array{processed:int,created:int,updated:int,skipped:int,error:?string}
     */
    private function mergeIntoHouseholds(HouseholdRecordFile $record): array
    {
        $result = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'error' => null,
        ];

        $extension = strtolower(pathinfo($record->original_name, PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'txt'], true)) {
            $result['error'] = 'Import skipped (only CSV/TXT files are supported for automatic merging).';

            return $result;
        }

        if (!Storage::disk($record->disk)->exists($record->storage_path)) {
            $result['error'] = 'Import failed (uploaded file missing from storage).';

            return $result;
        }

        $stream = Storage::disk($record->disk)->readStream($record->storage_path);
        if ($stream === false) {
            $result['error'] = 'Import failed (unable to read the uploaded file).';

            return $result;
        }

        $headers = null;

        while (($line = fgetcsv($stream)) !== false) {
            if ($headers === null) {
                $headers = $this->normalizeHeaders($line);
                continue;
            }

            if ($this->rowIsEmpty($line)) {
                $result['skipped']++;
                continue;
            }

            $payload = $this->mapHouseholdRow($headers, $line);

            if (!$payload || empty($payload['household_number']) || empty($payload['address_line'])) {
                $result['skipped']++;
                continue;
            }

            $identifier = ['household_number' => $payload['household_number']];
            $attributes = $payload;
            unset($attributes['household_number']);

            $household = Household::updateOrCreate($identifier, $attributes);

            if ($household->wasRecentlyCreated) {
                $result['created']++;
            } else {
                $result['updated']++;
            }

            $result['processed']++;
        }

        fclose($stream);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRecordPreview(?HouseholdRecordFile $record): array
    {
        $result = [
            'record' => $record,
            'headers' => [],
            'rows' => [],
            'truncated' => false,
            'error' => null,
        ];

        if (!$record) {
            return $result;
        }

        $extension = strtolower(pathinfo($record->original_name, PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'txt'], true)) {
            $result['error'] = strtoupper($extension) . ' preview is not supported. Download the file to review its contents.';

            return $result;
        }

        if (!Storage::disk($record->disk)->exists($record->storage_path)) {
            $result['error'] = 'Uploaded file is missing from storage. Please upload a new copy.';

            return $result;
        }

        $stream = Storage::disk($record->disk)->readStream($record->storage_path);
        if ($stream === false) {
            $result['error'] = 'Unable to open the stored file for preview.';

            return $result;
        }

        $headers = null;
        $rows = [];

        while (($line = fgetcsv($stream)) !== false) {
            if ($headers === null) {
                $headers = $this->normalizeHeaders($line);
                continue;
            }

            if ($this->rowIsEmpty($line)) {
                continue;
            }

            $rows[] = $this->padRow($headers, $line);

            if (count($rows) >= 25) {
                $result['truncated'] = true;
                break;
            }
        }

        fclose($stream);

        $result['headers'] = $headers ?? [];
        $result['rows'] = $rows;

        if (empty($result['headers'])) {
            $result['error'] = 'The uploaded file does not contain column headers.';
        }

        return $result;
    }

    /**
     * @param array<int, string|null> $headers
     * @return array<int, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(static function ($header, int $index): string {
            $trimmed = trim((string) $header);

            return $trimmed === '' ? 'Column ' . ($index + 1) : $trimmed;
        }, $headers, array_keys($headers));
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, string|null> $row
     * @return array<int, string>
     */
    private function padRow(array $headers, array $row): array
    {
        $normalized = [];
        foreach ($headers as $index => $_) {
            $normalized[$index] = isset($row[$index]) ? (string) $row[$index] : '';
        }

        return $normalized;
    }

    /**
     * @param array<int, string|null> $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, string|null> $row
     * @return array<string, mixed>|null
     */
    private function mapHouseholdRow(array $headers, array $row): ?array
    {
        $mapping = [];
        foreach ($headers as $index => $header) {
            $key = $this->normalizeHeaderKey($header);
            if ($key !== null) {
                $mapping[$key] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }
        }

        if ($mapping === []) {
            return null;
        }

        $payload = [];
        foreach ($mapping as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            switch ($key) {
                case 'household_number':
                case 'head_name':
                case 'contact_number':
                case 'purok':
                case 'zone':
                case 'notes':
                    $payload[$key] = $value;
                    break;
                case 'address_line':
                    $payload[$key] = $value;
                    break;
                case 'members_count':
                    $payload[$key] = max(0, (int) $value);
                    break;
            }
        }

        return $payload;
    }

    private function normalizeHeaderKey(string $header): ?string
    {
        $normalized = Str::of($header)
            ->lower()
            ->replace(['_', '-', '.'], ' ')
            ->squish()
            ->value();

        return match ($normalized) {
            'household number' => 'household_number',
            'head name' => 'head_name',
            'contact number' => 'contact_number',
            'purok' => 'purok',
            'zone' => 'zone',
            'address', 'address line' => 'address_line',
            'members count', 'members' => 'members_count',
            'notes' => 'notes',
            default => null,
        };
    }
}
