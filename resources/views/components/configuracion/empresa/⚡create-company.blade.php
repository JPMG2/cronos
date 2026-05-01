<?php

declare(strict_types=1);

use App\Dto\Style\ModalConfig;
use App\Livewire\Forms\Configuracion\Empresa\CompanyForm;
use App\Models\Company;
use App\Models\CurrentStatus;
use App\Models\Region;
use App\Models\TaxCondition;
use App\Models\WorldSettings;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Empresa')]
class extends Component {
    use HasNotifications;
    use WithFileUploads;

    public CompanyForm $form;
    public bool $isExisting = false;


    #[Computed]
    public function statuses(): Collection
    {
        return CurrentStatus::query()
            ->whereIn('id', [1, 2, 9, 10])
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function taxCondition(): Collection
    {
        return TaxCondition::query()
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function regions(): Collection
    {
        return Region::query()
            ->where('province_id', WorldSettings::defaultProvince())
            ->defaultFirst(WorldSettings::defaultRegion())
            ->get();
    }

    public function mount(): void
    {
        $company = $this->resolveCompany();

        if (!$company) {
            return;
        }

        $this->isExisting = true;
        $this->form->loadCompanyData($company);
    }

    public function cancel(): void
    {
        $this->resetValidation();

        $company = $this->resolveCompany();

        if ($company) {
            $this->form->loadCompanyData($company);
        } else {
            $this->form->reset();
            $this->isExisting = false;
        }
    }

    protected function resolveCompany(): ?Company
    {
        return Company::query()->first();
    }

    public function adviceCompany(): void
    {
        $this->form->validateCompany();

        if (!$this->resolveCompany()) {

            $config = new ModalConfig(
                title: 'Confirmar registro',
                message: 'Confirmá en crear la compañia con los datos ingresados. Los datos fiscales Nombre y CUIT no podrán ser editados.',
                type: 'info',
                buttons: [
                    [
                        'label' => 'Aceptar',
                        'action' => 'storeCompany',
                        'class' => 'save',
                        'params' => [],
                    ]
                ]);
            $this->dispatch('openModal', config: (array) $config);
        } else {
            $this->create(null);
        }
    }

    #[On('storeCompany')]
    public function create(?array $params): void
    {
        [$message, $type] = $this->form->checkCompany();
        $this->isExisting  = $this->form->companyId !== null;
        $this->getTypeMessage($message, $type);
    }
}
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <x-form-style.header-form
                title="Crear Nueva Empresa"
                description="Configure los datos de la organización médica para integrar sus servicios clínicos."
                sign="{{ $isExisting ? 'Empresa Registrada.' : 'Nueva Empresa.' }}"/>
        <div x-data="companyForm">
            <div class="relative z-10 px-8 py-4">
                {{-- ── Grid 2×2: 4 cards hijos directos — alturas iguales por fila ── --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                    {{-- Card 01: Datos Fiscales ──────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <x-form-style.number-tag number="01" label="Datos Fiscales"/>

                        <div class="space-y-4">
                            <x-form-inputs.text_input
                                    label="Nombre de la Empresa"
                                    name="name"
                                    icon="building-office"
                                    placeholder="Ej: Clínica Santa María"
                                    maxlength="200"
                                    wire:model="form.name"
                                    alpine-error="name"
                                    :readonly="$isExisting"
                                    class="uppercase"
                                    required/>
                            <div class="grid grid-cols-5 gap-4">
                                <div class="col-span-2">
                                    <x-form-inputs.text_input
                                            label="CUIT"
                                            name="fiscalNumber"
                                            icon="identification"
                                            placeholder="9001234567"
                                            maxlength="13"
                                            wire:model="form.fiscalNumber"
                                            x-mask="9999999999999"
                                            alpine-error="fiscalNumber"
                                            :readonly="$isExisting"
                                            required/>
                                </div>
                                <div class="col-span-3">
                                    <x-form-inputs.select
                                            label="Condición IVA"
                                            name="tipo_entidad"
                                            icon="building-library"
                                            wire:model="form.taxConditionId"
                                            alpine-error="taxConditionId"
                                            required>
                                        <option value="" readonly>Seleccionar tipo…</option>
                                        @foreach ($this->taxCondition as $tax)
                                            <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                        @endforeach
                                    </x-form-inputs.select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 03: Contacto ─────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <x-form-style.number-tag number="03" label="Contacto"/>

                        <div class="space-y-4">
                            <div class="grid grid-cols-5 gap-4">
                                <div class="col-span-2">
                                    <x-form-inputs.text_input
                                            label="Teléfono"
                                            name="telefono"
                                            type="tel"
                                            icon="phone"
                                            placeholder="299 123 4567"
                                            maxlength="20"
                                            wire:model="form.phone"
                                            alpine-error="phone"
                                            x-mask="999999999999999"
                                            required/>
                                </div>
                                <div class="col-span-3">
                                    <x-form-inputs.text_input
                                            label="Web"
                                            name="website"
                                            type="url"
                                            icon="globe-alt"
                                            placeholder="www.empresa.com"
                                            wire:model="form.website"
                                            maxlength="200"/>
                                </div>
                            </div>
                            <x-form-inputs.text_input
                                    label="Correo Electrónico Corporativo"
                                    name="email"
                                    type="email"
                                    icon="envelope"
                                    placeholder="contacto@empresa.com"
                                    maxlength="200"
                                    wire:model="form.email"
                                    alpine-error="email"
                                    required/>
                        </div>
                    </div>

                    {{-- Card 02: Ubicación ────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <x-form-style.number-tag number="02" label="Ubicación"/>

                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-2">
                                    <x-form-inputs.autocomplete
                                            wire:key="region-{{ $form->regionId }}"
                                            label="Ciudad"
                                            name="regionId"
                                            placeholder="Seleccionar ciudad…"
                                            :options="$this->regions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                            wire:model="form.regionId"
                                            alpine-error="regionId"
                                            :value="$form->regionId"
                                            required/>
                                </div>
                                <x-form-inputs.text_input
                                        label="Cód. Postal"
                                        name="postalCode"
                                        icon="inbox-arrow-down"
                                        placeholder="999999"
                                        maxlength="6"
                                        wire:model="form.postalCode"
                                        alpine-error="postalCode"
                                        required/>
                            </div>
                            <x-form-inputs.text_input
                                    label="Dirección"
                                    name="address"
                                    icon="map"
                                    placeholder="Calle, Altura"
                                    maxlength="200"
                                    wire:model="form.address"
                                    alpine-error="address"
                                    class="capitalize"
                                    required/>
                        </div>
                    </div>

                    {{-- Card 04: Identidad de Marca ──────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                         x-data="{ dragging: false }">
                        <x-form-style.number-tag number="04" label="Logo y Estatus"/>

                        <div class="flex gap-5">
                            {{-- Logo uploader ─────────────────────────────── --}}
                            <div class="flex shrink-0 flex-col items-center gap-2">
                                <span class="self-start text-xs font-semibold text-slate-400 dark:text-gray-500">Logo</span>
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
                                        : {{ $form->logo ? 'true' : 'false' }}
                                            ? 'border-indigo-200 dark:border-indigo-700/60'
                                            : 'border-indigo-200/80 dark:border-gray-700'"
                                        class="group relative h-[7.5rem] w-[7.5rem] cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed bg-white transition-all duration-200 dark:bg-gray-800/60"
                                        @click="$refs.logoInput.click()"
                                        role="button"
                                        aria-label="Subir logo de empresa"
                                        tabindex="0"
                                        @keydown.enter.prevent="$refs.logoInput.click()">
                                    @if ($form->logo)
                                        <img
                                                src="{{ $form->logo->temporaryUrl() }}"
                                                class="h-full w-full object-contain p-1.5"
                                                alt="Logo preview"/>
                                    @else
                                        <div class="flex h-full flex-col items-center justify-center gap-1.5">
                                            <x-menu.heroicon
                                                    name="building-office"
                                                    class="h-8 w-8 text-indigo-200 dark:text-indigo-700"/>
                                            <span class="text-[10px] font-medium text-indigo-200 dark:text-indigo-700">
                                                    PNG · JPG · SVG
                                                </span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center rounded-2xl bg-indigo-900/50 opacity-0 transition-all duration-200 group-hover:opacity-100 dark:bg-black/60">
                                        <x-menu.heroicon name="arrow-up-tray" class="h-6 w-6 text-white"/>
                                    </div>
                                    <div
                                            wire:loading
                                            wire:target="form.logo"
                                            class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 dark:bg-gray-900/80">
                                        <svg class="h-5 w-5 animate-spin text-indigo-500 dark:text-sky-400" fill="none"
                                             viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                    </div>
                                </div>
                                @if($form->logo)
                                    <button
                                            wire:click="$set('form.logo', null)"
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
                                        wire:model="form.logo"
                                        accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                        class="sr-only"/>
                                @error('form.logo')
                                <p class="text-center text-[10px] font-medium text-rose-500 dark:text-rose-400">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Estado operativo ──────────────────────────── --}}
                            <div class="flex flex-1 flex-col">
                                <x-form-inputs.select
                                        label="Estado"
                                        name="currentStatusId"
                                        icon="check-circle"
                                        wire:model="form.currentStatusId"
                                        alpine-error="currentStatusId"
                                        description="Disponibilidad operativa de la empresa en la plataforma."
                                        required>
                                    <option value="">Estado…</option>
                                    @foreach ($this->statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </x-form-inputs.select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Footer ──────────────────────────────────────────────────────── --}}
                <x-form-style.footer-button>

                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <x-btn.cancel label="Descartar" wire:click="cancel"/>
                            <x-btn.save label=" Guardar Empresa" @click="submit()" wire-target="adviceCompany"/>
                        </div>
                </x-form-style.footer-button>
            </div>
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>
@script
<script>
    Alpine.data('companyForm', () => ({
        errors: {},
        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    fiscalNumber: this.$wire.form.fiscalNumber,
                    taxConditionId: this.$wire.form.taxConditionId,
                    regionId: this.$wire.form.regionId,
                    address: this.$wire.form.address,
                    postalCode: this.$wire.form.postalCode,
                    phone: this.$wire.form.phone,
                    email: this.$wire.form.email,
                    currentStatusId: this.$wire.form.currentStatusId,
                },
                {
                    name: ['required', ['minLength', 3]],
                    fiscalNumber: ['required', ['minLength', 10]],
                    taxConditionId: ['required'],
                    regionId: ['required'],
                    address: ['required', ['minLength', 6]],
                    postalCode: ['required', ['minLength', 3]],
                    phone: ['required', ['minLength', 10]],
                    email: ['required', ['minLength', 3], ['email']],
                    currentStatusId: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.adviceCompany();
        },
    }));
</script>
@endscript
