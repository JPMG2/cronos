@props(['colorInt'=>0])


<div class="avatar-{{ $colorInt % 6 }} flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xs font-bold">
         {{ $slot }}
</div>