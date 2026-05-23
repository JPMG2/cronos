<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TaxCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxCondition>
 */
final class TaxConditionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'code' => mb_strtoupper(fake()->unique()->lexify('??')),
            'discriminate_tax' => false,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function discriminates(): static
    {
        return $this->state(['discriminate_tax' => true]);
    }
}
