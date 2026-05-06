<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class Branch extends Model
{
    /** @use HasFactory<BranchFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'current_status_id',
        'region_id',
        'name',
        'code',
        'phone',
        'email',
        'website',
        'address',
        'postal_code',
        'is_default',
        'logo',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'phone', 'email', 'address', 'postal_code', 'website', 'is_default', 'current_status_id', 'region_id'])
            ->logOnlyDirty()
            ->useLogName('branch')
            ->dontLogEmptyChanges();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CurrentStatus::class, 'current_status_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    protected function casts(): array
    {
        return [
            'company_id' => 'integer',
            'current_status_id' => 'integer',
            'region_id' => 'integer',
            'is_default' => 'boolean',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    #[Scope]
    protected function listBranches(Builder $query): void
    {
        $query->with('currentStatus', 'region')
            ->orderBy('name');
    }

    #[Scope]
    protected function statusIds(Builder $query, array $status = []): void
    {
        $query->whereIn('current_status_id', $status);
    }
}
