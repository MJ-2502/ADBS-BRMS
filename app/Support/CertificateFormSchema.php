<?php

namespace App\Support;

use App\Enums\CertificateType;
use Illuminate\Support\Arr;

class CertificateFormSchema
{
    public static function for(CertificateType|string $type): array
    {
        $key = $type instanceof CertificateType ? $type->value : (string) $type;

        return config('certificate_forms.' . $key, []);
    }

    public static function requiresDetails(CertificateType|string $type): bool
    {
        $schema = self::for($type);

        return (bool) ($schema['requires_details'] ?? false);
    }

    public static function rules(CertificateType|string $type): array
    {
        $fields = Arr::get(self::for($type), 'fields', []);

        return collect($fields)
            ->filter(fn (array $field): bool => isset($field['name']))
            ->mapWithKeys(fn (array $field): array => [
                $field['name'] => $field['rules'] ?? ['nullable'],
            ])
            ->all();
    }
}
