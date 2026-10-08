@section('titulo')
    Listado de Ventas
@endsection
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex justify-left items-center mb-4 space-x-4">
        <h1 class="text-3xl text-primary font-bold">Listado de Entregas de {{$servicio->nombre}}</h1>
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
    
                <x-tab-link :href="route('entrega-nacionales')" :active="request()->routeIs('entrega-nacionales')" wire:navigate.hover>
                    {{ __('Entregas Internacional') }}
                </x-tab-link>
            </div>
    </div>
    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
        <div class="flex md:flex-row items-end justify-between p-4">
            <div>
            </div>
            <div>
            </div>
                
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr class="">
                        @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Envio'])
                        @include('livewire.includes.sort-table', ['column' => 'envios', 'displayName' => 'Cedula Remitente'])
                        @include('livewire.includes.sort-table', ['column' => 'montos', 'displayName' => 'Nombre remitente'])
                        @include('livewire.includes.sort-table', ['column' => 'montos', 'displayName' => 'Total'])
                        @include('livewire.includes.sort-table', ['column' => 'montos', 'displayName' => 'costo por aviso'])
                        @include('livewire.includes.sort-table', ['column' => 'montos', 'displayName' => 'Costo por almacenaje'])
                        <th class="px-4 py-3 text-left text-black">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entregas as $entrega)
                        <tr wire:key="{{ $entrega->servicio_id }}" class="border-b text-left">
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->codigo_envio}}</th>
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->cedula_remitente}}</th>
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->nombre_remitente}}</th>
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->costo_total}} Bs</th>
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->coste_aviso}} Bs</th>
                            <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->coste_almacenaje}} Bs</th>
                            <th class="px-4 py-3 font-medium text-black text-left">
                                <!-- Botón para desplegar -->
                                <button onclick="toggleRow('detalle-{{ $entrega->servicio_id }}')">
                                    🔽
                                </button>
                            </th>
                        </tr>
                        <!-- Fila Oculta con información de pagos -->
                        <tr id="detalle-{{ $entrega->servicio_id }}" class="hidden">
                            <td colspan="7" class="px-4 py-3 bg-gray-100">
                                <strong>Detalles de Pago:</strong>
                                <ul>
                                    @foreach ($pagos as $pago)
                                    <p>{{ $pago->tipo_pago_nombre }} - Total Monto: {{ number_format($pago->total_monto, 2) }}Bs</p>
                                @endforeach
                                
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-b text-center">
                            <th colspan="8" class="py-7 text-default text-2xl">No hay Entregas</th>
                        </tr>
                    @endforelse
                </tbody>
                
                <script>
                    function toggleRow(id) {
                        var row = document.getElementById(id);
                        row.classList.toggle('hidden');
                    }
                </script>
                

            </table>
            
            <div class="bg-gray-100 p-4 grid grid-cols-2 content-center">
               
            </div>
        </div>
    </div>
</div>
<script>
    function toggleRow(id) {
        var row = document.getElementById(id);
        row.classList.toggle('hidden');
    }
</script>