@props([
    'label'       => null,
    'name'        => '',
    'description' => null,
    'placeholder' => 'Buscar o seleccionar…',
    'options'     => [],        // [{value, label}]  o  ['key' => 'label']
    'value'       => [],        // array de valores pre-seleccionados
    'icon'        => null,      // Heroicon leading (en el chip container)
    'size'        => 'md',      // sm | md | lg
    'required'    => false,
    'disabled'    => false,
    'max'         => null,      // máximo de selecciones permitidas (null = ilimitado)
])

@php
    /*
    |--------------------------------------------------------------------------
    | Normalización de opciones (mismo formato que autocomplete.blade.php)
    |--------------------------------------------------------------------------
    */
    $opts = collect($options)->map(function ($item, $key) {
        if (is_array($item)) {
            return [
                'value' => (string) ($item['value'] ?? $key),
                'label' => (string) ($item['label'] ?? $item['name'] ?? $item['text'] ?? (string) $key),
            ];
        }
        return ['value' => (string) $key, 'label' => (string) $item];
    })->values()->all();

    /*
    |--------------------------------------------------------------------------
    | Valores iniciales pre-seleccionados
    | Reconstruye los objetos {value, label} a partir del array de valores
    |--------------------------------------------------------------------------
    */
    $initValues  = array_map('strval', (array) ($value ?? []));
    $initSelected = collect($opts)
        ->filter(fn ($o) => in_array($o['value'], $initValues, true))
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | Integración Livewire 4
    | Extrae el nombre de la propiedad de wire:model (cualquier variante)
    | y lo pasa a Alpine para usar $wire.set() sin necesidad de boilerplate.
    |--------------------------------------------------------------------------
    */
    $wireModelProp = $attributes->get('wire:model')
        ?? $attributes->get('wire:model.live')
        ?? $attributes->get('wire:model.blur')
        ?? $attributes->get('wire:model.lazy')
        ?? null;

    // Tamaño del texto en el search input
    $textSize = match($size) { 'sm' => 'text-xs', 'lg' => 'text-base', default => 'text-sm' };

    // ID único para el label
    $inputId = $name . '_acm';

    // Estado de error para el estilo del contenedor
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div
    class="w-full"
    x-data="{
        open:     false,
        search:   '',
        selected: @js($initSelected),
        options:  @js($opts),
        max:      {{ $max ?? 'null' }},

        /*
         * filtered — excluye opciones ya seleccionadas y aplica búsqueda.
         * 0 requests al servidor: todo se filtra en el cliente.
         */
        get filtered() {
            const selectedVals = this.selected.map(s => s.value);
            let pool = this.options.filter(o => !selectedVals.includes(o.value));
            if (this.search) {
                const q = this.search.toLowerCase();
                pool = pool.filter(o => o.label.toLowerCase().includes(q));
            }
            return pool;
        },

        get selectedValues() {
            return this.selected.map(s => s.value);
        },

        get reachedMax() {
            return this.max !== null && this.selected.length >= this.max;
        },

        openDropdown() {
            if ({{ $disabled ? 'true' : 'false' }} || this.reachedMax) return;
            this.open = true;
            // Auto-focus: el search input recibe el foco al abrir
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        selectOption(opt) {
            if (this.reachedMax) return;
            this.selected.push(opt);
            this.search = '';
            // Mantener el foco en el search para selección rápida consecutiva
            this.$nextTick(() => this.$refs.searchInput?.focus());
            this.syncLivewire();
        },

        removeOption(value) {
            this.selected = this.selected.filter(s => s.value !== value);
            this.syncLivewire();
        },

        removeLastOption() {
            if (this.search === '' && this.selected.length > 0) {
                this.selected.pop();
                this.syncLivewire();
            }
        },

        /*
         * syncLivewire — actualiza la propiedad Livewire cuando cambia la selección.
         * Usa $wire.set() (Livewire 4 Alpine magic) de forma segura.
         * También despacha un evento nativo 'change' en el contenedor para casos
         * donde se prefiera x-on:change en el padre.
         */
        syncLivewire() {
            @if ($wireModelProp)
                try { this.$wire.set(@js($wireModelProp), this.selectedValues); } catch (e) {}
            @endif
            this.$dispatch('change', { values: this.selectedValues });
        },
    }"
    x-on:keydown.escape.stop="open = false"
    x-on:click.outside="open = false"
>
    {{-- ── Label ──────────────────────────────────────────────────────────── --}}
    @if ($label)
        <label
            for="{{ $inputId }}"
            class="mb-1.5 block font-label text-sm font-semibold text-slate-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="ml-0.5 text-rose-500" aria-hidden="true">*</span>
            @endif
            {{-- Contador cuando hay máximo --}}
            @if ($max)
                <span class="ml-2 font-normal text-slate-400 dark:text-gray-500">
                    (<span x-text="selected.length"></span>/{{ $max }})
                </span>
            @endif
        </label>
    @endif

    @if ($description)
        <p class="mb-1.5 text-xs text-slate-400 dark:text-gray-500">{{ $description }}</p>
    @endif

    {{-- ── Contenedor chips + search ───────────────────────────────────────── --}}
    <div class="relative">
        <div
            @click="openDropdown()"
            class="flex min-h-[44px] w-full cursor-text flex-wrap items-center gap-1.5
                   rounded-xl border bg-white px-3 py-2
                   shadow-sm transition-all duration-200
                   dark:bg-gray-800
                   {{ $hasError
                        ? 'border-rose-400 ring-2 ring-rose-400/25 dark:border-rose-400/60'
                        : '' }}"
            :class="{
                'border-indigo-400 ring-2 ring-indigo-400/25 dark:border-sky-500 dark:ring-sky-400/25': open && !{{ $hasError ? 'true' : 'false' }},
                'border-indigo-200/80 dark:border-gray-700': !open && !{{ $hasError ? 'true' : 'false' }},
            }"
        >
            {{-- Icono leading opcional --}}
            @if ($icon)
                <div class="flex-shrink-0 text-slate-400 dark:text-gray-500">
                    <x-menu.heroicon name="{{ $icon }}" class="h-4 w-4" />
                </div>
            @endif

            {{-- ── Chips de valores seleccionados ─────────────────────────── --}}
            <template x-for="item in selected" :key="item.value">
                <span class="inline-flex items-center gap-1 rounded-lg
                             bg-indigo-100 px-2 py-0.5
                             font-label text-xs font-semibold text-indigo-700
                             dark:bg-indigo-500/15 dark:text-indigo-300">
                    <span x-text="item.label"></span>
                    <button
                        type="button"
                        @click.stop="removeOption(item.value)"
                        {{ $disabled ? 'disabled' : '' }}
                        class="ml-0.5 rounded text-indigo-500 transition-colors
                               hover:text-rose-600 dark:text-indigo-400 dark:hover:text-rose-400
                               focus:outline-none"
                        :aria-label="`Quitar ${item.label}`">
                        <x-menu.heroicon name="x-mark" class="h-3 w-3" />
                    </button>
                </span>
            </template>

            {{-- ── Search input inline ─────────────────────────────────────── --}}
            <input
                id="{{ $inputId }}"
                type="text"
                x-ref="searchInput"
                x-model="search"
                @click.stop="openDropdown()"
                @focus="openDropdown()"
                @input="open = true"
                @keydown.backspace="removeLastOption()"
                @keydown.arrow-down.prevent="
                    open = true;
                    $nextTick(() => $refs.listbox?.querySelector('[role=option]')?.focus())
                "
                @keydown.enter.prevent="filtered.length > 0 && selectOption(filtered[0])"
                :placeholder="selected.length === 0 ? '{{ $placeholder }}' : ''"
                autocomplete="off"
                spellcheck="false"
                {{ $disabled ? 'disabled' : '' }}
                class="min-w-[80px] flex-1 border-0 bg-transparent p-0
                       {{ $textSize }} text-slate-700 placeholder-slate-400
                       focus:outline-none focus:ring-0
                       dark:text-gray-200 dark:placeholder-gray-600
                       {{ $disabled ? 'cursor-not-allowed opacity-60' : '' }}"
            />

            {{-- Chevron trailing --}}
            <span class="ml-auto flex-shrink-0 text-slate-400 dark:text-gray-500">
                <x-menu.heroicon name="chevron-up-down" class="h-4 w-4" />
            </span>
        </div>

        {{-- Hidden inputs para submit de formulario normal (PHP $_POST) --}}
        {{-- Un input oculto por valor seleccionado → name="campo[]" --}}
        <template x-for="item in selected" :key="item.value">
            <input type="hidden" :name="`{{ $name }}[]`" :value="item.value" />
        </template>

        {{-- Fallback cuando no hay seleccionados → envía array vacío --}}
        <input
            type="hidden"
            name="{{ $name }}[]"
            value=""
            x-show="false"
            x-bind:disabled="selected.length > 0"
        />

        {{-- ── Dropdown ────────────────────────────────────────────────────── --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.97]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.97]"
            x-ref="listbox"
            role="listbox"
            aria-multiselectable="true"
            class="absolute z-50 mt-1.5 w-full origin-top overflow-hidden
                   rounded-xl border border-indigo-100 bg-white
                   shadow-lg shadow-slate-200/60
                   dark:border-gray-700/80 dark:bg-gray-900 dark:shadow-black/30"
            style="display:none"
        >
            {{-- Mensaje cuando se alcanzó el máximo --}}
            <div
                x-show="reachedMax"
                class="flex items-center gap-2 border-b border-amber-100 bg-amber-50 px-4 py-2.5
                       dark:border-amber-500/20 dark:bg-amber-500/10"
                style="display:none"
            >
                <x-menu.heroicon name="information-circle" class="h-4 w-4 flex-shrink-0 text-amber-600 dark:text-amber-400" />
                <p class="text-xs font-medium text-amber-700 dark:text-amber-400">
                    Máximo de {{ $max }} selecciones alcanzado
                </p>
            </div>

            {{-- Lista de opciones disponibles --}}
            <ul
                class="max-h-60 overflow-y-auto py-1"
                x-show="filtered.length > 0 && !reachedMax"
            >
                <template x-for="opt in filtered" :key="opt.value">
                    <li
                        role="option"
                        tabindex="0"
                        :aria-selected="false"
                        @click="selectOption(opt)"
                        @keydown.enter.prevent="selectOption(opt)"
                        @keydown.arrow-down.prevent="$el.nextElementSibling?.focus()"
                        @keydown.arrow-up.prevent="
                            $el.previousElementSibling
                                ? $el.previousElementSibling.focus()
                                : $refs.searchInput.focus()
                        "
                        class="flex cursor-pointer items-center gap-3 px-4 py-2.5
                               text-sm font-body text-slate-700 transition-colors duration-75
                               hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none
                               dark:text-gray-200 dark:hover:bg-gray-800/60 dark:focus:bg-gray-800/60"
                    >
                        {{-- Mini checkbox visual --}}
                        <span class="flex h-4 w-4 flex-shrink-0 items-center justify-center
                                     rounded border-2 border-indigo-300 bg-transparent
                                     dark:border-gray-600">
                        </span>
                        <span x-text="opt.label"></span>
                    </li>
                </template>
            </ul>

            {{-- Todas las opciones ya seleccionadas --}}
            <div
                x-show="filtered.length === 0 && !reachedMax && options.length > 0 && search === ''"
                class="flex flex-col items-center gap-2 px-4 py-6 text-center"
                style="display:none"
            >
                <x-menu.heroicon name="check-circle" class="h-5 w-5 text-emerald-400 dark:text-emerald-500" />
                <p class="text-sm text-slate-400 dark:text-gray-500">
                    Todas las opciones seleccionadas
                </p>
            </div>

            {{-- Sin resultados de búsqueda --}}
            <div
                x-show="filtered.length === 0 && !reachedMax && search !== ''"
                class="flex flex-col items-center gap-2 px-4 py-6 text-center"
                style="display:none"
            >
                <x-menu.heroicon name="magnifying-glass" class="h-5 w-5 text-slate-300 dark:text-gray-600" />
                <p class="text-sm text-slate-400 dark:text-gray-500">
                    Sin resultados para
                    "<span class="font-semibold text-slate-600 dark:text-gray-300" x-text="search"></span>"
                </p>
            </div>

            {{-- Footer: hint de teclado --}}
            <div
                x-show="filtered.length > 0 && !reachedMax"
                class="border-t border-slate-100 px-4 py-2 dark:border-gray-800"
                style="display:none"
            >
                <p class="text-[10px] text-slate-400 dark:text-gray-600">
                    ↑↓ navegar · Enter seleccionar · Backspace quitar último
                </p>
            </div>
        </div>
    </div>

    {{-- ── Error ──────────────────────────────────────────────────────────── --}}
    @error($name)
        <p class="mt-1.5 text-xs font-medium text-rose-500 dark:text-rose-400" role="alert">
            {{ $message }}
        </p>
    @enderror

</div>
