<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\TaxCondition;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class TaxConditionForm extends BaseForm
{
    #[Locked]
    public ?int $taxId = null;

    public string $name = '';

    public string $code = '';

    public bool $discriminate_tax = false;

    public bool $is_active = true;

    public function storeTaxCondition(): array
    {
        $data = $this->validateServiceData();
        $tax = TaxCondition::query()->create($data);

        return $this->notificationService()->sendNotificacion($tax, 'create');
    }

    public function updateTaxCondition(): array
    {
        $data = $this->validateServiceData($this->taxId);
        $tax = $this->findTax($this->taxId);
        $tax->update($data);

        return $this->notificationService()->sendNotificacion($tax, 'update');
    }

    public function findTax(int $id): TaxCondition
    {
        return TaxCondition::query()->findOrFail($id);
    }

    public function fillStatusData(int $id): void
    {
        $tax = $this->findTax($id);
        $this->taxId = $tax->id;
        $this->name = $tax->name;
        $this->code = $tax->code;
        $this->discriminate_tax = $tax->discriminate_tax;
        $this->is_active = $tax->is_active;
    }

    public function updateDiscriminateTax(int $id): array
    {
        $tax = $this->findTax($id);
        $tax->update(['discriminate_tax' => ! $tax->discriminate_tax]);

        return $this->notificationService()->sendNotificacion($tax, 'update');
    }

    public function updateStatusTax(int $id): array
    {
        $status = $this->findTax($id);
        $status->update(['is_active' => ! $status->is_active]);

        return $this->notificationService()->sendNotificacion($status, 'update');
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => mb_trim($this->name),
            'code' => mb_strtoupper(mb_strtolower(mb_trim($this->code))),
            'discriminate_tax' => $this->discriminate_tax,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'tax_conditions', 'name', $excludeId),
            'code' => AttributeValidator::uniqueIdNameLength('2', 'tax_conditions', 'code', $excludeId),
            'discriminate_tax' => AttributeValidator::valueBoolean(false),
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
