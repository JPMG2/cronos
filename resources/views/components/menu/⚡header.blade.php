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
    Todos los botones de la derecha usan la misma clase base para ser consistentes.
--}}
<div class="flex h-16 flex-shrink-0 items-center justify-between border-b border-indigo-100 bg-indigo-50 px-6 dark:border-gray-800 dark:bg-gray-900">

    <!-- Mobile Menu Button -->
    <button
        @click="$dispatch('toggle-mobile-sidebar')"
        class="mr-2 rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 md:hidden dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Search Bar -->
    <div class="max-w-sm flex-1">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="block w-full rounded-xl border border-indigo-200/80 bg-white py-2 pl-9 pr-3 text-sm text-slate-700 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:placeholder-gray-600 dark:focus:border-sky-600/60 dark:focus:ring-sky-500/20"
                placeholder="Buscar..." />
        </div>
    </div>

    <!-- Right Side Icons — todos con la misma clase base para consistencia visual -->
    <div class="ml-4 flex items-center gap-0.5">

        <!-- Clase base compartida por todos los botones de ícono -->
        {{-- text-slate-500 hover:bg-indigo-100 hover:text-slate-700 --}}
        {{-- dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300 --}}

        <!-- AI Assistant — único con color de acento propio -->
        <button
            type="button"
            @click="$dispatch('open-ai-chat')"
            class="rounded-lg p-2 text-indigo-500 transition-colors hover:bg-indigo-100 hover:text-indigo-600 dark:text-sky-500 dark:hover:bg-gray-800 dark:hover:text-sky-400"
            title="Asistente IA">
            <x-menu.heroicon name="sparkles" class="h-5 w-5" />
        </button>

        <!-- Calendar -->
        <button
            type="button"
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            title="Calendario">
            <x-menu.heroicon name="calendar" class="h-5 w-5" />
        </button>

        <!-- Messages -->
        <button
            type="button"
            class="relative rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            title="Mensajes">
            <x-menu.heroicon name="envelope" class="h-5 w-5" />
            <span class="absolute right-1.5 top-1.5 block h-1.5 w-1.5 rounded-full bg-rose-500 ring-2 ring-indigo-50 dark:ring-gray-900"></span>
        </button>

        <!-- Help -->
        <button
            type="button"
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            title="Ayuda">
            <x-menu.heroicon name="question-mark-circle" class="h-5 w-5" />
        </button>

        <!-- Divisor -->
        <span class="mx-1.5 h-4 w-px bg-indigo-200 dark:bg-gray-700"></span>

        <!-- Dark Mode Toggle — mismo estilo base, hover amber para contexto visual -->
        <button
            type="button"
            x-data="{
                darkMode: localStorage.getItem('darkMode') === 'true' || false,
            }"
            x-init="
                if (darkMode) {
                    document.documentElement.classList.add('dark')
                } else {
                    document.documentElement.classList.remove('dark')
                }
            "
            @click="
                darkMode = !darkMode;
                localStorage.setItem('darkMode', darkMode);
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            "
            class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-amber-500 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-amber-400"
            title="Cambiar tema">
            <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <!-- Profile Dropdown — mismo estilo base que los demás botones -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                type="button"
                class="flex items-center gap-2 rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                <x-menu.heroicon name="user-circle" class="h-6 w-6" />
                <svg
                    class="h-3 w-3 transition-transform duration-200 dark:text-gray-600"
                    :class="open ? 'rotate-180' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="scale-95 -translate-y-1 opacity-0"
                x-transition:enter-end="scale-100 translate-y-0 opacity-100"
                x-transition:leave="transition duration-100 ease-in"
                x-transition:leave-start="scale-100 translate-y-0 opacity-100"
                x-transition:leave-end="scale-95 -translate-y-1 opacity-0"
                class="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-xl bg-white py-1 shadow-lg shadow-slate-200/60 ring-1 ring-slate-200/80 dark:bg-gray-800 dark:shadow-black/30 dark:ring-gray-700/60"
                style="display: none">
                <div class="border-b border-slate-100 px-4 py-3 dark:border-gray-700/60">
                    <p class="text-xs font-semibold text-slate-800 dark:text-gray-200">{{ auth()->user()?->name ?? 'Usuario' }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-400 dark:text-gray-500">{{ auth()->user()?->email ?? '' }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route("profile.edit") }}"
                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200">
                        <x-menu.heroicon name="user-circle" class="h-4 w-4 text-slate-400 dark:text-gray-600" />
                        Mi Perfil
                    </a>
                    <a href="#"
                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200">
                        <x-menu.heroicon name="cog-6-tooth" class="h-4 w-4 text-slate-400 dark:text-gray-600" />
                        Configuración
                    </a>
                </div>
                <div class="border-t border-slate-100 py-1 dark:border-gray-700/60">
                    <form method="POST" action="{{ route("logout") }}">
                        @csrf
                        <button type="submit"
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
