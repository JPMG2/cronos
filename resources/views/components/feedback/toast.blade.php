{{--
    Sistema de notificaciones flash — <x-feedback.toast />

    Colocar UNA VEZ en layouts/app.blade.php antes de </body>.
    Se activa disparando el evento "notify" desde Livewire:

        $this->dispatch('notify', type: 'success', message: 'Guardado correctamente.');

    Tipos disponibles: success | error | warning | info
    Auto-dismiss: 4.5 segundos con barra de progreso.
    Apilable: hasta 4 toasts simultáneos.
--}}

<div
    x-data="{
        toasts: [],
        config: {
            success: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>`,
                wrapper: 'border-emerald-200 bg-white dark:border-emerald-500/25 dark:bg-gray-900',
                iconWrap: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400',
                label: '¡Éxito!',
                labelColor: 'text-emerald-700 dark:text-emerald-300',
                bar: 'bg-emerald-500 dark:bg-emerald-400',
            },
            error: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>`,
                wrapper: 'border-rose-200 bg-white dark:border-rose-500/25 dark:bg-gray-900',
                iconWrap: 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400',
                label: 'Error',
                labelColor: 'text-rose-700 dark:text-rose-300',
                bar: 'bg-rose-500 dark:bg-rose-400',
            },
            warning: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'/></svg>`,
                wrapper: 'border-amber-200 bg-white dark:border-amber-500/25 dark:bg-gray-900',
                iconWrap: 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
                label: 'Advertencia',
                labelColor: 'text-amber-700 dark:text-amber-300',
                bar: 'bg-amber-500 dark:bg-amber-400',
            },
            info: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'/></svg>`,
                wrapper: 'border-indigo-200 bg-white dark:border-indigo-500/25 dark:bg-gray-900',
                iconWrap: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400',
                label: 'Información',
                labelColor: 'text-indigo-700 dark:text-indigo-300',
                bar: 'bg-indigo-500 dark:bg-sky-400',
            },
        },
        add(event) {
            if (this.toasts.length >= 4) this.toasts.shift();

            const id = Date.now() + Math.random();
            const duration = 4500;

            this.toasts.push({
                id,
                type: event.detail.type ?? 'info',
                message: event.detail.message ?? '',
                progress: 100,
                visible: true,
            });

            const start = performance.now();
            const tick = (now) => {
                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return;
                const elapsed = now - start;
                toast.progress = Math.max(0, 100 - (elapsed / duration) * 100);
                if (toast.progress > 0) {
                    requestAnimationFrame(tick);
                } else {
                    this.dismiss(id);
                }
            };
            requestAnimationFrame(tick);
        },
        dismiss(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) toast.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 320);
        },
    }"
    @notify.window="add($event)"
    class="fixed right-4 top-4 z-[9999] flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2.5"
    aria-live="polite"
    role="region"
    aria-label="Notificaciones del sistema">

    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-10 scale-95 opacity-0"
            x-transition:enter-end="translate-x-0 scale-100 opacity-100"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="translate-x-0 scale-100 opacity-100"
            x-transition:leave-end="translate-x-10 scale-95 opacity-0"
            :class="config[toast.type]?.wrapper ?? config.info.wrapper"
            class="relative overflow-hidden rounded-xl border shadow-lg shadow-slate-200/60 dark:shadow-black/30">

            {{-- Contenido principal --}}
            <div class="flex items-start gap-3 px-4 py-3.5">

                {{-- Ícono --}}
                <div
                    :class="config[toast.type]?.iconWrap ?? config.info.iconWrap"
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg">
                    <div class="h-4 w-4" x-html="config[toast.type]?.icon ?? config.info.icon"></div>
                </div>

                {{-- Texto --}}
                <div class="min-w-0 flex-1 pt-0.5">
                    <p
                        :class="config[toast.type]?.labelColor ?? config.info.labelColor"
                        class="font-label text-xs font-bold uppercase tracking-wider"
                        x-text="config[toast.type]?.label ?? 'Notificación'">
                    </p>
                    <p
                        class="mt-0.5 font-body text-sm leading-snug text-slate-700 dark:text-gray-300"
                        x-text="toast.message">
                    </p>
                </div>

                {{-- Botón cerrar --}}
                <button
                    @click="dismiss(toast.id)"
                    class="ml-1 shrink-0 rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-400"
                    aria-label="Cerrar notificación">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>

            {{-- Barra de progreso --}}
            <div class="h-0.5 w-full bg-slate-100 dark:bg-gray-800">
                <div
                    :class="config[toast.type]?.bar ?? config.info.bar"
                    :style="`width: ${toast.progress}%`"
                    class="h-full transition-none">
                </div>
            </div>

        </div>
    </template>
</div>
