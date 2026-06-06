# Prompt para Claude Code — InsuranceCompany Workspace (Hub Detalle)

Copiá todo este bloque y pegáselo a Claude Code en la raíz de `JPMG2/cronos`.

---

## 🎯 Objetivo

Diseñá la pantalla del componente **InsuranceCompany** como un *workspace* / hub de detalle: cada aseguradora es el centro de la pantalla, con sus **planes** (`InsurancePlan`) y **coberturas** (`InsuranceCoverage`) gestionados en línea, más una vista previa visual tipo carnet de afiliado.

**Stack obligatorio (no cambies):** Laravel + Livewire 3 (Volt) + Alpine + Tailwind 3. Mantené el mismo patrón de los componentes existentes (`create-company.blade.php`, `manage-roles.blade.php`): `<?php class extends Component {…} ?>` arriba, vista Blade abajo, Alpine inline al final con `@script`.

**Archivos a crear/editar:**

- 📄 Crear `resources/views/components/configuracion/empresa/⚡insurance-company.blade.php`
- 📄 Crear `app/Livewire/Forms/Configuracion/Empresa/InsuranceCompanyForm.php`
- 📄 Crear `app/Livewire/Forms/Configuracion/Empresa/InsurancePlanForm.php`
- 📄 Crear `app/Livewire/Forms/Configuracion/Empresa/InsuranceCoverageForm.php`
- 📄 Crear `app/Actions/Configuracion/InsuranceCompanyAction/CreateInsuranceCompanyAction.php` (y equivalentes para Plan / Coverage)
- ✏️ Agregar utilities CSS al final del `@layer components` de `resources/css/app.css`
- ✏️ Agregar ruta y entrada de menú correspondiente.

**Reutilizá** todo lo que ya tenés:
`x-form-style.border-style`, `x-form-style.main-div`, `x-form-style.header-form`, `x-form-style.number-tag`, `x-form-style.footer-button`, `x-form-inputs.text_input`, `x-form-inputs.select`, `x-form-inputs.autocomplete`, `x-form-inputs.textarea`, `x-btn.save`, `x-btn.cancel`, `x-menu.heroicon`, `x-feedback.toast`, `BaseForm`, `HasNotifications`, `AttributeValidator`.

---

## 📐 Layout — Grid de 3 columnas

```
┌─────────────┬─────────────────────────────────┬──────────────────┐
│ Lista       │ Hero + Tabs + Contenido         │ Carnet preview   │
│ (220 px)    │ (1fr — flex)                    │ (320 px)         │
│             │                                 │                  │
│ Aseguradora │  ┌─────────────────────────┐    │  ┌────────────┐  │
│ Aseguradora │  │ Hero brand-color card   │    │  │ Card 16:10 │  │
│ • OSDE      │  └─────────────────────────┘    │  │ ID-1 ratio │  │
│   Galeno    │   Tabs:                         │  └────────────┘  │
│   Swiss…    │   ① Datos generales             │                  │
│             │   ② Planes y coberturas         │  Contacto        │
│             │   ③ Historial                   │  Actividad       │
└─────────────┴─────────────────────────────────┴──────────────────┘
```

### Orden de tabs (importante)

1. **Datos generales** (default activa al cargar)
2. **Planes y coberturas**
3. **Historial**

---

## 1️⃣ Componente Livewire — `⚡insurance-company.blade.php`

### Cabecera PHP

```php
<?php

declare(strict_types=1);

use App\Livewire\Forms\Configuracion\Empresa\InsuranceCompanyForm;
use App\Livewire\Forms\Configuracion\Empresa\InsurancePlanForm;
use App\Livewire\Forms\Configuracion\Empresa\InsuranceCoverageForm;
use App\Models\InsuranceCompany;
use App\Models\InsurancePlan;
use App\Models\CurrentStatus;
use App\Models\Region;
use App\Models\WorldSettings;
use App\Dto\Style\ModalConfig;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Aseguradoras')]
class extends Component {
    use HasNotifications;
    use WithFileUploads;

    public InsuranceCompanyForm $form;
    public InsurancePlanForm $planForm;
    public InsuranceCoverageForm $coverageForm;

    public ?int $activeInsurerId = null;
    public ?int $activePlanId = null;
    public string $activeTab = 'datos'; // datos | planes | historial
    public string $search = '';
    public ?int $statusFilter = null;

    #[Computed]
    public function insurers(): Collection
    {
        return InsuranceCompany::query()
            ->withCount('plans')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                                              ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('current_status_id', $this->statusFilter))
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function activeInsurer(): ?InsuranceCompany
    {
        if (!$this->activeInsurerId) return null;
        return InsuranceCompany::query()
            ->with(['plans.coverages', 'currentStatus', 'region'])
            ->find($this->activeInsurerId);
    }

    #[Computed]
    public function activePlan(): ?InsurancePlan
    {
        if (!$this->activePlanId) return null;
        return InsurancePlan::query()
            ->with(['coverages', 'currentStatus'])
            ->find($this->activePlanId);
    }

    #[Computed]
    public function statuses(): Collection
    {
        return CurrentStatus::query()
            ->whereIn('id', [1, 2, 9, 10])
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function regions(): Collection
    {
        return Region::query()
            ->where('province_id', WorldSettings::defaultProvince())
            ->defaultFirst(WorldSettings::defaultRegion())
            ->get();
    }

    public function mount(): void
    {
        $first = InsuranceCompany::query()->orderBy('name')->first();
        if ($first) {
            $this->selectInsurer($first->id);
        }
    }

    public function selectInsurer(int $id): void
    {
        $this->activeInsurerId = $id;
        $insurer = InsuranceCompany::with('plans')->find($id);
        $this->form->loadInsurerData($insurer);
        $this->activePlanId = $insurer->plans->first()?->id;
        $this->activeTab = 'datos';
        $this->resetValidation();
    }

    public function selectPlan(int $id): void
    {
        $this->activePlanId = $id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function newInsurer(): void
    {
        $this->form->reset();
        $this->activeInsurerId = null;
        $this->activePlanId = null;
        $this->activeTab = 'datos';
    }

    public function adviceSave(): void
    {
        $this->form->validateInsurer();

        if (!$this->activeInsurerId) {
            $config = new ModalConfig(
                title: 'Confirmar registro',
                message: 'Se creará una nueva aseguradora con los datos ingresados. ¿Continuar?',
                type: 'info',
                buttons: [[
                    'label' => 'Aceptar',
                    'action' => 'storeInsurer',
                    'class' => 'save',
                    'params' => [],
                ]],
            );
            $this->dispatch('openModal', config: (array) $config);
        } else {
            $this->storeInsurer(null);
        }
    }

    #[On('storeInsurer')]
    public function storeInsurer(?array $params): void
    {
        Gate::authorize('empresa.update');
        [$message, $type] = $this->form->handleInsurerCreation();
        $this->activeInsurerId = $this->form->insurerId;
        $this->getTypeMessage($message, $type);
    }

    public function cancel(): void
    {
        $this->resetValidation();
        if ($this->activeInsurerId) {
            $insurer = InsuranceCompany::find($this->activeInsurerId);
            $this->form->loadInsurerData($insurer);
        } else {
            $this->form->reset();
        }
    }
}
?>
```

### Vista Blade — Layout principal

```html
<x-form-style.border-style>
    <x-form-style.main-div>

        <x-form-style.header-form
            title="Aseguradoras y Prepagas"
            description="Catálogo maestro de obras sociales y prepagas con sus planes y coberturas asociadas."
            sign="{{ $this->insurers->count() }} aseguradoras"/>

        <div class="grid min-h-[700px] grid-cols-1 lg:grid-cols-[220px_1fr_320px]"
             x-data="insuranceWorkspace">

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- ── COL 1: Lista lateral de aseguradoras ─────────────── --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            <aside class="flex flex-col border-r border-slate-100 bg-slate-50/60 px-3 py-4 dark:border-gray-800 dark:bg-gray-900/30">

                <div class="mb-3 flex items-center justify-between px-1">
                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                        Aseguradoras
                    </span>
                    <button type="button"
                            wire:click="newInsurer"
                            class="rounded-md p-1 text-indigo-600 hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-sky-500/10"
                            title="Nueva aseguradora">
                        <x-menu.heroicon name="plus" class="h-3.5 w-3.5"/>
                    </button>
                </div>

                <div class="relative mb-3 px-1">
                    <x-menu.heroicon name="magnifying-glass"
                                     class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 dark:text-gray-500"/>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar…"
                           class="w-full rounded-lg border border-slate-200 bg-white py-1.5 pl-8 pr-3 text-xs placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-sky-500"/>
                </div>

                <div class="flex-1 space-y-1 overflow-y-auto pr-1">
                    @forelse($this->insurers as $insurer)
                        @php
                            $isActive = $insurer->id === $activeInsurerId;
                            $statusColor = match($insurer->current_status_id) {
                                1  => 'emerald',
                                2  => 'amber',
                                9  => 'sky',
                                10 => 'rose',
                                default => 'slate',
                            };
                        @endphp
                        <button type="button"
                                wire:click="selectInsurer({{ $insurer->id }})"
                                @class([
                                    'relative flex w-full items-center gap-2.5 rounded-lg p-2 text-left transition-all',
                                    'bg-white shadow-sm dark:bg-gray-800' => $isActive,
                                    'hover:bg-white/50 dark:hover:bg-gray-800/50' => !$isActive,
                                ])>
                            @if($isActive)
                                <span class="absolute -left-3 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r"
                                      style="background: {{ $insurer->color ?? '#4f46e5' }}"></span>
                            @endif
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-headline text-[9px] font-extrabold"
                                 style="background: {{ $insurer->brand_bg ?? '#eef2ff' }}; color: {{ $insurer->color ?? '#4f46e5' }};">
                                {{ \Illuminate\Support\Str::limit($insurer->code, 4, '') }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div @class([
                                    'truncate text-xs font-bold',
                                    'text-indigo-700 dark:text-sky-300' => $isActive,
                                    'text-slate-700 dark:text-gray-200' => !$isActive,
                                ])>{{ $insurer->name }}</div>
                                <div class="text-[9px] text-slate-400 dark:text-gray-500">{{ $insurer->plans_count }} planes</div>
                            </div>
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-{{ $statusColor }}-500"></span>
                        </button>
                    @empty
                        <p class="px-3 py-6 text-center text-xs text-slate-400 dark:text-gray-500">Sin resultados</p>
                    @endforelse
                </div>
            </aside>

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- ── COL 2: Workspace principal ───────────────────────── --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            <main class="overflow-y-auto">

                @if($this->activeInsurer)
                    @php $insurer = $this->activeInsurer; @endphp

                    {{-- ─── HERO ─── --}}
                    <div class="relative m-5 overflow-hidden rounded-2xl px-6 py-6 text-white shadow-lg"
                         style="background: linear-gradient(135deg, {{ $insurer->color ?? '#4f46e5' }} 0%, {{ $insurer->color ?? '#3730a3' }}cc 100%);">

                        {{-- Pattern overlay --}}
                        <div class="absolute inset-0 opacity-[0.12]"
                             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.4) 1px, transparent 0); background-size: 18px 18px;"></div>
                        {{-- Glow --}}
                        <div class="absolute -top-16 -right-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>

                        <div class="relative flex items-center gap-5">
                            {{-- Logo --}}
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/95 font-headline text-xl font-black shadow-xl"
                                 style="color: {{ $insurer->color ?? '#4f46e5' }};">
                                {{ \Illuminate\Support\Str::limit($insurer->code, 4, '') }}
                            </div>
                            {{-- Title block --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-white">{{ $insurer->name }}</h1>
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm">
                                        <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                        {{ $insurer->currentStatus->name }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-white/80">CUIT {{ $insurer->cuit }} · {{ $insurer->region?->name }}</p>
                                {{-- Stats inline --}}
                                <div class="mt-3 flex gap-5">
                                    <div>
                                        <div class="font-headline text-2xl font-extrabold leading-none">{{ $insurer->plans->count() }}</div>
                                        <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-white/70">Planes</div>
                                    </div>
                                    <div class="w-px bg-white/20"></div>
                                    <div>
                                        <div class="font-headline text-2xl font-extrabold leading-none">{{ $insurer->plans->sum('members_count') ?? 0 }}</div>
                                        <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-white/70">Afiliados</div>
                                    </div>
                                    <div class="w-px bg-white/20"></div>
                                    <div>
                                        <div class="font-headline text-2xl font-extrabold leading-none">{{ $insurer->plans->sum(fn($p) => $p->coverages->count()) }}</div>
                                        <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-white/70">Coberturas</div>
                                    </div>
                                </div>
                            </div>
                            {{-- Actions --}}
                            <div class="flex flex-col gap-2">
                                <button type="button" class="flex items-center gap-1.5 rounded-lg border border-white/30 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm hover:bg-white/20">
                                    <x-menu.heroicon name="pencil" class="h-3 w-3"/>Editar datos
                                </button>
                                <button type="button" class="flex items-center gap-1.5 rounded-lg border border-white/30 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm hover:bg-white/20">
                                    <x-menu.heroicon name="duplicate" class="h-3 w-3"/>Duplicar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ─── TABS (orden: datos → planes → historial) ─── --}}
                    <div class="border-b border-slate-200 px-5 dark:border-gray-800">
                        <div class="flex items-center gap-0">
                            @foreach([
                                'datos'     => ['label' => 'Datos generales',    'icon' => 'building-office'],
                                'planes'    => ['label' => 'Planes y coberturas', 'icon' => 'document'],
                                'historial' => ['label' => 'Historial',           'icon' => 'clock'],
                            ] as $key => $tab)
                                <button type="button"
                                        wire:click="setTab('{{ $key }}')"
                                        @class([
                                            '-mb-px flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition-colors',
                                            'border-indigo-600 text-indigo-700 dark:border-sky-400 dark:text-sky-300' => $activeTab === $key,
                                            'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' => $activeTab !== $key,
                                        ])>
                                    <x-menu.heroicon name="{{ $tab['icon'] }}" class="h-4 w-4"/>
                                    {{ $tab['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─── TAB CONTENT ─── --}}
                    <div class="p-5">

                        {{-- ── TAB 1: DATOS GENERALES ── --}}
                        @if($activeTab === 'datos')
                            <div x-data="insurerForm" class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                                {{-- Card 01: Identificación --}}
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                    <x-form-style.number-tag number="01" label="Identificación"/>
                                    <div class="space-y-4">
                                        <x-form-inputs.text_input
                                            label="Nombre de la Aseguradora"
                                            name="name"
                                            icon="building-office"
                                            placeholder="Ej: OSDE, Swiss Medical…"
                                            maxlength="200"
                                            wire:model="form.name"
                                            alpine-error="name"
                                            class="uppercase"
                                            required/>
                                        <div class="grid grid-cols-2 gap-4">
                                            <x-form-inputs.text_input
                                                label="Código"
                                                name="code"
                                                icon="key"
                                                placeholder="OSDE"
                                                maxlength="20"
                                                wire:model="form.code"
                                                alpine-error="code"
                                                class="uppercase font-mono"
                                                required/>
                                            <x-form-inputs.text_input
                                                label="CUIT"
                                                name="cuit"
                                                icon="identification"
                                                placeholder="30-12345678-9"
                                                maxlength="13"
                                                wire:model="form.cuit"
                                                x-mask="99-99999999-9"
                                                alpine-error="cuit"
                                                required/>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card 02: Ubicación --}}
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                    <x-form-style.number-tag number="02" label="Ubicación"/>
                                    <div class="space-y-4">
                                        <x-form-inputs.autocomplete
                                            wire:key="region-{{ $form->regionId }}"
                                            label="Región"
                                            name="regionId"
                                            placeholder="Seleccionar región…"
                                            :options="$this->regions->map(fn($r) => ['value' => $r->id, 'label' => $r->name])"
                                            wire:model="form.regionId"
                                            alpine-error="regionId"
                                            :value="$form->regionId"
                                            required/>
                                        <x-form-inputs.text_input
                                            label="Dirección"
                                            name="address"
                                            icon="map"
                                            placeholder="Calle, número, ciudad"
                                            maxlength="200"
                                            wire:model="form.address"
                                            alpine-error="address"
                                            required/>
                                    </div>
                                </div>

                                {{-- Card 03: Contacto --}}
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                    <x-form-style.number-tag number="03" label="Contacto"/>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-5 gap-4">
                                            <div class="col-span-2">
                                                <x-form-inputs.text_input
                                                    label="Teléfono"
                                                    name="phone"
                                                    type="tel"
                                                    icon="phone"
                                                    placeholder="0810 ..."
                                                    maxlength="20"
                                                    wire:model="form.phone"
                                                    alpine-error="phone"
                                                    required/>
                                            </div>
                                            <div class="col-span-3">
                                                <x-form-inputs.text_input
                                                    label="Web"
                                                    name="website"
                                                    type="url"
                                                    icon="globe-alt"
                                                    placeholder="www.aseguradora.com"
                                                    wire:model="form.website"
                                                    maxlength="200"/>
                                            </div>
                                        </div>
                                        <x-form-inputs.text_input
                                            label="Correo Electrónico"
                                            name="email"
                                            type="email"
                                            icon="envelope"
                                            placeholder="contacto@aseguradora.com.ar"
                                            maxlength="200"
                                            wire:model="form.email"
                                            alpine-error="email"
                                            required/>
                                    </div>
                                </div>

                                {{-- Card 04: Logo + Estado --}}
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                                     x-data="{ dragging: false }">
                                    <x-form-style.number-tag number="04" label="Logo y Estado"/>
                                    <div class="flex gap-5">
                                        {{-- Logo uploader (reutilizá la misma estructura de create-company.blade) --}}
                                        <div class="flex shrink-0 flex-col items-center gap-2">
                                            <span class="self-start text-xs font-semibold text-slate-400 dark:text-gray-500">Logo</span>
                                            <div
                                                x-on:dragover.prevent="dragging = true"
                                                x-on:dragleave.prevent="dragging = false"
                                                x-on:drop.prevent="dragging = false;
                                                    const f = $event.dataTransfer.files;
                                                    if (f.length) { $refs.logoInput.files = f; $refs.logoInput.dispatchEvent(new Event('change')); }"
                                                :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-indigo-200/80'"
                                                class="group relative h-[7.5rem] w-[7.5rem] cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed bg-white transition-colors dark:bg-gray-800/60"
                                                @click="$refs.logoInput.click()">
                                                @if ($form->logo)
                                                    <img src="{{ $form->logo->temporaryUrl() }}" class="h-full w-full object-contain p-1.5"/>
                                                @else
                                                    <div class="flex h-full flex-col items-center justify-center gap-1.5">
                                                        <x-menu.heroicon name="shield-check" class="h-8 w-8 text-indigo-200"/>
                                                        <span class="text-[10px] font-medium text-indigo-200">PNG · SVG</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <input x-ref="logoInput" type="file" wire:model="form.logo" accept="image/*" class="sr-only"/>
                                        </div>
                                        <div class="flex flex-1 flex-col gap-4">
                                            <x-form-inputs.select
                                                label="Estado"
                                                name="currentStatusId"
                                                icon="check-circle"
                                                wire:model="form.currentStatusId"
                                                alpine-error="currentStatusId"
                                                required>
                                                <option value="">Seleccionar…</option>
                                                @foreach ($this->statuses as $status)
                                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                @endforeach
                                            </x-form-inputs.select>
                                            <x-form-inputs.textarea
                                                label="Notas internas"
                                                name="notes"
                                                placeholder="Acuerdos, observaciones…"
                                                wire:model="form.notes"
                                                rows="3"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer save --}}
                            <x-form-style.footer-button>
                                <div class="flex w-full items-center gap-2 sm:w-auto">
                                    <x-btn.cancel label="Descartar" wire:click="cancel"/>
                                    <x-btn.save label="Guardar Aseguradora" @click="submit()" wireTarget="adviceSave"/>
                                </div>
                            </x-form-style.footer-button>
                        @endif

                        {{-- ── TAB 2: PLANES Y COBERTURAS ── --}}
                        @if($activeTab === 'planes')
                            <div>
                                {{-- Planes gallery --}}
                                <div class="mb-6">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">
                                            Planes ({{ $insurer->plans->count() }})
                                        </span>
                                        <button type="button" class="btn-base btn-secondary btn-sm" @click="$dispatch('open-plan-modal')">
                                            <x-menu.heroicon name="plus" class="h-3 w-3"/>Nuevo plan
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                                        @foreach($insurer->plans as $plan)
                                            @php $isActivePlan = $plan->id === $activePlanId; @endphp
                                            <button type="button"
                                                    wire:click="selectPlan({{ $plan->id }})"
                                                    @class([
                                                        'plan-card relative cursor-pointer rounded-xl p-3 text-left transition-all',
                                                        'bg-white shadow-md' => $isActivePlan,
                                                        'bg-slate-50 hover:bg-white hover:shadow-sm' => !$isActivePlan,
                                                    ])
                                                    style="{{ $isActivePlan ? 'border-top: 3px solid '.($insurer->color ?? '#4f46e5') : 'border-top: 3px solid transparent' }}">
                                                <div class="mb-2 flex items-center justify-between">
                                                    <span class="rounded px-2 py-0.5 font-mono text-[10px] font-extrabold"
                                                          style="background: {{ $insurer->brand_bg ?? '#eef2ff' }}; color: {{ $insurer->color ?? '#4f46e5' }};">
                                                        {{ $plan->code }}
                                                    </span>
                                                    @if($isActivePlan)
                                                        <x-menu.heroicon name="check" class="h-3 w-3" style="color: {{ $insurer->color }}"/>
                                                    @endif
                                                </div>
                                                <div class="text-sm font-bold text-slate-700 dark:text-gray-200">{{ $plan->name }}</div>
                                                <div class="mt-0.5 text-[10px] text-slate-500 dark:text-gray-500">{{ $plan->description }}</div>
                                                <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400">
                                                    <span><strong class="text-slate-700 dark:text-gray-300">{{ $plan->members_count ?? 0 }}</strong> afil.</span>
                                                    <span><strong class="text-slate-700 dark:text-gray-300">{{ $plan->coverages->count() }}</strong> cob.</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Coverages table for active plan --}}
                                @if($this->activePlan)
                                    <div class="mb-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-gray-400">Coberturas de</span>
                                            <span class="font-headline text-sm font-extrabold" style="color: {{ $insurer->color }}">{{ $this->activePlan->name }}</span>
                                        </div>
                                        <button type="button" class="btn-base btn-secondary btn-sm" @click="$dispatch('open-coverage-modal')">
                                            <x-menu.heroicon name="plus" class="h-3 w-3"/>Nueva cobertura
                                        </button>
                                    </div>
                                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                                        <table class="w-full text-xs">
                                            <thead class="bg-slate-50 dark:bg-gray-800/50">
                                                <tr>
                                                    <th class="px-4 py-2.5 text-left font-label text-[9px] uppercase tracking-widest text-slate-500">Categoría</th>
                                                    <th class="px-2 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500">Cobertura</th>
                                                    <th class="px-2 py-2.5 text-right font-label text-[9px] uppercase tracking-widest text-slate-500">Copago</th>
                                                    <th class="px-2 py-2.5 text-right font-label text-[9px] uppercase tracking-widest text-slate-500">Tope</th>
                                                    <th class="px-2 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500">Autoriz.</th>
                                                    <th class="px-4 py-2.5 text-center font-label text-[9px] uppercase tracking-widest text-slate-500">Activo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($this->activePlan->coverages as $cov)
                                                    <tr @class(['border-t border-slate-100 dark:border-gray-800', 'opacity-50' => !$cov->is_active])>
                                                        <td class="px-4 py-2.5 font-semibold text-slate-700 dark:text-gray-200">{{ $cov->category }}</td>
                                                        <td class="px-2 py-2.5">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <div class="h-1.5 w-12 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                                                    <div class="h-full rounded-full {{ $cov->coverage_percentage == 100 ? 'bg-emerald-500' : ($cov->coverage_percentage >= 70 ? 'bg-indigo-500' : 'bg-amber-500') }}"
                                                                         style="width: {{ $cov->coverage_percentage }}%"></div>
                                                                </div>
                                                                <span class="font-bold tabular-nums">{{ $cov->coverage_percentage }}%</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-2 py-2.5 text-right font-mono {{ $cov->copay_amount > 0 ? '' : 'text-slate-400' }}">
                                                            {{ $cov->copay_amount > 0 ? '$'.number_format($cov->copay_amount, 0, ',', '.') : '—' }}
                                                        </td>
                                                        <td class="px-2 py-2.5 text-right font-mono {{ $cov->max_amount ? '' : 'text-slate-400' }}">
                                                            {{ $cov->max_amount ? '$'.number_format($cov->max_amount, 0, ',', '.') : '—' }}
                                                        </td>
                                                        <td class="px-2 py-2.5 text-center">
                                                            @if($cov->requires_authorization)
                                                                <span class="pill-warning">
                                                                    <x-menu.heroicon name="lock-closed" class="h-2.5 w-2.5"/>Sí
                                                                </span>
                                                            @else
                                                                <span class="text-slate-300">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2.5 text-center">
                                                            <button type="button"
                                                                    wire:click="toggleCoverage({{ $cov->id }})"
                                                                    class="relative inline-block h-4 w-7 rounded-full transition-colors"
                                                                    style="background: {{ $cov->is_active ? ($insurer->color ?? '#4f46e5') : '#cbd5e1' }}">
                                                                <span class="absolute top-0.5 h-3 w-3 rounded-full bg-white shadow transition-all"
                                                                      style="left: {{ $cov->is_active ? '14px' : '2px' }}"></span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                                            Este plan no tiene coberturas todavía. <button type="button" class="text-indigo-600 hover:underline">Agregar la primera</button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- ── TAB 3: HISTORIAL ── --}}
                        @if($activeTab === 'historial')
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <div class="mb-4 flex items-center justify-between">
                                    <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-500">Actividad — últimas 30 entradas</span>
                                </div>
                                {{-- Usá Spatie ActivityLog para listar registros del modelo: --}}
                                {{-- $insurer->activities()->latest()->take(30)->get() --}}
                                <div class="space-y-3">
                                    @forelse($insurer->activities()->latest()->take(30)->get() as $act)
                                        <div class="flex items-start gap-3 rounded-lg bg-slate-50 p-3 dark:bg-gray-800/50">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-[10px] font-bold text-indigo-700">
                                                {{ \Illuminate\Support\Str::limit($act->causer?->name ?? 'SY', 2, '') }}
                                            </div>
                                            <div class="flex-1 text-xs">
                                                <p class="text-slate-600">
                                                    <strong class="text-slate-800">{{ $act->causer?->name ?? 'Sistema' }}</strong>
                                                    {{ $act->description }}
                                                </p>
                                                <p class="mt-0.5 text-[10px] text-slate-400">{{ $act->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="py-8 text-center text-xs text-slate-400">Sin actividad registrada.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    {{-- Empty state cuando no hay aseguradora seleccionada / nueva --}}
                    <div class="flex h-full flex-col items-center justify-center p-10 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-sky-500/10 dark:text-sky-400">
                            <x-menu.heroicon name="shield-check" class="h-8 w-8"/>
                        </div>
                        <h3 class="font-headline text-lg font-bold">Nueva aseguradora</h3>
                        <p class="mt-1 text-sm text-slate-500">Completá los datos para registrar una nueva obra social o prepaga.</p>
                        {{-- Acá podés mostrar el form en modo creación (mismo Tab 1) o un mini-wizard --}}
                    </div>
                @endif
            </main>

            {{-- ══════════════════════════════════════════════════════ --}}
            {{-- ── COL 3: Carnet preview + contacto + actividad ───── --}}
            {{-- ══════════════════════════════════════════════════════ --}}
            @if($this->activeInsurer && $this->activePlan)
                @php $insurer = $this->activeInsurer; $plan = $this->activePlan; @endphp
                <aside class="hidden border-l border-slate-200 bg-white dark:border-gray-800 dark:bg-gray-900 lg:block">

                    <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3.5 dark:border-gray-800">
                        <x-menu.heroicon name="eye" class="h-3.5 w-3.5 text-indigo-600 dark:text-sky-400"/>
                        <span class="font-label text-[10px] font-bold uppercase tracking-widest text-slate-600 dark:text-gray-300">
                            Carnet de afiliado
                        </span>
                        <span class="live-dot ml-auto"></span>
                    </div>

                    {{-- Carnet (ratio ID-1: 1.586:1) --}}
                    <div class="p-5">
                        <div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-xl"
                             style="aspect-ratio: 1.586/1; background: linear-gradient(135deg, {{ $insurer->color }} 0%, {{ $insurer->color }}cc 100%);">
                            {{-- Overlay --}}
                            <div class="absolute -top-8 -right-8 h-36 w-36 rounded-full bg-white/10 blur-2xl"></div>
                            <div class="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-black/15 to-transparent"></div>

                            {{-- Header --}}
                            <div class="relative mb-4 flex items-start justify-between">
                                <div>
                                    <div class="text-[9px] font-bold uppercase tracking-widest opacity-90">{{ $insurer->name }}</div>
                                    <div class="font-headline text-base font-extrabold leading-tight">{{ $plan->name }}</div>
                                </div>
                                <span class="rounded bg-white/95 px-2 py-1 font-mono text-[9px] font-extrabold"
                                      style="color: {{ $insurer->color }}">{{ $plan->code }}</span>
                            </div>

                            {{-- Chip --}}
                            <div class="relative mb-3 h-6 w-9 rounded"
                                 style="background: linear-gradient(135deg, #fde68a 0%, #d97706 100%);">
                                <div class="absolute inset-1 border-y border-black/10"></div>
                            </div>

                            {{-- Number --}}
                            <div class="relative mb-3 font-mono text-sm font-bold tracking-widest">3245 · 9876 · 1023 · 5512</div>

                            {{-- Footer --}}
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
                        <p class="mt-2 text-center text-[10px] text-slate-400">Vista informativa — no es un carnet emitido.</p>
                    </div>

                    {{-- Quick contact --}}
                    <div class="px-5 pb-5">
                        <div class="mb-2 font-label text-[10px] font-bold uppercase tracking-widest text-slate-500">Contacto</div>
                        <div class="space-y-1.5 rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs dark:border-gray-800 dark:bg-gray-800/40">
                            <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                <x-menu.heroicon name="phone" class="h-3 w-3 text-slate-400"/>
                                <span>{{ $insurer->phone }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                <x-menu.heroicon name="envelope" class="h-3 w-3 text-slate-400"/>
                                <span class="truncate">{{ $insurer->email }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700 dark:text-gray-300">
                                <x-menu.heroicon name="globe-alt" class="h-3 w-3 text-slate-400"/>
                                <span class="truncate">{{ $insurer->website ?: '—' }}</span>
                            </div>
                            <div class="flex items-start gap-2 text-slate-700 dark:text-gray-300">
                                <x-menu.heroicon name="map" class="h-3 w-3 mt-0.5 text-slate-400"/>
                                <span>{{ $insurer->address }}</span>
                            </div>
                        </div>
                    </div>
                </aside>
            @endif
        </div>
    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('insuranceWorkspace', () => ({
        init() {
            // Listeners para abrir modales de plan/coverage si los implementás
            this.$wire.$on('insurer-saved', () => {
                this.$dispatch('notify', { type: 'success', message: 'Aseguradora guardada' });
            });
        },
    }));

    Alpine.data('insurerForm', () => ({
        errors: {},
        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    code: this.$wire.form.code,
                    cuit: this.$wire.form.cuit,
                    regionId: this.$wire.form.regionId,
                    address: this.$wire.form.address,
                    phone: this.$wire.form.phone,
                    email: this.$wire.form.email,
                    currentStatusId: this.$wire.form.currentStatusId,
                },
                {
                    name: ['required', ['minLength', 3]],
                    code: ['required', ['minLength', 2]],
                    cuit: ['required', ['minLength', 11]],
                    regionId: ['required'],
                    address: ['required', ['minLength', 6]],
                    phone: ['required', ['minLength', 8]],
                    email: ['required', ['email']],
                    currentStatusId: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.adviceSave();
        },
    }));
</script>
@endscript
```

---

## 2️⃣ `InsuranceCompanyForm.php` — Form Object

Copiá la estructura de `CompanyForm.php` y adaptá:

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use App\Actions\Configuracion\InsuranceCompanyAction\CreateInsuranceCompanyAction;
use App\Livewire\Forms\BaseForm;
use App\Models\InsuranceCompany;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class InsuranceCompanyForm extends BaseForm
{
    public string $name = '';
    public string $code = '';
    public string $cuit = '';

    #[Locked]
    public ?int $insurerId = null;

    public ?int $currentStatusId = null;
    public ?int $regionId = null;

    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $website = '';
    public string $notes = '';
    public mixed $logo = null;

    public array $dataInsurer = [];

    public function validateInsurer(): void
    {
        $this->dataInsurer = $this->validateServiceData($this->insurerId);
    }

    public function handleInsurerCreation(): array
    {
        return $this->tryAction(function () {
            $model = app(CreateInsuranceCompanyAction::class)->handle($this->dataInsurer);
            $this->insurerId = $model->id;

            if ($model->wasRecentlyCreated) {
                return ['Aseguradora creada correctamente', 'notifySuccess'];
            }
            if ($model->wasChanged()) {
                return ['Aseguradora actualizada correctamente.', 'notifySuccess'];
            }
            return ['No se realizaron cambios.', 'notifyInfo'];
        }, 'Error al guardar la aseguradora: ');
    }

    public function loadInsurerData(InsuranceCompany $insurer): void
    {
        $this->insurerId       = $insurer->id;
        $this->name            = $insurer->name;
        $this->code            = $insurer->code;
        $this->cuit            = $insurer->cuit;
        $this->currentStatusId = $insurer->current_status_id;
        $this->regionId        = $insurer->region_id;
        $this->address         = $insurer->address ?? '';
        $this->phone           = $insurer->phone ?? '';
        $this->email           = $insurer->email ?? '';
        $this->website         = $insurer->website ?? '';
        $this->notes           = $insurer->notes ?? '';
    }

    protected function transformServiceData(): array
    {
        return [
            'name'              => mb_trim($this->name),
            'code'              => mb_strtoupper(mb_trim($this->code)),
            'cuit'              => mb_trim($this->cuit),
            'current_status_id' => $this->currentStatusId,
            'region_id'         => $this->regionId,
            'address'           => mb_trim($this->address),
            'phone'             => mb_trim($this->phone),
            'email'             => mb_strtolower(mb_trim($this->email)),
            'website'           => mb_strtolower(mb_trim($this->website)),
            'notes'             => mb_trim($this->notes),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name'            => AttributeValidator::uniqueIdNameLength('3',  'insurance_companies', 'name', $excludeId),
            'code'            => AttributeValidator::uniqueIdNameLength('2',  'insurance_companies', 'code', $excludeId),
            'cuit'            => AttributeValidator::uniqueIdNameLength('11', 'insurance_companies', 'cuit', $excludeId),
            'currentStatusId' => AttributeValidator::requireAndExists('current_statuses', 'id', 'current_status_id', true),
            'regionId'        => AttributeValidator::requireAndExists('regions', 'id', 'region_id', true),
            'address'         => AttributeValidator::stringValid(true, '6'),
            'phone'           => AttributeValidator::digitValid('8', true),
            'email'           => AttributeValidator::uniqueEmail('insurance_companies', 'email', $excludeId),
            'website'         => AttributeValidator::stringValid(false, '5'),
            'notes'           => AttributeValidator::stringValid(false, '0'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name'            => 'nombre',
            'code'            => 'código',
            'cuit'            => 'CUIT',
            'currentStatusId' => 'estado',
            'regionId'        => 'región',
            'address'         => 'dirección',
            'phone'           => 'teléfono',
            'email'           => 'email',
            'website'         => 'sitio web',
            'notes'           => 'notas',
        ];
    }
}
```

---

## 3️⃣ Utilities CSS — agregá al `@layer components` de `resources/css/app.css`

```css
/* ── Pills semánticas (si no existen) ──────────────────────── */
.pill-warning {
    @apply inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5
           text-[11px] font-semibold text-amber-700
           dark:bg-amber-500/10 dark:text-amber-400;
}

/* ── Live dot ──────────────────────────────────────────────── */
@keyframes cronos-pulse {
    0%, 100% { opacity: 1; }
    50%      { opacity: 0.4; }
}
.live-dot {
    @apply h-1.5 w-1.5 rounded-full bg-emerald-500;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.2);
    animation: cronos-pulse 2s ease-in-out infinite;
}

/* ── Plan card hover ───────────────────────────────────────── */
.plan-card {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.plan-card:hover {
    transform: translateY(-2px);
}

/* ── Btn base si no está ───────────────────────────────────── */
.btn-base {
    @apply inline-flex items-center justify-center gap-1.5 rounded-lg
           font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-50;
}
.btn-secondary {
    @apply border border-slate-200 bg-white text-slate-700
           hover:bg-slate-50 dark:border-gray-700 dark:bg-gray-800
           dark:text-gray-200 dark:hover:bg-gray-700;
}
.btn-sm { @apply px-3 py-1.5 text-xs; }
```

---

## 4️⃣ Modelo InsuranceCompany — agregá accessors visuales

En `app/Models/InsuranceCompany.php`, añadí dos atributos opcionales para guardar la identidad visual:

**Migración nueva** (`2026_05_25_xxxxxx_add_brand_colors_to_insurance_companies.php`):

```php
Schema::table('insurance_companies', function (Blueprint $table) {
    $table->string('color', 7)->nullable()->after('logo');     // #1d4ed8
    $table->string('brand_bg', 7)->nullable()->after('color'); // #dbeafe
});
```

Y agregalos al `$fillable` del modelo.

Si no querés tocar la tabla, podés generar los colores en el modelo:

```php
public function getColorAttribute(): string
{
    return $this->attributes['color'] ?? '#' . substr(md5($this->code), 0, 6);
}
public function getBrandBgAttribute(): string
{
    // Variante clara del color
    return $this->attributes['brand_bg'] ?? '#eef2ff';
}
```

---

## 5️⃣ Ruta + menú

En `routes/web.php` (o donde tengas el resto de seguridad/empresa):

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('configuracion/empresa/aseguradoras', 'configuracion.empresa.insurance-company')
        ->name('insurance-companies.index');
});
```

Y agregá la entrada en el seeder de menús, debajo del nodo "Empresa" en Configuración.

---

## ✅ Checklist final

- [ ] La pantalla carga con la primera aseguradora seleccionada y el tab **Datos generales** activo.
- [ ] El orden de tabs es: **Datos generales → Planes y coberturas → Historial**.
- [ ] La búsqueda lateral filtra en vivo (`wire:model.live.debounce.300ms`).
- [ ] Al seleccionar un plan, la tabla de coberturas debajo se refresca sin perder el tab activo.
- [ ] El hero card hereda el color brand de la aseguradora.
- [ ] La columna derecha (carnet) solo aparece si hay aseguradora + plan activos.
- [ ] El toggle activo/inactivo de cobertura usa el color brand de la aseguradora.
- [ ] Dark mode funciona en todos los nuevos elementos (validá cada `dark:`).
- [ ] El historial usa `$insurer->activities()` de Spatie ActivityLog.
- [ ] Validaciones server-side respetan `unique` para `name`, `code`, `cuit`, `email`.
- [ ] El uploader de logo respeta el patrón drag&drop de `create-company.blade.php`.

## 📦 Sub-componentes opcionales (próxima iteración)

- Modal Livewire para crear/editar **InsurancePlan** (lánzalo con `$dispatch('open-plan-modal')`).
- Modal Livewire para crear/editar **InsuranceCoverage** (lánzalo con `$dispatch('open-coverage-modal')`).
- `members_count` real: agregá la relación cuando exista la tabla pivot afiliado-plan, mientras tanto devolvé 0.

Cualquier duda durante la implementación, preguntá antes de inventar campos o métodos que no existen en `BaseForm` o `AttributeValidator`.

