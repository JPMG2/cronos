<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SequenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Sequence extends Model
{
    /** @use HasFactory<SequenceFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'entity',
        'prefix',
        'current_value',
        'increment',
        'padding',
        'separator',
        'is_used',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function isConfigured(): bool
    {
        return filled($this->prefix)
            && filled($this->separator)
            && $this->padding > 0;
    }

    protected function casts(): array
    {
        return [
            'current_value' => 'integer',
            'increment' => 'integer',
            'padding' => 'integer',
            'is_used' => 'boolean',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}
