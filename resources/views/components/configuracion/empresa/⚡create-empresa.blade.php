<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Empresa')]
};
?>

<x-form-style.border-style>
    <x-form-style.main-div class="relative overflow-hidden">
        <x-form-style.header-form
            title="Crear Nueva Empresa"
            description="Configure los datos de la organización médica para integrar sus servicios clínicos."
            sign="Nueva Empresa." />

        <div class="relative z-10 px-8 py-4">
            <form class="grid grid-cols-1 gap-x-10 gap-y-4 md:grid-cols-2">
                {{-- ── Columna izquierda ── --}}
                <div class="space-y-6">
                    <x-form_inputs.text_input
                        label="Nombre de la Empresa"
                        name="nombre"
                        icon="building-office"
                        placeholder="Ej: Clínica Santa María"
                        required />

                    <x-form_inputs.text_input
                        label="CUIT"
                        name="cuit"
                        icon="identification"
                        placeholder="900.123.456-7"
                        required />

                    <x-form_inputs.text_input
                        label="Correo Electrónico Corporativo"
                        name="email"
                        type="email"
                        icon="envelope"
                        placeholder="contacto@empresa.com" />
                </div>

                {{-- ── Columna derecha ── --}}
                <div class="space-y-6">
                    <x-form_inputs.text_input
                        label="Dirección Principal"
                        name="direccion"
                        icon="map-pin"
                        placeholder="Av. Principal #123-45" />

                    <x-form_inputs.text_input
                        label="Teléfono de Contacto"
                        name="telefono"
                        type="tel"
                        icon="phone"
                        placeholder="+57 300 123 4567" />

                    <x-form_inputs.select label="Tipo de Entidad" name="tipo_entidad" icon="building-library">
                        <option value="">Seleccionar tipo…</option>
                        <option value="clinica_privada">Clínica Privada</option>
                        <option value="centro_medico">Centro Médico</option>
                        <option value="laboratorio">Laboratorio</option>
                        <option value="consultorio">Consultorio Independiente</option>
                        <option value="hospital">Hospital</option>
                    </x-form_inputs.select>
                </div>

                {{-- ── Ancho completo: descripción + actions ── --}}
                <div class="space-y-6 border-t border-slate-100 pt-4 dark:border-gray-800 md:col-span-2">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        {{-- Hint informativo --}}
                        <div class="flex gap-1">
                            <x-feedback.alerts
                                type="warning"
                                message="Los datos fiscales Nombre y CUIT no podrán ser editados posteriormente. Por favor, verifique los datos !"></x-feedback.alerts>
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
</x-form-style.border-style>
