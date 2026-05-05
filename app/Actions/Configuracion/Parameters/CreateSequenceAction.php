<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\Parameters;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class CreateSequenceAction
{
    public function handle(array $data): Sequence
    {
        return DB::transaction(function () use ($data): Sequence {
            $existing = Sequence::query()
                ->where('entity', $data['entity'])
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                throw new RuntimeException('Ya existe una secuencia para la entidad indicada.');
            }

            return Sequence::query()->create([
                'entity' => $data['entity'],
                'prefix' => $data['prefix'] ?: null,
                'padding' => $data['padding'] ?? null,
                'separator' => $data['separator'] ?: null,
                'current_value' => $data['current_value'] ?? 0,
                'increment' => $data['increment'] ?? 1,
            ]);
        });
    }
}
