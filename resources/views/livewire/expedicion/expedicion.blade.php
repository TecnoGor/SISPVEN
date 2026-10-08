<div>
    @section('titulo')
        Expedición
    @endsection

    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold uppercase tracking-tight">Expedición</h1>
        <p class="mt-1 text-sm text-gray-600">
            @if($vista === 'por_aceptar')
                Envíos enviados desde el almacén, pendientes de aceptación en Expedición.
            @else
                Envíos actualmente en Expedición, listos para ser cargados en valijas o despachados.
            @endif
        </p>
    </div>

    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            {{-- Barra superior: tabs + botón aceptar --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-between gap-3">
                <div class="inline-flex p-1 bg-gray-100 rounded-lg border border-gray-200 w-full sm:w-auto overflow-x-auto">
                    <button type="button" wire:click="setVista('por_aceptar')"
                        class="px-3 sm:px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 whitespace-nowrap flex-1 sm:flex-none
                            {{ $vista === 'por_aceptar' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        POR ACEPTAR
                    </button>
                    <button type="button" wire:click="setVista('en_expedicion')"
                        class="px-3 sm:px-4 py-1.5 text-xs font-bold rounded-md transition duration-200 whitespace-nowrap flex-1 sm:flex-none
                            {{ $vista === 'en_expedicion' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        EN EXPEDICIÓN
                    </button>
                </div>

                @if($vista === 'por_aceptar')
                    <button type="button" wire:click="aceptarSeleccionados"
                        @disabled(count($seleccionados_aceptar) === 0)
                        class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition w-full sm:w-auto
                            {{ count($seleccionados_aceptar) === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}">
                        Aceptar envíos
                        <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ count($seleccionados_aceptar) }}</span>
                    </button>
                @endif
            </div>

            {{-- Filtros --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50/50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Buscador --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Código, remitente o destinatario..."
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3 pl-10 bg-white">
                        </div>
                    </div>

                    {{-- Fecha desde --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">
                            {{ $vista === 'en_expedicion' ? 'Entrada desde' : 'Desde' }}
                        </label>
                        <input type="date" wire:model.live="desde"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3 bg-white">
                    </div>

                    {{-- Fecha hasta --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">
                            {{ $vista === 'en_expedicion' ? 'Entrada hasta' : 'Hasta' }}
                        </label>
                        <input type="date" wire:model.live="hasta"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3 bg-white">
                    </div>
                </div>

                @if($search || $servicio_id || $desde || $hasta)
                    <div class="mt-3 flex justify-end">
                        <button type="button" wire:click="limpiarFiltros"
                            class="text-xs font-bold text-gray-500 hover:text-[#6b1820] uppercase tracking-widest underline">
                            Limpiar filtros
                        </button>
                    </div>
                @endif
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            @if($vista === 'por_aceptar')
                                @php
                                    $idsPagina = $registros->pluck('envio_id')->map(fn($id) => (string) $id)->all();
                                    $todosMarcados = count($idsPagina) > 0
                                        && !array_diff($idsPagina, array_map('strval', $seleccionados_aceptar));
                                @endphp
                                <th class="px-3 py-3 text-center w-10">
                                    <input type="checkbox"
                                        wire:click="toggleSeleccionPagina"
                                        @checked($todosMarcados)
                                        @disabled($registros->isEmpty())
                                        title="Seleccionar todos los envíos de esta página"
                                        class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50">
                                    <span class="sr-only">Seleccionar todos los envíos de esta página</span>
                                </th>
                            @endif
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Código</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Tipo</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Servicio</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Remitente</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Destinatario</th>
                            @if($vista === 'en_expedicion')
                                <th class="px-4 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Entrada</th>
                                <th class="px-4 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Valija</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Aceptado por</th>
                            @endif
                            <th class="px-4 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($registros as $registro)
                            @php
                                $esPorAceptar = ($vista === 'por_aceptar');
                                $envio = $esPorAceptar ? $registro : $registro->envio;
                                $rowKey = $esPorAceptar
                                    ? 'env-' . $registro->envio_id
                                    : 'exp-' . $registro->envio_expedicion_id;
                                $servicioId = (int) ($envio?->servicio_id ?? 0);
                                $esEms      = $servicioId === \App\Livewire\Expedicion\Expedicion::SERVICIO_EMS;
                                $esBulto    = $servicioId === \App\Livewire\Expedicion\Expedicion::SERVICIO_BULTO;
                                $esIposplus = $servicioId === \App\Livewire\Expedicion\Expedicion::SERVICIO_IPOSPLUS;
                                $valijaActiva = !$esPorAceptar
                                    ? $envio?->sacas?->first()
                                    : null;
                            @endphp
                            <tr wire:key="{{ $rowKey }}" class="hover:bg-gray-50 transition">
                                @if($esPorAceptar)
                                    <td class="px-3 py-3 text-center">
                                        <input type="checkbox"
                                            wire:model.live="seleccionados_aceptar"
                                            value="{{ $envio->envio_id }}"
                                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
                                    </td>
                                @endif
                                <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">
                                    {{ $envio?->codigo_envio ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($esEms)
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 uppercase">EMS</span>
                                    @elseif($esBulto)
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">BULTO</span>
                                    @elseif($esIposplus)
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase">IPOSPLUS</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $envio?->servicio?->nombre ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $envio ? trim(($envio->nombre_rem ?? '') . ' ' . ($envio->apellido_rem ?? '')) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $envio ? trim(($envio->nombre_dest ?? '') . ' ' . ($envio->apellido_dest ?? '')) : '—' }}
                                </td>
                                @if($vista === 'en_expedicion')
                                    <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                        {{ $registro->Entrada ? \Carbon\Carbon::parse($registro->Entrada)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($valijaActiva)
                                            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase whitespace-nowrap"
                                                title="En valija {{ $valijaActiva->codigo_saca }}">
                                                {{ $valijaActiva->codigo_saca }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 uppercase">Suelto</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $registro->usuarioIngreso?->name ?? '—' }}
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-center">
                                    @if($envio?->envio_id)
                                        <a href="{{ route('encaminamiento.encaminamiento-detalles', $envio->envio_id) }}"
                                            target="_blank"
                                            title="Ver encaminamiento"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition">
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
                            @php
                                if ($vista === 'por_aceptar') {
                                    $colspan = 7;
                                    $emptyMsg = 'No hay envíos pendientes de aceptación.';
                                } else {
                                    $colspan = 9;
                                    $emptyMsg = 'No hay envíos actualmente en Expedición.';
                                }
                            @endphp
                            <tr>
                                <td colspan="{{ $colspan }}" class="px-4 py-12 text-center text-gray-500 italic">
                                    {{ $emptyMsg }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/50 flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">Por página:</label>
                    <select wire:model.live="perPage"
                        class="block border border-gray-300 rounded-md text-sm py-1.5 px-2 bg-white">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div>
                    {{ $registros->links() }}
                </div>
            </div>
        </div>
    </div>
</div>