<?php

declare(strict_types=1);

namespace App\Enums\Styles;

enum StatusColors: string
{
    case Activo = 'activo';
    case Bloqueado = 'bloqueado';
    case Cancelado = 'cancelado';
    case Archivado = 'archivado';
    case Eliminado = 'eliminado';
    case Inactivo = 'inactivo';
    case Pendiente = 'pendiente';
    case Rechazado = 'rechazado';
    case Suspendido = 'suspendido';
    case Pausado = 'pausado';
    case EnProceso = 'en proceso';
    case Finalizado = 'finalizado';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Bloqueado => 'Bloqueado',
            self::Cancelado => 'Cancelado',
            self::Archivado => 'Archivado',
            self::Eliminado => 'Eliminado',
            self::Inactivo => 'Inactivo',
            self::Pendiente => 'Pendiente',
            self::Rechazado => 'Rechazado',
            self::Suspendido => 'Suspendido',
            self::Pausado => 'Pausado',
            self::EnProceso => 'En proceso',
            self::Finalizado => 'Finalizado',
        };
    }

    /**
     * Clases Tailwind para el badge/pill del estado.
     * Usar junto a las clases base: inline-flex items-center gap-1.5 rounded-lg px-2.5 py-0.5 text-xs font-semibold
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Activo => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            self::Finalizado => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
            self::EnProceso => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-400',
            self::Pendiente => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            self::Pausado => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300',
            self::Inactivo => 'bg-slate-100 text-slate-600 dark:bg-gray-800 dark:text-gray-400',
            self::Archivado => 'bg-stone-100 text-stone-600 dark:bg-gray-800 dark:text-gray-400',
            self::Bloqueado => 'bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
            self::Suspendido => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
            self::Cancelado => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-500/10 dark:text-fuchsia-400',
            self::Rechazado => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
            self::Eliminado => 'bg-zinc-100 text-zinc-600 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    /**
     * Clase del dot indicador (h-1.5 w-1.5 rounded-full).
     */
    public function dotClass(): string
    {
        return match ($this) {
            self::Activo => 'bg-emerald-500 dark:bg-emerald-400',
            self::Finalizado => 'bg-purple-500 dark:bg-purple-400',
            self::EnProceso => 'bg-sky-500 dark:bg-sky-400',
            self::Pendiente => 'bg-amber-500 dark:bg-amber-400',
            self::Pausado => 'bg-indigo-500 dark:bg-indigo-400',
            self::Inactivo => 'bg-slate-400 dark:bg-gray-500',
            self::Archivado => 'bg-stone-400 dark:bg-gray-500',
            self::Bloqueado => 'bg-violet-500 dark:bg-violet-400',
            self::Suspendido => 'bg-orange-500 dark:bg-orange-400',
            self::Cancelado => 'bg-fuchsia-500 dark:bg-fuchsia-400',
            self::Rechazado => 'bg-rose-500 dark:bg-rose-400',
            self::Eliminado => 'bg-zinc-400 dark:bg-gray-500',
        };
    }
}
