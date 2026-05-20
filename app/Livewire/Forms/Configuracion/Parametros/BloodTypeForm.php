<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\BloodType;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class BloodTypeForm extends BaseForm
{
    public string $code = '';

    public string $name = '';

    public string $aboGroup = '';

    public string $rhFactor = '';

    public string $canDonateTo = '';

    public string $canReceiveFrom = '';

    public bool $isUniversalDonor = false;

    public bool $isUniversalRecipient = false;

    #[Locked]
    public ?int $editingId = null;

    public function storeBloodType(): array
    {
        $data = $this->validateServiceData();
        $bloodType = BloodType::query()->create($data);

        return $this->notificationService()->sendNotificacion($bloodType, 'create');
    }

    public function updateBloodType(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $bloodType = $this->findBloodType($this->editingId);
        $bloodType->update($data);

        return $this->notificationService()->sendNotificacion($bloodType, 'update');
    }

    public function findBloodType(int $id): BloodType
    {
        return BloodType::query()->findOrFail($id);
    }

    public function fillBloodType(int $id): void
    {
        $bloodType = $this->findBloodType($id);
        $this->code = $bloodType->code;
        $this->name = $bloodType->name;
        $this->aboGroup = $bloodType->abo_group;
        $this->rhFactor = $bloodType->rh_factor ?? '';
        $this->canDonateTo = implode(', ', $bloodType->can_donate_to ?? []);
        $this->canReceiveFrom = implode(', ', $bloodType->can_receive_from ?? []);
        $this->isUniversalDonor = $bloodType->is_universal_donor;
        $this->isUniversalRecipient = $bloodType->is_universal_recipient;
        $this->editingId = $bloodType->id;
    }

    protected function transformServiceData(): array
    {
        return [
            'code' => mb_strtoupper(mb_trim($this->code)),
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
            'abo_group' => mb_strtoupper(mb_trim($this->aboGroup)),
            'rh_factor' => $this->rhFactor,
            'can_donate_to' => array_values(array_filter(array_map(
                fn (string $c): string => mb_strtoupper(mb_trim($c)),
                explode(',', $this->canDonateTo),
            ))),
            'can_receive_from' => array_values(array_filter(array_map(
                fn (string $c): string => mb_strtoupper(mb_trim($c)),
                explode(',', $this->canReceiveFrom),
            ))),
            'is_universal_donor' => $this->isUniversalDonor,
            'is_universal_recipient' => $this->isUniversalRecipient,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'code' => AttributeValidator::uniqueIdNameLength('1', 'blood_types', 'code', $excludeId),
            'name' => AttributeValidator::stringValid(true, '3'),
            'abo_group' => AttributeValidator::stringValid(true, '1'),
            'rh_factor' => AttributeValidator::stringValid(true, '1'),
            'can_donate_to' => ['required', 'array', 'min:1'],
            'can_donate_to.*' => AttributeValidator::stringValid(true, '1'),
            'can_receive_from' => ['required', 'array', 'min:1'],
            'can_receive_from.*' => AttributeValidator::stringValid(true, '1'),
            'is_universal_donor' => AttributeValidator::valueBoolean(true),
            'is_universal_recipient' => AttributeValidator::valueBoolean(true),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'code' => config('nicename.code'),
            'name' => config('nicename.name'),
            'abo_group' => config('nicename.abo_group'),
            'rh_factor' => config('nicename.rh_factor'),
            'can_donate_to' => config('nicename.can_donate_to'),
            'can_receive_from' => config('nicename.can_receive_from'),
            'is_universal_donor' => config('nicename.is_universal_donor'),
            'is_universal_recipient' => config('nicename.is_universal_recipient'),
        ];
    }
}
