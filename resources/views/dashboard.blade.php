<x-app-layout>

    {{-- ════════════════════════════════════════════════════════════════════
         DASHBOARD — "The Ethereal Clinical"
         Paleta: indigo-600 (light primary) / sky-400 (dark primary)
         Tipografía: font-headline (Plus Jakarta Sans) / font-body (Manrope)
         Layout: welcome → bento stats → tabla actividad + panel análisis
         ════════════════════════════════════════════════════════════════════ --}}

    <div class="flex flex-col gap-8 px-2 pb-4">

        {{-- ── 1. BIENVENIDA ──────────────────────────────────────────── --}}
        <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="font-label text-xs font-bold uppercase tracking-widest text-indigo-400 dark:text-sky-500">
                    {{ now()->format('l, d \d\e F') }}
                </p>
                <h2 class="mt-1 font-headline text-3xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                    Bienvenido de nuevo, {{ auth()->user()?->name ?? 'Doctor' }}
                </h2>
                <p class="mt-1.5 max-w-md text-sm text-slate-500 dark:text-gray-400">
                    Aquí tienes un resumen de la actividad médica de hoy.
                </p>
            </div>
            <button
                class="inline-flex items-center gap-2 rounded-xl signature-gradient px-5 py-3
                       font-headline text-sm font-bold text-white
                       shadow-[0px_8px_24px_rgba(79,70,229,0.20)] transition-all duration-200
                       hover:-translate-y-0.5 hover:shadow-[0px_12px_32px_rgba(79,70,229,0.30)]
                       active:scale-[0.98] dark:shadow-[0px_8px_24px_rgba(14,165,233,0.15)]
                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 dark:focus:ring-sky-400/40">
                <x-menu.heroicon name="plus" class="h-4 w-4" />
                Nueva cita
            </button>
        </section>

        {{-- ── 2. BENTO STATS ─────────────────────────────────────────── --}}
        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Card 1: Pacientes activos --}}
            <div class="card-surface flex flex-col justify-between p-6 h-40 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md cursor-default">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                        <x-menu.heroicon name="users" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-label text-xs font-bold text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                        +12%
                    </span>
                </div>
                <div>
                    <p class="font-headline text-4xl font-black text-slate-800 dark:text-gray-100">1,284</p>
                    <p class="mt-1 font-label text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">
                        Pacientes Activos
                    </p>
                </div>
            </div>

            {{-- Card 2: Citas hoy --}}
            <div class="card-surface flex flex-col justify-between p-6 h-40 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md cursor-default">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-900/30">
                        <x-menu.heroicon name="calendar" class="h-5 w-5 text-sky-600 dark:text-sky-400" />
                    </div>
                    <span class="rounded-full bg-sky-50 px-2.5 py-1 font-label text-xs font-bold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                        Hoy
                    </span>
                </div>
                <div>
                    <p class="font-headline text-4xl font-black text-slate-800 dark:text-gray-100">47</p>
                    <p class="mt-1 font-label text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">
                        Citas Programadas
                    </p>
                </div>
            </div>

            {{-- Card 3: Tasa de éxito --}}
            <div class="card-surface flex flex-col justify-between p-6 h-40 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md cursor-default">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                        <x-menu.heroicon name="check-circle" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 font-label text-xs font-bold text-slate-500 dark:bg-gray-800 dark:text-gray-400">
                        Estable
                    </span>
                </div>
                <div>
                    <p class="font-headline text-4xl font-black text-slate-800 dark:text-gray-100">98.2<span class="text-xl font-bold">%</span></p>
                    <p class="mt-1 font-label text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">
                        Tasa de Éxito
                    </p>
                </div>
            </div>

            {{-- Card 4: Carga semanal — con mini-chart gradient --}}
            <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl p-6 h-40 signature-gradient shadow-[0px_12px_32px_rgba(79,70,229,0.25)]">
                {{-- Orbe decorativo --}}
                <div class="pointer-events-none absolute -right-8 -bottom-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative z-10">
                    <p class="font-label text-[10px] font-black uppercase tracking-widest text-white/80">
                        Carga Semanal
                    </p>
                    <p class="mt-1.5 font-headline text-2xl font-black text-white">Optimizada</p>
                </div>
                {{-- Mini bar chart --}}
                <div class="relative z-10 flex items-end gap-1">
                    @foreach ([48, 64, 40, 80, 56, 96, 72] as $h)
                        <div class="flex-1 rounded-t-sm bg-white/30" style="height: {{ $h / 4 }}px;"></div>
                    @endforeach
                </div>
            </div>

        </section>

        {{-- ── 3. ACTIVIDAD + ANÁLISIS ────────────────────────────────── --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ── 3a. Tabla actividad reciente (2/3) ── --}}
            <div class="flex flex-col gap-5 lg:col-span-2">

                <div class="flex items-center justify-between px-1">
                    <h3 class="font-headline text-lg font-bold text-slate-800 dark:text-gray-100">Actividad Reciente</h3>
                    <a href="#" class="font-label text-sm font-semibold text-indigo-600 transition-colors hover:text-indigo-700 dark:text-sky-400 dark:hover:text-sky-300">
                        Ver todo
                    </a>
                </div>

                <div class="card-surface overflow-hidden">
                    {{-- Header tabla — sin línea divisoria, shift de fondo --}}
                    <div class="grid grid-cols-3 sm:grid-cols-4 bg-slate-50 px-6 py-4 dark:bg-gray-800/60">
                        <div class="col-span-2 font-label text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">
                            Paciente / Procedimiento
                        </div>
                        <div class="font-label text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Estado</div>
                        <div class="hidden sm:block text-right font-label text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Fecha</div>
                    </div>

                    {{-- Filas — separadas por espacio vertical, sin dividers --}}
                    <div>
                        @foreach ([
                            ['initials' => 'JD', 'name' => 'Julianne Doe',    'proc' => 'Cirugía Cardiovascular', 'status' => 'En Progreso', 'color' => 'indigo', 'date' => 'Hoy, 14:30'],
                            ['initials' => 'MS', 'name' => 'Marcus Sterling',  'proc' => 'Análisis de Laboratorio', 'status' => 'Completado',  'color' => 'sky',    'date' => 'Ayer, 09:15'],
                            ['initials' => 'AR', 'name' => 'Aria Rosales',     'proc' => 'Consulta General',        'status' => 'Cancelado',   'color' => 'rose',   'date' => '24 Oct, 18:00'],
                            ['initials' => 'PG', 'name' => 'Pedro González',   'proc' => 'Urgencias',               'status' => 'Completado',  'color' => 'emerald','date' => '23 Oct, 11:00'],
                        ] as $row)
                            @php
                                $statusCls = match($row['status']) {
                                    'En Progreso' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-400',
                                    'Completado'  => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400',
                                    'Cancelado'   => 'bg-rose-50 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400',
                                    default       => 'bg-slate-100 text-slate-500',
                                };
                                $avatarCls = match($row['color']) {
                                    'indigo'  => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400',
                                    'sky'     => 'bg-sky-100 text-sky-600 dark:bg-sky-900/50 dark:text-sky-400',
                                    'rose'    => 'bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400',
                                    'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400',
                                    default   => 'bg-slate-100 text-slate-500',
                                };
                            @endphp
                            <div class="grid grid-cols-3 sm:grid-cols-4 items-center px-6 py-5 transition-colors hover:bg-slate-50/70 dark:hover:bg-gray-800/40">
                                <div class="col-span-2 flex items-center gap-4">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl {{ $avatarCls }} font-headline text-sm font-bold">
                                        {{ $row['initials'] }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-gray-100">{{ $row['name'] }}</p>
                                        <p class="text-xs text-slate-400 dark:text-gray-500">{{ $row['proc'] }}</p>
                                    </div>
                                </div>
                                <div>
                                    <span class="rounded-full px-3 py-1 font-label text-[11px] font-bold {{ $statusCls }}">
                                        {{ $row['status'] }}
                                    </span>
                                </div>
                                <div class="hidden sm:block text-right">
                                    <p class="text-xs font-semibold text-slate-500 dark:text-gray-400">{{ $row['date'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ── 3b. Panel análisis (1/3) ── --}}
            <div class="flex flex-col gap-5">

                <div class="px-1">
                    <h3 class="font-headline text-lg font-bold text-slate-800 dark:text-gray-100">Análisis del Día</h3>
                </div>

                <div class="card-surface flex h-full flex-col gap-6 p-6">

                    {{-- Donut chart --}}
                    <div class="flex flex-col items-center justify-center pt-4">
                        <div class="relative flex h-44 w-44 items-center justify-center">
                            <svg class="h-full w-full -rotate-90" viewBox="0 0 100 100">
                                {{-- Track --}}
                                <circle cx="50" cy="50" r="38" fill="transparent"
                                    class="text-slate-100 dark:text-gray-800"
                                    stroke="currentColor" stroke-width="10" />
                                {{-- Segmento indigo (citas) --}}
                                <circle cx="50" cy="50" r="38" fill="transparent"
                                    class="text-indigo-600 dark:text-indigo-400"
                                    stroke="currentColor" stroke-width="10"
                                    stroke-dasharray="239"
                                    stroke-dashoffset="60"
                                    stroke-linecap="round" />
                                {{-- Segmento sky (laboratorio) --}}
                                <circle cx="50" cy="50" r="38" fill="transparent"
                                    class="text-sky-400 dark:text-sky-500"
                                    stroke="currentColor" stroke-width="10"
                                    stroke-dasharray="239"
                                    stroke-dashoffset="180"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <p class="font-headline text-3xl font-black text-slate-800 dark:text-gray-100">74%</p>
                                <p class="font-label text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-gray-500">Eficiencia</p>
                            </div>
                        </div>
                    </div>

                    {{-- Leyenda + barras --}}
                    <div class="space-y-5">
                        {{-- Ítem 1 --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                                    <span class="font-label text-sm font-semibold text-slate-700 dark:text-gray-300">Citas</span>
                                </div>
                                <span class="font-label text-sm font-bold text-slate-500 dark:text-gray-400">54%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-indigo-600 dark:bg-indigo-400" style="width: 54%"></div>
                            </div>
                        </div>
                        {{-- Ítem 2 --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-2.5 rounded-full bg-sky-400 dark:bg-sky-500"></div>
                                    <span class="font-label text-sm font-semibold text-slate-700 dark:text-gray-300">Laboratorio</span>
                                </div>
                                <span class="font-label text-sm font-bold text-slate-500 dark:text-gray-400">32%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-sky-400 dark:bg-sky-500" style="width: 32%"></div>
                            </div>
                        </div>
                        {{-- Ítem 3 --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></div>
                                    <span class="font-label text-sm font-semibold text-slate-700 dark:text-gray-300">Urgencias</span>
                                </div>
                                <span class="font-label text-sm font-bold text-slate-500 dark:text-gray-400">14%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-emerald-500 dark:bg-emerald-400" style="width: 14%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Nota de insight --}}
                    <div class="mt-auto rounded-xl bg-indigo-50 p-4 dark:bg-indigo-900/20">
                        <p class="text-xs italic leading-relaxed text-indigo-700 dark:text-indigo-300">
                            "El flujo de pacientes aumentó un 15% este mes. Se recomienda ampliar turnos vespertinos."
                        </p>
                    </div>
                </div>

            </div>

        </section>

    </div>

</x-app-layout>
