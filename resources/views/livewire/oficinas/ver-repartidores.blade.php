<div>
    @section('titulo') Personal de Reparto @endsection

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- Encabezado --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mt-10 mb-6">
            <div class="text-left">
                <h1 class="text-2xl md:text-3xl text-primary font-bold uppercase">Personal de Reparto</h1>
                <p class="mt-1 text-sm text-gray-600">Gestión de repartidores asignados y estatus de envíos en esta oficina.</p>
            </div>

            <div class="mt-4 md:mt-0">
                <a href="{{ route('ver-almacen') }}" wire:navigate 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Almacén
                </a>
            </div>
        </div>

        {{-- Contenedor Principal --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-6">
            <div class="mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="relative w-full md:w-1/3">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition"
                            placeholder="Buscar repartidor por nombre...">
                    </div>
                </div>
            </div>

            {{-- Tabla Principal --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600 tracking-wider text-left">Repartidor</th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600 tracking-wider">Cédula</th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600 tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600 tracking-wider text-primary font-bold">Envíos</th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600 tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($carteros as $cartero)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-[#6b1820] flex items-center justify-center text-white font-bold text-xs mr-3">
                                            {{ substr($cartero->name, 0, 2) }}
                                        </div>
                                        <span class="font-medium text-gray-900 uppercase">{{ $cartero->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600 uppercase">{{ $cartero->cedula }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $cartero->activo ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200' }} border uppercase">
                                        {{ $cartero->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 font-bold text-[#6b1820] text-lg">
                                    {{ $cartero->conteo_real ?? 0 }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button wire:click="ver_detalles({{ $cartero->id }}, '{{ $cartero->name }}')" 
                                        class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-gray-500 italic">No se encontraron repartidores en esta oficina.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL DE DETALLES --}}
    @if($open_modal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-2xl overflow-hidden transition-all transform bg-white rounded-xl shadow-2xl">
                    
                    {{-- Cabecera Modal --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m.75-12H6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 22.5h12a2.25 2.25 0 0 0 2.25-2.25V8.25A2.25 2.25 0 0 0 18 6H9.75Z" />
                            </svg>
                            <h3 class="text-xl font-bold uppercase">Envíos de: {{ $repartidor_seleccionado }}</h3>
                        </div>
                        <button wire:click="$set('open_modal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Tabla de Envíos en Modal --}}
                    <div class="p-6">
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-center">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Guía de Tracking</th>
                                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Estatus Actual</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-center">
                                    @foreach($envios_lista as $envio)
                                        <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-left">
                                                <span class="font-mono font-bold text-sm text-gray-800 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                                    {{ $envio['tracking'] }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    // Ahora usamos el ID que configuramos en el componente (ya sea el real o el 16/18 de prueba)
                                                    $id_actual = $envio['id_estatus'];
                                                    $texto = $this->obtenerTextoEstatus($id_actual);
                                                    
                                                    $config = match($id_actual) {
                                                        16 => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                                        18 => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                                    };
                                                @endphp

                                                <div class="inline-flex items-center px-3 py-1 rounded-md border {{ $config['bg'] }} {{ $config['border'] }} {{ $config['text'] }}">
                                                    <span class="h-2 w-2 rounded-full {{ $config['dot'] }} mr-2"></span>
                                                    <span class="text-xs font-bold uppercase tracking-tight">
                                                        {{ $texto }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Botón de Cierre --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200">
                        <button wire:click="$set('open_modal', false)" 
                                class="px-6 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-widest">
                            Cerrar Ventana
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>