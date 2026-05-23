<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Degree;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class DegreeForm extends BaseForm
{
    public string $name = '';

    public string $code = '';

    #[Locked]
    public ?int $editingId = null;

    public function storeDegree(): array
    {
        $data = $this->validateServiceData();
        $degree = Degree::query()->create($data);

        return $this->notificationService()->sendNotificacion($degree, 'create');
    }

    public function updateDegree(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $degree = $this->findDegree($this->editingId);
        $degree->update($data);

        return $this->notificationService()->sendNotificacion($degree, 'update');
    }

    public function findDegree(int $id): Degree
    {
        return Degree::query()->findOrFail($id);
    }

    public function updateDegreeActive(int $id): array
    {
        $degree = $this->findDegree($id);
        $degree->update(['is_active' => ! $degree->is_active]);

        return $this->notificationService()->sendNotificacion($degree, 'update');
    }

    public function fillDegree(int $id): void
    {
        $degree = $this->findDegree($id);
        $this->name = $degree->name;
        $this->code = $degree->code ?? '';
        $this->editingId = $degree->id;
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
            'code' => mb_strtoupper(mb_strtolower(mb_trim($this->code))),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'degrees', 'name', $excludeId),
            'code' => AttributeValidator::stringValid(false, '2'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'code' => config('nicename.code'),
        ];
    }
}
