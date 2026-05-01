@props(['number'=>0,
'label'=>''])

<div class="mb-3 flex items-center gap-2.5">
      <span class="inline-flex h-5 items-center rounded-md bg-indigo-100 px-2 text-xs font-bold text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">{{ $number }}</span>
      <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">{{ $label }}</span>
</div>