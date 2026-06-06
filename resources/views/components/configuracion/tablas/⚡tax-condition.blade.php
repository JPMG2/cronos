<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Parametros\TaxConditionForm;
use App\Models\TaxCondition;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {

    use HasNotifications;

    public TaxConditionForm $form;

    #[Computed]
    public function taxConditions(): Collection
    {
        return TaxCondition::query()->orderBy('name')->get();
    }

    public function create(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->storeTaxCondition();
        $this->messageOutPut($message, $type);
    }

    public function update(): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateTaxCondition();
        $this->messageOutPut($message, $type);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function toggleDiscriminateTax(int $id): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateDiscriminateTax($id);
        $this->messageOutPut($message, $type);
    }

    public function toggleStatusTax(int $id): void
    {
        Gate::authorize('parametros.update');
        [$message, $type] = $this->form->updateStatusTax($id);
        $this->messageOutPut($message, $type);
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->taxConditions);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
    }

    public function startEditTaxCondition(int $id): void
    {
        $this->form->fillStatusData($id);
    }

    public function delete(int $id): never
    {
        Gate::authorize('parametros.update');
        dd('Queda por implementar confirmación');
    }
};
?>

<div class="flex h-full flex-col" x-data="taxConditionForm">

    {{-- ══ FORM — campos: nombre · código · discrimina IVA ══ --}}
    <div class="border-b border-slate-100 bg-white/70 px-5 py-3.5 dark:border-gray-800 dark:bg-gray-900/50">

        <div x-ref="taxTop" class="flex flex-col gap-2">

            {{-- Fila 1: Nombre --}}
            <div>
                <x-form-inputs.text_input
                    label="Nombre"
                    name="name"
                    icon="identification"
                    placeholder="Ej: Responsable Inscripto"
                    size="sm"
                    class="capitalize"
                    wire:model="form.name"
                    alpineError="name"
                    required />
            </div>

            {{-- Fila 2: Código · Discrimina IVA · Botones --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                <div class="sm:w-40 sm:shrink-0">
                    <x-form-inputs.text_input
                        label="Código"
                        name="code"
                        icon="hashtag"
                        placeholder="RI"
                        maxlength="10"
                        class="font-mono font-bold uppercase tracking-wider"
                        size="sm"
                        wire:model="form.code"
                        alpineError="code"
                        required />
                </div>

                {{-- Discrimina IVA --}}
                <div class="flex flex-col justify-end sm:mt-[26px]">
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 transition-colors hover:bg-slate-100 dark:border-gray-700 dark:bg-gray-800/60 dark:hover:bg-gray-800">
                        <input type="checkbox"
                               name="discriminate_tax"
                               wire:model="form.discriminate_tax"
                               alpineError="discriminate_tax"
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-xs font-semibold text-slate-600 dark:text-gray-300">Discrimina IVA</span>
                    </label>
                </div>

                <div class="flex flex-1 items-center justify-end gap-2 sm:mt-[26px]">

                    {{-- Badge "Editando" --}}
                    <div x-show="$wire.form.taxId !== null"
                         x-cloak
                         class="hidden shrink-0 items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                        <span class="h-1.5 w-1.5 motion-safe:animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        <span class="font-label text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            Editando
                        </span>
                    </div>

                    <x-btn.new-record
                        x-show="$wire.form.taxId === null"
                        @click="cancelEdit"
                        wire:click="cancelEdit"
                        label="Nueva entrada" />
                    <x-btn.mini-cancel @click="cancelEdit" wire:click="cancelEdit" />
                    <x-btn.save label="{{ $this->form->taxId ? 'Actualizar' : 'Guardar' }}" @click="submit()"
                                wire:target="create,update" />

                </div>

            </div>

        </div>

    </div>

    {{-- ══ LISTADO ══ --}}
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">

        {{-- Cabecera de columnas --}}
        @if($this->taxConditions->isNotEmpty())
            <x-list.header :code="true" :toggles="['Discrimina']" />
        @endif

        {{-- Filas --}}
        @forelse($this->taxConditions as $taxCondition)
            <div wire:key="tax-condition-{{ $taxCondition->id }}"
                 class="group flex items-center justify-between px-5 py-1.5 transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-900/40
                            {{ $this->form->taxId === $taxCondition->id ? 'border-l-2 border-amber-400 bg-amber-50/50 dark:border-amber-500 dark:bg-amber-900/10' : 'border-l-2 border-transparent' }}">

                {{-- Código badge + Nombre --}}
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <span class="shrink-0 min-w-[52px] text-center rounded-lg bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                        {{ $taxCondition->code }}
                    </span>
                    <span class="truncate text-sm font-semibold text-slate-700 dark:text-gray-200">
                        {{ $taxCondition->name }}
                    </span>
                </div>

                {{-- Discrimina · Estado · Acciones --}}
                <div class="flex shrink-0 items-center gap-1.5">

                    {{-- Discrimina IVA: toggle custom con ícono, ancho fijo w-24 para alinear con header --}}
                    <button type="button"
                            wire:click="toggleDiscriminateTax({{ $taxCondition->id }})"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="toggleDiscriminateTax({{ $taxCondition->id }})"
                            class="flex w-24 items-center justify-center rounded-lg transition-colors duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40 dark:focus:ring-sky-400/40"
                            aria-label="{{ $taxCondition->discriminate_tax ? 'No discrimina' : 'Discrimina' }} IVA">
                        @if($taxCondition->discriminate_tax)
                            <span class="hidden items-center gap-1 rounded-lg bg-violet-100 px-2 py-0.5 text-[11px] font-semibold text-violet-700 ring-1 ring-inset ring-violet-200/60 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20 sm:inline-flex">
                                <x-menu.heroicon name="receipt-percent" class="h-3 w-3" />
                                Discrimina
                            </span>
                        @else
                            <span class="hidden items-center gap-1 rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-400 ring-1 ring-inset ring-slate-200/60 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700 sm:inline-flex">
                                No discrimina
                            </span>
                        @endif
                    </button>

                    <x-list.toggle
                        :active="$taxCondition->is_active"
                        size="sm"
                        wire:click="toggleStatusTax({{ $taxCondition->id }})"
                        wire:loading.class="opacity-50 cursor-wait"
                        wire:target="toggleStatusTax({{ $taxCondition->id }})"
                        aria-label="{{ $taxCondition->is_active ? 'Desactivar' : 'Activar' }} {{ $taxCondition->name }}" />

                    <x-list.divider />

                    <x-list.actions>
                        <x-btn.mini-edit
                            lable="Editar"
                            data-name="{{ $taxCondition->name }}"
                            data-code="{{ $taxCondition->code }}"
                            data-discriminate_tax="{{ $taxCondition->discriminate_tax ? 'true' : 'false' }}"
                            @click="
                                $wire.form.name = $el.dataset.name;
                                $wire.form.code = $el.dataset.code;
                                $wire.form.discriminate_tax = $el.dataset.discriminate_tax === 'true';
                                $wire.startEditTaxCondition({{ $taxCondition->id }});
                                goTopTaxCondition();
                            " />
                        <x-btn.mini-delete lable="Eliminar" wire:click="delete({{ $taxCondition->id }})" />
                    </x-list.actions>

                </div>
            </div>

            @if(! $loop->last)
                <div class="mx-5 h-px bg-slate-100 dark:bg-gray-800/80"></div>
            @endif

        @empty
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                    <x-menu.heroicon name="receipt-percent" class="h-6 w-6" />
                </div>
                <h3 class="font-headline text-sm font-bold text-slate-800 dark:text-gray-100">
                    Sin condiciones impositivas registradas
                </h3>
                <p class="mt-1 font-body text-xs text-slate-500 dark:text-gray-400">
                    Usá el formulario de arriba para agregar la primera.
                </p>
            </div>
        @endforelse

    </div>

</div>

@script
<script>
    Alpine.data('taxConditionForm', () => ({
        errors: {},

        cancelEdit() {
            this.$wire.cancelEdit();
            this.errors = {};
        },
        submit() {
            const isEditing = this.$wire.form.taxId !== null;

            this.errors = validate(
                { code: this.$wire.form.code, name: this.$wire.form.name },
                {
                    code: ['required', ['minLength', 2]],
                    name: ['required', ['minLength', 3]],
                },
            );

            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.create();
            }
        },
        goTopTaxCondition() {
            this.$refs.taxTop.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
    }));
</script>
@endscript
