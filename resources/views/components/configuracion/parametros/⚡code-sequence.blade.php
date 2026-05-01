<?php

use App\Models\Sequence;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Secuencias de Código')]
class extends Component {

    #[Computed]
    public function sequences(): Collection
    {
        return Sequence::query()->orderBy('entity')->get();
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
                 x-data="{ dropOpen: false, dropSearch: '', selectedLabel: '' }"
                 @keydown.escape.window="dropOpen = false">

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
                        <div x-show="dropOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute z-30 mt-1.5 max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/60 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/40"
                             role="listbox">

                            {{-- Items mocked para diseño --}}
                            @foreach([
                                ['code' => 'PAC', 'name' => 'Pacientes',    'next' => 42,   'active' => true],
                                ['code' => 'TUR', 'name' => 'Turnos',       'next' => 1083, 'active' => true],
                                ['code' => 'FAC', 'name' => 'Facturación',  'next' => 217,  'active' => true],
                                ['code' => 'LAB', 'name' => 'Laboratorio',  'next' => 9,    'active' => true],
                            ] as $seq)
                                <button type="button"
                                        x-show="dropSearch === '' || '{{ strtolower($seq['name']) }}'.includes(dropSearch.toLowerCase()) || '{{ strtolower($seq['code']) }}'.includes(dropSearch.toLowerCase())"
                                        @click="selectSequence('{{ $seq['code'] }}', '{{ $seq['name'] }}'); selectedLabel = '{{ $seq['code'] }} · {{ strtoupper($seq['name']) }}'; dropOpen = false"
                                        class="group flex w-full items-center gap-3 border-b border-slate-100/80 px-4 py-3 text-left transition-colors duration-150 last:border-b-0 hover:bg-indigo-50/80 dark:border-gray-800 dark:hover:bg-gray-800/60"
                                        role="option">
                                    {{-- Avatar código --}}
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-xs font-extrabold tracking-wide text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400">
                                        {{ $seq['code'] }}
                                    </div>
                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700 dark:text-gray-100 dark:group-hover:text-sky-300">
                                            {{ $seq['name'] }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">
                                            Próximo · <span
                                                    class="font-semibold">{{ str_pad($seq['next'], 4, '0', STR_PAD_LEFT) }}</span>
                                        </p>
                                    </div>
                                    {{-- Código badge --}}
                                    <span class="shrink-0 font-label text-xs font-bold text-slate-400 dark:text-gray-600">
                                    {{ $seq['code'] }}
                                </span>
                                </button>
                            @endforeach

                        </div>
                    </div>

                    {{-- Botón nueva --}}
                    <button type="button"
                            @click="mode = 'create'; editingEntity = ''; selectedLabel = ''; dropOpen = false"
                            class="flex shrink-0 items-center gap-2 rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60">
                        <x-menu.heroicon name="plus-circle" class="h-4 w-4"/>
                        Nueva secuencia
                    </button>

                </div>
            </div>

            {{-- ══ FORM BODY ═══════════════════════════════════════════════════════ --}}
            <div class="relative z-10 px-8 py-5">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

                    {{-- ── Card 01: Entidad ─────────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <x-form-style.number-tag number="01" label="Entidad"/>
                        <div class="space-y-3">
                            <x-form-inputs.text_input
                                    label="Nombre de entidad"
                                    name="entity_name"
                                    icon="identification"
                                    placeholder="Ej: Pacientes"
                                    required/>
                            <x-form-inputs.text_input
                                    label="Código interno"
                                    name="entity"
                                    icon="hashtag"
                                    placeholder="Ej: PAC"
                                    description="Único en el sistema · máx. 6 caracteres"
                                    class="uppercase"
                                    required/>
                        </div>
                    </div>

                    {{-- ── Card 02: Formato del Código ──────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <x-form-style.number-tag number="02" label="Formato del Código"/>
                        <div class="space-y-3">
                            <x-form-inputs.text_input
                                    label="Prefijo"
                                    name="prefix"
                                    icon="hashtag"
                                    placeholder="Ej: PAC"
                                    description="Se añade antes del número generado"/>
                            <x-form-inputs.text_input
                                    label="Separador"
                                    name="separator"
                                    icon="link"
                                    placeholder="Ej: -"
                                    description="Vacío para omitir · Ej: - / •"/>
                            <x-form-inputs.select
                                    label="Dígitos (padding)"
                                    name="padding"
                                    icon="adjustments-horizontal"
                                    required>
                                <option value="2">2 dígitos → 01</option>
                                <option value="3">3 dígitos → 001</option>
                                <option value="4" selected>4 dígitos → 0001</option>
                                <option value="5">5 dígitos → 00001</option>
                            </x-form-inputs.select>
                        </div>
                    </div>

                    {{-- ── Card 03: Contador ────────────────────────────────────── --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        {{-- Amber badge — dato operacional --}}
                        <div class="mb-3 flex items-center gap-2.5">
                            <span class="inline-flex h-5 items-center rounded-md bg-amber-100 px-2 text-xs font-bold text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">03</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Contador</span>
                        </div>
                        <div class="space-y-3">
                            <x-form-inputs.text_input
                                    label="Valor actual"
                                    name="current_value"
                                    icon="hashtag"
                                    type="number"
                                    placeholder="0"
                                    description="Número desde el que continúa la secuencia"/>
                            <x-form-inputs.text_input
                                    label="Incremento"
                                    name="increment"
                                    icon="plus"
                                    type="number"
                                    placeholder="1"
                                    description="Cuánto sube en cada asignación"/>

                            {{-- Próximo número — display read-only, no input --}}
                            <div>
                                <p class="mb-1.5 text-sm font-semibold text-slate-700 dark:text-gray-300">
                                    Próximo número
                                </p>
                                <div class="flex items-center gap-3 rounded-xl border border-indigo-200/60 bg-indigo-50/60 px-4 py-2.5 dark:border-indigo-700/30 dark:bg-indigo-500/10">
                                    <x-menu.heroicon name="arrow-right-on-rectangle"
                                                     class="h-4 w-4 shrink-0 -rotate-180 text-indigo-400 dark:text-indigo-500"/>
                                    <span class="font-headline text-xl font-extrabold text-indigo-600 dark:text-sky-400">42</span>
                                    <span class="ml-auto font-label text-xs text-slate-400 dark:text-gray-600">calculado</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══ PREVIEW PANEL ═══════════════════════════════════════════════ --}}
                <div class="mt-5 overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-white to-slate-50/60 dark:border-indigo-900/40 dark:from-indigo-950/30 dark:via-gray-900 dark:to-gray-900">
                    <div class="flex flex-col items-center justify-between gap-6 px-8 py-5 sm:flex-row">

                        {{-- Label --}}
                        <div class="shrink-0">
                            <p class="font-label text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                                Vista previa
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-600">
                                Próxima asignación automática
                            </p>
                        </div>

                        {{-- Código generado --}}
                        <div class="flex items-baseline gap-3">
                            <span class="font-headline text-4xl font-extrabold tracking-tight text-indigo-700 dark:text-sky-400">
                                PAC
                            </span>
                            <span class="font-headline text-3xl font-light text-slate-300 dark:text-gray-700">
                                —
                            </span>
                            <span class="font-headline text-4xl font-extrabold tracking-tight text-indigo-400 dark:text-indigo-500">
                                0042
                            </span>
                        </div>

                        {{-- Status --}}
                        <div class="flex shrink-0 flex-col items-center gap-1.5 sm:items-end">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                Activa
                            </span>
                            <span class="font-label text-xs text-slate-400 dark:text-gray-600">
                                entidad · PAC
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ══ FOOTER ══════════════════════════════════════════════════════════ --}}
            <x-form-style.footer-button>
                <x-btn.cancel label="Descartar"/>
                <x-btn.save label="Guardar Secuencia"/>
            </x-form-style.footer-button>

        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('sequenceManager', () => ({
        mode: 'create',
        editingEntity: '',

        selectSequence(code) {
            this.mode = 'edit';
            this.editingEntity = code;
        },
    }));
</script>
@endscript
