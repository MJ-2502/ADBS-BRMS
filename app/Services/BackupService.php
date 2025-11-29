<?php

namespace App\Services;

use App\Models\BackupJob;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use JsonException;
use RuntimeException;
use Throwable;

class BackupService
{
    private const HOUSEHOLD_COLUMNS = [
        'id',
        'household_number',
        'address_line',
        'purok',
        'zone',
        'head_name',
        'members_count',
        'notes',
        'created_at',
        'updated_at',
    ];

    private const RESIDENT_COLUMNS = [
        'id',
        'reference_id',
        'household_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
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
        'emergency_contact_name',
        'emergency_contact_number',
        'remarks',
        'archived_at',
        'created_at',
        'updated_at',
    ];

    private const CERTIFICATE_COLUMNS = [
        'id',
        'resident_id',
        'requested_by',
        'certificate_type',
        'purpose',
        'status',
        'remarks',
        'payload',
        'reference_no',
        'approved_by',
        'approved_at',
        'released_at',
        'fee',
        'expires_at',
        'pdf_path',
        'created_at',
        'updated_at',
    ];

    private const USER_COLUMNS = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'role',
        'phone',
        'purok',
        'address_line',
        'api_token',
        'is_active',
        'last_login_at',
        'preferences',
        'verification_status',
        'verification_proof_path',
        'verification_notes',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    public function run(?User $operator = null): BackupJob
    {
        $job = BackupJob::create([
            'file_path' => '',
            'status' => 'running',
            'ran_by' => $operator?->id,
            'started_at' => now(),
        ]);

        try {
            $payload = $this->snapshot();
            $filePath = 'backups/brms-' . now()->format('Ymd-His') . '.json';
            Storage::disk('local')->put($filePath, json_encode($payload, JSON_PRETTY_PRINT));

            $job->update([
                'file_path' => $filePath,
                'status' => 'completed',
                'completed_at' => now(),
                'metadata' => [
                    'size' => Storage::disk('local')->size($filePath),
                    'records' => [
                        'residents' => count($payload['residents']),
                        'households' => count($payload['households']),
                        'requests' => count($payload['certificate_requests']),
                        'users' => count($payload['users']),
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            $job->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            throw $exception;
        }

        return $job;
    }

    /**
     * @throws Throwable
     */
    public function restoreFromJob(BackupJob $job, ?User $operator = null): array
    {
        if (!$job->file_path || !Storage::disk('local')->exists($job->file_path)) {
            throw new RuntimeException('Backup file not found.');
        }

        $payload = $this->decodePayload(Storage::disk('local')->get($job->file_path));

        return $this->restoreFromPayload($payload, $operator, $job->file_path);
    }

    /**
     * @throws Throwable
     */
    public function restoreFromUploadedFile(UploadedFile $file, ?User $operator = null): array
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Unable to read uploaded backup file.');
        }

        $payload = $this->decodePayload($contents);

        return $this->restoreFromPayload($payload, $operator, $file->getClientOriginalName() ?? 'uploaded-backup');
    }

    /**
     * @throws Throwable
     */
    protected function restoreFromPayload(array $payload, ?User $operator, string $source): array
    {
        $users = $this->prepareUsers($payload['users'] ?? []);
        $households = $this->prepareHouseholds($payload['households'] ?? []);
        $residents = $this->prepareResidents($payload['residents'] ?? []);
        $requests = $this->prepareCertificateRequests($payload['certificate_requests'] ?? [], $users, $operator);

        Schema::disableForeignKeyConstraints();

        try {
            CertificateRequest::truncate();
            Resident::truncate();
            Household::truncate();
            User::truncate();

            foreach ($users as $record) {
                $this->persistUser($record);
            }

            foreach ($households as $record) {
                $this->persistHousehold($record);
            }

            foreach ($residents as $record) {
                $this->persistResident($record);
            }

            foreach ($requests as $record) {
                $this->persistCertificate($record);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        return [
            'source' => $source,
            'counts' => [
                'users' => count($users),
                'households' => count($households),
                'residents' => count($residents),
                'certificate_requests' => count($requests),
            ],
            'generated_at' => $payload['generated_at'] ?? null,
        ];
    }

    /**
     * @throws JsonException
     */
    protected function decodePayload(string $json): array
    {
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($payload)) {
            throw new RuntimeException('Invalid backup file supplied.');
        }

        return $payload;
    }

    protected function prepareHouseholds(array $records): array
    {
        return array_map(fn (array $record) => Arr::only($record, self::HOUSEHOLD_COLUMNS), $records);
    }

    protected function prepareResidents(array $records): array
    {
        return array_map(fn (array $record) => Arr::only($record, self::RESIDENT_COLUMNS), $records);
    }

    protected function prepareCertificateRequests(array $records, array $users, ?User $operator): array
    {
        $userIds = array_column($users, 'id');
        $userLookup = array_fill_keys($userIds, true);
        $fallbackUserId = $userIds[0] ?? $operator?->id ?? null;

        return array_map(function (array $record) use ($userLookup, $fallbackUserId): array {
            unset($record['resident'], $record['requester']);

            if (empty($record['requested_by']) || !isset($userLookup[$record['requested_by']])) {
                if (!$fallbackUserId) {
                    throw new RuntimeException('Cannot restore certificate requests because requester accounts are missing.');
                }

                $record['requested_by'] = $fallbackUserId;
            }

            if (!empty($record['approved_by']) && !isset($userLookup[$record['approved_by']])) {
                $record['approved_by'] = null;
            }

            return Arr::only($record, self::CERTIFICATE_COLUMNS);
        }, $records);
    }

    protected function prepareUsers(array $records): array
    {
        return array_map(fn (array $record) => Arr::only($record, self::USER_COLUMNS), $records);
    }

    protected function persistHousehold(array $record): void
    {
        $model = new Household();
        $model->forceFill($record);
        $model->saveQuietly();
    }

    protected function persistResident(array $record): void
    {
        $model = new Resident();
        $model->forceFill($record);
        $model->saveQuietly();
    }

    protected function persistCertificate(array $record): void
    {
        $model = new CertificateRequest();
        $model->forceFill($record);
        $model->saveQuietly();
    }

    protected function persistUser(array $record): void
    {
        $model = new User();
        $model->forceFill($record);
        $model->saveQuietly();
    }

    protected function snapshot(): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'households' => Household::with('residents:id,household_id,first_name,last_name,purok,residency_status')->get()->toArray(),
            'residents' => Resident::with('household:id,household_number')->get()->toArray(),
            'certificate_requests' => CertificateRequest::with(['resident:id,first_name,last_name', 'requester:id,name'])
                ->orderBy('created_at')
                ->get()
                ->toArray(),
            'users' => User::select(self::USER_COLUMNS)
                ->orderBy('id')
                ->get()
                ->map(function (User $user): array {
                    $visible = $user->makeVisible(['password', 'remember_token', 'api_token'])->toArray(); // include hidden auth secrets
                    return Arr::only($visible, self::USER_COLUMNS);
                })
                ->all(),
        ];
    }
}
