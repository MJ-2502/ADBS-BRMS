<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public function log(string $event, ?string $description = null, array $context = []): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'description' => $description,
            'ip_address' => request()?->ip(),
            'context' => $context,
        ]);
    }
}
