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

    public function alertWrapperClass(): string
    {
        return match ($this) {
            self::Warning => 'border border-amber-200/70 bg-amber-50/80 dark:border-amber-500/25 dark:bg-amber-500/10',
            self::Info => 'border border-indigo-200/70 bg-indigo-50/80 dark:border-indigo-500/25 dark:bg-indigo-500/10',
            self::Success => 'border border-emerald-200/70 bg-emerald-50/80 dark:border-emerald-500/25 dark:bg-emerald-500/10',
            self::Error => 'border border-rose-200/70 bg-rose-50/80 dark:border-rose-500/25 dark:bg-rose-500/10',
        };
    }

    public function alertAccentClass(): string
    {
        return match ($this) {
            self::Warning => 'bg-amber-500 dark:bg-amber-400',
            self::Info => 'bg-indigo-500 dark:bg-sky-400',
            self::Success => 'bg-emerald-500 dark:bg-emerald-400',
            self::Error => 'bg-rose-500 dark:bg-rose-400',
        };
    }

    public function alertLabelClass(): string
    {
        return match ($this) {
            self::Warning => 'text-amber-700 dark:text-amber-300',
            self::Info => 'text-indigo-700 dark:text-indigo-300',
            self::Success => 'text-emerald-700 dark:text-emerald-300',
            self::Error => 'text-rose-700 dark:text-rose-300',
        };
    }
}
