<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Resident>
 */
class ResidentFactory extends Factory
{
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'reference_id' => (string) Str::uuid(),
            'household_id' => Household::factory(),
            'first_name' => $this->faker->firstName($gender === 'male' ? 'male' : 'female'),
            'middle_name' => $this->faker->lastName(),
            'last_name' => $this->faker->lastName(),
            'suffix' => null,
            'birthdate' => $this->faker->dateTimeBetween('-70 years', '-18 years'),
            'gender' => $gender,
            'civil_status' => $this->faker->randomElement(['single', 'married', 'widowed']),
            'occupation' => $this->faker->jobTitle(),
            'religion' => $this->faker->randomElement(['Roman Catholic', 'Iglesia ni Cristo', 'Born Again']),
            'years_of_residency' => $this->faker->numberBetween(1, 40),
            'residency_status' => 'active',
            'is_voter' => $this->faker->boolean(70),
            'voter_precinct' => $this->faker->boolean(70) ? 'Precinct ' . $this->faker->numberBetween(1000, 9999) : null,
            'contact_number' => $this->faker->numerify('09#########'),
            'email' => $this->faker->safeEmail(),
            'address_line' => $this->faker->streetAddress(),
            'purok' => 'Purok ' . $this->faker->numberBetween(1, 7),
            'education' => $this->faker->randomElement(['Elementary', 'High School', 'College Graduate']),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_number' => $this->faker->numerify('09#########'),
            'remarks' => null,
        ];
    }
}
