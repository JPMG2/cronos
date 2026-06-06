<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CoverageTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description', 'is_active', 'requires_insurance_data'])]
final class CoverageType extends Model
{
    /** @use HasFactory<CoverageTypeFactory> */
    use HasFactory;

    public function insuranceCompanies(): HasMany
    {
        return $this->hasMany(InsuranceCompany::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_insurance_data' => 'boolean',
        ];
    }

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper(mb_strtolower(mb_trim($value))),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => mb_strtoupper($value),
            set: fn (string $value) => mb_strtolower(mb_trim($value)),
        );
    }
}
