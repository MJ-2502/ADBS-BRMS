<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Clerk = 'clerk';
    case Resident = 'resident';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Clerk => 'Clerk / Encoder',
            self::Resident => 'Resident',
        };
    }

    public static function staffRoles(): array
    {
        return [self::Admin->value, self::Clerk->value];
    }
}
