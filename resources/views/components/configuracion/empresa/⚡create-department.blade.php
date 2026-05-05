<?php

declare(strict_types=1);

use App\Dto\Style\ModalConfig;
use App\Enums\Styles\StatusColors;
use App\Livewire\Forms\Configuracion\Empresa\DepartmentForm;
use App\Models\CurrentStatus;
use App\Models\Department;
use App\Models\Sequence;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Departamentos')]
class extends Component {

    use HasNotifications;

    public DepartmentForm $form;

    #[Computed]
    public function sequenceReady(): bool
    {
        $sequence = Sequence::query()->where('entity', 'Departamento')->first();

        return $sequence?->isConfigured() ?? false;
    }

    public function selectDepartment(int $departmentId): void
    {
        $this->form->fillFromDepartment($departmentId);
    }

    public function newDepartment(): void
    {
        $this->resetValidation();
        $this->form->reset();
    }

    public function adviceDepartment(): void
    {
        $this->form->validateDepartment();

        if (is_null($this->form->id)) {
            $msgMessage = 'Confirmá en crear el departamento. El campo código no podrá ser modificado luego de ser generado.';
            $widowAction = 'storeDepartment';
        } else {
            $msgMessage = 'Confirmá en actualizar el departamento.';
            $widowAction = 'updateDepartment';
        }
        $config = new ModalConfig(
            title: 'Confirmar registro',
            message: $msgMessage,
            type: 'info',
            buttons: [
                [
                    'label' => 'Aceptar',
                    'action' => $widowAction,
                    'class' => 'save',
                    'params' => [],
                ]
            ]);
        $this->dispatch('openModal', config: (array) $config);
    }

    #[Computed]
    public function departments(): Collection
    {
        return Department::query()->listDepartment()
            ->statusIds([1, 2])
            ->get();
    }

    #[Computed]
    public function currentStatus(): Collection
    {
        return CurrentStatus::query()->whereIn('id',[1, 2])
            ->get();
    }

    #[On('storeDepartment')]
    public function create(): void
    {
        [$message, $type] = $this->form->createDepartment();
        $this->getTypeMessage($message, $type);
    }

    #[On('updateDepartment')]
    public function update(): void
    {
        [$message, $type] = $this->form->updateDepartment();
        $this->getTypeMessage($message, $type);
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <div x-data="departmentManager">

            {{-- ══ Header ══════════════════════════════════════════════════════════ --}}
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-3 dark:border-gray-800">
                <div>
                    <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100"
                        x-text="mode === 'edit' ? 'Editando Departamento' : 'Nuevo Departamento'">
                        Nuevo Departamento
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400"
                       x-text="mode === 'edit'
                           ? 'Modificá los datos del departamento. Los cambios aplican a todas las sucursales asignadas.'
                           : 'Configurá el departamento para luego asignarlo a las sucursales correspondientes.'">
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

                {{-- Badge: nuevo --}}
                <div x-show="mode === 'create'"
                     class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                    <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                        Nuevo Departamento
                    </span>
                </div>
            </div>

            {{-- ══ Selector de departamento ════════════════════════════════════════ --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-3 dark:border-gray-800 dark:bg-gray-900/30"
                 x-data="{ dropOpen: false, dropSearch: '', selectedLabel: '', get noMatch() { return this.dropSearch !== '' && !Alpine.store('departmentItems').some(i => i.e.includes(this.dropSearch.toLowerCase()) || i.p.includes(this.dropSearch.toLowerCase())); } }"
                 @keydown.escape.window="dropOpen = false">

                <div class="mb-1.5 flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="building-library" class="h-4 w-4"/>
                    </div>
                    <span class="font-headline text-sm font-bold text-slate-700 dark:text-gray-300">Seleccionar departamento</span>
                    @if($this->departments->count() > 0)
                        <span class="inline-flex items-center rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                            {{ $this->departments->count() }} {{ $this->departments->count() === 1 ? 'departamento' : 'departamentos' }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">

                    {{-- Combobox --}}
                    <div class="relative flex-1" @click.outside="dropOpen = false">
                        <div class="relative">
                            <x-menu.heroicon
                                    name="magnifying-glass"
                                    class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-gray-500"/>
                            <input
                                    type="text"
                                    :value="dropOpen ? dropSearch : selectedLabel"
                                    @focus="dropOpen = true; dropSearch = ''"
                                    @input="dropSearch = $event.target.value; dropOpen = true"
                                    :placeholder="selectedLabel && !dropOpen ? '' : 'Buscar departamento por nombre o código…'"
                                    autocomplete="off"
                                    role="combobox"
                                    :aria-expanded="dropOpen"
                                    aria-haspopup="listbox"
                                    class="w-full rounded-xl border border-indigo-200/80 bg-white py-2.5 pl-10 pr-10 text-sm placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-sky-500 dark:focus:ring-sky-400/25"/>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 transition-transform duration-200"
                                  :class="dropOpen ? 'rotate-180' : ''">
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
                                class="absolute z-30 mt-1.5 max-h-60 w-full overflow-y-auto rounded-xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/60 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/40"
                                style="display: none"
                                role="listbox">

                            @forelse($this->departments as $department)
                                @php
                                    $initials   = $department->name ? strtoupper(substr($department->name, 0, 2)) : '??';
                                    $statusEnum = StatusColors::tryFrom($department->currentStatus?->name ?? '');
                                @endphp

                                <button
                                        type="button"
                                        x-show="dropSearch === ''
                                            || '{{ strtolower($department->name) }}'.includes(dropSearch.toLowerCase())
                                            || '{{ strtolower($department->code) }}'.includes(dropSearch.toLowerCase())"
                                        @click="selectDepartment({{ $department->id }}, '{{ $department->code }}'); selectedLabel = '{{ addslashes(mb_strtoupper($department->name)) }}'; dropOpen = false; dropSearch = ''"
                                        class="group flex w-full items-center gap-3 border-b border-slate-100/80 px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-indigo-50/80 dark:border-gray-800 dark:hover:bg-gray-800/60"
                                        :class="editingCode === '{{ $department->code }}' ? 'bg-indigo-50/60 dark:bg-indigo-500/10' : ''"
                                        role="option"
                                        :aria-selected="editingCode === '{{ $department->code }}'">

                                    {{-- Avatar --}}
                                    <x-form-style.avatar :colorInt="$loop->index">
                                        {{ $initials }}
                                    </x-form-style.avatar>

                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="break-words text-sm font-semibold leading-snug text-slate-800 group-hover:text-indigo-700 dark:text-gray-100 dark:group-hover:text-sky-300">
                                            {{ mb_strtoupper($department->name) }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">
                                            #{{ mb_strtoupper($department->code) }}
                                        </p>
                                    </div>

                                    {{-- Status badge --}}
                                    @if($statusEnum)
                                        <x-form-style.badge x-cloak
                                                            class="{{ $statusEnum->dotClass() }}">{{ $statusEnum->label() }}</x-form-style.badge>
                                    @endif

                                    {{-- Check activo --}}
                                    <span x-show="editingCode === '{{ $department->code }}'"
                                          class="ml-1 shrink-0 text-indigo-600 dark:text-sky-400">
                                        <x-menu.heroicon name="check-circle" class="h-4 w-4"/>
                                    </span>
                                </button>

                            @empty
                                <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
                                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10">
                                        <x-menu.heroicon name="building-library"
                                                         class="h-5 w-5 text-indigo-400 dark:text-indigo-500"/>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-400">Sin departamentos aún</p>
                                    <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-600">Creá el primero con el botón.</p>
                                </div>
                            @endforelse

                            <div x-show="noMatch" x-cloak
                                 class="flex flex-col items-center justify-center px-4 py-8 text-center">
                                <x-nomatch/>
                            </div>

                        </div>
                    </div>

                    {{-- Botón nuevo departamento --}}
                    <button
                            @click="newDepartment(); selectedLabel = ''; dropOpen = false; dropSearch = ''"
                            class="flex shrink-0 items-center gap-2 rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60">
                        <x-menu.heroicon name="plus-circle" class="h-4 w-4"/>
                        Nuevo departamento
                    </button>
                </div>

            </div>{{-- /selector --}}

            {{-- ══ Formulario ══════════════════════════════════════════════════════ --}}
            <div class="relative z-10 px-6 py-3">

                {{-- Card único ───────────────────────────────────────────────── --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-3">

                        {{-- Fila 1: Nombre + Código + Estado --}}
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start">

                            <div class="flex-1">
                                <x-form-inputs.text_input
                                        label="Departamento"
                                        name="name"
                                        icon="building-library"
                                        placeholder="Ej: Laboratorio"
                                        maxlength="150"
                                        class="uppercase"
                                        wire:model="form.name"
                                        alpine-error="name"
                                        required/>
                            </div>

                            <div class="w-full lg:w-44 lg:shrink-0">
                                <x-form-inputs.text_input
                                        label="Código"
                                        name="code"
                                        icon="hashtag"
                                        maxlength="20"
                                        description="Auto-generado."
                                        class="uppercase"
                                        :readonly="true"
                                        wire:model="form.code"
                                        required/>
                            </div>

                            <div class="w-full lg:w-44 lg:shrink-0">
                                <x-form-inputs.select
                                        label="Estado"
                                        name="current_status_id"
                                        icon="check-circle"
                                        wire:model="form.current_status_id"
                                        alpine-error="current_status_id"
                                        required>
                                    <option value="">Estado…</option>
                                     @foreach ($this->currentStatus as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                     @endforeach
                                </x-form-inputs.select>
                            </div>

                        </div>

                        {{-- Fila 2: Descripción --}}
                        <div class="border-t border-slate-100 pt-3 dark:border-gray-800">
                            <div class="mb-1.5 flex items-center gap-2">
                                <span class="text-sm font-semibold text-slate-700 dark:text-gray-300">Descripción</span>
                                <span class="text-xs text-slate-400 dark:text-gray-500">(opcional)</span>
                            </div>
                            <x-form-inputs.textarea
                                    name="description"
                                    placeholder="Breve descripción del departamento y sus funciones…"
                                    wire-model="form.description"
                                    :rows="2"/>
                        </div>

                    </div>
                </div>

                {{-- Alerta secuencia no configurada --}}
                @if(!$this->sequenceReady)
                    <div class="mt-3">
                        <x-feedback.alerts type="warning" size="sm"
                            message="La secuencia para Departamentos no está configurada o le faltan datos. Completá la configuración en Parámetros → Secuencias antes de crear registros."/>
                    </div>
                @endif

                {{-- Footer --}}
                <x-form-style.footer-button>
                    <x-btn.cancel label="Descartar" x-on:click="cancel"/>
                    <x-btn.save label="Guardar Departamento" @click="submit()"
                                wire-taget="adviceDepartment"
                                :disabled="!$this->sequenceReady"/>
                </x-form-style.footer-button>

            </div>
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.store('departmentItems', @json($this->departments->map(fn($d) => ['e' => strtolower($d->name), 'p' => strtolower($d->code)])->values()));

    Alpine.data('departmentManager', () => ({
        mode: 'create',
        editingCode: '',
        errors: {},

        newDepartment() {
            this.mode = 'create';
            this.editingCode = '';
            this.errors = {};
            this.$wire.newDepartment();
        },

        cancel() {
            this.mode = 'create';
            this.editingCode = '';
            this.errors = {};
        },

        selectDepartment(id, code) {
            this.mode = 'edit';
            this.editingCode = code;
            this.errors = {};
            this.$wire.selectDepartment(id);
        },

        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    current_status_id: this.$wire.form.current_status_id,
                },
                {
                    name: ['required', ['minLength', 3]],
                    current_status_id: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.adviceDepartment();
        },
    }));
</script>
@endscript
