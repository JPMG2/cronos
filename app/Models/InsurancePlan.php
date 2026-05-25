<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InsurancePlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class InsurancePlan extends Model
{
    /** @use HasFactory<InsurancePlanFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'insurance_company_id',
        'current_status_id',
        'name',
        'code',
        'description',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'insurance_company_id', 'current_status_id'])
            ->logOnlyDirty()
            ->useLogName('insurance_plan')
            ->dontLogEmptyChanges();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function coverages(): HasMany
    {
        return $this->hasMany(InsuranceCoverage::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CurrentStatus::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected function casts(): array
    {
        return [
            'insurance_company_id' => 'integer',
            'current_status_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}
