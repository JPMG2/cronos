<?php

declare(strict_types=1);

use App\Models\Gender;

use function Pest\Livewire\livewire;

describe('Gender', function () {

    it('renders the component', function () {
        livewire('configuracion.tablas.gender')
            ->assertOk();
    });

    it('shows existing genders in the list', function () {
        $gender = Gender::factory()->create();

        livewire('configuracion.tablas.gender')
            ->assertSee($gender->name);
    });

    it('creates a gender successfully', function () {
        livewire('configuracion.tablas.gender')
            ->set('form.name', 'No binario')
            ->call('create')
            ->assertHasNoErrors();

        expect(Gender::query()->where('name', 'No binario')->exists())->toBeTrue();
    });

    it('requires a name to create', function () {
        livewire('configuracion.tablas.gender')
            ->set('form.name', '')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.gender')
            ->set('form.name', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate name', function () {
        Gender::factory()->create(['name' => 'Masculino']);

        livewire('configuracion.tablas.gender')
            ->set('form.name', 'Masculino')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('fills the form when editing a gender', function () {
        $gender = Gender::factory()->create();

        livewire('configuracion.tablas.gender')
            ->call('startEdit', $gender->id)
            ->assertSet('form.name', $gender->name)
            ->assertSet('form.editingId', $gender->id);
    });

    it('updates a gender successfully', function () {
        $gender = Gender::factory()->create(['name' => 'Nombre original']);

        livewire('configuracion.tablas.gender')
            ->call('startEdit', $gender->id)
            ->set('form.name', 'Nombre actualizado test')
            ->call('update')
            ->assertHasNoErrors();

        expect($gender->fresh()->name)->toBe('Nombre actualizado test');
    });

    it('allows keeping the same name when updating', function () {
        $gender = Gender::factory()->create();

        livewire('configuracion.tablas.gender')
            ->call('startEdit', $gender->id)
            ->set('form.name', $gender->name)
            ->call('update')
            ->assertHasNoErrors();
    });

    it('toggles a gender from active to inactive', function () {
        $gender = Gender::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.gender')
            ->call('toggleActive', $gender->id);

        expect($gender->fresh()->is_active)->toBeFalse();
    });

    it('toggles a gender from inactive to active', function () {
        $gender = Gender::factory()->inactive()->create();

        livewire('configuracion.tablas.gender')
            ->call('toggleActive', $gender->id);

        expect($gender->fresh()->is_active)->toBeTrue();
    });

    it('resets the form on cancel', function () {
        $gender = Gender::factory()->create();

        livewire('configuracion.tablas.gender')
            ->call('startEdit', $gender->id)
            ->call('cancelEdit')
            ->assertSet('form.name', '')
            ->assertSet('form.editingId', null);
    });

});
