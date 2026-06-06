<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\GenderForm;
use App\Models\Gender;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use HasNotifications;

    public GenderForm $form;

    #[Computed]
    public function genders(): Collection
    {
        return Gender::query()->orderBy('name')->get();
    }

    public function create(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->storeGender();
        $this->messageOutPut($message, $type);
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->genders);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function startEdit(int $id): void
    {
        $this->form->fillDataGender($id);
    }

    public function update(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateGender();
        $this->messageOutPut($message, $type);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleActive(int $id): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateGenderActive($id);
        $this->messageOutPut($message, $type);
    }

    public function delete(int $id): never
    {
        Gate::authorize('parametros.update');
        dd('Queda por implementar confirmación');
    }
};
?>

<div class="flex h-full flex-col" x-data="genderForm">

    {{-- ══ FORM ══ --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        {{-- items-start: cada hijo arranca desde arriba · botones con mt-[26px] = bajan al nivel del input --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

            <div class="min-w-0 flex-1">
                <x-form-inputs.text_input
                    label="Nombre del género"
                    name="name"
                    icon="identification"
                    placeholder="Ej: Masculino, Femenino, No binario…"
                    wire:model="form.name"
                    alpineError="name"
                    class="uppercase"
                    size="sm"
                    required/>
            </div>

            {{-- Botones: mt-[26px] los ancla al nivel del input, nunca se mueven con el error --}}
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

                <x-btn.save label="{{ $this->form->editingId ? 'Actualizar' : 'Guardar'  }}" @click="submit()"
                            wire:target="create,update"/>

            </div>

        </div>

    </div>

    {{-- ══ LISTADO — min-h-0 necesario para que overflow-y-auto funcione en flex ══ --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        {{-- Cabecera de columnas --}}
        @if($this->genders->isNotEmpty())
            <x-list.header />
        @endif

        {{-- Filas --}}
        @forelse($this->genders as $gender)
            <div wire:key="gender-{{ $gender->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                        {{ $this->form->editingId === $gender->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Nombre --}}
                <div class="min-w-0 flex-1">
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                        {{ $this->form->editingId === $gender->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $gender->name }}
                    </span>
                </div>

                {{-- Estado + Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">

                    {{-- Badge activo/inactivo — clickeable para toggle --}}
                    <x-list.toggle
                        :active="$gender->is_active"
                        size="sm"
                        wire:click="toggleActive({{ $gender->id }})"
                        wire:loading.class="opacity-50 cursor-wait"
                        wire:target="toggleActive({{ $gender->id }})"
                        aria-label="{{ $gender->is_active ? 'Desactivar' : 'Activar' }} {{ $gender->name }}" />

                    <x-list.divider />

                    <x-list.actions>
                        <x-btn.mini-edit lable="Editar" wire:click="startEdit({{ $gender->id }})"/>
                        <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $gender->id }})"/>
                    </x-list.actions>

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
                    Sin géneros registrados
                </h3>
                <p class="mt-1 font-body text-xs text-slate-500 dark:text-gray-400">
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
