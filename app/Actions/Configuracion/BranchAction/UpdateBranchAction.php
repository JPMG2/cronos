<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\BranchAction;

use App\Models\Branch;

final class UpdateBranchAction
{
    public function handle(array $data): Branch
    {
        $model = Branch::query()->findOrFail($data['id']);
        $model->update([
            'company_id' => $data['company_id'],
            'current_status_id' => $data['current_status_id'],
            'region_id' => $data['region_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'is_default' => $data['is_default'] ?? false,
            'website' => $data['website'] ?? null,
            'logo' => $data['logo'] ?? null,
        ]);

        return $model;
    }
}
