<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'icon',
        'route',
        'order',
        'is_active',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('childrenRecursive');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootMenus($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }
}
