<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InsuranceCoverageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class InsuranceCoverage extends Model
{
    /** @use HasFactory<InsuranceCoverageFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'insurance_plan_id',
        'category',
        'coverage_percentage',
        'copay_amount',
        'max_amount',
        'requires_authorization',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['category', 'coverage_percentage', 'copay_amount', 'max_amount', 'requires_authorization', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('insurance_coverage')
            ->dontLogEmptyChanges();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InsurancePlan::class, 'insurance_plan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'insurance_plan_id' => 'integer',
            'coverage_percentage' => 'decimal:2',
            'copay_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'requires_authorization' => 'boolean',
            'is_active' => 'boolean',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }
}
