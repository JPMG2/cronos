<?php

declare(strict_types=1);

namespace App\Models;

use Attribute;
use Database\Factories\InsuranceCompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class InsuranceCompany extends Model
{
    /** @use HasFactory<InsuranceCompanyFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'coverage_type_id',
        'current_status_id',
        'region_id',
        'name',
        'code',
        'cuit',
        'phone',
        'email',
        'website',
        'address',
        'logo',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'cuit', 'phone', 'email', 'current_status_id', 'region_id'])
            ->logOnlyDirty()
            ->useLogName('insurance_company')
            ->dontLogEmptyChanges();
    }

    public function coverageType(): BelongsTo
    {
        return $this->belongsTo(CoverageType::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(InsurancePlan::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CurrentStatus::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
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

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? mb_strtolower(mb_trim($value)) : null,
        );
    }

    protected function casts(): array
    {
        return [
            'coverage_type_id' => 'integer',
            'current_status_id' => 'integer',
            'region_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}
