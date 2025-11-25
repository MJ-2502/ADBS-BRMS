<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Household>
 */
class HouseholdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'household_number' => 'HH-' . $this->faker->unique()->numerify('#####'),
            'address_line' => $this->faker->streetAddress(),
            'purok' => 'Purok ' . $this->faker->numberBetween(1, 7),
            'zone' => 'Zone ' . $this->faker->numberBetween(1, 5),
            'head_name' => $this->faker->name(),
            'members_count' => $this->faker->numberBetween(2, 8),
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
        ];
    }
}
