<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Parametros;

use App\Livewire\Forms\BaseForm;
use App\Models\DocumentType;
use App\Rules\AttributeValidator;
use Livewire\Attributes\Locked;

final class DocumentTypeForm extends BaseForm
{
    public string $code = '';

    public string $name = '';

    public string $shortName = '';

    #[Locked]
    public ?int $editingId = null;

    public function storeDocumentType(): array
    {
        $data = $this->validateServiceData();
        $documentType = DocumentType::query()->create($data);

        return $this->notificationService()->sendNotificacion($documentType, 'create');

    }

    public function updateDocumentType(): array
    {

        $data = $this->validateServiceData($this->editingId);

        $documentType = $this->findDocumentType($this->editingId);
        $documentType->update($data);

        return $this->notificationService()->sendNotificacion($documentType, 'update');
    }

    public function findDocumentType(int $id): DocumentType
    {
        return DocumentType::query()->findOrFail($id);
    }

    public function updateDocumentTypeActive(int $id): array
    {
        $data = $this->findDocumentType($id);
        $wasActive = $data->is_active;
        $data->update(['is_active' => ! $wasActive]);

        return $this->notificationService()->sendNotificacion($data, 'update');
    }

    public function fillDocumentType(int $id): void
    {
        $documentType = $this->findDocumentType($id);
        $this->code = $documentType->code;
        $this->name = $documentType->name;
        $this->shortName = $documentType->short_name;
        $this->editingId = $documentType->id;
    }

    protected function transformServiceData(): array
    {
        return [
            'code' => mb_strtoupper(mb_strtolower(mb_trim($this->code))),
            'name' => ucfirst(mb_strtolower(mb_trim($this->name))),
            'short_name' => mb_strtoupper(mb_strtolower(mb_trim($this->shortName))),
        ];
    }

    protected function getValidationRules(?int $excludeId = null): array
    {
        return [
            'code' => AttributeValidator::uniqueIdNameLength('2', 'document_types', 'code', $excludeId),
            'name' => AttributeValidator::uniqueIdNameLength('3', 'document_types', 'name', $excludeId),
            'short_name' => AttributeValidator::stringValid(false, '2'),
        ];
    }

    protected function getValidationAttributes(): array
    {
        return [
            'name' => config('nicename.name'),
            'code' => config('nicename.code'),
            'short_name' => config('nicename.short_name'),
        ];
    }
}
