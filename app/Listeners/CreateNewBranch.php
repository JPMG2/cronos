<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Configuracion\BranchAction\CreateBranchAction;
use App\Events\NewBranch;

final class CreateNewBranch
{
    public function handle(NewBranch $event): void
    {
        $company = $event->company;

        (new CreateBranchAction)->handle([
            'company_id' => $company->id,
            'current_status_id' => $company->current_status_id,
            'region_id' => $company->region_id,
            'name' => $company->name,
            'phone' => $company->phone,
            'email' => $company->email,
            'address' => $company->address,
            'postal_code' => $company->postal_code,
            'is_default' => true,
            'logo' => $company->logo,
        ]);
    }
}
