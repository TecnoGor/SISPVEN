<div>
    @section('titulo')
        Guia de Despacho
    @endsection
    @php
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id; // Oficina del usuario
        $oficina = \App\Models\Oficina::where('oficina_id', $oficinaId)->first();
    @endphp
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Guias De Despacho de {{ $oficina->nombre }}</h1>
            </div>
        </div>

        <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">

            {{-- Barra de filtros --}}
            <div class="flex flex-col gap-4 border-b border-gray-100 bg-gray-50/60 p-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-wrap items-end gap-4 w-full md:w-auto">
                    <div class="relative">
                        <label for="fechaDesde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                        <input type="date" id="fechaDesde" wire:model.live="fechaDesde"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary block w-full pl-3 p-2 shadow-sm transition">
                    </div>
                    <div class="relative">
                        <label for="fechaHasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                        <input type="date" id="fechaHasta" wire:model.live="fechaHasta"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary block w-full pl-3 p-2 shadow-sm transition">
                    </div>
                </div>
                {{-- Contador de resultados --}}
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1.5 text-sm font-semibold text-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5m-16.5 6h16.5" />
                        </svg>
                        {{ count($despachos) }} {{ count($despachos) === 1 ? 'guía' : 'guías' }}
                    </span>
                </div>
            </div>

            {{-- Encabezado del listado --}}
            <div class="flex items-center gap-2 border-b border-gray-100 px-4 py-3">
                <span class="h-5 w-1 rounded-full bg-primary"></span>
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-600">Listado de Guías</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                @forelse ($despachos as $despacho)
                    <a href="{{ route('manifiestos_paquetes', $despacho->numero_despacho_id) }}"
                        class="group w-full bg-white shadow-lg rounded-lg p-4 hover:shadow-xl transform hover:scale-105 transition duration-300 border border-gray-200 text-left relative overflow-hidden">

                        {{-- Acento lateral --}}
                        <span class="absolute left-0 top-0 h-full w-1 bg-primary/80"></span>

                        {{-- Encabezado: título + estatus --}}
                        <div class="flex items-center justify-between gap-2 pl-2 pb-2 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-primary">Despacho N° {{ $despacho->numero_despacho }}</h3>
                            @if ($despacho->activo)
                                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-semibold text-yellow-700 ring-1 ring-inset ring-yellow-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>Sin Despachar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Despachado
                                </span>
                            @endif
                        </div>

                        {{-- Datos --}}
                        <div class="pl-2 mt-3 space-y-2 text-sm">
                            <p class="flex items-center gap-2 text-gray-600">
                                <svg class="h-4 w-4 flex-shrink-0 text-primary/60" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span class="text-gray-400">Destino:</span>
                                <span class="font-medium text-gray-700">{{ optional($despacho->oficinaDestino)->nombre ?? '-' }}</span>
                            </p>
                            <p class="flex items-center gap-2 text-gray-600">
                                <svg class="h-4 w-4 flex-shrink-0 text-primary/60" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                <span class="text-gray-400">Valijas:</span>
                                <span class="font-medium text-gray-700">{{ $despacho->cantidad_valijas }}</span>
                            </p>
                            <p class="flex items-center gap-2 text-gray-600">
                                <svg class="h-4 w-4 flex-shrink-0 text-primary/60" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span class="text-gray-400">Fecha:</span>
                                <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($despacho->created_at)->format('d/m/Y') }}</span>
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center px-6 py-14 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-6m6 0V6.75a.75.75 0 0 0-.75-.75H3.75a.75.75 0 0 0-.75.75v11.25m13.5 0V9.75" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-gray-700">No hay guías de despacho</h3>
                        <p class="mt-1 max-w-sm text-sm text-gray-400">No se encontraron guías en el rango de fechas seleccionado. Prueba ajustando las fechas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>