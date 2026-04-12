@props([
    'label'       => null,
    'description' => null,
    'name'        => '',
    'placeholder' => '',
    'value'       => null,
    'rows'        => 4,
    'size'        => 'md',
    'required'    => false,
    'disabled'    => false,
    'readonly'    => false,
])

@php
    $sizes = [
        'sm' => 'px-3 py-2 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-4 py-3 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $hasError = $errors->has($name);

    $borderBase  = 'border border-indigo-200/80 focus:border-indigo-400 focus:ring-indigo-400/25 dark:border-gray-700 dark:focus:border-sky-500 dark:focus:ring-sky-400/25';
    $borderError = 'border border-rose-400 focus:border-rose-400 focus:ring-rose-400/25 dark:border-rose-500/60 dark:focus:ring-rose-500/20';
    $borderClass = $hasError ? $borderError : $borderBase;

    $textareaBase  = 'block w-full rounded-xl bg-white text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 resize-none dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500';
    $disabledClass = ($disabled || $readonly) ? 'opacity-60 cursor-not-allowed' : '';
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

    <textarea
        {{ $attributes->merge(['class' => implode(' ', array_filter([$textareaBase, $sizeClass, $borderClass, $disabledClass]))]) }}
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
