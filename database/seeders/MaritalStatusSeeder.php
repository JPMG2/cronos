<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

final class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maritalstatus = [
            ['name' => 'Soltero'],
            ['name' => 'Casado'],
            ['name' => 'Divorciado'],
            ['name' => 'Viudo'],
            ['name' => 'Unión Libre'],
        ];
        foreach ($maritalstatus as $marital) {
            MaritalStatus::query()->create($marital);
        }
    }
}
