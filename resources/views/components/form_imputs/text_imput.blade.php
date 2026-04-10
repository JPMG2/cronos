@props([
    'label'       => null,
    'description' => null,
    'name'        => '',
    'type'        => 'text',
    'placeholder' => '',
    'value'       => null,
    'size'        => 'md',
    'autofocus'   => false,
    'autocomplete' => null,
    'required'    => false,
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-4 py-3 text-base',
    ];
    $sizeClass  = $sizes[$size] ?? $sizes['md'];
    $borderClass = $errors->has($name) ? 'input-border-error' : 'input-border-normal';
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
        <p class="mt-1 text-xs text-slate-400 dark:text-gray-500">{{ $description }}</p>
    @endif

    <input
        {{ $attributes->merge(['class' => 'mt-1.5 input-text-base ' . $sizeClass . ' ' . $borderClass]) }}
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif />

    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
