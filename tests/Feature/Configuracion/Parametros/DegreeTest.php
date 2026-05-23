<?php

declare(strict_types=1);

use App\Models\Degree;

use function Pest\Livewire\livewire;

describe('Degree', function () {

    // ── Renderizado ───────────────────────────────────────────────────────────

    it('renders the component', function () {
        livewire('configuracion.tablas.degree')
            ->assertOk();
    });

    it('shows existing degrees in the list', function () {
        $degree = Degree::factory()->create(['name' => 'Licenciado en enfermería']);

        livewire('configuracion.tablas.degree')
            ->assertSee($degree->name);
    });

    // ── Crear ─────────────────────────────────────────────────────────────────

    it('creates a degree successfully', function () {
        livewire('configuracion.tablas.degree')
            ->set('form.name', 'Doctor en Medicina')
            ->call('create')
            ->assertHasNoErrors();

        expect(Degree::query()->where('name', 'Doctor en medicina')->exists())->toBeTrue();
    });

    it('creates a degree with optional code', function () {
        livewire('configuracion.tablas.degree')
            ->set('form.name', 'Licenciado en Kinesiología')
            ->set('form.code', 'LK')
            ->call('create')
            ->assertHasNoErrors();

        expect(Degree::query()->where('code', 'LK')->exists())->toBeTrue();
    });

    // ── Validación al crear ───────────────────────────────────────────────────

    it('requires a name to create', function () {
        livewire('configuracion.tablas.degree')
            ->set('form.name', '')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.degree')
            ->set('form.name', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate name', function () {
        Degree::factory()->create(['name' => 'Técnico superior']);

        livewire('configuracion.tablas.degree')
            ->set('form.name', 'Técnico superior')
            ->call('create')
            ->assertHasErrors('name');
    });

    // ── Editar ────────────────────────────────────────────────────────────────

    it('fills the form when editing a degree', function () {
        $degree = Degree::factory()->withCode()->create([
            'name' => 'Profesora de educación física',
        ]);

        livewire('configuracion.tablas.degree')
            ->call('startEdit', $degree->id)
            ->assertSet('form.name', $degree->name)
            ->assertSet('form.code', $degree->code)
            ->assertSet('form.editingId', $degree->id);
    });

    it('updates a degree successfully', function () {
        $degree = Degree::factory()->create(['name' => 'Nombre original']);

        livewire('configuracion.tablas.degree')
            ->call('startEdit', $degree->id)
            ->set('form.name', 'Nombre Actualizado')
            ->call('update')
            ->assertHasNoErrors();

        expect($degree->fresh()->name)->toBe('Nombre actualizado');
    });

    it('allows keeping the same name when updating', function () {
        $degree = Degree::factory()->create(['name' => 'Odontólogo general']);

        livewire('configuracion.tablas.degree')
            ->call('startEdit', $degree->id)
            ->set('form.name', 'Odontólogo general')
            ->call('update')
            ->assertHasNoErrors();
    });

    // ── Cancelar edición ──────────────────────────────────────────────────────

    it('resets the form on cancel', function () {
        $degree = Degree::factory()->create();

        livewire('configuracion.tablas.degree')
            ->call('startEdit', $degree->id)
            ->call('cancelEdit')
            ->assertSet('form.name', '')
            ->assertSet('form.editingId', null);
    });

    // ── Activar / Desactivar ──────────────────────────────────────────────────

    it('toggles a degree from active to inactive', function () {
        $degree = Degree::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.degree')
            ->call('toggleActive', $degree->id);

        expect($degree->fresh()->is_active)->toBeFalse();
    });

    it('toggles a degree from inactive to active', function () {
        $degree = Degree::factory()->inactive()->create();

        livewire('configuracion.tablas.degree')
            ->call('toggleActive', $degree->id);

        expect($degree->fresh()->is_active)->toBeTrue();
    });

});
