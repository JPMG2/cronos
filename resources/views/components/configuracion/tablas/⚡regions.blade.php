<?php

use App\Livewire\Forms\Configuracion\Parametros\RegionForm;
use App\Models\Province;
use App\Models\Region;
use App\Traits\Livewire\HasNotifications;
use App\Traits\Utilities\WorldConfiguration;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    use HasNotifications;
    use WorldConfiguration;

    public RegionForm $form;

    protected function getCountryIdForFilter(): ?int
    {
        return $this->form->country_id;
    }


    #[Computed(persist: true)]
    public function regions(): Collection
    {
        return Region::query()
            ->with('province')
            ->when($this->form->province_id, fn($q) => $q->where('province_id', $this->form->province_id))
            ->orderBy('name')
            ->get();
    }

    public function countryId(?int $id): void
    {
        $this->form->country_id = ($id && $id > 0) ? $id : null;
        unset($this->provinces);
    }

    public function provinceId(?int $id): void
    {
        $this->form->province_id = ($id && $id > 0) ? $id : null;
        unset($this->regions);
    }

    #[On('updateRegion')]
    public function update(): void
    {
        dd('update');
    }

    #[On('createRegion')]
    public function create(): void
    {
        [$message, $type] = $this->form->storeRegion();
        $this->messageOutPut($message, $type);
    }

    public function cancelRegionEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        dd('Queda por implementar confirmación');
    }

    /**
     * @param  mixed  $message
     * @param  mixed  $type
     * @return void
     */
    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->regions);
        $this->getTypeMessage($message, $type);
        $this->cancelRegionEdit();
    }
};
?>
{{-- ═════════════════════════ TAB: REGIONES ═══════════════════════════════════════ --}}

@placeholder
<div class="animate-pulse">
    {{-- Form skeleton --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">
        <div class="flex flex-col gap-2">
            {{-- Fila 1: País · Provincia --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
                <div class="flex flex-col gap-1.5 sm:w-52 sm:shrink-0">
                    <div class="h-3 w-10 rounded bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-8 w-full rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                </div>
                <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                    <div class="h-3 w-16 rounded bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-8 w-full rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                </div>
            </div>
            {{-- Fila 2: Nombre · Botones --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
                <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                    <div class="h-3 w-24 rounded bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-8 w-full rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                </div>
                <div class="flex shrink-0 items-center gap-2 sm:mt-[26px]">
                    <div class="h-8 w-24 rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-8 w-8 rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- List skeleton --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
        {{-- Header labels --}}
        <div class="flex items-center justify-between px-5 py-2">
            <div class="h-2.5 w-28 rounded bg-slate-200 dark:bg-gray-700"></div>
            <div class="h-2.5 w-28 rounded bg-slate-200 dark:bg-gray-700"></div>
        </div>
        <div
            class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>

        {{-- Rows --}}
        @foreach(range(1, 6) as $i)
            <div class="flex items-center justify-between border-l-2 border-transparent px-5 py-1.5">
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <div class="h-5 w-20 shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-500/15"></div>
                    <div class="h-4 rounded bg-slate-200 dark:bg-gray-700"
                         style="width: {{ [40, 55, 48, 62, 45, 52][$i - 1] }}%"></div>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <div class="h-5 w-14 rounded-lg bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-4 w-px bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-7 w-7 rounded-lg bg-slate-200 dark:bg-gray-700"></div>
                    <div class="h-7 w-7 rounded-lg bg-slate-200 dark:bg-gray-700"></div>
                </div>
            </div>
            @if($i < 6)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif
        @endforeach
    </div>
</div>
@endplaceholder

<div class="flex h-full flex-col"
     @regions-edit-started.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">

    {{-- FORM Región --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">
        <div class="flex flex-col gap-2">

            {{-- Fila 1: País (cascade) · Provincia --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="sm:w-52 sm:shrink-0"
                     wire:key="country-{{ $this->form->country_id }}"
                     @change="$wire.countryId(parseInt($event.target.value) || null)">

                    <x-form-inputs.autocomplete
                        label="País"
                        name="country_id"
                        placeholder="Seleccionar país…"
                        :options="$this->countries->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                        alpine-error="country_id"
                        :value="$form->country_id"
                        size="sm"
                        required/>
                </div>

                <div class="min-w-0 flex-1"
                     wire:key="region-{{ $this->form->country_id ?? 'all' }}"
                     @change="$wire.provinceId(parseInt($event.target.value) || null)"
                >
                    <x-form-inputs.autocomplete
                        label="Provincia"
                        name="province_id"
                        placeholder="Seleccionar provincia…"
                        :options="$this->provinces->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                        alpine-error="province_id"
                        :value="$form->province_id"
                        size="sm"
                        loading="true"
                        loading-target="countryId"
                        required/>
                </div>

            </div>

            {{-- Fila 2: Nombre · Botones --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="min-w-0 flex-1">
                    <x-form-inputs.text_input
                        label="Nombre de la región"
                        name="name"
                        icon="map-pin"
                        placeholder="Ej: Zona Norte"
                        wire:model="form.name"
                        alpineError="name"
                        size="sm"
                        required/>
                </div>

                <div class="flex shrink-0 items-center justify-end gap-2 sm:mt-[26px]">

                    <div x-show="$wire.form.regionId !== null"
                         x-cloak
                         class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        <span
                            class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Editando
                            </span>
                    </div>

                    <x-btn.new-record
                        x-show="$wire.form.regionId === null"
                        @click="cancelRegionEdit"
                        wire:click="cancelRegionEdit"
                        label="Nueva entrada"/>

                   <x-btn.mini-cancel wire:click="cancelRegionEdit" @click="errors = {}"/>

                    <x-btn.save label="{{ $this->form->regionId ? 'Actualizar' : 'Guardar' }}"
                                @click="submitRegion($wire.form.country_id,$wire.form.province_id,$wire.form.regionId,$wire.form.name)"
                                wire:target="create,update"/>
                </div>

            </div>
        </div>
    </div>

    {{-- LISTADO Regiones --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        @if($this->regions->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                    <span
                        class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                        Provincia · Nombre
                    </span>
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                        Estado · Acciones
                    </span>
            </div>
            <div
                class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>
        @endif

        @forelse($this->regions as $region)
            <div wire:key="region-{{ $region->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-blue-50 dark:hover:bg-gray-900/40
                            {{ $this->form->regionId === $region->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                <div class="flex min-w-0 flex-1 items-center gap-2">
                        <span
                            class="max-w-[120px] truncate rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                            {{ $region->province->name ?? '—' }}
                        </span>
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                                     {{ $this->form->regionId === $region->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                            {{ $region->name }}
                        </span>
                </div>

                <div class="flex shrink-0 items-center gap-1.5">

                    <button type="button"
                            wire:click="toggleRegionActive({{ $region->id }})"
                            wire:loading.class="cursor-wait opacity-50"
                            wire:target="toggleRegionActive({{ $region->id }})"
                            class="rounded-lg transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $region->is_active ? 'Desactivar' : 'Activar' }} {{ $region->name }}">
                        @if($region->is_active)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60 transition-colors duration-150 hover:bg-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20 dark:hover:bg-emerald-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                    Activo
                                </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500 ring-1 ring-inset ring-slate-200/60 transition-colors duration-150 hover:bg-slate-200/80 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700 dark:hover:bg-gray-700/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-gray-600"></span>
                                    Inactivo
                                </span>
                        @endif
                    </button>

                    <span class="h-4 w-px bg-slate-200 dark:bg-gray-700"></span>

                    <x-btn.mini-edit lable="Editar" wire:click="startEditRegion({{ $region->id }})"/>
                    <x-btn.mini-delete lable="Eliminar" wire:click="deleteRegion({{ $region->id }})"/>

                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="map-pin" class="h-6 w-6"/>
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    @if($this->form->regionId)
                        Sin regiones para esta provincia
                    @else
                        Sin regiones registradas
                    @endif
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                    @if($this->form->regionId)
                        Creá la primera con el formulario de arriba.
                    @else
                        Seleccioná una provincia y usá el formulario para agregar regiones.
                    @endif
                </p>
            </div>
        @endforelse

    </div>

</div>
