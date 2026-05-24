<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Acceso Restringido — {{ config('app.name') }}</title>

    {{-- Dark mode init — antes del CSS para evitar flash --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Orbes de fondo — flotan lentamente ─── */
        @keyframes orb-drift-1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -40px) scale(1.05); }
            66%       { transform: translate(-20px, 20px) scale(0.97); }
        }
        @keyframes orb-drift-2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            40%       { transform: translate(-35px, 30px) scale(1.04); }
            70%       { transform: translate(25px, -20px) scale(0.98); }
        }
        @keyframes orb-drift-3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(20px, 35px) scale(1.06); }
        }

        /* ── Entrada del card ──────────────────── */
        @keyframes card-enter {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Pulso suave del escudo ────────────── */
        @keyframes shield-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.25); }
            50%       { box-shadow: 0 0 0 14px rgba(239, 68, 68, 0); }
        }
        @keyframes ring-expand {
            0%   { transform: scale(0.85); opacity: 0.6; }
            100% { transform: scale(1.5);  opacity: 0; }
        }

        .orb-1 { animation: orb-drift-1 18s ease-in-out infinite; }
        .orb-2 { animation: orb-drift-2 22s ease-in-out infinite; }
        .orb-3 { animation: orb-drift-3 15s ease-in-out infinite; }

        .card-enter { animation: card-enter 0.55s cubic-bezier(0.16, 1, 0.3, 1) both; }

        .shield-icon { animation: shield-pulse 2.8s ease-in-out infinite; }

        .ring-1 {
            animation: ring-expand 2.4s ease-out infinite;
        }
        .ring-2 {
            animation: ring-expand 2.4s ease-out 0.8s infinite;
        }

        /* ── Grid sutil de fondo ──────────────── */
        .bg-grid {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .dark .bg-grid {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
        }

        @media (prefers-reduced-motion: reduce) {
            .orb-1, .orb-2, .orb-3 { animation: none; }
            .card-enter             { animation: none; }
            .shield-icon            { animation: none; }
            .ring-1, .ring-2        { animation: none; }
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="relative min-h-screen overflow-hidden bg-slate-50 dark:bg-gray-950">

    {{-- ── Grid de fondo ────────────────────────────────────────────── --}}
    <div class="bg-grid pointer-events-none absolute inset-0"></div>

    {{-- ── Orbes de fondo ────────────────────────────────────────────── --}}
    <div class="orb-1 pointer-events-none absolute -left-32 -top-32 h-[480px] w-[480px] rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-600/15"></div>
    <div class="orb-2 pointer-events-none absolute -bottom-24 -right-24 h-[560px] w-[560px] rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-600/15"></div>
    <div class="orb-3 pointer-events-none absolute right-1/3 top-1/3 h-[300px] w-[300px] rounded-full bg-sky-400/8 blur-3xl dark:bg-sky-500/10"></div>
    <div class="pointer-events-none absolute bottom-1/4 left-1/4 h-[200px] w-[200px] rounded-full bg-rose-400/6 blur-2xl dark:bg-rose-500/8"></div>

    {{-- ── Contenido centrado ─────────────────────────────────────────── --}}
    <div class="relative flex min-h-screen items-center justify-center px-4 py-12">

        <div class="card-enter w-full max-w-md">

            {{-- ── Card principal ──────────────────────────────────────── --}}
            <div class="glass-panel elevation-4 rounded-3xl px-8 py-10">

                {{-- Badge marca --}}
                <div class="mb-8 flex justify-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/60 bg-indigo-50/90 px-4 py-1.5 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                        <span class="live-dot"></span>
                        <span class="font-label text-[11px] font-bold uppercase tracking-widest text-indigo-700 dark:text-sky-400">
                            {{ config('app.name') }}
                        </span>
                    </div>
                </div>

                {{-- Ícono escudo con anillos ─────────────────────────── --}}
                <div class="relative mx-auto mb-6 flex h-28 w-28 items-center justify-center">

                    {{-- Anillos expansivos --}}
                    <div class="ring-1 absolute inset-0 rounded-full border-2 border-rose-400/30 dark:border-rose-500/25"></div>
                    <div class="ring-2 absolute inset-0 rounded-full border-2 border-rose-400/20 dark:border-rose-500/15"></div>

                    {{-- Círculo base --}}
                    <div class="absolute inset-3 rounded-full bg-rose-50 dark:bg-rose-500/10"></div>

                    {{-- Contenedor del ícono --}}
                    <div class="shield-icon relative z-10 flex h-16 w-16 items-center justify-center rounded-2xl
                                bg-white shadow-lg shadow-rose-500/20
                                dark:bg-gray-900 dark:shadow-rose-500/15"
                         style="border: 1px solid rgba(239,68,68,0.2)">
                        {{-- shield-exclamation --}}
                        <svg class="h-8 w-8 text-rose-500 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>

                {{-- Número 403 ───────────────────────────────────────── --}}
                <div class="mb-1 text-center">
                    <span class="font-headline select-none bg-gradient-to-br from-rose-500 to-rose-700
                                 bg-clip-text text-transparent
                                 dark:from-rose-400 dark:to-rose-600"
                          style="font-size: 4.5rem; font-weight: 800; line-height: 1; letter-spacing: -2px;">
                        403
                    </span>
                </div>

                {{-- Título ──────────────────────────────────────────── --}}
                <h1 class="font-headline mb-2 text-center text-2xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                    Acceso Restringido
                </h1>

                {{-- Descripción ──────────────────────────────────────── --}}
                <p class="mb-6 text-center text-sm leading-relaxed text-slate-500 dark:text-gray-400">
                    Tu cuenta no tiene los permisos necesarios para acceder a esta sección del sistema Cronos.
                </p>

                {{-- Info de ruta bloqueada ───────────────────────────── --}}
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50/70 px-4 py-3
                            dark:border-rose-500/20 dark:bg-rose-500/8">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Sección bloqueada</p>
                        <p class="mt-0.5 truncate font-mono text-xs text-rose-500/80 dark:text-rose-400/70">
                            /{{ request()->path() }}
                        </p>
                    </div>
                </div>

                {{-- Info del usuario autenticado ─────────────────────── --}}
                @auth
                    <div class="mb-6 flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white/60 px-4 py-3
                                dark:border-gray-700/60 dark:bg-gray-800/50">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                    bg-indigo-100 text-xs font-bold text-indigo-700
                                    dark:bg-indigo-500/20 dark:text-sky-400">
                            {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-slate-700 dark:text-gray-300">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="truncate text-[10px] text-slate-400 dark:text-gray-500">
                                @php
                                    $roles = Auth::user()->getRoleNames()->implode(', ');
                                @endphp
                                {{ $roles ?: 'Sin rol asignado' }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="pill-neutral">
                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                Sesión activa
                            </span>
                        </div>
                    </div>
                @endauth

                {{-- Acciones ─────────────────────────────────────────── --}}
                <div class="flex flex-col gap-2.5 sm:flex-row">
                    <a href="{{ route('dashboard') }}"
                       class="btn-base btn-save btn-md flex-1 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Ir al Dashboard
                    </a>
                    <button type="button"
                            onclick="history.back()"
                            class="btn-base btn-secondary btn-md flex-1 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Volver atrás
                    </button>
                </div>

                {{-- Separador + nota final ────────────────────────────── --}}
                <div class="mt-7 flex items-center gap-3 border-t border-slate-100 pt-5 dark:border-gray-800">
                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-300 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-[11px] leading-relaxed text-slate-400 dark:text-gray-600">
                        Si creés que esto es un error, contactá al administrador del sistema para que revise los permisos de tu rol.
                    </p>
                </div>

            </div>{{-- /card --}}

            {{-- Timestamp ──────────────────────────────────────────── --}}
            <p class="mt-4 text-center font-mono text-[10px] text-slate-300 dark:text-gray-700">
                {{ now()->format('d/m/Y H:i:s') }} · HTTP 403 Forbidden
            </p>

        </div>{{-- /max-w-md --}}
    </div>{{-- /flex --}}

</div>{{-- /relative --}}

</body>
</html>
