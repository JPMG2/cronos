<?php

use App\Models\Province;
use App\Models\TaxCondition;
use App\Models\WorldSettings;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

new #[Title("Empresa")] class extends Component {
    use WithFileUploads;

    public string $name = "";
    public string $fiscalNumber = "";
    public null|int $taxConditionId = null;
    public null|int $provinceId = null;
    public string $address = "";
    public string $zipCode = "";
    public string $phone = "";
    public string $email = "";
    public string $web = "";

    #[Validate(["nullable", "image", "max:2048", "mimes:jpg,jpeg,png,svg,webp"])]
    public null|string $logo = null;

    #[Computed]
    public function taxCondition(): Collection
    {
        return TaxCondition::query()
            ->orderBy("name", "asc")
            ->get();
    }

    #[Computed]
    public function provinces(): Collection
    {
        $idProvince = WorldSettings::defaultProvince();
        $idCountry = WorldSettings::defaultCountry();

        return Province::query()
            ->where("country_id", $idCountry)
            ->defaultFirst($idProvince)
            ->get();
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <x-form-style.header-form
            title="Crear Nueva Empresa"
            description="Configure los datos de la organización médica para integrar sus servicios clínicos."
            sign="Nueva Empresa." />

        <div class="relative z-10 px-8 py-4">
            {{-- ── Layout: campos (izq) + logo uploader (der) ─────────────── --}}
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-8">
                {{-- Columna de campos ─────────────────────────────────────── --}}
                <div class="min-w-0 flex-1 space-y-5">
                    {{-- Fila 1: Identidad Fiscal --}}
                    <div class="grid grid-cols-12 gap-x-4 gap-y-4">
                        <div class="col-span-12 sm:col-span-5">
                            <x-form-inputs.text_input
                                label="Nombre de la Empresa"
                                name="nombre"
                                icon="building-office"
                                placeholder="Ej: Clínica Santa María"
                                maxlength="200"
                                required />
                        </div>
                        <div class="col-span-12 sm:col-span-3">
                            <x-form-inputs.text_input
                                label="CUIT"
                                name="cuit"
                                icon="identification"
                                placeholder="900.123.456-7"
                                maxlength="20"
                                required />
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <x-form-inputs.select
                                label="Condición IVA"
                                name="tipo_entidad"
                                icon="building-library"
                                required>
                                <option value="" readonly>Seleccionar tipo…</option>
                                @foreach ($this->taxCondition as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                @endforeach
                            </x-form-inputs.select>
                        </div>
                    </div>

                    {{-- Fila 2: Ubicación --}}
                    <div class="grid grid-cols-12 gap-x-4 gap-y-4">
                        <div class="col-span-12 sm:col-span-4">
                            <x-form-inputs.autocomplete
                                label="Provincia"
                                name="provinca_id"
                                placeholder="Seleccionar provincia…"
                                :options="$this->provinces->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                required />
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <x-form-inputs.text_input
                                label="Dirección"
                                name="direccion"
                                icon="map"
                                placeholder="Calle, Altura"
                                maxlength="200"
                                required />
                        </div>
                        <div class="col-span-12 sm:col-span-2">
                            <x-form-inputs.text_input
                                label="Cód. Postal"
                                name="codpostal"
                                icon="inbox-arrow-down"
                                placeholder="999"
                                maxlength="10"
                                required />
                        </div>
                    </div>
                </div>

                {{-- Columna del logo ──────────────────────────────────────── --}}
                <div x-data="{ dragging: false }" class="flex flex-col items-center gap-2 lg:pt-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500">
                        Logo
                    </span>

                    {{-- Cuadrado uploader --}}
                    <div
                        x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false"
                        x-on:drop.prevent="
                            dragging = false
                            const f = $event.dataTransfer.files
                            if (f.length) {
                                $refs.logoInput.files = f
                                $refs.logoInput.dispatchEvent(new Event('change'))
                            }
                        "
                        :class="dragging
                            ? 'border-indigo-400 bg-indigo-50 dark:border-sky-500 dark:bg-sky-500/10'
                            : @js($logo)
                                ? 'border-indigo-200 dark:border-indigo-700/60'
                                : 'border-indigo-200/80 dark:border-gray-700'"
                        class="group relative h-[7.5rem] w-[7.5rem] cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed bg-white transition-all duration-200 dark:bg-gray-800/60"
                        @click="$refs.logoInput.click()"
                        role="button"
                        aria-label="Subir logo de empresa"
                        tabindex="0"
                        @keydown.enter.prevent="$refs.logoInput.click()">
                        {{-- Preview cuando hay imagen --}}

                        @if ($logo)
                            <img
                                src="{{ $logo->temporaryUrl() }}"
                                class="h-full w-full object-contain p-1.5"
                                alt="Logo preview" />
                        @else
                            {{-- Placeholder vacío --}}
                            <div class="flex h-full flex-col items-center justify-center gap-1.5">
                                <x-menu.heroicon
                                    name="building-office"
                                    class="h-8 w-8 text-indigo-200 dark:text-indigo-700" />
                                <span class="text-[10px] font-medium text-indigo-200 dark:text-indigo-700">
                                    PNG · JPG · SVG
                                </span>
                            </div>
                        @endif

                        {{-- Hover overlay con ícono de cámara/upload --}}
                        <div
                            class="absolute inset-0 flex items-center justify-center rounded-2xl bg-indigo-900/50 opacity-0 transition-all duration-200 group-hover:opacity-100 dark:bg-black/60">
                            <x-menu.heroicon name="arrow-up-tray" class="h-6 w-6 text-white" />
                        </div>

                        {{-- Spinner mientras Livewire procesa --}}
                        <div
                            wire:loading
                            wire:target="logo"
                            class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 dark:bg-gray-900/80">
                            <svg
                                class="h-5 w-5 animate-spin text-indigo-500 dark:text-sky-400"
                                fill="none"
                                viewBox="0 0 24 24">
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Botón quitar (solo cuando hay logo) --}}
                    @if ($logo)
                        <button
                            wire:click="$set('logo', null)"
                            type="button"
                            class="text-[11px] font-medium text-rose-400 transition-colors hover:text-rose-600 dark:text-rose-500 dark:hover:text-rose-400">
                            Quitar
                        </button>
                    @else
                        <span class="text-[11px] text-slate-400 dark:text-gray-600">Máx. 2 MB</span>
                    @endif

                    <input
                        x-ref="logoInput"
                        type="file"
                        wire:model="logo"
                        accept="image/png,image/jpeg,image/svg+xml,image/webp"
                        class="sr-only" />

                    @error("logo")
                        <p class="text-center text-[10px] font-medium text-rose-500 dark:text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- ── Fila 3: Contacto (ancho completo) ───────────────────────── --}}
            <div class="mt-5 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                <x-form-inputs.text_input
                    label="Teléfono de Contacto"
                    name="telefono"
                    type="tel"
                    icon="phone"
                    placeholder="+57 300 123 4567"
                    maxlength="20"
                    required />
                <x-form-inputs.text_input
                    label="Correo Electrónico Corporativo"
                    name="email"
                    type="email"
                    icon="envelope"
                    placeholder="contacto@empresa.com"
                    maxlength="200"
                    required />
                <x-form-inputs.text_input
                    label="Web"
                    name="web"
                    type="url"
                    icon="globe-alt"
                    placeholder="www.empresa.com"
                    maxlength="200" />
            </div>

            {{-- ── Footer: advertencia + acciones ──────────────────────────── --}}
            <div class="mt-5 space-y-6 border-t border-slate-100 pt-4 dark:border-gray-800">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex gap-1">
                        <x-feedback.alerts
                            type="warning"
                            size="sm"
                            message="Los datos fiscales Nombre y CUIT no podrán ser editados posteriormente. Por favor, verifique los datos !" />
                    </div>

                    <div class="flex w-full items-center gap-2 sm:w-auto">
                        <button type="button" class="btn-base btn-cancel btn-sm flex-1 sm:flex-none">
                            <x-menu.heroicon name="x-mark" class="h-3.5 w-3.5" />
                            Cancelar
                        </button>
                        <button type="submit" class="btn-base btn-primary btn-sm flex-1 sm:flex-none">
                            <x-menu.heroicon name="document-check" class="h-3.5 w-3.5" />
                            Guardar Empresa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>
