@props([
    "label" => null,
    "name" => "",
    "type" => "text",
    "placeholder" => "",
    "value" => null,
    "size" => "md",
    "autofocus" => false,
    "autocomplete" => null,
])

@php
    $sizes = [
        "sm" => "px-3 py-1.5 text-xs",
        "md" => "px-4 py-2.5 text-sm",
        "lg" => "px-4 py-3 text-base",
    ];
    $sizeClass = $sizes[$size] ?? $sizes["md"];

    $borderClass = $errors->has($name) ? "input-border-error" : "input-border-normal";
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <input
        {{ $attributes->merge(["class" => "input-text-base $sizeClass $borderClass"]) }}
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}" />

    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
