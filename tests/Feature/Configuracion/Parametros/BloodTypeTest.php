<?php

declare(strict_types=1);

use App\Models\BloodType;

use function Pest\Livewire\livewire;

describe('BloodType', function () {

    it('renders the component', function () {
        livewire('configuracion.tablas.blood-type')
            ->assertOk();
    });

    it('shows existing blood types in the list', function () {
        $bloodType = BloodType::factory()->create();

        livewire('configuracion.tablas.blood-type')
            ->assertSee($bloodType->code);
    });

    it('creates a blood type successfully', function () {
        livewire('configuracion.tablas.blood-type')
            ->set('form.code', 'A+')
            ->set('form.name', 'A positivo')
            ->set('form.aboGroup', 'A')
            ->set('form.rhFactor', '+')
            ->set('form.canDonateTo', 'A+, AB+')
            ->set('form.canReceiveFrom', 'A+, A-, O+, O-')
            ->set('form.isUniversalDonor', false)
            ->set('form.isUniversalRecipient', false)
            ->call('create')
            ->assertHasNoErrors();

        expect(BloodType::query()->where('code', 'A+')->exists())->toBeTrue();
    });

    it('requires a code to create', function () {
        livewire('configuracion.tablas.blood-type')
            ->set('form.code', '')
            ->set('form.name', 'A positivo')
            ->set('form.aboGroup', 'A')
            ->set('form.rhFactor', '+')
            ->set('form.canDonateTo', 'A+')
            ->set('form.canReceiveFrom', 'O-')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('requires abo group to create', function () {
        livewire('configuracion.tablas.blood-type')
            ->set('form.code', 'B+')
            ->set('form.name', 'B positivo')
            ->set('form.aboGroup', '')
            ->set('form.rhFactor', '+')
            ->set('form.canDonateTo', 'B+')
            ->set('form.canReceiveFrom', 'O-')
            ->call('create')
            ->assertHasErrors('abo_group');
    });

    it('requires rh factor to create', function () {
        livewire('configuracion.tablas.blood-type')
            ->set('form.code', 'O+')
            ->set('form.name', 'O positivo')
            ->set('form.aboGroup', 'O')
            ->set('form.rhFactor', '')
            ->set('form.canDonateTo', 'O+')
            ->set('form.canReceiveFrom', 'O-')
            ->call('create')
            ->assertHasErrors('rh_factor');
    });

    it('rejects a duplicate code', function () {
        BloodType::factory()->create();

        $existing = BloodType::query()->first();

        livewire('configuracion.tablas.blood-type')
            ->set('form.code', $existing->code)
            ->set('form.name', 'Duplicado test')
            ->set('form.aboGroup', $existing->abo_group)
            ->set('form.rhFactor', '+')
            ->set('form.canDonateTo', 'A+')
            ->set('form.canReceiveFrom', 'O-')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('fills the form when editing a blood type', function () {
        $bloodType = BloodType::factory()->create();

        livewire('configuracion.tablas.blood-type')
            ->call('startEdit', $bloodType->id)
            ->assertSet('form.code', $bloodType->code)
            ->assertSet('form.editingId', $bloodType->id);
    });

    it('updates a blood type successfully', function () {
        $bloodType = BloodType::factory()->create();

        livewire('configuracion.tablas.blood-type')
            ->call('startEdit', $bloodType->id)
            ->set('form.name', 'Nombre actualizado')
            ->call('update')
            ->assertHasNoErrors();

        expect($bloodType->fresh()->name)->toBe('Nombre actualizado');
    });

    it('updates a blood type keeping the same code', function () {
        $bloodType = BloodType::factory()->create();

        livewire('configuracion.tablas.blood-type')
            ->call('startEdit', $bloodType->id)
            ->set('form.code', $bloodType->code)
            ->call('update')
            ->assertHasNoErrors();
    });

});
