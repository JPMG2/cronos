<?php

use App\Models\Province;
use App\Models\TaxCondition;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title("Empresa")]
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
        return Province::query()
            ->defaultFirst()
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

        <div class="relative z-10 space-y-4 px-8 py-4">

            <div class="grid grid-cols-12 gap-x-4 gap-y-4">
                <div class="col-span-12 sm:col-span-5">
                    <x-form-inputs.text_input
                        label="Nombre de la Empresa"
                        name="nombre"
                        icon="building-office"
                        placeholder="Ej: Clínica Santa María"
                        required />
                </div>
                <div class="col-span-12 sm:col-span-3">
                    <x-form-inputs.text_input
                        label="CUIT"
                        name="cuit"
                        icon="identification"
                        placeholder="900.123.456-7"
                        required />
                </div>
                <div class="col-span-12 sm:col-span-4">
                    <x-form-inputs.select label="Condición IVA" name="tipo_entidad" icon="building-library" required>
                        <option value="" readonly>Seleccionar tipo…</option>
                        @foreach ($this->taxCondition as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                        @endforeach
                    </x-form-inputs.select>
                </div>
            </div>

            {{-- ── Fila 2: iguales ── --}}
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                <x-form-inputs.autocomplete
                    label="Provincia"
                    name="provinca_id"
                    placeholder="Seleccionar provincia…"
                    :options="$this->provinces->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                    required />
                <x-form-inputs.text_input
                    label="Teléfono de Contacto"
                    name="telefono"
                    type="tel"
                    icon="phone"
                    placeholder="+57 300 123 4567" />
                <x-form-inputs.text_input
                    label="Correo Electrónico Corporativo"
                    name="email"
                    type="email"
                    icon="envelope"
                    placeholder="contacto@empresa.com" />
            </div>
            {{-- ── Ancho completo: descripción + actions ── --}}
            <div class="space-y-6 border-t border-slate-100 pt-4 dark:border-gray-800 md:col-span-2">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    {{-- Hint informativo --}}
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
