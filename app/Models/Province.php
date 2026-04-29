<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['country_id', 'name', 'is_active'])]
final class Province extends Model
{
    /** @use HasFactory<ProvinceFactory> */
    use HasFactory, SoftDeletes;

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
        ];
    }

    #[Scope]
    protected function defaultFirst(Builder $query, $modelId): void
    {
        $query->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$modelId]);
    }
}
