<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\BranchAction;

use App\Models\Branch;

final class CreateBranchAction
{
    public function handle(array $data): Branch
    {
        /** @var Branch $branch */
        $branch = Branch::query()->create([
            'company_id' => $data['company_id'],
            'current_status_id' => $data['current_status_id'],
            'region_id' => $data['region_id'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'is_default' => $data['is_default'] ?? false,
            'logo' => $data['logo'] ?? null,
        ]);

        return $branch;
    }
}
