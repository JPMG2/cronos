@props([
    'label'       => null,
    'name'        => '',
    'description' => null,
    'placeholder' => 'Buscar…',
    'options'     => [],
    'value'       => null,
    'icon'        => null,
    'size'        => 'md',
    'required'    => false,
    'disabled'    => false,
    'loading'       => false,
    'loadingTarget' => null,
])

@php
    $opts = collect($options)->map(function ($item, $key) {
        if (is_array($item)) {
            return [
                'value' => (string) ($item['value'] ?? $key),
                'label' => (string) ($item['label'] ?? $item['name'] ?? $item['text'] ?? (string) $key),
            ];
        }
        return ['value' => (string) $key, 'label' => (string) $item];
    })->values()->all();

    $initValue = (string) ($value ?? '');
    $initLabel = collect($opts)->firstWhere('value', $initValue)['label'] ?? '';

    $padY    = match($size) { 'sm' => 'py-1.5 text-xs', 'lg' => 'py-3 text-base', default => 'py-2.5 text-sm' };
    $padLeft = $icon ? 'pl-10' : 'pl-4';
    $inputId = $name . '_ac';
@endphp

<div
    {{ $attributes->only('class')->merge(['class' => 'w-full']) }}
    x-data="{
        open:          false,
        search:        @js($initLabel),
        selected:      @js($initValue),
        selectedLabel: @js($initLabel),
        options:       @js($opts),

        get filtered() {
            if (!this.search || this.search === this.selectedLabel) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },

        openDropdown() {
            if ({{ $disabled ? 'true' : 'false' }}) return;
            this.open = true;
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        selectOption(opt) {
            this.selected      = opt.value;
            this.selectedLabel = opt.label;
            this.search        = opt.label;
            this.open          = false;
            this.$nextTick(() => {
                const el = this.$refs.hiddenInput;
                el.dispatchEvent(new Event('input',  { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },

        clear() {
            this.selected      = '';
            this.selectedLabel = '';
            this.search        = '';
            this.open          = false;
            this.$nextTick(() => {
                const el = this.$refs.hiddenInput;
                el.dispatchEvent(new Event('input',  { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
    }"
    x-on:keydown.escape.stop="open = false"
    x-on:click.outside="open = false; search = selectedLabel"
>
    {{-- Label --}}
    @if ($label)
        <label
            for="{{ $inputId }}"
            class="mb-1.5 block font-label text-sm font-semibold text-slate-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="ml-0.5 text-rose-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    @if ($description)
        <p class="mb-1.5 text-xs text-slate-400 dark:text-gray-500">{{ $description }}</p>
    @endif

    <div class="relative">

        {{-- Icono leading --}}
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-menu.heroicon name="{{ $icon }}" class="h-4 w-4 text-slate-400 dark:text-gray-500" />
            </div>
        @endif

        {{-- Input visible --}}
        <input
            id="{{ $inputId }}"
            type="text"
            role="combobox"
            :aria-expanded="open"
            aria-autocomplete="list"
            aria-haspopup="listbox"
            autocomplete="off"
            spellcheck="false"
            x-ref="searchInput"
            x-model="search"
            @click="openDropdown()"
            @focus="openDropdown()"
            @input="open = true"
            @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.listbox?.querySelector('[role=option]')?.focus())"
            @keydown.enter.prevent="filtered.length > 0 && selectOption(filtered[0])"
            placeholder="{{ $placeholder }}"
            {{ $disabled ? 'disabled' : '' }}
            class="input-text-base input-border-normal w-full pr-9 {{ $padLeft }} {{ $padY }}
                   @error($name) input-border-error @enderror
                   {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}"
        />

        {{-- Trailing: clear | chevron | spinner (solo si loading=true) --}}
        <span class="absolute inset-y-0 right-0 flex items-center pr-2.5">

            @if ($loading)
                {{-- Spinner: wire:loading lo muestra, wire:loading.remove lo oculta --}}
                <svg wire:loading {{ $loadingTarget ? "wire:target=$loadingTarget" : '' }}
                    class="h-4 w-4 animate-spin text-indigo-500 dark:text-sky-400"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove {{ $loadingTarget ? "wire:target=$loadingTarget" : '' }} class="flex items-center">
                    <button
                        type="button"
                        x-show="selected"
                        @click.stop="clear()"
                        style="display:none"
                        class="rounded p-0.5 text-slate-400 transition-colors hover:text-rose-500 dark:text-gray-500 dark:hover:text-rose-400"
                        aria-label="Limpiar selección">
                        <x-menu.heroicon name="x-mark" class="h-3.5 w-3.5" />
                    </button>
                    <span x-show="!selected" class="pointer-events-none text-slate-400 dark:text-gray-500">
                        <x-menu.heroicon name="chevron-up-down" class="h-4 w-4" />
                    </span>
                </span>
            @else
                <button
                    type="button"
                    x-show="selected"
                    @click.stop="clear()"
                    style="display:none"
                    class="rounded p-0.5 text-slate-400 transition-colors hover:text-rose-500 dark:text-gray-500 dark:hover:text-rose-400"
                    aria-label="Limpiar selección">
                    <x-menu.heroicon name="x-mark" class="h-3.5 w-3.5" />
                </button>
                <span x-show="!selected" class="pointer-events-none text-slate-400 dark:text-gray-500">
                    <x-menu.heroicon name="chevron-up-down" class="h-4 w-4" />
                </span>
            @endif

        </span>

        {{-- Hidden input para wire:model --}}
        <input
            type="hidden"
            name="{{ $name }}"
            x-ref="hiddenInput"
            :value="selected"
            {{ $attributes->whereStartsWith('wire:') }}
        />

        {{-- Dropdown --}}
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
            class="absolute z-50 mt-1.5 w-full origin-top overflow-hidden rounded-xl border border-indigo-100 bg-white shadow-lg shadow-slate-200/60 dark:border-gray-700/80 dark:bg-gray-900 dark:shadow-black/30"
            style="display:none"
        >
            <ul class="max-h-60 overflow-y-auto py-1" x-show="filtered.length > 0">
                <template x-for="opt in filtered" :key="opt.value">
                    <li
                        role="option"
                        tabindex="0"
                        :aria-selected="selected === opt.value"
                        @click="selectOption(opt)"
                        @keydown.enter.prevent="selectOption(opt)"
                        @keydown.arrow-down.prevent="$el.nextElementSibling?.focus()"
                        @keydown.arrow-up.prevent="$el.previousElementSibling ? $el.previousElementSibling.focus() : $refs.searchInput.focus()"
                        class="flex cursor-pointer items-center justify-between px-4 py-2.5 text-sm transition-colors duration-75 hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none dark:hover:bg-gray-800/60 dark:focus:bg-gray-800/60"
                        :class="selected === opt.value
                            ? 'font-semibold text-indigo-700 dark:text-sky-400'
                            : 'font-body text-slate-700 dark:text-gray-200'"
                    >
                        <span x-text="opt.label"></span>
                        <span
                            class="ml-2 flex-shrink-0 text-indigo-600 transition-opacity dark:text-sky-400"
                            :class="selected === opt.value ? 'opacity-100' : 'opacity-0'">
                            <x-menu.heroicon name="check-circle" class="h-4 w-4" />
                        </span>
                    </li>
                </template>
            </ul>

            <div
                x-show="filtered.length === 0"
                class="flex flex-col items-center gap-2 px-4 py-6 text-center"
                style="display:none"
            >
                <x-menu.heroicon name="magnifying-glass" class="h-5 w-5 text-slate-300 dark:text-gray-600" />
                <p class="text-sm text-slate-400 dark:text-gray-500">
                    Sin resultados para
                    "<span class="font-semibold text-slate-600 dark:text-gray-300" x-text="search"></span>"
                </p>
            </div>
        </div>

    </div>

    {{-- Error --}}
    @error($name)
        <p class="mt-1.5 text-xs font-medium text-rose-500 dark:text-rose-400" role="alert">
            {{ $message }}
        </p>
    @enderror

</div>
