{{--
    Botón Guardar autónomo — <x-btn.save>

    Props:
        label  — texto del botón   (default: "Guardar")
        icon   — heroicon          (default: "document-check")

    El wire:click se pasa como atributo normal y se usa
    automáticamente como wire:target para el spinner.

    Uso:
        <x-btn.save wire:click="saveRegion" />
        <x-btn.save wire:click="store" label="Crear" icon="plus" />
--}}

@props([
    'label'      => 'Guardar',
    'icon'       => 'document-check',
    'wireTarget' => null,
])

@php
    $hasErrors  = $errors->isNotEmpty();
    $activeIcon = $hasErrors ? 'x-mark' : $icon;
    // Prop explícito tiene prioridad; si no, infiere desde wire:click
    $target     = $wireTarget ?? $attributes->get('wire:click');
@endphp

<button
    type="button"
    {{ $attributes->class(['btn-base btn-save btn-sm w-full disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto']) }}
    @if ($target) wire:loading.attr="disabled" wire:target="{{ $target }}" @endif>

    {{-- Spinner durante loading --}}
    @if ($target)
        <svg wire:loading wire:target="{{ $target }}"
            class="h-3.5 w-3.5 animate-spin"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
    @endif

    {{-- Icono normal/error --}}
    <span @if ($target) wire:loading.remove wire:target="{{ $target }}" @endif>
        <x-menu.heroicon name="{{ $activeIcon }}" class="h-3.5 w-3.5 {{ $hasErrors ? 'text-rose-400' : '' }}" />
    </span>

    {{ $label }}
</button>
