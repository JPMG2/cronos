<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\NationalityForm;
use App\Models\Country;
use App\Models\Province;
use App\Models\Region;
use App\Traits\Livewire\HasNotifications;
use App\Traits\Utilities\WorldConfiguration;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use HasNotifications;
    use WorldConfiguration;

    public NationalityForm $form;

    public bool $provincesLoaded = false;

    public bool $regionsLoaded = false;

    // ── Región ────────────────────────────────────────────────────────────────
    public ?int $regionCountryId = null;

    public ?int $regionProvinceId = null;

    public string $regionName = '';

    public ?int $editingRegionId = null;



    public function loadProvinces(): void
    {
        $this->provincesLoaded = true;
    }

    public function loadRegions(): void
    {
        $this->regionsLoaded = true;
    }

    // ── Updated hooks (cascada) ───────────────────────────────────────────────

    public function updatedRegionCountryId(): void
    {
        $this->regionProvinceId = null;
        unset($this->provincesOptions, $this->regions);
    }

    public function updatedRegionProvinceId(): void
    {
        unset($this->regions);
    }

    public function create(): void
    {
        [$message, $type] = $this->form->storeCountry();
        $this->messageOutPut($message, $type);
    }

    /**
     * @param  mixed  $message
     * @param  mixed  $type
     * @return void
     */
    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->countries);
        $this->getTypeMessage($message, $type);
        $this->cancelCountryEdit();
    }

    public function startEditCountry(int $id): void
    {
        $this->form->fillCountryData($id);
    }

    public function updateCountry(): void
    {
        $this->validate(
            [
                'countryName' => [
                    'required', 'min:3', 'max:100', Rule::unique('countries', 'name')->ignore($this->countryId)
                ],
                'countryCode' => [
                    'required', 'min:2', 'max:3', Rule::unique('countries', 'code')->ignore($this->countryId)
                ],
                'countryPhoneCode' => ['nullable', 'max:10'],
            ],
            [],
            ['countryName' => 'nombre', 'countryCode' => 'código', 'countryPhoneCode' => 'cód. teléfono'],
        );

        Country::query()->findOrFail($this->countryId)->update([
            'name' => $this->countryName,
            'code' => mb_strtoupper($this->countryCode),
            'phone_code' => $this->countryPhoneCode ?: null,
        ]);

        $this->cancelCountryEdit();
        unset($this->countries);
        $this->getTypeMessage('País actualizado correctamente.', 'notifySuccess');
    }

    public function cancelCountryEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleCountryActive(int $id): void
    {
        [$message, $type] = $this->form->updateStatusCountry($id);
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): void
    {
        dd('Queda por implementar confirmación');
    }



    public function startEditRegion(int $id): void
    {
        $region = Region::query()->with('province')->findOrFail($id);
        $this->editingRegionId = $id;
        $this->regionProvinceId = $region->province_id;
        $this->regionCountryId = $region->province->country_id;
        $this->regionName = $region->name;
        unset($this->provincesOptions);
        $this->resetValidation();
    }

    public function updateRegion(): void
    {
        $this->validate(
            [
                'regionProvinceId' => ['required', 'integer', 'exists:provinces,id'],
                'regionName' => [
                    'required', 'min:3', 'max:100',
                    Rule::unique('regions', 'name')
                        ->where('province_id', $this->regionProvinceId)
                        ->ignore($this->editingRegionId),
                ],
            ],
            [],
            ['regionProvinceId' => 'provincia', 'regionName' => 'nombre'],
        );

        Region::query()->findOrFail($this->editingRegionId)->update([
            'province_id' => $this->regionProvinceId,
            'name' => $this->regionName,
        ]);

        $this->regionName = '';
        $this->editingRegionId = null;
        unset($this->regions);
        $this->getTypeMessage('Región actualizada correctamente.', 'notifySuccess');
    }

    public function toggleRegionActive(int $id): void
    {
        $region = Region::query()->findOrFail($id);
        $wasActive = $region->is_active;
        $region->update(['is_active' => !$wasActive]);
        unset($this->regions);
        $this->getTypeMessage(
            $wasActive ? 'Región desactivada.' : 'Región activada.',
            $wasActive ? 'notifyInfo' : 'notifySuccess',
        );
    }


};
?>
@placeholder
<div class="flex h-full flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
        <x-menu.heroicon name="globe-alt" class="h-6 w-6"/>
    </div>
    <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
        Cargando configuración de países…
    </h3>
    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
        Por favor, espera mientras se cargan los datos.
    </p>
</div>

@endplaceholder
<div class="flex h-full flex-col" x-data="nationalityForm">

    {{-- ══ TAB BAR ══ --}}
    <div class="flex items-stretch border-b border-slate-100 bg-white/70 dark:border-gray-800 dark:bg-gray-900/50">

        <button @click="switchTab('countries')"
                :class="activeTab === 'countries'
                    ? 'border-indigo-500 text-indigo-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400'"
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-all duration-150">
            <x-menu.heroicon name="globe-alt" class="h-4 w-4"/>
            Países
        </button>

        <button @click="switchTab('provinces')"
                :class="activeTab === 'provinces'
                    ? 'border-indigo-500 text-indigo-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400'"
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-all duration-150">
            <x-menu.heroicon name="building-library" class="h-4 w-4"/>
            Provincias
        </button>

        <button @click="switchTab('regions')"
                :class="activeTab === 'regions'
                    ? 'border-indigo-500 text-indigo-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400'"
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-all duration-150">
            <x-menu.heroicon name="map-pin" class="h-4 w-4"/>
            Regiones
        </button>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: PAÍSES                                                           --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'countries'" class="flex min-h-0 flex-1 flex-col" x-cloak>

        {{-- FORM País --}}
        <div x-ref="countryForm" class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">
            <div class="flex flex-col gap-2">

                {{-- Fila 1: Nombre (ancho completo) --}}
                <div>
                    <x-form-inputs.text_input
                            label="Nombre del país"
                            name="name"
                            icon="globe-alt"
                            placeholder="Ej: Argentina"
                            wire:model="form.countryName"
                            alpine-error="name"
                            class="uppercase"
                            size="sm"
                            required/>
                </div>

                {{-- Fila 2: Código · Cód. Teléfono · Botones --}}
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                    <div class="sm:w-32 sm:shrink-0">
                        <x-form-inputs.text_input
                                label="Código"
                                name="code"
                                icon="hashtag"
                                placeholder="AR"
                                maxlength="6"
                                x-mask="aaaaaa"
                                wire:model="form.countryCode"
                                alpine-error="code"
                                class="uppercase"
                                size="sm"
                                required/>
                    </div>

                    <div class="sm:w-40 sm:shrink-0">
                        <x-form-inputs.text_input
                                label="Cód. teléfono"
                                name="phone_code"
                                placeholder="54"
                                maxlength="8"
                                x-mask="99999999"
                                wire:model="form.countryPhoneCode"
                                alpine-error="phone_code"
                                size="sm"/>
                    </div>

                    <div class="flex flex-1 items-center justify-end gap-2 sm:mt-[26px]">

                        <div x-show="$wire.form.countryId !== null"
                             x-cloak
                             class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                            <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Editando
                            </span>
                        </div>

                        <x-btn.new-record
                                x-show="$wire.form.countryId === null"
                                @click="cancelCountryEdit"
                                label="Nueva entrada"/>

                        <x-btn.mini-cancel wire:click="cancelCountryEdit"/>

                        <x-btn.save label="{{ $this->form->countryId ? 'Actualizar' : 'Guardar' }}"
                                    @click="submitCountry()"
                                    wire:target="create,updateCountry"/>
                    </div>

                </div>
            </div>
        </div>

        {{-- LISTADO Países --}}
        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

            @if($this->countries->isNotEmpty())
                <div class="flex items-center justify-between px-5 py-2">
                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                        Código · Nombre · Teléfono
                    </span>
                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                        Estado · Acciones
                    </span>
                </div>
                <div class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>
            @endif

            @forelse($this->countries as $country)
                <div wire:key="country-{{ $country->id }}"
                     class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-blue-50 dark:hover:bg-gray-900/40
                            {{ $this->form->countryId === $country->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                    <div class="flex min-w-0 flex-1 items-center gap-2">
                        <span class="shrink-0 rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                            {{ $country->code }}
                        </span>
                        <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                                     {{ $this->form->countryId === $country->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                            {{ $country->name }}
                        </span>
                        @if($country->phone_code)
                            <span class="hidden shrink-0 text-xs text-slate-400 dark:text-gray-600 sm:block">
                                {{ $country->phone_code }}
                            </span>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-1.5">

                        <button type="button"
                                wire:click="toggleCountryActive({{ $country->id }})"
                                wire:loading.class="cursor-wait opacity-50"
                                wire:target="toggleCountryActive({{ $country->id }})"
                                class="rounded-lg transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                                aria-label="{{ $country->is_active ? 'Desactivar' : 'Activar' }} {{ $country->name }}">
                            @if($country->is_active)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60 transition-colors duration-150 hover:bg-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20 dark:hover:bg-emerald-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500 ring-1 ring-inset ring-slate-200/60 transition-colors duration-150 hover:bg-slate-200/80 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700 dark:hover:bg-gray-700/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-gray-600"></span>
                                    Inactivo
                                </span>
                            @endif
                        </button>

                        <span class="h-4 w-px bg-slate-200 dark:bg-gray-700"></span>

                        <x-btn.mini-edit lable="Editar"
                                         @click="$wire.startEditCountry({{ $country->id }}), goTopNationality()"/>
                        <x-btn.mini-delete lable="Eliminar" wire:click="deleteCountry({{ $country->id }})"/>

                    </div>
                </div>

                @if(! $loop->last)
                    <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
                @endif

            @empty
                <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="globe-alt" class="h-6 w-6"/>
                    </div>
                    <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                        Sin países registrados
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                        Usá el formulario de arriba para agregar el primero.
                    </p>
                </div>
            @endforelse

        </div>

    </div>
    <div x-show="activeTab === 'provinces'" x-cloak class="flex min-h-0 flex-1 flex-col">
        @if($provincesLoaded)
            <livewire:configuracion.tablas.provinces lazy/>
        @endif
    </div>

    <div x-show="activeTab === 'regions'" x-cloak class="flex min-h-0 flex-1 flex-col">
        @if($regionsLoaded)
            <livewire:configuracion.tablas.regions lazy/>
        @endif
    </div>

</div>

@script
<script>
    Alpine.data('nationalityForm', () => ({
        activeTab: 'countries',
        errors: {},

        switchTab(tab) {
            this.activeTab = tab;

            if (tab === 'provinces' && !this.$wire.provincesLoaded) {
                this.$wire.loadProvinces();
            }

            if (tab === 'regions' && !this.$wire.regionsLoaded) {
                this.$wire.loadRegions();
            }
            this.errors = {};
        },

        cancelCountryEdit() {
            this.$wire.cancelCountryEdit();
            this.errors = {};
        },
        submitCountry() {
            const isEditing = this.$wire.form.countryId !== null;
            this.errors = validate(
                {name: this.$wire.form.countryName, code: this.$wire.form.countryCode},
                {
                    name: ['required', ['minLength', 3]],
                    code: ['required', ['minLength', 2]],
                },
            );
            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.updateCountry() : this.$wire.create();
            }
        },
        goTopNationality() {
            this.$refs.countryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        submitProvince(countryId, name, provinceId) {
            const isEditing = provinceId !== null;

            this.errors = validate(
                {country_id: countryId, name: name},
                {country_id: ['required'], name: ['required', ['minLength', 3]]},
            );
            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$dispatch('updateProvinces') : this.$dispatch('createProvinces');
            }
        },

        cancelRegionEdit() {
            this.$wire.cancelRegionEdit();
            this.errors = {};
        },
        submitRegion(countryId,provinceId,regionId,name) {
            const isEditing = regionId !== null;
            this.errors = validate(
                {country_id: countryId, province_id: provinceId, name: name},
                {country_id: ['required'],province_id: ['required'], name: ['required', ['minLength', 3]]},
            );
            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$dispatch('updateRegion') : this.$dispatch('createRegion');
            }
        },
    }));
</script>
@endscript
