<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentType>
 */
final class DocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => mb_strtoupper(fake()->unique()->lexify('??')),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'short_name' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
