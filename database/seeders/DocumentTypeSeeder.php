<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

final class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $document = [
            [
                'code' => 'DNI',
                'name' => 'Documento Nacional de Identidad',
                'short_name' => 'DNI',
            ],
            [
                'code' => 'PASAPORTE',
                'name' => 'Pasaporte',
                'short_name' => 'Pasaporte',
            ],
            [
                'code' => 'CI',
                'name' => 'Cédula',
                'short_name' => 'CI',
            ],
        ];

        foreach ($document as $doc) {
            DocumentType::query()->create($doc);
        }
    }
}
