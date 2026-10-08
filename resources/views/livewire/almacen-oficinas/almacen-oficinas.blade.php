<div>
    @section('titulo') Información de Envios @endsection
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Información de Envios</h1>
        <p class="mt-1 text-sm text-gray-600">Consulta y seguimiento detallado de envíos en almacén y entregados.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA SUPERIOR: BUSCADOR Y FILTROS --}}
            <div class="mt-2 mb-6 w-full">
                <div class="flex flex-col space-y-4">
                    
                    {{-- Fila 1: Búsqueda y Tabs --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        {{-- Búsqueda --}}
                        <div class="relative flex-1 min-w-[280px]">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.400ms="search"
                                   type="text"
                                   placeholder="Buscar por código, remitente o destinatario..."
                                   class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 transition duration-150">
                        </div>

                        {{-- Filtro de Estatus (Tabs) --}}
                        <div class="inline-flex p-1 bg-gray-100 rounded-lg border border-gray-200 shrink-0">
                            <button wire:click="setStatus(true)"
                                    class="px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 {{ $filterStatus ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                CREADOS
                            </button>
                            <button wire:click="setStatus(false)"
                                    class="px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 {{ !$filterStatus ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                ENTREGADOS
                            </button>
                        </div>
                    </div>

                    {{-- Fila 2: Estado, Oficina y Fechas --}}
                    <div class="flex flex-wrap items-center gap-4">
                        {{-- Selector de Estado --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estado:</span>
                            <select wire:model.live="estado_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary py-2 pl-3 pr-10 transition duration-150 shrink-0 cursor-pointer min-w-[200px]">
                                <option value="">— Todos los estados —</option>
                                @foreach($estados as $est)
                                    <option value="{{ $est->estado_id }}">{{ $est->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Selector de oficina --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Oficina:</span>
                            <select wire:model.live="oficina_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary py-2 pl-3 pr-10 transition duration-150 shrink-0 cursor-pointer min-w-[250px]">
                                <option value="">— Todas las oficinas —</option>
                                @foreach($oficinas as $oficina)
                                    <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fecha Desde --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Desde:</span>
                            <input type="date" wire:model.live="desde"
                                   class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary p-2 transition duration-150">
                        </div>

                        {{-- Fecha Hasta --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hasta:</span>
                            <input type="date" wire:model.live="hasta"
                                   class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-primary focus:border-primary p-2 transition duration-150">
                        </div>
                    </div>
                </div>
            </div>


            {{-- TABLA DE RESULTADOS --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                {{-- Indicador de carga --}}
                <div wire:loading class="w-full h-1 bg-primary animate-pulse"></div>

                <div class="overflow-x-auto overflow-y-auto max-h-[55vh]">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Código Envío</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Remitente</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Destinatario</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Oficina</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo Envío</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Último Estatus</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($envios as $item)
                                @php
                                    // En modo Creados: $item es Envio. En modo Entregados: $item es RegistroEntrega
                                    $envio       = $filterStatus ? $item : $item->envio;
                                    $oficina     = $filterStatus ? $item->oficinas : $envio?->oficinas;
                                    $pk          = $filterStatus ? $item->envio_id : $item->registro_entrega_id;
                                    $envio_id    = $envio?->envio_id;
                                    $ultimoEstatus = $envio?->envio_encaminamientos
                                        ?->sortBy('envios_encaminamiento_id')
                                        ->last()
                                        ?->envio_estatus
                                        ?->estatus;
                                @endphp
                                <tr wire:key="row-{{ $pk }}"
                                    class="hover:bg-gray-50 transition duration-150">

                                    {{-- Código --}}
                                    <td class="px-6 py-3 text-center">
                                        <span class="font-bold text-gray-900 tracking-wide">
                                            {{ $envio?->codigo_envio ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Remitente --}}
                                    <td class="px-6 py-3 text-gray-700 text-center">
                                        {{ $envio ? trim(($envio->nombre_rem ?? '') . ' ' . ($envio->apellido_rem ?? '')) : '—' }}
                                    </td>

                                    {{-- Destinatario --}}
                                    <td class="px-6 py-3 text-gray-700 text-center">
                                        {{ $envio ? trim(($envio->nombre_dest ?? '') . ' ' . ($envio->apellido_dest ?? '')) : '—' }}
                                    </td>

                                    {{-- Oficina --}}
                                    <td class="px-6 py-3 text-gray-600 text-center">
                                        {{ $oficina?->nombre ?? '—' }}
                                    </td>

                                    {{-- Tipo envío (badge) --}}
                                    <td class="px-6 py-3 text-center">
                                        @if($envio?->tipo_envio === 'nacional')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Nacional</span>
                                        @elseif($envio?->tipo_envio === 'internacional')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase">Internacional</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Último Estatus --}}
                                    <td class="px-6 py-3 text-center">
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 uppercase">
                                            {{ $ultimoEstatus ?? 'PENDIENTE' }}
                                        </span>
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-3 text-center text-gray-600">
                                        {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '—' }}
                                    </td>

                                    {{-- Acción: Ver encaminamiento --}}
                                    <td class="px-6 py-3 text-center">
                                        @if($envio_id)
                                            <a href="{{ route('encaminamiento.encaminamiento-detalles', $envio_id) }}"
                                               target="_blank"
                                               title="Ver encaminamiento"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-16 text-center text-gray-500 italic text-base">
                                        No se encontraron envíos con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- PAGINACIÓN AL ESTILO PERSONAL AUTORIZADO --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4 mb-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 px-3">
            {{-- Selector de registros por página --}}
            <div class="flex items-center gap-4">
                <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                <select wire:model.live="perPage" id="perPage_bottom" 
                        class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2.5 px-2 bg-white">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            {{-- Información de resultados --}}
            <div class="text-center">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Mostrando <span class="text-primary">{{ $envios->firstItem() ?? 0 }}</span>
                    a <span class="text-primary">{{ $envios->lastItem() ?? 0 }}</span>
                    de <span class="text-primary">{{ $envios->total() }}</span> registros
                </span>
            </div>

            {{-- Links de paginación --}}
            <div>
                {{ $envios->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: message.message,
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
    @endscript
@endpush
</div>

