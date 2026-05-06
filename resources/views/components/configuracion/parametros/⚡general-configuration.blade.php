<?php

declare(strict_types=1);

use App\Models\CatalogForm;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Configuración General')]
class extends Component {

    public ?int $selectedId = null;

    public string $form = '';
    public ?int $formId = null;

    public function selectCatalog(int $id): void
    {
        $this->selectedId = $id;
        $catalog = CatalogForm::query()->find($id);
        $this->form = $catalog->component;
        $this->formId = $id;
    }

    #[Computed]
    public function catalogs(): Collection
    {
        return CatalogForm::query()
            ->active()
            ->orderBy('group')
            ->orderBy('order')
            ->get()
            ->groupBy('group');
    }

    #[Computed]
    public function selected(): ?CatalogForm
    {
        return $this->selectedId
            ? CatalogForm::find($this->selectedId)
            : null;
    }
};
?>

<x-form-style.border-style>
    <x-form-style.main-div>

        {{-- ══ Header ═════════════════════════════════════════════════════════════ --}}
        <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4 dark:border-gray-800">
            <div>
                <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                    Configuración General
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                    Administrá los módulos de referencia del sistema.
                </p>
            </div>
            <div class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                    Módulos Generales
                </span>
            </div>
        </div>

        {{-- ══ Cuerpo: dos paneles ═════════════════════════════════════════════════ --}}
        {{-- Mobile: flex-col, un panel a la vez. Desktop (lg+): flex-row, ambos visibles --}}
        <div class="flex min-h-[520px] flex-col lg:flex-row" x-data="{ selectedId: @entangle('selectedId') }">

            {{-- ── Panel izquierdo: tree-view ──────────────────────────────────── --}}
            {{-- Mobile: visible solo cuando NO hay selección. Desktop: siempre visible --}}
            {{-- x-data en aside para que search sea accesible por toda la nav --}}
            <aside class="shrink-0 border-b border-slate-100 bg-slate-50/60 dark:border-gray-800 dark:bg-gray-900/40 lg:w-64 lg:border-b-0 lg:border-r"
                   :class="selectedId ? 'hidden lg:block' : 'block'"
                   x-data="{ search: '' }">
                <div class="px-3 py-3">

                    {{-- Buscador --}}
                    <div class="relative">
                        <x-menu.heroicon name="magnifying-glass"
                            class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 dark:text-gray-500"/>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Buscar módulo…"
                            class="w-full rounded-lg border border-slate-200 bg-white py-1.5 pl-8 pr-3 text-xs placeholder-slate-400 shadow-sm transition focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"/>
                    </div>

                    {{-- Grupos + ítems --}}
                    <nav class="mt-3 space-y-2">

                        @foreach($this->catalogs as $group => $items)
                            <div x-data="{ open: true }">

                                {{-- Cabecera de grupo --}}
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between rounded-lg px-2 py-1.5 text-left transition hover:bg-slate-100 dark:hover:bg-gray-800/60">
                                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-500">
                                        {{ $group }}
                                    </span>
                                    {{-- ✅ span wrapper para que Alpine maneje :class, no Blade --}}
                                    <span :class="open ? '' : '-rotate-90'" class="inline-flex transition-transform duration-200">
                                        <x-menu.heroicon name="chevron-down" class="h-3 w-3 text-slate-400 dark:text-gray-600"/>
                                    </span>
                                </button>

                                {{-- Ítems del grupo --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-0.5 space-y-0.5">

                                    @foreach($items as $catalog)
                                        <button
                                            type="button"
                                            wire:click="selectCatalog({{ $catalog->id }})"
                                            wire:loading.class="opacity-40 pointer-events-none" wire:target="selectCatalog"
                                            x-show="search === '' || '{{ strtolower($catalog->title) }}'.includes(search.toLowerCase())"
                                            class="group relative flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-left transition-all duration-150"
                                            :class="selectedId === {{ $catalog->id }}
                                                ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-sky-300'
                                                : 'text-slate-600 hover:bg-white hover:text-slate-800 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-200'">

                                            {{-- Línea activa izquierda --}}
                                            <span x-show="selectedId === {{ $catalog->id }}"
                                                  x-cloak
                                                  class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-indigo-500 dark:bg-sky-400">
                                            </span>

                                            {{-- Ícono — ✅ span wrapper para Alpine :class --}}
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition-colors duration-150"
                                                  :class="selectedId === {{ $catalog->id }}
                                                      ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-sky-400'
                                                      : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-500 dark:bg-gray-800 dark:text-gray-500'">
                                                <x-menu.heroicon name="{{ $catalog->icon ?? 'squares-2x2' }}" class="h-3.5 w-3.5"/>
                                            </span>

                                            {{-- Título --}}
                                            <span class="truncate text-xs font-semibold leading-none">
                                                {{ $catalog->title }}
                                            </span>

                                            {{-- Flecha — ✅ span wrapper para Alpine :class --}}
                                            <span class="ml-auto opacity-0 transition-opacity duration-150 group-hover:opacity-50"
                                                  :class="selectedId === {{ $catalog->id }} ? '!opacity-50' : ''">
                                                <x-menu.heroicon name="chevron-right" class="h-3 w-3 shrink-0"/>
                                            </span>
                                        </button>
                                    @endforeach

                                </div>
                            </div>
                        @endforeach

                    </nav>
                </div>
            </aside>

            {{-- ── Panel derecho: contenido ─────────────────────────────────────── --}}
            {{-- Mobile: visible solo cuando HAY selección. Desktop: siempre visible --}}
            <div class="flex flex-1 flex-col"
                 :class="selectedId ? 'flex' : 'hidden lg:flex'">

                @if(is_null($this->selected))
                    {{-- Empty state — patrón /temascoloryestilo: ícono + título + subtítulo --}}
                    <div class="flex flex-1 flex-col items-center justify-center gap-3 px-8 py-16 text-center">
                        <div class="mb-1 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                            <x-menu.heroicon name="squares-2x2" class="h-8 w-8"/>
                        </div>
                        <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                            Seleccioná un módulo para configurar
                        </h3>
                        <p class="max-w-xs text-sm text-slate-500 dark:text-gray-400">
                            Elegí una opción del panel izquierdo para administrar sus registros.
                        </p>
                        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-900">
                            <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5 text-slate-400 dark:text-gray-600"/>
                            <span class="text-xs text-slate-400 dark:text-gray-600">Usá el árbol de la izquierda</span>
                        </div>
                    </div>

                @else
                    {{-- Botón volver — solo mobile --}}
                    <div class="flex items-center border-b border-slate-100 px-4 py-3 dark:border-gray-800 lg:hidden">
                        <button
                            type="button"
                            @click="selectedId = null; $wire.set('selectedId', null)"
                            class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-indigo-500/10">
                            <x-menu.heroicon name="arrow-left" class="h-4 w-4"/>
                            Volver
                        </button>
                    </div>

                    {{-- Header del catálogo seleccionado --}}
                    <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4 dark:border-gray-800">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-sky-400">
                            <x-menu.heroicon name="{{ $this->selected->icon ?? 'squares-2x2' }}" class="h-5 w-5"/>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                                {{ $this->selected->title }}
                            </h3>
                            @if($this->selected->description)
                                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-gray-500">
                                    {{ $this->selected->description }}
                                </p>
                            @endif
                        </div>
                        <div class="shrink-0 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-1 dark:border-indigo-800/30 dark:bg-indigo-900/20">
                            <span class="font-label text-[10px] font-bold uppercase tracking-wider text-indigo-500 dark:text-sky-400">
                                {{ $this->selected->group }}
                            </span>
                        </div>
                    </div>

                    {{-- Área del componente dinámico --}}
                    <div class="flex-1 px-6 py-4">
                        {{-- wire:key fuerza la recreación del elemento al cambiar $formId,
                             lo que re-inicializa Alpine y dispara la transición de entrada --}}
                        <div wire:key="form-panel-{{ $formId }}"
                             wire:loading.class="opacity-50" wire:target="selectCatalog"
                             x-data="{ ready: false }"
                             x-init="$nextTick(() => ready = true)"
                             x-show="ready"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-3"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="display: none"
                             class="flex h-full min-h-[300px] flex-col rounded-2xl border-2 border-slate-200 bg-slate-50/50 transition-opacity duration-150 dark:border-gray-700 dark:bg-gray-900/30">
                            <livewire:dynamic-component :is="$form" :wire:key="$formId" />
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </x-form-style.main-div>
</x-form-style.border-style>