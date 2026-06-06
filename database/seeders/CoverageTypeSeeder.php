<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CoverageType;
use Illuminate\Database\Seeder;

final class CoverageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ── Social / Seguro obligatorio ──────────────────────────────
            [
                'code' => 'obra_social',
                'name' => 'Obra Social',
                'category' => 'social',
                'applies_to' => 'insurance',
                'country_code' => 'ARG',
                'description' => 'Seguro de salud obligatorio vinculado al empleo formal. Regulado por SSSalud (Ley 23.660).',
                'order' => 1,
                'requires_insurance_data' => true,
            ],
            [
                'code' => 'eps',
                'name' => 'EPS',
                'category' => 'social',
                'applies_to' => 'insurance',
                'country_code' => 'COL',
                'description' => 'Entidad Promotora de Salud. Seguro obligatorio colombiano (Ley 100/1993).',
                'order' => 2,
                'requires_insurance_data' => true,
            ],
            [
                'code' => 'ivss',
                'name' => 'IVSS',
                'category' => 'social',
                'applies_to' => 'insurance',
                'country_code' => 'VEN',
                'description' => 'Instituto Venezolano de los Seguros Sociales. Seguro social estatal venezolano.',
                'order' => 3,
                'requires_insurance_data' => true,
            ],

            // ── Medicina privada / voluntaria ────────────────────────────
            [
                'code' => 'prepaga',
                'name' => 'Medicina Prepaga',
                'category' => 'privada',
                'applies_to' => 'insurance',
                'country_code' => 'ARG',
                'description' => 'Plan de salud privado voluntario. Regulado por SSSalud (Ley 26.682).',
                'order' => 4,
                'requires_insurance_data' => true,
            ],
            [
                'code' => 'hcm',
                'name' => 'Seguro HCM',
                'category' => 'privada',
                'applies_to' => 'insurance',
                'country_code' => 'VEN',
                'description' => 'Hospitalización, Cirugía y Maternidad. Póliza privada venezolana (SUDESEG).',
                'order' => 5,
                'requires_insurance_data' => true,
            ],
            [
                'code' => 'prepagada',
                'name' => 'Medicina Prepagada',
                'category' => 'privada',
                'applies_to' => 'insurance',
                'country_code' => 'COL',
                'description' => 'Plan complementario voluntario sobre la EPS. Colombia.',
                'order' => 6,
                'requires_insurance_data' => true,
            ],

            // ── Riesgos laborales ────────────────────────────────────────
            [
                'code' => 'art',
                'name' => 'ART',
                'category' => 'laboral',
                'applies_to' => 'art_company',
                'country_code' => 'ARG',
                'description' => 'Aseguradora de Riesgos del Trabajo. Regulada por SRT (Ley 24.557). Contratada por el empleador.',
                'order' => 7,
                'requires_insurance_data' => true,
            ],
            [
                'code' => 'arl',
                'name' => 'ARL',
                'category' => 'laboral',
                'applies_to' => 'art_company',
                'country_code' => 'COL',
                'description' => 'Administradora de Riesgos Laborales. Equivalente colombiano de la ART (Decreto 1295/1994).',
                'order' => 8,
                'requires_insurance_data' => true,
            ],

            // ── Convenio ─────────────────────────────────────────────────
            [
                'code' => 'convenio',
                'name' => 'Convenio',
                'category' => 'convenio',
                'applies_to' => 'convenio',
                'country_code' => null,
                'description' => 'Acuerdo directo entre la clínica y una empresa o institución. Sin intermediario asegurador.',
                'order' => 9,
                'requires_insurance_data' => true,
            ],

            // ── Particular ───────────────────────────────────────────────
            [
                'code' => 'particular',
                'name' => 'Particular',
                'category' => 'particular',
                'applies_to' => null,
                'country_code' => null,
                'description' => 'Pago directo por el paciente. Sin cobertura de terceros.',
                'order' => 10,
                'requires_insurance_data' => false,
            ],
        ];

        foreach ($types as $type) {
            CoverageType::firstOrCreate(['code' => $type['code']], $type);
        }
    }
}
