<?php

use App\Models\Menu;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    /** @return array<int, array{title: string, route: string|null}> */
    #[Computed]
    public function breadcrumbs(): array
    {
        return Menu::breadcrumbTrail(Route::currentRouteName() ?? '');
    }
};
?>

<div wire:ignore>
    @if (count($this->breadcrumbs) > 0)
        <nav aria-label="Breadcrumb">
            <ol role="list" class="flex items-center gap-1.5">

                {{-- Home --}}
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center text-slate-400 transition-colors duration-200 hover:text-indigo-600 dark:text-gray-500 dark:hover:text-sky-400">
                        <x-menu.heroicon name="home" class="h-4 w-4 shrink-0" />
                        <span class="sr-only">Inicio</span>
                    </a>
                </li>

                @foreach ($this->breadcrumbs as $crumb)
                    <li class="flex items-center gap-1.5">

                        {{-- Separador --}}
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                             class="h-4 w-4 shrink-0 text-slate-300 dark:text-gray-700">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" />
                        </svg>

                        @if ($loop->last)
                            <span aria-current="page"
                                  class="text-xs font-semibold text-indigo-600 dark:text-sky-400">
                                {{ $crumb['title'] }}
                            </span>
                        @elseif ($crumb['route'] && Route::has($crumb['route']))
                            <a href="{{ route($crumb['route']) }}"
                               class="text-xs font-medium text-slate-500 transition-colors duration-200 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-sky-400">
                                {{ $crumb['title'] }}
                            </a>
                        @else
                            <span class="text-xs font-medium text-slate-400 dark:text-gray-500">
                                {{ $crumb['title'] }}
                            </span>
                        @endif

                    </li>
                @endforeach

            </ol>
        </nav>
    @endif
</div>
