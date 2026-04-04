<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            ['name' => 'Argentino', 'code' => 'ARG', 'is_default' => true],
            ['name' => 'Boliviano', 'code' => 'BOL'],
            ['name' => 'Brasileño', 'code' => 'BRA'],
            ['name' => 'Chileno', 'code' => 'CHL'],
            ['name' => 'Colombiano', 'code' => 'COL'],
            ['name' => 'Costarricense', 'code' => 'CRI'],
            ['name' => 'Cubano', 'code' => 'CUB'],
            ['name' => 'Ecuatoriano', 'code' => 'ECU'],
            ['name' => 'Salvadoreño', 'code' => 'SLV'],
            ['name' => 'Guatemalteco', 'code' => 'GTM'],
            ['name' => 'Hondureño', 'code' => 'HND'],
            ['name' => 'Mexicano', 'code' => 'MEX'],
            ['name' => 'Nicaragüense', 'code' => 'NIC'],
            ['name' => 'Panameño', 'code' => 'PAN'],
            ['name' => 'Paraguayo', 'code' => 'PRY'],
            ['name' => 'Peruano', 'code' => 'PER'],
            ['name' => 'Puertorriqueño', 'code' => 'PRI'],
            ['name' => 'Dominicano', 'code' => 'DOM'],
            ['name' => 'Uruguayo', 'code' => 'URY'],
            ['name' => 'Venezolano', 'code' => 'VEN'],
        ];
    }
}
