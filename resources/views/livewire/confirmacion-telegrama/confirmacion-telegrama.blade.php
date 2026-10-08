<div>
    @section('titulo')
       Confirmacion de Telegramas
    @endsection
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
            @if($seccion == 1)
                Recepción de Telegramas
            @else
                Telegramas Emitidos
            @endif
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            @if($seccion == 1)
                Gestión de telegramas entrantes y control de avisos.
            @else
                Historial de telegramas enviados desde esta oficina.
            @endif
        </p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y BOTONES DE ACCIÓN --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-1/3">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar por remitente, destino o GIT...">
                        </div>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="reporte_excel">
                            REPORTE EXCEL
                        </x-primary-button>

                        <a
                            href="{{ route('confirmacion-telegrama.pdf', [
                                'seccion' => $seccion,
                                'desde'   => $desde,
                                'hasta'   => $hasta,
                                'search'  => $search,
                            ]) }}"
                            target="_blank"
                            class="inline-flex items-center px-3 py-2 bg-[#6b1820] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 active:bg-gray-900 active:ring-2 active:ring-sky-300 active:ring-offset-2 focus:outline-none transition ease-in-out duration-150 shadow-sm no-underline"
                            title="Abrir reporte PDF en nueva pestaña">
                            REPORTE PDF
                        </a>

                        <button 
                            wire:click="cambiar_seccion" 
                            class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            Ir a: {{ $seccion == 1 ? 'Telegramas Enviados' : 'Telegramas Recibidos' }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- FILTROS DE FECHA --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start border-t border-gray-100 pt-4">
                <div class="w-full md:w-1/4">
                    <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Origen</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Remitente</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Destino</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Destinatario</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">GIT</th>
                            @if($seccion == 2)
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Aprobación</th>
                            @endif
                            @if($seccion == 1)
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Imprimir</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Aviso</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cant.</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($telegramas as $index => $telegrama)
                            <tr wire:key="{{ $telegrama->envio_id }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 whitespace-nowrap text-center font-medium text-gray-900">{{ $telegrama->oficinas?->nombre }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">{{ $telegrama->nombre_rem }} {{ $telegrama->apellido_rem }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">{{ $telegrama->oficinas_destino?->nombre }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">{{ $telegrama->nombre_dest }} {{ $telegrama->apellido_dest }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-primary">{{ $telegrama->codigo_envio }}</td>
                                
                                @if($seccion == 2)
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $telegrama->telegrama_recibido->first()->recibido ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $telegrama->telegrama_recibido->first()->recibido ? 'Recibido' : 'En espera' }}
                                        </span>
                                    </td>
                                @endif

                                @if($seccion == 1)
                                    <td class="px-4 py-3 text-center">
                                        @if(!$telegrama->telegrama_recibido->first()->recibido)
                                            <button wire:click="confirmar({{$telegrama->telegrama_recibido->first()}})" class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                                </svg>
                                            </button>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('confirmacion-telegrama.envio-pdf', $telegrama->telegrama_recibido->first()->envio_id) }}"
                                        target="_blank"
                                        class="imprimir text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                            </svg>
                                        </a>

                                        {{-- Contenido oculto para imprimir --}}
                                        <div style="display:none" class="contenido-imprimir" id="contenido-{{ $telegrama->telegrama_recibido->first()->envio_id }}">
                                            <p><strong>Remitente:</strong> {{ $telegrama->nombre_rem }} {{ $telegrama->apellido_rem }}</p>
                                            <p><strong>Oficina:</strong> {{$telegrama->oficinas->nombre}}</p>
                                            <p><strong>Dirección:</strong> {{ $telegrama->direccion_rem }}</p>
                                            <p><strong>Código GIT:</strong> {{ $telegrama->codigo_envio }}</p>
                                            <hr><br>
                                            {!! nl2br(e($telegrama->telegrama_recibido->first()->contenido_telegrama)) !!}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="generar_aviso({{$telegrama->envio_id}})" class="text-orange-600 hover:text-orange-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-red-700">
                                        {{$telegrama->avisos_telegrama->count()}}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-10 text-gray-500 text-lg italic bg-gray-50 text-center">
                                    No hay telegramas {{ $seccion == 1 ? 'por recibir' : 'emitidos' }} en este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN SUPERIOR --}}
            <div class="py-4 px-3">
                {{ $telegramas->links() }}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4 mb-10">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom"
                class="block w-24 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-2 bg-white">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

@push('scripts')
    <script>
        // success alert Livewire
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
@endpush