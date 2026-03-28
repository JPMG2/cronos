@props([
    "menu",
    "openMenus" => [],
    "level" => 0,
    "collapsed" => false,
])

@php
    $children = $menu->childrenRecursive ?? collect();
    $hasChildren = $children->count() > 0;
    $isOpen = in_array($menu->id, $openMenus);
    $isActive = ! $hasChildren && $menu->route && \Illuminate\Support\Facades\Route::has($menu->route) && request()->routeIs($menu->route);

    $href = "#";
    if (! $hasChildren && $menu->route && \Illuminate\Support\Facades\Route::has($menu->route)) {
        try {
            $href = route($menu->route);
        } catch (Exception $e) {
        }
    }
@endphp

{{--
    ┌─────────────────────────────────────────────────────────────────┐
    │  JERARQUÍA DE COLORES — sidebar bg-indigo-50 / dark:bg-gray-900 │
    │                                                                   │
    │  LIGHT MODE (bg-indigo-50):                                      │
    │    Nivel 0 activo   → bg-indigo-600  text-white     (fuerte)     │
    │    Nivel 0 abierto  → bg-indigo-100  text-slate-700 (medio)      │
    │    Nivel 0 inactivo → text-slate-500 hover:bg-indigo-100         │
    │    Nivel 1 activo   → text-indigo-600 font-semibold  (acento)    │
    │    Nivel 1 inactivo → text-slate-500 hover:text-slate-700        │
    │    Nivel 2+         → text-slate-400 hover:text-slate-600        │
    │    Árbol            → bg-indigo-200/50                           │
    │                                                                   │
    │  DARK MODE (bg-gray-900):                                        │
    │    Nivel 0 activo   → bg-sky-500/20  text-sky-300   (acento)     │
    │    Nivel 0 abierto  → bg-gray-800    text-gray-200  (prominente) │
    │    Nivel 0 inactivo → text-gray-500  hover:bg-gray-800           │
    │    Nivel 1 activo   → text-sky-400                               │
    │    Nivel 1 inactivo → text-gray-500  hover:text-gray-300         │
    │    Nivel 2+         → text-gray-600  hover:text-gray-400         │
    │    Árbol            → bg-gray-700/50                             │
    └─────────────────────────────────────────────────────────────────┘
--}}

<div
    x-data="{
        open: @js($isOpen),
        menuId: {{ $menu->id }},
        hovering: false,
        collapsed: @js($collapsed),
        init() {
            this.$watch('$wire.openMenus', (value) => {
                const menus = Array.isArray(value)
                    ? value
                    : Object.values(value || {})
                this.open = menus.includes(this.menuId)
            })
            this.$watch('collapsed', (newVal, oldVal) => {
                if (newVal !== oldVal) {
                    this.hovering = false
                    if (newVal) {
                        this.open = false
                    }
                }
            })
        },
    }"
    x-init="
        $watch('$wire.collapsed', (value) => {
            collapsed = value
        })
    "
    wire:key="menu-item-{{ $menu->id }}-{{ $collapsed ? "collapsed" : "expanded" }}"
    class="relative">

    {{-- ══════════════════════════════════════════════════════════════
         COLAPSADO — Solo icono centrado (nivel 0)
         ══════════════════════════════════════════════════════════════ --}}
    @if ($collapsed && $level === 0)
        @if ($hasChildren)
            <button
                x-ref="menuButton"
                x-on:click="open = !open; $dispatch('toggle-menu', {{ $menu->id }})"
                x-on:mouseenter="hovering = true"
                x-on:mouseleave="hovering = false"
                class="group relative flex w-full items-center justify-center py-1.5 transition-all duration-200"
                title="{{ $menu->title }}">
                <span
                    :class="open
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-gray-800 dark:text-sky-400'
                        : 'text-slate-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300'"
                    class="flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-200">
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-5 w-5 shrink-0" />
                </span>
                <div class="pointer-events-none absolute left-full z-50 ml-3 whitespace-nowrap rounded-lg bg-slate-800 px-3 py-1.5 text-[11px] font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:translate-x-1 group-hover:opacity-100 dark:bg-gray-700">
                    {{ $menu->title }}
                </div>
            </button>

            {{-- Flyout --}}
            <div
                x-show="hovering"
                x-transition:enter="transition duration-200 ease-out"
                x-transition:enter-start="translate-x-4 scale-95 opacity-0"
                x-transition:enter-end="translate-x-0 scale-100 opacity-100"
                x-transition:leave="transition duration-150 ease-in"
                x-transition:leave-end="translate-x-4 scale-95 opacity-0"
                x-on:mouseenter="hovering = true"
                x-on:mouseleave="hovering = false"
                class="fixed z-[9999] w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 dark:border-gray-700 dark:bg-gray-800 dark:shadow-black/40"
                style="display: none"
                x-bind:style="`left: 5.5rem; top: ${$refs.menuButton ? $refs.menuButton.getBoundingClientRect().top - 8 : 0}px;`">
                <div class="flex items-center gap-2.5 border-b border-slate-100 bg-slate-50 px-4 py-3 dark:border-gray-700/60 dark:bg-gray-800/80">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="{{ $menu->icon }}" class="h-4 w-4" />
                    </div>
                    <span class="text-xs font-semibold text-slate-700 dark:text-gray-200">{{ $menu->title }}</span>
                </div>
                <div class="max-h-80 overflow-y-auto py-1.5">
                    @foreach ($children as $child)
                        @php
                            $flyChildHasChildren = ($child->childrenRecursive ?? collect())->count() > 0;
                            $flyHref = "#";
                            if (! $flyChildHasChildren && $child->route && \Illuminate\Support\Facades\Route::has($child->route)) {
                                try { $flyHref = route($child->route); } catch (Exception $e) {}
                            }
                        @endphp

                        @if ($flyChildHasChildren)
                            <div x-data="{ expanded: false }" class="px-1.5">
                                <button
                                    x-on:click="expanded = !expanded"
                                    :class="expanded
                                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200'"
                                    class="group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200">
                                    <x-menu.heroicon name="{{ $child->icon }}" class="h-4 w-4 flex-shrink-0 text-slate-400 group-hover:text-indigo-500 dark:text-gray-600 dark:group-hover:text-indigo-400" />
                                    <span class="flex-1 text-left">{{ $child->title }}</span>
                                    <svg :class="expanded ? 'rotate-180 text-indigo-500' : 'text-slate-400'"
                                        class="h-3 w-3 flex-shrink-0 transition-all duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="expanded" x-collapse class="mt-0.5 space-y-0.5 pb-1">
                                    @foreach ($child->childrenRecursive as $subChild)
                                        @php
                                            $subHref = "#";
                                            if ($subChild->route && \Illuminate\Support\Facades\Route::has($subChild->route)) {
                                                try { $subHref = route($subChild->route); } catch (Exception $e) {}
                                            }
                                        @endphp
                                        <a href="{{ $subHref }}"
                                            @if ($subHref !== "#") wire:navigate x-on:click="if (window.innerWidth < 768) { $dispatch('close-mobile-sidebar') }" @endif
                                            @class([
                                                "group flex items-center gap-2.5 rounded-lg py-1.5 pl-9 pr-3 text-[11px] font-medium transition-all duration-200",
                                                "bg-indigo-600 text-white" => request()->url() === $subHref,
                                                "text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-700/60 dark:hover:text-gray-300" => request()->url() !== $subHref,
                                            ])>
                                            <span class="flex-1">{{ $subChild->title }}</span>
                                            <div @class([
                                                "h-1.5 w-1.5 rounded-full bg-emerald-400 transition-all duration-200",
                                                "opacity-100" => request()->url() === $subHref,
                                                "opacity-0 group-hover:opacity-60" => request()->url() !== $subHref,
                                            ])></div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="px-1.5">
                                <a href="{{ $flyHref }}"
                                    @if ($flyHref !== "#") wire:navigate x-on:click="if (window.innerWidth < 768) { $dispatch('close-mobile-sidebar') }" @endif
                                    @class([
                                        "group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200",
                                        "bg-indigo-600 text-white shadow-sm" => request()->url() === $flyHref,
                                        "text-slate-600 hover:bg-slate-100 hover:text-slate-800 dark:text-gray-400 dark:hover:bg-gray-700/60 dark:hover:text-gray-200" => request()->url() !== $flyHref,
                                    ])>
                                    <x-menu.heroicon name="{{ $child->icon }}"
                                        @class([
                                            "h-4 w-4 flex-shrink-0 transition-all duration-200",
                                            "text-white" => request()->url() === $flyHref,
                                            "text-slate-400 group-hover:text-indigo-500 dark:text-gray-600 dark:group-hover:text-indigo-400" => request()->url() !== $flyHref,
                                        ]) />
                                    <span class="flex-1">{{ $child->title }}</span>
                                    <div @class([
                                        "h-1.5 w-1.5 rounded-full bg-emerald-400 transition-all duration-200",
                                        "opacity-100" => request()->url() === $flyHref,
                                        "opacity-0 group-hover:opacity-60" => request()->url() !== $flyHref,
                                    ])></div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        @else
            {{-- Colapsado — leaf sin hijos --}}
            <a href="{{ $href }}"
                @if ($href !== "#") wire:navigate x-on:click="if (window.innerWidth < 768) { $dispatch('close-mobile-sidebar') }" @endif
                x-on:mouseenter="hovering = true"
                x-on:mouseleave="hovering = false"
                class="group relative flex w-full items-center justify-center py-1.5 transition-all duration-200"
                title="{{ $menu->title }}">
                <span @class([
                    "flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition-all duration-200",
                    "bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:bg-sky-500/20 dark:text-sky-300 dark:shadow-none" => $isActive,
                    "text-slate-500 hover:bg-indigo-100 hover:text-indigo-700 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300" => ! $isActive,
                ])>
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-5 w-5 shrink-0" />
                </span>
                <div class="pointer-events-none absolute left-full z-50 ml-3 whitespace-nowrap rounded-lg bg-slate-800 px-3 py-1.5 text-[11px] font-semibold text-white opacity-0 shadow-xl transition-all duration-200 group-hover:translate-x-1 group-hover:opacity-100 dark:bg-gray-700">
                    {{ $menu->title }}
                </div>
            </a>
        @endif

    {{-- ══════════════════════════════════════════════════════════════
         NIVEL 0 EXPANDIDO
         ══════════════════════════════════════════════════════════════ --}}
    @elseif ($level === 0)
        @if ($hasChildren)
            <button
                x-on:click="open = !open; $dispatch('toggle-menu', {{ $menu->id }})"
                class="group relative mx-2 flex w-[calc(100%-1rem)] items-center gap-2.5 rounded-xl px-3 py-2 text-[11px] font-semibold uppercase tracking-wide transition-all duration-200"
                :class="open
                    ? 'bg-indigo-200/80 text-indigo-800 dark:bg-gray-800 dark:text-gray-200'
                    : 'text-slate-700 hover:bg-indigo-100/80 hover:text-indigo-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300'">
                <span
                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center transition-all duration-200"
                    :class="open
                        ? 'text-indigo-700 dark:text-sky-400'
                        : 'text-indigo-400 group-hover:text-indigo-700 dark:text-gray-600 dark:group-hover:text-gray-400'">
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-4 w-4" />
                </span>
                <span class="flex-1 text-left">{{ $menu->title }}</span>
                <svg
                    :class="open
                        ? 'rotate-180 text-indigo-400 dark:text-gray-500'
                        : 'text-slate-400 group-hover:text-indigo-500 dark:text-gray-700 dark:group-hover:text-gray-500'"
                    class="h-3 w-3 flex-shrink-0 transition-all duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse>
                <div class="relative mt-0.5 space-y-0.5 pb-1 pt-0.5">
                    <span class="pointer-events-none absolute inset-y-1 left-[1.35rem] w-px bg-indigo-300/70 dark:bg-gray-700/60" aria-hidden="true"></span>
                    @foreach ($children as $child)
                        <x-menu.menu-item :menu="$child" :open-menus="$openMenus" :level="1" :collapsed="$collapsed" />
                    @endforeach
                </div>
            </div>

        @else
            <a href="{{ $href }}"
                @if ($href !== "#") wire:navigate x-on:click="if (window.innerWidth < 768) { $dispatch('close-mobile-sidebar') }" @endif
                @class([
                    "group relative mx-2 flex w-[calc(100%-1rem)] items-center gap-2.5 rounded-xl px-3 py-2 text-[11px] font-semibold uppercase tracking-wide transition-all duration-200",
                    "bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:bg-sky-500/20 dark:text-sky-300 dark:shadow-none" => $isActive,
                    "text-slate-700 hover:bg-indigo-100 hover:text-indigo-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300" => ! $isActive,
                ])>
                <span @class([
                    "flex h-7 w-7 flex-shrink-0 items-center justify-center transition-all duration-200",
                    "text-white dark:text-sky-300" => $isActive,
                    "text-indigo-400 group-hover:text-indigo-700 dark:text-gray-600 dark:group-hover:text-gray-400" => ! $isActive,
                ])>
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-4 w-4" />
                </span>
                <span class="flex-1">{{ $menu->title }}</span>
                @if ($isActive)
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.7)]"></span>
                @endif
            </a>
        @endif

    {{-- ══════════════════════════════════════════════════════════════
         NIVEL 1+ EXPANDIDO
         ══════════════════════════════════════════════════════════════ --}}
    @else
        @if ($hasChildren)
            <button
                x-on:click="open = !open; $dispatch('toggle-menu', {{ $menu->id }})"
                @php
                    $marginLeft = "mx-2";
                    if ($level === 1) { $marginLeft = "ml-7 mr-2"; }
                    elseif ($level > 1) { $marginLeft = "ml-11 mr-2"; }
                @endphp
                @class([
                    "group relative $marginLeft flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-[11px] font-medium transition-all duration-200",
                    "w-[calc(100%-1rem)]" => $level === 0,
                    "w-[calc(100%-2.25rem)]" => $level === 1,
                    "w-[calc(100%-3.25rem)]" => $level > 1,
                    "bg-indigo-100/80 text-indigo-700 dark:bg-gray-800 dark:text-gray-300" => $isOpen,
                    "text-slate-600 hover:bg-indigo-100/70 hover:text-indigo-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300" => ! $isOpen,
                ])>
                <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center text-indigo-400 transition-all duration-200 group-hover:text-indigo-600 dark:text-gray-600 dark:group-hover:text-gray-400">
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-3.5 w-3.5" />
                </div>
                <span class="flex-1 text-left">{{ $menu->title }}</span>
                <svg
                    :class="open
                        ? 'rotate-180 text-indigo-600 dark:text-gray-500'
                        : 'text-slate-400 group-hover:text-indigo-500 dark:text-gray-700 dark:group-hover:text-gray-600'"
                    class="h-3 w-3 flex-shrink-0 transition-all duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse>
                <div class="relative mt-0.5 space-y-0.5 pb-1 pt-0.5">
                    <span class="pointer-events-none absolute inset-y-1 left-10 w-px bg-indigo-300/60 dark:bg-gray-700/50" aria-hidden="true"></span>
                    @foreach ($children as $child)
                        <x-menu.menu-item :menu="$child" :open-menus="$openMenus" :level="$level + 1" :collapsed="$collapsed" />
                    @endforeach
                </div>
            </div>

        @else
            <a href="{{ $href }}"
                @if ($href !== "#") wire:navigate x-on:click="if (window.innerWidth < 768) { $dispatch('close-mobile-sidebar') }" @endif
                @php
                    $marginLeft = "mx-2";
                    if ($level === 1) { $marginLeft = "ml-7 mr-2"; }
                    elseif ($level > 1) { $marginLeft = "ml-11 mr-2"; }
                @endphp
                @class([
                    "group relative $marginLeft flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-[11px] font-medium transition-all duration-200",
                    "w-[calc(100%-1rem)]" => $level === 0,
                    "w-[calc(100%-2.25rem)]" => $level === 1,
                    "w-[calc(100%-3.25rem)]" => $level > 1,
                    "text-indigo-600 font-semibold dark:text-sky-400" => $isActive,
                    "text-slate-600 hover:bg-indigo-100/70 hover:text-indigo-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300" => ! $isActive,
                ])>
                <div @class([
                    "flex h-5 w-5 flex-shrink-0 items-center justify-center transition-all duration-200",
                    "text-indigo-500 dark:text-sky-400" => $isActive,
                    "text-indigo-400 group-hover:text-indigo-600 dark:text-gray-600 dark:group-hover:text-gray-400" => ! $isActive,
                ])>
                    <x-menu.heroicon name="{{ $menu->icon }}" class="h-3.5 w-3.5" />
                </div>
                <span class="flex-1">{{ $menu->title }}</span>
                <div @class([
                    "h-1.5 w-1.5 rounded-full bg-emerald-600 shadow-[0_0_8px_rgba(5,150,105,0.7)] transition-all duration-200 dark:bg-emerald-400 dark:shadow-[0_0_6px_rgba(52,211,153,0.6)]",
                    "opacity-100" => $isActive,
                    "opacity-0 group-hover:opacity-100" => ! $isActive,
                ])></div>
            </a>
        @endif
    @endif
</div>
