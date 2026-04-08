<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CredentialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'is_active'])]
final class Credential extends Model
{
    /** @use HasFactory<CredentialFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
