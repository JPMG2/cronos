{{--
    Contenedor principal de página — <x-form-style.main-div>

    Usar como wrapper raíz dentro de cada vista Livewire.
    El layout (app.blade.php) provee p-6 alrededor, por lo que este componente
    no agrega padding propio; las secciones internas (header, body, footer) lo manejan.

    Light : bg-white + shadow-sm + border slate/200  →  tarjeta sutil sobre fondo blanco
    Dark  : bg-gray-900 + border-gray-800           →  claramente visible sobre bg-gray-950
    Width : w-full — crece/colapsa automáticamente con el sidebar vía el flex-1 del layout
--}}

<div {{ $attributes->merge(['class' => 'main-div-container']) }}>
    {{ $slot }}
</div>
