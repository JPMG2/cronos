<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Occupation;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class OccupationForm extends BaseForm
{
    public string $name = '';

    #[Locked]
    public ?int $editingId = null;

    public function storeOccupation(): array
    {
        $data = $this->validateServiceData();
        $occupation = Occupation::query()->create($data);

        return $this->notificationService()->sendNotificacion($occupation, 'create');
    }

    public function updateOccupation(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $occupation = $this->findOccupation($this->editingId);
        $occupation->update($data);

        return $this->notificationService()->sendNotificacion($occupation, 'update');
    }

    public function updateOccupationActive(int $id): array
    {
        $occupation = $this->findOccupation($id);
        $wasActive = $occupation->is_active;
        $occupation->update(['is_active' => ! $wasActive]);

        return $this->notificationService()->sendNotificacion($occupation, 'update');
    }

    public function fillOccupation(int $id): void
    {
        $occupation = $this->findOccupation($id);
        $this->name = $occupation->name;
        $this->editingId = $occupation->id;
    }

    protected function findOccupation(int $id): Occupation
    {
        return Occupation::query()->findOrFail($id);
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
            'name' => AttributeValidator::uniqueIdNameLength('3', 'occupations', 'name', $excludeId),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
        ];
    }
}
