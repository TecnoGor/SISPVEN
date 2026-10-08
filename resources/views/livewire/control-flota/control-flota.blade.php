
@section('titulo')
    Control de Flota
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <!-- Header y acciones -->
    <div class="flex flex-col md:flex-row md:justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl md:text-4xl font-bold text-primary flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V7a2 2 0 012-2h2.5M21 13.5V7a2 2 0 00-2-2h-2.5M3 13.5V17a2 2 0 002 2h2.5m13.5-5.5V17a2 2 0 01-2 2h-2.5M3 13.5h18" /></svg>
            Vehículos Internos
        </h1>
        <div class="flex gap-2">
            <x-secondary-button wire:click="exportarPDF">
                <svg class="w-5 h-5 mr-1 inline-block text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 9.293a1 1 0 011.414 1.414l-8 8a1 1 0 01-1.414 0l-8-8a1 1 0 111.414-1.414L9 16.586V3a1 1 0 112 0v13.586l6.293-6.293z" /></svg>
                PDF
            </x-secondary-button>
            <x-secondary-button wire:click="exportarExcel">
                <svg class="w-5 h-5 mr-1 inline-block text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17 3a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1h12a1 1 0 001-1V3zm-2 2v10H5V5h10zm-7.293 7.707a1 1 0 001.414 0L10 12.414l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414z" /></svg>
                Excel
            </x-secondary-button>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center border border-blue-100">
            <span class="text-2xl font-bold text-blue-700">{{ $totalVehiculos ?? ($vehiculos->total() ?? 0) }}</span>
            <span class="text-gray-500 text-sm">Total Vehículos</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center border border-green-100">
            <span class="text-2xl font-bold text-green-600">{{ $vehiculosActivos ?? ($vehiculos->where('Activo', true)->count() ?? 0) }}</span>
            <span class="text-gray-500 text-sm">Activos</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center border border-red-100">
            <span class="text-2xl font-bold text-red-600">{{ $vehiculosInactivos ?? ($vehiculos->where('Activo', false)->count() ?? 0) }}</span>
            <span class="text-gray-500 text-sm">Inactivos</span>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="flex flex-col md:flex-row gap-2 mb-4 items-center">
        <div class="relative w-full md:w-1/3">
            <input type="text" wire:model.live.debounce.300ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-400 focus:border-blue-400 block w-full pl-10 p-2"
                placeholder="Buscar vehículo, placa, marca...">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
            </div>
        </div>
        <div class="flex-1"></div>
        <div>
            <label class="text-sm font-medium text-gray-900 mr-2">Por página</label>
            <select wire:model.live="perPage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-400 focus:border-blue-400 p-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
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

    <!-- Tabla de vehículos -->
    <div class="overflow-x-auto rounded-xl shadow border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Imagen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Placa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Color</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Marca</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Oficina</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Año</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Modelo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($vehiculos as $vehiculo)
                    <tr wire:key="{{ $vehiculo->vehiculo_id }}" class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3 text-center">
                            <img src="{{ $vehiculo->imagen ? Storage::url($vehiculo->imagen) : asset('images/camion.png') }}" alt="Imagen del vehículo" class="w-16 h-16 object-cover rounded-full border-2 border-gray-200 mx-auto">
                        </td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->placa }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->color }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->marca }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->oficina->nombre ?? 'Sin oficina' }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->año }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->modelo }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $vehiculo['Activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $vehiculo['Activo'] ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="edit({{ $vehiculo->vehiculo_id }})" title="Ver Mantenimientos" class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                Mantenimientos
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="border-b text-center">
                        <td colspan="9" class="py-7 text-default text-2xl">No hay vehículos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="py-4 px-3">
        <div class="flex justify-end">
            {{ $vehiculos->links() }}
        </div>
    </div>

    <!-- Modal de mantenimientos (sin cambios, solo ajusta estilos si quieres) -->
    <form wire:submit.prevent="update">
        <x-dialog-modal wire:model.defer="editForm.open">
            <x-slot name="title">
                Mantenimientos del Vehículo
                <x-secondary-button onclick="descargarReportePDF()">
                    Generar PDF
                </x-secondary-button>
                <x-secondary-button onclick="descargarReporteExcel()">
                    Exportar Excel
                </x-secondary-button>
                <form id="reporteMantenimientosForm" method="POST" target="_blank" style="display:none;">
                    @csrf
                    <input type="hidden" name="vehiculo" id="vehiculoInput">
                    <input type="hidden" name="mantenimientos" id="mantenimientosInput">
                </form>
                <script>
                function descargarReportePDF() {
                    const vehiculoId = document.getElementById('vehiculo_id_hidden').value;
                    if (!vehiculoId) {
                        alert('No hay información del vehículo.');
                        return;
                    }
                    const search = @json($searchMantenimiento ?? '');
                    const start = @json($startDate ?? '');
                    const end = @json($endDate ?? '');
                    const page = @json($mantenimiento_page ?? 1);
                    const params = new URLSearchParams({
                        search: search || '',
                        start: start || '',
                        end: end || '',
                        page: page || 1
                    });
                    window.open(`/reporte-mantenimientos/pdf/${vehiculoId}?${params.toString()}`, '_blank');
                }
                function descargarReporteExcel() {
                    const vehiculoId = document.getElementById('vehiculo_id_hidden').value;
                    if (!vehiculoId) {
                        alert('No hay información del vehículo.');
                        return;
                    }
                    const search = @json($searchMantenimiento ?? '');
                    const start = @json($startDate ?? '');
                    const end = @json($endDate ?? '');
                    const page = @json($mantenimiento_page ?? 1);
                    const params = new URLSearchParams({
                        search: search || '',
                        start: start || '',
                        end: end || '',
                        page: page || 1
                    });
                    window.open(`/reporte-mantenimientos/excel/${vehiculoId}?${params.toString()}`, '_blank');
                }
                </script>
            </x-slot>
            <x-slot name="content">
                <input type="hidden" id="vehiculo_id_hidden" value="{{ $vehiculoActual->vehiculo_id ?? '' }}">
                <div class="mb-4 flex flex-col md:flex-row md:items-end gap-2">
                    <div>
                        <x-input-label for="filtro_fecha" value="Filtrar por fecha" />
                        <input type="date" id="filtro_fecha" wire:model.live="startDate" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <div>
                        <x-input-label for="filtro_fecha_fin" value="Hasta" />
                        <input type="date" id="filtro_fecha_fin" wire:model.live="endDate" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <div class="flex-1">
                        <x-input-label for="filtro_descripcion" value="Buscar descripción" />
                        <input type="text" id="filtro_descripcion" wire:model.live="searchMantenimiento" placeholder="Buscar..." class="border-gray-300 rounded-md shadow-sm w-full" />
                    </div>
                </div>
                <div class="max-h-[40vh] overflow-y-auto">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-default uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3">N°</th>
                                    <th class="px-4 py-3">Descripción</th>
                                    <th class="px-4 py-3">Fecha</th>
                                    <th class="px-4 py-3">Costo</th>
                                    <th class="px-4 py-3">Kilometraje</th>
                                    <th class="px-4 py-3">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($mantenimientosPaginados ?? collect()) as $index => $mantenimiento)
                                    <tr wire:key="mantenimiento-{{ $mantenimiento->mantenimiento_id }}" class="border-b text-left">
                                        <th class="px-4 py-3 font-medium text-black">{{ $mantenimientosPaginados->firstItem() + $index }}</th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $mantenimiento->descripcion }}</th>
                                        <th class="px-4 py-3 font-medium text-black">
                                            {{ \Carbon\Carbon::parse($mantenimiento->created_at)->format('d/m/Y h:i A') }}
                                        </th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $mantenimiento->costo ?? 'N/A' }}</th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $mantenimiento->kilometraje ?? 'N/A' }}</th>
                                        <th class="px-4 py-3 text-center">
                                            <button wire:click="toggleDetails({{ $mantenimiento->mantenimiento_id }})" class="focus:outline-none">
                                                @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </th>
                                    </tr>
                                    @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                                        @foreach ($mantenimiento->detalles as $i => $detalle)
                                            <tr class="border-b bg-gray-80">
                                                <td colspan="5" class="px-4 py-3 text-black text-left">
                                                    {{ $i + 1 }}. {{ $detalle->servicioFlota->nombre }} - {{ \Carbon\Carbon::parse($detalle->fecha)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr class="border-b text-center">
                                        <td colspan="8" class="py-7 text-gray-500 text-lg">No hay resultado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($mantenimientosPaginados)
                            <div class="mt-2">
                                {{ $mantenimientosPaginados->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="$set('editForm.open', false)">
                    Cerrar
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    </form>
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