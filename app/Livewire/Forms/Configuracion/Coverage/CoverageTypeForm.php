<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Coverage;

use App\Livewire\Forms\BaseForm;
use App\Models\CoverageType;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class CoverageTypeForm extends BaseForm
{
    public string $code = '';

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public bool $requiresInsuranceData = true;

    #[Locked]
    public ?int $editingId = null;

    public function storeCoverageType(): array
    {
        $data = $this->validateServiceData();
        $coverageType = CoverageType::query()->create($data);

        return $this->notificationService()->sendNotificacion($coverageType, 'create');
    }

    public function updateCoverageType(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $coverageType = $this->findCoverageType($this->editingId);
        $coverageType->update($data);

        return $this->notificationService()->sendNotificacion($coverageType, 'update');
    }

    public function findCoverageType(int $id): CoverageType
    {
        return CoverageType::query()->findOrFail($id);
    }

    public function fillCoverageType(int $id): void
    {
        $coverageType = $this->findCoverageType($id);
        $this->code = $coverageType->code;
        $this->name = $coverageType->name;
        $this->description = $coverageType->description ?? '';
        $this->isActive = $coverageType->is_active;
        $this->requiresInsuranceData = $coverageType->requires_insurance_data;
        $this->editingId = $coverageType->id;
    }

    protected function transformServiceData(): array
    {
        return [
            'code' => mb_strtoupper(mb_strtolower(mb_trim($this->code))),
            'name' => mb_strtolower(mb_trim($this->name)),
            'description' => mb_trim($this->description) !== '' ? mb_trim($this->description) : null,
            'is_active' => $this->isActive,
            'requires_insurance_data' => $this->requiresInsuranceData,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'code' => AttributeValidator::uniqueIdNameLength('2', 'coverage_types', 'code', $excludeId),
            'name' => AttributeValidator::uniqueIdNameLength('3', 'coverage_types', 'name', $excludeId),
            'description' => AttributeValidator::stringValid(false, '3'),
            'is_active' => AttributeValidator::valueBoolean(true),
            'requires_insurance_data' => AttributeValidator::valueBoolean(true),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'code' => config('nicename.code'),
            'name' => config('nicename.name'),
            'description' => config('nicename.description'),
            'is_active' => config('nicename.is_active'),
            'requires_insurance_data' => config('nicename.requires_insurance_data'),
        ];
    }
}
