<?php

declare(strict_types=1);

use App\Models\Occupation;

use function Pest\Livewire\livewire;

describe('Occupation', function () {

    it('renders the component', function () {
        livewire('configuracion.tablas.occupation')
            ->assertOk();
    });

    it('shows existing occupations in the list', function () {
        $occupation = Occupation::factory()->create();

        livewire('configuracion.tablas.occupation')
            ->assertSee($occupation->name);
    });

    it('creates an occupation successfully', function () {
        livewire('configuracion.tablas.occupation')
            ->set('form.name', 'Técnico en laboratorio')
            ->call('create')
            ->assertHasNoErrors();

        expect(Occupation::query()->where('name', 'Técnico en laboratorio')->exists())->toBeTrue();
    });

    it('requires a name to create', function () {
        livewire('configuracion.tablas.occupation')
            ->set('form.name', '')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.occupation')
            ->set('form.name', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate name', function () {
        Occupation::factory()->create(['name' => 'Enfermero general']);

        livewire('configuracion.tablas.occupation')
            ->set('form.name', 'Enfermero general')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('fills the form when editing an occupation', function () {
        $occupation = Occupation::factory()->create();

        livewire('configuracion.tablas.occupation')
            ->call('startEdit', $occupation->id)
            ->assertSet('form.name', $occupation->name)
            ->assertSet('form.editingId', $occupation->id);
    });

    it('updates an occupation successfully', function () {
        $occupation = Occupation::factory()->create(['name' => 'Ocupación anterior']);

        livewire('configuracion.tablas.occupation')
            ->call('startEdit', $occupation->id)
            ->set('form.name', 'Ocupación actualizada test')
            ->call('update')
            ->assertHasNoErrors();

        expect($occupation->fresh()->name)->toBe('Ocupación actualizada test');
    });

    it('allows keeping the same name when updating', function () {
        $occupation = Occupation::factory()->create();

        livewire('configuracion.tablas.occupation')
            ->call('startEdit', $occupation->id)
            ->set('form.name', $occupation->name)
            ->call('update')
            ->assertHasNoErrors();
    });

    it('toggles an occupation from active to inactive', function () {
        $occupation = Occupation::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.occupation')
            ->call('toggleActive', $occupation->id);

        expect($occupation->fresh()->is_active)->toBeFalse();
    });

    it('toggles an occupation from inactive to active', function () {
        $occupation = Occupation::factory()->inactive()->create();

        livewire('configuracion.tablas.occupation')
            ->call('toggleActive', $occupation->id);

        expect($occupation->fresh()->is_active)->toBeTrue();
    });

});
