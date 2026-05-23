<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\MaritalStatusForm;
use App\Models\MaritalStatus;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use HasNotifications;

    public MaritalStatusForm $form;

    #[Computed]
    public function maritalStatus(): Collection
    {
        return MaritalStatus::query()->orderBy('name')->get();
    }

    public function create(): void
    {
        [$message, $type] = $this->form->storeMaritalStatus();
        $this->messageOutPut($message, $type);
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->maritalStatus);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function update(): void
    {
        [$message, $type] = $this->form->updateMaritalStatus();
        $this->messageOutPut($message, $type);
    }

    public function startEdit(int $id): void
    {
        $this->form->fillMaritalStatus($id);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleActive(int $id): void
    {
        [$message, $type] = $this->form->updateMaritalStatusActive($id);
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): never
    {
        dd('Qeuda por implementar confirmación');
    }

};
?>

<div class="flex h-full flex-col" x-data="maritalStatus">

    {{-- ══ FORM ══ --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

            <div class="min-w-0 flex-1">
                <x-form-inputs.text_input
                    label="Nombre del estado civil"
                    name="name"
                    icon="identification"
                    placeholder="Ej: Soltero, Casado, Divorciado…"
                    wire:model="form.name"
                    alpineError="name"
                    size="sm"
                    required/>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2 sm:mt-[26px]">

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

    {{-- ══ LISTADO — min-h-0 necesario para que overflow-y-auto funcione en flex ══ --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        {{-- Cabecera de columnas --}}
        @if($this->maritalStatus->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Nombre
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
        @forelse($this->maritalStatus as $maritalStatu)
            <div wire:key="marital-status-{{ $maritalStatu->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                        {{ $this->form->editingId === $maritalStatu->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Nombre --}}
                <div class="min-w-0 flex-1">
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                        {{ $this->form->editingId === $maritalStatu->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $maritalStatu->name }}
                    </span>
                </div>

                {{-- Estado + Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">

                    {{-- Badge activo/inactivo — clickeable para toggle --}}
                    <button type="button"
                            wire:click="toggleActive({{ $maritalStatu->id }})"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="toggleActive({{ $maritalStatu->id }})"
                            class="rounded-lg transition-colors duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $maritalStatu->is_active ? 'Desactivar' : 'Activar' }} {{ $maritalStatu->name }}">
                        @if($maritalStatu->is_active)
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

                    {{-- Separador visual --}}
                    <span class="h-4 w-px bg-slate-200 dark:bg-gray-700"></span>

                    <x-btn.mini-edit lable="Editar" wire:click="startEdit({{ $maritalStatu->id }})"/>
                    <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $maritalStatu->id }})"/>

                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            {{-- Empty state — ícono + título + subtítulo (patrón obligatorio) --}}
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="user-circle" class="h-6 w-6"/>
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    Sin estados civiles registrados
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
    Alpine.data('maritalStatus', () => ({
        errors: {},

        cancelEdit() {
            this.$wire.cancelEdit();
            this.errors = {};
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
