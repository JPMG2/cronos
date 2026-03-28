<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config("app.name", "Laravel") }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="font-sans antialiased">
        {{-- Light: contenido bg-white, nav bg-indigo-50 --}}
        {{-- Dark:  contenido bg-gray-950, nav bg-gray-900 --}}
        <div class="flex h-screen bg-white dark:bg-gray-950">
            <div class="flex-shrink-0">
                @livewire("menu.sidebar", [], key("sidebar-static"))
            </div>
            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                @livewire("menu.header")
                <main class="flex-1 overflow-y-auto bg-white p-6 dark:bg-gray-950" wire:key="main-content-{{ request()->path() }}">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
