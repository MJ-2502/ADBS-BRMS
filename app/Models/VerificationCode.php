<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    public const TYPE_EMAIL = 'email';
    public const TYPE_PHONE = 'phone';

    protected $fillable = [
        'type',
        'target',
        'code',
        'attempts',
        'verification_token',
        'expires_at',
        'verified_at',
        'used_at',
        'session_id',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at instanceof CarbonInterface && $this->expires_at->isPast();
    }

    public function markVerified(string $token): void
    {
        $this->forceFill([
            'verified_at' => now(),
            'verification_token' => $token,
        ])->save();
    }

    public function markUsed(): void
    {
        $this->forceFill([
            'used_at' => now(),
        ])->save();
    }
}
