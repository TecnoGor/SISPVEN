<div>
    @section('titulo')
        Pedidos de Imprenta
    @endsection
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Pedidos de Imprenta</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de Imprenta.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y BOTÓN --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar sello...">
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            <x-button
                                type="button"
                                wire:click="toggleHistory"
                                class="px-6 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                            >
                                @if($showHistory)
                                    Ver activos
                                @else
                                    Ver Completados
                                @endif
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                {{-- <div class="w-full md:w-1/4">
                    <label for="serie" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Serie</label>
                    <select id="serie" wire:model.live="serie"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                        <option value="">Todas las Series</option>
                    </select>
                </div> --}}
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cliente</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cedula</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Alto x Ancho</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Imagen</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pedidos as $index => $pedido)
                            <tr wire:key="{{ $pedido->pedido_imprenta_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $pedido->nombre.' '.$pedido->apellido }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $pedido->tipo_documento.' - '.$pedido->documento}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $pedido->alto.' x '.$pedido->ancho }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    <a href="{{ asset('storage/' . $pedido->imagen_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $pedido->imagen_path) }}" 
                                            alt="{{ $pedido->nombre_original }}" 
                                            class="w-16 h-16 object-cover rounded-md mx-auto hover:opacity-80">
                                    </a>
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($showHistory)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full uppercase tracking-wide">
                                            Completado
                                        </span>
                                    @else
                                        <button wire:click="estatus({{ $pedido->pedido_imprenta_id }})" class="inline-flex items-center justify-center p-2 rounded transition hover:bg-gray-100">
                                            @if($pedido->activo)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-700">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                                </svg>
                                            @endif
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="4" class="py-7 text-gray-500 text-lg italic bg-gray-50">
                                    @if($showHistory)
                                        No hay pedidos completados
                                    @else
                                        No hay sellos disponibles
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{$pedidos->links()}}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="paginacion" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="paginacion" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
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
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess3', message => {
            Swal.fire({
                position: "center",
                icon: "info",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

        Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
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
