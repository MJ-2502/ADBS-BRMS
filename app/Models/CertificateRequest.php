<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'payload' => 'array',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'expires_at' => 'datetime',
        'fee' => 'decimal:2',
        'certificate_type' => CertificateType::class,
        'status' => CertificateStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            $request->reference_no ??= 'BRMS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        });
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status?->badgeColor()
        );
    }
}
