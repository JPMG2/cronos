<?php

use App\Models\Country;
use App\Models\Currency;
use App\Models\Province;
use App\Models\WorldSettings;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title("Parametros Regionales")] class extends Component {
    use HasNotifications;

    #[Validate("required|integer|exists:countries,id")]
    public null|int $country_id = null;
    #[Validate("required|integer|exists:provinces,id")]
    public null|int $province_id = null;
    #[Validate("required|integer|exists:regions,id")]
    public null|int $region_id = null;
    #[Validate("required|integer|exists:currencies,id")]
    public null|int $currency_id = null;
    public string $currency_symbol = "";
    public string $currency_code = "";
    public int $decimal_places = 2;

    public function updatedCurrencyId(?int $value): void
    {
        if (! $value) {
            $this->currency_symbol = "";
            $this->currency_code = "";
            $this->decimal_places = 2;
            return;
        }

        $currency = Currency::query()->find($value);

        if ($currency) {
            $this->currency_symbol = $currency->symbol;
            $this->currency_code = $currency->code;
            $this->decimal_places = $currency->decimal_places;
        }
    }

    #[Computed]
    public function countries(): Collection
    {
        return Country::query()
            ->orderBy("name", "asc")
            ->get();
    }

    #[Computed]
    public function provinces(): Collection
    {
        return $this->country_id > 0
            ? Country::find($this->country_id)
                    ?->provinces()
                    ->orderBy("name", "asc")
                    ->get() ?? collect()
            : collect();
    }

    #[Computed]
    public function regions(): Collection
    {
        return $this->province_id > 0
            ? Province::find($this->province_id)
                    ?->regions()
                    ->orderBy("name", "asc")
                    ->get() ?? collect()
            : collect();
    }

    #[Computed]
    public function currencies(): Collection
    {
        return Currency::query()
            ->orderBy("name", "asc")
            ->get();
    }

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function cancel(): void
    {
        $this->resetValidation();
        $this->loadSettings();
    }

    public function saveRegion(): void
    {
        $this->validate();
        try {
            $this->createUpdate();
            $this->loadSettings();
            $this->notifySuccess("¡Configuración regional guardada exitosamente!");
        } catch (\Exception $e) {
            $this->notifyError("Error al guardar la configuración regional: " . $e->getMessage());
        }
    }

    public function createUpdate(): WorldSettings
    {
        return DB::transaction(function () {
            Currency::query()
                ->where('id', $this->currency_id)
                ->update(['decimal_places' => $this->decimal_places]);

            return WorldSettings::query()->updateOrCreate(
                ['id' => 1],
                [
                    'country_id'  => $this->country_id,
                    'province_id' => $this->province_id,
                    'region_id'   => $this->region_id,
                    'currency_id' => $this->currency_id,
                    'updated_by'  => auth()->id(),
                ],
            );
        });
    }

    private function loadSettings(): void
    {
        $settings = WorldSettings::query()->find(1);

        if (! $settings) {
            return;
        }

        $this->country_id  = $settings->country_id;
        $this->province_id = $settings->province_id;
        $this->region_id   = $settings->region_id;
        $this->currency_id = $settings->currency_id;

        $currency = Currency::query()->find($settings->currency_id);

        if ($currency) {
            $this->currency_symbol = $currency->symbol;
            $this->currency_code   = $currency->code;
            $this->decimal_places  = $currency->decimal_places;
        }
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <x-form-style.header-form
            title="Configuración Regional"
            description="Define las preferencias de localización y ajustes monetarios para el sistema médico."
            sign="Parametrización" />

        <div
            x-data="{
                errors: {},
                submit() {
                    this.errors = validate(
                        {
                            country_id: $wire.country_id,
                            province_id: $wire.province_id,
                            region_id: $wire.region_id,
                            currency_id: $wire.currency_id,
                        },
                        {
                            country_id: ['required'],
                            province_id: ['required'],
                            region_id: ['required'],
                            currency_id: ['required'],
                        },
                    )
                    if (Object.keys(this.errors).length === 0) $wire.saveRegion()
                },
            }">
            <div class="relative z-10 space-y-5 px-6 py-4">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-x-8">
                    {{-- ── Sección Localización ── --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                                <x-menu.heroicon name="map-pin" class="h-4 w-4" />
                            </div>
                            <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                                Localización
                            </h3>
                        </div>

                        <div wire:key="country-{{ $country_id }}">
                            <x-form-inputs.autocomplete
                                label="País"
                                name="pais_id"
                                placeholder="Seleccionar país…"
                                :options="$this->countries->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                :value="$country_id"
                                wire:model.live="country_id"
                                required />
                            <p
                                x-show="errors.country_id"
                                x-text="errors.country_id"
                                x-transition
                                class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
                        </div>

                        <div wire:key="province-{{ $country_id }}-{{ $province_id }}">
                            <x-form-inputs.autocomplete
                                class="data-loading:opacity-50"
                                label="Provincia o Estado"
                                name="provincia_id"
                                placeholder="Seleccionar provincia…"
                                :options="$this->provinces->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                :value="$province_id"
                                wire:model.live="province_id"
                                :loading="true"
                                loading-target="country_id"
                                required />
                            <p
                                x-show="errors.province_id"
                                x-text="errors.province_id"
                                x-transition
                                class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
                        </div>

                        <div wire:key="region-{{ $province_id }}-{{ $region_id }}">
                            <x-form-inputs.autocomplete
                                class="data-loading:opacity-50"
                                label="Ciudad"
                                name="ciudad_id"
                                placeholder="Seleccionar ciudad…"
                                :options="$this->regions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                :value="$region_id"
                                wire:model.live="region_id"
                                :loading="true"
                                loading-target="province_id"
                                required />
                            <p
                                x-show="errors.region_id"
                                x-text="errors.region_id"
                                x-transition
                                class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
                        </div>
                    </div>

                    {{-- ── Sección Moneda y Finanzas ── --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                                <x-menu.heroicon name="currency-dollar" class="h-4 w-4" />
                            </div>
                            <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                                Moneda y Finanzas
                            </h3>
                        </div>

                        <div wire:key="currency-{{ $currency_id }}">
                            <x-form-inputs.autocomplete
                                label="Moneda"
                                name="currency"
                                placeholder="Seleccionar tipo de moneda…"
                                :options="$this->currencies->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                :value="$currency_id"
                                wire:model.live="currency_id"
                                required />
                            <p
                                x-show="errors.currency_id"
                                x-text="errors.currency_id"
                                x-transition
                                class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-form-inputs.text_input
                                label="Código"
                                name="currency_code"
                                icon="hashtag"
                                readonly
                                wire:model.live="currency_code" />

                            <x-form-inputs.text_input
                                label="Símbolo"
                                name="currency_symbol"
                                icon="currency-dollar"
                                readonly
                                wire:model.live="currency_symbol" />
                        </div>

                        <div class="grid grid-cols-2 items-stretch gap-4">
                            <x-form-inputs.select
                                label="Decimales"
                                name="decimal_places"
                                icon="adjustments-horizontal"
                                wire:model.live="decimal_places">
                                <option value="0">0 — Sin decimales</option>
                                <option value="1">1 decimal</option>
                                <option value="2">2 decimales</option>
                                <option value="3">3 decimales</option>
                            </x-form-inputs.select>

                            {{-- Vista previa del formato --}}
                            <div class="flex flex-col">
                                <p class="mb-1.5 text-sm font-semibold text-slate-700 dark:text-gray-300">
                                    Vista previa
                                </p>
                                <div
                                    class="flex flex-1 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50/60 px-3 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/10">
                                    <x-menu.heroicon
                                        name="information-circle"
                                        class="h-4 w-4 shrink-0 text-indigo-400 dark:text-indigo-500" />
                                    <p
                                        class="font-headline text-base font-extrabold tabular-nums text-slate-800 dark:text-gray-100">
                                        {{ $currency_symbol }} 1.250,{{ str_repeat("0", $decimal_places) ?: "—" }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-form-style.footer-button>
                <x-btn.cancel label="Descartar" wire:click="cancel" />
                <x-btn.save label="Guardar Configuración" @click="submit()" wire-target="saveRegion" />
            </x-form-style.footer-button>
        </div>
        {{-- cierre x-data wrapper --}}
    </x-form-style.main-div>
</x-form-style.border-style>
