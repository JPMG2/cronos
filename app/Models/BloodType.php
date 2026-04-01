<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BloodTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class BloodType extends Model
{
    /** @use HasFactory<BloodTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'abo_group',
        'rh_factor',
        'can_donate_to',
        'can_receive_from',
        'is_universal_donor',
        'is_universal_recipient',
    ];

    protected function casts(): array
    {
        return [
            'rh_factor' => 'array',
            'can_donate_to' => 'array',
            'can_receive_from' => 'array',
            'is_universal_donor' => 'boolean',
            'is_universal_recipient' => 'boolean',
        ];
    }
}
