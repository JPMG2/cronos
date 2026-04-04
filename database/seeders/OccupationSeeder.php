<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Seeder;

final class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            ['name' => 'Doctor'],
            ['name' => 'Mantenimiento'],
            ['name' => 'Cocinero'],
            ['name' => 'Enfermero'],
            ['name' => 'Panadero'],
            ['name' => 'Secretaria'],
            ['name' => 'Contador'],
            ['name' => 'Ingeniero'],
            ['name' => 'Arquitecto'],
            ['name' => 'Abogado'],
            ['name' => 'Profesor'],
            ['name' => 'Estudiante'],
            ['name' => 'Farmacéutico'],
            ['name' => 'Jubilado'],
            ['name' => 'Desempleado'],
            ['name' => 'Electricista'],
            ['name' => 'Mecánico'],
            ['name' => 'Otro'],
        ];
        foreach ($occupations as $occupation) {
            Occupation::query()->create($occupation);
        }
    }
}
