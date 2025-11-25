<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $appends = ['full_name'];

    protected $casts = [
        'birthdate' => 'date',
        'is_voter' => 'boolean',
        'years_of_residency' => 'integer',
        'archived_at' => 'datetime',
        'contact_number' => 'encrypted',
        'address_line' => 'encrypted',
        'emergency_contact_name' => 'encrypted',
        'emergency_contact_number' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $resident): void {
            $resident->reference_id ??= (string) Str::uuid();
        });
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(collect([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->suffix,
            ])->filter()->implode(' '))
        );
    }
}
