<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class PersonSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Person::factory(20)->create();
    }
}
