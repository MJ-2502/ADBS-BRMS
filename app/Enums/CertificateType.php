<?php

namespace App\Enums;

enum CertificateType: string
{
    case BarangayClearance = 'barangay_clearance';
    case Residency = 'certificate_of_residency';
    case Indigency = 'certificate_of_indigency';
    case BusinessClearance = 'business_clearance';
    case GoodMoral = 'certificate_of_good_moral_character';

    public function label(): string
    {
        return match ($this) {
            self::BarangayClearance => 'Barangay Clearance',
            self::Residency => 'Certificate of Residency',
            self::Indigency => 'Certificate of Indigency',
            self::BusinessClearance => 'Business Clearance',
            self::GoodMoral => 'Certificate of Good Moral Character',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()])
            ->all();
    }
}
