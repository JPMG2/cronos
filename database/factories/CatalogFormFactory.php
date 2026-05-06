<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CatalogForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogForm>
 */
final class CatalogFormFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group' => $this->faker->randomElement(['Datos Personales', 'Profesional', 'Financiero']),
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'component' => 'configuracion.tablas.' . $this->faker->slug(1),
            'icon' => 'cog-6-tooth',
            'order' => $this->faker->numberBetween(1, 10),
            'permission_key' => null,
            'is_active' => true,
        ];
    }
}
