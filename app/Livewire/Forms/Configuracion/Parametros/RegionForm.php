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

    public function storeRegion(): array
    {
        $data = $this->validateServiceData();
        $model = Region::query()->create($data);

        return $this->notificationService()->sendNotificacion($model, 'create');
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
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
}
