<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Ipostel - @yield('titulo', 'Previsualización')</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-inter antialiased bg-[#F1F0F3] text-black">
        <div class="w-full bg-white">
            <img src="{{ asset('images/cintillo.jpg') }}" class="w-full h-full" alt="">
        </div>

        <main class="min-h-screen p-4">
            @yield('content')
        </main>

        @livewireScriptConfig
        @livewireScripts
        @stack('scripts')
    </body>
</html>

