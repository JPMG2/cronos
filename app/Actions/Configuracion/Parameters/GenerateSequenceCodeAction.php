<?php

declare(strict_types=1);

namespace App\Actions\Configuracion\Parameters;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

final class GenerateSequenceCodeAction
{
    public function handle(string $entity): string
    {
        return DB::transaction(function () use ($entity): string {
            /** @var Sequence $sequence */
            $sequence = Sequence::query()
                ->where('entity', $entity)
                ->lockForUpdate()
                ->firstOrFail();

            $next = $sequence->current_value + $sequence->increment;

            $number = $sequence->padding > 0
                ? mb_str_pad((string) $next, $sequence->padding, '0', STR_PAD_LEFT)
                : (string) $next;

            $code = $sequence->prefix
                ? $sequence->prefix . $sequence->separator . $number
                : $number;

            $sequence->update([
                'current_value' => $next,
                'is_used' => true,
            ]);

            return $code;
        });
    }
}
