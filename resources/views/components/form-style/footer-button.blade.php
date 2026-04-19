{{--
    Footer de formulario reutilizable — <x-form-style.footer-button>

    Renderiza el par Cancelar + Guardar al pie de cualquier form.
    Mobile-first: botones full-width en mobile, auto en sm+.

    Props:
        saveLabel    — texto del botón guardar      (default: "Guardar")
        cancelLabel  — texto del botón cancelar     (default: "Cancelar")
        saveIcon     — heroicon del botón guardar   (default: "document-check")
        cancelAction — método Livewire a llamar     (default: null)

    Uso básico:
        <x-form-style.footer-button />

    Con acción cancelar y label personalizado:
        <x-form-style.footer-button
            save-label="Crear Región"
            cancel-action="resetForm" />
--}}

@props([
    'saveLabel'    => 'Guardar',
    'cancelLabel'  => 'Cancelar',
    'saveIcon'     => 'document-check',
    'cancelAction' => null,
])

<div class="flex flex-col-reverse gap-2.5 border-t border-slate-100 bg-slate-50/50 px-8 py-4 dark:border-gray-800 dark:bg-gray-900/40 sm:flex-row sm:items-center sm:justify-end sm:gap-3">

    {{-- Cancelar --}}
    <button
        type="button"
        @if ($cancelAction) wire:click="{{ $cancelAction }}" @endif
        class="btn-base btn-cancel btn-sm w-full sm:w-auto">
        <x-menu.heroicon name="x-mark" class="h-3.5 w-3.5" />
        {{ $cancelLabel }}
    </button>




</div>
