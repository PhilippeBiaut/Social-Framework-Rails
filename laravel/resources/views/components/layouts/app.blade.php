<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        {{-- Avoid a flash of the wrong theme before Alpine boots. --}}
        <script>
            (function () {
                var saved = localStorage.getItem('theme');
                var dark = saved ? saved === 'dark'
                                 : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', dark);
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
        <x-flash />

        @auth
            <x-navbar />
            <main class="mx-auto max-w-2xl px-4 pb-24 pt-6 sm:pt-8">
                {{ $slot }}
            </main>
        @else
            <main class="flex min-h-screen items-center justify-center px-4 py-10">
                {{ $slot }}
            </main>
        @endauth

        @livewireScripts
    </body>
</html>
