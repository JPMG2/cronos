<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Sequence extends Model
{
    /** @use HasFactory<\Database\Factories\SequenceFactory> */
    use HasFactory;
}
