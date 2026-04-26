@props([
    'label'       => null,
    'description' => null,
    'name'        => '',
    'size'        => 'md',
    'required'    => false,
    'disabled'    => false,
    'icon'        => null,
    'alpineError' => null,
])

@php
    $sizes = [
        'sm' => 'py-2 text-xs',
        'md' => 'py-2.5 text-sm',
        'lg' => 'py-3 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $hasError = $errors->has($name);

    $paddingLeft  = $icon ? 'pl-11' : 'pl-4';

    $borderBase  = 'border border-indigo-200/80 focus:border-indigo-400 focus:ring-indigo-400/25 dark:border-gray-700 dark:focus:border-sky-500 dark:focus:ring-sky-400/25';
    $borderError = 'border border-rose-400 focus:border-rose-400 focus:ring-rose-400/25 dark:border-rose-500/60 dark:focus:ring-rose-500/20';
    $borderClass = $hasError ? $borderError : $borderBase;

    $selectBase  = 'block w-full rounded-xl bg-white text-slate-800 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100';
    $disabledClass = $disabled ? 'opacity-60 cursor-not-allowed' : '';
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="ml-0.5 text-rose-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    @if ($description)
        <p class="mt-1 mb-1.5 text-xs text-slate-400 dark:text-gray-500">{{ $description }}</p>
    @endif

    <div class="relative mt-1.5 group">
        {{-- Leading icon --}}
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <x-menu.heroicon
                    name="{{ $icon }}"
                    class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-indigo-500 dark:text-gray-500 dark:group-focus-within:text-sky-400"
                />
            </div>
        @endif

        <select
            {{ $attributes->merge(['class' => 'appearance-none bg-none pr-10 ' . implode(' ', array_filter([$selectBase, $sizeClass, $paddingLeft, $borderClass, $disabledClass]))]) }}
            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            @if ($disabled) disabled @endif
        >
            {{ $slot }}
        </select>

        {{-- Trailing chevron --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
            <x-menu.heroicon
                name="chevron-up-down"
                class="h-5 w-5 text-slate-400 dark:text-gray-500"
            />
        </div>
    </div>

    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
    @enderror

    @if ($alpineError)
        <p
            x-show="errors.{{ $alpineError }}"
            x-text="errors.{{ $alpineError }}"
            x-transition
            class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400"></p>
    @endif
</div>
