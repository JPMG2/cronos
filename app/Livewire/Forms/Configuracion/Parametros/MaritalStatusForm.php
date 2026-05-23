<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\MaritalStatus;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class MaritalStatusForm extends BaseForm
{
    public string $name = '';

    #[Locked]
    public ?int $editingId = null;

    public function storeMaritalStatus(): array
    {
        $data = $this->validateServiceData();
        $maritalStatus = MaritalStatus::query()->create($data);

        return $this->notificationService()->sendNotificacion($maritalStatus, 'create');
    }

    public function updateMaritalStatus(): array
    {
        $this->validateServiceData($this->editingId);
        $maritalStatus = $this->findMaritalStaus($this->editingId);
        $maritalStatus->update($this->transformServiceData());

        return $this->notificationService()->sendNotificacion($maritalStatus, 'update');
    }

    public function findMaritalStaus(int $id): MaritalStatus
    {
        return MaritalStatus::query()->findOrFail($id);
    }

    public function updateMaritalStatusActive(int $id): array
    {
        $maritalStatus = $this->findMaritalStaus($id);
        $maritalStatus->update(['is_active' => ! $maritalStatus->is_active]);

        return $this->notificationService()->sendNotificacion($maritalStatus, 'update');
    }

    public function fillMaritalStatus(int $id): void
    {
        $maritalStatus = $this->findMaritalStaus($id);
        $this->name = $maritalStatus->name;
        $this->editingId = $maritalStatus->id;
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'marital_statuses', 'name', $excludeId),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
        ];
    }
}
