@props(['label' => ''])

<button type="button"
        {{ $attributes->class([
            'rounded-lg p-1.5 transition-all duration-200 active:scale-[0.98]',
            'text-rose-500 hover:bg-rose-100 hover:text-rose-700',
            'focus:outline-none focus:ring-2 focus:ring-inset focus:ring-rose-400/40',
            'dark:text-rose-400 dark:hover:bg-rose-500/15 dark:hover:text-rose-300 dark:focus:ring-rose-400/40',
        ]) }}
        aria-label="Eliminar {{ $label }}">
    <x-menu.heroicon name="trash" class="h-4 w-4" />
</button>
