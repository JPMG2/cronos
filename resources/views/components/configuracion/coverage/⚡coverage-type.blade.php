<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Coverage\CoverageTypeForm;
use App\Models\CoverageType;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tipos de Cobertura')]
class extends Component {
    use HasNotifications;

    public CoverageTypeForm $form;

    #[Computed]
    public function coverageTypes(): Collection
    {
        return CoverageType::query()->orderBy('name')->get();
    }

    public function create(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->storeCoverageType();
        $this->messageOutPut($message, $type);
    }

    public function update(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateCoverageType();
        $this->messageOutPut($message, $type);
    }

    public function startEdit(int $id): void
    {
        $this->form->fillCoverageType($id);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->coverageTypes);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function toggleActive(int $id): void
    {
        Gate::authorize('parametros.update');
        $coverageType = CoverageType::query()->findOrFail($id);
        $coverageType->update(['is_active' => ! $coverageType->is_active]);
        unset($this->coverageTypes);
    }

    public function toggleRequiresInsuranceData(int $id): void
    {
        Gate::authorize('parametros.update');
        $coverageType = CoverageType::query()->findOrFail($id);
        $coverageType->update(['requires_insurance_data' => ! $coverageType->requires_insurance_data]);
        unset($this->coverageTypes);
    }

    public function delete(int $id): never
    {
        Gate::authorize('parametros.update');
        dd('Queda por implementar confirmación');
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>

        <x-form-style.header-form
            title="Tipos de Cobertura"
            description="Definí los tipos de cobertura médica disponibles en el sistema."
            sign="Coberturas"/>

        <div class="flex h-full flex-col" x-data="coverageTypeForm">

            {{-- ══ FORM — código · nombre · descripción · toggles ══ --}}
            <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

                <div class="flex flex-col gap-2">

                    {{-- Fila 1: Código · Nombre · Requiere datos · Activo --}}
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end">

                        <div class="sm:w-40 sm:shrink-0">
                            <x-form-inputs.text_input
                                label="Código"
                                name="code"
                                icon="hashtag"
                                placeholder="OS"
                                maxlength="30"
                                wire:model="form.code"
                                alpineError="code"
                                class="uppercase font-mono font-bold tracking-wider"
                                size="sm"
                                required/>
                        </div>

                        <div class="flex-1">
                            <x-form-inputs.text_input
                                label="Nombre"
                                name="name"
                                icon="identification"
                                placeholder="Ej: Obra Social"
                                wire:model="form.name"
                                alpineError="name"
                                class="uppercase font-semibold"
                                size="sm"
                                required/>
                        </div>

                        <div class="flex shrink-0 items-center gap-2 pb-0.5">

                            <button type="button"
                                    @click="$wire.form.requiresInsuranceData = !$wire.form.requiresInsuranceData"
                                    :class="$wire.form.requiresInsuranceData
                                        ? 'bg-teal-100 text-teal-700 ring-teal-200/60 dark:bg-teal-500/10 dark:text-teal-400 dark:ring-teal-500/20'
                                        : 'bg-slate-100 text-slate-500 ring-slate-200/60 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700'"
                                    class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 active:scale-95">
                                <span class="h-1.5 w-1.5 rounded-full transition-colors"
                                      :class="$wire.form.requiresInsuranceData
                                          ? 'bg-teal-500 dark:bg-teal-400'
                                          : 'bg-slate-400 dark:bg-gray-600'"></span>
                                Requiere datos
                            </button>

                            <button type="button"
                                    @click="$wire.form.isActive = !$wire.form.isActive"
                                    :class="$wire.form.isActive
                                        ? 'bg-emerald-100 text-emerald-700 ring-emerald-200/60 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20'
                                        : 'bg-slate-100 text-slate-500 ring-slate-200/60 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700'"
                                    class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 active:scale-95">
                                <span class="h-1.5 w-1.5 rounded-full transition-colors"
                                      :class="$wire.form.isActive
                                          ? 'bg-emerald-500 dark:bg-emerald-400'
                                          : 'bg-slate-400 dark:bg-gray-600'"></span>
                                Activo
                            </button>

                        </div>

                    </div>

                    {{-- Fila 2: Descripción --}}
                    <x-form-inputs.text_input
                        label="Descripción"
                        name="description"
                        icon="document-text"
                        placeholder="Descripción breve del tipo de cobertura…"
                        wire:model="form.description"
                        alpineError="description"
                        size="sm"/>

                </div>

            </div>

            {{-- ══ FOOTER — botones de acción ══ --}}
            <x-form-style.footer-button>

                <div x-show="$wire.form.editingId !== null"
                     x-cloak
                     class="me-auto hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                    <span class="h-1.5 w-1.5 motion-safe:animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                    <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        Editando
                    </span>
                </div>

                <x-btn.new-record
                    x-show="$wire.form.editingId === null"
                    @click="cancelEdit"
                    wire:click="cancelEdit"
                    label="Nueva entrada"/>

                <x-btn.cancel wire:click="cancelEdit"/>

                <x-btn.save label="{{ $this->form->editingId ? 'Actualizar' : 'Guardar' }}" @click="submit()"
                            wire:target="create,update"/>

            </x-form-style.footer-button>

            {{-- ══ LISTADO ══ --}}
            <div class="flex min-h-0 flex-1 flex-col overflow-y-auto mb-4">

                @if($this->coverageTypes->isNotEmpty())
                    <x-list.header :code="true" extra="Descripción" :toggles="['Req. datos']" color="teal" />
                @endif

                @forelse($this->coverageTypes as $coverageType)
                    <div wire:key="coverage-type-{{ $coverageType->id }}"
                         class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                                {{ $this->form->editingId === $coverageType->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                        {{-- Código badge + Nombre + Descripción --}}
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <span class="shrink-0 min-w-[52px] text-center rounded-lg bg-teal-100 px-2 py-0.5 font-mono text-[11px] font-bold uppercase tracking-wider text-teal-700 dark:bg-teal-500/15 dark:text-teal-300">
                                {{ $coverageType->code }}
                            </span>
                            <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                                {{ $this->form->editingId === $coverageType->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                                {{ $coverageType->name }}
                            </span>
                            @if($coverageType->description)
                                <span class="hidden truncate text-xs text-slate-400 dark:text-gray-500 sm:block">
                                    {{ \Illuminate\Support\Str::limit($coverageType->description, 40) }}
                                </span>
                            @endif
                        </div>

                        {{-- Requiere Datos · Estado · Acciones --}}
                        <div class="flex shrink-0 items-center gap-1.5">

                            <x-list.toggle
                                :active="$coverageType->requires_insurance_data"
                                active-label="Req. datos"
                                inactive-label="Particular"
                                active-color="teal"
                                inactive-color="violet"
                                wire:click="toggleRequiresInsuranceData({{ $coverageType->id }})"
                                wire:loading.class="opacity-50 cursor-wait"
                                wire:target="toggleRequiresInsuranceData({{ $coverageType->id }})"
                                aria-label="{{ $coverageType->requires_insurance_data ? 'Sin datos requeridos' : 'Requiere datos' }} {{ $coverageType->name }}" />

                            <x-list.toggle
                                :active="$coverageType->is_active"
                                size="sm"
                                wire:click="toggleActive({{ $coverageType->id }})"
                                wire:loading.class="opacity-50 cursor-wait"
                                wire:target="toggleActive({{ $coverageType->id }})"
                                aria-label="{{ $coverageType->is_active ? 'Desactivar' : 'Activar' }} {{ $coverageType->name }}" />

                            <x-list.divider />

                            <x-list.actions>
                                <x-btn.mini-edit lable="Editar" wire:click="startEdit({{ $coverageType->id }})"/>
                                <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $coverageType->id }})"/>
                            </x-list.actions>

                        </div>

                    </div>

                    @if(! $loop->last)
                        <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
                    @endif

                @empty
                    <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-100 text-teal-400 dark:bg-teal-500/15 dark:text-teal-400">
                            <x-menu.heroicon name="shield-check" class="h-6 w-6"/>
                        </div>
                        <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                            Sin tipos de cobertura registrados
                        </h3>
                        <p class="mt-1 font-body text-xs text-slate-500 dark:text-gray-400">
                            Usá el formulario de arriba para agregar el primero.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>

    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('coverageTypeForm', () => ({
        errors: {},

        cancelEdit() {
            this.$wire.cancelEdit();
            this.errors = {};
        },
        submit() {
            const isEditing = this.$wire.form.editingId !== null;

            this.errors = validate(
                {
                    code: this.$wire.form.code,
                    name: this.$wire.form.name,
                },
                {
                    code: ['required', ['minLength', 2]],
                    name: ['required', ['minLength', 3]],
                },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.create();
            }
        },
    }));
</script>
@endscript
