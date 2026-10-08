<div>
    <div>
        @section('titulo')
            Paquetes Disponibles
        @endsection
        @php
            $usuario = auth()->user();
            $oficinaId = $usuario->oficina_id; // Obtener la oficina_id asociada al usuario
            // Consultar la información de la oficina con esa oficina_id
            $oficina = \App\Models\Oficina::where('oficina_id', $oficinaId)->first();
        @endphp

        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Paquetes Disponibles en
                        {{ $oficina->nombre }}</h1>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 max-w-full mx-auto">
                {{-- CONTENEDOR PRINCIPAL --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

                    {{-- BARRA SUPERIOR: BUSCADOR Y ACCIONES --}}
                    <div class="mt-2 mb-6 w-full">
                        {{-- Contenedor principal — apila en móvil, en línea en pantallas grandes --}}
                        <div class="flex flex-col xl:flex-row xl:flex-wrap xl:items-center xl:justify-between gap-4">

                            {{-- Grupo Izquierdo: Buscador, Tabs y Select --}}
                            <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto xl:flex-1">

                                {{-- Búsqueda --}}
                                <div class="relative w-full sm:w-72 sm:max-w-full">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor"
                                            viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 transition duration-150"
                                        placeholder="Buscar envío...">
                                </div>


                                {{-- Filtro de Estatus (Tabs) --}}
                                <div class="inline-flex p-1 bg-gray-100 rounded-lg border border-gray-200 shrink-0">
                                    <button wire:click="$set('filterStatus', true)"
                                        class="px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 {{ $filterStatus ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                        DISPONIBLES
                                    </button>
                                    <button wire:click="$set('filterStatus', false)"
                                        class="px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 {{ !$filterStatus ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                        ENTREGADOS
                                    </button>
                                </div>

                                {{-- Select --}}
                                <select id="devolucion" wire:model.live="devoluciones"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 pl-3 pr-10 transition duration-150 cursor-pointer w-full sm:w-auto">
                                    <option value="0">Todos los envíos</option>
                                    <option value="1">Solo devoluciones</option>
                                </select>

                                {{-- Select Origen: creados aquí vs recibidos desde otra OPT --}}
                                <select id="origen" wire:model.live="origen"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary py-2.5 pl-3 pr-10 transition duration-150 cursor-pointer w-full sm:w-auto">
                                    <option value="0">Todos los orígenes</option>
                                    <option value="1">Creados en esta oficina</option>
                                    <option value="2">Recibidos desde otra OPT</option>
                                </select>
                            </div>

                            {{-- Grupo Derecho: Botones de Acción --}}
                            <div class="flex flex-wrap items-center gap-2 w-full xl:w-auto">
                                @can('Entrega en Agencia')
                                    @if($filterStatus && in_array($tipoOficinaId, [1, 2, 3, 4, 5]))
                                        <button type="button" wire:click="entregarSeleccionados"
                                            @disabled(count($seleccionados) === 0)
                                            class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 h-10 rounded-lg text-sm font-semibold text-white transition w-full sm:w-auto
                                                {{ count($seleccionados) === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-700 hover:bg-emerald-800' }}">
                                            Entregar seleccionados
                                            <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ count($seleccionados) }}</span>
                                        </button>
                                    @endif
                                @endcan

                                @if($filterStatus && !empty($this->salasPermitidas))
                                    <div class="relative w-full sm:w-64">
                                        <input type="text"
                                            wire:model="codigo_lote"
                                            wire:keydown.enter.prevent="agregarPorCodigo"
                                            placeholder="Escanear o escribir código..."
                                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block h-10 px-3 w-full">
                                        @if($mensaje_codigo)
                                            <span class="absolute left-0 top-full mt-1 text-[11px] font-medium whitespace-nowrap
                                                {{ $mensaje_codigo_tipo === 'success' ? 'text-green-600' : '' }}
                                                {{ $mensaje_codigo_tipo === 'error' ? 'text-red-600' : '' }}
                                                {{ $mensaje_codigo_tipo === 'warning' ? 'text-amber-600' : '' }}">
                                                {{ $mensaje_codigo }}
                                            </span>
                                        @endif
                                    </div>
                                    @can('Enviar a Sala')
                                        <button type="button" wire:click="abrirModalSala"
                                            @disabled(count($seleccionados) === 0)
                                            class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 h-10 rounded-lg text-sm font-semibold text-white transition w-full sm:w-auto
                                                {{ count($seleccionados) === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                            Enviar a...
                                            <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ count($seleccionados) }}</span>
                                        </button>
                                    @endcan
                                @endif

                                @if($pendientesAperturaCount > 0)
                                    <button type="button" wire:click="abrirModalApertura"
                                        class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 h-10 rounded-lg text-sm font-semibold text-white transition w-full sm:w-auto bg-amber-600 hover:bg-amber-700">
                                        Por aceptar
                                        <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ $pendientesAperturaCount }}</span>
                                    </button>
                                @endif

                                <x-button class="whitespace-nowrap shadow-sm h-10 w-full sm:w-auto" wire:click="exportToExcel"
                                    :disabled="count($envios) === 0">
                                    Exportar Excel
                                </x-button>
                                <x-button class="whitespace-nowrap shadow-sm h-10 w-full sm:w-auto" wire:click="generateEnviosPDF"
                                    :disabled="count($envios) === 0">
                                    Imprimir PDF
                                </x-button>
                                <a href="{{ route('repartidores.index') }}" wire:navigate class="w-full sm:w-auto">
                                    <x-primary-button class="shadow-sm whitespace-nowrap h-10 w-full sm:w-auto justify-center">
                                        Ver Repartidores
                                    </x-primary-button>
                                </a>
                            </div>

                        </div>
                    </div>

                    {{-- TABLA DE RESULTADOS --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-[45vh] lg:max-h-[50vh]">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    {{-- La condición debe ser IDÉNTICA a la del <td> del checkbox
                                         (más abajo en el <tbody>): si difieren, un rol que cumpla
                                         una y no la otra ve la fila corrida respecto al encabezado. --}}
                                    @canany(['Entrega en Agencia', 'Enviar a Sala'])
                                        @if($filterStatus && in_array($tipoOficinaId, [1, 2, 3, 4, 5]))
                                            <th scope="col" class="px-3 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-10">
                                                <span class="sr-only">Seleccionar</span>
                                            </th>
                                        @endif
                                    @endcanany
                                    @include('livewire.includes.sort-table', [
                                        'column' => 'codigo',
                                        'displayName' => 'N° Envío',
                                    ])
                                    @include('livewire.includes.sort-table', [
                                        'column' => 'codigo',
                                        'displayName' => 'Usuario',
                                    ])
                                    @include('livewire.includes.sort-table', [
                                        'column' => 'codigo',
                                        'displayName' => 'Contenido',
                                    ])
                                    @include('livewire.includes.sort-table', [
                                        'column' => 'codigo',
                                        'displayName' => 'Peso',
                                    ])
                                    @include('livewire.includes.sort-table', [
                                        'column' => 'codigo',
                                        'displayName' => 'Tipo',
                                    ])

                                    @if ($filterStatus)
                                        @include('livewire.includes.sort-table', [
                                            'column' => 'proveedor',
                                            'displayName' => 'Estatus',
                                        ])
                                        @include('livewire.includes.sort-table', [
                                            'column' => 'proveedor',
                                            'displayName' => 'Almacenado',
                                        ])
                                        @include('livewire.includes.sort-table', [
                                            'column' => 'proveedor',
                                            'displayName' => 'Entrada',
                                        ])
                                        <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Servicio</th>
                                        <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                                    @else
                                        <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Servicio</th>
                                        <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Salida</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200" wire:poll.60s>
                                @forelse ($envios as $envio_almacen)
                                    <tr wire:key="{{ $envio_almacen->envio_almacen_id }}"
                                        @class([
                                            'transition duration-50',
                                            'hover:bg-gray-50' => !in_array($envio_almacen->envio_almacen_id, $seleccionados),
                                            'bg-emerald-50 hover:bg-emerald-100 shadow-[inset_4px_0_0_0_theme(colors.emerald.600)]' => in_array($envio_almacen->envio_almacen_id, $seleccionados),
                                        ])>
                                        @canany(['Entrega en Agencia', 'Enviar a Sala'])
                                            @if($filterStatus && in_array($tipoOficinaId, [1, 2, 3, 4, 5]))
                                                <td class="px-3 py-3 text-center">
                                                    <label class="inline-flex items-center justify-center p-1.5 rounded-lg cursor-pointer hover:bg-emerald-50 transition">
                                                        <input type="checkbox"
                                                            wire:model.live="seleccionados"
                                                            value="{{ $envio_almacen->envio_almacen_id }}"
                                                            class="h-6 w-6 rounded-md border-[3px] border-gray-700 text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 cursor-pointer transition
                                                            checked:bg-emerald-600 checked:border-emerald-600 hover:border-emerald-500">
                                                    </label>
                                                </td>
                                            @endif
                                        @endcanany
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $envio_almacen->envio->codigo_envio }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $envio_almacen->envio->users->name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $envio_almacen->envio->contenido }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $envio_almacen->envio->peso }}gr</td>
                                        <td class="px-4 py-3 text-gray-700 uppercase text-xs">
                                            {{ $envio_almacen->envio->tipo_envio }}</td>

                                        @if ($filterStatus)
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">Por
                                                    entregar</span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 text-xs italic">
                                                {{ \Carbon\Carbon::parse($envio_almacen->Entrada)->diffInDays(now()) }}
                                                días
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ \Carbon\Carbon::parse($envio_almacen->Entrada)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ optional(optional($envio_almacen->envio)->servicio)->nombre ?? 'No disponible' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                <div class="flex items-center justify-center space-x-3">
                                                    {{-- Botón Asignar --}}
                                                    <button
                                                        wire:click="abrirModalAsignar({{ $envio_almacen->envio_almacen_id }})" title="Asignar Repartidor"
                                                        class="p-1.5 rounded-lg transition duration-200 transform hover:scale-110 shadow-sm {{ $envio_almacen->carteros->isNotEmpty() ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                                                        @if ($envio_almacen->carteros->isNotEmpty())
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.66-1.546 9.974 9.974 0 00-6.66-.546 9.974 9.974 0 00-6 2.202z" />
                                                            </svg>
                                                        @endif
                                                    </button>
                                                    
                                                    {{-- Intento de Entrega / Fallido --}}
                                                    @if (in_array($tipoOficinaId, [1, 2, 3]))
                                                    <button wire:click="modal_intento_entrega({{ $envio_almacen->envio_almacen_id }})" type="button" class="text-orange-600 hover:text-orange-800 transition-colors transform hover:scale-110" title="Informar Intento Fallido de Entrega">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mx-auto">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </button>
                                                    @endif

                                                    @can('Entrega en Agencia')
                                                        @if (in_array($tipoOficinaId, [1, 2, 3, 4, 5]))
                                                            <button type="button"
                                                                wire:click="mostrar({{ $envio_almacen->envio_almacen_id }})" title="Agregar Aviso de llegada"
                                                                class="text-gray-500 hover:text-red-700 transition transform hover:scale-110">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                    class="size-6 mx-auto">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @else
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ optional(optional($envio_almacen->envio)->servicio)->nombre ?? 'No disponible' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ \Carbon\Carbon::parse($envio_almacen->Salida)->format('d/m/Y') }}
                                            </td>
                                        @endif
                                    </tr>

                                @empty
                                    <tr class="border-b text-center">
                                        <td colspan="10" class="py-7 text-gray-500 text-lg">No hay envíos</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- PAGINACIÓN AL ESTILO EXPORTA FÁCIL --}}
            <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                    <select wire:model.live="perPage"
                        class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value="30">30</option>
                        <option value="35">35</option>
                    </select>
                </div>
                <div>
                    {{ $envios->links() }}
                </div>
            </div>

            {{-- MODAL AVISO LLEGADA --}}
            @if ($mostrar_modal)
                <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div
                        class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                        <div
                            class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden transition-all transform scale-100">
                            <div class="p-8 text-center">
                                <div
                                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800 mb-2">¿Desea registrar un nuevo Aviso de
                                    Llegada para este envío?</h2>
                                <div class="mb-4 inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-sm">
                                    <span class="text-sm font-medium text-blue-700">Avisos enviados:</span>
                                    <span class="ml-2 text-lg font-bold text-blue-900">{{ $cantidad_avisos }}/8</span>
                                </div>
                                <div class="flex justify-center gap-4 mt-6">
                                    <button wire:click="avisos"
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold shadow-md">
                                        SÍ, REGISTRAR
                                    </button>
                                    <button wire:click="cerrar"
                                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-bold">
                                        NO, CANCELAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- MODAL AVISO LLEGADA --}}
            @if ($intento)
                <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div
                        class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                        <div
                            class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden transition-all transform scale-100">
                            <div class="p-8 text-center">
                                <div
                                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800 mb-2">¿Desea registrar un nuevo Intento de
                                    Entrega para este envío?</h2>
                                <div class="mb-4 inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-sm">
                                    <span class="text-sm font-medium text-blue-700">Intentos enviados:</span>
                                    <span class="ml-2 text-lg font-bold text-blue-900">{{ $cantidad_intentos }}/3</span>
                                </div>
                                <div class="flex justify-center gap-4 mt-6">
                                    <button wire:click="intento_entrega"
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold shadow-md">
                                        SÍ, REGISTRAR
                                    </button>
                                    <button wire:click="cerrar"
                                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-bold">
                                        NO, CANCELAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- MODAL ASIGNAR CARTERO --}}
            @if ($mostrar_modal_asignar)
                <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div
                        class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                        <div
                            class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden transition-all transform scale-100">

                            {{-- Header Modal --}}
                            <div
                                class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                                <div class="flex items-center text-[#6b1820]">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 0v4m0-4h4m-4 0H8" class="opacity-0" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 13v6m-3-3h6" />
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-800">
                                        {{ $solo_visualizar ? 'Detalle de Asignación' : 'Asignar Repartidor' }}
                                    </h3>
                                </div>
                                <button type="button" wire:click="cerrarModalAsignar"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body Modal --}}
                            <div class="p-6 space-y-5">
                                @if ($selected_envio)
                                    <div class="space-y-4 text-sm">
                                        <div class="flex justify-between border-b border-gray-50 pb-2">
                                            <span
                                                class="text-gray-500 font-medium uppercase tracking-wider text-xs">Número
                                                de Envío:</span>
                                            <span
                                                class="font-bold text-gray-800">{{ $selected_envio->envio->codigo_envio }}</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-50 pb-2">
                                            <span
                                                class="text-gray-500 font-medium uppercase tracking-wider text-xs">Usuario:</span>
                                            <span
                                                class="text-gray-800">{{ $selected_envio->envio->users->name }}</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-50 pb-2">
                                            <span
                                                class="text-gray-500 font-medium uppercase tracking-wider text-xs">Peso
                                                / Tipo:</span>
                                            <span class="text-gray-800 font-medium">{{ $selected_envio->envio->peso }}
                                                gr | {{ $selected_envio->envio->tipo_envio }}</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-50 pb-2">
                                            <span
                                                class="text-gray-500 font-medium uppercase tracking-wider text-xs">Estatus:</span>
                                            <span
                                                class="{{ $selected_envio->estatus ? 'text-green-600' : 'text-gray-500' }} font-bold">
                                                {{ $selected_envio->estatus ? 'POR ENTREGAR' : 'ENTREGADO' }}
                                            </span>
                                        </div>


                                        <div class="mt-6">
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar
                                                Repartidor:</label>
                                            @if ($solo_visualizar)
                                                <div
                                                    class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 font-bold text-center">
                                                    {{ $nombre_cartero ?? 'No asignado' }}
                                                </div>
                                            @else
                                                {{-- Select ajustado al color corporativo --}}
                                                <select wire:model.live="cartero_seleccionado"
                                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820] px-3 py-2.5 transition duration-150">
                                                    <option value="" disabled selected>-- Seleccione un
                                                        repartidor --</option>
                                                    @foreach ($carteros as $cartero)
                                                        <option value="{{ $cartero->id }}">{{ $cartero->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Modal con el diseño solicitado --}}
                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                                @unless ($solo_visualizar)
                                    <x-button class="bg-[#6b1820] hover:bg-[#7b1f27] text-white"
                                        wire:click="asignarCartero" wire:loading.attr="disabled"
                                        wire:target="asignarCartero">
                                        Confirmar Asignación
                                    </x-button>
                                @endunless

                                <x-button class="bg-gray-200 text-white hover:bg-gray-300"
                                    wire:click="cerrarModalAsignar">
                                    {{ $solo_visualizar ? 'Volver' : 'Cancelar' }}
                                </x-button>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal: Enviar a sala --}}
            @if ($mostrar_modal_sala)
                <div class="fixed inset-0 bg-gray-900 bg-opacity-60 flex justify-center items-center z-50 backdrop-blur-sm">
                    <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-md w-full">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Enviar a...</h2>
                        <p class="text-sm text-gray-500 mb-4">
                            Se enviarán <span class="font-semibold text-gray-800">{{ count($seleccionados) }}</span> envío(s) al departamento seleccionado. Una vez confirmado, dejarán de estar disponibles en el almacén.
                        </p>

                        <div class="space-y-2 mb-6">
                            @foreach($this->salasPermitidas as $codigo => $sala)
                                @php
                                    // Expedición usa estatus_id dinámico (null por diseño); las demás requieren estatus_id fijo.
                                    $salaHabilitada = $sala['estatus_id'] !== null || $codigo === 'expedicion';
                                @endphp
                                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                                    {{ !$salaHabilitada ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-indigo-50 border-gray-200' }}
                                    {{ $sala_destino === $codigo ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                    <input type="radio"
                                        wire:model.live="sala_destino"
                                        value="{{ $codigo }}"
                                        @disabled(!$salaHabilitada)
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">{{ $sala['nombre'] }}</span>
                                    @if(!$salaHabilitada)
                                        <span class="ml-auto text-[10px] uppercase font-bold text-gray-400">Próximamente</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="cerrarModalSala"
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium transition">
                                Cancelar
                            </button>
                            <button type="button" wire:click="enviarASala"
                                @disabled(!$sala_destino)
                                class="px-4 py-2 rounded-lg text-white text-sm font-medium transition
                                    {{ !$sala_destino ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                Confirmar envío
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal: Bandeja de aceptación de Apertura --}}
            @if ($mostrar_modal_apertura)
                <div class="fixed inset-0 bg-gray-900 bg-opacity-60 flex justify-center items-center z-50 backdrop-blur-sm p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800">Envíos por aceptar — Apertura</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Envíos internacionales devueltos desde la Unidad de Análisis de Devolución. Al aceptar, entrarán al almacén con estatus "Entrada en Apertura".
                            </p>
                        </div>

                        <div class="overflow-y-auto flex-1 px-6 py-4">
                            @if ($pendientesApertura->isEmpty())
                                <div class="text-center py-12 text-gray-500 italic">
                                    No hay envíos pendientes de aceptación.
                                </div>
                            @else
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-3 text-center w-10">
                                                <span class="sr-only">Seleccionar</span>
                                            </th>
                                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Código</th>
                                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Servicio</th>
                                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Remitente</th>
                                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Destinatario</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($pendientesApertura as $envio)
                                            <tr wire:key="apertura-{{ $envio->envio_id }}" class="hover:bg-gray-50 transition">
                                                <td class="px-3 py-3 text-center">
                                                    <input type="checkbox"
                                                        wire:model.live="seleccionados_apertura"
                                                        value="{{ $envio->envio_id }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                                </td>
                                                <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">
                                                    {{ $envio->codigo_envio ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ $envio->servicio?->nombre ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ trim(($envio->nombre_rem ?? '') . ' ' . ($envio->apellido_rem ?? '')) ?: '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ trim(($envio->nombre_dest ?? '') . ' ' . ($envio->apellido_dest ?? '')) ?: '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                            <button type="button" wire:click="cerrarModalApertura"
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium transition">
                                Cancelar
                            </button>
                            <button type="button" wire:click="aceptarApertura"
                                @disabled(count($seleccionados_apertura) === 0)
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-medium transition
                                    {{ count($seleccionados_apertura) === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-amber-600 hover:bg-amber-700' }}">
                                Aceptar envíos
                                <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ count($seleccionados_apertura) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
        @script
            <script>
                // success alert
                Livewire.on('alertSuccess', message => {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: message.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                })

                    Livewire.on('alertSuccess2', message => {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: message.message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    })

                    Livewire.on('alertError', message => {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: message.message,
                            showConfirmButton: false,
                            timer: 3500
                        });
                    })
            </script>
        @endscript

    <script>
        function formatCurrency(input) {
            // Eliminar caracteres no numéricos
            let value = input.value.replace(/[^0-9]/g, '');

            // Convertir a número y formatear
            if (value.length === 0) {
                input.value = '0,00';
                return;
            }

            // Convertir a centimos
            let cents = parseInt(value, 10);

            // Formatear a bs y centimos
            let bs = Math.floor(cents / 100);
            let formattedCents = (cents % 100).toString().padStart(2, '0');

            // // Agregar separador de miles
            let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Actualizar el valor del input
            input.value = `${formattedbs},${formattedCents}`;
            console.log('entro');

        }
    </script>
@endpush
