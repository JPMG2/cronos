<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\Country;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class NationalityForm extends BaseForm
{
    public string $countryName = '';

    public string $countryCode = '';

    public string $countryPhoneCode = '';

    #[Locked]
    public ?int $countryId = null;

    public function fillCountryData(int $id): void
    {
        $country = $this->findCountry($id);

        $this->countryId = $country->id;
        $this->countryName = $country->name;
        $this->countryCode = $country->code;
        $this->countryPhoneCode = $country->phone_code;
    }

    /**
     * @return array<int, mixed>
     */
    public function updateStatusCountry(int $id): array
    {
        $country = $this->findCountry($id);
        $country->update(['is_active' => ! $country->is_active]);

        return $this->notificationService()->sendNotificacion($country, 'update');
    }

    /**
     * @return array<int, mixed>
     */
    public function storeCountry(): array
    {
        $data = $this->validateServiceData();
        $country = Country::query()->create($data);

        return $this->notificationService()->sendNotificacion($country, 'create');
    }

    public function updateCountry(): array
    {
        $data = $this->validateServiceData($this->countryId);
        $country = $this->findCountry($this->countryId);
        $country->update($data);

        return $this->notificationService()->sendNotificacion($country, 'update');
    }

    /**
     * @return array<string, mixed>
     */
    protected function transformServiceData(): array
    {
        return [
            'name' => ucwords(mb_strtolower(mb_trim($this->countryName))),
            'code' => mb_strtoupper(mb_trim($this->countryCode)),
            'phone_code' => $this->countryPhoneCode,
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'countries', 'name', $excludeId),
            'code' => AttributeValidator::uniqueIdNameLength('3', 'countries', 'code', $excludeId),
            'phone_code' => AttributeValidator::digitValid('2', false),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'code' => config('nicename.code'),
            'phone_code' => config('nicename.phone_code'),
        ];
    }

    private function findCountry(int $id): Country
    {
        return Country::query()->findOrFail($id);
    }
}
