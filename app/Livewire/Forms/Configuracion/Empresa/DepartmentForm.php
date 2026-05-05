<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use App\Actions\Configuracion\DepartmentAction\CreateDepartmentAction;
use App\Actions\Configuracion\DepartmentAction\UpdateDepartmentAction;
use App\Actions\Configuracion\Parameters\GenerateSequenceCodeAction;
use App\Livewire\Forms\BaseForm;
use App\Models\Department;
use App\Rules\AttributeValidator;
use App\Services\NotificationService;
use Livewire\Attributes\Locked;

final class DepartmentForm extends BaseForm
{
    #[Locked]
    public ?int $id = null;

    #[Locked]
    public string $code = '';

    public string $name = '';

    public ?int $current_status_id = null;

    public string $description = '';

    public array $dataDepartment = [];

    private ?NotificationService $notificationService = null;

    public function fillFromDepartment(int $departmentId): void
    {
        $department = Department::query()->findOrFail($departmentId);

        $this->id = $department->id;
        $this->current_status_id = $department->current_status_id;
        $this->name = $department->name;
        $this->code = $department->code;
        $this->description = $department->description ?? '';
    }

    public function validateDepartment(): void
    {
        $this->dataDepartment = $this->validateServiceData($this->id);
    }

    public function createDepartment(): array
    {
        return $this->tryAction(function () {
            $this->code = app(GenerateSequenceCodeAction::class)->handle('Departamento');
            $this->dataDepartment['code'] = $this->code;
            $model = app(CreateDepartmentAction::class)->handle($this->dataDepartment);

            return $this->notificationService()->sendNotificacion($model, 'create');

        }, 'Error al guardar el departamento: ');
    }

    public function updateDepartment(): array
    {
        return $this->tryAction(function () {
            $data = array_merge(['id' => $this->id],
                $this->dataDepartment);
            $model = app(UpdateDepartmentAction::class)->handle($data);

            return $this->notificationService()->sendNotificacion($model, 'update');

        }, 'Error al actualizar el departamento: ');
    }

    protected function transformServiceData(): array
    {
        return [
            'name' => mb_trim($this->name),
            'current_status_id' => $this->current_status_id,
            'description' => mb_trim($this->description),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'name' => AttributeValidator::uniqueIdNameLength('3', 'departments', 'name', $excludeId),
            'current_status_id' => AttributeValidator::requireAndExists('current_statuses', 'id', 'current_status_id', true),
            'description' => AttributeValidator::stringValid(false, '4'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'current_status_id' => config('nicename.current_status_id'),
            'description' => config('nicename.description'),
        ];
    }

    private function notificationService(): NotificationService
    {
        return $this->notificationService ??= resolve(NotificationService::class);
    }
}
