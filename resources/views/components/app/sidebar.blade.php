@php
    $usuario = auth()->user();
    $tipoOficinaId = optional(optional($usuario->oficina)->tipo_oficina)->tipo_oficina_id;
@endphp

<div
    :style="`--sidebar-w: ${expanded ? '312px' : '80px'};`"
    class="relative shrink-0 h-full w-0 lg:w-[var(--sidebar-w)] lg:[transition:width_500ms_cubic-bezier(0.4,0,0.2,1)]">

    <!-- Backdrop para móvil: cierra el sidebar al tocar fuera -->
    <div x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden"
        style="display: none;"></div>

    <!-- Sidebar -->
    <div id="sidebar"
        x-cloak
        class="flex flex-col fixed lg:absolute z-40 left-0 top-0 h-full overflow-y-scroll overflow-x-hidden no-scrollbar text-white will-change-[width]
               max-lg:transition-transform max-lg:duration-300"
        :class="{ 'max-lg:translate-x-0': sidebarOpen, 'max-lg:-translate-x-full': !sidebarOpen }"
        :style="`width: ${expanded ? '312px' : '80px'}; transition: width 500ms cubic-bezier(0.4,0,0.2,1); background-image: url('/images/imagen.png'); background-size: cover; background-position: center; background-repeat: no-repeat;`"
        @mouseenter="if (window.matchMedia('(min-width: 1024px)').matches && !pinned) sidebarOpen = true"
        @mouseleave="if (window.matchMedia('(min-width: 1024px)').matches && !pinned) sidebarOpen = false">
        <!-- Botón chinche (pin) en esquina superior derecha -->
        <button @click="togglePin()"
            x-show="expanded"
            x-transition
            :title="pinned ? 'Fijado' : 'Fijar sidebar'"
            class="hidden lg:block"
            :class="pinned
                ? 'absolute top-2 right-2 z-50 p-1.5 rounded-full text-white hover:bg-white/15 transition'
                : 'absolute top-2 right-2 z-50 p-1.5 rounded-full text-white/40 hover:text-white/70 hover:bg-white/10 transition'">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16"
                :class="pinned ? '' : 'rotate-45'">
                <path d="M4.146.146A.5.5 0 0 1 4.5 0h7a.5.5 0 0 1 .5.5c0 .68-.342 1.174-.646 1.479-.126.125-.25.224-.354.298v4.431l.078.048c.203.127.476.314.751.555C12.36 7.775 13 8.527 13 9.5a.5.5 0 0 1-.5.5h-4v4.5c0 .276-.224 1.5-.5 1.5s-.5-1.224-.5-1.5V10h-4a.5.5 0 0 1-.5-.5c0-.973.64-1.725 1.17-2.189A6 6 0 0 1 5 6.708V2.277a3 3 0 0 1-.354-.298C4.342 1.674 4 1.179 4 .5a.5.5 0 0 1 .146-.354"/>
            </svg>
        </button>

        <!-- Encabezado del Sidebar -->
        <div class="flex flex-col items-center p-2 gap-2">
            <!-- Logo -->
            <a class="flex items-center w-full">
                <img x-show="expanded" class="w-52 h-20 transition-all duration-200 ml-2"
                    src="{{ asset('images/ipostel.png') }}" alt="Logo normal">
                <img x-show="!expanded" class="w-20 h-10 transition-all duration-200 ml-2"
                    src="{{ asset('images/logo.png') }}" alt="Logo contraído">
            </a>
        </div>

        <!-- Links -->
        <div class="flex items-left justify-between p-2 min-w-0">
            <!-- Pages group -->
            <div class="min-w-0 w-full">
                <h3 class="text-xs uppercase text-white dark:text-white font-semibold pl-1 mt-6 ">

                </h3>
                <ul class="mt-2 ml-2"
                    x-effect="
                        if (!expanded) {
                            $el.querySelectorAll('[x-data]').forEach(el => {
                                const data = Alpine.$data(el);
                                if ('open' in data) {
                                    if (!('_wasOpen' in data)) data._wasOpen = false;
                                    data._wasOpen = data.open;
                                    data.open = false;
                                }
                            });
                        } else {
                            $el.querySelectorAll('[x-data]').forEach(el => {
                                const data = Alpine.$data(el);
                                if ('open' in data && '_wasOpen' in data) {
                                    data.open = data._wasOpen;
                                }
                            });
                        }
                    ">
                    <!-- Dashboard -->

                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(100deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['dashboard', 'integrantes', 'semaforo-postal', 'gestion-clientes', 'asignacion-jefe-opt'])) {{ 'from-white' }} @endif"
                        x-data="{ open: {{ in_array(Request::segment(1), ['dashboard', 'integrantes', 'semaforo-postal', 'gestion-clientes', 'asignacion-jefe-opt']) ? 1 : 0 }} }">
                        <a class="block text-white dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                            href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['dashboard', 'integrantes', 'semaforo-postal', 'gestion-clientes', 'asignacion-jefe-opt'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                                        <path
                                            d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                                    </svg>
                                    <span
                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gestión</span>
                                </div>
                                <!-- Icon -->
                                <div
                                    class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                        :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                        <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                            <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                :class="open ? '!block' : 'hidden'">
                                <li class="mb-1 last:mb-0">
                                    <a class="block text-white dark:text-white hover:text-white dark:hover:text-whitetransition truncate @if (Route::is('dashboard')) {{ '!text-primary' }} @endif"
                                        href="{{ route('dashboard') }}">
                                        @can('Ver estadisticas')
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Estadisticas</span>
                                        @endcan
                                        @cannot('Ver estadisticas')
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Inicio</span>
                                        @endcannot
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                            <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['integrantes-gestion'])) {{ 'hidden' }} @endif"
                                :class="open ? '!block' : 'hidden'">
                                <li class="mb-1 last:mb-0">
                                    <a class="block text-white dark:text-white hover:text-white dark:hover:text-whitetransition truncate @if (Route::is('integrantes-gestion')) {{ '!text-primary' }} @endif"
                                        href="{{ route('integrantes-gestion') }}" wire:navigate>
                                        @can('Gestionar Integrantes')
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gestión
                                                de Integrantes</span>
                                        @endcan
                                    </a>
                                </li>
                            </ul>
                        </div> --}}

                        @can('Ver Semaforo Postal')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['semaforo-postal'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-whitetransition truncate @if (Route::is('semaforo-postal')) {{ '!text-primary' }} @endif"
                                            href="{{ route('semaforo-postal') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Semaforo
                                                Postal
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        @can('Ver Informacion de Envíos')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['almacen-de-oficinas'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('almacen-de-oficinas')) {{ '!text-primary' }} @endif"
                                            href="{{ route('almacen-de-oficinas') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Información de Envios
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan
                        <!-- Gestion Clientes -->
                        <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                            <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['gestion-clientes'])) {{ 'hidden' }} @endif"
                                :class="open ? '!block' : 'hidden'">
                                <li class="mb-1 last:mb-0">
                                    <a class="block text-white dark:text-white hover:text-white transition truncate @if (Route::is('clientes.index')) {{ '!text-primary' }} @endif"
                                        href="{{ route('clientes.index') }}" wire:navigate>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Gestión de Clientes
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Asignacion Jefe OPT -->
                        @can('Ver Asignacion de Jefe en Opts')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['asignacion-jefe-opt'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white transition truncate @if (Route::is('asignacion-jefe-opt')) {{ '!text-primary' }} @endif"
                                            href="{{ route('asignacion-jefe-opt') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Asignación de Jefe OPT
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        @can('Ver Asignacion de Gerente Estado')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['asignar-gerente-estado'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white transition truncate @if (Route::is('asignar-gerente-estado')) {{ '!text-primary' }} @endif"
                                            href="{{ route('asignar-gerente-estado') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Asignación de Gerente de Estado
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        @can('Ver Asignacion de Presidente')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['asignar-presidente'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white transition truncate @if (Route::is('asignar-presidente')) {{ '!text-primary' }} @endif"
                                            href="{{ route('asignar-presidente') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Asignación de Presidente
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan
                    </li>


                    <!-- Cuenta -->
                    @can('Ver usuarios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['cuentas', 'roles'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['cuentas', 'roles']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['cuentas', 'roles'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            class="size-6  @if (in_array(Request::segment(1), ['cuentas', 'roles'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Usuarios
                                            y Roles</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('cuentas')) {{ '!text-primary' }} @endif"
                                            href="{{ route('cuentas') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Cuentas</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @can('Ver Roles')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('roles-mostrar')) {{ '!text-primary' }} @endif"
                                                href="{{ route('roles-mostrar') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Roles</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                        </li>
                    @endcan
                    @canany(['Realizar Entradas de Despacho', 'Realizar Salidas de Despacho'])
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['Registrar-Entrada', 'servicios', 'servicios-internacionales', 'Registrar-Salida'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['Registrar-Entrada', 'Registrar-Salida', 'servicios-internacionales', 'servicios-flota']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Gestion de Despachos'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), ['ver-tasas', 'servicios', 'Registrar-Entrada'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M7 4V20H17V4H7ZM6 2H18C18.5523 2 19 2.44772 19 3V21C19 21.5523 18.5523 22 18 22H6C5.44772 22 5 21.5523 5 21V3C5 2.44772 5.44772 2 6 2ZM12 17C12.5523 17 13 17.4477 13 18C13 18.5523 12.5523 19 12 19C11.4477 19 11 18.5523 11 18C11 17.4477 11.4477 17 12 17Z">
                                            </path>
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gestion
                                            de Paquetes</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>



                            @if (in_array($tipoOficinaId, [4]))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('Registrar-Entrada-Cop')) {{ '!text-primary' }} @endif"
                                                href="{{ route('entradaCOP') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                    {{ $usuario->oficina_id == 10 ? 'Entrada CPC' : 'entrada COP' }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif


                            @if (in_array($tipoOficinaId, [4]))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('Registrar-Salida-Cop')) {{ '!text-primary' }} @endif"
                                                href="{{ route('salidaCOP') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                    {{ $usuario->oficina_id == 10 ? 'Salida CPC' : 'salida COP' }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            @if (in_array($tipoOficinaId, [1, 2, 3]))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('Registrar-Entrada')) {{ '!text-primary' }} @endif"
                                                href="{{ route('entradaOPT') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                    entrada OPT</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            @if (in_array($tipoOficinaId, [1, 2, 3]))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('Registrar-Salida')) {{ '!text-primary' }} @endif"
                                                href="{{ route('salidaOPT') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                    salida OPT</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endcan
                    <!-- Roles -->


                    <!-- Oficinas -->
                    @can('Ver Oficinas')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), [
                                'oficinas',
                                'sacas',
                                'codigo-apartado',
                                'ver-almacen',
                                'oficina-externa',
                                'confirmacion-telegrama',
                                'aduana',
                                'expedicion',
                                'distribucion-paquetes-muestras',
                                'unidad-analisis-devolucion',
                                'almacen-rezago',
                            ])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['oficinas', 'sacas', 'codigo-apartado', 'ver-almacen', 'oficina-externa', 'confirmacion-telegrama', 'aduana', 'expedicion', 'distribucion-paquetes-muestras', 'unidad-analisis-devolucion', 'almacen-rezago']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (
                                !in_array(Request::segment(1), [
                                    'Oficinas',
                                    'sacas',
                                    'codigo-apartado',
                                    'ver-almacen',
                                    'oficina-externa',
                                    'confirmacion-telegrama',
                                    'aduana',
                                    'expedicion',
                                    'distribucion-paquetes-muestras',
                                    'unidad-analisis-devolucion',
                                    'almacen-rezago',
                                ])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), ['oficinas'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                        </svg>


                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Oficinas</span>

                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        @can('Ver Oficinas-admin')
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('oficinas-mostrar')) {{ '!text-primary' }} @endif"
                                                href="{{ route('oficinas-mostrar') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">Ver
                                                    Oficinas</span>
                                            </a>
                                        @endcan
                                    </li>
                                </ul>
                            </div>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        @can('Crear Oficinas Aliadas')
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('oficina-externa')) {{ '!text-primary' }} @endif"
                                                href="{{ route('oficina-externa') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">Oficinas
                                                    Aliadas</span>
                                            </a>
                                        @endcan
                                    </li>
                                </ul>
                            </div>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        @can('Ver Mi oficina')
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate"
                                                @if (Route::is('mi-oficina')) {{ '!text-primary' }} @endif"
                                                href="{{ route('mi-oficina') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Mi
                                                    Oficina</span>
                                            </a>
                                        @endcan
                                    </li>
                                </ul>
                            </div>

                            @can('Ver Guias de Despacho')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate"
                                                @if (Route::is('Guias-Despacho')) {{ '!text-primary' }} @endif"
                                                href="{{ route('manifiestos') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Guias
                                                    de Despacho</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                            @if ($usuario->oficina_id && $usuario->can('Ver valijas'))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('mostrar-sacas')) {{ '!text-primary' }} @endif"
                                                href="{{ route('mostrar-sacas') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">Valijas</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif


                            @if ($usuario->oficina_id && $usuario->can('Ver Despachos Disponibles'))
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('ver-almacen')) {{ '!text-primary' }} @endif"
                                                href="{{ route('ver-almacen') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">Paquetes
                                                    Disponibles</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif


                            @if ($usuario->oficina_id)
                                @can('Visualizar Apartados')
                                    <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                        <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                            :class="open ? '!block' : 'hidden'">
                                            <li class="mb-1 last:mb-0">
                                                <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate  @if (Route::is('codigo-apartado')) {{ '!text-primary' }} @endif"
                                                    href="{{ route('codigo-apartado') }}" wire:navigate>
                                                    <span
                                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Apartados
                                                        Postales</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endcan
                            @endif

                            @if ($usuario->oficina_id)
                                @can('Ver telegramas')
                                    <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                        <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                            :class="open ? '!block' : 'hidden'">
                                            <li class="mb-1 last:mb-0">
                                                <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate  @if (Route::is('confirmacion-telegrama')) {{ '!text-primary' }} @endif"
                                                    href="{{ route('confirmacion-telegrama') }}" wire:navigate>
                                                    <span
                                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Recepcion
                                                        de Telegramas</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endcan
                            @endif

                            @if ($usuario->oficina_id)
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('aduana')) {{ '!text-primary' }} @endif"
                                                href="{{ route('aduana') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Aduana</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            @if ($usuario->oficina_id && $usuario->oficina_id === 10)
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('expedicion')) {{ '!text-primary' }} @endif"
                                                href="{{ route('expedicion') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Expedición</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            @if ($usuario->oficina_id && $usuario->oficina_id === 10)
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('distribucion-paquetes-muestras')) {{ '!text-primary' }} @endif"
                                                href="{{ route('distribucion-paquetes-muestras') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Distribución Paquetes Muestras</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            @if ($usuario->oficina_id && $usuario->oficina_id === 10)
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('unidad-analisis-devolucion')) {{ '!text-primary' }} @endif"
                                                href="{{ route('unidad-analisis-devolucion') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Unidad de Análisis Devolucion</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('almacen-rezago')) {{ '!text-primary' }} @endif"
                                            href="{{ route('almacen-rezago') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Almacén de Rezago</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    <!-- Envios -->
                    @can('Ver envios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), [
                                'envios',
                                'consulta-envios',
                                'envios-confirmar',
                                'listado-ventas',
                                'listado-ventas-nacionales',
                                'listado-ventas-internacionales',
                                'cierre-caja',
                                'devolucion-incidencia',
                                'envios-internacionales',
                                'envios-corporativos',
                                'recibo-consignacion',
                                'envios-lotes',
                                'carga-masiva',
                            ])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['envios', 'consulta-envios', 'envios-confirmar', 'listado-ventas', 'listado-ventas-nacionales', 'listado-ventas-internacionales', 'cierre-caja', 'devolucion-incidencia', 'envios-internacionales', 'envios-corporativos', 'envios-lotes', 'carga-masiva', 'recibo-consignacion']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Envios'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), [
                                                    'envios',
                                                    'consulta-envios',
                                                    'envios-confirmar',
                                                    'listado-ventas',
                                                    'listado-ventas-nacionales',
                                                    'listado-ventas-internacionales',
                                                    'cierre-caja',
                                                    'devolucion-incidencia',
                                                    'envios-internacionales',
                                                    'envios-corporativos',
                                                    'envios-lotes',
                                                    'carga-masiva',
                                                    'recibo-consignacion',
                                                ])) {{ 'text-primary' }}@else{{ 'text-white dark:text-black' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>


                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Envios</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>

                            @can('Crear envios')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('envios-lotes')) {{ '!text-primary' }} @endif"
                                                href="{{ route('envios-lotes') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Crear
                                                    Envios</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                            @can('Crear envios')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('carga-masiva')) {{ '!text-primary' }} @endif"
                                                href="{{ route('carga-masiva') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Carga
                                                    Masiva</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                            @can('Aperturar Envios')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('envios-internacionales')) {{ '!text-primary' }} @endif"
                                                href="{{ route('envios-internacionales') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Apertura
                                                    de Envios Internacionales</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                            @can('Crear envios')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('envios-corporativos')) {{ '!text-primary' }} @endif"
                                                href="{{ route('envios-corporativos') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Envios
                                                    Corporativos</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('confirmar-envios')) {{ '!text-primary' }} @endif"
                                            href="{{ route('confirmar-envios') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Envios
                                                por confirmar</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @can('Ver Recibos Consignacion')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('recibo-consignacion')) {{ '!text-primary' }} @endif"
                                                href="{{ route('recibo-consignacion') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                    Recibos de consignación
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                            @can('Ver Listado de Ventas')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('envios.listado-ventas') ||
                                                    Route::is('envios.listado-ventas-nacionales') ||
                                                    Route::is('envios.listado-ventas-internacionales')) {{ '!text-primary' }} @endif"
                                                href="{{ route('envios.listado-ventas') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Listado
                                                    de Ventas</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                            @can('Crear envios')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-gray-200 transition truncate @if (Route::is('devolucion-incidencia') || Route::is('devolucion-incidencia') || Route::is('devolucion-incidencia')) {{ '!text-primary' }} @endif"
                                                href="{{ route('devolucion-incidencia') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Incidencias/Devoluciones</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan

                        </li>
                    @endcan


                    @can('Servicios extras')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['apartado', 'tarjetas-postales', 'apartado-beneficiarios'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['apartado', 'tarjetas-postales', 'apartado-beneficiarios']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['apartado', 'tarjetas-postales', 'apartado-beneficiarios'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m7.875 14.25 1.214 1.942a2.25 2.25 0 0 0 1.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 0 1 1.872 1.002l.164.246a2.25 2.25 0 0 0 1.872 1.002h2.092a2.25 2.25 0 0 0 1.872-1.002l.164-.246A2.25 2.25 0 0 1 16.954 9h4.636M2.41 9a2.25 2.25 0 0 0-.16.832V12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 0 1 .382-.632l3.285-3.832a2.25 2.25 0 0 1 1.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0 0 21.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Servicios
                                            Extras</span>

                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('apartado-postal')) {{ '!text-primary' }} @endif"
                                            href="{{ route('apartado-postal') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Asignacion de <br> Apartado Postal</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('tarjetas-postales')) {{ '!text-primary' }} @endif"
                                            href="{{ route('tarjetas-postales') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Venta de <br> Tarjetas Postales</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('apartados-beneficiarios')) {{ '!text-primary' }} @endif"
                                            href="{{ route('apartados-beneficiarios') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Beneficiarios de <br> Apartado</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('Servicios especiales')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), [
                                'almacenamiento',
                                'imprenta',
                                'pedidos-imprenta',
                                'filatelia',
                                'alianza-recaudacion',
                                'apostilla',
                            ])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['almacenamiento', 'imprenta', 'pedidos-imprenta', 'filatelia', 'alianza-recaudacion', 'apostilla']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (
                                !in_array(Request::segment(1), [
                                    'almacenamiento',
                                    'imprenta',
                                    'pedidos-imprenta',
                                    'filatelia',
                                    'alianza-recaudacion',
                                    'apostilla',
                                ])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Servicios
                                            Especiales</span>

                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('almacenamiento')) {{ '!text-primary' }} @endif"
                                            href="{{ route('almacenamiento') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Almacenamiento</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('imprenta')) {{ '!text-primary' }} @endif"
                                            href="{{ route('imprenta') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Imprenta</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('pedidos-imprenta')) {{ '!text-primary' }} @endif"
                                            href="{{ route('pedidos-imprenta') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Pedidos de Imprenta</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('filatelia')) {{ '!text-primary' }} @endif"
                                            href="{{ route('filatelia') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Filatelia</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('alianza-recaudacion')) {{ '!text-primary' }} @endif"
                                            href="{{ route('alianza-recaudacion') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                Alianza y Recaudacion</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            @can('Ver Apostilla')
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['apostilla'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('apostilla')) {{ '!text-primary' }} @endif"
                                            href="{{ route('apostilla') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Apostilla</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @endcan
                        </li>
                    @endcan

                    @can('Ver telegramas')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['telegrama'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['telegrama']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['telegrama'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('telegrama') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Telegramas</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan


                    {{-- @can('Clientes corporativos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['clientes-corporativos'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['clientes-corporativos']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['clientes-corporativos'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('clientes-corporativos') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Clientes
                                            Corporativos</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan --}}

                    @can('Clientes corporativos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['clientes-corporativos', 'corporativo-autorizados', 'contratos-detalles'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['clientes-corporativos', 'corporativo-autorizados', 'contratos-detalles']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['clientes-corporativos', 'corporativo-autorizados', 'contratos-detalles'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m7.875 14.25 1.214 1.942a2.25 2.25 0 0 0 1.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 0 1 1.872 1.002l.164.246a2.25 2.25 0 0 0 1.872 1.002h2.092a2.25 2.25 0 0 0 1.872-1.002l.164-.246A2.25 2.25 0 0 1 16.954 9h4.636M2.41 9a2.25 2.25 0 0 0-.16.832V12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 0 1 .382-.632l3.285-3.832a2.25 2.25 0 0 1 1.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0 0 21.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Clientes
                                            Corporativos</span>

                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            @can('Ver gestion clientes')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('clientes-corporativos')) {{ '!text-primary' }} @endif"
                                                href="{{ route('clientes-corporativos') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                    Gestion de Clientes</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                            @can('Ver personal autorizado')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('corporativo-autorizados')) {{ '!text-primary' }} @endif"
                                                href="{{ route('corporativo-autorizados') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                    Personal Autorizado</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                            @can('Ver detalle contratos')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('contratos-detalles')) {{ '!text-primary' }} @endif"
                                                href="{{ route('contratos-detalles') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-10white0 duration-200">
                                                    Detalles de Contrato</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                        </li>
                    @endcan

                    @can('Ver clientes corporativos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['apartados-c-n-c'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['clientes-corporativos']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['apartados-c-n-c'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('apartados-c-n-c') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Apartados
                                            CNC</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Exporta facil')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['exporta-facil'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['exporta-facil']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['exporta-facil'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('exporta-facil') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m20.893 13.393-1.135-1.135a2.252 2.252 0 0 1-.421-.585l-1.08-2.16a.414.414 0 0 0-.663-.107.827.827 0 0 1-.812.21l-1.273-.363a.89.89 0 0 0-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.212.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 0 1-1.81 1.025 1.055 1.055 0 0 1-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.655-.261a2.25 2.25 0 0 1-1.383-2.46l.007-.042a2.25 2.25 0 0 1 .29-.787l.09-.15a2.25 2.25 0 0 1 2.37-1.048l1.178.236a1.125 1.125 0 0 0 1.302-.795l.208-.73a1.125 1.125 0 0 0-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 0 1-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 0 1-1.458-1.137l1.411-2.353a2.25 2.25 0 0 0 .286-.76m11.928 9.869A9 9 0 0 0 8.965 3.525m11.928 9.868A9 9 0 1 1 8.965 3.525" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Exporta
                                            Fácil</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Crear envios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['iposplus'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['iposplus']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['iposplus'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('iposplus') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">IposPlus</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan


                    {{-- Módulo de Correspondencia --}}
                    @canany(['Ver Correspondencia-Presidente', 'Ver Correspondencia-Director', 'Ver Correspondencia-Gerente', 'Ver Correspondencia-Analista', 'Ver Correspondencia-Usuario', 'Ver Correspondencia-Admin'])
                        <li
                            class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (Request::segment(1) == 'correspondencia') {{ 'from-white' }} @endif">
                            <a class="block text-white dark:text-white truncate transition @if (Request::segment(1) != 'correspondencia') {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="{{ url('/correspondencia') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (Request::segment(1) == 'correspondencia') {{ 'text-primary' }} @endif">
                                            <!-- Paper protruding -->
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 5h12v7H6V5Z" />
                                            <!-- Lines on paper -->
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8m-8 2h4" />
                                            <!-- Envelope body -->
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9 7 9-7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z" />
                                            <!-- Open flap -->
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-6 9 6" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Correspondencia</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcanany

                    @can('Ver parametros')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), [
                                'ver-tasas',
                                'tarifas',
                                'gestion',
                                'gestion-alianzas',
                                'tarifas-iposplus',
                                'servicios-flota',
                                'precios-insumos',
                                'paises-exporta-facil',
                                'series-y-sellos',
                                'parametros-valijas'
                            ])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['ver-tasas', 'tarifas', 'gestion', 'gestion-alianzas', 'tarifas-iposplus', 'servicios-flota', 'precios-insumos', 'paises-exporta-facil', 'series-y-sellos', 'parametros-valijas']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Envios'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), [
                                                    'ver-tasas',
                                                    'servicios',
                                                    'servicios-internacionales',
                                                    'precios-insumos',
                                                    'países-exporta-facil',
                                                    'series-y-sellos',
                                                    'gestion-alianzas',
                                                    'tarifas-iposplus',
                                                    'parametros-valijas'
                                                ])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Parámetros</span>
                                    </div>

                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            @can('Ver tasas')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('ver-tasa')) {{ '!text-primary' }} @endif"
                                                href="{{ route('ver-tasa') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Tasas</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('tarifas') || Route::is('tarifas-internacionales')) {{ '!text-primary' }} @endif"
                                            href="{{ route('tarifas') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Servicios</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('servicios-flota') || Route::is('servicios-flota')) {{ '!text-primary' }} @endif"
                                            href="{{ route('servicios-flota') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Servicios
                                                Flota</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('precios-insumos')) {{ '!text-primary' }} @endif"
                                            href="{{ route('precios-insumos') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Lista de Insumos
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('paises-exporta-facil')) {{ '!text-primary' }} @endif"
                                            href="{{ route('paises-exporta-facil') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Países exporta fácil
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('series-y-sellos') || Route::is('series-y-sellos')) {{ '!text-primary' }} @endif"
                                            href="{{ route('series-y-sellos') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Filatelia</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['gestion'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('gestion.circuitos_tribunales')) {{ '!text-primary' }} @endif"
                                            href="{{ route('gestion.circuitos_tribunales') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                C. Judicial/Tribunal
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard', 'gestion-alianzas'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('gestion.alianzas')) {{ '!text-primary' }} @endif"
                                            href="{{ route('gestion.alianzas') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Servicios de Alianzas
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['tarifas-iposplus'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('tarifas.index')) {{ '!text-primary' }} @endif"
                                            href="{{ route('tarifas.index') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Tarifas Iposplus
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['dashboard', 'parametros-valijas'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('parametros-valijas')) {{ '!text-primary' }} @endif"
                                            href="{{ route('parametros-valijas') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Opciones de Valijas
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </li>
                    @endcan

                    @can('Ver envios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['encaminamiento'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['encaminamiento']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['encaminamiento'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('encaminamiento.encaminamiento') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6  @if (in_array(Request::segment(1), ['encaminamiento'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Seguimiento
                                        de Paquetes</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan
                    @can('Ver envios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['encaminamiento-valija'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['encaminamiento-valija']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['encaminamiento-valija'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('encaminamiento-valija') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6  @if (in_array(Request::segment(1), ['encaminamiento'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Seguimiento de Valijas</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan
                    @can('Rutas')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['rutas-locales', 'viajes-locales', 'rutas-nacionales', 'viajes-nacionales'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['rutas-locales', 'viajes-locales', 'rutas-nacionales', 'viajes-nacionales']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['rutas-locales', 'viajes-locales', 'rutas-nacionales', 'viajes-nacionales'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" stroke-linejoin="round"
                                            class="size-6 @if (in_array(Request::segment(1), ['rutas-locales', 'viajes-locales', 'rutas-nacionales', 'viajes-nacionales'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path
                                                d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6m-6 -6h15m-6 0v-5" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Rutas
                                            y Viajes</span>
                                    </div>

                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate 
                                            @if (Request::segment(1) == 'ver-rutas-locales' || Route::is('ver-rutas-locales')) {{ '!text-primary' }} @else {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                            href="{{ route('ver-rutas-locales') }}" wire:navigate
                                            @click.prevent="open = !open; sidebarExpanded = true">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Locales
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            @can('Rutas Nacionales')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate 
                                                @if (Request::segment(1) == 'ver-rutas-nacionales' || Route::is('ver-rutas-nacionales')) {{ '!text-primary' }} @else {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                                href="{{ route('ver-rutas-nacionales') }}" wire:navigate
                                                @click.prevent="open = !open; sidebarExpanded = true">
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                    Nacionales
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan  
                        </li>
                    @endcan
                    
                    @can('Ver Incidencias')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['Incidencias'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['Incidencias']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Incidencias'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('Incidencias') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Incidencias</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Control flota')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['Control-Flota'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['Control-Flota']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['tracking'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('flota') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), ['Control-Flota'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">>
                                            <path
                                                d="M19 21H5V22C5 22.5523 4.55228 23 4 23H3C2.44772 23 2 22.5523 2 22V12L4.4174 7.97099C4.77884 7.36858 5.42986 6.99998 6.13238 6.99998H17.8676C18.5701 6.99998 19.2212 7.36858 19.5826 7.97099L22 12V22C22 22.5523 21.5523 23 21 23H20C19.4477 23 19 22.5523 19 22V21ZM20 14H4V19H20V14ZM4.33238 12H19.6676L17.8676 8.99998H6.13238L4.33238 12ZM5.43934 3.43932L6.5 2.37866L7.56066 3.43932C7.83211 3.71077 8 4.08577 8 4.49998C8 5.32841 7.32843 5.99998 6.5 5.99998C5.67157 5.99998 5 5.32841 5 4.49998C5 4.08577 5.16789 3.71077 5.43934 3.43932ZM10.9393 3.43932L12 2.37866L13.0607 3.43932C13.3321 3.71077 13.5 4.08577 13.5 4.49998C13.5 5.32841 12.8284 5.99998 12 5.99998C11.1716 5.99998 10.5 5.32841 10.5 4.49998C10.5 4.08577 10.6679 3.71077 10.9393 3.43932ZM16.4393 3.43932L17.5 2.37866L18.5607 3.43932C18.8321 3.71077 19 4.08577 19 4.49998C19 5.32841 18.3284 5.99998 17.5 5.99998C16.6716 5.99998 16 5.32841 16 4.49998C16 4.08577 16.1679 3.71077 16.4393 3.43932ZM6.5 18C5.67157 18 5 17.3284 5 16.5C5 15.6716 5.67157 15 6.5 15C7.32843 15 8 15.6716 8 16.5C8 17.3284 7.32843 18 6.5 18ZM17.5 18C16.6716 18 16 17.3284 16 16.5C16 15.6716 16.6716 15 17.5 15C18.3284 15 19 15.6716 19 16.5C19 17.3284 18.3284 18 17.5 18Z">
                                            </path>
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Control
                                            de Flota</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @role('Jefe de Aduana')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['Registrar-Entrada', 'servicios', 'servicios-internacionales', 'Registrar-Salida'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['Registrar-Entrada', 'Registrar-Salida', 'servicios-internacionales', 'servicios-flota']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Gestion de Despachos'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), ['ver-tasas', 'servicios', 'Registrar-Entrada'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M7 4V20H17V4H7ZM6 2H18C18.5523 2 19 2.44772 19 3V21C19 21.5523 18.5523 22 18 22H6C5.44772 22 5 21.5523 5 21V3C5 2.44772 5.44772 2 6 2ZM12 17C12.5523 17 13 17.4477 13 18C13 18.5523 12.5523 19 12 19C11.4477 19 11 18.5523 11 18C11 17.4477 11.4477 17 12 17Z">
                                            </path>
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gestion
                                            de Paquetes Aduana</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('entrada-aduana')) {{ '!text-primary' }} @endif"
                                            href="{{ route('entrada-aduana') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                Entrada Aduana</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('salida-aduana')) {{ '!text-primary' }} @endif"
                                            href="{{ route('salida-aduana') }}" wire:navigate>
                                            <span
                                                class="text-sm font-medium lg:opacity-0 ml-4 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar
                                                Salida Aduana</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endrole

                    @role('Jefe de Aduana')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['almacen-aduana'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['almacen-aduana']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['almacen-aduana'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('almacen-aduana') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), ['ver-tasas', 'servicios', 'almacen-aduana'])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Almacen Aduana</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endrole


                    @role('SuperAdmin')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (Request::segment(1) == 'ver-apis') {{ 'from-white' }} @endif"
                            x-data="{ open: {{ Request::segment(1) == 'ver-apis' ? 1 : 0 }} }">

                            <a class="block text-white dark:text-white truncate transition @if (Request::segment(1) != 'ver-apis') {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('ver-apis') }}" wire:navigate>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (Request::segment(1) == 'ver-apis') {{ 'text-white' }} @else {{ 'text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 @if (Request::segment(1) == 'ver-apis') {{ 'text-white' }} @else {{ 'text-white' }} @endif">
                                            APIs
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        {{-- <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['mapavenezuela'])){{ 'from-white' }}@endif" x-data="{ open: {{ in_array(Request::segment(1), ['mapavenezuela']) ? 1 : 0}} }">
                        <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['tracking'])){{ 'hover:text-gray-900 dark:hover:text-white' }}@endif" href="{{ route('mapavenezuela') }}" wire:navigate>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.893 13.393-1.135-1.135a2.252 2.252 0 0 1-.421-.585l-1.08-2.16a.414.414 0 0 0-.663-.107.827.827 0 0 1-.812.21l-1.273-.363a.89.89 0 0 0-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.212.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 0 1-1.81 1.025 1.055 1.055 0 0 1-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.655-.261a2.25 2.25 0 0 1-1.383-2.46l.007-.042a2.25 2.25 0 0 1 .29-.787l.09-.15a2.25 2.25 0 0 1 2.37-1.048l1.178.236a1.125 1.125 0 0 0 1.302-.795l.208-.73a1.125 1.125 0 0 0-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 0 1-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 0 1-1.458-1.137l1.411-2.353a2.25 2.25 0 0 0 .286-.76m11.928 9.869A9 9 0 0 0 8.965 3.525m11.928 9.868A9 9 0 1 1 8.965 3.525" />
                                    </svg>
                                    <span class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Semaforo postal</span>
                                </div>
                            </div>
                        </a>
                    </li> --}}
                    @endrole

                    @can('Ver Insumos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['inventario'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['inventario']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['inventario'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('inventario') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Inventario
                                            de Insumos</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Insumos Usuarios')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['inventario-promotor'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['inventario-promotor']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['inventario'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('inventario-promotor') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Inventario
                                            Promotor</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Inventario General de Insumos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['inventario-general'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['inventario-general']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['inventario-general'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('inventario-general') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Inventario
                                            General de Insumos</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Gastos Operativos')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['gastos-operativos'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['gastos-operativos']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['gastos-operativos'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('gastos-operativos') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gastos
                                            Operativos</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['gastos-servicios'])) {{ 'from-white' }} @endif"
                        x-data="{ open: {{ in_array(Request::segment(1), ['gastos-servicios']) ? 1 : 0 }} }">
                        <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['gastos-servicios'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                            href="{{ route('gastos-servicios') }}" wire:navigate>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                                    </svg>
                                    <span
                                        class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pagos
                                        de servicios</span>
                                </div>
                            </div>
                        </a>
                    </li>

                    @can('Ver Reportes Presidencia')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['reportes-presidencia'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['reportes-presidencia']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['reportes-presidencia'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('reportes-presidencia') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Reportes
                                            Presidencia</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Gastos Arrendamiento')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), ['arrendamiento-oficina'])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['arrendamiento-oficina']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['inventario'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="{{ route('arrendamiento-oficina') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                                        </svg>
                                        <span
                                            class="text-sm text-white font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gastos
                                            de Arrendamiento</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endcan

                    @can('Ver Talento Humano')
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] @if (in_array(Request::segment(1), [
                                'registro-empleado',

                            ])) {{ 'from-white' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['registro-empleado']) ? 1 : 0 }} }">
                            <a class="block text-white dark:text-white truncate transition @if (!in_array(Request::segment(1), ['Envios'])) {{ 'hover:text-black dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-6 @if (in_array(Request::segment(1), [
                                                    'registro-empleado',
                                
                                                ])) {{ 'text-primary' }}@else{{ 'text-white dark:text-white' }} @endif">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                        </svg>

                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Talento Humano
                                        </span>
                                    </div>

                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-white dark:text-white @if (in_array(Request::segment(1), ['utility'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            
                            @can('Ver Registro de Empleados')
                                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                    <ul class="pl-4 mt-1 @if (!in_array(Request::segment(1), ['utility'])) {{ 'hidden' }} @endif"
                                        :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-white dark:text-white hover:text-white dark:hover:text-white transition truncate @if (Route::is('registro-empleado')) {{ '!text-primary' }} @endif"
                                                href="{{ route('registro-empleado') }}" wire:navigate>
                                                <span
                                                    class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Registrar Empleados</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                        </li>
                    @endcan

                </ul>
            </div>


        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="w-12 pl-4 pr-3 py-2">
                <button
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors"
                    @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="shrink-0 fill-current text-primary dark:text-black sidebar-expanded:rotate-180"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M15 16a1 1 0 0 1-1-1V1a1 1 0 1 1 2 0v14a1 1 0 0 1-1 1ZM8.586 7H1a1 1 0 1 0 0 2h7.586l-2.793 2.793a1 1 0 1 0 1.414 1.414l4.5-4.5A.997.997 0 0 0 12 8.01M11.924 7.617a.997.997 0 0 0-.217-.324l-4.5-4.5a1 1 0 0 0-1.414 1.414L8.586 7M12 7.99a.996.996 0 0 0-.076-.373Z" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
