<?php

namespace App\Models;

use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CertificateFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_type',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public static function amountFor(CertificateType|string $type): float
    {
        $typeValue = $type instanceof CertificateType ? $type->value : $type;

        $fees = static::feeMap();

        return (float) ($fees[$typeValue] ?? 0.0);
    }

    public static function feeMap(): array
    {
        return Cache::rememberForever('certificate_fee_map', function (): array {
            return static::query()
                ->pluck('amount', 'certificate_type')
                ->map(fn ($amount) => (float) $amount)
                ->all();
        });
    }

    public static function syncFees(array $fees): void
    {
        foreach ($fees as $type => $amount) {
            static::query()->updateOrCreate(
                ['certificate_type' => $type],
                ['amount' => $amount]
            );
        }

        Cache::forget('certificate_fee_map');
    }
}
