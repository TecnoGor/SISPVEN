@section('titulo')
    Historial de Consumo de Combustible
@endsection

<div class="max-w-6xl mx-auto sm:px-8 lg:px-12 mt-8">
    <div class="bg-white rounded-2xl shadow-xl p-12 border border-gray-200">
        <!-- Encabezado profesional -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 mb-10">
            <div class="flex items-center gap-8">
                @php
                    $vehiculo = \App\Models\Vehiculo::find($vehiculoId);
                @endphp
                <div class="flex-shrink-0">
                    @if($vehiculo && $vehiculo->imagen)
                        <img src="{{ Storage::url($vehiculo->imagen) }}" alt="Foto del vehículo" class="w-40 h-28 object-cover rounded-lg border border-gray-300 shadow-sm">
                    @else
                        <img src="{{ asset('images/camion.png') }}" alt="Foto por defecto" class="w-40 h-28 object-cover rounded-lg border border-gray-300 shadow-sm">
                    @endif
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary mb-2 flex items-center gap-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 19V4C3 3.44772 3.44772 3 4 3H13C13.5523 3 14 3.44772 14 4V12H16C17.1046 12 18 12.8954 18 14V18C18 18.5523 18.4477 19 19 19C19.5523 19 20 18.5523 20 18V11H18C17.4477 11 17 10.5523 17 10V6.41421L15.3431 4.75736L16.7574 3.34315L21.7071 8.29289C21.9024 8.48816 22 8.74408 22 9V18C22 19.6569 20.6569 21 19 21C17.3431 21 16 19.6569 16 18V14H14V19H15V21H2V19H3ZM5 5V11H12V5H5Z"></path></svg>
                        Historial de Consumo
                    </div>
                    <div class="text-xl text-gray-700 font-semibold">{{ $vehiculoNombre }} <span class="text-gray-400">|</span> <span class="font-mono">{{ $vehiculoPlaca }}</span></div>
                    <div class="text-base text-gray-500 mt-1">Año: <span class="font-semibold">{{ $vehiculo->año ?? '-' }}</span> | Color: <span class="font-semibold">{{ $vehiculo->color ?? '-' }}</span> | Oficina: <span class="font-semibold">{{ $vehiculo->oficina->nombre ?? '-' }}</span></div>
                </div>
            </div>
            <div class="flex flex-col gap-3 md:items-end">
                <button wire:click="exportarHistorialPDF" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold flex items-center shadow text-lg">
                    <svg class="inline w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg> Exportar PDF
                </button>
                <button wire:click="exportarHistorialExcel" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold flex items-center shadow text-lg">
                    <svg class="inline w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg> Exportar Excel
                </button>
            </div>
        </div>
        <!-- Tarjetas resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            @php
                $totalLitros = $historial->sum('litros');
                $totalCostos = $historial->sum('costo_total');
                $totalCargas = $historial->total();
            @endphp
            <div class="bg-gray-50 rounded-xl p-7 flex flex-col items-center shadow-sm border border-gray-100">
                <div class="text-xs text-gray-500 uppercase font-semibold mb-1">Litros Totales</div>
                <div class="text-3xl font-bold text-green-700">{{ number_format($totalLitros, 2) }} L</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-7 flex flex-col items-center shadow-sm border border-gray-100">
                <div class="text-xs text-gray-500 uppercase font-semibold mb-1">Costo Total</div>
                <div class="text-3xl font-bold text-primary">{{ number_format($totalCostos, 2) }} Bs</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-7 flex flex-col items-center shadow-sm border border-gray-100">
                <div class="text-xs text-gray-500 uppercase font-semibold mb-1">Cargas Registradas</div>
                <div class="text-3xl font-bold text-gray-700">{{ $totalCargas }}</div>
            </div>
        </div>
        <!-- Filtros -->
        <div class="flex flex-col md:flex-row md:items-end gap-6 mb-8">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha inicio</label>
                <input type="date" wire:model.live="fechaInicio" class="border border-gray-300 rounded-md px-4 py-3 text-base focus:ring-primary focus:border-primary shadow-sm" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha fin</label>
                <input type="date" wire:model.live="fechaFin" class="border border-gray-300 rounded-md px-4 py-3 text-base focus:ring-primary focus:border-primary shadow-sm" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Por página</label>
                <div class="flex gap-2 mt-1">
                    @foreach([5, 10, 15, 25] as $option)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="perPage" value="{{ $option }}" class="hidden peer" wire:change="$set('page', 1)">
                            <span class="px-4 py-2 rounded-lg font-bold text-base transition border-2 focus:outline-none
                                peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary peer-checked:shadow-lg
                                bg-white text-primary border-gray-300 hover:bg-primary hover:text-white">
                                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                                {{ $option }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Tabla -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-gray-50">
            <table class="w-full text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Litros</th>
                        <th class="px-4 py-3">Costo</th>
                        <th class="px-4 py-3">Kilometraje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $carga)
                        <tr class="text-center hover:bg-gray-100">
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($carga->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ number_format($carga->litros, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format($carga->costo_total, 2) }}</td>
                            <td class="px-4 py-3">{{ $carga->kilometraje }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-gray-400 text-lg">No se encontró resultado para el rango de fechas seleccionado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-8 flex justify-center">
            {{ $historial->links() }}
        </div>
        <div class="mt-10 flex justify-end">
            <a href="{{ route('consumo-combustible') }}" class="text-primary font-bold hover:underline text-xl flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Volver
            </a>
        </div>
    </div>
</div>
