<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CatalogForm;
use Illuminate\Database\Seeder;

final class CatalogFormSeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = [
            // ── Datos Personales ──────────────────────────────────────────
            [
                'group' => 'Datos Personales',
                'title' => 'Género',
                'description' => 'Géneros disponibles para registro de personas',
                'component' => 'configuracion.tablas.gender',
                'icon' => 'user-circle',
                'order' => 1,
                'permission_key' => 'catalog.gender.manage',
            ],
            [
                'group' => 'Datos Personales',
                'title' => 'Estado Civil',
                'description' => 'Estados civiles para registro de personas',
                'component' => 'configuracion.tablas.marital-status',
                'icon' => 'heart',
                'order' => 2,
                'permission_key' => 'catalog.marital_status.manage',
            ],
            [
                'group' => 'Datos Personales',
                'title' => 'Tipo de Documento',
                'description' => 'Tipos de documentos de identidad aceptados',
                'component' => 'configuracion.tablas.document-type',
                'icon' => 'identification',
                'order' => 3,
                'permission_key' => 'catalog.document_type.manage',
            ],
            [
                'group' => 'Datos Personales',
                'title' => 'Nacionalidad',
                'description' => 'Nacionalidades disponibles para registro',
                'component' => 'configuracion.tablas.nationality',
                'icon' => 'flag',
                'order' => 4,
                'permission_key' => 'catalog.nationality.manage',
            ],
            [
                'group' => 'Datos Personales',
                'title' => 'Tipo de Sangre',
                'description' => 'Grupos sanguíneos para historia clínica',
                'component' => 'configuracion.tablas.blood-type',
                'icon' => 'beaker',
                'order' => 5,
                'permission_key' => 'catalog.blood_type.manage',
            ],

            // ── Profesional ───────────────────────────────────────────────
            [
                'group' => 'Profesional',
                'title' => 'Título Académico',
                'description' => 'Títulos y grados académicos del personal',
                'component' => 'configuracion.tablas.degree',
                'icon' => 'academic-cap',
                'order' => 1,
                'permission_key' => 'catalog.degree.manage',
            ],
            [
                'group' => 'Profesional',
                'title' => 'Ocupación',
                'description' => 'Ocupaciones y cargos del personal',
                'component' => 'configuracion.tablas.occupation',
                'icon' => 'briefcase',
                'order' => 2,
                'permission_key' => 'catalog.occupation.manage',
            ],

            // ── Financiero / Tributario ───────────────────────────────────
            [
                'group' => 'Financiero',
                'title' => 'Moneda',
                'description' => 'Monedas utilizadas en transacciones',
                'component' => 'configuracion.tablas.currency',
                'icon' => 'currency-dollar',
                'order' => 1,
                'permission_key' => 'catalog.currency.manage',
            ],
            [
                'group' => 'Financiero',
                'title' => 'Condición Tributaria',
                'description' => 'Condiciones impositivas de personas y empresas',
                'component' => 'configuracion.tablas.tax-condition',
                'icon' => 'receipt-percent',
                'order' => 2,
                'permission_key' => 'catalog.tax_condition.manage',
            ],
        ];

        foreach ($catalogs as $catalog) {
            CatalogForm::query()->firstOrCreate(
                ['component' => $catalog['component']],
                $catalog + ['is_active' => true],
            );
        }
    }
}
