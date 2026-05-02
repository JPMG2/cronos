<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class SequenceForm extends BaseForm
{
    #[Locked]
    public ?int $sequenceId = null;

    public string $entity = '';

    public string $prefix = '';

    public ?int $padding = null;

    public string $separator = '';

    public ?int $current_value = null;

    public ?int $increment = null;

    public array $dataSequence = [];

    public function validateSequence(): void
    {
        $this->dataSequence = $this->validateServiceData($this->sequenceId);
    }

    protected function transformServiceData(): array
    {
        return [
            'entity' => mb_trim($this->entity),
            'prefix' => mb_trim($this->prefix),
            'padding' => $this->padding,
            'separator' => mb_trim($this->separator),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'entity' => AttributeValidator::uniqueIdNameLength('3', 'sequences', 'entity', $excludeId),
            'prefix' => AttributeValidator::uniqueIdNameLength('3', 'sequences', 'prefix', $excludeId),
            'padding' => AttributeValidator::numericInteger(true),
            'separator' => AttributeValidator::stringValid(true, '1'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'entity' => config('nicename.entity'),
            'prefix' => config('nicename.prefix'),
            'padding' => config('nicename.padding'),
            'separator' => config('nicename.separator'),
        ];
    }
}
