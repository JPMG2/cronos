<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['country_id', 'name', 'is_active'])]
final class Province extends Model
{
    /** @use HasFactory<ProvinceFactory> */
    use HasFactory, SoftDeletes;

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function defaultFirst(Builder $query): void
    {
        $query->orderByDesc('is_default')->orderByDesc('name');
    }
}
