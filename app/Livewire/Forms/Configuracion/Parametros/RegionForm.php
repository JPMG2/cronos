<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Region;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class RegionForm extends BaseForm
{
    #[Locked]
    public ?int $country_id = null;

    #[Locked]
    public ?int $province_id = null;

    #[Locked]
    public ?int $regionId = null;

    public string $name = '';

    public function fillRegionData(int $id): void
    {
        $region = $this->findRegion($id);
        $this->country_id = $region->province->country->id;
        $this->regionId = $region->id;
        $this->province_id = $region->province_id;
        $this->name = $region->name;
    }

    public function storeRegion(): array
    {
        $data = $this->validateServiceData();
        $model = Region::query()->create($data);

        return $this->notificationService()->sendNotificacion($model, 'create');
    }

    public function updateRegion(): array
    {
        $data = $this->validateServiceData($this->regionId);
        $model = $this->findRegion($this->regionId);
        $model->update($data);

        return $this->notificationService()->sendNotificacion($model, 'update');
    }

    public function updateStatusRegion(int $id): array
    {
        $model = $this->findRegion($id);
        $model->update(['is_active' => ! $model->is_active]);

        return $this->notificationService()->sendNotificacion($model, 'update');
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucwords(mb_strtolower(mb_trim($this->name))),
            'province_id' => $this->province_id,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::requiredExistModelRelation('regions', 'name', 'province_id', $this->province_id, $excludeId),
            'province_id' => AttributeValidator::requireAndExists('provinces', 'id', 'province_id', true),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'province_id' => config('nicename.province_id'),
        ];
    }

    private function findRegion(int $id): Region
    {
        return Region::query()->with('province.country:id')->findOrFail($id);
    }
}
