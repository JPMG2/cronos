<?php

declare(strict_types=1);

use App\Dto\Style\ModalConfig;
use App\Enums\Styles\StatusColors;
use App\Livewire\Forms\Configuracion\Empresa\BranchForm;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CurrentStatus;
use App\Models\Province;
use App\Models\Region;
use App\Models\WorldSettings;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Sucursales')]
class extends Component {
    use HasNotifications;
    use WithFileUploads;

    public BranchForm $form;

    #[Computed]
    public function companyData(): ?Company
    {
        return Company::query()->first();
    }

    #[Computed]
    public function statuses(): Collection
    {
        return CurrentStatus::query()
            ->whereIn('id', [1, 2, 9, 10])
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function provinces(): Collection
    {
        return Province::query()
            ->where('country_id', WorldSettings::defaultCountry())
            ->defaultFirst(WorldSettings::defaultProvince())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function regions(): Collection
    {

        return Region::query()
            ->where('province_id', $this->form->province_id)
            ->defaultFirst(WorldSettings::defaultRegion())
            ->get();
    }

    #[Computed]
    public function branches(): Collection
    {
        return Branch::query()->listBranches()->get();
    }

    public function selectBranch(int $branchId): void
    {
        $this->form->fillFromBranch($branchId);
    }

    public function newBranch(): void
    {
         $this->resetValidation();
         $this->form->reset();
         
    }

    public function adviceBranch(): void
    {
        $this->form->validateBranchData();

        if(is_null($this->form->branch_id)){

        $config = new ModalConfig(
            title: 'Confirmar registro',
            message: 'Confirmá en crear la sucursal con los datos ingresados. El campo código no podrá ser modificado luego de ser creado.',
            type: 'info',
            buttons: [
                [
                    'label' => 'Aceptar',
                    'action' => 'storeBranch',
                    'class' => 'save',
                    'params' => [],
                ]
            ]);
            $this->dispatch('openModal', config: (array) $config);
        }
        $this->update();
    }

    #[On('storeBranch')]
    public function create(?array $params): void
    {
      [$message, $type] = $this->form->createBranch();
      $this->getTypeMessage($message, $type);
    }

    public function update(): void
    {
      [$message, $type] =  $this->form->updateBranch();
      $this->getTypeMessage($message, $type);
    }

};
?>


<x-form-style.border-style>
    <x-form-style.main-div>
        <div x-data="branchManager">

            {{-- ══ Header ══════════════════════════════════════════════════════════ --}}
            <div class="flex items-start justify-between border-b border-slate-100 px-8 py-4 dark:border-gray-800">
                <div>
                    <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100"
                        x-text="mode === 'edit' ? 'Editando Sucursal' : 'Crear Nueva Sucursal'">
                        Crear Nueva Sucursal
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400"
                       x-text="mode === 'edit'
                           ? 'Modificá los datos de operación.Los cambios impactan a partir del guardado.'
                           : 'Configure los datos de la sucursal para integrar sus servicios clínicos.'">
                    </p>
                </div>
                {{-- Badge: editando --}}
                <div x-show="mode === 'edit'"
                     x-cloak
                     class="hidden shrink-0 items-center gap-2 rounded-xl border border-amber-200/80 bg-amber-50 px-4 py-2 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                    <span class="font-label text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400"
                          x-text="'Editando · ' + editingCode"></span>
                </div>
                {{-- Badge: nueva --}}
                <div x-show="mode === 'create'"
                     class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                    <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                        Nueva Sucursal.
                    </span>
                </div>
            </div>

            {{-- ══ Selector de sucursal ════════════════════════════════════════════ --}}
            {{--
                Patrón combobox: escala a cualquier cantidad de sucursales.
                El dropdown filtra client-side sobre los items renderizados por Blade.
                La tarjeta "Editando" muestra la sede seleccionada con todos sus datos.
            --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/30"
                 x-data="{ dropOpen: false, dropSearch: '', selectedLabel: '' }"
                 @keydown.escape.window="dropOpen = false">

                {{-- Subheader: ícono + título + contador --}}
                <div class="mb-2 flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="building-office" class="h-4 w-4"/>
                    </div>
                    <span class="font-headline text-sm font-bold text-slate-700 dark:text-gray-300">Seleccionar sede</span>
                    @if($this->branches->count() > 0)
                        <span class="inline-flex items-center rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                            {{ $this->branches->count() }} {{ $this->branches->count() === 1 ? 'sede' : 'sedes' }}
                        </span>
                    @endif
                </div>

                {{-- Fila: combobox + botón nueva sucursal --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">

                    {{-- ── Combobox ──────────────────────────────────────────────── --}}
                    <div class="relative flex-1" @click.outside="dropOpen = false">

                        {{-- Trigger --}}
                        <div class="relative">
                            <x-menu.heroicon
                                    name="magnifying-glass"
                                    class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-gray-500"/>
                            <input
                                    type="text"
                                    :value="dropOpen ? dropSearch : selectedLabel"
                                    @focus="dropOpen = true; dropSearch = ''"
                                    @input="dropSearch = $event.target.value; dropOpen = true"
                                    :placeholder="selectedLabel && !dropOpen ? '' : 'Buscar sucursal por nombre o código…'"
                                    autocomplete="off"
                                    role="combobox"
                                    :aria-expanded="dropOpen"
                                    aria-haspopup="listbox"
                                    class="w-full rounded-xl border border-indigo-200/80 bg-white py-2.5 pl-10 pr-10 text-sm placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-sky-500 dark:focus:ring-sky-400/25"/>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 transition-transform duration-200"
                                  :class="dropOpen ? 'rotate - 180' : ''">
                                <x-menu.heroicon name="chevron-up-down"
                                                 class="h-4 w-4 text-slate-400 dark:text-gray-500"/>
                            </span>
                        </div>

                        {{-- Dropdown list --}}
                        <div
                                x-show="dropOpen"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="absolute z-30 mt-1.5 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/60 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/40"
                                role="listbox">

                            @forelse($this->branches as $branch)
                                @php
                                    $initials   = $branch->name ? strtoupper(substr($branch->name, 0, 2)) : ' ?? ';
                                    $statusEnum = StatusColors::tryFrom($branch->currentStatus?->name ?? '');
                                @endphp

                                <button
                                        type="button"
                                        x-show="dropSearch === ''
                                            || '{{ strtolower($branch->name) }}'.includes(dropSearch.toLowerCase())
                                            || '{{ strtolower($branch->code) }}'.includes(dropSearch.toLowerCase())"
                                        @click="selectBranch({{ $branch->id }},'{{ $branch->code }}'); selectedLabel = '{{ addslashes(mb_strtoupper($branch->name)) }}'; dropOpen = false; dropSearch = ''"
                                        class="group flex w-full items-center gap-3 border-b border-slate-100/80 px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-indigo-50/80 dark:border-gray-800 dark:hover:bg-gray-800/60"
                                        :class="editingCode === '{{ $branch->code }}' ? 'bg-indigo-50/60 dark:bg-indigo-500/10' : ''"
                                        role="option"
                                        :aria-selected="editingCode === '{{ $branch->code }}'">

                                    {{-- Avatar --}}
                                    <x-form-style.avatar
                                        :colorInt="$loop->index">
                                        {{ $initials }}
                                   </x-form-style.avatar>

                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="break-words text-sm font-semibold leading-snug text-slate-800 group-hover:text-indigo-700 dark:text-gray-100 dark:group-hover:text-sky-300">
                                            {{ mb_strtoupper($branch->name) }}
                                            @if($branch->is_default)
                                                <span class="ml-1 inline-flex items-center rounded-md bg-indigo-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                                                    Principal
                                                </span>
                                            @endif
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">
                                            @if($branch->region?->name)
                                                {{ $branch->region->name }} ·
                                            @endif#{{ mb_strtoupper($branch->code) }}
                                        </p>
                                    </div>

                                    {{-- Status badge --}}
                                    @if($statusEnum)
                                        <x-form-style.badge x-cloak
                                                            class="{{ $statusEnum->dotClass() }}">{{ $statusEnum->label() }}</x-form-style.badge>
                                    @endif

                                    {{-- Check activo --}}
                                    <span x-show="editingCode === '{{ $branch->code }}'"
                                          class="ml-1 shrink-0 text-indigo-600 dark:text-sky-400">
                                        <x-menu.heroicon name="check-circle" class="h-4 w-4"/>
                                    </span>
                                </button>

                            @empty
                                <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
                                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10">
                                        <x-menu.heroicon name="building-office"
                                                         class="h-5 w-5 text-indigo-400 dark:text-indigo-500"/>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-400">Sin sucursales
                                        aún</p>
                                    <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-600">Creá la primera con el
                                        botón.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ── Botón nueva sucursal ────────────────────────────────────── --}}
                    <button
                            @click="newBranch(); selectedLabel = ''; dropOpen = false; dropSearch = ''"
                            class="flex shrink-0 items-center gap-2 rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60">
                        <x-menu.heroicon name="plus-circle" class="h-4 w-4"/>
                        Nueva sucursal
                    </button>
                </div>


            </div>{{-- /selector --}}

            {{-- ══ Formulario (ancho completo) ════════════════════════════════════ --}}
            <div class="relative z-10 px-8 py-4">
                <div class="grid grid-cols-1 gap-3">

                    {{-- Card 01: Identificación — full width ─────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">

                       <x-form-style.number-tag number="01" label="Identificación" />
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start">
                            <div class="flex-[3]">
                                <x-form-inputs.text_input
                                        label="Sucursal"
                                        name="name"
                                        icon="building-office"
                                        placeholder="Ej: Cipolletti Centro Sur"
                                        maxlength="200"
                                        size="lg"
                                        wire:model="form.name"
                                        alpine-error="name"
                                        class="uppercase"
                                        required/>
                            </div>
                            <div class="flex-[3]">
                                <x-form-inputs.text_input
                                        label="Empresa"
                                        name="companyName"
                                        icon="building-office-2"
                                        size="lg"
                                        class="uppercase"
                                        :value="$this->companyData->name ?? ''"
                                        :readonly="true"/>
                            </div>
                            <div class="w-full lg:w-36 lg:shrink-0">
                                <x-form-inputs.text_input
                                        label="Código"
                                        name="code"
                                        icon="hashtag"
                                        placeholder="CIP-01"
                                        maxlength="20"
                                        size="lg"
                                        wire:model="form.code"
                                        description="Auto-generado."
                                        class="uppercase"
                                        :readonly="!($form->code === '')"
                                        required/>
                            </div>
                        </div>
                    </div>

                    {{-- Cards 02, 03, 04 — tres columnas ──────────────────────────── --}}
                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-8">

                        {{-- Card 02: Ubicación ─────────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-3">
                            <x-form-style.number-tag number="02" label="Ubicación" />

                            <div class="space-y-3">
                                <div wire:key="province-{{ $this->form->province_id }}">
                                    <x-form-inputs.autocomplete
                                            label="Provincia"
                                            name="province_id"
                                            placeholder="Seleccionar provincia…"
                                            :options="$this->provinces->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                            wire:model.live="form.province_id"
                                            alpine-error="province_id"
                                            :value="$form->province_id"
                                            required/>
                                </div>
                                <div wire:key="region-{{ $this->form->province_id }}-{{ $this->form->region_id }}">
                                    <x-form-inputs.autocomplete
                                            label="Ciudad"
                                            name="region_id"
                                            placeholder="Seleccionar ciudad…"
                                            :options="$this->regions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                            wire:model="form.region_id"
                                            :value="$form->region_id"
                                            alpine-error="region_id"
                                            required/>
                                </div>
                                <x-form-inputs.text_input
                                        label="Cód. Postal"
                                        name="postal_code"
                                        icon="inbox-arrow-down"
                                        placeholder="8300"
                                        maxlength="6"
                                        wire:model="form.postal_code"
                                        alpine-error="postal_code"
                                        required/>
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

                        {{-- Card 03: Contacto ──────────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-3">
                            <x-form-style.number-tag number="03" label="Contacto" />

                            <div class="space-y-3">
                                <x-form-inputs.text_input
                                        label="Teléfono"
                                        name="phone"
                                        type="tel"
                                        icon="phone"
                                        placeholder="299 123 4567"
                                        maxlength="20"
                                        autocomplete="tel"
                                        inputmode="numeric"
                                        wire:model="form.phone"
                                        x-mask="999999999999999"
                                        alpine-error="phone"
                                        required/>
                                <x-form-inputs.text_input
                                        label="Correo Electrónico"
                                        name="email"
                                        type="email"
                                        icon="envelope"
                                        placeholder="sucursal@empresa.com"
                                        maxlength="200"
                                        autocomplete="email"
                                        wire:model="form.email"
                                        alpine-error="email"
                                        class="lowercase"
                                        required/>
                                <x-form-inputs.text_input
                                        label="Web"
                                        name="website"
                                        type="url"
                                        icon="globe-alt"
                                        placeholder="www.sucursal.com"
                                        wire:model="form.website"
                                        class="lowercase"
                                        maxlength="200"/>
                            </div>
                        </div>

                        {{-- Card 04: Logo y Estatus ─────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-2"
                             x-data="{ dragging: false }">
                            <x-form-style.number-tag number="04" label="Logo y Estatus" />

                            <div class="space-y-3">

                                {{-- Logo --}}
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-gray-300">Logo</span>
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
                                            class="group relative h-[7.5rem] w-[7.5rem] cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed bg-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:bg-gray-800/60 dark:focus:ring-sky-400/25"
                                            @click="$refs.logoInput.click()"
                                            role="button"
                                            aria-label="Subir logo de sucursal"
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
                                            <svg class="h-5 w-5 animate-spin text-indigo-500 dark:text-sky-400"
                                                 fill="none"
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
                                    <p class="text-center text-[10px] font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Estado --}}
                                <div>
                                    <x-form-inputs.select
                                            label="Estado"
                                            name="current_status_id"
                                            icon="check-circle"
                                            wire:model="form.current_status_id"
                                            alpine-error="current_status_id"
                                            required>
                                        <option value="">Estado…</option>
                                        @foreach ($this->statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </x-form-inputs.select>
                                </div>

                                {{-- Sucursal principal --}}
                                <div>
                                    <label for="isDefault"
                                           class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200/80 bg-slate-50/80 p-3.5 transition-colors duration-150 hover:border-indigo-200 hover:bg-indigo-50/40 dark:border-gray-700/60 dark:bg-gray-800/40 dark:hover:border-indigo-700/40 dark:hover:bg-indigo-900/10">
                                        <input
                                                id="isDefault"
                                                type="checkbox"
                                                wire:model="form.is_default"
                                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 shadow-sm transition-colors focus:ring-2 focus:ring-indigo-500/30 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-sky-500 dark:focus:ring-sky-500/30"/>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-gray-300">
                                            Sucursal principal
                                        </p>
                                    </label>
                                </div>

                            </div>
                        </div>

                    </div>{{-- /grid 3 columnas --}}
                </div>{{-- /grid principal --}}

                {{-- ── Footer ──────────────────────────────────────────────────────── --}}
                 <x-form-style.footer-button>
                            <x-btn.cancel label="Descartar" wire:click="cancel"/>
                            <x-btn.save
                                    label="Guardar Sucursal"
                                    @click="submit()"
                                    wire-target="adviceBranch"/>
                 </x-form-style.footer-button>
            </div>
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('branchManager', () => ({
        mode: 'create',
        editingCode: '',
        errors: {},

        newBranch() {
            this.mode = 'create';
            this.editingCode = '';
            this.errors = {};
            this.$wire.newBranch();
        },

        selectBranch(id, code) {
            this.mode = 'edit';
            this.editingCode = code;
            this.errors = {};
            this.$wire.selectBranch(id);
        },

        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    phone: this.$wire.form.phone,
                    email: this.$wire.form.email,
                    province_id: this.$wire.form.province_id,
                    region_id: this.$wire.form.region_id,
                    address: this.$wire.form.address,
                    postal_code: this.$wire.form.postal_code,
                    currentStatusId: this.$wire.form.current_status_id,
                },
                {
                    name: ['required', ['minLength', 3]],
                    phone: ['required', ['minLength', 10]],
                    email: ['required', ['email']],
                    province_id: ['required'],
                    region_id: ['required'],
                    address: ['required', ['minLength', 6]],
                    postal_code: ['required', ['minLength', 3]],
                    currentStatusId: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.adviceBranch();
        },
    }));
</script>
@endscript
