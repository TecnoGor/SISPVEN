@section('titulo')
    Listado de Ventas
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex justify-left items-center mb-4 space-x-4">
        <h1 class="text-3xl text-primary font-bold">Listado de Entregas Internacionales</h1>
    </div>

    <div class="pmax-w-[90%] mx-auto">
        @if (session()->has('mensaje'))
            <x-alert class="bg-green-100 border-green-600 text-green-600 mb-4">
                {{ session('mensaje') }}
            </x-alert>
        @endif

        <div class="flex flex-col md:flex-row">
            <x-tab-link :href="route('envios.listado-ventas')" :active="request()->routeIs('envios.listado-ventas')" wire:navigate.hover>
                {{ __('Todos los Servicios') }}
            </x-tab-link>

            <x-tab-link :href="route('envios.listado-ventas-nacionales')" :active="request()->routeIs('envios.listado-ventas-nacionales')" wire:navigate.hover>
                {{ __('Servicios Nacionales') }}
            </x-tab-link>

            <x-tab-link :href="route('envios.listado-ventas-internacionales')" :active="request()->routeIs('envios.listado-ventas-internacionales')" wire:navigate.hover>
                {{ __('Servicios Internacionales') }}
            </x-tab-link>

            <x-tab-link :href="route('entrega-nacionales')" :active="request()->routeIs('entrega-nacionales')" wire:navigate.hover>
                {{ __('Entregas Nacionales') }}
            </x-tab-link>

            <x-tab-link :href="route('entrega-internacionales')" :active="request()->routeIs('entrega-internacionales')" wire:navigate.hover>
                {{ __('Entregas Internacionales') }}
            </x-tab-link>
        </div>
    </div>

    <div class="bg-white shadow-md sm:rounded-lg overflow-hidden border border-gray-200">
        <div class="flex md:flex-row items-end justify-between p-4">
            <div>
                <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Desde</label>
                <input id="desde" type="date" wire:model.live="desde"
                    class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>
            <div>
                <label for="hasta" class="block text-sm font-medium text-gray-700 mt-4">Hasta</label>
                <input id="hasta" type="date" wire:model.live="hasta"
                    class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Código Envío</th>
                        <th class="px-4 py-3 text-left">Cédula Remitente</th>
                        <th class="px-4 py-3 text-left">Nombre Remitente</th>
                        <th class="px-4 py-3 text-left">Costo Total (Bs)</th>
                        <th class="px-4 py-3 text-left">Costo Aviso (Bs)</th>
                        <th class="px-4 py-3 text-left">Costo Almacenaje (Bs)</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entregas as $entrega)
                        <tr class="border-b text-left">
                            <td class="px-4 py-3">{{ $entrega->codigo_envio }}</td>
                            <td class="px-4 py-3">{{ $entrega->cedula_remitente ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $entrega->nombre_remitente ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ number_format($entrega->costo_total, 2) }} Bs</td>
                            <td class="px-4 py-3">{{ number_format($entrega->coste_aviso, 2) }} Bs</td>
                            <td class="px-4 py-3">{{ number_format($entrega->coste_almacenaje, 2) }} Bs</td>
                            <td class="px-4 py-3">
                                <button onclick="toggleRow('detalle-{{ $entrega->registro_entrega_id }}')" class="text-blue-500">
                                    🔽
                                </button>
                            </td>
                        </tr>
                        <tr id="detalle-{{ $entrega->registro_entrega_id }}" class="hidden">
                            <td colspan="7">
                                @if ($pagos->contains('registro_entrega_id', $entrega->registro_entrega_id))
                                    <div class="bg-gray-100 p-4">
                                        <h3 class="text-lg font-bold text-gray-800">Métodos de Pago</h3>
                                        <ul class="list-disc pl-5">
                                            @foreach ($pagos->where('registro_entrega_id', $entrega->registro_entrega_id) as $pago)
                                                <li>{{ $pago->tipopago->nombre }} - {{ number_format($pago->monto, 2) }} Bs</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-gray-500">No hay pagos registrados para esta entrega.</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="border-b text-center">
                            <td colspan="7" class="py-7 text-gray-500 text-2xl">No hay entregas disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

                <div class="bg-gray-100 p-4 grid grid-cols-2 content-center">
                    <div class="content-center">
                        <div class="text-lg font-bold text-gray-800">Total de envíos: {{$cantidad_envios_internacionales}}</div>
                        <div class="text-lg font-bold text-gray-800">Total de costos de envíos: {{ number_format($total_costo_envios, 2) }}Bs</div>
                    </div>
                    <div class="justify-self-end content-center">
                        @if ($entregas->isEmpty())
                        <button class="mt-8 text-white font-bold py-2 px-4 rounded bg-gray-400 cursor-not-allowed" disabled>
                            Imprimir Reporte
                        </button>
                        <button class="mt-8 ml-4 text-white font-bold py-2 px-4 rounded bg-gray-400 cursor-not-allowed" disabled>
                            Imprimir PDF
                        </button>
                        @else
                        <button wire:click="exportarExcel" class="mt-8 text-white font-bold py-2 px-4 rounded bg-green-500 hover:bg-green-700">
                            Imprimir Reporte
                        </button>
                        <button wire:click="exportarPDF" class="mt-8 ml-4 text-white font-bold py-2 px-4 rounded bg-red-500 hover:bg-red-700">
                            Imprimir PDF
                        </button>
                         @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function toggleRow(id) {
        var row = document.getElementById(id);
        if (row) {
            row.classList.toggle('hidden');
        }
    }
</script>
