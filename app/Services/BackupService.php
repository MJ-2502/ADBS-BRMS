<?php

namespace App\Services;

use App\Models\BackupJob;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackupService
{
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
            'users' => User::select('id', 'name', 'email', 'role', 'is_active')->get()->toArray(),
        ];
    }
}
