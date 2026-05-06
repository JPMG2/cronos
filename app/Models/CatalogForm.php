<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CatalogFormFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'title', 'description', 'component', 'icon', 'order', 'permission_key', 'is_active'])]
final class CatalogForm extends Model
{
    /** @use HasFactory<CatalogFormFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
