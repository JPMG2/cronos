{{--
    Botón Cancelar autónomo — <x-btn.cancel>

    Props:
        label      — texto del botón   (default: "Cancelar")
        wireClick  — método Livewire   (default: null)

    Uso:
        <x-btn.cancel />
        <x-btn.cancel wire:click="resetForm" label="Descartar" />
--}}

@props([
    'label'     => 'Cancelar',
    'wireClick' => null,
])

<button
    type="button"
    @if ($wireClick) wire:click="{{ $wireClick }}" @endif
    {{ $attributes->class(['btn-base btn-cancel btn-sm w-full sm:w-auto']) }}>
    <x-menu.heroicon name="x-mark" class="h-4 w-4" />
    {{ $label }}
</button>
