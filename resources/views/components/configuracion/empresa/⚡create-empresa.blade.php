<?php

use App\Models\Company;
use App\Models\CurrentStatus;
use App\Models\Region;
use App\Models\TaxCondition;
use App\Models\WorldSettings;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Empresa')] class extends Component {
    use HasNotifications;
    use WithFileUploads;

    #[Validate('required|unique:companies,name|min:3|max:200')]
    public string $name = '';
    #[Validate('required|unique:companies,fiscal_identifier|min:11|max:15')]
    public string $fiscalNumber = '';
    #[Validate('required|exists:tax_conditions,id')]
    public null|int $taxConditionId = null;
    #[Validate('required|exists:current_statuses,id')]
    public null|int $currentStatusId = null;
    #[Validate('required|exists:regions,id')]
    public null|int $regionId = null;
    #[Validate('required|min:6|max:200')]
    public string $address = '';
    #[Validate('required|min:3|max:12')]
    public string $postalCode = '';
    #[Validate('required|min:6|max:15')]
    public string $phone = '';
    #[Validate('required|min:3|max:200|email|unique:companies,email')]
    public string $email = '';
    #[Validate('sometimes|url|unique:companies,web')]
    public string $web = '';
    public bool $isExisting = false;

    #[Validate('nullable|image|max:2048|mimes:jpg,jpeg,png,svg,webp')]
    public null|string $logo = null;

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
        $company = Company::query()->first();

        if (! $company) {
            return;
        }

        $this->name            = $company->name;
        $this->fiscalNumber    = $company->fiscal_identifier;
        $this->taxConditionId  = $company->tax_condition_id;
        $this->currentStatusId = $company->current_status_id;
        $this->regionId        = $company->region_id;
        $this->address         = $company->address;
        $this->postalCode      = $company->postal_code;
        $this->phone           = $company->phone;
        $this->email           = $company->email;
        $this->web             = $company->website ?? '';
        $this->isExisting      = true;
    }

    public function saveCompany(): void
    {
        $data = $this->validate();

        try {
            $this->createUpdate($data);
            $this->notifySuccess('Empresa creada exitosamente.');
        } catch (Exception $e) {
            $this->notifyError('Error al crear empresa: ' . $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->resetValidation();
        $this->mount();
    }

    public function createUpdate(array $data): Company
    {
        return DB::transaction(fn () => Company::query()->updateOrCreate(
            ['fiscal_identifier' => $data['fiscalNumber']],
            [
                'name'              => $data['name'],
                'current_status_id' => $data['currentStatusId'],
                'tax_condition_id'  => $data['taxConditionId'],
                'region_id'         => $data['regionId'],
                'address'           => $data['address'],
                'postal_code'       => $data['postalCode'],
                'phone'             => $data['phone'],
                'email'             => $data['email'],
                'website'           => $data['web'] ?: null,
            ],
        ));
    }
}
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <x-form-style.header-form
                title="Crear Nueva Empresa"
                description="Configure los datos de la organización médica para integrar sus servicios clínicos."
                sign="Nueva Empresa."/>
        <div x-data="companyForm">
            <div class="relative z-10 px-8 py-4">
                {{-- ── Grid 2×2: 4 cards hijos directos — alturas iguales por fila ── --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                        {{-- Card 01: Datos Fiscales ──────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">01</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Datos Fiscales</span>
                            </div>
                            <div class="space-y-4">
                                <x-form-inputs.text_input
                                        label="Nombre de la Empresa"
                                        name="name"
                                        icon="building-office"
                                        placeholder="Ej: Clínica Santa María"
                                        maxlength="200"
                                        wire:model="name"
                                        alpine-error="name"
                                        :readonly="$isExisting"
                                        required/>
                                <div class="grid grid-cols-5 gap-4">
                                    <div class="col-span-2">
                                        <x-form-inputs.text_input
                                                label="CUIT"
                                                name="fiscalNumber"
                                                icon="identification"
                                                placeholder="9001234567"
                                                maxlength="13"
                                                wire:model="fiscalNumber"
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
                                                wire:model="taxConditionId"
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
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">03</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Contacto</span>
                            </div>
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
                                                wire:model="phone"
                                                alpine-error="phone"
                                                x-mask="999999999999999"
                                                required/>
                                    </div>
                                    <div class="col-span-3">
                                        <x-form-inputs.text_input
                                                label="Web"
                                                name="web"
                                                type="url"
                                                icon="globe-alt"
                                                placeholder="www.empresa.com"
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
                                        wire:model="email"
                                        alpine-error="email"
                                        required/>
                            </div>
                        </div>

                        {{-- Card 02: Ubicación ────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">02</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Ubicación</span>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <x-form-inputs.autocomplete
                                                label="Ciudad"
                                                name="regionId"
                                                placeholder="Seleccionar ciudad…"
                                                :options="$this->regions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                                wire:model="regionId"
                                                alpine-error="regionId"
                                                required/>
                                    </div>
                                    <x-form-inputs.text_input
                                            label="Cód. Postal"
                                            name="codpostal"
                                            icon="inbox-arrow-down"
                                            placeholder="999999"
                                            maxlength="6"
                                            wire:model="postalCode"
                                            alpine-error="postalCode"
                                            required/>
                                </div>
                                <x-form-inputs.text_input
                                        label="Dirección"
                                        name="direccion"
                                        icon="map"
                                        placeholder="Calle, Altura"
                                        maxlength="200"
                                        wire:model="address"
                                        alpine-error="address"
                                        required/>
                            </div>
                        </div>

                        {{-- Card 04: Identidad de Marca ──────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                             x-data="{ dragging: false }">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">04</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Identidad de Marca</span>
                            </div>
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
                                        : @js($logo)
                                            ? 'border-indigo-200 dark:border-indigo-700/60'
                                            : 'border-indigo-200/80 dark:border-gray-700'"
                                            class="group relative h-[7.5rem] w-[7.5rem] cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed bg-white transition-all duration-200 dark:bg-gray-800/60"
                                            @click="$refs.logoInput.click()"
                                            role="button"
                                            aria-label="Subir logo de empresa"
                                            tabindex="0"
                                            @keydown.enter.prevent="$refs.logoInput.click()">
                                        @if ($logo)
                                            <img
                                                    src="{{ $logo->temporaryUrl() }}"
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
                                                wire:target="logo"
                                                class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 dark:bg-gray-900/80">
                                            <svg class="h-5 w-5 animate-spin text-indigo-500 dark:text-sky-400" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if($logo)
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
                                            class="sr-only"/>
                                    @error('logo')
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
                                            wire:model="currentStatusId"
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

                {{-- ── Footer: advertencia + acciones ──────────────────────────── --}}
                <div class="mt-5 space-y-6 border-t border-slate-100 pt-4 dark:border-gray-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <div class="flex gap-1">
                            <x-feedback.alerts
                                    type="warning"
                                    size="sm"
                                    message="Los datos fiscales Nombre y CUIT no podrán ser editados posteriormente. Por favor, verifique los datos !"/>
                        </div>

                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <x-btn.cancel label="Descartar" wire:click="cancel"/>
                            <x-btn.save label=" Guardar Empresa" @click="submit()" wire-target="saveCompany"/>
                        </div>
                    </div>
                </div>
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
                    name: this.$wire.name,
                    fiscalNumber: this.$wire.fiscalNumber,
                    taxConditionId: this.$wire.taxConditionId,
                    regionId: this.$wire.regionId,
                    address: this.$wire.address,
                    postalCode: this.$wire.postalCode,
                    phone: this.$wire.phone,
                    email: this.$wire.email,
                    currentStatusId: this.$wire.currentStatusId,
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
            if (Object.keys(this.errors).length === 0) this.$wire.saveCompany();
        },
    }));
</script>
@endscript
