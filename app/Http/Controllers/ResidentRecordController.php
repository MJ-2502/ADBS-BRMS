<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Resident;
use App\Models\ResidentRecordFile;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentRecordController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function index(): View
    {
        $latestRecord = ResidentRecordFile::with('uploader')->latest()->first();
        $recordHistory = ResidentRecordFile::with('uploader')
            ->latest()
            ->paginate(10, ['*'], 'record_page');

        return view('residents.records', [
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
        $nextVersion = (int) (ResidentRecordFile::max('version') ?? 0) + 1;
        $timestamp = now()->format('Ymd-His');
        $sanitizedName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $storedName = $timestamp . '-' . ($sanitizedName ?: 'resident-record') . '.' . $extension;
        $storagePath = $file->storeAs('resident-records/v' . $nextVersion, $storedName, $disk);

        $record = ResidentRecordFile::create([
            'version' => $nextVersion,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $storagePath,
            'disk' => $disk,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        $mergeSummary = $this->mergeIntoResidents($record);

        $this->activityLogger->log('resident.record.uploaded', 'Uploaded resident record file', [
            'record_id' => $record->id,
        ]);

        $statusMessage = 'Resident record uploaded successfully.';
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

    public function download(ResidentRecordFile $residentRecord): StreamedResponse|RedirectResponse
    {
        if (!Storage::disk($residentRecord->disk)->exists($residentRecord->storage_path)) {
            return back()->withErrors(['record' => 'Stored file is missing. Please upload a new copy.']);
        }

        return Storage::disk($residentRecord->disk)->download(
            $residentRecord->storage_path,
            $residentRecord->original_name
        );
    }

    public function template(): StreamedResponse
    {
        $filename = 'residents-template-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Reference ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Suffix',
            'Household Number',
            'Birthdate',
            'Gender',
            'Civil Status',
            'Occupation',
            'Religion',
            'Years of Residency',
            'Residency Status',
            'Is Voter',
            'Voter Precinct',
            'Contact Number',
            'Email',
            'Address Line',
            'Purok',
            'Education',
            'Remarks',
        ];

        $residents = Resident::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get([
                'reference_id',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'household_id',
                'birthdate',
                'gender',
                'civil_status',
                'occupation',
                'religion',
                'years_of_residency',
                'residency_status',
                'is_voter',
                'voter_precinct',
                'contact_number',
                'email',
                'address_line',
                'purok',
                'education',
                'remarks',
            ]);

        $householdMap = Household::pluck('household_number', 'id');

        return response()->streamDownload(function () use ($headers, $residents, $householdMap): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($residents as $resident) {
                fputcsv($handle, [
                    $resident->reference_id,
                    $resident->first_name,
                    $resident->middle_name,
                    $resident->last_name,
                    $resident->suffix,
                    $resident->household_id ? ($householdMap[$resident->household_id] ?? '') : '',
                    optional($resident->birthdate)?->format('Y-m-d'),
                    $resident->gender,
                    $resident->civil_status,
                    $resident->occupation,
                    $resident->religion,
                    $resident->years_of_residency,
                    $resident->residency_status,
                    $resident->is_voter ? 'Yes' : 'No',
                    $resident->voter_precinct,
                    $resident->contact_number,
                    $resident->email,
                    $resident->address_line,
                    $resident->purok,
                    $resident->education,
                    $resident->remarks,
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
    private function mergeIntoResidents(ResidentRecordFile $record): array
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

            $payload = $this->mapResidentRow($headers, $line);

            if (!$payload || empty($payload['first_name']) || empty($payload['last_name'])) {
                $result['skipped']++;
                continue;
            }

            $referenceId = $payload['reference_id'] ?? null;
            $householdNumber = $payload['household_number'] ?? null;
            $householdId = $payload['household_id'] ?? null;
            unset($payload['reference_id'], $payload['household_number']);

            if (!$householdId && $householdNumber) {
                $householdId = Household::where('household_number', $householdNumber)->value('id');
            }

            if ($householdId) {
                $payload['household_id'] = $householdId;
            } else {
                $payload['household_id'] = null;
            }

            $this->normalizeResidentPayload($payload);

            $resident = $this->resolveResident($referenceId, $payload);

            if ($resident) {
                $resident->fill($payload);
                $resident->save();
                $result['updated']++;
            } else {
                $createPayload = array_merge([
                    'reference_id' => $referenceId ?: (string) Str::uuid(),
                ], $payload);

                Resident::create($createPayload);
                $result['created']++;
            }

            $result['processed']++;
        }

        fclose($stream);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRecordPreview(?ResidentRecordFile $record): array
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
    private function mapResidentRow(array $headers, array $row): ?array
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

            $payload[$key] = $value;
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
            'reference', 'reference id', 'resident reference' => 'reference_id',
            'household number' => 'household_number',
            'household id' => 'household_id',
            'first name' => 'first_name',
            'middle name' => 'middle_name',
            'last name' => 'last_name',
            'suffix' => 'suffix',
            'birthdate', 'birthday', 'date of birth', 'dob' => 'birthdate',
            'gender' => 'gender',
            'civil status' => 'civil_status',
            'occupation' => 'occupation',
            'religion' => 'religion',
            'years of residency', 'years residency', 'years' => 'years_of_residency',
            'residency status', 'status' => 'residency_status',
            'is voter', 'voter', 'voter status' => 'is_voter',
            'voter precinct', 'precinct' => 'voter_precinct',
            'contact number', 'contact', 'phone', 'mobile' => 'contact_number',
            'email', 'email address' => 'email',
            'address', 'address line' => 'address_line',
            'purok' => 'purok',
            'education' => 'education',
            'remarks', 'notes' => 'remarks',
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function normalizeResidentPayload(array &$payload): void
    {
        if (isset($payload['birthdate'])) {
            $payload['birthdate'] = $this->parseDate($payload['birthdate']);
        }

        if (isset($payload['years_of_residency'])) {
            $payload['years_of_residency'] = max(0, (int) $payload['years_of_residency']);
        }

        if (isset($payload['household_id'])) {
            $payload['household_id'] = $payload['household_id'] ? (int) $payload['household_id'] : null;
        }

        if (isset($payload['is_voter'])) {
            $bool = $this->parseBoolean($payload['is_voter']);
            $payload['is_voter'] = $bool;
        }


        if (!isset($payload['residency_status'])) {
            $payload['residency_status'] = null;
        }
    }

    private function parseDate(string $value): ?string
    {
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function parseBoolean(string $value): ?bool
    {
        $normalized = Str::of($value)->lower()->squish()->value();
        if ($normalized === '') {
            return null;
        }

        return match ($normalized) {
            'yes', 'y', 'true', '1', 'voter' => true,
            'no', 'n', 'false', '0' => false,
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveResident(?string $referenceId, array $payload): ?Resident
    {
        if ($referenceId) {
            return Resident::where('reference_id', $referenceId)->first();
        }

        if (!empty($payload['email'])) {
            return Resident::where('email', $payload['email'])->first();
        }

        if (!empty($payload['first_name']) && !empty($payload['last_name']) && !empty($payload['birthdate'])) {
            return Resident::where('first_name', $payload['first_name'])
                ->where('last_name', $payload['last_name'])
                ->whereDate('birthdate', $payload['birthdate'])
                ->first();
        }

        return null;
    }
}
