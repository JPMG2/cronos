<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SequenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Sequence extends Model
{
    /** @use HasFactory<SequenceFactory> */
    use HasFactory;

    protected $fillable = [
        'entity',
        'prefix',
        'current_value',
        'increment',
        'padding',
        'separator',
    ];

    protected function casts(): array
    {
        return [
            'current_value' => 'integer',
            'increment' => 'integer',
            'padding' => 'integer',
        ];
    }
}
