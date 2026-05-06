<?php

declare(strict_types=1);

use App\Models\Gender;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use HasNotifications;

    public string $name = '';
    public ?int $editingId = null;
    public ?int $confirmDeleteId = null;

    #[Computed]
    public function genders(): Collection
    {
        return Gender::query()->orderBy('name')->get();
    }

    public function store(): void
    {
        $this->validate(['name' => 'required|min:2|max:100|unique:genders,name']);

        Gender::query()->create(['name' => $this->name, 'is_active' => true]);
        $this->resetState();
        unset($this->genders);
        $this->getTypeMessage('Género creado correctamente.', 'notifySuccess');
    }

    public function startEdit(int $id): void
    {
        $gender = Gender::query()->findOrFail($id);
        $this->editingId = $id;
        $this->name = $gender->name;
        $this->confirmDeleteId = null;
        $this->resetValidation();
    }

    public function update(): void
    {
        $this->validate(['name' => 'required|min:2|max:100|unique:genders,name,' . $this->editingId]);

        Gender::query()->findOrFail($this->editingId)->update(['name' => $this->name]);
        $this->resetState();
        unset($this->genders);
        $this->getTypeMessage('Género actualizado correctamente.', 'notifySuccess');
    }

    public function cancelEdit(): void
    {
        $this->resetState();
        $this->resetValidation();
    }

    public function toggleActive(int $id): void
    {
        $gender = Gender::query()->findOrFail($id);
        $wasActive = $gender->is_active;
        $gender->update(['is_active' => ! $wasActive]);
        unset($this->genders);
        $this->getTypeMessage(
            $wasActive ? 'Género desactivado.' : 'Género activado.',
            $wasActive ? 'notifyInfo' : 'notifySuccess'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
        $this->editingId = null;
        $this->name = '';
        $this->resetValidation();
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(int $id): void
    {
        Gender::query()->findOrFail($id)->delete();
        $this->resetState();
        unset($this->genders);
        $this->getTypeMessage('Género eliminado correctamente.', 'notifySuccess');
    }

    private function resetState(): void
    {
        $this->name = '';
        $this->editingId = null;
        $this->confirmDeleteId = null;
    }
};
?>

<div class="flex h-full flex-col" x-data="genderForm">

    {{-- ══ FORM — label arriba, input + badge/botón + acciones en la misma fila ══ --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        <label for="gender-name"
               class="mb-1.5 block font-label text-sm font-semibold text-slate-700 dark:text-gray-300">
            Nombre del género <span class="text-rose-500">*</span>
        </label>

        {{-- Fila única: input · badge/botón · cancelar · guardar --}}
        <div class="flex items-center gap-2">

            {{-- Input (escape hatch justificado: layout inline) --}}
            <input id="gender-name"
                   type="text"
                   wire:model="name"
                   placeholder="Ej: Masculino, Femenino, No binario…"
                   class="block min-w-0 flex-1 rounded-xl border border-indigo-200/80 bg-white px-4 py-2.5 text-sm placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-sky-500 dark:focus:ring-sky-400/25" />

            {{-- Badge "Editando" --}}
            <div x-show="$wire.editingId !== null"
                 x-cloak
                 class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                    Editando
                </span>
            </div>

            {{-- Botón "Nueva entrada" --}}
            <button type="button"
                    x-show="$wire.editingId === null"
                    wire:click="cancelEdit"
                    class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-indigo-200 bg-white px-2.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60 sm:flex">
                <x-menu.heroicon name="plus-circle" class="h-3.5 w-3.5" />
                Nueva entrada
            </button>

            {{-- Cancelar (icono compacto) --}}
            <button type="button"
                    wire:click="cancelEdit"
                    class="shrink-0 rounded-lg p-2 text-slate-400 transition-all duration-150 hover:bg-slate-100 hover:text-slate-600 active:scale-[0.98] dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-400"
                    aria-label="Cancelar">
                <x-menu.heroicon name="x-mark" class="h-4 w-4" />
            </button>

            {{-- Guardar / Actualizar --}}
            <button type="button"
                    @click="submit()"
                    wire:loading.attr="disabled"
                    wire:target="store,update"
                    class="flex shrink-0 items-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-150 hover:bg-indigo-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60 dark:bg-sky-500 dark:hover:bg-sky-600">
                <x-menu.heroicon wire:loading.remove wire:target="store,update"
                                 name="document-check" class="h-3.5 w-3.5" />
                <svg wire:loading wire:target="store,update"
                     class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <span wire:loading.remove wire:target="store,update"
                      x-text="$wire.editingId ? 'Actualizar' : 'Guardar'">Guardar</span>
                <span wire:loading wire:target="store,update">Guardando…</span>
            </button>
        </div>

        {{-- Errores --}}
        <template x-if="errors.name">
            <p x-text="errors.name" class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
        </template>
        @error('name')
            <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
        @enderror

    </div>

    {{-- ══ LISTADO — min-h-0 necesario para que overflow-y-auto funcione en flex ══ --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        {{-- Cabecera de columnas --}}
        @if($this->genders->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Nombre
                </span>
                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Estado · Acciones
                </span>
            </div>
            <div class="mx-5 h-px bg-gradient-to-r from-transparent via-indigo-200/60 to-transparent dark:via-indigo-800/40"></div>
        @endif

        {{-- Filas --}}
        @forelse($this->genders as $gender)
            <div class="group flex items-center justify-between px-5 py-3 transition-colors duration-150 hover:bg-white/60 dark:hover:bg-gray-900/40
                        {{ $editingId === $gender->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Nombre --}}
                <div class="min-w-0 flex-1">
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                                 {{ $editingId === $gender->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $gender->name }}
                    </span>
                </div>

                {{-- Estado + Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">

                    {{-- Badge activo/inactivo — clickeable para toggle --}}
                    <button type="button"
                            wire:click="toggleActive({{ $gender->id }})"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="toggleActive({{ $gender->id }})"
                            class="rounded-lg transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $gender->is_active ? 'Desactivar' : 'Activar' }} {{ $gender->name }}">
                        @if($gender->is_active)
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

                    {{-- Separador visual --}}
                    <span class="h-4 w-px bg-slate-200 dark:bg-gray-700"></span>

                    {{-- Editar --}}
                    <button type="button"
                            wire:click="startEdit({{ $gender->id }})"
                            class="rounded-lg p-1.5 text-slate-400 transition-all duration-150 hover:bg-indigo-50 hover:text-indigo-600 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-sky-400 dark:focus:ring-sky-400/40"
                            aria-label="Editar {{ $gender->name }}">
                        <x-menu.heroicon name="pencil" class="h-3.5 w-3.5" />
                    </button>

                    {{-- Eliminar: confirmación inline --}}
                    @if($confirmDeleteId === $gender->id)
                        <div class="flex items-center gap-1"
                             x-data
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <span class="text-[11px] font-semibold text-rose-600 dark:text-rose-400">¿Eliminar?</span>
                            <button type="button"
                                    wire:click="delete({{ $gender->id }})"
                                    class="rounded-lg bg-rose-500 px-2 py-1 text-[11px] font-bold text-white transition-colors duration-150 hover:bg-rose-600 active:scale-95 dark:bg-rose-600 dark:hover:bg-rose-700">
                                Sí
                            </button>
                            <button type="button"
                                    wire:click="cancelDelete"
                                    class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600 transition-colors duration-150 hover:bg-slate-200 active:scale-95 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                No
                            </button>
                        </div>
                    @else
                        <button type="button"
                                wire:click="confirmDelete({{ $gender->id }})"
                                class="rounded-lg p-1.5 text-slate-300 transition-all duration-150 hover:bg-rose-50 hover:text-rose-500 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-rose-400/40 dark:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-rose-400 dark:focus:ring-rose-400/40"
                                aria-label="Eliminar {{ $gender->name }}">
                            <x-menu.heroicon name="trash" class="h-3.5 w-3.5" />
                        </button>
                    @endif
                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            {{-- Empty state — ícono + título + subtítulo (patrón obligatorio) --}}
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="user-circle" class="h-6 w-6" />
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    Sin géneros registrados
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
    Alpine.data('genderForm', () => ({
        errors: {},

        submit() {
            const isEditing = this.$wire.editingId !== null;

            this.errors = validate(
                { name: this.$wire.name },
                { name: ['required', ['minLength', 2]] },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.store();
            }
        },
    }));
</script>
@endscript
