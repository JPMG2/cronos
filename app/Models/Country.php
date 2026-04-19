<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'phone_code', 'is_default', 'is_active'])]
final class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
