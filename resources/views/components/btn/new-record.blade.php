<button type="button"
        {{ $attributes->class(['hidden shrink-0 items-center gap-1.5 rounded-xl border border-indigo-200 bg-white px-2.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50 active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:border-gray-600 dark:hover:bg-gray-700/60 sm:flex']) }}>
    <x-menu.heroicon name="plus-circle" class="h-3.5 w-3.5" />
    {{ $label ?? 'Nuevo' }}
</button>