<div>
    <div>
        @section('titulo')
            Almacén de Rezago
        @endsection
        @php
            $usuario = auth()->user();
            $oficinaId = $usuario->oficina_id;
            $oficina = \App\Models\Oficina::where('oficina_id', $oficinaId)->first();
        @endphp

        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
                        Almacén de Rezago - {{ $oficina->nombre }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $vista === 'por_aceptar'
                            ? 'Envíos enviados desde otras salas, pendientes de aceptación en rezago.'
                            : 'Envíos actualmente en el almacén de rezago.' }}
                    </p>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 max-w-full mx-auto">
                {{-- CONTENEDOR PRINCIPAL --}}
                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

                    {{-- BARRA SUPERIOR: tabs + botón aceptar + buscador --}}
                    <div class="mt-2 mb-6 w-full flex flex-col gap-3">

                        {{-- FILA 1: Tabs + botón aceptar + buscador --}}
                        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3">

                            {{-- Tabs --}}
                            <div class="inline-flex h-11 p-1 bg-gray-100 rounded-lg border border-gray-200 items-center w-full sm:w-auto">
                                <button type="button" wire:click="setVista('por_aceptar')"
                                    class="px-3 sm:px-4 h-9 text-xs font-bold rounded-md transition duration-200 whitespace-nowrap flex-1 sm:flex-none
                                        {{ $vista === 'por_aceptar' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    POR ACEPTAR
                                </button>
                                <button type="button" wire:click="setVista('en_rezago')"
                                    class="px-3 sm:px-4 h-9 text-xs font-bold rounded-md transition duration-200 whitespace-nowrap flex-1 sm:flex-none
                                        {{ $vista === 'en_rezago' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    EN REZAGO
                                </button>
                            </div>

                            {{-- Botón aceptar (solo POR ACEPTAR) --}}
                            @if($vista === 'por_aceptar')
                                <button type="button" wire:click="aceptarSeleccionados"
                                    @disabled(count($seleccionados_aceptar) === 0)
                                    class="whitespace-nowrap shadow-sm h-11 inline-flex items-center justify-center gap-2 px-4 rounded-lg text-sm font-semibold text-white transition w-full sm:w-auto
                                        {{ count($seleccionados_aceptar) === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}">
                                    Aceptar envíos
                                    <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ count($seleccionados_aceptar) }}</span>
                                </button>
                            @endif

                            {{-- Buscador --}}
                            <div class="relative w-full sm:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full h-11 pl-10 transition duration-150"
                                    placeholder="Buscar por código...">
                            </div>

                            {{-- Botones de reportes (solo EN REZAGO, alineados a la derecha en desktop) --}}
                            @if($vista === 'en_rezago')
                                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto sm:ml-auto">
                                    <x-button class="whitespace-nowrap shadow-sm h-11 w-full sm:w-auto"
                                        wire:click="reporteEstan"
                                        title="Paquetes que actualmente están en el almacén de rezago">
                                        Inventario Actual
                                    </x-button>
                                    <x-button class="whitespace-nowrap shadow-sm h-11 w-full sm:w-auto"
                                        wire:click="reporteLlevanTiempo"
                                        title="Paquetes con más permanencia en el almacén">
                                        Antigüedad
                                    </x-button>
                                </div>
                            @endif
                        </div>

                        {{-- FILA 2: Filtros de EN REZAGO (servicio, fechas, días) --}}
                        @if($vista === 'en_rezago')
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end gap-3">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Servicio</label>
                                    <select wire:model.live="servicio_filtro"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary h-11 pl-3 pr-10 cursor-pointer w-full lg:w-56">
                                        <option value="">Todos los servicios</option>
                                        @foreach ($servicios as $servicio)
                                            <option value="{{ $servicio->servicio_id }}">{{ $servicio->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Entrada desde</label>
                                    <input type="date" wire:model.live="desde"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary h-11 px-3 w-full lg:w-44">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Entrada hasta</label>
                                    <input type="date" wire:model.live="hasta"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary h-11 px-3 w-full lg:w-44">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Llevan al menos (días)</label>
                                    <input type="number" min="0" wire:model.live.debounce.500ms="dias_minimo"
                                        placeholder="Ej: 30"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary h-11 px-3 w-full lg:w-40">
                                </div>

                                @if($search || $servicio_filtro || $desde || $hasta || $dias_minimo !== '')
                                    <button type="button" wire:click="limpiarFiltros"
                                        class="h-11 text-xs font-bold text-gray-500 hover:text-primary uppercase tracking-widest underline justify-self-start">
                                        Limpiar
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Filtro de servicio en POR ACEPTAR --}}
                        @if($vista === 'por_aceptar')
                            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3">
                                <div class="w-full sm:w-auto">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Servicio</label>
                                    <select wire:model.live="servicio_filtro"
                                        class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary h-11 pl-3 pr-10 cursor-pointer w-full sm:w-56">
                                        <option value="">Todos los servicios</option>
                                        @foreach ($servicios as $servicio)
                                            <option value="{{ $servicio->servicio_id }}">{{ $servicio->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($search || $servicio_filtro)
                                    <button type="button" wire:click="limpiarFiltros"
                                        class="h-11 text-xs font-bold text-gray-500 hover:text-primary uppercase tracking-widest underline self-start">
                                        Limpiar
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- TABLA DE RESULTADOS --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-[45vh] lg:max-h-[50vh]">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    @if($vista === 'por_aceptar')
                                        @php
                                            $idsPagina = $envios->pluck('envio_id')->map(fn($id) => (string) $id)->all();
                                            $todosMarcados = count($idsPagina) > 0
                                                && !array_diff($idsPagina, array_map('strval', $seleccionados_aceptar));
                                        @endphp
                                        <th class="px-3 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">
                                            <input type="checkbox"
                                                wire:click="toggleSeleccionPagina"
                                                @checked($todosMarcados)
                                                @disabled($envios->isEmpty())
                                                title="Seleccionar todos los envíos de esta página"
                                                class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                            <span class="sr-only">Seleccionar todos los envíos de esta página</span>
                                        </th>
                                    @endif
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">N° Envío</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Usuario</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Contenido</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Peso</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tipo</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Servicio</th>
                                    @if($vista === 'en_rezago')
                                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Días en rezago</th>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Entrada</th>
                                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aceptado por</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($envios as $registro)
                                    @php
                                        // POR ACEPTAR: $registro es Envio.
                                        // EN REZAGO: $registro es EnvioRezago (su ->envio es el Envio).
                                        $esPorAceptar = ($vista === 'por_aceptar');
                                        $envio = $esPorAceptar ? $registro : $registro->envio;
                                        $rowKey = $esPorAceptar
                                            ? 'env-' . $registro->envio_id
                                            : 'rez-' . $registro->envio_rezago_id;
                                    @endphp
                                    <tr wire:key="{{ $rowKey }}" class="hover:bg-gray-50 transition duration-150">
                                        @if($esPorAceptar)
                                            <td class="px-3 py-3 text-center">
                                                <input type="checkbox"
                                                    wire:model.live="seleccionados_aceptar"
                                                    value="{{ $envio->envio_id }}"
                                                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
                                            </td>
                                        @endif
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $envio?->codigo_envio ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ optional($envio?->users)->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $envio?->contenido ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $envio?->peso ?? 0 }}gr
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 uppercase text-xs text-center">
                                            {{ $envio?->tipo_envio ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 text-center">
                                            {{ optional($envio?->servicio)->nombre ?? 'No disponible' }}
                                        </td>
                                        @if(!$esPorAceptar)
                                            <td class="px-4 py-3 text-gray-600 text-xs italic text-center">
                                                @if ($registro->Entrada)
                                                    {{ (int) \Carbon\Carbon::parse($registro->Entrada)->diffInDays(now()) }} días
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 text-center">
                                                {{ $registro->Entrada ? \Carbon\Carbon::parse($registro->Entrada)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $registro->usuarioIngreso?->name ?? '—' }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr class="border-b text-center">
                                        <td colspan="{{ $vista === 'por_aceptar' ? 7 : 9 }}" class="py-7 text-gray-500 text-lg">
                                            {{ $vista === 'por_aceptar'
                                                ? 'No hay envíos pendientes de aceptación.'
                                                : 'No hay envíos en rezago.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PAGINACIÓN --}}
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
        </div>
    </div>
</div>