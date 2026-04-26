<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\WorldSettingsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class WorldSettings extends Model
{
    /** @use HasFactory<WorldSettingsFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'id',
        'country_id',
        'province_id',
        'region_id',
        'currency_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function defaultCountry(): ?int
    {
        return self::query()->value('country_id');
    }

    public static function defaultProvince(): ?int
    {
        return self::query()->value('province_id');
    }

    public static function defaultRegion(): ?int
    {
        return self::query()->value('region_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['country_id', 'province_id', 'region_id', 'currency_id'])
            ->logOnlyDirty()
            ->useLogName('world-settings')
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'province_id' => 'integer',
            'region_id' => 'integer',
            'currency_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}
