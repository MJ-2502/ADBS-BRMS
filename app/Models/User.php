<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Resident;
use App\Models\CertificateRequest;
use App\Models\ActivityLog;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'purok',
        'address_line',
        'api_token',
        'is_active',
        'preferences',
        'verification_status',
        'verification_proof_path',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => UserRole::class,
            'verification_status' => VerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function generateApiToken(): string
    {
        $token = bin2hex(random_bytes(40));
        $this->forceFill(['api_token' => hash('sha256', $token)])->save();

        return $token;
    }

    public static function findByPlainTextToken(string $token): ?self
    {
        return static::where('api_token', hash('sha256', $token))->first();
    }

    public function residentProfile(): HasOne
    {
        return $this->hasOne(Resident::class);
    }

    public function resident(): HasOne
    {
        return $this->hasOne(Resident::class);
    }

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class, 'requested_by');
    }

    public function approvedCertificates(): HasMany
    {
        return $this->hasMany(CertificateRequest::class, 'approved_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(self::class, 'verified_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isClerk(): bool
    {
        return $this->role === UserRole::Clerk;
    }

    public function isResident(): bool
    {
        return $this->role === UserRole::Resident;
    }

    public function canManageRecords(): bool
    {
        return $this->isAdmin() || $this->isClerk();
    }

    public function canManageAccounts(): bool
    {
        return $this->isAdmin();
    }

    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::Approved;
    }
}
