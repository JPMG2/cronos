<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'phone_code', 'is_active'])]
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
            'is_active' => 'boolean',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucwords(mb_strtolower(mb_trim($value))),
        );
    }

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(mb_strtolower(mb_trim($value))),
        );
    }
}
