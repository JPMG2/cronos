<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Validator;
use Livewire\Form;

abstract class BaseForm extends Form
{
    abstract protected function transformServiceData(): array;

    abstract protected function getValidationRules(?int $excludeId = null): array;

    protected function validateServiceData(?int $excludeId = null): array
    {
        return Validator::make(
            $this->transformServiceData(),
            $this->getValidationRules($excludeId),
            [],
            $this->getValidationAttributes(),
        )->validate();
    }
}
