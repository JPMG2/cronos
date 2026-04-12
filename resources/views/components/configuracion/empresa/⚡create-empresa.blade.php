<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Empresa')]
};
?>

{{--
    ════════════════════════════════════════════════════════════════════
    Crear Empresa — "The Ethereal Clinical"
    Paleta: indigo-600 (light primary) / sky-400 (dark primary)
    Layout: header + form 2 cols + info cards
    ════════════════════════════════════════════════════════════════════
--}}
<x-form-style.main-div class="relative overflow-hidden">
    {{-- ── Orbes decorativos (detrás de todo) ────────────────────── --}}
    <div
        class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-indigo-600/5 blur-3xl dark:bg-indigo-400/5"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-sky-400/5 blur-3xl"></div>

    <div class="flex items-start justify-between border-b border-slate-100 px-8 py-6 dark:border-gray-800">
        <div>
            <h2 class="font-headline text-2xl font-extrabold tracking-tight text-slate-800 dark:text-gray-100">
                Crear Nueva Empresa
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                Configure los datos de la organización médica para integrar sus servicios clínicos.
            </p>
        </div>
        {{-- Badge de sesión activa --}}
        <div
            class="hidden shrink-0 items-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-2 dark:border-indigo-800/30 dark:bg-indigo-900/20 sm:flex">
            <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500 dark:bg-sky-400"></span>
            <span class="font-label text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-sky-400">
                Nueva entidad
            </span>
        </div>
    </div>

    {{--
        ══════════════════════════════════════════════════════════════
        FORMULARIO
        ══════════════════════════════════════════════════════════════
    --}}
    <div class="relative z-10 px-8 py-8">
        <form class="grid grid-cols-1 gap-x-10 gap-y-7 md:grid-cols-2">
            {{-- ── Columna izquierda ── --}}
            <div class="space-y-6">
                <x-form_inputs.text_input
                    label="Nombre de la Empresa"
                    name="nombre"
                    icon="building-office"
                    placeholder="Ej: Clínica Santa María"
                    required
                />

                <x-form_inputs.text_input
                    label="NIT / ID Fiscal"
                    name="nit"
                    icon="identification"
                    placeholder="900.123.456-7"
                    required
                />

                <x-form_inputs.text_input
                    label="Correo Electrónico Corporativo"
                    name="email"
                    type="email"
                    icon="envelope"
                    placeholder="contacto@empresa.com"
                />
            </div>

            {{-- ── Columna derecha ── --}}
            <div class="space-y-6">
                <x-form_inputs.text_input
                    label="Dirección Principal"
                    name="direccion"
                    icon="map-pin"
                    placeholder="Av. Principal #123-45"
                />

                <x-form_inputs.text_input
                    label="Teléfono de Contacto"
                    name="telefono"
                    type="tel"
                    icon="phone"
                    placeholder="+57 300 123 4567"
                />

                <x-form_inputs.select
                    label="Tipo de Entidad"
                    name="tipo_entidad"
                    icon="building-library"
                >
                    <option value="">Seleccionar tipo…</option>
                    <option value="clinica_privada">Clínica Privada</option>
                    <option value="centro_medico">Centro Médico</option>
                    <option value="laboratorio">Laboratorio</option>
                    <option value="consultorio">Consultorio Independiente</option>
                    <option value="hospital">Hospital</option>
                </x-form_inputs.select>
            </div>

            {{-- ── Ancho completo: descripción + actions ── --}}
            <div class="space-y-6 border-t border-slate-100 pt-7 dark:border-gray-800 md:col-span-2">
                <x-form_inputs.textarea
                    label="Descripción o Notas Adicionales"
                    name="descripcion"
                    description="(opcional)"
                    placeholder="Breve descripción de la actividad comercial o notas de facturación…"
                    :rows="4"
                />

                {{-- Action bar --}}
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    {{-- Hint informativo --}}
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/30">
                            <x-menu.heroicon
                                name="information-circle"
                                class="h-4 w-4 text-indigo-500 dark:text-indigo-400" />
                        </div>
                        <p class="max-w-sm text-xs leading-relaxed text-slate-400 dark:text-gray-500">
                            Al guardar, se enviará una invitación de configuración al correo electrónico proporcionado.
                        </p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        <button
                            type="button"
                            class="flex-1 rounded-xl px-6 py-3 text-sm font-semibold text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300/50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200 sm:flex-none">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="signature-gradient inline-flex flex-1 items-center justify-center gap-2 rounded-xl px-8 py-3 text-sm font-bold text-white shadow-[0px_8px_24px_rgba(79,70,229,0.20)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0px_12px_32px_rgba(79,70,229,0.30)] focus:outline-none focus:ring-2 focus:ring-indigo-400/40 active:scale-[0.98] dark:shadow-[0px_8px_24px_rgba(14,165,233,0.15)] dark:focus:ring-sky-400/40 sm:flex-none">
                            <x-menu.heroicon name="document-check" class="h-4 w-4" />
                            Guardar Empresa
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-form-style.main-div>
