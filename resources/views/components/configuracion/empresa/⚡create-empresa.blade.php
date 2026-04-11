<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Empresa')]
};
?>

<x-form-style.main-div>
    {{-- Header de la sección --}}
    <div class="border-b border-slate-100 px-6 py-4 dark:border-gray-800">
        <h2 class="text-base font-bold text-slate-800 dark:text-gray-100">Crear Empresa</h2>
        <p class="mt-0.5 text-sm text-slate-500 dark:text-gray-500">Completa los datos de la empresa.</p>
    </div>

    {{-- Body --}}
    <div class="px-6 py-5">
        <p class="text-sm text-slate-600 dark:text-gray-400">Aquí irán los inputs del formulario.</p>
    </div>
</x-form-style.main-div>
