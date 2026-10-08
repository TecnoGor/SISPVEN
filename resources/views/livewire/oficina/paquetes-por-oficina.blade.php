<div>
    @section('titulo') Paquetes por Oficina @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Paquetes por Oficina</h1>
        <p class="mt-1 text-sm text-gray-600">
            Envíos con estatus <span class="font-semibold">Llegada desde COP</span> o
            <span class="font-semibold">Llegada desde OPT</span> registrados entre el
            <span class="font-semibold">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</span>
            y el
            <span class="font-semibold">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>.
        </p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- Buscador --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
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
                                placeholder="Buscar oficina, código o estado...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                <div class="flex flex-col md:flex-row gap-4 w-full items-start md:items-center">

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

                    <div class="w-full md:w-1/4">
                        <label for="tipo_oficina_filtro" class="block text-sm font-medium text-gray-700 mb-1">Tipo de oficina</label>
                        <select id="tipo_oficina_filtro" wire:model.live="tipo_oficina_id"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                            <option value="">Todos</option>
                            @foreach ($tiposOficina as $tipo)
                                <option value="{{ $tipo->tipo_oficina_id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <label for="estado_filtro" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="estado_filtro" wire:model.live="estado_id"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                            <option value="">Todos</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Código</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Oficina</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Envíos que llegaron</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($oficinas as $oficina)
                            <tr wire:key="oficina-{{ $oficina->oficina_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $oficina->codigo ?: '—' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $oficina->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $oficina->estado->nombre ?? 'Sin estado' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-1 rounded-full text-xs font-bold
                                        {{ $oficina->total_envios_entrada > 0 ? 'bg-[#6b1820] text-white' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $oficina->total_envios_entrada ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if ($oficina->total_envios_entrada > 0)
                                        <a href="{{ route('paquetes-por-oficina.detalle', ['oficina_id' => $oficina->oficina_id, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                                            Ver envíos
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin envíos</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg italic bg-gray-50">No se encontraron oficinas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
                {{ $oficinas->links() }}
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
