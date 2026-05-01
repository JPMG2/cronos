@php
    use App\Enums\Styles\InformacionColors;

    $flashes = collect(InformacionColors::cases())
        ->filter(fn (InformacionColors $type) => session()->has($type->value))
        ->map(fn (InformacionColors $type) => [
            'type'    => $type,
            'message' => session($type->value),
        ])
        ->values();
@endphp

@if ($flashes->isNotEmpty())
    <div class="mb-4 space-y-2.5">

        @foreach ($flashes as $flash)
            @php $type = $flash['type']; @endphp

            <div
                x-data="{
                    show: true,
                    progress: 100,
                    init() {
                        const duration = 6000;
                        const start = performance.now();
                        const tick = (now) => {
                            const pct = Math.max(0, 100 - ((now - start) / duration) * 100);
                            this.progress = pct;
                            if (pct > 0) requestAnimationFrame(tick);
                            else this.show = false;
                        };
                        requestAnimationFrame(tick);
                    },
                }"
                x-show="show"
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0 -translate-y-1.5 scale-[0.98]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition duration-250 ease-in"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1.5"
                class="{{ $type->alertWrapperClass() }} relative overflow-hidden rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-black/20"
                role="alert"
                aria-live="polite">

                {{-- Acento lateral izquierdo --}}
                <div class="absolute bottom-0 left-0 top-0 w-[3px] {{ $type->alertAccentClass() }}"></div>

                {{-- Contenido --}}
                <div class="flex items-center gap-2.5 py-2.5 pl-4 pr-3">

                    {{-- Ícono --}}
                    <x-menu.heroicon name="{{ $type->icon() }}" class="h-4 w-4 shrink-0 {{ $type->alertLabelClass() }}" />

                    {{-- Texto en línea --}}
                    <p class="min-w-0 flex-1 font-body text-sm text-slate-800 dark:text-gray-100">
                        <span class="font-headline font-bold {{ $type->alertLabelClass() }}">{{ $type->label() }}:</span>
                        {{ $flash['message'] }}
                    </p>

                    {{-- Botón cerrar --}}
                    <button
                        @click="show = false"
                        class="shrink-0 rounded-md p-0.5 text-slate-400 transition-colors hover:bg-black/5 hover:text-slate-600 dark:text-gray-600 dark:hover:bg-white/5 dark:hover:text-gray-400"
                        aria-label="Cerrar aviso">
                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                </div>

                {{-- Barra de progreso --}}
                <div class="ml-4 h-[2px] bg-black/5 dark:bg-white/5">
                    <div
                        :style="`width: ${progress}%`"
                        class="h-full transition-none {{ $type->alertAccentClass() }} opacity-50">
                    </div>
                </div>

            </div>
        @endforeach

    </div>
@endif
