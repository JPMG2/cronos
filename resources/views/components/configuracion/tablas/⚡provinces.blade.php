<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\ProvincesForm;
use App\Traits\Livewire\HasNotifications;
use App\Traits\Utilities\WorldConfiguration;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    use HasNotifications;
    use WorldConfiguration;

    public ProvincesForm $form;

    protected function getCountryIdForFilter(): ?int
    {
        return $this->form->country_id;
    }

    public function countryId(?int $id): void
    {
        $this->form->country_id = ($id && $id > 0) ? $id : null;

        unset($this->provinces);
    }

    public function toggleProvinceActive(int $id): void
    {
        [$message, $type] = $this->form->updateStatusProvinces($id);
        $this->messageOutPut($message, $type);
    }

    public function startEditProvince(int $id): void
    {
        $this->form->fillProvincesData($id);
    }

    public function cancelProvinceEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    #[On('createProvinces')]
    public function create(): void
    {
        [$message, $type] = $this->form->storeProvinces();
        $this->messageOutPut($message, $type);
    }

    #[On('updateProvinces')]
    public function update(): void
    {
        [$message, $type] = $this->form->updateProvinces();
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): never
    {
        dd('Queda por implementar confirmación');
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->provinces);
        $this->getTypeMessage($message, $type);
        $this->cancelProvinceEdit();
    }
};
?>

{{-- ═════════════════════════ TAB: PROVINCIAS ═══════════════════════════════════════ --}}

@placeholder
<div class="motion-safe:animate-pulse">
    {{-- Form skeleton --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
            {{-- Select país --}}
            <div class="flex flex-col gap-1.5 sm:w-52 sm:shrink-0">
                <div class="h-3 w-10 rounded bg-slate-200 dark:bg-gray-700"></div>
                <div class="h-8 w-full rounded-xl bg-slate-200 dark:bg-gray-700"></div>
            </div>
            {{-- Input provincia --}}
            <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                <div class="h-3 w-16 rounded bg-slate-200 dark:bg-gray-700"></div>
                <div class="h-8 w-full rounded-xl bg-slate-200 dark:bg-gray-700"></div>
            </div>
            {{-- Buttons --}}
            <div class="flex shrink-0 items-center gap-2 sm:mt-[26px]">
                <div class="h-8 w-24 rounded-xl bg-slate-200 dark:bg-gray-700"></div>
                <div class="h-8 w-8 rounded-xl bg-slate-200 dark:bg-gray-700"></div>
            </div>
        </div>
    </div>

    {{-- List skeleton --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
        {{-- Header labels --}}
        <div class="flex items-center justify-between px-5 py-2">
            <div class="h-2.5 w-24 rounded bg-slate-200 dark:bg-gray-700"></div>
            <div class="h-2.5 w-28 rounded bg-slate-200 dark:bg-gray-700"></div>
        </div>
        <div class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>

        {{-- Rows --}}
        @foreach(range(1, 6) as $i)
            <div class="flex items-center justify-between border-l-2 border-transparent px-5 py-1.5">
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <div class="h-5 w-10 shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-500/15"></div>
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

<div class="flex h-full flex-col" @province-edit-started.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">

    {{-- FORM Provincia --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">
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
                    required />
            </div>

            <div class="min-w-0 flex-1">
                <x-form-inputs.text_input
                    label="Provincia"
                    name="name"
                    icon="map-pin"
                    placeholder="Ej: Buenos Aires"
                    wire:model="form.name"
                    alpine-error="name"
                    size="sm"
                    required />
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2 sm:mt-[26px]">

                <div x-show="$wire.form.provinceId !== null"
                     x-cloak
                     class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                    <span class="h-1.5 w-1.5 motion-safe:animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                    <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        Editando
                    </span>
                </div>

                <x-btn.new-record
                    x-show="$wire.form.provinceId === null"
                    @click="errors = {}"
                    wire:click="cancelProvinceEdit"
                    label="Nueva entrada" />

                <x-btn.mini-cancel wire:click="cancelProvinceEdit" @click="errors = {}" />

                <x-btn.save label="{{ $this->form->provinceId ? 'Actualizar' : 'Guardar' }}"
                            wire:target="create,update"
                            @click="submitProvince($wire.form.country_id, $wire.form.name, $wire.form.provinceId)" />

            </div>

        </div>
    </div>

    {{-- LISTADO Provincias --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        @if($this->provinces->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    País · Nombre
                </span>
                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Estado · Acciones
                </span>
            </div>
            <div class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>
        @endif

        @forelse($this->provinces as $province)
            <div wire:key="province-{{ $province->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-blue-50 dark:hover:bg-gray-900/40
                            {{ $this->form->provinceId === $province->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <span class="shrink-0 rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                        {{ $province->country->code ?? '-' }}
                    </span>
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                                 {{ $this->form->provinceId === $province->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $province->name }}
                    </span>
                </div>

                <div class="flex shrink-0 items-center gap-1.5">

                    <button type="button"
                            wire:click="toggleProvinceActive({{ $province->id }})"
                            wire:loading.class="cursor-wait opacity-50"
                            wire:target="toggleProvinceActive({{ $province->id }})"
                            class="rounded-lg transition-colors duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $province->is_active ? 'Desactivar' : 'Activar' }} {{ $province->name }}">
                        @if($province->is_active)
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

                    <x-btn.mini-edit
                        lable="Editar"
                        wire:click="startEditProvince({{ $province->id }})"
                        @click="$dispatch('province-edit-started')" />

                    <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $province->id }})" />

                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="building-library" class="h-6 w-6" />
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    @if($this->form->country_id)
                        Sin provincias para este país
                    @else
                        Sin provincias registradas
                    @endif
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                    @if($this->form->country_id)
                        Creá la primera con el formulario de arriba.
                    @else
                        Seleccioná un país y usá el formulario para agregar provincias.
                    @endif
                </p>
            </div>
        @endforelse

    </div>
</div>
