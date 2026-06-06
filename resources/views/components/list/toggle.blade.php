@props([
    'active'        => false,
    'activeLabel'   => 'Activo',
    'inactiveLabel' => 'Inactivo',
    'activeColor'   => 'emerald',
    'inactiveColor' => 'slate',
    'size'          => 'md',
])

@php
$width = $size === 'sm' ? 'w-20' : 'w-24';

$activeBadge = match($activeColor) {
    'teal'   => 'bg-teal-100 text-teal-700 ring-teal-200/60 hover:bg-teal-200/80 dark:bg-teal-500/10 dark:text-teal-400 dark:ring-teal-500/20 dark:hover:bg-teal-500/20',
    'violet' => 'bg-violet-100 text-violet-600 ring-violet-200/60 hover:bg-violet-200/80 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20 dark:hover:bg-violet-500/20',
    'indigo' => 'bg-indigo-100 text-indigo-700 ring-indigo-200/60 hover:bg-indigo-200/80 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20 dark:hover:bg-indigo-500/20',
    'amber'  => 'bg-amber-100 text-amber-700 ring-amber-200/60 hover:bg-amber-200/80 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20 dark:hover:bg-amber-500/20',
    default  => 'bg-emerald-100 text-emerald-700 ring-emerald-200/60 hover:bg-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20 dark:hover:bg-emerald-500/20',
};

$inactiveBadge = match($inactiveColor) {
    'violet' => 'bg-violet-100 text-violet-600 ring-violet-200/60 hover:bg-violet-200/80 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20 dark:hover:bg-violet-500/20',
    default  => 'bg-slate-100 text-slate-500 ring-slate-200/60 hover:bg-slate-200/80 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-700 dark:hover:bg-gray-700/60',
};

$activeDot = match($activeColor) {
    'teal'   => 'bg-teal-500 dark:bg-teal-400',
    'violet' => 'bg-violet-500 dark:bg-violet-400',
    'indigo' => 'bg-indigo-500 dark:bg-indigo-400',
    'amber'  => 'bg-amber-500 dark:bg-amber-400',
    default  => 'bg-emerald-500 dark:bg-emerald-400',
};

$inactiveDot = match($inactiveColor) {
    'violet' => 'bg-violet-500 dark:bg-violet-400',
    default  => 'bg-slate-400 dark:bg-gray-600',
};
@endphp

<button type="button"
        {{ $attributes->class([
            "flex {$width} items-center justify-center",
            'rounded-lg transition-colors duration-150 active:scale-95',
            'focus:outline-none focus:ring-2 focus:ring-inset focus:ring-slate-400/20',
        ]) }}>
    @if($active)
        <span class="inline-flex items-center gap-1.5 rounded-lg px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 {{ $activeBadge }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $activeDot }}"></span>
            {{ $activeLabel }}
        </span>
    @else
        <span class="inline-flex items-center gap-1.5 rounded-lg px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset transition-colors duration-150 {{ $inactiveBadge }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $inactiveDot }}"></span>
            {{ $inactiveLabel }}
        </span>
    @endif
</button>
