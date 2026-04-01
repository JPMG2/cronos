<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name", "Cronos") }} — Iniciar Sesión</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])

        <!-- Aplica dark mode inmediatamente para evitar flash -->
        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                if (stored === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        <div
            class="flex min-h-screen items-center justify-center bg-indigo-50 p-4 dark:bg-gray-950"
            x-data="{
                dark: document.documentElement.classList.contains('dark'),
                setTheme(theme) {
                    this.dark = theme === 'dark'
                    localStorage.setItem('theme', theme)
                    document.documentElement.classList.toggle('dark', this.dark)
                },
            }">
            <!-- Selector de tema (solo desktop, fixed) -->
            <div
                class="fixed right-4 top-4 hidden items-center gap-1 rounded-xl bg-white/90 p-1 shadow-sm ring-1 ring-indigo-100 backdrop-blur-sm dark:bg-gray-900/90 dark:ring-gray-800 md:flex">
                <!-- Modo claro -->
                <button
                    @click="setTheme('light')"
                    title="Modo claro"
                    :class="!dark ? 'bg-indigo-100 text-indigo-700' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300'"
                    class="rounded-lg p-1.5 transition-all duration-200">
                    <x-menu.heroicon name="light-tem" class="h-3.5 w-3.5" />
                </button>
                <!-- Modo oscuro -->
                <button
                    @click="setTheme('dark')"
                    title="Modo oscuro"
                    :class="dark ? 'bg-gray-700 text-sky-400' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300'"
                    class="rounded-lg p-1.5 transition-all duration-200">
                    <x-menu.heroicon name="dark-tem" class="h-3.5 w-3.5" />
                </button>
            </div>

            <!-- Card principal -->
            <div
                class="flex w-full max-w-screen-md flex-col overflow-hidden rounded-2xl shadow-2xl shadow-indigo-400/60 ring-1 ring-indigo-100 dark:shadow-black/60 dark:ring-gray-800 md:flex-row">
                <!-- Panel izquierdo: branding -->
                {{-- Mobile: barra horizontal compacta | Desktop: columna completa --}}
                <div
                    class="flex items-center justify-between bg-indigo-600 px-6 py-5 dark:bg-indigo-900 md:w-80 md:flex-shrink-0 md:flex-col md:items-center md:justify-evenly md:px-8 md:py-10">
                    <!-- Logo + nombre (siempre visible) -->
                    <div class="flex items-center gap-3 md:flex-col md:gap-0 md:text-center">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white/20 md:h-14 md:w-14">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-white md:h-8 md:w-8"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold tracking-wide text-white md:mt-3 md:text-3xl">
                            {{ config("app.name", "Cronos") }}
                        </h1>
                    </div>

                    <!-- Selector de tema mobile: solo visible en móvil, dentro de la barra -->
                    <div class="flex items-center gap-1 rounded-xl bg-white/10 p-1 md:hidden">
                        <!-- Modo claro -->
                        <button
                            @click="setTheme('light')"
                            title="Modo claro"
                            :class="!dark ? 'bg-white/25 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white'"
                            class="rounded-lg p-1.5 transition-all duration-200">
                            <x-menu.heroicon name="light-tem" class="h-3.5 w-3.5" />
                        </button>
                        <!-- Modo oscuro -->
                        <button
                            @click="setTheme('dark')"
                            title="Modo oscuro"
                            :class="dark ? 'bg-white/25 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white'"
                            class="rounded-lg p-1.5 transition-all duration-200">
                            <x-menu.heroicon name="dark-tem" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <!-- Descripción: solo desktop -->
                    <p class="hidden text-center font-normal leading-relaxed text-indigo-200 md:block">
                        Sistema integral de gestión médica. Administra citas, laboratorio y turnos de forma eficiente y
                        segura.
                    </p>

                    <!-- Acceso: solo desktop -->
                    <div class="hidden md:flex md:flex-col md:items-center md:text-center">
                        <p class="text-sm text-indigo-200">¿Necesitas acceso al sistema?</p>
                        <p class="mt-1 text-sm font-semibold text-white">Contacta al administrador</p>
                    </div>

                    <!-- Términos: solo desktop -->
                    <p class="hidden text-center text-xs text-indigo-300 md:block">
                        Al acceder aceptas nuestros
                        <a href="#" class="underline transition-colors hover:text-white">términos</a>
                        y
                        <a href="#" class="underline transition-colors hover:text-white">condiciones</a>
                    </p>
                </div>

                <!-- Panel derecho: formulario -->
                <div class="flex-1 bg-white p-6 dark:bg-gray-900 md:p-8">
                    <h3 class="mb-5 text-xl font-semibold text-slate-800 dark:text-gray-100 md:mb-6 md:text-2xl">
                        Bienvenido de nuevo
                    </h3>

                    <!-- Session Status -->
                    @if (session("status"))
                        <div
                            class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mt-0.5 h-4 w-4 flex-shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>{{ session("status") }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route("login") }}" class="flex flex-col space-y-4 md:space-y-5">
                        @csrf

                        <!-- Correo electrónico -->
                        <x-form_imputs.text_imput
                            label="Correo electrónico"
                            name="email"
                            type="email"
                            placeholder="correo@ejemplo.com"
                            value="{{ old('email') }}"
                            autofocus
                            autocomplete="false"
                            required />

                        <!-- Contraseña -->
                        <x-form_imputs.text_imput
                            label="Contraseña"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="false"
                            required />
                        @if (Route::has("password.request"))
                            <div class="-mt-2 flex justify-end">
                                <a
                                    href="{{ route("password.request") }}"
                                    class="text-xs font-medium text-indigo-600 transition-colors hover:text-indigo-700 dark:text-sky-400 dark:hover:text-sky-300">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                        @endif

                        <!-- Recordarme -->
                        <div class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                id="remember_me"
                                name="remember"
                                class="h-4 w-4 rounded border-indigo-300 text-indigo-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-600 dark:bg-gray-800" />
                            <label for="remember_me" class="text-sm font-semibold text-slate-700 dark:text-gray-300">
                                Recordarme
                            </label>
                        </div>

                        <!-- Botón submit -->
                        <div class="pt-1">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition-all duration-200 hover:bg-indigo-700 hover:shadow-md hover:shadow-indigo-200/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-[0.98] dark:bg-indigo-600 dark:shadow-none dark:hover:bg-indigo-500 dark:focus:ring-indigo-500/30">
                                <x-menu.heroicon name="login" class="h-3.5 w-3.5" />
                                Iniciar sesión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Livewire carga Alpine.js; necesario en páginas standalone sin componentes Livewire --}}
        @livewireScripts
    </body>
</html>
