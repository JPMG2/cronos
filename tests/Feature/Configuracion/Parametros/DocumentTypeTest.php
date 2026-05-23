<?php

declare(strict_types=1);

use App\Models\DocumentType;

use function Pest\Livewire\livewire;

describe('DocumentType', function () {

    // ── Renderizado ───────────────────────────────────────────────────────────

    it('renders the component', function () {
        livewire('configuracion.tablas.document-type')
            ->assertOk();
    });

    it('shows existing document types in the list', function () {
        $type = DocumentType::factory()->create();

        livewire('configuracion.tablas.document-type')
            ->assertSee($type->name);
    });

    // ── Crear ─────────────────────────────────────────────────────────────────

    it('creates a document type successfully', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Documento Nacional de Identidad')
            ->set('form.code', 'DNI')
            ->call('create')
            ->assertHasNoErrors();

        expect(DocumentType::query()->where('code', 'DNI')->exists())->toBeTrue();
    });

    it('creates a document type with optional short name', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Libreta de Enrolamiento')
            ->set('form.code', 'LE')
            ->set('form.shortName', 'L.E.')
            ->call('create')
            ->assertHasNoErrors();
    });

    // ── Validación al crear ───────────────────────────────────────────────────

    it('requires a name to create', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', '')
            ->set('form.code', 'XX')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires a code to create', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Pasaporte internacional')
            ->set('form.code', '')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'AB')
            ->set('form.code', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires code to have at least 2 characters', function () {
        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Cédula de identidad')
            ->set('form.code', 'X')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('rejects a duplicate name', function () {
        DocumentType::factory()->create(['name' => 'Pasaporte', 'code' => 'PA']);

        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Pasaporte')
            ->set('form.code', 'PS')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate code', function () {
        DocumentType::factory()->create(['name' => 'Documento nacional', 'code' => 'DN']);

        livewire('configuracion.tablas.document-type')
            ->set('form.name', 'Documento regional')
            ->set('form.code', 'DN')
            ->call('create')
            ->assertHasErrors('code');
    });

    // ── Editar ────────────────────────────────────────────────────────────────

    it('fills the form when editing a document type', function () {
        $type = DocumentType::factory()->create();

        livewire('configuracion.tablas.document-type')
            ->call('startEdit', $type->id)
            ->assertSet('form.name', $type->name)
            ->assertSet('form.code', $type->code)
            ->assertSet('form.editingId', $type->id);
    });

    it('updates a document type successfully', function () {
        $type = DocumentType::factory()->create(['name' => 'Libreta civica', 'code' => 'LC']);

        livewire('configuracion.tablas.document-type')
            ->call('startEdit', $type->id)
            ->set('form.name', 'Libreta cívica renovada')
            ->call('update')
            ->assertHasNoErrors();

        expect($type->fresh()->name)->toBe('Libreta cívica renovada');
    });

    it('allows keeping the same name and code when updating', function () {
        $type = DocumentType::factory()->create();

        livewire('configuracion.tablas.document-type')
            ->call('startEdit', $type->id)
            ->set('form.name', $type->name)
            ->set('form.code', $type->code)
            ->call('update')
            ->assertHasNoErrors();
    });

    // ── Activar / Desactivar ──────────────────────────────────────────────────

    it('toggles a document type from active to inactive', function () {
        $type = DocumentType::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.document-type')
            ->call('toggleActive', $type->id);

        expect($type->fresh()->is_active)->toBeFalse();
    });

    it('toggles a document type from inactive to active', function () {
        $type = DocumentType::factory()->inactive()->create();

        livewire('configuracion.tablas.document-type')
            ->call('toggleActive', $type->id);

        expect($type->fresh()->is_active)->toBeTrue();
    });

});
