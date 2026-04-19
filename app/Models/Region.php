<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['province_id', 'name', 'is_active'])]
final class Region extends Model
{
    /** @use HasFactory<\Database\Factories\RegionFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'province_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
