<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaxCondition;
use Illuminate\Database\Seeder;

final class TaxConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxConditions = [
            [
                'name' => 'Responsable Inscripto',
                'code' => 'RI',
                'discriminate_tax' => true,
            ],
            [
                'name' => 'IVA Exento',
                'code' => 'EX',
                'discriminate_tax' => false,
            ],
            [
                'name' => 'Monotributista',
                'code' => 'MT',
                'discriminate_tax' => false,
            ],
            [
                'name' => 'IVA No Alcanzado',
                'code' => 'NA',
                'discriminate_tax' => false,
            ],
            [
                'name' => 'Consumidor Final',
                'code' => 'CF',
                'discriminate_tax' => false,
            ],
        ];
        foreach ($taxConditions as $taxCondition) {
            TaxCondition::query()->create($taxCondition);
        }
    }
}
