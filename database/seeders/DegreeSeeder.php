<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Degree;
use Illuminate\Database\Seeder;

final class DegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $degrees = [
            ['name' => 'Licenciado en Enfermería', 'code' => 'Lic. Enf.'],
            ['name' => 'Técnico Superior Universitario', 'code' => 'TSU'],
            ['name' => 'Enfermero Auxiliar', 'code' => 'Aux.'],
            ['name' => 'Licenciado', 'code' => 'Lic.'],
            ['name' => 'Licenciada', 'code' => 'Licda.'],
            ['name' => 'Magíster', 'code' => 'Mag.'],
            ['name' => 'Ingeniero', 'code' => 'Ing.'],
            ['name' => 'Ingeniera', 'code' => 'Inga.'],
            ['name' => 'Abogado', 'code' => 'Abg.'],
            ['name' => 'Psicólogo', 'code' => 'Psic.'],
            ['name' => 'Doctor', 'code' => 'Dr.'],
            ['name' => 'Doctora', 'code' => 'Dra.'],
            ['name' => 'Doctor en Ciencias', 'code' => 'Ph.D.'],
            ['name' => 'Postdoctorado', 'code' => 'PostDoc'],
            ['name' => 'Médico Especialista', 'code' => 'Esp.'],
        ];
        foreach ($degrees as $degree) {
            Degree::query()->create($degree);
        }
    }
}
