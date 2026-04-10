<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

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
}
