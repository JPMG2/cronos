<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\DepartmentAction;

use App\Models\Department;

final class UpdateDepartmentAction
{
    public function handle(array $data): Department
    {

        $model = Department::query()->findOrFail($data['id']);
        $model->update([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return $model;
    }
}
