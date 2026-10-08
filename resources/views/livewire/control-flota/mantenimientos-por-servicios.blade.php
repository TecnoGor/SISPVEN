
@section('titulo')
    Control de Flota
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <!-- Header y acciones -->
    <div class="flex flex-col md:flex-row md:justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl md:text-4xl font-bold text-primary flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V7a2 2 0 012-2h2.5M21 13.5V7a2 2 0 00-2-2h-2.5M3 13.5V17a2 2 0 002 2h2.5m13.5-5.5V17a2 2 0 01-2 2h-2.5M3 13.5h18" /></svg>
            Mantenimientos por Fecha
        </h1>
        <div class="flex gap-2">
            <x-secondary-button wire:click="generarPDFPorRango">
                <svg class="w-5 h-5 mr-1 inline-block text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 9.293a1 1 0 011.414 1.414l-8 8a1 1 0 01-1.414 0l-8-8a1 1 0 111.414-1.414L9 16.586V3a1 1 0 112 0v13.586l6.293-6.293z" /></svg>
                Imprimir PDF
            </x-secondary-button>
            <x-secondary-button wire:click="exportarExcelPorRango">
                <svg class="w-5 h-5 mr-1 inline-block text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17 3a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1h12a1 1 0 001-1V3zm-2 2v10H5V5h10zm-7.293 7.707a1 1 0 001.414 0L10 12.414l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414z" /></svg>
                Exportar Excel
            </x-secondary-button>
        </div>
    </div>


    <!-- Tarjetas resumen -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center border border-blue-100">
            <span class="text-2xl font-bold text-blue-700">{{ $mantenimientos->count() }}</span>
            <span class="text-gray-500 text-sm">Mantenimientos en Rango</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center border border-green-100">
            <span class="text-2xl font-bold text-green-600">{{ $mantenimientos->unique('vehiculo_id')->count() }}</span>
            <span class="text-gray-500 text-sm">Vehículos Atendidos</span>
        </div>
    </div>

    <!-- Filtros destacados -->
    <div class="flex flex-col md:flex-row gap-4 mb-6 items-end">
        <div>
            <label for="fechaDesde" class="block text-sm font-medium text-gray-700">Desde</label>
            <input id="fechaDesde" type="date" wire:model.live="fechaDesde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-400 focus:border-blue-400 block w-full p-2" />
        </div>
        <div>
            <label for="fechaHasta" class="block text-sm font-medium text-gray-700">Hasta</label>
            <input id="fechaHasta" type="date" wire:model.live="fechaHasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-400 focus:border-blue-400 block w-full p-2" />
        </div>
    </div>

   <!-- Pestañas -->
   <div class="flex flex-col md:flex-row">
    <x-tab-link :href="route('flota')" :active="request()->routeIs('flota')" wire:navigate.hover>
        {{ __('Todos los Vehículos') }}
    </x-tab-link>

    <x-tab-link :href="route('mantenimientos-hoy')" :active="request()->routeIs('mantenimientos-hoy')" wire:navigate.hover>
        {{ __('Mantenimientos de Hoy') }}
    </x-tab-link>
    <x-tab-link :href="route('mantenimientos-servicios')" :active="request()->routeIs('mantenimientos-servicios')" wire:navigate.hover>
        {{ __('Por Fecha') }}
    </x-tab-link>
    <x-tab-link :href="route('consumo-combustible')" :active="request()->routeIs('consumo-combustible')" wire:navigate.hover>
        {{ __('Consumo de Combustible') }}
    </x-tab-link>
</div>

    <!-- Lista de mantenimientos en tarjetas modernas -->
    <div class="space-y-4">
        @forelse ($mantenimientos as $mantenimiento)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 flex flex-col md:flex-row md:items-center gap-4 hover:shadow-lg transition">
                <div class="flex items-center gap-4 flex-1">
                    <div class="flex flex-col items-center justify-center">
                        <div class="bg-blue-100 text-blue-700 rounded-full w-10 h-10 flex items-center justify-center font-bold text-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <span class="font-semibold text-lg text-primary">{{ $mantenimiento->descripcion }}</span>
                            <span class="ml-2 px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-semibold">
                                {{ $mantenimiento->vehiculo->placa ?? 'Desconocido' }}
                                <span class="text-gray-400">({{ $mantenimiento->vehiculo->modelo ?? 'Desconocido' }})</span>
                            </span>
                            <span class="ml-2 px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold">
                                {{ $mantenimiento->vehiculo->oficina->nombre ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ \Carbon\Carbon::parse($mantenimiento->created_at)->format('d/m/Y h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <button wire:click="toggleDetails({{ $mantenimiento->mantenimiento_id }})" class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg shadow-sm transition">
                        @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
            @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                <div class="bg-gray-50 border-l-4 border-blue-300 rounded-b-xl shadow-inner px-6 py-3 mb-2 animate-fade-in">
                    <div class="font-semibold text-primary mb-2">Servicios realizados:</div>
                    <ul class="list-disc pl-6">
                        @foreach ($mantenimiento->detalles as $i => $detalle)
                            <li class="mb-1">
                                <span class="font-semibold text-blue-700">{{ $detalle->servicioFlota->nombre }}</span>
                                <span class="ml-2 text-xs text-gray-500">{{ \Carbon\Carbon::parse($detalle->fecha)->format('d/m/Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500 text-lg">
                No hay mantenimientos en el rango seleccionado.
            </div>
        @endforelse
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        })
    </script>
    @endscript
@endpush