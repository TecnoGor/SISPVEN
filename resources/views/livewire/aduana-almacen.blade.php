<div>
    @section('titulo')
        Aduana Almacen
    @endsection

    @php
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;
        $oficina = \App\Models\Oficina::where('oficina_id', $oficinaId)->first();
    @endphp

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Envíos en Aduana para {{ $oficina->nombre }}</h1>
            </div>
        </div>

        <div class="flex justify-start mb-4 mt-6 gap-2">
            <x-secondary-button wire:click="toggleEntregados">
                {{ $mostrarEntregados ? 'Ver Disponibles' : 'Ver Entregados' }}
            </x-secondary-button>

<x-secondary-button wire:click="generateEnviosPDF" :disabled="count($envios) === 0">
    Imprimir PDF
</x-secondary-button>

            <x-secondary-button wire:click="exportToExcel" :disabled="count($envios) === 0">
                Exportar Excel
            </x-secondary-button>
        </div>

        <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between p-4">
                <div class="w-full md:w-1/2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                      clip-rule="evenodd" />
                            </svg>
                        </div>
                     <input type="text" wire:model.live.300ms="search"
       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2"
       placeholder="Buscar por código de envío...">

                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm table-auto border border-gray-300">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b text-left">
                        <tr>
                            <th class="px-4 py-2">Código de Envío</th>
                            <th class="px-4 py-2">Contenido</th>
                            <th class="px-4 py-2">Peso</th>
                            @if (!$mostrarEntregados)
                                <th class="px-4 py-2">Estatus</th>
                                <th class="px-4 py-2">Fecha de Entrada</th>
                            @else
                                <th class="px-4 py-2">Fecha de Salida</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($envios as $envio)
                            <tr wire:key="{{ $envio->envio_almacen_id }}" class="border-b bg-white hover:bg-gray-50 text-left">
                                <td class="px-4 py-2 text-gray-800">{{ $envio->codigo_envio }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $envio->contenido }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $envio->peso }}gr</td>

                                @if (!$mostrarEntregados)
                                    <td class="px-4 py-2">
                                        <span class="text-green-600 font-bold">Disponible en Almacen</span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-800">
                                        {{ \Carbon\Carbon::parse($envio->almacenAduana->Entrada)->format('d/m/Y') }}
                                    </td>
                                @else
                                    <td class="px-4 py-2 text-gray-800">
                                        {{ \Carbon\Carbon::parse($envio->almacenAduana->Salida)->format('d/m/Y') }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg">No hay envíos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
