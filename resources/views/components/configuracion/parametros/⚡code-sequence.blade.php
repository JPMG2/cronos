<?php

use App\Dto\Style\ModalConfig;
use App\Livewire\Forms\Configuracion\Parametros\SequenceForm;
use App\Models\Sequence;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Secuencias de Código')]
class extends Component {

    public SequenceForm $form;
    use HasNotifications;
    public bool $readOnly = false;

    #[Computed]
    public function sequences(): Collection
    {
        return Sequence::query()->orderBy('entity')->get();
    }

    public function adviceSequence(): void
    {
        $this->form->validateSequence();

        if (is_null($this->form->sequenceId)) {
            $msgMessage = 'Confirmá la creación de la nueva secuencia. Los campos entidad y código no podrán editarse una vez generado el primer registro.';
             $widowAction = 'storeSequence';
        } else {
            $msgMessage = 'Confirmá la actualización de la secuencia. Los campos entidad y código no podrán editarse una vez generado el primer registro.';
             $widowAction = 'updateSequence';
        }

        $config = new ModalConfig(
            title: 'Confirmar registro',
            message: $msgMessage,
            type: 'info',
            buttons: [
                [
                    'label' => 'Aceptar',
                    'action' =>  $widowAction,
                    'class' => 'save',
                    'params' => [],
                ]
            ]);
        $this->dispatch('openModal', config: (array) $config);
    }

    #[On('storeSequence')]
    public function create(): void
    {
        [$message, $type] = $this->form->createSequence();
        $this->getTypeMessage($message, $type);
        $this->resetFormValues();
    }

    #[On('updateSequence')]
    public function update()
    {
       [$message, $type] = $this->form->updateSequence();
        $this->getTypeMessage($message, $type);
        $this->resetFormValues();
    }

    public function selectSequence(int $id)
    {
        $this->form->fillFromSequence($id);
        $this->readOnly = $this->form->isUsed;
    }

    public function resetFormValues(): void
    {
        $this->resetValidation();
        $this->form->reset();
        $this->readOnly = false;
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>
        <div x-data="sequenceManager">

            {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
            <div class="flex items-start justify-between border-b border-slate-100 px-8 py-4 dark:border-gray-800">
                <div>
                    <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100"
                        x-text="mode === 'edit' ? 'Editando Secuencia' : 'Configuración de Secuencias'">
                        Configuración de Secuencias
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                        Configure prefijo, separador y contador para cada entidad del sistema.
                    </p>
                </div>

                {{-- Badge: nueva --}}
                <div x-show="mode === 'create'"
                     class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                    <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                        Nueva Secuencia
                    </span>
                </div>

                {{-- Badge: editando --}}
                <div x-show="mode === 'edit'"
                     x-cloak
                     class="hidden shrink-0 items-center gap-2 rounded-xl border border-amber-200/80 bg-amber-50 px-4 py-2 dark:border-amber-700/30 dark:bg-amber-900/20 sm:flex">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500 dark:bg-amber-400"></span>
                    <span class="font-label text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400"
                          x-text="'Editando · ' + editingEntity"></span>
                </div>
            </div>

            {{-- ══ SELECTOR DE ENTIDAD ═════════════════════════════════════════════ --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/30"
                 x-data="{ dropOpen: false, dropSearch: '', selectedLabel: '', get noMatch() { return this.dropSearch !== '' && !Alpine.store('seqItems').some(i => i.e.includes(this.dropSearch.toLowerCase()) || i.p.includes(this.dropSearch.toLowerCase())); } }"
                 @keydown.escape.window="dropOpen = false"
                 @sequence-reset.window="dropSearch = ''; selectedLabel = ''; dropOpen = false"
            >

                <div class="mb-2 flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="queue-list" class="h-4 w-4"/>
                    </div>
                    <span class="font-headline text-sm font-bold text-slate-700 dark:text-gray-300">Seleccionar secuencia</span>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">

                    {{-- Combobox --}}
                    <div class="relative flex-1" @click.outside="dropOpen = false">
                        <div class="relative">
                            <x-menu.heroicon name="magnifying-glass"
                                             class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-gray-500"/>
                            <input type="text"
                                   :value="dropOpen ? dropSearch : selectedLabel"
                                   @focus="dropOpen = true; dropSearch = ''"
                                   @input="dropSearch = $event.target.value; dropOpen = true"
                                   :placeholder="selectedLabel && !dropOpen ? '' : 'Buscar secuencia por nombre o código…'"
                                   autocomplete="off"
                                   role="combobox"
                                   :aria-expanded="dropOpen"
                                   class="w-full rounded-xl border border-indigo-200/80 bg-white py-2.5 pl-10 pr-10 text-sm placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-sky-500 dark:focus:ring-sky-400/25"/>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2">
                                <x-menu.heroicon name="chevron-up-down"
                                                 class="h-4 w-4 text-slate-400 dark:text-gray-500"/>
                            </span>
                        </div>

                        {{-- Dropdown --}}
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

                            @forelse($this->sequences as $seq)
                                @php
                                    $initials   = $seq->prefix ? strtoupper(substr($seq->prefix, 0, 2)) : ' ?? ';
                                @endphp
                                <button type="button"
                                        x-show="dropSearch === '' || '{{ strtolower($seq->entity) }}'.includes(dropSearch.toLowerCase()) || '{{ strtolower($seq->prefix) }}'.includes(dropSearch.toLowerCase())"
                                        @click="selectSequence('{{ $seq->prefix }}', '{{ $seq->entity }}', '{{ $seq->id }}'); selectedLabel = '{{ $seq->prefix }} · {{ mb_strtoupper( $seq->entity ) }}'; dropOpen = false"
                                        class="group flex w-full items-center gap-3 border-b border-slate-100/80 px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-indigo-50/80 dark:border-gray-800 dark:hover:bg-gray-800/60"
                                        role="option">
                                    {{-- Avatar código --}}
                                    <x-form-style.avatar
                                            :colorInt="$loop->index">
                                        {{ $initials }}
                                    </x-form-style.avatar>
                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700 dark:text-gray-100 dark:group-hover:text-sky-300">
                                            {{ $seq->entity }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">
                                            Próximo · <span
                                                    class="font-semibold">{{ str_pad($seq->next_number, 4, '0', STR_PAD_LEFT) }}</span>
                                        </p>
                                    </div>

                                </button>
                            @empty
                                <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
                                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10">
                                        <x-menu.heroicon name="hashtag"
                                                         class="h-5 w-5 text-indigo-400 dark:text-indigo-500"/>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600 dark:text-gray-400">Sin secuencias
                                        aún</p>
                                    <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-600">No se encontraron
                                        secuencias registradas.</p>
                                </div>
                            @endforelse

                            <div x-show="noMatch" x-cloak
                                 class="flex flex-col items-center justify-center px-4 py-8 text-center">
                                <x-nomatch/>
                            </div>

                        </div>
                    </div>

                    {{-- Botón nueva --}}
                    <x-btn.new-record
                        @click="mode = 'create'; editingEntity = ''; selectedLabel = ''; dropOpen = false"
                        wire:click="resetFormValues"
                        label="Nueva secuencia" />

                </div>
            </div>

            {{-- ══ FORM BODY ═══════════════════════════════════════════════════════ --}}
            <div class="relative z-10 px-8 py-5">
                <div class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-12">

                    {{-- Fila 1: Identificación --}}
                    <div class="sm:col-span-4">
                        <x-form-inputs.text_input
                                label="Entidad"
                                name="entity"
                                icon="identification"
                                placeholder="Ej: Factura"
                                class="uppercase"
                                maxlength="40"
                                wire:model="form.entity"
                                alpine-error="entity"
                                :readonly="$readOnly"
                                required/>
                    </div>
                    <div class="sm:col-span-3">
                        <x-form-inputs.text_input
                                label="Código interno"
                                name="prefix"
                                icon="hashtag"
                                placeholder="Ej: FAC"
                                description="Único en el sistema · máx. 3 a 4 caracteres"
                                class="uppercase"
                                maxlength="4"
                                wire:model="form.prefix"
                                alpine-error="prefix"
                                :readonly="$readOnly"
                                required/>
                    </div>


                    {{-- Fila 2: Formato + Contador --}}
                    <div class="sm:col-span-2">
                        <x-form-inputs.text_input
                                label="Separador"
                                name="separator"
                                icon="link"
                                placeholder="Ej: -"
                                description="Caracter separador entre la entidad y código"
                                wire:model="form.separator"
                                alpine-error="separator"
                                :readonly="$readOnly"
                                maxlength="1"/>
                    </div>
                    <div class="sm:col-span-3">
                        <x-form-inputs.text_input
                                label="Dígitos"
                                name="padding"
                                icon="adjustments-horizontal"
                                placeholder="Ej: 4"
                                description="Cantidad de dígitos (ej: 4 → 0042), máximo 6"
                                maxlength="1"
                                x-mask="9"
                                alpine-error="padding"
                                :readonly="$readOnly"
                                wire:model="form.padding"
                                required/>
                    </div>
                    <div class="sm:col-span-3">
                        <x-form-inputs.text_input
                                label="Valor actual"
                                name="current_value"
                                icon="hashtag"
                                placeholder="Ej: 1"
                                description="Número desde el que continúa"
                                maxlength="4"
                                x-mask="9999"
                                wire:model="form.current_value"
                                alpine-error="current_value"
                                required/>
                    </div>
                    <div class="sm:col-span-3">
                        <x-form-inputs.text_input
                                label="Incremento"
                                name="increment"
                                icon="plus"
                                placeholder="Ej: 1"
                                description="Cuánto sube en cada asignación"
                                maxlength="4"
                                x-mask="9999"
                                wire:model="form.increment"
                                alpine-error="increment"
                                :readonly="$readOnly"
                                required/>
                    </div>

                    <div class="sm:col-span-3">
                        <x-form-inputs.text_input
                                label="Proximo valor "
                                name="current_value_db"
                                icon="circle-stack"
                                maxlength="4"
                                x-mask="9999"
                                readonly/>
                    </div>

                </div>

                {{-- ══ PREVIEW PANEL ═══════════════════════════════════════════════ --}}
                <div class="mt-5 overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-white to-slate-50/60 dark:border-indigo-900/40 dark:from-indigo-950/30 dark:via-gray-900 dark:to-gray-900">
                    <div class="flex flex-col items-center justify-center gap-3 px-8 py-6">

                        <p class="font-label text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                            Vista previa
                        </p>

                        {{-- Estado vacío --}}
                        <p x-show="!hasPreview"
                           class="text-sm text-slate-400 dark:text-gray-600">
                            Completá los campos para ver la vista previa
                        </p>

                        {{-- Código generado --}}
                        <div class="items-baseline gap-1"
                             :class="hasPreview ? 'flex' : 'hidden'">
                            <span x-text="previewPrefix"
                                  class="font-headline text-4xl font-extrabold tracking-tight text-indigo-700 dark:text-sky-400"></span>
                            <span x-text="previewSeparator"
                                  :class="previewSeparator !== '' ? 'inline' : 'hidden'"
                                  class="font-headline text-3xl font-light text-slate-400 dark:text-gray-600"></span>
                            <span x-text="previewNumber"
                                  class="font-headline text-4xl font-extrabold tracking-tight text-indigo-400 dark:text-indigo-500"></span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
            <x-form-style.footer-button>
              <div class="flex justify-between items-center w-full">
                    <div>
                        @if($readOnly)
                            <x-feedback.alerts type="warning"
                                               message="No se puede editar entidad y código ya generó una secuencia."
                            />
                        @endif
                    </div>
                    <div>
                        <x-btn.cancel label="Descartar"
                                      x-on:click="cancel"
                                      wire:click="resetFormValues"
                        />
                        <x-btn.save
                                label="Guardar Secuencia"
                                @click="submit()"
                                wire-target="adviceSequence"/>
                    </div>
              </div>
            </x-form-style.footer-button>

        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.store('seqItems', @json($this->sequences->map(fn($s) => ['e' => strtolower($s->entity), 'p' => strtolower($s->prefix)])->values()));

    Alpine.data('sequenceManager', () => ({
        mode: 'create',
        editingEntity: '',
        errors: {},

        get hasPreview() {
            return !!this.$wire.form.prefix;
        },
        get previewPrefix() {
            return (this.$wire.form.prefix || '').toUpperCase();
        },
        get previewSeparator() {
            return this.$wire.form.separator || '';
        },
        get previewNumber() {
            const current = parseInt(this.$wire.form.current_value) || 0;
            const pad = parseInt(this.$wire.form.padding) || 0;
            return pad > 0 ? String(current).padStart(pad, '0') : String(current);
        },

        cancel() {
            this.mode = 'create';
            this.editingEntity = '';
            this.errors = {};
            this.$dispatch('sequence-reset');
        },

        selectSequence(code, entidad, id) {
            this.mode = 'edit';
            this.editingEntity = entidad + '-' + code;
            this.$wire.selectSequence(id);
        },
        submit() {
            this.errors = validate(
                {
                    entity: this.$wire.form.entity,
                    prefix: this.$wire.form.prefix,
                    separator: this.$wire.form.separator,
                    padding: this.$wire.form.padding,
                    current_value: this.$wire.form.current_value,
                    increment: this.$wire.form.increment,
                },
                {
                    entity: ['required', ['minLength', 3]],
                    prefix: ['required', ['minLength', 3], ['maxLength', 4]],
                    separator: ['required', ['maxLength', 1]],
                    padding: ['required', ['maxLength', 1], ['max', 6]],
                    current_value: ['required', ['maxLength', 4],['min', 1]],
                    increment: ['required', ['maxLength', 1],['min', 1]],

                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.adviceSequence();
        },
    }));
</script>
@endscript
