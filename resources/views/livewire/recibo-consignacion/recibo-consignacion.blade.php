@section('titulo')
    Recibos de consignación
@endsection
<div>
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Recibos de consignación</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y visualización de documentos de envío.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">
            
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="buscar"
                               class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                               placeholder="Buscar por código, documento, nombre o apellido...">
                        
                        <div wire:loading wire:target="buscar" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="animate-spin h-4 w-4 text-[#6b1820]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end">
                        <label class="text-sm font-medium text-gray-700">Mostrar:</label>
                        <select wire:model.live="input" 
                                class="block w-20 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-2">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            @php $cols = [
                                'servicio_id' => 'Servicio',
                                'user_id' => 'Usuario',
                                'created_at' => 'Fecha Creación',
                                'codigo_envio' => 'Código Envío',
                                'nombre_rem' => 'Remitente',
                                'documento_rem' => 'Documento'
                            ]; @endphp

                            @foreach($cols as $key => $label)
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 cursor-pointer hover:bg-gray-200 transition-colors" 
                                wire:click="sortBy('{{ $key }}')">
                                <div class="flex items-center gap-1">
                                    {{ $label }}
                                    @if($sortBy === $key)
                                        <svg class="w-4 h-4 text-[#6b1820]" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                            @else
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($envios as $envio)
                            <tr wire:key="{{ $envio->envio_id }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex items-center">
                                        {{ $envio->servicio ? $envio->servicio->nombre : 'Servicio no asignado' }}
                                        @if($envio->servicio && $envio->servicio->servicio_id == 21)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 ml-2">
                                                EMS
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $envio->users ? $envio->users->name : 'No encontrado'}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $envio->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-800">{{ $envio->codigo_envio ?? 'No encontrado' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $envio->nombre_rem.' '.$envio->apellido_rem ?? 'No encontrado' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ ($envio->tipo_documento_rem.'-'.$envio->documento_rem) ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('recibo-consignacion.pdf', $envio->envio_id) }}" 
                                           target="_blank" 
                                           title="Descargar PDF" 
                                           class="inline-flex items-center justify-center p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center bg-gray-50">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="text-lg font-semibold text-gray-500">No hay recibos de consignación</div>
                                        <p class="text-sm text-gray-400">Intenta ajustar los filtros de búsqueda para encontrar lo que buscas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Mostrando <span class="font-semibold text-gray-900">{{ $envios->firstItem() ?? 0 }}</span> a <span class="font-semibold text-gray-900">{{ $envios->lastItem() ?? 0 }}</span> de <span class="font-semibold text-gray-900">{{ $envios->total() }}</span> resultados
                    </div>
                    <div>
                        {{ $envios->links() }}
                    </div>
                </div>
            </div>

        </div> 
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
        <script>
            // success alert
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

    <script>
        function formatCurrency(input) {
            // Eliminar caracteres no numéricos
            let value = input.value.replace(/[^0-9]/g, '');

            // Convertir a número y formatear
            if (value.length === 0) {
                input.value = '0,00';
                return;
            }

            // Convertir a centimos
            let cents = parseInt(value, 10);

            // Formatear a bs y centimos
            let bs = Math.floor(cents / 100);
            let formattedCents = (cents % 100).toString().padStart(2, '0');

            // // Agregar separador de miles
            let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Actualizar el valor del input
            input.value = `${formattedbs},${formattedCents}`;
            console.log('entro');
        }
    </script>
@endpush