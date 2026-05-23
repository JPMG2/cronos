<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaritalStatus>
 */
final class MaritalStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
