<?php

declare(strict_types=1);

namespace App\Traits\Utilities;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

trait WorldConfiguration
{
    protected static ?Collection $staticCountries = null;

    #[Computed(cache: true)]
    public function countries(): Collection
    {
        return self::$staticCountries ??= Country::query()->orderBy('name')->get();
    }

    #[Computed]
    public function provinces(): Collection
    {
        $countryId = $this->getCountryIdForFilter();

        return Province::query()
            ->with('country:id,code')
            ->when($countryId, fn ($q) => $q->where('country_id', $countryId))
            ->orderBy('name')
            ->get();
    }

    protected function getCountryIdForFilter(): mixed
    {
        return null;
    }
}
