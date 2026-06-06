<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\NationalityForm;
use App\Traits\Livewire\HasNotifications;
use App\Traits\Utilities\WorldConfiguration;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component {
    use HasNotifications;
    use WorldConfiguration;

    public NationalityForm $form;

    public bool $provincesLoaded = false;

    public bool $regionsLoaded = false;

    public function loadProvinces(): void
    {
        $this->provincesLoaded = true;
    }

    public function loadRegions(): void
    {
        $this->regionsLoaded = true;
    }

    public function create(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->storeCountry();
        $this->messageOutPut($message, $type);
    }

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
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateCountry();
        $this->messageOutPut($message, $type);
    }

    public function cancelCountryEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleCountryActive(int $id): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateStatusCountry($id);
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): never
    {
        Gate::authorize('parametros.update');
        dd('Queda por implementar confirmación');
    }

};
?>

@placeholder
<div class="flex h-full flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
        <x-menu.heroicon name="globe-alt" class="h-6 w-6" />
    </div>
    <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
        Cargando configuración de países…
    </h3>
    <p class="mt-1 font-body text-xs text-slate-500 dark:text-gray-400">
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
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-colors duration-150">
            <x-menu.heroicon name="globe-alt" class="h-4 w-4" />
            Países
        </button>

        <button @click="switchTab('provinces')"
                :class="activeTab === 'provinces'
                    ? 'border-indigo-500 text-indigo-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400'"
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-colors duration-150">
            <x-menu.heroicon name="building-library" class="h-4 w-4" />
            Provincias
        </button>

        <button @click="switchTab('regions')"
                :class="activeTab === 'regions'
                    ? 'border-indigo-500 text-indigo-600 dark:border-sky-400 dark:text-sky-400'
                    : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400'"
                class="flex items-center gap-2 border-b-2 px-5 py-3 text-xs font-bold uppercase tracking-wider transition-colors duration-150">
            <x-menu.heroicon name="map-pin" class="h-4 w-4" />
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
                        required />
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
                            required />
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
                            size="sm" />
                    </div>

                    <div class="flex flex-1 items-center justify-end gap-2 sm:mt-[26px]">

                        <div x-show="$wire.form.countryId !== null"
                             x-cloak
                             class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                            <span class="h-1.5 w-1.5 motion-safe:animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                            <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Editando
                            </span>
                        </div>

                        <x-btn.new-record
                            x-show="$wire.form.countryId === null"
                            @click="cancelCountryEdit"
                            label="Nueva entrada" />

                        <x-btn.mini-cancel wire:click="cancelCountryEdit" />

                        <x-btn.save label="{{ $this->form->countryId ? 'Actualizar' : 'Guardar' }}"
                                    @click="submitCountry()"
                                    wire:target="create,updateCountry" />
                    </div>

                </div>
            </div>
        </div>

        {{-- LISTADO Países --}}
        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

            @if($this->countries->isNotEmpty())
                <x-list.header :code="true" extra="Teléfono" />
            @endif

            @forelse($this->countries as $country)
                <div wire:key="country-{{ $country->id }}"
                     class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                            {{ $this->form->countryId === $country->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                    <div class="flex min-w-0 flex-1 items-center gap-2">
                        <span class="shrink-0 min-w-[52px] text-center rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
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

                        <x-list.toggle
                            :active="$country->is_active"
                            size="sm"
                            wire:click="toggleCountryActive({{ $country->id }})"
                            wire:loading.class="cursor-wait opacity-50"
                            wire:target="toggleCountryActive({{ $country->id }})"
                            aria-label="{{ $country->is_active ? 'Desactivar' : 'Activar' }} {{ $country->name }}" />

                        <x-list.divider />

                        <x-list.actions>
                            <x-btn.mini-edit
                                lable="Editar"
                                data-name="{{ $country->name }}"
                                data-code="{{ $country->code }}"
                                data-phone="{{ $country->phone_code }}"
                                @click="
                                    $wire.form.countryName = $el.dataset.name;
                                    $wire.form.countryCode = $el.dataset.code;
                                    $wire.form.countryPhoneCode = $el.dataset.phone;
                                    $wire.startEditCountry({{ $country->id }});
                                    goTopNationality();
                                " />
                            <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $country->id }})" />
                        </x-list.actions>

                    </div>
                </div>

                @if(! $loop->last)
                    <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
                @endif

            @empty
                <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="globe-alt" class="h-6 w-6" />
                    </div>
                    <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                        Sin países registrados
                    </h3>
                    <p class="mt-1 font-body text-xs text-slate-500 dark:text-gray-400">
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
                { name: this.$wire.form.countryName, code: this.$wire.form.countryCode },
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
                { country_id: countryId, name: name },
                { country_id: ['required'], name: ['required', ['minLength', 3]] },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$dispatch('updateProvinces') : this.$dispatch('createProvinces');
            }
        },

        submitRegion(countryId, provinceId, regionId, name) {
            const isEditing = regionId !== null;

            this.errors = validate(
                { country_id: countryId, province_id: provinceId, name: name },
                { country_id: ['required'], province_id: ['required'], name: ['required', ['minLength', 3]] },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$dispatch('updateRegion') : this.$dispatch('createRegion');
            }
        },
    }));
</script>
@endscript
