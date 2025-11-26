<?php

namespace App\Services;

use App\Models\Household;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HouseholdRecordArchive
{
    private const ARCHIVE_DIRECTORY = 'household-records';
    private const LOG_FILENAME = 'household-log.jsonl';

    public function appendHousehold(Household $household, User $user): void
    {
        try {
            Storage::disk('local')->makeDirectory(self::ARCHIVE_DIRECTORY);

            $payload = [
                'timestamp' => now()->toIso8601String(),
                'household_id' => $household->id,
                'household_number' => $household->household_number,
                'head_name' => $household->head_name,
                'purok' => $household->purok,
                'encoded_by' => $user->only(['id', 'name', 'email']),
            ];

            Storage::disk('local')->append(
                self::ARCHIVE_DIRECTORY . '/' . self::LOG_FILENAME,
                json_encode(array_filter($payload, static fn ($value) => $value !== null), JSON_THROW_ON_ERROR)
            );
        } catch (\Throwable $exception) {
            Log::warning('Failed to append household record archive entry.', [
                'exception' => $exception->getMessage(),
                'household_id' => $household->id,
            ]);
        }
    }
}
