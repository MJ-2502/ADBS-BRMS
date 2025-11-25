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
        return [
            'resident_id' => Resident::factory(),
            'requested_by' => User::factory(),
            'certificate_type' => $this->faker->randomElement(CertificateType::cases())->value,
            'purpose' => $this->faker->randomElement(['Employment', 'Scholarship', 'Personal Record']),
            'status' => $this->faker->randomElement(CertificateStatus::cases())->value,
            'remarks' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            'payload' => [
                'issued_to' => $this->faker->name(),
                'address' => $this->faker->streetAddress(),
            ],
            'fee' => 50,
            'reference_no' => 'BRMS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
        ];
    }
}
