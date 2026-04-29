<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\CompanyAction;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

final class SaveCompanyAction
{
    public function handle(array $data): Company
    {
        /** @var Company $company */
        $company = DB::transaction(fn () => Company::query()->updateOrCreate(
            ['fiscal_identifier' => $data['fiscalNumber']],
            [
                'name' => $data['name'],
                'current_status_id' => $data['currentStatusId'],
                'tax_condition_id' => $data['taxConditionId'],
                'region_id' => $data['regionId'],
                'address' => $data['address'],
                'postal_code' => $data['postalCode'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'website' => $data['website'] ?: null,
            ],
        ));

        return $company;
    }
}
