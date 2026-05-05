<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\DepartmentAction;

use App\Models\Department;

final class CreateDepartmentAction
{
    public function handle(array $data): Department
    {
        /** @var Department $department */
        $department = Department::query()->create([
            'current_status_id' => $data['current_status_id'],
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
        ]);

        return $department;
    }
}
