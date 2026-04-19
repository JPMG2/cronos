<?php

use App\Models\Country;
use App\Models\Province;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component {
    #[Title("Parametros Regionales")]
    public null|int $country_id = null;
    public null|int $province_id = null;
    public null|int $region_id = null;

    #[Computed]
    public function country(): Collection
    {
        return Country::query()
            ->orderBy("name", "asc")
            ->get();
    }

    #[Computed]
    public function province(): Collection
    {
        return $this->country_id > 0
            ? Country::find($this->country_id)
                ->provinces()
                ->orderBy("name", "asc")
                ->get()
            : collect();
    }

    #[Computed]
    public function region(): Collection
    {
        return $this->province_id > 0
            ? Province::find($this->province_id)
                ->regions()
                ->orderBy("name", "asc")
                ->get()
            : collect();
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <x-form-style.header-form
            title="Configuración de Ubicación"
            description="Define el país, provincia y región que el sistema utilizará por defecto."
            sign="Crear" />
        <div class="relative z-10 space-y-4 px-8 py-4">
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                <x-form-inputs.autocomplete
                    label="País"
                    name="pais_id"
                    placeholder="Seleccionar país…"
                    :options="$this->country->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                    wire:model.live="country_id"
                    required />

                <div wire:key="province-{{ $this->country_id }}">
                    <x-form-inputs.autocomplete
                        class="data-loading:opacity-50"
                        label="Provincia"
                        name="provincia_id"
                        placeholder="Seleccionar provincia…"
                        :options="$this->province->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                        wire:model.live="province_id"
                        :loading="true"
                        loading-target="country_id"
                        required />
                </div>

                <div wire:key="region-{{ $this->province_id }}">
                    <x-form-inputs.autocomplete
                        class="data-loading:opacity-50"
                        label="Ciudad"
                        name="ciudad_id"
                        placeholder="Seleccionar ciudad…"
                        :options="$this->region->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                        wire:model.live="region_id"
                        :loading="true"
                        loading-target="province_id"
                        required />
                </div>
            </div>
        </div>

        <x-form-style.footer-button save-label="Guardar Configuración" />
    </x-form-style.main-div>
</x-form-style.border-style>
