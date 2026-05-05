<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\Parameters;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class UpdateSequenceAction
{
    public function handle(array $data): Sequence
    {
        return DB::transaction(function () use ($data): Sequence {
            /** @var Sequence $sequence */
            $sequence = Sequence::query()
                ->lockForUpdate()
                ->findOrFail($data['id']);

            if ($sequence->is_used) {
                if (mb_trim($data['entity']) !== $sequence->entity) {
                    throw new RuntimeException('La entidad no puede modificarse una vez que la secuencia ha sido utilizada.');
                }

                if ((mb_trim($data['prefix']) ?: null) !== $sequence->prefix) {
                    throw new RuntimeException('El código no puede modificarse una vez que la secuencia ha sido utilizada.');
                }

                if ((int) $data['current_value'] <= $sequence->current_value) {
                    throw new RuntimeException(
                        'El valor actual debe ser estrictamente mayor al registrado (' . $sequence->current_value . ').',
                    );
                }
            }

            $sequence->update([
                'entity' => mb_trim($data['entity']),
                'prefix' => mb_trim($data['prefix']) ?: null,
                'padding' => $data['padding'] ?? null,
                'separator' => mb_trim($data['separator']) ?: null,
                'current_value' => (int) $data['current_value'],
                'increment' => (int) $data['increment'],
            ]);

            return $sequence;
        });
    }
}
