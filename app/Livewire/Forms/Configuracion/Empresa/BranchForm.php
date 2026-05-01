<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use App\Actions\Configuracion\BranchAction\CreateBranchAction;
use App\Actions\Configuracion\BranchAction\UpdateBranchAction;
use App\Livewire\Forms\BaseForm;
use App\Models\Branch;
use App\Rules\AttributeValidator;
use Exception;
use Livewire\Attributes\Locked;

final class BranchForm extends BaseForm
{
    #[Locked]
    public ?int $company_id = null;

    #[Locked]
    public ?int $branch_id = null;

    public string $companyName = '';

    public string $name = '';

    public string $code = '';

    public ?int $province_id = null;

    public ?int $region_id = null;

    public string $postal_code = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $website = '';

    public ?int $current_status_id = null;

    public bool $is_default = false;

    public mixed $logo = null;

    public array $dataBranch = [];

    public function fillFromBranch(int $branchId): void
    {
        $branch = Branch::query()->with('region')->find($branchId);

        if (! $branch) {
            return;
        }

        $this->branch_id = $branch->id;
        $this->company_id = $branch->company_id;
        $this->current_status_id = $branch->current_status_id;
        $this->province_id = $branch->region?->province_id;
        $this->region_id = $branch->region_id;
        $this->name = $branch->name;
        $this->code = $branch->code;
        $this->phone = $branch->phone ?? '';
        $this->email = $branch->email ?? '';
        $this->website = $branch->website ?? '';
        $this->address = $branch->address ?? '';
        $this->postal_code = $branch->postal_code ?? '';
        $this->is_default = (bool) $branch->is_default;
    }

    public function validateBranchData(): void
    {
        $this->dataBranch = $this->validateServiceData($this->branch_id);
    }

    public function createBranch(): array
    {
        try {
            $model = app(CreateBranchAction::class)->handle($this->dataBranch);
            if ($model->wasRecentlyCreated) {
                return ['Sucursal creada correctamente', 'notifySuccess'];
            }
        } catch (Exception $e) {
            return ['Error al guardar la sucursal: ' . $e->getMessage(), 'notifyError'];
        }

        return ['Sucursal no creada', 'notifyError'];
    }

    public function updateBranch(): array
    {
        $data = array_merge(['id' => $this->branch_id], $this->dataBranch);

        try {
            $model = app(UpdateBranchAction::class)->handle($data);
            if ($model->wasChanged()) {
                return ['Sucursal actualizada correctamente', 'notifySuccess'];
            }
        } catch (Exception $e) {
            return ['Error al actualizar la sucursal: ' . $e->getMessage(), 'notifyError'];
        }

        return ['No se realizaron cambios en la sucursal.', 'notifyInfo'];
    }

    protected function transformServiceData(): array
    {
        return [
            'company_id' => $this->company_id,
            'name' => mb_trim($this->name),
            'code' => mb_trim($this->code),
            'province_id' => $this->province_id,
            'region_id' => $this->region_id,
            'phone' => mb_trim($this->phone),
            'email' => mb_strtolower(mb_trim($this->email)),
            'website' => mb_strtolower(mb_trim($this->website)),
            'address' => mb_trim($this->address),
            'postal_code' => mb_trim($this->postal_code),
            'is_default' => $this->is_default,
            'logo' => $this->logo,
            'current_status_id' => $this->current_status_id,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'branches', 'name', $excludeId),
            'code' => AttributeValidator::uniqueIdNameLength('3', 'branches', 'code', $excludeId),
            'province_id' => AttributeValidator::requireAndExists('provinces', 'id', 'province_id', true),
            'region_id' => AttributeValidator::requireAndExists('regions', 'id', 'region_id', true),
            'phone' => AttributeValidator::digitValid('10', true),
            'email' => AttributeValidator::uniqueEmail('branches', 'email', $excludeId),
            'website' => AttributeValidator::stringValid(false, '5'),
            'address' => AttributeValidator::stringValid(true, '6'),
            'postal_code' => AttributeValidator::stringValid(true, '3'),
            'current_status_id' => AttributeValidator::requireAndExists('current_statuses', 'id', 'current_status_id', true),
            'company_id' => AttributeValidator::requireAndExists('companies', 'id', 'company_id', true),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'code' => config('nicename.code'),
            'province_id' => config('nicename.province_id'),
            'region_id' => config('nicename.region_id'),
            'phone' => config('nicename.phone'),
            'email' => config('nicename.email'),
            'website' => config('nicename.website'),
            'address' => config('nicename.address'),
            'postal_code' => config('nicename.postal_code'),
            'current_status_id' => config('nicename.current_status_id'),
            'company_id' => config('nicename.company'),
        ];
    }
}
