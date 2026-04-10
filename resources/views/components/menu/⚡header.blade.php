<?php

use Livewire\Component;

new class extends Component {
    public string $search = "";

    public function performSearch(): void
    {
    }
};
?>

{{--
    LIGHT : bg-indigo-50  — mismo tono que el sidebar, borde sutil abajo
    DARK  : bg-gray-900   — mismo tono que el sidebar, borde sutil abajo
    Todos los botones de la derecha usan la misma clase base para consistencia visual.
--}}
<div class="flex h-16 flex-shrink-0 items-center justify-between border-b border-indigo-100 bg-indigo-50 px-6 dark:border-gray-800 dark:bg-gray-900">

    {{-- Sidebar toggle — solo en desktop, mobile siempre muestra modo icono --}}
    <button
        type="button"
        @click="$dispatch('toggle-mobile-sidebar')"
        aria-label="Colapsar/expandir menú"
        class="mr-2 hidden rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 md:flex dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
        <x-menu.heroicon name="bars-3" class="h-5 w-5" />
    </button>

    {{-- Search Bar --}}
    <div class="max-w-sm flex-1">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-menu.heroicon
                    wire:loading.remove
                    wire:target="search"
                    name="magnifying-glass"
                    class="h-4 w-4 text-slate-400 dark:text-gray-600" />
                <svg
                    wire:loading
                    wire:target="search"
                    class="h-4 w-4 animate-spin text-indigo-400 dark:text-sky-500"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                aria-label="Buscar"
                class="block w-full rounded-xl border border-indigo-200/80 bg-white py-2 pl-9 pr-3 text-sm text-slate-700 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:placeholder-gray-600 dark:focus:border-sky-600/60 dark:focus:ring-sky-500/20"
                placeholder="Buscar..." />
        </div>
    </div>

    {{-- Right Side Icons — todos con la misma clase base para consistencia visual --}}
    <div class="ml-4 flex items-center gap-0.5">

        {{-- AI Assistant — único con color de acento propio --}}
        <button
            type="button"
            @click="$dispatch('open-ai-chat')"
            aria-label="Asistente IA"
            class="rounded-lg p-2 text-indigo-500 transition-colors hover:bg-indigo-100 hover:text-indigo-600 dark:text-sky-500 dark:hover:bg-gray-800 dark:hover:text-sky-400">
            <x-menu.heroicon name="sparkles" class="h-5 w-5" />
        </button>

        {{-- Calendar --}}
        <button
            type="button"
            aria-label="Calendario"
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
            <x-menu.heroicon name="calendar" class="h-5 w-5" />
        </button>

        {{-- Messages --}}
        <button
            type="button"
            aria-label="Mensajes"
            class="relative rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
            <x-menu.heroicon name="envelope" class="h-5 w-5" />
            <span class="absolute right-1.5 top-1.5 block h-1.5 w-1.5 rounded-full bg-rose-500 ring-2 ring-indigo-50 dark:ring-gray-900"></span>
        </button>

        {{-- Help --}}
        <button
            type="button"
            aria-label="Ayuda"
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
            <x-menu.heroicon name="question-mark-circle" class="h-5 w-5" />
        </button>

        {{-- Divisor --}}
        <span class="mx-1.5 h-4 w-px bg-indigo-200 dark:bg-gray-700"></span>

        {{-- Dark Mode Toggle --}}
        <button
            type="button"
            x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
            @click="
                darkMode = !darkMode;
                localStorage.setItem('darkMode', darkMode);
                darkMode
                    ? document.documentElement.classList.add('dark')
                    : document.documentElement.classList.remove('dark');
            "
            :aria-label="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
            :aria-pressed="darkMode"
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-amber-500 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-amber-400">
            <x-menu.heroicon x-show="!darkMode" name="moon" class="h-5 w-5" />
            <x-menu.heroicon x-show="darkMode" x-cloak name="sun" class="h-5 w-5" />
        </button>

        {{-- Profile Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open"
                aria-haspopup="menu"
                aria-label="Menú de usuario"
                class="flex items-center gap-2 rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                <x-menu.heroicon name="user-circle" class="h-6 w-6" />
                <x-menu.heroicon
                    name="chevron-down"
                    class="h-3 w-3 transition-transform duration-200 dark:text-gray-600"
                    :class="open ? 'rotate-180' : ''" />
            </button>

            <div
                x-show="open"
                @click.away="open = false"
                @keydown.escape.window="open = false"
                x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="scale-95 -translate-y-1 opacity-0"
                x-transition:enter-end="scale-100 translate-y-0 opacity-100"
                x-transition:leave="transition duration-100 ease-in"
                x-transition:leave-start="scale-100 translate-y-0 opacity-100"
                x-transition:leave-end="scale-95 -translate-y-1 opacity-0"
                role="menu"
                aria-orientation="vertical"
                class="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-xl bg-white py-1 shadow-lg shadow-slate-200/60 ring-1 ring-slate-200/80 dark:bg-gray-800 dark:shadow-black/30 dark:ring-gray-700/60"
                style="display: none">
                <div class="border-b border-slate-100 px-4 py-3 dark:border-gray-700/60">
                    <p class="text-xs font-semibold text-slate-800 dark:text-gray-200">{{ auth()->user()?->name ?? 'Usuario' }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">{{ auth()->user()?->email ?? '' }}</p>
                </div>
                <div class="py-1">
                    <a
                        href="{{ route('profile.edit') }}"
                        role="menuitem"
                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200">
                        <x-menu.heroicon name="user-circle" class="h-4 w-4 text-slate-400 dark:text-gray-600" />
                        Mi Perfil
                    </a>
                    <a
                        href="#"
                        role="menuitem"
                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200">
                        <x-menu.heroicon name="cog-6-tooth" class="h-4 w-4 text-slate-400 dark:text-gray-600" />
                        Configuración
                    </a>
                </div>
                <div class="border-t border-slate-100 py-1 dark:border-gray-700/60">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            role="menuitem"
                            class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-rose-500 transition-colors hover:bg-rose-50 hover:text-rose-600 dark:text-rose-400 dark:hover:bg-rose-500/10 dark:hover:text-rose-300">
                            <x-menu.heroicon name="arrow-right-on-rectangle" class="h-4 w-4" />
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
