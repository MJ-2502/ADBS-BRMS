<?php

namespace App\Enums;

enum CertificateStatus: string
{
    case Pending = 'pending';
    case ForReview = 'for_review';
    case Approved = 'approved';
    case Released = 'released';
    case Denied = 'denied';
    case Cancelled = 'cancelled';

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::ForReview => 'bg-sky-100 text-sky-800',
            self::Approved => 'bg-emerald-100 text-emerald-800',
            self::Released => 'bg-blue-100 text-blue-800',
            self::Denied, self::Cancelled => 'bg-rose-100 text-rose-800',
        };
    }
}
