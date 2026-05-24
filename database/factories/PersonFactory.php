<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BloodType;
use App\Models\Country;
use App\Models\CurrentStatus;
use App\Models\Degree;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Occupation;
use App\Models\Person;
use App\Models\Province;
use App\Models\Region;
use App\Models\TaxCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
final class PersonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'user_id' => null,
            'document_type_id' => DocumentType::query()->inRandomOrder()->value('id'),
            'document_number' => fake()->numerify('##########'),
            'first_name' => $gender === 'male' ? fake('es_ES')->firstNameMale() : fake('es_ES')->firstNameFemale(),
            'second_name' => fake()->boolean(40) ? ($gender === 'male' ? fake('es_ES')->firstNameMale() : fake('es_ES')->firstNameFemale()) : null,
            'last_name' => fake('es_ES')->lastName(),
            'second_last_name' => fake()->boolean(70) ? fake('es_ES')->lastName() : null,
            'birth_date' => fake()->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
            'gender_id' => Gender::query()->inRandomOrder()->value('id'),
            'blood_type_id' => BloodType::query()->inRandomOrder()->value('id'),
            'nationality_id' => Nationality::query()->inRandomOrder()->value('id'),
            'marital_status_id' => MaritalStatus::query()->inRandomOrder()->value('id'),
            'occupation_id' => Occupation::query()->inRandomOrder()->value('id'),
            'degree_id' => Degree::query()->inRandomOrder()->value('id'),
            'email' => fake()->boolean(80) ? fake()->unique()->safeEmail() : null,
            'phone_mobile' => fake()->boolean(90) ? fake()->numerify('+54 9 ### ###-####') : null,
            'phone_home' => fake()->boolean(40) ? fake()->numerify('+54 ### ##-####') : null,
            'phone_work' => fake()->boolean(30) ? fake()->numerify('+54 ### ##-####') : null,
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->boolean(30) ? fake()->secondaryAddress() : null,
            'country_id' => Country::query()->inRandomOrder()->value('id'),
            'province_id' => Province::query()->inRandomOrder()->value('id'),
            'region_id' => Region::query()->inRandomOrder()->value('id'),
            'postal_code' => fake()->numerify('#####'),
            'latitude' => fake()->boolean(50) ? fake()->latitude(-55, -22) : null,
            'longitude' => fake()->boolean(50) ? fake()->longitude(-73, -53) : null,
            'tax_condition_id' => TaxCondition::query()->inRandomOrder()->value('id'),
            'known_allergies' => fake()->boolean(25) ? fake()->sentence(6) : null,
            'emergency_contact_name' => fake()->boolean(75) ? fake('es_ES')->name() : null,
            'emergency_contact_phone' => fake()->boolean(75) ? fake()->numerify('+54 9 ### ###-####') : null,
            'emergency_contact_relationship' => fake()->boolean(75) ? fake()->randomElement(['Padre', 'Madre', 'Cónyuge', 'Hijo/a', 'Hermano/a', 'Amigo/a']) : null,
            'current_status_id' => CurrentStatus::query()->inRandomOrder()->value('id'),
            'photo_path' => null,
            'notes' => fake()->boolean(20) ? fake()->paragraph(2) : null,
        ];
    }

    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => \App\Models\User::factory(),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status_id' => CurrentStatus::query()->where('name', 'Activo')->value('id'),
        ]);
    }
}
