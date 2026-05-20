<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\BloodTypeForm;
use App\Models\BloodType;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use HasNotifications;

    public BloodTypeForm $form;

    #[Computed]
    public function bloodTypes(): Collection
    {
        return BloodType::query()->orderBy('code')->get();
    }

    public function create(): void
    {
        [$message, $type] = $this->form->storeBloodType();
        $this->messageOutPut($message, $type);
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->bloodTypes);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function startEdit(int $id): void
    {
        $this->form->fillBloodType($id);
    }

    public function update(): void
    {
        [$message, $type] = $this->form->updateBloodType();
        $this->messageOutPut($message, $type);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function delete(int $id): never
    {
        dd('Queda por implementar confirmación');
    }
};
?>

<div class="flex h-full flex-col" x-data="bloodTypeForm">

    {{-- ══ FORM — código · nombre · grupo ABO · factor Rh · compatibilidades · universales ══ --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        <div class="flex flex-col gap-2">

            {{-- Fila 1: Código · Nombre --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="sm:w-24 sm:shrink-0">
                    <x-form-inputs.text_input
                        label="Código"
                        name="code"
                        icon="hashtag"
                        placeholder="A+"
                        maxlength="5"
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
                        placeholder="Ej: A positivo"
                        wire:model="form.name"
                        alpineError="name"
                        size="sm"
                        required/>
                </div>

            </div>

            {{-- Fila 2: Grupo ABO · Factor Rh · Puede donar a · Puede recibir de --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="sm:w-28 sm:shrink-0">
                    <x-form-inputs.select
                        label="Grupo ABO"
                        name="aboGroup"
                        wire:model="form.aboGroup"
                        alpineError="aboGroup"
                        size="sm"
                        required>
                        <option value="">— grupo —</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </x-form-inputs.select>
                </div>

                <div class="sm:w-32 sm:shrink-0">
                    <x-form-inputs.select
                        label="Factor Rh"
                        name="rhFactor"
                        wire:model="form.rhFactor"
                        alpineError="rhFactor"
                        size="sm"
                        required>
                        <option value="">— factor —</option>
                        <option value="+">Positivo (+)</option>
                        <option value="-">Negativo (−)</option>
                    </x-form-inputs.select>
                </div>

                <div class="flex-1">
                    <x-form-inputs.text_input
                        label="Puede donar a"
                        name="canDonateTo"
                        icon="arrow-right-circle"
                        placeholder="A+, AB+"
                        wire:model="form.canDonateTo"
                        alpineError="canDonateTo"
                        size="sm"
                        required/>
                </div>

                <div class="flex-1">
                    <x-form-inputs.text_input
                        label="Puede recibir de"
                        name="canReceiveFrom"
                        icon="arrow-left-circle"
                        placeholder="A+, A−, O+, O−"
                        wire:model="form.canReceiveFrom"
                        alpineError="canReceiveFrom"
                        size="sm"
                        required/>
                </div>

            </div>

            {{-- Fila 3: Toggles universales · Botones --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">

                {{-- Donante / receptor universal --}}
                <div class="flex items-center gap-2">

                    <button type="button"
                            @click="$wire.form.isUniversalDonor = !$wire.form.isUniversalDonor"
                            :class="$wire.form.isUniversalDonor
                                ? 'bg-rose-100 text-rose-700 ring-rose-200/60 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20'
                                : 'bg-slate-100 text-slate-500 ring-slate-200/60 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700'"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 active:scale-95">
                        <span class="h-1.5 w-1.5 rounded-full transition-colors"
                              :class="$wire.form.isUniversalDonor
                                  ? 'bg-rose-500 dark:bg-rose-400'
                                  : 'bg-slate-400 dark:bg-gray-600'"></span>
                        Donante univ.
                    </button>

                    <button type="button"
                            @click="$wire.form.isUniversalRecipient = !$wire.form.isUniversalRecipient"
                            :class="$wire.form.isUniversalRecipient
                                ? 'bg-violet-100 text-violet-700 ring-violet-200/60 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20'
                                : 'bg-slate-100 text-slate-500 ring-slate-200/60 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700'"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 active:scale-95">
                        <span class="h-1.5 w-1.5 rounded-full transition-colors"
                              :class="$wire.form.isUniversalRecipient
                                  ? 'bg-violet-500 dark:bg-violet-400'
                                  : 'bg-slate-400 dark:bg-gray-600'"></span>
                        Receptor univ.
                    </button>

                </div>

                <div class="flex flex-1 items-center justify-end gap-2">

                    {{-- Badge "Editando" --}}
                    <div x-show="$wire.form.editingId !== null"
                         x-cloak
                         class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        <span
                            class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            Editando
                        </span>
                    </div>

                    <x-btn.new-record
                        x-show="$wire.form.editingId === null"
                        @click="cancelEdit"
                        wire:click="cancelEdit"
                        label="Nueva entrada"/>

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
        @if($this->bloodTypes->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-2">
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Código · Nombre
                </span>
                <span
                    class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                    Acciones
                </span>
            </div>
            <div
                class="mx-5 h-px bg-gradient-to-r from-transparent via-rose-200/60 to-transparent dark:via-rose-800/40"></div>
        @endif

        {{-- Filas --}}
        @forelse($this->bloodTypes as $bloodType)
            <div wire:key="blood-type-{{ $bloodType->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-blue-50 dark:hover:bg-gray-900/40
                        {{ $this->form->editingId === $bloodType->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Código badge + Nombre + Universales --}}
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <span
                        class="shrink-0 rounded-lg bg-rose-100 px-2 py-0.5 font-mono text-[11px] font-bold uppercase tracking-wider text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">
                        {{ $bloodType->code }}
                    </span>
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200
                        {{ $this->form->editingId === $bloodType->id ? 'text-amber-700 dark:text-amber-300' : '' }}">
                        {{ $bloodType->name }}
                    </span>
                    @if($bloodType->is_universal_donor)
                        <span
                            class="hidden shrink-0 items-center gap-1 rounded-lg bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-600 ring-1 ring-inset ring-rose-200/60 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20 sm:inline-flex">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500 dark:bg-rose-400"></span>
                            Donante univ.
                        </span>
                    @endif
                    @if($bloodType->is_universal_recipient)
                        <span
                            class="hidden shrink-0 items-center gap-1 rounded-lg bg-violet-100 px-2 py-0.5 text-[10px] font-semibold text-violet-600 ring-1 ring-inset ring-violet-200/60 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20 sm:inline-flex">
                            <span class="h-1.5 w-1.5 rounded-full bg-violet-500 dark:bg-violet-400"></span>
                            Receptor univ.
                        </span>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-btn.mini-edit lable="Editar" wire:click="startEdit({{ $bloodType->id }})"/>
                    <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $bloodType->id }})"/>
                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-400 dark:bg-rose-500/15 dark:text-rose-400">
                    <x-menu.heroicon name="heart" class="h-6 w-6"/>
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    Sin tipos de sangre registrados
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
    Alpine.data('bloodTypeForm', () => ({
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
                    aboGroup: this.$wire.form.aboGroup,
                    rhFactor: this.$wire.form.rhFactor,
                    canDonateTo: this.$wire.form.canDonateTo,
                    canReceiveFrom: this.$wire.form.canReceiveFrom,
                },
                {
                    code: ['required', ['minLength', 1]],
                    name: ['required', ['minLength', 3]],
                    aboGroup: ['required'],
                    rhFactor: ['required'],
                    canDonateTo: ['required'],
                    canReceiveFrom: ['required'],
                },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.create();
            }
        },
    }));
</script>
@endscript
