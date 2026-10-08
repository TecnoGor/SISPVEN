@can('Ver estadisticas')
    @section('titulo')
        Gestion
    @endsection

    <x-app-layout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            <!-- Dashboard actions -->
            <div class="sm:flex sm:justify-between sm:items-center mb-8">
                <!-- Left: Title -->
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-primary font-bold">Gestión</h1>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex flex-wrap gap-4 items-end w-full max-w-4xl"
                id="filtros-dashboard">
                <div class="flex-1 min-w-[180px]">
                    <label for="estado_id" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por estado</label>
                    @if(count($estados) === 1)
                        <select id="estado_id" class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed text-gray-600 font-semibold" disabled>
                            <option value="{{ $estados->first()->estado_id }}" selected>
                                {{ $estados->first()->nombre }}
                            </option>
                        </select>
                        <input type="hidden" name="estado_id" value="{{ $estados->first()->estado_id }}">
                    @else
                        <select name="estado_id" id="estado_id" class="block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos los estados</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->estado_id }}"
                                    {{ request('estado_id') == $estado->estado_id || $estadoId == $estado->estado_id ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="flex-1 min-w-[220px]">
                    <label for="oficina_id" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por oficina</label>
                    <select name="oficina_id" id="oficina_id" class="block w-full border-gray-300 rounded-md shadow-sm"
                        {{ request('estado_id') ? '' : 'disabled' }}>
                        <option value="">Todas las oficinas</option>
                        @foreach ($oficinas as $oficina)
                            <option value="{{ $oficina->oficina_id }}" data-estado="{{ $oficina->estado_id }}"
                                data-tipo="{{ $oficina->tipo_oficina_id }}" style="display:none"
                                {{ request('oficina_id') == $oficina->oficina_id ? 'selected' : '' }}>
                                {{ $oficina->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-1 gap-4 min-w-[300px]">
                    <div class="flex-1">
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            value="{{ $fechaInicioStr ?? request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}"
                            class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50">
                    </div>
                    <div class="flex-1">
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                            value="{{ $fechaFinStr ?? request('fecha_fin', now()->endOfMonth()->format('Y-m-d')) }}"
                            class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50">
                    </div>
                </div>
            </form>

            <!-- Cards -->
            <div class="grid grid-cols-12 gap-6">
                <x-dashboard.dashboard-card-01 :dataFeed="$dataFeed" />
                <x-dashboard.dashboard-card-14 :dataFeed="$dataFeed" />
                <x-dashboard.dashboard-card-02 :dataFeed="$dataFeed" />
                <x-dashboard.dashboard-card-15 :dataFeed="$dataFeed" />
                <x-dashboard.dashboard-card-07 :dataFeed="$dataFeed" />
                @can('Ver Semáforo Postal')
                    <x-dashboard.dashboard-card-12 :dataFeed="$dataFeed" />
                @endcan
                <x-dashboard.dashboard-card-09 />
                <x-dashboard.dashboard-card-10 />
                <x-dashboard.dashboard-card-16 />
                {{-- Tarjeta "Margen de Beneficio" ocultada temporalmente hasta definir la fórmula de cálculo. No eliminar: volver a habilitar cuando se confirme el método. --}}
                {{-- <x-dashboard.dashboard-card-13 :dataFeed="$dataFeed" /> --}}
            </div>
        </div>
        @can('Ver Semáforo Postal')
            <livewire:mapa.venezuela-mapa />
        @endcan
    </x-app-layout>

    <script>
        // Datos pre-calculados con filtros aplicados desde el backend
        window.dashboardChartData = {
            envios: {!! $chartEnvios !!},
            ingresos: {!! $chartIngresos !!},
            serviciosEnvios: {!! $chartServiciosEnvios !!},
            serviciosIngresos: {!! $chartServiciosIngresos !!},
            margen: {!! $chartMargen !!},
            entregados: {!! $chartEntregados !!},
            devoluciones: {!! $chartDevoluciones !!},
            ingresosEntregados: {!! $chartIngresosEntregados !!},
            enviosPorEstado: {!! $chartEnviosPorEstado !!}
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estadoSelect = document.getElementById('estado_id');
            const oficinaSelect = document.getElementById('oficina_id');
            const form = document.getElementById('filtros-dashboard');
            const allOptions = Array.from(oficinaSelect.options);

            function filtrarOficinas() {
                const estadoId = estadoSelect.value;
                allOptions.forEach(option => {
                    if (!option.value) {
                        option.style.display = '';
                        return;
                    }
                    const tipo = Number(option.getAttribute('data-tipo'));
                    if (estadoId && option.getAttribute('data-estado') == estadoId && [1, 2, 3].includes(
                            tipo)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                oficinaSelect.disabled = !estadoId;
                if (!estadoId) {
                    oficinaSelect.value = '';
                }
            }

            filtrarOficinas();

            estadoSelect.addEventListener('change', function() {
                oficinaSelect.value = '';
                filtrarOficinas();
                form.submit(); // Enviar el formulario automáticamente al cambiar estado
            });

            oficinaSelect.addEventListener('change', function() {
                form.submit(); // Enviar el formulario automáticamente al cambiar oficina
            });

            document.getElementById('fecha_inicio').addEventListener('change', function() {
                form.submit(); // Enviar el formulario automáticamente al cambiar fecha de inicio
            });

            document.getElementById('fecha_fin').addEventListener('change', function() {
                form.submit(); // Enviar el formulario automáticamente al cambiar fecha de fin
            });
        });
    </script>

@endcan

@cannot('Ver estadisticas')
    @section('titulo')
        Bienvenido
    @endsection
    <x-app-layout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

            <div class="bg-gradient-to-r from-primary via-primary to-secondary rounded-lg p-8 text-white shadow-lg">
                <h1 class="text-4xl font-bold mb-4 text-center">¡Bienvenido a SISPVEN!</h1>
                <p class="text-lg text-center">
                    ¡Bienvenido! Explora las opciones disponibles adaptadas específicamente a tu rol asignado y aprovecha al
                    máximo las herramientas del sistema.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta 1 -->
                @can('Rutas')
                    <a href="{{ route('ver-rutas-locales') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Gestión de Rutas Locales</h2>
                            <p class="text-sm text-gray-500">Administra las rutas locales de manera eficiente con nuestras
                                herramientas.</p>
                        </div>
                    </a>
                @endcan

                @can('Control flota')
                    <a href="{{ route('flota') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Gestion de Flotas</h2>
                            <p class="text-sm text-gray-500">Administra las los vehiculos de tu oficinas de una formas mas
                                organizada y eficiente</p>
                        </div>
                    </a>
                @endcan

                @can('Rutas Nacionales')
                    <a href="{{ route('ver-rutas-nacionales') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Gestión de Rutas Nacionales</h2>
                            <p class="text-sm text-gray-500">Administra las rutas nacionales de manera eficiente con nuestras
                                herramientas.</p>
                        </div>
                    </a>
                @endcan

                @can('Ver servicioss')
                    <a href="{{ route('tarifas') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Servicios</h2>
                            <p class="text-sm text-gray-500">Consulta y evalua los diferentes servicios disponibles</p>
                        </div>
                    </a>
                @endcan

                @can('Ver usuarios')
                    <a href="{{ route('cuentas') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Usuarios</h2>
                            <p class="text-sm text-gray-500">Consulta de usuarios para poder gestionar los mismos</p>
                        </div>
                    </a>
                @endcan

                @can('Ver Roles')
                    <a href="{{ route('roles-mostrar') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Roles</h2>
                            <p class="text-sm text-gray-500">Administra los roles junto a sus permisos de una manera mas
                                eficiente</p>
                        </div>
                    </a>
                @endcan

                @can('Ver Oficinas-admin')
                    <a href="{{ route('oficinas-mostrar') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Oficinas</h2>
                            <p class="text-sm text-gray-500">Visualiza y Administra las oficinas</p>
                        </div>
                    </a>
                @endcan

                @can('Ver Mi oficina')
                    <a href="{{ route('mi-oficina') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Mi Oficina</h2>
                            <p class="text-sm text-gray-500">Ve detalles de la oficina a la cual perteneces</p>
                        </div>
                    </a>
                @endcan

                @can('Ver envio')
                    <a href="{{ route('envios-form') }}"
                        class="bg-white rounded-lg shadow p-6 flex items-center space-x-4 transition transform hover:scale-105 hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h11M9 21H7m0-10v10m12-10h2m-2 0h2M7 10h6m5 5H7m0 5h6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700">Envios</h2>
                            <p class="text-sm text-gray-500">Entra a los Formularios para realizar envios</p>
                        </div>
                    </a>
                @endcan
            </div>
        </div>
    </x-app-layout>
@endcannot
