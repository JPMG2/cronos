<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Degree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Degree>
 */
final class DegreeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'code' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function withCode(): static
    {
        return $this->state(['code' => mb_strtoupper(fake()->unique()->lexify('??'))]);
    }
}
