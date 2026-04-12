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

    private const PARENT_KEY = 'parent_id';

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

        if (! $item instanceof self) {
            return [];
        }

        $trail = [];

        for ($node = $item; $node instanceof self; $node = $node->parentRecursive) {
            array_unshift($trail, [
                'title' => $node->title,
                'route' => $node->route,
            ]);
        }

        return $trail;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, self::PARENT_KEY);
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->belongsTo(self::class, self::PARENT_KEY)->with('parentRecursive');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, self::PARENT_KEY)->orderBy('order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->hasMany(self::class, self::PARENT_KEY)
            ->orderBy('order')
            ->with('childrenRecursive');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function rootMenus(Builder $query): void
    {
        $query->whereNull(self::PARENT_KEY)->orderBy('order');
    }

    #[Scope]
    protected function forRoute(Builder $query, string $routeName): void
    {
        $query->where('route', $routeName);
    }
}
