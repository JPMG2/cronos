<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name") . (isset($title) ? " - " . $title : "") }}</title>

        <!-- Dark mode init — debe ejecutarse antes del CSS para evitar flash -->
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="font-sans antialiased">
        {{-- Light: contenido bg-white, nav bg-indigo-50 --}}
        {{-- Dark:  contenido bg-gray-950, nav bg-gray-900 --}}
        <div class="flex h-screen bg-slate-50 dark:bg-gray-950">
            {{-- En mobile el sidebar es fixed (no ocupa espacio en el flex) --}}
            {{-- En desktop flex-shrink-0 evita que el sidebar se comprima --}}
            <div class="md:flex-shrink-0">
                @livewire("menu.sidebar", [], key("sidebar-static"))
            </div>
            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                @livewire("menu.header")
                <main
                    class="flex-1 overflow-y-auto bg-slate-50 px-4 pb-6 pt-4 dark:bg-gray-950"
                    wire:key="main-content-{{ request()->path() }}">
                    <div class="mb-3 flex justify-end">
                        @livewire("form-style.breadcrumb", key("breadcrumb-" . request()->path()))
                    </div>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
