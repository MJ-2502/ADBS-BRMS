<?php

namespace Database\Factories;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\CertificateRequest>
 */
class CertificateRequestFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(CertificateType::cases());
        $payload = $this->makePayload($type);

        return [
            'resident_id' => Resident::factory(),
            'requested_by' => User::factory(),
            'certificate_type' => $type->value,
            'purpose' => $this->faker->randomElement(['Employment', 'Scholarship', 'Personal Record']),
            'status' => $this->faker->randomElement(CertificateStatus::cases())->value,
            'remarks' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            'payload' => $payload,
            'fee' => 50,
            'reference_no' => 'BRMS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
        ];
    }

    protected function makePayload(CertificateType $type): array
    {
        $schemas = config('certificate_forms');
        $fields = $schemas[$type->value]['fields'] ?? [];

        $payload = [];
        foreach ($fields as $field) {
            $payload[$field['name']] = $this->fakeForField($field['type'] ?? 'text');
        }

        return $payload;
    }

    protected function fakeForField(string $type): string
    {
        return match ($type) {
            'number' => (string) $this->faker->numberBetween(1, 100),
            'date' => $this->faker->date('Y-m-d'),
            'textarea' => $this->faker->sentence(12),
            default => $this->faker->words(3, true),
        };
    }
}
