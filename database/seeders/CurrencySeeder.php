<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

final class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'ARS',
                'name' => 'Peso Argentino',
                'symbol' => '$',
                'decimal_places' => 2,

            ],
            [
                'code' => 'USD',
                'name' => 'Dólar Estadounidense',
                'symbol' => '$',
                'decimal_places' => 2,

            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'decimal_places' => 2,
            ],
        ];
        foreach ($currencies as $currency) {
            Currency::query()->create($currency);
        }
    }
}
