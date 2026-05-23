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
        $role = Role::findOrFail($this->editingId);
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
