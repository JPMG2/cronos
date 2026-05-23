<?php

declare(strict_types=1);

use App\Models\Menu;
use App\Models\Role;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Acceso al Menú')]
class extends Component {
    use HasNotifications;

    public ?int $selectedRoleId = null;

    /** @var array<int, string> */
    public array $selectedMenuIds = [];

    #[Computed]
    public function roles(): Collection
    {
        return Role::query()->withCount('menus')->orderBy('name')->get();
    }

    #[Computed]
    public function menuTree(): Collection
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with('childrenRecursive')
            ->get();
    }

    public function selectRole(int $id): void
    {
        $this->selectedRoleId = $id;
        $role = Role::with('menus')->findOrFail($id);
        $this->selectedMenuIds = $role->menus->pluck('id')
            ->map(fn ($menuId) => (string) $menuId)
            ->toArray();
    }

    public function save(): void
    {
        if (! $this->selectedRoleId) {
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);
        $role->menus()->sync(array_map('intval', $this->selectedMenuIds));
        unset($this->roles);
        $this->notifySuccess('Acceso al menú actualizado correctamente.');
        $this->dispatch('menu-access-saved');
    }
};
?>

@php
    $collectIds = function (App\Models\Menu $menu) use (&$collectIds): array {
        $ids = [$menu->id];
        foreach ($menu->childrenRecursive as $child) {
            $ids = array_merge($ids, $collectIds($child));
        }
        return $ids;
    };
@endphp

<x-form-style.border-style>
    <x-form-style.main-div>

        {{-- ══ Header ═════════════════════════════════════════════════════════════ --}}
        <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4 dark:border-gray-800">
            <div>
                <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                    Acceso al Menú
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                    Definí qué secciones del sistema puede ver cada rol.
                </p>
            </div>
            <div class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                <span class="h-2 w-2 motion-safe:animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                    Menú por Rol
                </span>
            </div>
        </div>

        {{-- ══ Dos paneles ════════════════════════════════════════════════════════ --}}
        <div class="flex min-h-[560px] flex-col lg:flex-row"
             x-data="manageMenuAccess">

            {{-- ── Panel 1: Lista de roles ──────────────────────────────────────── --}}
            <aside class="shrink-0 border-b border-slate-100 bg-slate-50/60 dark:border-gray-800 dark:bg-gray-900/40 lg:w-56 lg:border-b-0 lg:border-r"
                   :class="showTree ? 'hidden lg:block' : 'block'">

                <div class="px-3 py-3">
                    <p class="mb-2 px-1 font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                        Roles
                    </p>
                    <nav class="space-y-0.5">
                        @forelse($this->roles as $role)
                            <button
                                type="button"
                                wire:click="selectRole({{ $role->id }})"
                                wire:key="role-nav-{{ $role->id }}"
                                @click="selectRole({{ $role->id }})"
                                class="group relative flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-left transition-colors duration-150"
                                :class="{{ $role->id }} === activeRoleId
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-sky-300'
                                    : 'text-slate-600 hover:bg-white hover:text-slate-800 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-200'">

                                <span x-show="{{ $role->id }} === activeRoleId"
                                      x-cloak
                                      class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-indigo-500 dark:bg-sky-400">
                                </span>

                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition-colors duration-150"
                                      :class="{{ $role->id }} === activeRoleId
                                          ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-sky-400'
                                          : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-500 dark:bg-gray-800 dark:text-gray-500'">
                                    <x-menu.heroicon name="{{ $role->name === 'super-admin' ? 'shield-check' : 'key' }}" class="h-3.5 w-3.5"/>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold leading-none">{{ $role->name }}</p>
                                    <p class="mt-0.5 text-[10px] leading-none opacity-60">{{ $role->menus_count }} secciones</p>
                                </div>

                                <x-menu.heroicon name="chevron-right"
                                                 class="ml-auto h-3 w-3 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-50"
                                                 :class="{{ $role->id }} === activeRoleId ? '!opacity-50' : ''"/>
                            </button>
                        @empty
                            <p class="px-2 py-4 text-center text-xs text-slate-400 dark:text-gray-600">Sin roles creados</p>
                        @endforelse
                    </nav>
                </div>
            </aside>

            {{-- ── Panel 2: Árbol del menú ──────────────────────────────────────── --}}
            <div class="flex flex-1 flex-col"
                 :class="showTree ? 'flex' : 'hidden lg:flex'">

                {{-- Botón volver — solo mobile --}}
                <div class="flex items-center border-b border-slate-100 px-4 py-2.5 dark:border-gray-800 lg:hidden">
                    <button
                        type="button"
                        @click="showTree = false"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs font-semibold text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-indigo-500/10">
                        <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5"/>
                        Roles
                    </button>
                </div>

                {{-- Empty state —————————————————————————————————————————————————— --}}
                <div x-show="!showTree"
                     class="flex flex-1 flex-col items-center justify-center gap-3 px-8 py-16 text-center">
                    <div class="mb-1 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="bars-3" class="h-8 w-8"/>
                    </div>
                    <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                        Seleccioná un rol
                    </h3>
                    <p class="max-w-xs text-sm text-slate-500 dark:text-gray-400">
                        Elegí un rol del panel izquierdo para configurar qué secciones puede ver.
                    </p>
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-900">
                        <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5 text-slate-400 dark:text-gray-600"/>
                        <span class="text-xs text-slate-400 dark:text-gray-600">Usá el panel de roles</span>
                    </div>
                </div>

                {{-- Contenido del árbol ——————————————————————————————————————————— --}}
                <div x-show="showTree" x-cloak class="flex flex-1 flex-col overflow-hidden">

                    {{-- Header: nombre del rol + botón guardar --}}
                    <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4 dark:border-gray-800">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-sky-400">
                            <x-menu.heroicon name="bars-3" class="h-5 w-5"/>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                                Árbol de navegación
                            </h3>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-gray-500">
                                Marcá las secciones que este rol puede ver
                            </p>
                        </div>
                        <x-btn.save wire:click="save" label="Guardar acceso"/>
                    </div>

                    {{-- Árbol ————————————————————————————————————————————————————— --}}
                    <div class="flex-1 overflow-y-auto px-5 py-4">
                        <div class="space-y-2">

                            @foreach($this->menuTree as $root)
                                @php $rootIds = $collectIds($root); @endphp

                                @if($root->childrenRecursive->isEmpty())
                                    {{-- Ítem raíz hoja (ej: Dashboard) ─────────────── --}}
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 shadow-sm transition-all duration-150 hover:border-indigo-200 hover:shadow-md dark:border-gray-700/60 dark:bg-gray-800/40 dark:hover:border-indigo-700/40">
                                        <input
                                            type="checkbox"
                                            wire:model="selectedMenuIds"
                                            value="{{ $root->id }}"
                                            class="h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"/>
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 dark:bg-indigo-500/15 dark:text-sky-400">
                                            <x-menu.heroicon name="{{ $root->icon ?? 'squares-2x2' }}" class="h-4 w-4"/>
                                        </span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">{{ $root->title }}</span>
                                    </label>

                                @else
                                    {{-- Sección raíz con hijos ──────────────────────── --}}
                                    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800/40">

                                        {{-- Cabecera de sección con select-all --}}
                                        <div class="flex items-center gap-3 border-b border-slate-100 bg-slate-50/80 px-4 py-2.5 dark:border-gray-700/60 dark:bg-gray-900/40">
                                            <input
                                                type="checkbox"
                                                :checked="groupAllChecked({{ json_encode($rootIds) }})"
                                                :indeterminate="groupSomeChecked({{ json_encode($rootIds) }})"
                                                @click.stop
                                                @change.stop="toggleGroup({{ json_encode($rootIds) }}, $event.target.checked)"
                                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"/>
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 dark:bg-indigo-500/15 dark:text-sky-400">
                                                <x-menu.heroicon name="{{ $root->icon ?? 'squares-2x2' }}" class="h-4 w-4"/>
                                            </span>
                                            <span class="text-sm font-bold text-slate-700 dark:text-gray-200">{{ $root->title }}</span>
                                            <div class="ml-auto flex items-center gap-2">
                                                <span class="font-label text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-600">sel. todo</span>
                                                <button type="button"
                                                        @click.stop="toggleExpand({{ $root->id }})"
                                                        class="rounded p-0.5 text-slate-400 transition-colors hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400">
                                                    <x-menu.heroicon name="chevron-down"
                                                                     class="h-3.5 w-3.5 transition-transform duration-200"
                                                                     :class="isExpanded({{ $root->id }}) ? '' : '-rotate-90'"/>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Hijos nivel 1 --}}
                                        <div class="divide-y divide-slate-100 dark:divide-gray-700/50"
                                             x-show="isExpanded({{ $root->id }})"
                                             x-collapse>
                                            @foreach($root->childrenRecursive as $l1)
                                                @php $l1Ids = $collectIds($l1); @endphp

                                                @if($l1->childrenRecursive->isEmpty())
                                                    {{-- L1 hoja (ej: Calendario) ────────────── --}}
                                                    <label class="flex cursor-pointer items-center gap-3 px-4 py-2.5 transition-colors hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5">
                                                        <input
                                                            type="checkbox"
                                                            wire:model="selectedMenuIds"
                                                            value="{{ $l1->id }}"
                                                            class="h-3.5 w-3.5 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"/>
                                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-gray-500">
                                                            <x-menu.heroicon name="{{ $l1->icon ?? 'document' }}" class="h-3.5 w-3.5"/>
                                                        </span>
                                                        <span class="text-xs font-medium text-slate-600 dark:text-gray-300">{{ $l1->title }}</span>
                                                        @if(! $l1->is_active)
                                                            <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">Próx.</span>
                                                        @endif
                                                    </label>

                                                @else
                                                    {{-- L1 subsección con hijos ─────────────── --}}
                                                    <div>
                                                        <div class="flex items-center gap-3 bg-slate-50/40 px-4 py-2 dark:bg-gray-900/20">
                                                            <input
                                                                type="checkbox"
                                                                :checked="groupAllChecked({{ json_encode($l1Ids) }})"
                                                                :indeterminate="groupSomeChecked({{ json_encode($l1Ids) }})"
                                                                @click.stop
                                                                @change.stop="toggleGroup({{ json_encode($l1Ids) }}, $event.target.checked)"
                                                                class="h-3.5 w-3.5 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"/>
                                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-gray-500">
                                                                <x-menu.heroicon name="{{ $l1->icon ?? 'folder' }}" class="h-3.5 w-3.5"/>
                                                            </span>
                                                            <span class="text-xs font-bold text-slate-600 dark:text-gray-300">{{ $l1->title }}</span>
                                                            <button type="button"
                                                                    @click.stop="toggleExpand('l{{ $l1->id }}')"
                                                                    class="ml-auto rounded p-0.5 text-slate-400 transition-colors hover:text-slate-600 dark:text-gray-600 dark:hover:text-gray-400">
                                                                <x-menu.heroicon name="chevron-down"
                                                                                 class="h-3 w-3 transition-transform duration-200"
                                                                                 :class="isExpanded('l{{ $l1->id }}') ? '' : '-rotate-90'"/>
                                                            </button>
                                                        </div>

                                                        {{-- L2 hojas ────────────────────────── --}}
                                                        <div class="divide-y divide-slate-100/70 dark:divide-gray-700/30"
                                                             x-show="isExpanded('l{{ $l1->id }}')"
                                                             x-collapse>
                                                            @foreach($l1->childrenRecursive as $l2)
                                                                <label class="flex cursor-pointer items-center gap-3 py-2 pl-14 pr-4 transition-colors hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5">
                                                                    <input
                                                                        type="checkbox"
                                                                        wire:model="selectedMenuIds"
                                                                        value="{{ $l2->id }}"
                                                                        class="h-3.5 w-3.5 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"/>
                                                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded text-slate-400 dark:text-gray-600">
                                                                        <x-menu.heroicon name="{{ $l2->icon ?? 'document' }}" class="h-3 w-3"/>
                                                                    </span>
                                                                    <span class="text-xs text-slate-600 dark:text-gray-400">{{ $l2->title }}</span>
                                                                    @if(! $l2->is_active)
                                                                        <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">Próx.</span>
                                                                    @endif
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                            @endforeach
                                        </div>

                                    </div>
                                @endif

                            @endforeach

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('manageMenuAccess', () => ({
        activeRoleId: null,
        showTree: false,
        expanded: {},

        init() {
            this.$wire.$on('menu-access-saved', () => {});
        },

        isExpanded(id) {
            return this.expanded[id] !== false;
        },

        toggleExpand(id) {
            this.expanded[id] = !this.isExpanded(id);
        },

        selectRole(id) {
            this.activeRoleId = id;
            this.showTree = true;
        },

        groupAllChecked(ids) {
            if (ids.length === 0) return false;
            return ids.every(id => this.$wire.selectedMenuIds.includes(String(id)));
        },

        groupSomeChecked(ids) {
            const hasAny = ids.some(id => this.$wire.selectedMenuIds.includes(String(id)));
            return hasAny && !this.groupAllChecked(ids);
        },

        toggleGroup(ids, checked) {
            const current = [...this.$wire.selectedMenuIds];
            ids.forEach(id => {
                const strId = String(id);
                const idx = current.indexOf(strId);
                if (checked && idx === -1) current.push(strId);
                if (!checked && idx > -1) current.splice(idx, 1);
            });
            this.$wire.set('selectedMenuIds', current);
        },
    }));
</script>
@endscript
