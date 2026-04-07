<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Credential;
use Illuminate\Database\Seeder;

final class CredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $credentials = [
            [
                'name' => 'matrícula nacional',
                'code' => 'MN',
            ],
            [
                'name' => 'matrícula provincial',
                'code' => 'MP',
            ],
        ];
        foreach ($credentials as $credential) {
            Credential::query()->create($credential);
        }
    }
}
