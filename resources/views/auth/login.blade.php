<x-authentication-layout>
    <a class="block text-center" href="{{ route('dashboard') }}">
        <!-- Ajusta el tamaño de la imagen y centra dentro del contenedor -->
        <img src="images/sispven.png" class="w-1xl h-auto mx-auto" alt="Logo"> <!-- Ajustado tamaño de imagen -->
    </a>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    @if (request('expirado'))
        <div class="mb-4 flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/60">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3.75 2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div class="text-sm leading-snug">
                <p class="font-semibold text-gray-800 dark:text-gray-100">Tu sesión se cerró</p>
                <p class="text-gray-500 dark:text-gray-400">Ingresa de nuevo para continuar.</p>
            </div>
        </div>
    @endif
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4 ">          
            <div>
                <x-label for="email" value="{{ __('Correo') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus />                
            </div>
            <div>
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <div class="relative">
                    <x-input id="password" type="password" name="password" required autocomplete="current-password" class="pr-10" />
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Mostrar u ocultar contraseña">
                        <!-- Icono: ojo (mostrar) -->
                        <svg id="eyeShow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <!-- Icono: ojo tachado (ocultar) -->
                        <svg id="eyeHide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))             
            @endif            
            <x-button class="items-center w-full">
                {{ __('Iniciar Sesion') }}
            </x-button>            
        </div>
    </form>
    <x-validation-errors class="mt-4" />
    <!-- Footer -->

    <script>
        (function () {
            const toggle = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            const eyeShow = document.getElementById('eyeShow');
            const eyeHide = document.getElementById('eyeHide');

            if (toggle && input) {
                toggle.addEventListener('click', function () {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    eyeShow.classList.toggle('hidden', isPassword);
                    eyeHide.classList.toggle('hidden', !isPassword);
                });
            }
        })();
    </script>
</x-authentication-layout>
