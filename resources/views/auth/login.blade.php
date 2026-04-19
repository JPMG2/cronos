<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name", "Cronos") }} — Iniciar Sesión</title>

        {{-- Dark mode init antes del CSS para evitar flash --}}
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap"
            rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>

    <body
        class="min-h-screen bg-indigo-50 font-body antialiased dark:bg-gray-950"
        x-data="{
            dark: document.documentElement.classList.contains('dark'),
            loading: false,
            setTheme(isDark) {
                this.dark = isDark
                localStorage.setItem('darkMode', isDark)
                document.documentElement.classList.toggle('dark', isDark)
            },
        }">
        <div class="flex min-h-screen">
            {{--
                ════════════════════════════════════════════════════════════
                PANEL IZQUIERDO — Branding (solo desktop)
                ════════════════════════════════════════════════════════════
            --}}
            <section
                class="signature-gradient relative hidden flex-col items-center justify-center overflow-hidden p-10 lg:flex lg:w-[52%] xl:p-14">
                {{-- Orbes decorativos --}}
                <div
                    class="pointer-events-none absolute -left-32 -top-32 h-[500px] w-[500px] rounded-full bg-white/10 blur-[130px]"></div>
                <div
                    class="pointer-events-none absolute -bottom-24 -right-24 h-[420px] w-[420px] rounded-full bg-indigo-950/25 blur-[110px]"></div>
                <div
                    class="pointer-events-none absolute left-1/2 top-1/2 h-[300px] w-[300px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-[80px]"></div>

                <div class="relative z-10 w-full max-w-lg">
                    {{-- Logo --}}
                    <div class="mb-8 flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 shadow-lg backdrop-blur-md">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
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
                        <h1 class="font-headline text-4xl font-extrabold tracking-tight text-white">
                            {{ config("app.name", "Cronos") }}
                        </h1>
                    </div>

                    {{-- Titular --}}
                    <div class="space-y-5">
                        <h2 class="font-headline text-4xl font-bold leading-[1.1] tracking-tight text-white">
                            Sistema integral de gestión médica.
                        </h2>
                        <p class="text-xl font-medium leading-relaxed text-indigo-200">
                            Administra citas, laboratorio y turnos de forma eficiente y segura.
                        </p>
                    </div>

                    {{-- Feature list --}}
                    <div class="mt-8 space-y-3">
                        @foreach ([
                                ["icon" => "calendar", "text" => "Gestión de citas y turnos"],
                                ["icon" => "beaker", "text" => "Módulo de laboratorio integrado"],
                                ["icon" => "shield-check", "text" => "Acceso seguro por roles"]
                            ]
                            as $feat)
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-white/15">
                                    <x-menu.heroicon name="{{ $feat['icon'] }}" class="h-4 w-4 text-white" />
                                </div>
                                <span class="text-sm font-medium text-indigo-100">{{ $feat["text"] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Trust badges --}}
                    <div class="mt-8 flex items-center gap-8 text-indigo-300/80">
                        <div class="flex items-center gap-2">
                            <x-menu.heroicon name="lock-closed" class="h-3.5 w-3.5" />
                            <span class="font-label text-xs font-semibold uppercase tracking-wider">
                                256-bit Encryption
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-menu.heroicon name="shield-check" class="h-3.5 w-3.5" />
                            <span class="font-label text-xs font-semibold uppercase tracking-wider">Datos seguros</span>
                        </div>
                    </div>
                </div>
            </section>

            {{--
                ════════════════════════════════════════════════════════════
                PANEL DERECHO — Formulario de login
                ════════════════════════════════════════════════════════════
            --}}
            <main
                class="relative flex w-full items-center justify-center bg-white px-6 py-8 dark:bg-gray-900 sm:px-12 sm:py-8 md:px-16 md:py-6 lg:w-[48%]">
                {{-- Toggle tema desktop --}}
                <div
                    class="absolute right-6 top-6 hidden items-center gap-1 rounded-xl bg-slate-100 p-1 shadow-sm dark:bg-gray-800 md:flex">
                    <button
                        type="button"
                        @click="setTheme(false)"
                        aria-label="Modo claro"
                        :class="!dark ? 'bg-white shadow-sm text-indigo-600 dark:bg-gray-700' : 'text-slate-400 hover:text-slate-600 dark:text-gray-500'"
                        class="rounded-lg p-2 transition-all duration-200">
                        <x-menu.heroicon name="sun" class="h-4 w-4" />
                    </button>
                    <button
                        type="button"
                        @click="setTheme(true)"
                        aria-label="Modo oscuro"
                        :class="dark ? 'bg-gray-700 shadow-sm text-sky-400' : 'text-slate-400 hover:text-slate-600 dark:text-gray-500'"
                        class="rounded-lg p-2 transition-all duration-200">
                        <x-menu.heroicon name="moon" class="h-4 w-4" />
                    </button>
                </div>

                <div class="w-full max-w-md space-y-5">
                    {{-- ── Header móvil ── --}}
                    <div class="flex items-center justify-between lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="signature-gradient flex h-9 w-9 items-center justify-center rounded-xl">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
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
                            <span class="font-headline text-xl font-bold text-indigo-600 dark:text-sky-400">
                                {{ config("app.name", "Cronos") }}
                            </span>
                        </div>
                        {{-- Toggle tema móvil --}}
                        <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1 dark:bg-gray-800">
                            <button
                                type="button"
                                @click="setTheme(false)"
                                :class="!dark ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                                class="rounded-lg p-1.5 transition-all duration-200">
                                <x-menu.heroicon name="sun" class="h-3.5 w-3.5" />
                            </button>
                            <button
                                type="button"
                                @click="setTheme(true)"
                                :class="dark ? 'bg-gray-700 shadow-sm text-sky-400' : 'text-slate-400'"
                                class="rounded-lg p-1.5 transition-all duration-200">
                                <x-menu.heroicon name="moon" class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>

                    {{-- ── Cabecera del formulario ── --}}
                    <div class="space-y-2">
                        <h3
                            class="font-headline text-2xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                            Bienvenido de nuevo
                        </h3>
                        <p class="text-sm font-medium text-slate-500 dark:text-gray-400">
                            Ingresa tus credenciales para continuar.
                        </p>
                    </div>

                    {{-- ── Session status ── --}}
                    @if (session("status"))
                        <div
                            class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <x-menu.heroicon name="check-circle" class="mt-0.5 h-4 w-4 shrink-0" />
                            <p>{{ session("status") }}</p>
                        </div>
                    @endif

                    {{-- ── Formulario ── --}}
                    <form
                        method="POST"
                        action="{{ route("login") }}"
                        class="space-y-4"
                        @submit.prevent="loading = true; $el.submit()">
                        @csrf

                        {{-- Email --}}
                        <x-form-inputs.text_input
                            label="Correo electrónico"
                            name="email"
                            type="email"
                            icon="envelope"
                            placeholder="correo@ejemplo.com"
                            :value="old('email')"
                            autocomplete="email"
                            autofocus
                            required />

                        {{-- Contraseña --}}
                        <x-form-inputs.text_input
                            label="Contraseña"
                            name="password"
                            type="password"
                            icon="lock-closed"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required />

                        {{-- Recordarme + olvidaste contraseña --}}
                        <div class="flex items-center justify-between">
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input
                                    type="checkbox"
                                    id="remember_me"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-300 bg-white text-indigo-600 transition-all focus:ring-2 focus:ring-indigo-400/25 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-sky-400/25" />
                                <span class="text-sm font-medium text-slate-600 dark:text-gray-400">Recordarme</span>
                            </label>
                            @if (Route::has("password.request"))
                                <a
                                    href="{{ route("password.request") }}"
                                    class="text-sm font-semibold text-indigo-600 transition-colors hover:text-indigo-700 dark:text-sky-400 dark:hover:text-sky-300">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botón submit --}}
                        <button
                            type="submit"
                            :disabled="loading"
                            :class="loading
                            ? 'opacity-70 cursor-not-allowed'
                            : 'hover:-translate-y-0.5 hover:shadow-[0px_16px_40px_rgba(79,70,229,0.30)] dark:hover:shadow-[0px_16px_40px_rgba(14,165,233,0.25)]'"
                            class="signature-gradient inline-flex w-full items-center justify-center gap-2 rounded-2xl px-6 py-3 font-headline text-sm font-bold text-white shadow-[0px_12px_32px_rgba(79,70,229,0.20)] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-[0.98] dark:focus:ring-sky-400/40">
                            <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 12 6.477 12 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <x-menu.heroicon x-show="!loading" name="login" class="h-5 w-5" />
                            <span x-show="!loading">Iniciar sesión</span>
                            <span x-show="loading">Iniciando sesión…</span>
                        </button>
                    </form>

                    {{-- Footer --}}
                    <p class="text-center text-sm text-slate-500 dark:text-gray-400">
                        ¿Necesitas acceso?
                        <a
                            href="#"
                            class="font-semibold text-indigo-600 transition-colors hover:text-indigo-700 dark:text-sky-400 dark:hover:text-sky-300">
                            Contacta al administrador
                        </a>
                    </p>
                </div>
            </main>
        </div>

        {{-- Livewire necesario en páginas sin componentes Livewire para cargar Alpine --}}
        @livewireScripts
    </body>
</html>
