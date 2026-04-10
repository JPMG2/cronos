<?php

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    public bool $collapsed = false;

    #[Locked]
    public array $openMenus = [];

    public function mount(): void
    {
        $this->collapsed = session("sidebar_collapsed", false);
    }

    public function toggleSidebar(): void
    {
        $this->collapsed = ! $this->collapsed;
        session(["sidebar_collapsed" => $this->collapsed]);
    }

    public function toggleMenu(int $menuId): void
    {
        if (in_array($menuId, $this->openMenus)) {
            $this->openMenus = array_values(array_diff($this->openMenus, [$menuId]));
        } else {
            $menu = Menu::find($menuId);
            if ($menu && is_null($menu->parent_id)) {
                $rootIds = Menu::whereNull("parent_id")
                    ->pluck("id")
                    ->toArray();
                $this->openMenus = array_values(array_diff($this->openMenus, $rootIds));
            }
            $this->openMenus[] = $menuId;
        }
    }

    public function resetOpenMenus(): void
    {
        $this->openMenus = [];
    }

    public function render(): Illuminate\View\View
    {
        $homeMenu = (object) [
            "id" => 0,
            "title" => "Inicio",
            "icon" => "home",
            "route" => "dashboard",
            "order" => -1,
            "is_active" => true,
            "childrenRecursive" => collect(),
        ];

        $dbMenus = Cache::remember("sidebar_menus_db", now()->addHours(24), function () {
            return Menu::active()
                ->whereNull("parent_id")
                ->with("childrenRecursive")
                ->orderBy("order")
                ->get();
        });

        $menus = collect([$homeMenu])->concat($dbMenus);

        return $this->view(["menus" => $menus]);
    }
};
?>

{{--
    LIGHT : bg-indigo-50  — lavanda suave, mismo tono que el header
    DARK  : bg-gray-900   — mismo tono que el header

    Comportamiento responsive:
      Desktop expandido : w-64 (inline en flex layout)
      Desktop colapsado : w-20 (solo iconos)
      Mobile            : auto-colapsa a w-20 al entrar en viewport < 768px
                          restaura el estado desktop al volver a > 768px

    El botón hamburguesa del header (toggle-mobile-sidebar) llama a toggleSidebar()
    para expandir/colapsar también en mobile.
--}}

<div wire:key="sidebar-component">

    <div
        x-data="{
            sidebarCollapsed: @entangle('collapsed').live,
            _desktopCollapsed: null,
            isMobile: false,

            checkMobile() {
                const mobile = window.innerWidth < 768;
                this.isMobile = mobile;
                if (mobile && this._desktopCollapsed === null) {
                    this._desktopCollapsed = this.sidebarCollapsed;
                    this.sidebarCollapsed = true;
                    this.$wire.resetOpenMenus();
                } else if (!mobile && this._desktopCollapsed !== null) {
                    this.sidebarCollapsed = this._desktopCollapsed;
                    this._desktopCollapsed = null;
                }
            },
        }"
        x-init="checkMobile(); window.addEventListener('resize', () => checkMobile())"
        @toggle-menu.window="$wire.toggleMenu($event.detail)"
        @toggle-mobile-sidebar.window="if (!isMobile) $wire.toggleSidebar()"
        :class="sidebarCollapsed ? 'w-20' : 'w-64'"
        class="flex h-screen flex-col border-r border-indigo-100 bg-indigo-50 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900"
        id="app-sidebar"
        role="navigation"
        aria-label="Menú principal">

        {{-- ── Header ─────────────────────────────────────────── --}}
        <div
            class="flex h-16 flex-shrink-0 items-center border-b border-indigo-100 px-4 transition-all duration-300 dark:border-gray-800"
            :class="sidebarCollapsed ? 'justify-center' : 'flex-row gap-3'">

            <div :class="sidebarCollapsed ? 'hidden' : 'flex'" class="min-w-0 flex-1 flex-col overflow-hidden">
                <p class="truncate text-sm font-bold leading-none tracking-tight text-slate-800 dark:text-gray-100">
                    {{ config("app.name") }}
                </p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-indigo-400 dark:text-gray-500">
                    Medical Ecosystem
                </p>
            </div>

            {{-- Collapse/Expand button — solo en desktop --}}
            <button
                @click="$wire.toggleSidebar()"
                :aria-label="sidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'"
                :aria-expanded="!sidebarCollapsed"
                class="hidden flex-shrink-0 items-center justify-center transition-all duration-200 md:flex"
                :class="sidebarCollapsed
                    ? 'h-9 w-9 rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:bg-sky-500 dark:shadow-sky-900/30'
                    : 'h-7 w-7 rounded-lg text-indigo-400 hover:bg-indigo-100 hover:text-indigo-600 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-400'">
                <div class="relative h-4 w-4">
                    <svg class="absolute inset-0 h-4 w-4 transform transition-all duration-500"
                        :class="sidebarCollapsed ? 'opacity-0 scale-50 rotate-180' : 'opacity-100 scale-100 rotate-0'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    </svg>
                    <svg class="absolute inset-0 h-4 w-4 transform transition-all duration-500"
                        :class="sidebarCollapsed ? 'opacity-100 scale-100 rotate-0' : 'opacity-0 scale-50 -rotate-180'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </button>
        </div>

        {{-- ── Navigation ─────────────────────────────────────── --}}
        <nav class="flex-1 space-y-0.5 overflow-y-auto overflow-x-hidden py-3">
            @foreach ($menus as $menu)
                <x-menu.menu-item :menu="$menu" :open-menus="$openMenus" :collapsed="$collapsed" />
            @endforeach
        </nav>

        {{-- ── Footer ─────────────────────────────────────────── --}}
        <div class="flex-shrink-0 border-t border-indigo-100 px-4 py-3 dark:border-gray-800">
            <div :class="sidebarCollapsed ? 'hidden' : 'block'"
                class="text-center text-[10px] font-medium text-indigo-300 dark:text-gray-700">
                &copy; {{ date("Y") }} &bull; {{ config("app.name") }}
            </div>
            <div :class="sidebarCollapsed ? 'flex' : 'hidden'" class="justify-center">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.6)]"></span>
            </div>
        </div>

    </div>
</div>
