<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\WorldSettingsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['country_id', 'province_id', 'region_id'])]
final class WorldSettings extends Model
{
    /** @use HasFactory<WorldSettingsFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'province_id' => 'integer',
            'region_id' => 'integer',
        ];
    }
}
