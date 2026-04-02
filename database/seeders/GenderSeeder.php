<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Seeder;

final class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            ['name' => 'Masculino'],
            ['name' => 'Femenino'],
            ['name' => 'No binario'],
            ['name' => 'Otro'],
        ];

        foreach ($genders as $gender) {
            Gender::query()->create($gender);
        }
    }
}
