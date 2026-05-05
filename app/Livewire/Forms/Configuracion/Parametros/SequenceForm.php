<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Actions\Configuracion\Parameters\CreateSequenceAction;
use App\Actions\Configuracion\Parameters\UpdateSequenceAction;
use App\Livewire\Forms\BaseForm;
use App\Models\Sequence;
use App\Rules\AttributeValidator;
use App\Services\NotificationService;
use Livewire\Attributes\Locked;

final class SequenceForm extends BaseForm
{
    #[Locked]
    public ?int $sequenceId = null;

    public string $entity = '';

    public string $prefix = '';

    public ?int $padding = null;

    public ?string $separator = '';

    public ?int $current_value = null;

    public ?int $increment = null;

    public array $dataSequence = [];

    public bool $isUsed = false;

    private ?NotificationService $notificationService = null;

    public function validateSequence(): void
    {
        $this->dataSequence = $this->validateServiceData($this->sequenceId);
    }

    public function createSequence(): array
    {

        return $this->tryAction(function () {

            $model = app(CreateSequenceAction::class)->handle($this->dataSequence);

            return $this->notificationService()->sendNotificacion($model, 'create');

        }, 'Error al crear la sequencia: ');

    }

    public function updateSequence(): array
    {
        return $this->tryAction(function () {
            $data = array_merge(['id' => $this->sequenceId], $this->dataSequence);
            $model = app(UpdateSequenceAction::class)->handle($data);

            return $this->notificationService()->sendNotificacion($model, 'update');

        }, 'Error al actualizar la sequencia: ');

    }

    public function fillFromSequence(int $sequenceId): void
    {
        $data = Sequence::query()->findOrFail($sequenceId);

        $this->sequenceId = $data->id;
        $this->entity = $data->entity;
        $this->prefix = $data->prefix;
        $this->padding = $data->padding;
        $this->separator = $data->separator;
        $this->current_value = $data->current_value;
        $this->increment = $data->increment;
        $this->isUsed = $data->is_used;
    }

    protected function transformServiceData(): array
    {
        return [
            'entity' => mb_trim($this->entity),
            'prefix' => mb_trim($this->prefix),
            'padding' => $this->padding,
            'separator' => mb_trim($this->separator),
            'current_value' => $this->current_value,
            'increment' => $this->increment,
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'entity' => AttributeValidator::uniqueIdNameLength('3', 'sequences', 'entity', $excludeId),
            'prefix' => AttributeValidator::uniqueIdNameLength('3', 'sequences', 'prefix', $excludeId),
            'padding' => AttributeValidator::numericInteger(false, 3),
            'separator' => AttributeValidator::stringValid(false, '1'),
            'current_value' => AttributeValidator::numericInteger(true, 1),
            'increment' => AttributeValidator::numericInteger(true, 1),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'entity' => config('nicename.entity'),
            'prefix' => config('nicename.prefix'),
            'padding' => config('nicename.padding'),
            'separator' => config('nicename.separator'),
            'current_value' => config('nicename.current_value'),
            'increment' => config('nicename.increment'),
        ];
    }

    private function notificationService(): NotificationService
    {
        return $this->notificationService ??= resolve(NotificationService::class);
    }
}
