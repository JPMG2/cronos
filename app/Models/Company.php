<?php

declare(strict_types=1);

namespace App\Models;

use Attribute;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'current_status_id',
        'tax_condition_id',
        'region_id',
        'name',
        'fiscal_identifier',
        'phone',
        'email',
        'address',
        'postal_code',
        'website',
        'logo',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'fiscal_identifier', 'phone', 'email', 'address', 'postal_code', 'website', 'current_status_id', 'tax_condition_id', 'region_id'])
            ->logOnlyDirty()
            ->useLogName('company')
            ->dontLogEmptyChanges();
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    protected function casts(): array
    {
        return [
            'current_status_id' => 'integer',
            'tax_condition_id' => 'integer',
            'region_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => mb_strtolower(mb_trim($value)),
        );
    }
}
