<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CurrentStatus;
use Illuminate\Database\Seeder;

final class CurrentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $current_status = [
            ['name' => 'Activo'],
            ['name' => 'Bloqueado'],
            ['name' => 'Cancelado'],
            ['name' => 'Archivado'],
            ['name' => 'Eliminado'],
            ['name' => 'Inactivo'],
            ['name' => 'Pendiente'],
            ['name' => 'Rechazado'],
            ['name' => 'Suspendido'],
            ['name' => 'Pausado'],
            ['name' => 'En proceso'],
            ['name' => 'Finalizado'],
        ];

        foreach ($current_status as $status) {
            CurrentStatus::create($status);
        }
    }
}
