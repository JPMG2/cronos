<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Province;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class ProvincesForm extends BaseForm
{
    #[Locked]
    public ?int $country_id = null;

    #[Locked]
    public ?int $provinceId = null;

    public string $name = '';

    public function fillProvincesData(int $id): void
    {
        $provinces = $this->findProvinces($id);

        $this->country_id = $provinces->country_id;
        $this->provinceId = $provinces->id;
        $this->name = $provinces->name;
    }

    public function updateStatusProvinces(int $id): array
    {
        $province = $this->findProvinces($id);

        $wasActive = $province->is_active;
        $province->update(['is_active' => ! $wasActive]);

        return $this->notificationService()->sendNotificacion($province, 'update');
    }

    public function storeProvinces(): array
    {
        $data = $this->validateServiceData();
        $model = Province::query()->create($data);

        return $this->notificationService()->sendNotificacion($model, 'create');
    }

    public function updateProvinces(): array
    {
        $data = $this->validateServiceData($this->provinceId);
        $model = $this->findProvinces($this->provinceId);

        $model->update($data);

        return $this->notificationService()->sendNotificacion($model, 'update');
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
            'country_id' => $this->country_id,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::requiredExistModelRelation('provinces', 'name', 'country_id', $this->country_id, $excludeId),
            'country_id' => AttributeValidator::requireAndExists('countries', 'id', 'country_id', true),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'country_id' => config('nicename.country_id'),
        ];
    }

    private function findProvinces(int $id): Province
    {
        return Province::query()->findOrFail($id);
    }
}
