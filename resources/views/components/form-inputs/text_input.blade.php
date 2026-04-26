@props([
    'label'        => null,
    'description'  => null,
    'name'         => '',
    'type'         => 'text',
    'placeholder'  => '',
    'value'        => null,
    'size'         => 'md',
    'autofocus'    => false,
    'autocomplete' => null,
    'required'     => false,
    'disabled'     => false,
    'readonly'     => false,
    'icon'         => null,
    'iconTrailing' => null,
    'alpineError'  => null,
])

@php
    $sizes = [
        'sm' => 'py-2 text-xs',
        'md' => 'py-2.5 text-sm',
        'lg' => 'py-3 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $hasError    = $errors->has($name);
    $isPassword  = $type === 'password';

    $paddingLeft  = $icon          ? 'pl-11' : 'pl-4';
    $paddingRight = ($isPassword || $iconTrailing) ? 'pr-11' : 'pr-4';

    $borderBase  = 'border border-indigo-200/80 focus:border-indigo-400 focus:ring-indigo-400/25 dark:border-gray-700 dark:focus:border-sky-500 dark:focus:ring-sky-400/25';
    $borderError = 'border border-rose-400 focus:border-rose-400 focus:ring-rose-400/25 dark:border-rose-500/60 dark:focus:ring-rose-500/20';
    $borderClass = $hasError ? $borderError : $borderBase;

    $bgText = $readonly
        ? 'bg-slate-100 text-slate-900 dark:bg-gray-700/70 dark:text-gray-50'
        : 'bg-white text-slate-800 dark:bg-gray-800 dark:text-gray-100';

    $inputBase = "block w-full rounded-xl {$bgText} placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 dark:placeholder-gray-500";
    $disabledClass = $disabled ? 'opacity-60 cursor-not-allowed' : ($readonly ? 'cursor-default' : '');
@endphp

<div
    @if ($isPassword) x-data="{ showPwd: false }" @endif
>
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

        {{-- Input --}}
        <input
            {{ $attributes->merge(['class' => implode(' ', array_filter([$inputBase, $sizeClass, $paddingLeft, $paddingRight, $borderClass, $disabledClass]))]) }}
            @if ($isPassword)
                :type="showPwd ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($autofocus) autofocus @endif
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            @if ($readonly) readonly @endif
        />

        {{-- Password toggle --}}
        @if ($isPassword)
            <button
                type="button"
                @click="showPwd = !showPwd"
                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition-colors hover:text-indigo-500 dark:text-gray-500 dark:hover:text-sky-400"
                :aria-label="showPwd ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            >
                <x-menu.heroicon x-show="!showPwd" name="eye" class="h-5 w-5" />
                <x-menu.heroicon x-show="showPwd" name="eye-slash" class="h-5 w-5" />
            </button>
        {{-- Trailing icon (non-password) --}}
        @elseif ($iconTrailing)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <x-menu.heroicon
                    name="{{ $iconTrailing }}"
                    class="h-5 w-5 text-slate-400 dark:text-gray-500"
                />
            </div>
        @endif
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
