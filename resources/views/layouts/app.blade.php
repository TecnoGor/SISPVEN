<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            {{-- Auto-logout por inactividad: el JS (resources/js/app.js) usa
                 estos valores para cerrar la sesión y redirigir al login sin
                 que el usuario tenga que recargar. El tiempo sale de
                 SESSION_LIFETIME (config/session.php), fuente única de verdad. --}}
            <meta name="session-lifetime" content="{{ config('session.lifetime') }}">
        @endauth

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
    class="font-inter antialiased bg-[#F1F0F3] text-black dark:text-gray-500"

        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{
            sidebarOpen: false,
            sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true',
            pinned: localStorage.getItem('sidebar_pinned') === null ? true : localStorage.getItem('sidebar_pinned') === 'true',
            togglePin() {
                this.pinned = !this.pinned;
                localStorage.setItem('sidebar_pinned', this.pinned);
            },
            get expanded() {
                // Contenido desplegado (logo grande, textos, submenús):
                // en desktop manda el pin o el hover; en móvil, si el panel está abierto.
                return this.sidebarOpen || this.pinned;
            }
        }"
        x-init="
            $watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value));
            document.addEventListener('livewire:navigated', () => { if (!pinned) sidebarOpen = false });
        "
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

            <x-app.sidebar :variant="$attributes['sidebarVariant']" />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if($attributes['background']){{ $attributes['background'] }}@endif" x-ref="contentarea">

                <x-app.header :variant="$attributes['headerVariant']" />

                <main class="grow">
                    {{ $slot }}
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
