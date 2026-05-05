<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Department extends Model
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'current_status_id',
        'name',
        'code',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CurrentStatus::class, 'current_status_id');
    }

    protected function casts(): array
    {
        return [
            'current_status_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    #[Scope]
    protected function statusIds(Builder $query, array $status = []): void
    {
        $query->whereIn('current_status_id', $status);
    }

    #[Scope]
    protected function listDepartment(Builder $query): void
    {
        $query->with('currentStatus');
    }
}
