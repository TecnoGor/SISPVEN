<div>
    <div>
        @section('titulo')
            Incidencias
        @endsection
        @php
            $usuario = auth()->user();
            $oficinaId = $usuario->oficina_id; // Obtener la oficina_id asociada al usuario
        @endphp
        
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-7">
            <div class="mb-4 text-left">
                <h1 class="text-2xl md:text-3xl text-primary font-bold">Incidencias</h1>
                <p class="mt-1 text-sm text-gray-600">Listado de envíos con novedades o devoluciones.</p>
            </div>
            
            <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4">
                
                <div class="flex items-center justify-between pb-4">
                    <div class="flex w-full items-center">
                        <div class="relative w-full md:w-1/3"> 
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            
                            <input type="text" 
                                wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820]
                                    block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar Código, Usuario, Tipo...">
                        </div>
                    </div>

                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">Código Envío</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">Tipo de Envío</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">Usuario</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">Peso Envío</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">Fecha y Hora</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-800">
                                    Incidencias
                                </th>
                            </tr>
                        </thead>
                        
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($incidencias as $envio)
                                <tr wire:key="{{ $envio->envio_id }}" class="border-b text-left hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $envio->envio->codigo_envio }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $envio->envio->tipo_envio }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $envio->usuario->name}}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $envio->envio->peso }} gr</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $envio->created_at?->format('d/m/Y H:i:s') }}
                                    </td>
                                    
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        <button type="button" wire:click='verIncidencia({{$envio->envio_incidencia_id}})'
                                            class="text-red-500 hover:text-red-700 transition duration-150"
                                            title="Ver Detalles de la Incidencia">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg> 
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b text-center">
                                    <td colspan="6" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay Envíos con Incidencias registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- EL MODAL --}}
    @if($mostrar_incidencia)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 bg-black bg-opacity-40">
            <div class="relative w-full max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-3">
                            <svg class="w-6 h-6 text-[#6b1820]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Lista de Incidencias
                        </h3>

                        <button type="button" wire:click='cerrarIncidencia' class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 max-h-[60vh] overflow-y-auto space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-3">Incidencias</h4>
                                <ul class="space-y-3">
                                    @foreach($detalles as $detalle)
                                        <li class="bg-white border border-gray-100 p-3 rounded-lg shadow-sm hover:shadow-md transition">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#f6eaea] text-[#6b1820] font-semibold">!</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-gray-700 font-medium">{{ $detalle->incidencia }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">Registrada: {{ optional($detalle->created_at)->diffForHumans() ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-3">Descripción</h4>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-gray-700">
                                    {{ $envio_incidencia->detalle ?? 'Sin detalles adicionales registrados.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <x-button class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27]" type="button" wire:click='cerrarIncidencia'>
                            Cerrar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
