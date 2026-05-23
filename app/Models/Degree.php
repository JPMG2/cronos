<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DegreeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'is_active'])]
final class Degree extends Model
{
    /** @use HasFactory<DegreeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(mb_strtolower(mb_trim($value))),
        );
    }

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(mb_strtolower(mb_trim($value))),
        );
    }
}
