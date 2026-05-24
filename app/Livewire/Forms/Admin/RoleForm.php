<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Admin;

use App\Livewire\Forms\BaseForm;
use App\Models\Role;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class RoleForm extends BaseForm
{
    public string $name = '';

    #[Locked]
    public ?int $editingId = null;

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public array $moduleLabels = [
        // Clínica
        'pacientes' => 'Pacientes',
        'profesionales' => 'Profesionales',
        // Agenda
        'turnos' => 'Turnos',
        'calendario' => 'Calendario',
        // Historia Clínica
        'consultas' => 'Consultas',
        // Reportes
        'reportes' => 'Reportes',
        // Configuración › Empresa
        'empresa' => 'Empresa',
        'sucursales' => 'Sucursales',
        'departamentos' => 'Departamentos',
        'estructura' => 'Estructura Org.',
        // Configuración › Seguridad
        'usuarios' => 'Usuarios',
        'roles' => 'Roles y Permisos',
        'menu-acceso' => 'Acceso al Menú',
        'auditoria' => 'Auditoría',
        // Configuración › Parámetros
        'parametros' => 'Parámetros',
        'secuencias' => 'Secuencias',
        // Configuración › Integraciones
        'integraciones' => 'Integraciones',
    ];

    public array $moduleIcons = [
        'pacientes' => 'users',
        'profesionales' => 'academic-cap',
        'turnos' => 'clock',
        'calendario' => 'calendar-days',
        'consultas' => 'document-text',
        'reportes' => 'chart-bar',
        'empresa' => 'building-office',
        'sucursales' => 'building-storefront',
        'departamentos' => 'building-library',
        'estructura' => 'chart-bar-square',
        'usuarios' => 'users',
        'roles' => 'key',
        'menu-acceso' => 'bars-3',
        'auditoria' => 'document-magnifying-glass',
        'parametros' => 'adjustments-horizontal',
        'secuencias' => 'hashtag',
        'integraciones' => 'link',
    ];

    public array $actionLabels = [
        'view' => 'Ver',
        'create' => 'Crear',
        'update' => 'Editar',
        'delete' => 'Eliminar',
        'manage' => 'Gestionar',
        'print' => 'Imprimir',
        'export' => 'Exportar',
    ];

    public array $roleIcons = [
        'super-admin' => 'shield-check',
        'admin' => 'cog-8-tooth',
        'medico' => 'academic-cap',
        'recepcionista' => 'identification',
        'enfermero' => 'heart',
    ];

    public function storeRole(): array
    {
        $data = $this->validateServiceData();
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($this->selectedPermissions);

        return $this->notificationService()->sendNotificacion($role, 'create');
    }

    public function updateRole(): array
    {
        $data = $this->validateServiceData($this->editingId);
        $role = Role::query()->findOrFail($this->editingId);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($this->selectedPermissions);

        return $this->notificationService()->sendNotificacion($role, 'update');
    }

    public function fillRole(int $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => mb_strtolower(mb_trim($this->name)),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'roles', 'name', $excludeId),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => 'nombre del rol',
        ];
    }
}
