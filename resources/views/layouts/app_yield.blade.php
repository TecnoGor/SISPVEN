<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Ipostel - @yield('titulo')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        @stack('styles')
    </head>
    <body
    class="font-inter antialiased bg-[#F1F0F3] text-black dark:text-gray-500 pl-24 lg:pl-0"
        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
        x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"
    >
        <script>
            if (localStorage.getItem('sidebar-expanded') == 'true') {
                document.querySelector('body').classList.add('sidebar-expanded');
            } else {
                document.querySelector('body').classList.remove('sidebar-expanded');
            }
        </script>

        <div class="w-full  items-center  bg-white">
            <img src="{{ asset('images/cintillo.jpg') }}" class="w-full h-full flex items-center justify-center  " alt="">
        </div>

        <!-- Page wrapper -->
        <div class="flex h-[90dvh] overflow-hidden">

            <x-app.sidebar :variant="null" />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden" x-ref="contentarea">

                <x-app.header :variant="null" />

                <main class="grow">
                    @yield('content')
                </main>

            </div>

        </div>

        @livewireScriptConfig
        @livewireScripts
        @stack('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        @include('partials.validation-alert')
    </body>
</html>

