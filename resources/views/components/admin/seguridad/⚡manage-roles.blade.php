<?php

declare(strict_types=1);

use App\Livewire\Forms\Admin\RoleForm;
use App\Traits\Livewire\HasNotifications;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

new #[Title('Roles y Permisos')]
class extends Component {
    use HasNotifications;

    public RoleForm $form;

    #[Computed]
    public function roles(): Collection
    {
        return Role::query()->with('permissions')->orderBy('name')->get();
    }

    #[Computed]
    public function permissionGroups(): Collection
    {
        return Permission::query()->orderBy('name')->get()
            ->groupBy(fn (Permission $p) => explode('.', $p->name)[0]);
    }

    public function create(): void
    {
        [$message, $type] = $this->form->storeRole();
        $this->messageOutPut($message, $type);
    }

    public function update(): void
    {
        [$message, $type] = $this->form->updateRole();
        $this->messageOutPut($message, $type);
    }

    public function startEdit(int $id): void
    {
        $this->form->fillRole($id);
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }

    public function delete(int $id): never
    {
        dd('Queda por implementar confirmación');
    }

    public function messageOutPut(mixed $message, mixed $type): void
    {
        unset($this->roles);
        $this->getTypeMessage($message, $type);
        $this->cancelEdit();
        $this->dispatch('role-saved');
    }
};
?>

@php
    $moduleLabels = [
        'configuracion'    => 'Configuración',
        'empresa'          => 'Empresa',
        'usuarios'         => 'Usuarios',
        'roles'            => 'Roles',
        'parametros'       => 'Parámetros',
        'pacientes'        => 'Pacientes',
        'profesionales'    => 'Profesionales',
        'agenda'           => 'Agenda',
        'historia-clinica' => 'Historia Clínica',
        'reportes'         => 'Reportes',
    ];
    $moduleIcons = [
        'configuracion'    => 'cog-8-tooth',
        'empresa'          => 'building-office',
        'usuarios'         => 'users',
        'roles'            => 'key',
        'parametros'       => 'adjustments-horizontal',
        'pacientes'        => 'user-group',
        'profesionales'    => 'academic-cap',
        'agenda'           => 'calendar-days',
        'historia-clinica' => 'clipboard-document-list',
        'reportes'         => 'chart-bar',
    ];
    $actionLabels = [
        'ver'       => 'Ver',
        'crear'     => 'Crear',
        'editar'    => 'Editar',
        'eliminar'  => 'Eliminar',
        'gestionar' => 'Gestionar',
    ];
@endphp

<x-form-style.border-style>
    <x-form-style.main-div>

        {{-- ══ Header ═════════════════════════════════════════════════════════════ --}}
        <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4 dark:border-gray-800">
            <div>
                <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                    Roles y Permisos
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                    Definí los roles del sistema y asignales los permisos por módulo.
                </p>
            </div>
            <div class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
                <span class="h-2 w-2 motion-safe:animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
                <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                    Roles del Sistema
                </span>
            </div>
        </div>

        {{-- ══ Tres paneles en cadena ══════════════════════════════════════════════ --}}
        <div class="flex min-h-[560px] flex-col lg:flex-row"
             x-data="manageRoles">

            {{-- ── Panel 1: Lista de roles ──────────────────────────────────────── --}}
            <aside class="shrink-0 border-b border-slate-100 bg-slate-50/60 dark:border-gray-800 dark:bg-gray-900/40 lg:w-56 lg:border-b-0 lg:border-r"
                   :class="showEditor ? 'hidden lg:block' : 'block'">

                <div class="px-3 py-3">

                    {{-- Botón nuevo rol --}}
                    <button
                        type="button"
                        @click="newRole"
                        wire:click="cancelEdit"
                        class="flex w-full items-center gap-2 rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm transition-colors duration-150 hover:bg-indigo-50 dark:border-indigo-700/40 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                        <x-menu.heroicon name="plus" class="h-3.5 w-3.5"/>
                        Nuevo rol
                    </button>

                    {{-- Lista de roles --}}
                    <nav class="mt-3 space-y-0.5">
                        @forelse($this->roles as $role)
                            <button
                                type="button"
                                wire:click="startEdit({{ $role->id }})"
                                wire:key="role-nav-{{ $role->id }}"
                                @click="selectRole({{ $role->id }})"
                                class="group relative flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-left transition-colors duration-150"
                                :class="{{ $role->id }} === activeRoleId
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-sky-300'
                                    : 'text-slate-600 hover:bg-white hover:text-slate-800 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-200'">

                                {{-- Línea activa --}}
                                <span x-show="{{ $role->id }} === activeRoleId"
                                      x-cloak
                                      class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-indigo-500 dark:bg-sky-400">
                                </span>

                                {{-- Ícono --}}
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition-colors duration-150"
                                      :class="{{ $role->id }} === activeRoleId
                                          ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-sky-400'
                                          : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-500 dark:bg-gray-800 dark:text-gray-500'">
                                    <x-menu.heroicon name="{{ $role->name === 'super-admin' ? 'shield-check' : 'key' }}" class="h-3.5 w-3.5"/>
                                </span>

                                {{-- Nombre + permisos --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold leading-none">{{ $role->name }}</p>
                                    <p class="mt-0.5 text-[10px] leading-none opacity-60">{{ $role->permissions->count() }} permisos</p>
                                </div>

                                <x-menu.heroicon name="chevron-right" class="ml-auto h-3 w-3 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-50"
                                                 :class="{{ $role->id }} === activeRoleId ? '!opacity-50' : ''"/>
                            </button>
                        @empty
                            <p class="px-2 py-4 text-center text-xs text-slate-400 dark:text-gray-600">Sin roles creados</p>
                        @endforelse
                    </nav>

                </div>
            </aside>

            {{-- ── Panel 2: Nombre del rol + módulos de permisos ───────────────── --}}
            <aside class="shrink-0 border-b border-slate-100 bg-slate-50/60 dark:border-gray-800 dark:bg-gray-900/40 lg:w-64 lg:border-b-0 lg:border-r"
                   :class="showEditor
                       ? (selectedModule ? 'hidden lg:block' : 'block')
                       : 'hidden lg:block'">

                {{-- Botón volver — solo mobile --}}
                <div class="flex items-center border-b border-slate-100 px-4 py-2.5 dark:border-gray-800 lg:hidden">
                    <button
                        type="button"
                        @click="showEditor = false; selectedModule = null; $wire.cancelEdit()"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs font-semibold text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-indigo-500/10">
                        <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5"/>
                        Roles
                    </button>
                </div>

                <div x-show="showEditor" x-cloak class="flex h-full flex-col">

                    {{-- Formulario de nombre --}}
                    <div class="border-b border-slate-100 px-3 py-3 dark:border-gray-800">
                        <x-form-inputs.text_input
                            label="Nombre del rol"
                            name="name"
                            icon="key"
                            placeholder="Ej: medico, recepcionista…"
                            wire:model="form.name"
                            alpineError="name"
                            size="sm"
                            required/>
                        <div class="mt-2 flex items-center justify-end gap-2">
                            <x-btn.mini-cancel @click="cancelEdit"/>
                            <x-btn.save
                                label="{{ $this->form->editingId ? 'Actualizar' : 'Guardar' }}"
                                @click="submit()"
                                wireTarget="create,update"/>
                        </div>
                    </div>

                    {{-- Lista de módulos --}}
                    <div class="overflow-y-auto px-3 py-3">
                        <p class="mb-2 px-1 font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600">
                            Módulos
                        </p>
                        <nav class="space-y-0.5">
                            @foreach($this->permissionGroups as $module => $permissions)
                                @php $permNames = $permissions->pluck('name')->toArray(); @endphp
                                <button
                                    type="button"
                                    @click="selectedModule = '{{ $module }}'"
                                    class="group relative flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-left transition-colors duration-150"
                                    :class="selectedModule === '{{ $module }}'
                                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-sky-300'
                                        : 'text-slate-600 hover:bg-white hover:text-slate-800 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800/70 dark:hover:text-gray-200'">

                                    {{-- Línea activa --}}
                                    <span x-show="selectedModule === '{{ $module }}'"
                                          x-cloak
                                          class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-indigo-500 dark:bg-sky-400">
                                    </span>

                                    {{-- Checkbox select-all --}}
                                    <input
                                        type="checkbox"
                                        class="h-3.5 w-3.5 shrink-0 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800"
                                        :checked="groupAllChecked('{{ $module }}', {{ json_encode($permNames) }})"
                                        @click.stop
                                        @change.stop="toggleGroup('{{ $module }}', {{ json_encode($permNames) }}, $event.target.checked)"/>

                                    {{-- Ícono --}}
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md transition-colors duration-150"
                                          :class="selectedModule === '{{ $module }}'
                                              ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-sky-400'
                                              : 'bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-500 dark:bg-gray-800 dark:text-gray-500'">
                                        <x-menu.heroicon name="{{ $moduleIcons[$module] ?? 'squares-2x2' }}" class="h-3.5 w-3.5"/>
                                    </span>

                                    {{-- Nombre + contador --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold leading-none">
                                            {{ $moduleLabels[$module] ?? ucfirst($module) }}
                                        </p>
                                        <p class="mt-0.5 text-[10px] leading-none opacity-60">
                                            {{ $permissions->count() }} permisos
                                        </p>
                                    </div>

                                    <x-menu.heroicon name="chevron-right" class="ml-auto h-3 w-3 shrink-0 opacity-0 transition-opacity duration-150 group-hover:opacity-50"
                                                     :class="selectedModule === '{{ $module }}' ? '!opacity-50' : ''"/>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                </div>

                {{-- Empty state cuando no hay rol seleccionado — solo desktop --}}
                <div x-show="!showEditor"
                     class="hidden flex-1 flex-col items-center justify-center gap-2 px-6 py-12 text-center lg:flex">
                    <x-menu.heroicon name="key" class="h-8 w-8 text-slate-300 dark:text-gray-700"/>
                    <p class="text-xs text-slate-400 dark:text-gray-600">Seleccioná un rol</p>
                </div>

            </aside>

            {{-- ── Panel 3: Permisos del módulo seleccionado ───────────────────── --}}
            <div class="flex flex-1 flex-col"
                 :class="selectedModule ? 'flex' : 'hidden lg:flex'">

                {{-- Botón volver — solo mobile --}}
                <div class="flex items-center border-b border-slate-100 px-4 py-2.5 dark:border-gray-800 lg:hidden">
                    <button
                        type="button"
                        @click="selectedModule = null"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs font-semibold text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-sky-400 dark:hover:bg-indigo-500/10">
                        <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5"/>
                        Módulos
                    </button>
                </div>

                {{-- Empty state — ningún módulo seleccionado --}}
                <div x-show="!selectedModule"
                     class="flex flex-1 flex-col items-center justify-center gap-3 px-8 py-16 text-center">
                    <div class="mb-1 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <x-menu.heroicon name="shield-check" class="h-8 w-8"/>
                    </div>
                    <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                        Seleccioná un módulo
                    </h3>
                    <p class="max-w-xs text-sm text-slate-500 dark:text-gray-400">
                        Elegí un módulo del panel central para ver y configurar sus permisos.
                    </p>
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-900">
                        <x-menu.heroicon name="arrow-left" class="h-3.5 w-3.5 text-slate-400 dark:text-gray-600"/>
                        <span class="text-xs text-slate-400 dark:text-gray-600">Usá el panel de módulos</span>
                    </div>
                </div>

                {{-- Permisos por módulo --}}
                @foreach($this->permissionGroups as $module => $permissions)
                    <div x-show="selectedModule === '{{ $module }}'" x-cloak>

                        {{-- Header del módulo --}}
                        <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4 dark:border-gray-800">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-sky-400">
                                <x-menu.heroicon name="{{ $moduleIcons[$module] ?? 'squares-2x2' }}" class="h-5 w-5"/>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                                    {{ $moduleLabels[$module] ?? ucfirst($module) }}
                                </h3>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-gray-500">
                                    {{ $permissions->count() }} {{ $permissions->count() === 1 ? 'permiso disponible' : 'permisos disponibles' }}
                                </p>
                            </div>
                            <div class="shrink-0 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-1 dark:border-indigo-800/30 dark:bg-indigo-900/20">
                                <span class="font-label text-[10px] font-bold uppercase tracking-wider text-indigo-500 dark:text-sky-400">
                                    Permisos
                                </span>
                            </div>
                        </div>

                        {{-- Permisos --}}
                        <div class="px-6 py-5">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($permissions as $permission)
                                    @php $action = explode('.', $permission->name)[1] ?? $permission->name; @endphp
                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all duration-150 hover:border-indigo-200 hover:shadow-md dark:border-gray-700/60 dark:bg-gray-800/40 dark:hover:border-indigo-700/40">
                                        <input
                                            type="checkbox"
                                            wire:model="form.selectedPermissions"
                                            value="{{ $permission->name }}"
                                            class="mt-0.5 h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/25 dark:border-gray-600 dark:bg-gray-800 dark:text-indigo-400"/>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-700 dark:text-gray-200">
                                                {{ $actionLabels[$action] ?? ucfirst($action) }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-400 dark:text-gray-500">
                                                {{ $permission->name }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </x-form-style.main-div>
</x-form-style.border-style>

@script
<script>
    Alpine.data('manageRoles', () => ({
        showEditor: false,
        selectedModule: null,
        activeRoleId: null,
        errors: {},

        init() {
            this.$wire.$on('role-saved', () => {
                this.showEditor = false;
                this.selectedModule = null;
                this.activeRoleId = null;
                this.errors = {};
            });
        },

        newRole() {
            this.showEditor = true;
            this.selectedModule = null;
            this.activeRoleId = null;
            this.errors = {};
        },

        selectRole(id) {
            this.showEditor = true;
            this.selectedModule = null;
            this.activeRoleId = id;
            this.errors = {};
        },

        cancelEdit() {
            this.$wire.cancelEdit();
            this.showEditor = false;
            this.selectedModule = null;
            this.activeRoleId = null;
            this.errors = {};
        },

        groupAllChecked(module, permNames) {
            if (permNames.length === 0) return false;
            return permNames.every(p => this.$wire.form.selectedPermissions.includes(p));
        },

        toggleGroup(module, permNames, checked) {
            const current = [...this.$wire.form.selectedPermissions];
            permNames.forEach(p => {
                const idx = current.indexOf(p);
                if (checked && idx === -1) current.push(p);
                if (!checked && idx > -1) current.splice(idx, 1);
            });
            this.$wire.set('form.selectedPermissions', current);
        },

        submit() {
            const isEditing = this.$wire.form.editingId !== null;
            this.errors = validate(
                {name: this.$wire.form.name},
                {name: ['required', ['minLength', 3]]},
            );
            if (Object.keys(this.errors).length === 0) {
                isEditing ? this.$wire.update() : this.$wire.create();
            }
        },
    }));
</script>
@endscript
