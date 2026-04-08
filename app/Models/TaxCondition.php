<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'discriminate_tax', 'is_active'])]
final class TaxCondition extends Model
{
    /** @use HasFactory<\Database\Factories\TaxConditionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'discriminate_tax' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
