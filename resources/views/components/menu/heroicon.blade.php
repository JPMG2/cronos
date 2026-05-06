@props([
    "name" => false,
])

@php
    $icons = require resource_path('views/components/menu/heroicons-registry.php');
    $path  = $icons[$name] ?? $icons["question-mark-circle"];
    $defaultClass = $attributes->has('class') ? '' : 'w-6 h-6';
@endphp

<svg
    {{ $attributes->merge(["class" => $defaultClass]) }}
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
    stroke-width="1.5"
    aria-hidden="true">
    {!! $path !!}
</svg>
