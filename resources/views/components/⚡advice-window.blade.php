<?php

use App\Dto\Style\ModalConfig;
use App\Enums\Styles\InformacionColors;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;
    public string $title = '';
    public string $type='';
    public string $message = '';
    public string $btnLabel='';
    public string $btnClass = '';
    public string $nameAction = '';
    public array $paramsEvent = [];

    #[On('openModal')]
    public function openModal(array $config): void
    {
        $modal = ModalConfig::fromArray($config);
        $this->title = $modal->title;
        $this->message = $modal->message;
        $this->type  = $modal->type;
        $this->btnLabel = $modal->buttons[0]['label'] ?? '';
        $this->btnClass = $modal->buttons[0]['class'] ?? '';
        $this->nameAction = $modal->buttons[0]['action'] ?? '';
        $this->paramsEvent = $modal->buttons[0]['params'] ?? [];
        $this->show  = true;
    }

    public function fireEvent(): void
    {
      $this->show   = false;
      $this->dispatch($this->nameAction,  (array) $this->paramsEvent);
    }
};
?>

<x-modal name="advice-modal">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-gray-800">

        @php $color = InformacionColors::tryFrom($type) ?? InformacionColors::Info; @endphp
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $color->badgeClasses() }}">
                <x-menu.heroicon name="{{ $color->icon() }}" class="h-4 w-4"/>
            </div>
            <h2 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">
                {{ $title ?? 'Aviso Importante' }}
            </h2>
        </div>

        <button
                x-on:click="$dispatch('close-modal', 'advice-modal')"
                class="rounded-lg p-1.5 text-slate-400 transition-colors
                   hover:bg-slate-100 hover:text-slate-600
                   dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-400"
                aria-label="Cerrar modal">
            <x-menu.heroicon name="x-mark" class="h-5 w-5"/>
        </button>
    </div>

    {{-- ── BODY ── --}}
    <div class="space-y-4 px-6 py-5">

        <p class="font-body text-sm leading-relaxed text-slate-700 dark:text-gray-300">
            {{ $message }}
        </p>




    </div>

    {{-- ── FOOTER ── --}}
    <div class="flex flex-col-reverse gap-2.5 border-t border-slate-100 bg-slate-50/60 px-6 py-4
                dark:border-gray-800 dark:bg-gray-900/40
                sm:flex-row sm:items-center sm:justify-end sm:gap-3">


        <x-btn.cancel label="Cancelar"
          x-on:click="$dispatch('close-modal', 'advice-modal')"
        />
        @if($btnClass)
            <x-dynamic-component :component="'btn.' . $btnClass" :label="$btnLabel" wire:click="fireEvent" />
        @endif
    </div>

</x-modal>
