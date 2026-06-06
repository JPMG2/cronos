<?php
declare(strict_types=1);

use Livewire\Component;

new class extends Component {};
?>

<x-form-style.border-style>
    <x-form-style.main-div>

        <x-form-style.header-form
            title="Aseguradoras y Prepagas"
            description="Catálogo maestro de obras sociales y prepagas con sus planes y coberturas asociadas."
            sign="6 aseguradoras"/>

        {{-- ════════════════════════════════════════════════════════════════════
             WORKSPACE — 2 columnas: lista | main
             ════════════════════════════════════════════════════════════════════ --}}
        <div class="grid min-h-[700px] grid-cols-1 lg:grid-cols-[220px_1fr]"
             x-data="{
                 activeTab: 'datos',
                 activeInsurer: 'osde',
                 activePlan: 'plan1',
                 insurers: [
                     { id: 'osde',  name: 'OSDE',              code: 'OSDE', status: 1, plans: 4, color: '#1d4ed8', bg: '#dbeafe' },
                     { id: 'gale',  name: 'Galeno Argentina',  code: 'GALE', status: 1, plans: 3, color: '#0f766e', bg: '#ccfbf1' },
                     { id: 'swis',  name: 'Swiss Medical',     code: 'SWIS', status: 1, plans: 5, color: '#7c3aed', bg: '#ede9fe' },
                     { id: 'acco',  name: 'Accord Salud',      code: 'ACCO', status: 2, plans: 2, color: '#b45309', bg: '#fef3c7' },
                     { id: 'iosp',  name: 'IOSPER',            code: 'IOSP', status: 9, plans: 3, color: '#0369a1', bg: '#e0f2fe' },
                     { id: 'pami',  name: 'PAMI',              code: 'PAMI', status: 1, plans: 2, color: '#15803d', bg: '#dcfce7' },
                 ],
                 plans: [
                     { id: 'plan1', name: 'OSDE 210',   code: '210',  desc: 'Plan básico',     members: 1240, coverages: 18 },
                     { id: 'plan2', name: 'OSDE 310',   code: '310',  desc: 'Plan intermedio',  members: 876,  coverages: 24 },
                     { id: 'plan3', name: 'OSDE 410',   code: '410',  desc: 'Plan plus',        members: 542,  coverages: 31 },
                     { id: 'plan4', name: 'OSDE Bonus', code: 'BONX', desc: 'Plan premium',     members: 234,  coverages: 42 },
                 ],
                 coverages: [
                     { cat: 'Consultas médicas', pct: 100, copay: 0,    max: null,  auth: false, active: true  },
                     { cat: 'Laboratorio',        pct: 80,  copay: 1500, max: 50000, auth: false, active: true  },
                     { cat: 'Internación',        pct: 100, copay: 0,    max: null,  auth: true,  active: true  },
                     { cat: 'Medicamentos',       pct: 70,  copay: 0,    max: 30000, auth: false, active: true  },
                     { cat: 'Cirugía',            pct: 100, copay: 0,    max: null,  auth: true,  active: true  },
                     { cat: 'Prácticas Ambul.',   pct: 80,  copay: 2000, max: 80000, auth: true,  active: false },
                 ],
                 get activeInsurerData() {
                     return this.insurers.find(i => i.id === this.activeInsurer) || this.insurers[0];
                 },
                 get activePlanData() {
                     return this.plans.find(p => p.id === this.activePlan) || this.plans[0];
                 },
                 statusDot(status) {
                     const map = { 1: 'bg-emerald-500', 2: 'bg-amber-500', 9: 'bg-sky-500', 10: 'bg-rose-500' };
                     return map[status] || 'bg-slate-400';
                 }
             }">

            {{-- ══════════════════════════════════════════════════════════════
                 COL 1 — Lista lateral de aseguradoras (220 px)
                 ══════════════════════════════════════════════════════════════ --}}
            <aside class="flex flex-col border-r border-slate-100 bg-slate-50/60 px-3 py-4 dark:border-gray-800 dark:bg-gray-900/30">

                {{-- Label + botón nueva --}}
                <div class="mb-3 flex items-center justify-between px-1">
                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                        Aseguradoras
                    </span>
                    <button type="button"
                            class="rounded-md p-1 text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-sky-500/10"
                            aria-label="Nueva aseguradora">
                        <x-menu.heroicon name="plus" class="h-3.5 w-3.5"/>
                    </button>
                </div>

                {{-- Campo de búsqueda --}}
                <div class="mb-3 px-1">
                    <x-form-inputs.text_input
                        label="Buscar"
                        name="search_insurer"
                        icon="magnifying-glass"
                        placeholder="Buscar aseguradora…"
                        size="sm"/>
                </div>

                {{-- Lista scrolleable --}}
                <div class="flex-1 space-y-1 overflow-y-auto pr-1">
                    <template x-for="ins in insurers" :key="ins.id">
                        <button type="button"
                                @click="activeInsurer = ins.id; activeTab = 'datos'; activePlan = 'plan1'"
                                :class="activeInsurer === ins.id
                                    ? 'bg-white shadow-sm dark:bg-gray-800'
                                    : 'hover:bg-white/50 dark:hover:bg-gray-800/50'"
                                class="relative flex w-full items-center gap-2.5 rounded-lg p-2 text-left transition-all duration-150">

                            {{-- Barra indicadora de activo --}}
                            <span x-show="activeInsurer === ins.id"
                                  x-transition:enter="transition-all duration-200"
                                  class="absolute -left-3 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r"
                                  :style="`background: ${ins.color}`"></span>

                            {{-- Badge logo --}}
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-headline text-[9px] font-extrabold"
                                 :style="`background: ${ins.bg}; color: ${ins.color}`"
                                 x-text="ins.code.slice(0,4)"></div>

                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-bold transition-colors"
                                     :class="activeInsurer === ins.id
                                        ? 'text-indigo-700 dark:text-sky-300'
                                        : 'text-slate-700 dark:text-gray-200'"
                                     x-text="ins.name"></div>
                                <div class="text-[9px] text-slate-400 dark:text-gray-500">
                                    <span x-text="ins.plans"></span> planes
                                </div>
                            </div>

                            {{-- Dot de estado --}}
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full" :class="statusDot(ins.status)"></span>
                        </button>
                    </template>
                </div>
            </aside>

            {{-- ══════════════════════════════════════════════════════════════
                 COL 2 — Workspace principal
                 ══════════════════════════════════════════════════════════════ --}}
            <main class="overflow-y-auto">

                {{-- ─── TABS (orden: datos → planes → historial) ─── --}}
                <div class="border-b border-slate-200 px-5 dark:border-gray-800">
                    <div class="flex items-center gap-0">

                        <button type="button"
                                @click="activeTab = 'datos'"
                                :class="activeTab === 'datos'
                                    ? 'border-indigo-600 text-indigo-700 dark:border-sky-400 dark:text-sky-300'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200'"
                                class="-mb-px flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition-colors">
                            <x-menu.heroicon name="building-office" class="h-4 w-4"/>
                            Datos generales
                        </button>

                        <button type="button"
                                @click="activeTab = 'planes'"
                                :class="activeTab === 'planes'
                                    ? 'border-indigo-600 text-indigo-700 dark:border-sky-400 dark:text-sky-300'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200'"
                                class="-mb-px flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition-colors">
                            <x-menu.heroicon name="document-text" class="h-4 w-4"/>
                            Planes y coberturas
                        </button>

                        <button type="button"
                                @click="activeTab = 'historial'"
                                :class="activeTab === 'historial'
                                    ? 'border-indigo-600 text-indigo-700 dark:border-sky-400 dark:text-sky-300'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200'"
                                class="-mb-px flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition-colors">
                            <x-menu.heroicon name="clock" class="h-4 w-4"/>
                            Historial
                        </button>

                    </div>
                </div>

                {{-- ─── CONTENIDO DE TABS ─── --}}
                <div class="p-5">

                    {{-- ── TAB 1: DATOS GENERALES ──────────────────────────── --}}
                    <div x-show="activeTab === 'datos'">
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                            {{-- Card 01: Identificación --}}
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <x-form-style.number-tag number="01" label="Identificación"/>
                                <div class="space-y-3">
                                    <x-form-inputs.text_input
                                        label="Nombre de la Aseguradora"
                                        name="name"
                                        icon="building-office"
                                        placeholder="Ej: OSDE, Swiss Medical…"
                                        value="OSDE"
                                        size="md"
                                        required/>
                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <div class="sm:w-36 sm:shrink-0">
                                            <x-form-inputs.text_input
                                                label="Código"
                                                name="code"
                                                icon="key"
                                                placeholder="OSDE"
                                                value="OSDE"
                                                size="md"
                                                required/>
                                        </div>
                                        <div class="flex-1">
                                            <x-form-inputs.text_input
                                                label="CUIT"
                                                name="cuit"
                                                icon="identification"
                                                placeholder="30-12345678-9"
                                                value="30-70017081-9"
                                                size="md"
                                                required/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 02: Ubicación --}}
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <x-form-style.number-tag number="02" label="Ubicación"/>
                                <div class="space-y-3">
                                    <x-form-inputs.autocomplete
                                        label="Región"
                                        name="regionId"
                                        placeholder="Seleccionar región…"
                                        :options="[
                                            ['value' => 1, 'label' => 'Neuquén Capital'],
                                            ['value' => 2, 'label' => 'Plottier'],
                                            ['value' => 3, 'label' => 'Centenario'],
                                            ['value' => 4, 'label' => 'Cipolletti'],
                                        ]"
                                        :value="1"
                                        required/>
                                    <x-form-inputs.text_input
                                        label="Dirección"
                                        name="address"
                                        icon="map"
                                        placeholder="Calle, número, ciudad"
                                        value="Av. Olascoaga 1702, Neuquén"
                                        required/>
                                </div>
                            </div>

                            {{-- Card 03: Contacto --}}
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <x-form-style.number-tag number="03" label="Contacto"/>
                                <div class="space-y-3">
                                    <x-form-inputs.text_input
                                        label="Teléfono"
                                        name="phone"
                                        type="tel"
                                        icon="phone"
                                        placeholder="0810 555 6337"
                                        value="0810 555 6337"
                                        size="md"
                                        required/>
                                    <x-form-inputs.text_input
                                        label="Sitio Web"
                                        name="website"
                                        type="url"
                                        icon="globe-alt"
                                        placeholder="www.osde.com.ar"
                                        value="www.osde.com.ar"
                                        size="md"/>
                                    <x-form-inputs.text_input
                                        label="Correo Electrónico"
                                        name="email"
                                        type="email"
                                        icon="envelope"
                                        placeholder="contacto@osde.com.ar"
                                        value="contacto@osde.com.ar"
                                        size="md"
                                        required/>
                                </div>
                            </div>

                            {{-- Card 04: Logo y Estado --}}
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <x-form-style.number-tag number="04" label="Logo y Estado"/>
                                <div class="flex gap-5">

                                    {{-- Logo uploader --}}
                                    <div class="flex shrink-0 flex-col items-center gap-2">
                                        <span class="self-start text-xs font-semibold text-slate-400 dark:text-gray-500">Logo</span>
                                        <div class="group relative flex h-[7.5rem] w-[7.5rem] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-indigo-200/80 bg-white transition-colors hover:border-indigo-400 hover:bg-indigo-50/50 dark:border-gray-700 dark:bg-gray-800/60 dark:hover:border-indigo-600/60"
                                             role="button"
                                             aria-label="Subir logo de aseguradora">
                                            {{-- Mockup: logo placeholder brand --}}
                                            <div class="flex h-14 w-14 items-center justify-center rounded-xl font-headline text-xl font-extrabold"
                                                 style="background: #dbeafe; color: #1d4ed8;">OS</div>
                                            <span class="mt-1 text-[10px] font-medium text-indigo-300 dark:text-indigo-600">PNG · SVG</span>
                                        </div>
                                    </div>

                                    {{-- Estado --}}
                                    <div class="flex flex-1 flex-col gap-3">
                                        <x-form-inputs.select
                                            label="Estado"
                                            name="currentStatusId"
                                            icon="check-circle"
                                            size="md"
                                            required>
                                            <option value="1" selected>Activo</option>
                                            <option value="2">Inactivo</option>
                                            <option value="9">Pendiente</option>
                                            <option value="10">Suspendido</option>
                                        </x-form-inputs.select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer de guardado --}}
                        <x-form-style.footer-button>
                            <div class="flex w-full items-center gap-2 sm:w-auto">
                                <x-btn.cancel label="Descartar"/>
                                <x-btn.save label="Guardar Aseguradora"/>
                            </div>
                        </x-form-style.footer-button>
                    </div>

                    {{-- ── TAB 2: PLANES Y COBERTURAS ──────────────────────── --}}
                    <div x-show="activeTab === 'planes'">

                        {{-- Galería de planes --}}
                        <div class="mb-6">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                                    Planes (<span x-text="plans.length"></span>)
                                </span>
                                <button type="button" class="btn-base btn-secondary btn-sm">
                                    <x-menu.heroicon name="plus" class="h-3 w-3"/>
                                    Nuevo plan
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                                <template x-for="plan in plans" :key="plan.id">
                                    <button type="button"
                                            @click="activePlan = plan.id"
                                            :class="activePlan === plan.id
                                                ? 'bg-white shadow-md dark:bg-gray-800'
                                                : 'bg-slate-50 hover:bg-white hover:shadow-sm dark:bg-gray-800/50 dark:hover:bg-gray-800'"
                                            class="plan-card relative rounded-xl p-3 text-left"
                                            :style="activePlan === plan.id
                                                ? `border-top: 3px solid ${activeInsurerData.color}; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;`
                                                : 'border-top: 3px solid transparent; border: 1px solid #f1f5f9;'">

                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="rounded px-2 py-0.5 font-mono text-[10px] font-extrabold"
                                                  :style="`background: ${activeInsurerData.bg}; color: ${activeInsurerData.color}`"
                                                  x-text="plan.code"></span>
                                            <span x-show="activePlan === plan.id"
                                                  class="text-indigo-600 dark:text-sky-400">
                                                <x-menu.heroicon name="check-circle" class="h-3.5 w-3.5"/>
                                            </span>
                                        </div>
                                        <div class="text-sm font-bold text-slate-700 dark:text-gray-200"
                                             x-text="plan.name"></div>
                                        <div class="mt-0.5 text-[10px] text-slate-500 dark:text-gray-500"
                                             x-text="plan.desc"></div>
                                        <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400">
                                            <span>
                                                <strong class="text-slate-700 dark:text-gray-300" x-text="plan.members.toLocaleString('es-AR')"></strong> afil.
                                            </span>
                                            <span>
                                                <strong class="text-slate-700 dark:text-gray-300" x-text="plan.coverages"></strong> cob.
                                            </span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Tabla de coberturas del plan activo --}}
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">Coberturas de</span>
                                <span class="font-headline text-sm font-extrabold"
                                      :style="`color: ${activeInsurerData.color}`"
                                      x-text="activePlanData.name"></span>
                            </div>
                            <button type="button" class="btn-base btn-secondary btn-sm ml-auto">
                                <x-menu.heroicon name="plus" class="h-3 w-3"/>
                                Nueva cobertura
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500">Categoría</th>
                                            <th class="px-2 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500">Cobertura</th>
                                            <th class="px-2 py-2.5 text-right font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500">Copago</th>
                                            <th class="hidden px-2 py-2.5 text-right font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500 md:table-cell">Tope</th>
                                            <th class="hidden px-2 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500 md:table-cell">Autoriz.</th>
                                            <th class="px-4 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500 dark:text-gray-500">Activo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                                        <template x-for="(cov, idx) in coverages" :key="idx">
                                            <tr :class="!cov.active ? 'opacity-50' : ''"
                                                class="transition-colors hover:bg-indigo-50/40 dark:hover:bg-gray-800/40">

                                                <td class="px-4 py-2.5 font-semibold text-slate-700 dark:text-gray-200"
                                                    x-text="cov.cat"></td>

                                                <td class="px-2 py-2.5">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <div class="h-1.5 w-12 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                                            <div class="h-full rounded-full"
                                                                 :class="cov.pct === 100 ? 'bg-emerald-500' : (cov.pct >= 70 ? 'bg-indigo-500' : 'bg-amber-500')"
                                                                 :style="`width: ${cov.pct}%`"></div>
                                                        </div>
                                                        <span class="font-bold tabular-nums text-slate-700 dark:text-gray-200"
                                                              x-text="`${cov.pct}%`"></span>
                                                    </div>
                                                </td>

                                                <td class="px-2 py-2.5 text-right font-mono"
                                                    :class="cov.copay > 0 ? 'text-slate-700 dark:text-gray-200' : 'text-slate-400 dark:text-gray-600'"
                                                    x-text="cov.copay > 0 ? `$${cov.copay.toLocaleString('es-AR')}` : '—'"></td>

                                                <td class="hidden px-2 py-2.5 text-right font-mono md:table-cell"
                                                    :class="cov.max ? 'text-slate-700 dark:text-gray-200' : 'text-slate-400 dark:text-gray-600'"
                                                    x-text="cov.max ? `$${cov.max.toLocaleString('es-AR')}` : '—'"></td>

                                                <td class="hidden px-2 py-2.5 text-center md:table-cell">
                                                    <template x-if="cov.auth">
                                                        <span class="pill-warning">
                                                            <x-menu.heroicon name="lock-closed" class="h-2.5 w-2.5"/>
                                                            Sí
                                                        </span>
                                                    </template>
                                                    <template x-if="!cov.auth">
                                                        <span class="text-slate-300 dark:text-gray-600">—</span>
                                                    </template>
                                                </td>

                                                <td class="px-4 py-2.5 text-center">
                                                    <button type="button"
                                                            @click="cov.active = !cov.active"
                                                            class="relative inline-block h-4 w-7 rounded-full transition-colors"
                                                            :style="`background: ${cov.active ? activeInsurerData.color : '#cbd5e1'}`"
                                                            :aria-label="cov.active ? 'Desactivar cobertura' : 'Activar cobertura'">
                                                        <span class="absolute top-0.5 h-3 w-3 rounded-full bg-white shadow transition-all"
                                                              :style="`left: ${cov.active ? '14px' : '2px'}`"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ── Carnet + contacto + KPIs ── --}}
                        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-[280px_1fr]">

                            {{-- Carnet (ratio ID-1: 1.586:1) --}}
                            <div>
                                <div class="mb-2 flex items-center gap-2">
                                    <x-menu.heroicon name="eye" class="h-3.5 w-3.5 text-indigo-600 dark:text-sky-400"/>
                                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                                        Carnet de afiliado
                                    </span>
                                    <span class="live-dot ml-auto"></span>
                                </div>

                                <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-xl"
                                     style="aspect-ratio: 1.586/1"
                                     :style="`background: linear-gradient(135deg, ${activeInsurerData.color} 0%, ${activeInsurerData.color}cc 100%)`">
                                    <div class="pointer-events-none absolute -right-8 -top-8 h-36 w-36 rounded-full bg-white/10 blur-2xl"></div>
                                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-black/15 to-transparent"></div>

                                    <div class="relative mb-4 flex items-start justify-between">
                                        <div>
                                            <div class="text-[9px] font-bold uppercase tracking-widest opacity-90" x-text="activeInsurerData.name"></div>
                                            <div class="font-headline text-base font-extrabold leading-tight" x-text="activePlanData.name"></div>
                                        </div>
                                        <span class="rounded bg-white/95 px-2 py-1 font-mono text-[9px] font-extrabold"
                                              :style="`color: ${activeInsurerData.color}`"
                                              x-text="activePlanData.code"></span>
                                    </div>

                                    <div class="relative mb-3 h-6 w-9 rounded"
                                         style="background: linear-gradient(135deg, #fde68a 0%, #d97706 100%)">
                                        <div class="absolute inset-1 border-y border-black/10"></div>
                                    </div>

                                    <div class="relative mb-3 font-mono text-sm font-bold tracking-widest">
                                        3245 · 9876 · 1023 · 5512
                                    </div>

                                    <div class="relative flex items-end justify-between">
                                        <div>
                                            <div class="text-[8px] font-semibold uppercase tracking-widest opacity-70">Afiliado</div>
                                            <div class="text-[11px] font-bold">JUAN P. MORENO</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[8px] font-semibold uppercase tracking-widest opacity-70">Vigencia</div>
                                            <div class="font-mono text-[10px] font-semibold">12/2027</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1.5 text-center text-[10px] text-slate-400 dark:text-gray-600">
                                    Vista informativa — no es un carnet emitido.
                                </p>
                            </div>

                            {{-- Contacto + KPIs --}}
                            <div class="space-y-4">

                                {{-- Contacto rápido --}}
                                <div>
                                    <div class="mb-2 font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">Contacto</div>
                                    <div class="space-y-1.5 rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs dark:border-gray-800 dark:bg-gray-800/40">
                                        <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                            <x-menu.heroicon name="phone" class="h-3 w-3 shrink-0 text-slate-400 dark:text-gray-600"/>
                                            <span>0810 555 6337</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                            <x-menu.heroicon name="envelope" class="h-3 w-3 shrink-0 text-slate-400 dark:text-gray-600"/>
                                            <span class="truncate">contacto@osde.com.ar</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                            <x-menu.heroicon name="globe-alt" class="h-3 w-3 shrink-0 text-slate-400 dark:text-gray-600"/>
                                            <span class="truncate">www.osde.com.ar</span>
                                        </div>
                                        <div class="flex items-start gap-2 text-slate-700 dark:text-gray-300">
                                            <x-menu.heroicon name="map" class="mt-0.5 h-3 w-3 shrink-0 text-slate-400 dark:text-gray-600"/>
                                            <span>Av. Olascoaga 1702, Neuquén</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- KPIs --}}
                                <div>
                                    <div class="mb-2 font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">Resumen</div>
                                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-gray-800 dark:bg-gray-800/40">
                                            <div class="font-headline text-xl font-extrabold text-slate-800 dark:text-gray-100" x-text="activeInsurerData.plans"></div>
                                            <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500">Planes activos</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-gray-800 dark:bg-gray-800/40">
                                            <div class="font-headline text-xl font-extrabold text-slate-800 dark:text-gray-100">2.892</div>
                                            <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500">Afiliados</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-gray-800 dark:bg-gray-800/40">
                                            <div class="font-headline text-xl font-extrabold text-slate-800 dark:text-gray-100" x-text="activePlanData.coverages"></div>
                                            <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500">Coberturas</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3 dark:border-gray-800 dark:bg-gray-800/40">
                                            <div class="font-headline text-xl font-extrabold text-slate-800 dark:text-gray-100" x-text="activePlanData.members.toLocaleString('es-AR')"></div>
                                            <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-500">Afil. del plan</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ── TAB 3: HISTORIAL ─────────────────────────────────── --}}
                    <div x-show="activeTab === 'historial'">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex items-center justify-between">
                                <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                                    Actividad — últimas 30 entradas
                                </span>
                            </div>

                            <div class="space-y-3">
                                @php
                                    $activities = [
                                        ['user' => 'JP', 'name' => 'Juan P. Moreno', 'desc' => 'actualizó la dirección de facturación.',         'time' => 'hace 2 días'],
                                        ['user' => 'AM', 'name' => 'Ana M. García',  'desc' => 'agregó el plan OSDE Bonus con 42 coberturas.',  'time' => 'hace 5 días'],
                                        ['user' => 'SY', 'name' => 'Sistema',        'desc' => 'sincronizó los datos del registro fiscal.',     'time' => 'hace 1 semana'],
                                        ['user' => 'JP', 'name' => 'Juan P. Moreno', 'desc' => 'modificó el estado a Activo.',                  'time' => 'hace 10 días'],
                                        ['user' => 'AM', 'name' => 'Ana M. García',  'desc' => 'creó la aseguradora OSDE en el catálogo.',      'time' => 'hace 2 semanas'],
                                    ];
                                @endphp
                                @foreach($activities as $act)
                                    <div class="flex items-start gap-3 rounded-lg bg-slate-50 p-3 dark:bg-gray-800/50">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400">
                                            {{ $act['user'] }}
                                        </div>
                                        <div class="flex-1 text-xs">
                                            <p class="text-slate-600 dark:text-gray-300">
                                                <strong class="text-slate-800 dark:text-gray-100">{{ $act['name'] }}</strong>
                                                {{ $act['desc'] }}
                                            </p>
                                            <p class="mt-0.5 text-[10px] text-slate-400 dark:text-gray-500">{{ $act['time'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </main>


        </div>
    </x-form-style.main-div>
</x-form-style.border-style>
