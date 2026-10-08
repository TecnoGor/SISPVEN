<div>
    @section('titulo') Envíos en {{ $oficina->nombre }} @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-left w-full md:w-auto">
            <h1 class="text-2xl md:text-3xl text-primary font-bold">
                Envíos en {{ $oficina->nombre }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Estado: <span class="font-semibold">{{ $oficina->estado->nombre ?? '—' }}</span>
                @if ($oficina->codigo)
                    · Código: <span class="font-semibold">{{ $oficina->codigo }}</span>
                @endif
                · Envíos con estatus <span class="font-semibold">Llegada desde COP</span> o
                <span class="font-semibold">Llegada desde OPT</span> entre el
                <span class="font-semibold">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</span>
                y el
                <span class="font-semibold">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>.
            </p>
        </div>

        {{-- Botón regresar --}}
        <a href="{{ route('paquetes-por-oficina', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
           class="inline-flex items-center gap-2 bg-white border-2 border-gray-200 shadow-sm
                  rounded-xl px-5 py-2.5 text-sm font-bold text-gray-600
                  transition-all duration-200
                  hover:border-primary hover:text-primary hover:shadow-md hover:bg-gray-50 active:scale-95 uppercase tracking-widest min-w-max">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            Volver
        </a>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- Buscador --}}
            <div class="mt-2 mb-4">
                <div class="flex items-center w-full md:w-2/3">
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                            placeholder="Buscar por código de envío, nombre o documento del destinatario...">
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                <div class="w-full md:w-1/4">
                    <label for="fecha_inicio_filtro" class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" id="fecha_inicio_filtro" wire:model.live="fecha_inicio" max="{{ now()->toDateString() }}"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="fecha_fin_filtro" class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                    <input type="date" id="fecha_fin_filtro" wire:model.live="fecha_fin" max="{{ now()->toDateString() }}"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Código Envío</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Servicio</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Destinatario</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Documento</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado Destino</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha de entrada</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($envios as $envio)
                            <tr wire:key="envio-{{ $envio->envio_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    {{ $envio->codigo_envio ?: '—' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ optional($envio->servicio)->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ trim(($envio->nombre_dest ?? '') . ' ' . ($envio->apellido_dest ?? '')) ?: '—' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $envio->documento_dest ?: '—' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $envio->estadoDestino->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $envio->fecha_entrada_oficina ? \Carbon\Carbon::parse($envio->fecha_entrada_oficina)->format('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="6" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay envíos en estatus de entrada para esta oficina</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
                {{ $envios->links() }}
            </div>

        </div>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>
