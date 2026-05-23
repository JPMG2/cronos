<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\DegreeForm;
use App\Models\Degree;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use HasNotifications;

    public DegreeForm $form;

    #[Computed]
    public function degrees(): Collection
    {
        return Degree::query()->orderBy('name')->get();
    }

    public function create(): void
    {
        [$message, $type] = $this->form->storeDegree();
        $this->messageOutPut($message, $type);
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->degrees);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function startEdit(int $id): void
    {
        $this->form->fillDegree($id);
    }

    public function update(): void
    {
        [$message, $type] = $this->form->updateDegree();
        $this->messageOutPut($message, $type);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleActive(int $id): void
    {
        [$message, $type] = $this->form->updateDegreeActive($id);
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): never
    {
        dd('Queda por implementar confirmación');
    }
};
?>

<div class="flex h-full flex-col" x-data="degreeForm">

    {{-- ══ FORM ══ --}}
    <div x-ref="degreeForm" class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        <div class="flex flex-col gap-2">

            {{-- Fila 1: Nombre (ancho completo) --}}
            <div>
                <x-form-inputs.text_input
                    label="Título / Grado"
                    name="name"
                    icon="academic-cap"
                    placeholder="Ej: Doctor en Medicina"
                    wire:model="form.name"
                    alpineError="name"
                    size="sm"
                    required/>
            </div>

            {{-- Fila 2: Código · Botones --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="sm:w-40 sm:shrink-0">
                    <x-form-inputs.text_input
                        label="Código"
                        name="code"
                        icon="hashtag"
                        placeholder="DR"
                        maxlength="10"
                        wire:model="form.code"
                        alpineError="code"
                        class="uppercase font-mono font-bold tracking-wider"
                        size="sm"/>
                </div>

                <div class="flex flex-1 items-center justify-end gap-2 sm:mt-[26px]">

                    {{-- Badge "Editando" --}}
                    <div x-show="$wire.form.editingId !== null"
                         x-cloak
                         class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                        <span class="h-1.5 w-1.5 motion-safe:animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        <span
                            class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            Editando
                        </span>
                    </div>

                    {{-- Botón "Nueva entrada" --}}
                    <x-btn.new-record
                        x-show="$wire.form.editingId === null"
                        @click="cancelEdit"
                        wire:click="cancelEdit"
                        label="Nueva entrada"/>

                    {{-- Cancelar (icono compacto) --}}
                    <x-btn.mini-cancel wire:click="cancelEdit"/>

                    <x-btn.save label="{{ $this->form->editingId ? 'Actualizar' : 'Guardar' }}" @click="submit()"
                                wire:target="create,update"/>

                </div>

            </div>

        </div>

    </div>

    {{-- ══ LISTADO ══ --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        {{-- Cabecera de columnas --}}
        @if($this->degrees->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Código · Nombre
                </span>
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Estado · Acciones
                </span>
            </div>
            <div
                class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>
        @endif

        {{-- Filas --}}
        @forelse($this->degrees as $degree)
            <div wire:key="degree-{{ $degree->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                        {{ $this->form->editingId === $degree->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Código badge + Nombre --}}
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    @if($degree->code)
                        <span
                            class="shrink-0 rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                            {{ $degree->code }}
                        </span>
                    @endif
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                        {{ $this->form->editingId === $degree->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $degree->name }}
                    </span>
                </div>

                {{-- Estado + Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">

                    <button type="button"
                            wire:click="toggleActive({{ $degree->id }})"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="toggleActive({{ $degree->id }})"
                            class="rounded-lg transition-colors duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $degree->is_active ? 'Desactivar' : 'Activar' }} {{ $degree->name }}">
                        @if($degree->is_active)
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

                    <x-btn.mini-edit lable="Editar"
                                     @click="$wire.startEdit({{ $degree->id }}); goTopDegree()"/>
                    <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $degree->id }})"/>

                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="academic-cap" class="h-6 w-6"/>
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    Sin títulos registrados
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                    Usá el formulario de arriba para agregar el primero.
                </p>
            </div>
        @endforelse

    </div>

</div>

@script
<script>
    Alpine.data('degreeForm', () => ({
        errors: {},

        cancelEdit() {
            this.$wire.cancelEdit();
            this.errors = {};
        },
        goTopDegree() {
            this.$refs.degreeForm.scrollIntoView({behavior: 'smooth', block: 'start'});
        },
        submit() {
            const isEditing = this.$wire.form.editingId !== null;

            this.errors = validate(
                {name: this.$wire.form.name},
                {name: ['required', ['minLength', 3]]},
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.create();
            }
        },
    }));
</script>
@endscript
