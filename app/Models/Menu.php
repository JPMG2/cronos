<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Construye el trail completo de breadcrumbs para una ruta nombrada.
     *
     * @return array<int, array{title: string, route: string|null}>
     */
    public static function breadcrumbTrail(string $routeName): array
    {
        if ($routeName === '') {
            return [];
        }

        $item = self::query()
            ->active()
            ->forRoute($routeName)
            ->with('parentRecursive')
            ->first();

        if (! $item) {
            return [];
        }

        $trail = [];
        $node = $item;

        do {
            array_unshift($trail, [
                'title' => $node->title,
                'route' => $node->route,
            ]);
            $node = $node->parentRecursive;
        } while ($node instanceof self);

        return $trail;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->with('parentRecursive');
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

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function rootMenus(Builder $query): void
    {
        $query->whereNull('parent_id')->orderBy('order');
    }

    #[Scope]
    protected function forRoute(Builder $query, string $routeName): void
    {
        $query->where('route', $routeName);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }
}
