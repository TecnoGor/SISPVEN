@section('titulo')
    facturacion
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-10">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Envíos sin facturar
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Lista de envíos pendientes de facturación
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-200">

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <!-- Table head -->
                <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b">
                    <tr>
                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Fecha'])
                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código'])
                        @include('livewire.includes.sort-table', ['column' => 'nombre_rem', 'displayName' => 'Remitente'])
                        @include('livewire.includes.sort-table', ['column' => 'estado_dest', 'displayName' => 'Oficina'])
                        @include('livewire.includes.sort-table', ['column' => 'contenido', 'displayName' => 'Descripción'])
                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Peso'])
                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Coste'])
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>

                <!-- Table body -->
                <tbody class="divide-y">

                    @forelse($envio as $envi)
                        <tr wire:key="{{ $envi->envio_id }}"
                            class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-gray-600">
                                {{ $envi->created_at }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $envi->codigo_envio }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">
                                    {{ $envi->nombre_rem }} {{ $envi->apellido_rem }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $envi->documento_rem }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $envi->oficinas->nombre }}
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                {{ $envi->contenido }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-md">
                                    {{ $envi->peso }} g
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-md">
                                    {{ $envi->coste }} Bs.
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">

                                <button
                                    wire:click="agregar({{$envi->envio_id}})"
                                    class="p-2 rounded-lg hover:bg-green-50 transition"
                                    title="Agregar envío">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5 text-green-600">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25
                                            2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976
                                            -2.192a48.424 48.424 0 0 0-1.123-.08m-5.801
                                            0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5
                                            a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664" />
                                    </svg>

                                </button>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-400">
                                No hay envíos disponibles
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>


    <!-- Selected shipments -->
    <div class="mt-8">

        <h3 class="text-lg font-semibold text-gray-800 mb-3">
            Envíos seleccionados
        </h3>

        <div class="bg-white border rounded-xl p-4">

            <ul class="space-y-2">

                @forelse($envios_sel as $envio)
                    <li class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="font-medium text-gray-700">
                            Envío #{{ $envio->codigo_envio }}
                        </span>

                        <span class="text-xs text-gray-500">
                            listo para facturar
                        </span>
                    </li>

                @empty
                    <li class="text-gray-400 text-sm">
                        Sin envíos seleccionados
                    </li>
                @endforelse

            </ul>

        </div>

        <button 
            type="button"
            wire:click="pagar"
            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition duration-200">
            Pagar
        </button>

    </div>

            @if($cancelar_pago)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm overflow-y-auto p-4">
                    <div class="relative bg-white shadow-2xl rounded-xl max-w-4xl w-full overflow-hidden">
                        
                        <div class="bg-gray-50 border-b px-6 py-4 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Finalizar Pago</h3>
                            <button wire:click="cerrar_pago" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <livewire:caja-de-pago.caja-de-pago/>
                                

                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                    <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider border-b pb-2">Detalle de la Orden</h4>
                                    <div class="space-y-3">
                                        @foreach ($totales_por_servicio as $servicio_id => $totales)
                                            @php $servicio = \App\Models\Servicio::find($servicio_id); @endphp
                                            <div class="flex justify-between items-start text-sm">
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-800">{{ $servicio->nombre }}</span>
                                                    <span class="text-xs text-gray-500">Cantidad: {{ $totales['cant'] }}</span>
                                                </div>
                                                <span class="font-semibold text-gray-700">{{ number_format($totales['total'], 2) }} Bs</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-300">
                                        <div class="flex justify-between items-center">
                                            <span class="text-base font-bold text-gray-800">Total a Pagar:</span>
                                            <span class="text-xl font-black text-red-600">{{ $total_lote }} Bs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3">
                            <x-button type="button" wire:click="cerrar_pago" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</x-button>
                            <x-button type="button" wire:click="aprobar_pago" class="bg-green-600 hover:bg-green-700 text-white px-8">Aprobar Pago</x-button>
                        </div>
                    </div>
                </div>
            @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('tarifaUpdated', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
@endpush
