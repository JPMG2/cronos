<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Empresa\BranchForm;
use App\Models\Company;
use App\Models\CurrentStatus;
use App\Models\Region;
use App\Models\WorldSettings;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Sucursales')]
class extends Component {
    use HasNotifications;
    use WithFileUploads;

    public BranchForm $form;

    #[Computed]
    public function companyData()
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
    public function regions(): Collection
    {
        return Region::query()
            ->where('province_id', WorldSettings::defaultProvince())
            ->defaultFirst(WorldSettings::defaultRegion())
            ->get();
    }

    public function mount(): void
    {
       $this->form->companyName = $this->companyData->name ?? 'Empresa';
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <div class="flex flex-col lg:flex-row" x-data="branchManager">

            {{-- ══ Panel izquierdo: lista de sucursales ══════════════════════════ --}}
            <div
                    :class="panelOpen ? 'lg:w-64' : 'lg:w-12'"
                    class="shrink-0 overflow-hidden border-b border-indigo-100 bg-indigo-50/60 transition-all duration-300 lg:border-b-0 lg:border-r dark:border-gray-800 dark:bg-gray-900/50">

                {{-- Header del panel --}}
                <div class="flex items-center justify-between border-b border-indigo-100/80 px-4 py-3 dark:border-gray-800">
                    <div x-show="panelOpen" x-transition:enter="transition-opacity duration-200 delay-100"
                         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0">
                        <p class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">Sucursales</p>
                        {{-- wire: se conecta al total de sucursales --}}
                        <p class="text-[11px] text-slate-400 dark:text-gray-600">Gestión de sedes</p>
                    </div>
                    <button
                            @click="panelOpen = !panelOpen"
                            :aria-label="panelOpen ? 'Colapsar panel' : 'Expandir panel'"
                            class="shrink-0 rounded-lg p-1.5 text-slate-400 transition-colors duration-150 hover:bg-indigo-100 hover:text-indigo-600 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-sky-400">
                        <x-menu.heroicon name="bars-3" class="h-4 w-4"/>
                    </button>
                </div>

                {{-- Buscador --}}
                <div x-show="panelOpen" class="px-3 py-2.5">
                    <div class="relative">
                        <x-menu.heroicon
                                name="magnifying-glass"
                                class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 dark:text-gray-600"/>
                        <input
                                type="text"
                                placeholder="Buscar sucursal o ciudad…"
                                x-model="search"
                                class="w-full rounded-lg border border-indigo-200/60 bg-white py-1.5 pl-8 pr-3 text-xs placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400/30 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:placeholder-gray-600 dark:focus:border-sky-500 dark:focus:ring-sky-500/20"/>
                    </div>
                </div>

                {{-- Botón nueva sucursal --}}
                <div x-show="panelOpen" class="px-3 pb-2">
                    <button
                            @click="newBranch()"
                            class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-indigo-200 bg-white py-1.5 text-xs font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60">
                        <x-menu.heroicon name="plus-circle" class="h-3.5 w-3.5"/>
                        Nueva sucursal
                    </button>
                </div>

                {{-- Lista de sucursales ─────────────────── --}}
                {{-- Conectar con @forelse ($this->branches as $branch) --}}
                <div x-show="panelOpen"
                     class="max-h-48 space-y-0.5 overflow-y-auto px-2 pb-3 lg:max-h-[calc(100vh-22rem)]">

                    {{-- Item activo (seleccionado para editar) --}}
                    <div class="flex cursor-pointer items-center gap-2.5 rounded-lg bg-indigo-600 px-2.5 py-2 dark:bg-sky-500/20">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 text-[11px] font-bold text-white dark:bg-sky-400/20 dark:text-sky-200">
                            CC
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1">
                                <p class="truncate text-xs font-bold text-white dark:text-sky-200">Cipolletti Centro</p>
                                <span class="shrink-0 text-[10px] text-amber-300 dark:text-amber-400">★</span>
                            </div>
                            <p class="text-[10px] font-mono text-indigo-200 dark:text-sky-500">CIP-01</p>
                        </div>
                        <span class="shrink-0 rounded-md bg-emerald-400/25 px-1.5 py-0.5 text-[10px] font-bold text-emerald-100 dark:text-emerald-300">ON</span>
                    </div>

                    {{-- Items normales --}}
                    @foreach ([
                        ['initials' => 'NC', 'name' => 'Neuquén Capital', 'code' => 'NQN-02', 'status' => true,  'color' => 'sky'],
                        ['initials' => 'RS', 'name' => 'Roca Sur',        'code' => 'ROC-01', 'status' => true,  'color' => 'amber'],
                        ['initials' => 'PN', 'name' => 'Plottier Norte',  'code' => 'PLO-04', 'status' => false, 'color' => 'rose'],
                        ['initials' => 'C',  'name' => 'Centenario',      'code' => 'CEN-05', 'status' => true,  'color' => 'violet'],
                        ['initials' => 'AC', 'name' => 'Allen Centro',    'code' => 'ALL-06', 'status' => true,  'color' => 'emerald'],
                    ] as $item)
                        <div class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 transition-colors duration-150 hover:bg-indigo-100/80 dark:hover:bg-gray-800/60">
                            <div @class([
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold',
                            'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-400'     => $item['color'] === 'sky',
                            'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400' => $item['color'] === 'amber',
                            'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400'     => $item['color'] === 'rose',
                            'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400' => $item['color'] === 'violet',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' => $item['color'] === 'emerald',
                        ])>
                                {{ $item['initials'] }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-slate-700 dark:text-gray-300">{{ $item['name'] }}</p>
                                <p class="font-mono text-[10px] text-slate-400 dark:text-gray-600">{{ $item['code'] }}</p>
                            </div>
                            @if ($item['status'])
                                <span class="shrink-0 rounded-md bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">ON</span>
                            @else
                                <span class="shrink-0 rounded-md bg-rose-100 px-1.5 py-0.5 text-[10px] font-bold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">OFF</span>
                            @endif
                        </div>
                    @endforeach

                    {{-- Empty state — mostrar cuando no hay sucursales --}}
                    {{--
                    <div class="flex flex-col items-center justify-center px-4 py-10 text-center">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10">
                            <x-menu.heroicon name="building-office" class="h-6 w-6 text-indigo-400 dark:text-indigo-500" />
                        </div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-gray-400">Sin sucursales</p>
                        <p class="mt-0.5 text-[10px] text-slate-400 dark:text-gray-600">Creá la primera arriba</p>
                    </div>
                    --}}
                </div>
            </div>

            {{-- ══ Panel derecho: formulario ═════════════════════════════════════ --}}
            <div class="flex flex-1 flex-col">

                {{-- Header del formulario --}}
                <div class="flex items-start justify-between border-b border-slate-100 px-8 py-4 dark:border-gray-800">
                    <div>
                        <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100"
                            x-text="mode === 'edit' ? 'Editando Sucursal' : 'Crear Nueva Sucursal'">
                            Crear Nueva Sucursal
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400"
                           x-text="mode === 'edit'
                               ? 'Modificá los datos de operación. Los cambios impactan a partir del guardado.'
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

                {{-- Cuerpo del formulario --}}
                <div class="relative z-10 px-8 py-4">
                    <div class="grid grid-cols-1 gap-4">

                        {{-- Card 01: Identificación — full width, campos en fila ─── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-2 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">01</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Identificación</span>
                            </div>
                            <div class="flex flex-col gap-3  lg:flex-row lg:items-start">
                                {{-- Sucursal: campo más grande --}}
                                <div class="flex-[3]">
                                    <x-form-inputs.text_input
                                        label="Sucursal"
                                        name="name"
                                        icon="building-office"
                                        placeholder="Ej: Cipolletti Centro"
                                        maxlength="200"
                                        wire:model="form.name"
                                        class="uppercase"
                                        required />
                                </div>
                                {{-- Empresa: gana espacio del código --}}
                                <div class="flex-[3]">
                                    <x-form-inputs.text_input
                                        label="Empresa"
                                        name="companyName"
                                        icon="building-office-2"
                                        wire:model="form.companyName"
                                        class="uppercase"
                                        :readonly="true" />
                                </div>
                                {{-- Código: ancho mínimo para 7 chars + ícono --}}
                                <div class="w-full lg:w-32 lg:shrink-0">
                                    <x-form-inputs.text_input
                                        label="Código"
                                        name="code"
                                        icon="hashtag"
                                        placeholder="CIP-01"
                                        maxlength="20"
                                        wire:model="form.code"
                                        class="uppercase"
                                        required
                                    />
                                    {{-- Va debajo del input para no romper alineación de labels --}}
                                    {{-- El @error del componente ya reserva su espacio arriba --}}
                                    <p class="mt-1.5 text-[11px] leading-tight text-slate-400 dark:text-gray-600">
                                        Auto-generado.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Cards 02, 03, 04 — tres columnas ────────────────────── --}}
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

                        {{-- Card 02: Contacto ────────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">02</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Contacto</span>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-5 gap-4">
                                    <div class="col-span-2">
                                        <x-form-inputs.text_input
                                                label="Teléfono"
                                                name="phone"
                                                type="tel"
                                                icon="phone"
                                                placeholder="299 123 4567"
                                                maxlength="20"
                                                wire:model="form.phone"
                                                x-mask="999999999999999"
                                                required/>
                                    </div>
                                    <div class="col-span-3">
                                        <x-form-inputs.text_input
                                                label="Web"
                                                name="website"
                                                type="url"
                                                icon="globe-alt"
                                                placeholder="www.sucursal.com"
                                                wire:model="form.website"
                                                maxlength="200"/>
                                    </div>
                                </div>
                                <x-form-inputs.text_input
                                        label="Correo Electrónico"
                                        name="email"
                                        type="email"
                                        icon="envelope"
                                        placeholder="sucursal@empresa.com"
                                        maxlength="200"
                                        wire:model="form.email"
                                        required/>
                            </div>
                        </div>

                        {{-- Card 03: Ubicación ───────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">03</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Ubicación</span>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2">
                                        <x-form-inputs.autocomplete
                                                wire:key="region-branch"
                                                label="Ciudad"
                                                name="regionId"
                                                placeholder="Seleccionar ciudad…"
                                                :options="$this->regions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
                                                wire:model="form.regionId"
                                                required/>
                                    </div>
                                    <x-form-inputs.text_input
                                            label="Cód. Postal"
                                            name="postal_code"
                                            icon="inbox-arrow-down"
                                            placeholder="8300"
                                            maxlength="6"
                                            wire:model="form.postal_code"
                                            required/>
                                </div>
                                <x-form-inputs.text_input
                                        label="Dirección"
                                        name="address"
                                        icon="map"
                                        placeholder="Calle, Altura"
                                        maxlength="200"
                                        wire:model="form.address"
                                        required/>
                            </div>
                        </div>

                        {{-- Card 04: Operación ───────────────────────────────────── --}}
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center gap-2.5">
                                <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">04</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Operación</span>
                            </div>
                            <div class="space-y-4">
                                <x-form-inputs.select
                                        label="Estado"
                                        name="current_status_id"
                                        icon="check-circle"
                                        wire:model="form.current_status_id"
                                        description="Disponibilidad operativa de la sucursal en la plataforma."
                                        required>
                                    <option value="">Estado…</option>
                                    @foreach ($this->statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </x-form-inputs.select>

                                {{-- Toggle: sucursal principal --}}
                                <label for="isDefault"
                                       class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/80 p-3.5 transition-colors duration-150 hover:border-indigo-200 hover:bg-indigo-50/40 dark:border-gray-700/60 dark:bg-gray-800/40 dark:hover:border-indigo-700/40 dark:hover:bg-indigo-900/10">
                                    <input
                                            id="isDefault"
                                            type="checkbox"
                                            wire:model="form.isDefault"
                                            class="mt-0.5 h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 shadow-sm transition-colors focus:ring-2 focus:ring-indigo-500/30 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-sky-500 dark:focus:ring-sky-500/30"/>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-gray-300">
                                            Marcar como sucursal principal
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-600">
                                            Se muestra por defecto en toda la plataforma.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        </div>{{-- /grid 3 columnas --}}
                    </div>{{-- /grid principal --}}

                    {{-- ── Footer ──────────────────────────────────────────────────── --}}
                    <div class="mt-5 border-t border-slate-100 pt-4 dark:border-gray-800">
                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <div class="flex gap-1">
                                <x-feedback.alerts
                                        type="warning"
                                        size="sm"
                                        message="El código interno no podrá ser modificado una vez guardado. Verificá antes de continuar."/>
                            </div>
                            <div class="flex w-full items-center gap-2 sm:w-auto">
                                <x-btn.cancel label="Descartar" wire:click="cancel"/>
                                <x-btn.save
                                        label="Guardar Sucursal"
                                        @click="submit()"
                                        wire-target="saveBranch"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('branchManager', () => ({
        mode: 'create',
        editingCode: '',
        search: '',
        panelOpen: true,
        errors: {},

        newBranch() {
            this.mode = 'create';
            this.editingCode = '';
            this.$wire.newBranch();
        },

        selectBranch(id, code) {
            this.mode = 'edit';
            this.editingCode = code;
            this.$wire.selectBranch(id);
        },

        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    phone: this.$wire.form.phone,
                    email: this.$wire.form.email,
                    regionId: this.$wire.form.regionId,
                    address: this.$wire.form.address,
                    postalCode: this.$wire.form.postalCode,
                    currentStatusId: this.$wire.form.currentStatusId,
                },
                {
                    name: ['required', ['minLength', 3]],
                    phone: ['required', ['minLength', 10]],
                    email: ['required', ['email']],
                    regionId: ['required'],
                    address: ['required', ['minLength', 6]],
                    postalCode: ['required', ['minLength', 3]],
                    currentStatusId: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.saveBranch();
        },
    }));
</script>
@endscript
