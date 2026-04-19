{{--
    Contenedor principal de página — <x-form-style.main-div>


    Usar como wrapper raíz dentro de cada vista Livewire.
    El layout (app.blade.php) provee p-6 alrededor, por lo que este componente
    no agrega padding propio; las secciones internas (header, body, footer) lo manejan.


    Light : bg-white + shadow-sm + border slate/200  →  tarjeta sutil sobre fondo blanco
    Dark  : bg-gray-900 + border-gray-800           →  claramente visible sobre bg-gray-950
    Width : w-full — crece/colapsa automáticamente con el sidebar vía el flex-1 del layout
--}}

<div {{ $attributes->merge(["class" => "main-div-container relative"]) }}>
    <div
        class="pointer-events-none absolute right-0 top-0 h-48 w-48 rounded-full bg-indigo-600/5 blur-3xl dark:bg-indigo-400/5"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 h-48 w-48 rounded-full bg-sky-400/5 blur-3xl"></div>
    {{ $slot }}
</div>
