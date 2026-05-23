<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Gender;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class GenderForm extends BaseForm
{
    public string $name = '';

    #[Locked]
    public ?int $editingId = null;

    public function storeGender(): array
    {
        $data = $this->validateServiceData();
        $gender = Gender::query()->create($data);

        return $this->notificationService()->sendNotificacion($gender, 'create');
    }

    public function updateGender(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $gender = $this->findGender($this->editingId);
        $gender->update($data);

        return $this->notificationService()->sendNotificacion($gender, 'update');
    }

    public function updateGenderActive(int $id): array
    {
        $gender = $this->findGender($id);
        $wasActive = $gender->is_active;
        $gender->update(['is_active' => ! $wasActive]);

        return $this->notificationService()->sendNotificacion($gender, 'update');
    }

    public function fillDataGender(int $id): void
    {
        $gender = $this->findGender($id);
        $this->name = $gender->name;
        $this->editingId = $gender->id;
    }

    protected function findGender(int $id): Gender
    {
        return Gender::query()->findOrFail($id);
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucfirst(mb_strtolower($this->name)),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'genders', 'name', $excludeId),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
        ];
    }
}
