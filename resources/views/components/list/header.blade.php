@props([
    'code'      => false,
    'codeLabel' => 'Código',
    'name'      => 'Nombre',
    'extra'     => null,
    'toggles'   => [],
    'status'    => 'Estado',
    'color'     => 'indigo',
])

@php
$lbl = 'font-label text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-gray-600';

$gradient = match($color) {
    'teal'    => 'via-teal-200/60 dark:via-teal-800/40',
    'rose'    => 'via-rose-200/60 dark:via-rose-800/40',
    'emerald' => 'via-emerald-200/60 dark:via-emerald-800/40',
    'violet'  => 'via-violet-200/60 dark:via-violet-800/40',
    'amber'   => 'via-amber-200/60 dark:via-amber-800/40',
    default   => 'via-indigo-200/60 dark:via-indigo-800/40',
};
@endphp

<div>
    <div class="flex items-center px-5 py-2">

        {{-- Izquierda: espeja la estructura de la fila de datos --}}
        <div class="flex min-w-0 flex-1 items-center gap-2">
            @if($code)
                <span class="shrink-0 min-w-[52px] text-center {{ $lbl }}">{{ $codeLabel }}</span>
            @endif
            <span class="{{ $lbl }}">{{ $name }}</span>
            @if($extra)
                <span class="hidden {{ $lbl }} sm:block">{{ $extra }}</span>
            @endif
        </div>

        {{-- Derecha: anchos fijos que coinciden con x-list.toggle y x-list.actions --}}
        <div class="flex shrink-0 items-center gap-1.5">
            @foreach($toggles as $toggle)
                <span class="inline-flex w-24 justify-center {{ $lbl }}">{{ $toggle }}</span>
            @endforeach
            @if($status)
                <span class="inline-flex w-20 justify-center {{ $lbl }}">{{ $status }}</span>
            @endif
            @if($status || count($toggles) > 0)
                <span class="w-px"></span>
            @endif
            <span class="inline-flex w-[66px] justify-center {{ $lbl }}">Acciones</span>
        </div>

    </div>
    <div class="mx-5 h-px bg-gradient-to-r from-transparent {{ $gradient }} to-transparent"></div>
</div>
