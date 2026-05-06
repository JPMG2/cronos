@props(['label' => ''])

<button type="button"
        {{ $attributes->class([
            'rounded-lg p-1.5 transition-all duration-200 active:scale-[0.98]',
            'text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700',
            'focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400/40',
            'dark:text-sky-400 dark:hover:bg-indigo-500/15 dark:hover:text-sky-300 dark:focus:ring-sky-400/40',
        ]) }}
        aria-label="Editar {{ $label }}">
    <x-menu.heroicon name="pencil" class="h-4 w-4" />
</button>
