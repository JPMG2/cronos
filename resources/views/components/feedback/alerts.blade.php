@props([
    "type" => "info",
    "message" => false,
])
@php
    $variants = [
        "success" => [
            "label"   => "¡Éxito!",
            "icon"    => "success",
            "wrapper" => "bg-green-50 outline-green-200",
            "icon"    => "check-circle",
            "text"    => "text-green-600",
        ],
        "error" => [
            "label"   => "Error",
            "icon"    => "x-circle",
            "wrapper" => "bg-red-50 outline-red-200",
            "text"    => "text-red-500",
        ],
        "warning" => [
            "label"   => "Advertencia",
            "icon"    => "exclamation-triangle",
            "wrapper" => "bg-amber-50 outline-amber-200",
            "text"    => "text-amber-500",
        ],
        "info" => [
            "label"   => "Información",
            "icon"    => "information-circle",
            "wrapper" => "bg-blue-50 outline-blue-200",
            "text"    => "text-blue-500",
        ],
    ];

    $variant = $variants[$type] ?? $variants["info"];
@endphp

<div class="{{ $variant['wrapper'] }} flex max-w-md items-start gap-2 rounded-md px-3 py-2 outline outline-1">
    <div class="flex items-start gap-2">
        <x-menu.heroicon name="{{ $variant['icon'] }}" class="w-6 h-6 {{ $variant['text'] }}" />

        <p class="text-xs leading-4 text-slate-700">
            <span class="block font-semibold {{ $variant['text'] }}">{{ $variant['label'] }}</span>
            {{ $message }}
        </p>
    </div>
</div>
