<?php

declare(strict_types=1);

use App\Models\MaritalStatus;

use function Pest\Livewire\livewire;

describe('MaritalStatus', function () {

    it('renders the component', function () {
        livewire('configuracion.tablas.marital-status')
            ->assertOk();
    });

    it('shows existing marital statuses in the list', function () {
        $status = MaritalStatus::factory()->create();

        livewire('configuracion.tablas.marital-status')
            ->assertSee($status->name);
    });

    it('creates a marital status successfully', function () {
        livewire('configuracion.tablas.marital-status')
            ->set('form.name', 'Casado en comunión')
            ->call('create')
            ->assertHasNoErrors();

        expect(MaritalStatus::query()->where('name', 'Casado en comunión')->exists())->toBeTrue();
    });

    it('requires a name to create', function () {
        livewire('configuracion.tablas.marital-status')
            ->set('form.name', '')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.marital-status')
            ->set('form.name', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate name', function () {
        MaritalStatus::factory()->create(['name' => 'Soltero']);

        livewire('configuracion.tablas.marital-status')
            ->set('form.name', 'Soltero')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('fills the form when editing a marital status', function () {
        $status = MaritalStatus::factory()->create();

        livewire('configuracion.tablas.marital-status')
            ->call('startEdit', $status->id)
            ->assertSet('form.name', $status->name)
            ->assertSet('form.editingId', $status->id);
    });

    it('updates a marital status successfully', function () {
        $status = MaritalStatus::factory()->create(['name' => 'Nombre inicial']);

        livewire('configuracion.tablas.marital-status')
            ->call('startEdit', $status->id)
            ->set('form.name', 'Nombre modificado test')
            ->call('update')
            ->assertHasNoErrors();

        expect($status->fresh()->name)->toBe('Nombre modificado test');
    });

    it('allows keeping the same name when updating', function () {
        $status = MaritalStatus::factory()->create();

        livewire('configuracion.tablas.marital-status')
            ->call('startEdit', $status->id)
            ->set('form.name', $status->name)
            ->call('update')
            ->assertHasNoErrors();
    });

    it('toggles a marital status from active to inactive', function () {
        $status = MaritalStatus::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.marital-status')
            ->call('toggleActive', $status->id);

        expect($status->fresh()->is_active)->toBeFalse();
    });

    it('toggles a marital status from inactive to active', function () {
        $status = MaritalStatus::factory()->inactive()->create();

        livewire('configuracion.tablas.marital-status')
            ->call('toggleActive', $status->id);

        expect($status->fresh()->is_active)->toBeTrue();
    });

});
