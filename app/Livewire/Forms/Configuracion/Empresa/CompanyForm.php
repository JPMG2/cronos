<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use App\Actions\Configuracion\CompanyAction\CreateCompanyAction;
use App\Events\NewBranch;
use App\Livewire\Forms\BaseForm;
use App\Models\Company;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class CompanyForm extends BaseForm
{
    public string $name = '';

    public string $fiscalNumber = '';

    #[Locked]
    public ?int $companyId = null;

    public ?int $taxConditionId = null;

    public ?int $currentStatusId = null;

    public ?int $regionId = null;

    public string $address = '';

    public string $postalCode = '';

    public string $phone = '';

    public string $email = '';

    public string $website = '';

    public mixed $logo = null;

    public array $dataCompany = [];

    public function validateCompany(): void
    {
        $this->dataCompany = $this->validateServiceData($this->companyId);
    }

    public function handleCompanyCreation(): array
    {
        return $this->tryAction(function () {

            $model = app(CreateCompanyAction::class)->handle($this->dataCompany);

            if ($model->wasRecentlyCreated) {
                NewBranch::dispatch($model);

                return ['Empresa creada correctamente', 'notifySuccess'];
            }

            if ($model->wasChanged()) {
                return ['Empresa actualizada correctamente.', 'notifySuccess'];
            }

            return ['No se realizaron cambios en la empresa.', 'notifyInfo'];

        }, 'Error al crear la empresa: ');

    }

    public function loadCompanyData(Company $company): void
    {
        $this->companyId = $company->id;
        $this->name = $company->name;
        $this->fiscalNumber = $company->fiscal_identifier;
        $this->taxConditionId = $company->tax_condition_id;
        $this->currentStatusId = $company->current_status_id;
        $this->regionId = $company->region_id;
        $this->address = $company->address;
        $this->postalCode = $company->postal_code;
        $this->phone = $company->phone;
        $this->email = $company->email;
        $this->website = $company->website ?? '';
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => mb_trim($this->name),
            'fiscalNumber' => mb_trim($this->fiscalNumber),
            'taxConditionId' => $this->taxConditionId,
            'regionId' => $this->regionId,
            'currentStatusId' => $this->currentStatusId,
            'address' => mb_trim($this->address),
            'postalCode' => mb_trim($this->postalCode),
            'phone' => mb_trim($this->phone),
            'email' => mb_strtolower(mb_trim($this->email)),
            'website' => mb_strtolower(mb_trim($this->website)),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'companies', 'name', $excludeId),
            'fiscalNumber' => AttributeValidator::uniqueIdNameLength('10', 'companies', 'fiscal_identifier', $excludeId),
            'taxConditionId' => AttributeValidator::requireAndExists('tax_conditions', 'id', 'tax_condition_id', true),
            'currentStatusId' => AttributeValidator::requireAndExists('current_statuses', 'id', 'current_status_id', true),
            'regionId' => AttributeValidator::requireAndExists('regions', 'id', 'region_id', true),
            'address' => AttributeValidator::stringValid(true, '6'),
            'postalCode' => AttributeValidator::stringValid(true, '3'),
            'phone' => AttributeValidator::digitValid('10', true),
            'email' => AttributeValidator::uniqueEmail('companies', 'email', $excludeId),
            'website' => AttributeValidator::stringValid(false, '5'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'fiscalNumber' => config('nicename.fiscal_number'),
            'taxConditionId' => config('nicename.tax_condition_id'),
            'currentStatusId' => config('nicename.current_status_id'),
            'regionId' => config('nicename.region_id'),
            'address' => config('nicename.address'),
            'postalCode' => config('nicename.postal_code'),
            'phone' => config('nicename.phone'),
            'email' => config('nicename.email'),
            'website' => config('nicename.website'),
        ];
    }
}
