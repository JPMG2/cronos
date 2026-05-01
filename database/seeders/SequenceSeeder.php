<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sequence;
use Illuminate\Database\Seeder;

final class SequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sequences = [
            ['entity' => 'Factura',  'prefix' => 'FAC', 'current_value' => 0, 'increment' => 1],
            ['entity' => 'Compañia',  'prefix' => 'CO', 'current_value' => 0, 'increment' => 1],
            ['entity' => 'Sucursal',  'prefix' => 'SUC', 'current_value' => 0, 'increment' => 1],
            ['entity' => 'Departamento', 'prefix' => 'DPTO', 'current_value' => 0, 'increment' => 1],
        ];

        foreach ($sequences as $sequence) {
            Sequence::create($sequence);
        }
    }
}
