<?php

declare(strict_types=1);

use App\Models\TaxCondition;

use function Pest\Livewire\livewire;

describe('TaxCondition', function () {

    it('renders the component', function () {
        livewire('configuracion.tablas.tax-condition')
            ->assertOk();
    });

    it('shows existing tax conditions in the list', function () {
        $tax = TaxCondition::factory()->create();

        livewire('configuracion.tablas.tax-condition')
            ->assertSee($tax->name);
    });

    it('creates a tax condition successfully', function () {
        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'Responsable inscripto')
            ->set('form.code', 'RI')
            ->call('create')
            ->assertHasNoErrors();

        expect(TaxCondition::query()->where('code', 'RI')->exists())->toBeTrue();
    });

    it('requires a name to create', function () {
        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', '')
            ->set('form.code', 'XX')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires a code to create', function () {
        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'Consumidor final')
            ->set('form.code', '')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('requires name to have at least 3 characters', function () {
        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'AB')
            ->set('form.code', 'AB')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('requires code to have at least 2 characters', function () {
        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'Monotributista social')
            ->set('form.code', 'X')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('rejects a duplicate name', function () {
        TaxCondition::factory()->create(['name' => 'Exento', 'code' => 'EX']);

        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'Exento')
            ->set('form.code', 'EY')
            ->call('create')
            ->assertHasErrors('name');
    });

    it('rejects a duplicate code', function () {
        TaxCondition::factory()->create(['name' => 'Monotributista', 'code' => 'MT']);

        livewire('configuracion.tablas.tax-condition')
            ->set('form.name', 'Monotributista plus')
            ->set('form.code', 'MT')
            ->call('create')
            ->assertHasErrors('code');
    });

    it('fills the form when editing a tax condition', function () {
        $tax = TaxCondition::factory()->create();

        livewire('configuracion.tablas.tax-condition')
            ->call('startEditTaxCondition', $tax->id)
            ->assertSet('form.name', $tax->name)
            ->assertSet('form.code', $tax->code)
            ->assertSet('form.taxId', $tax->id);
    });

    it('updates a tax condition successfully', function () {
        $tax = TaxCondition::factory()->create(['name' => 'Nombre previo', 'code' => 'NP']);

        livewire('configuracion.tablas.tax-condition')
            ->call('startEditTaxCondition', $tax->id)
            ->set('form.name', 'Nombre actualizado test')
            ->call('update')
            ->assertHasNoErrors();

        expect($tax->fresh()->name)->toBe('Nombre actualizado test');
    });

    it('allows keeping the same name and code when updating', function () {
        $tax = TaxCondition::factory()->create();

        livewire('configuracion.tablas.tax-condition')
            ->call('startEditTaxCondition', $tax->id)
            ->set('form.name', $tax->name)
            ->set('form.code', $tax->code)
            ->call('update')
            ->assertHasNoErrors();
    });

    it('toggles a tax condition from active to inactive', function () {
        $tax = TaxCondition::factory()->create(['is_active' => true]);

        livewire('configuracion.tablas.tax-condition')
            ->call('toggleStatusTax', $tax->id);

        expect($tax->fresh()->is_active)->toBeFalse();
    });

    it('toggles a tax condition from inactive to active', function () {
        $tax = TaxCondition::factory()->inactive()->create();

        livewire('configuracion.tablas.tax-condition')
            ->call('toggleStatusTax', $tax->id);

        expect($tax->fresh()->is_active)->toBeTrue();
    });

    it('toggles discriminate tax flag', function () {
        $tax = TaxCondition::factory()->create(['discriminate_tax' => false]);

        livewire('configuracion.tablas.tax-condition')
            ->call('toggleDiscriminateTax', $tax->id);

        expect($tax->fresh()->discriminate_tax)->toBeTrue();
    });

});
