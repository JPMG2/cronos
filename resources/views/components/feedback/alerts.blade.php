@props([
    "type"    => "info",
    "message" => false,
    "size"    => "md",
])
@php
    $variants = [
        "success" => [
            "label"      => "¡Éxito!",
            "icon"       => "check-circle",
            "wrapper"    => "border border-emerald-200 bg-emerald-50 dark:border-emerald-500/30 dark:bg-emerald-500/10",
            "iconClass"  => "text-emerald-600 dark:text-emerald-400",
            "labelClass" => "text-emerald-700 dark:text-emerald-300",
        ],
        "error" => [
            "label"      => "Error",
            "icon"       => "x-circle",
            "wrapper"    => "border border-rose-200 bg-rose-50 dark:border-rose-500/30 dark:bg-rose-500/10",
            "iconClass"  => "text-rose-600 dark:text-rose-400",
            "labelClass" => "text-rose-700 dark:text-rose-300",
        ],
        "warning" => [
            "label"      => "Advertencia",
            "icon"       => "exclamation-triangle",
            "wrapper"    => "border border-amber-200 bg-amber-50 dark:border-amber-500/30 dark:bg-amber-500/10",
            "iconClass"  => "text-amber-600 dark:text-amber-400",
            "labelClass" => "text-amber-700 dark:text-amber-300",
        ],
        "info" => [
            "label"      => "Información",
            "icon"       => "information-circle",
            "wrapper"    => "border border-indigo-200 bg-indigo-50 dark:border-indigo-500/30 dark:bg-indigo-500/10",
            "iconClass"  => "text-indigo-600 dark:text-indigo-400",
            "labelClass" => "text-indigo-700 dark:text-indigo-300",
        ],
    ];

    $sizes = [
        "sm" => ["padding" => "px-3 py-2",    "text" => "text-xs", "icon" => "h-4 w-4"],
        "md" => ["padding" => "px-4 py-3.5",  "text" => "text-sm", "icon" => "h-5 w-5"],
        "lg" => ["padding" => "px-5 py-4",    "text" => "text-base","icon" => "h-6 w-6"],
    ];

    $variant  = $variants[$type] ?? $variants["info"];
    $sizeConf = $sizes[$size]    ?? $sizes["md"];
@endphp

<div class="{{ $variant['wrapper'] }} {{ $sizeConf['padding'] }} flex max-w-md items-start gap-3 rounded-xl {{ $sizeConf['text'] }}">
    <x-menu.heroicon name="{{ $variant['icon'] }}" class="mt-0.5 shrink-0 {{ $sizeConf['icon'] }} {{ $variant['iconClass'] }}" />
    <p class="text-slate-700 dark:text-gray-300">
        <span class="block font-semibold {{ $variant['labelClass'] }}">{{ $variant['label'] }}</span>
        {{ $message }}
    </p>
</div>
