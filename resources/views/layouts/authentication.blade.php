<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('Ipostel', 'Ipostel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <div class="w-full items-center bg-black">
        <img src="{{ asset('images/cintillo.jpg') }}" class="w-full h-full flex items-center justify-center" alt="">
    </div>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .flex-grow {
            flex: 1;
        }
    </style>
</head>
<body 
    style="background-image: url('/images/imagen.png'); background-size: cover; background-position: center; background-repeat: no-repeat;" 
    class="dark:bg-gray-900 bg-custom">



    <div class="flex-grow">
        <div class="relative flex">
            <!-- Content -->
            <div class="w-full mt-18 mb-8">
                <div class="min-h-[70dvh] h-full flex flex-col after:flex-1 ">
                    <!-- Header -->
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <!-- Logo -->
                    </div>

                    <div class="max-w-sm mx-auto w-full px-4 py-12 shadow-2xl rounded-lg" style="background-color: rgba(209, 213, 219, 0.6);">

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
   </div>

   <footer class="bg-[rgba(255,255,255,0.5)] shadow mt-4 py-4">
    <div class="max-w-6xl mx-auto text-center">
        <p class="text-black">
            &copy; {{ date('Y') }} Ipostel. Todos los derechos reservados.
        </p>
        <p class="text-black text-sm">
            Diseñado por Ipostel
        </p>
    </div>
</footer>


    @livewireScriptConfig
</body>
</html>
