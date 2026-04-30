<?php

declare(strict_types=1);

namespace App\Enums\Styles;

enum InformacionColors: string
{
    case Warning = 'warning';
    case Info = 'info';
    case Success = 'success';

    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Warning => 'Advertencia',
            self::Info => 'Información',
            self::Success => 'Éxito',
            self::Error => 'Error',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Warning => 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
            self::Info => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400',
            self::Success => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400',
            self::Error => 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Warning => 'exclamation-triangle',
            self::Info => 'information-circle',
            self::Success => 'check-circle',
            self::Error => 'x-circle',
        };
    }
}
