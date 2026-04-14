@props([
    "title" => false,
    "description" => false,
    "sign" => false,
])
<div class="flex items-start justify-between border-b border-slate-100 px-8 py-4 dark:border-gray-800">
    <div>
        <h2 class="font-headline text-xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
            {{ $title }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
            {{ $description }}
        </p>
    </div>
    @if ($sign)
        <div
            class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
            <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
            <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                {{ $sign }}
            </span>
        </div>
    @endif
</div>
