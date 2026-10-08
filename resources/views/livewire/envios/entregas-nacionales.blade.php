@section('titulo')
    Listado de Ventas
@endsection
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex justify-left items-center mb-4 space-x-4">
        <h1 class="text-3xl text-primary font-bold">Listado de Ventas</h1>
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
    
    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
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
                    <tr class="">
                        @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Servicio'])
                        @include('livewire.includes.sort-table', ['column' => 'envios', 'displayName' => 'Cantidad'])
                        @include('livewire.includes.sort-table', ['column' => 'montos', 'displayName' => 'Monto'])
                        <th class="px-4 py-3 text-left text-black">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entregas as $entrega)
                    <tr wire:key="{{ $entrega->servicio_id }}" class="border-b text-left">
                        <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->servicio->nombre }}</th>
                        <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->total_envios}}</th>
                        <th class="px-4 py-3 font-medium text-black text-left">{{ $entrega->costo_total}} Bs</th>
                        <th class="px-4 py-3 font-medium text-black text-left">
                            @if(auth()->user()->hasRole('Promotor Integral'))
                            <button href="{{ route('entrega-nacionales-detalles', [$entrega->servicio_id, $desde, $hasta]) }}" title="Detalle por Servicio" wire:navigate.hover>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </button>
                            @endif
                        </th>
                    </tr>
                @empty
                    <tr class="border-b text-center">
                        <th colspan="8" class="py-7 text-default text-2xl">No hay Entregas</th>
                    </tr>
                @endforelse
                </tbody>
            </table>
            
            <div class="bg-gray-100 p-4 grid grid-cols-2 content-center">
                <div class="content-center">
                    <div class="text-lg font-bold text-gray-800">Monto Total: {{$totalMontos}}Bs.</div>
                    <div class="text-lg font-bold text-gray-800">Total de envíos: {{$totalEnvios}}</div>
                </div>
                <div class="justify-self-end content-center">
                    <button wire:click="exportarExcel"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400"
                        @if($entregas->isEmpty()) disabled @endif>
                        Imprimir Reporte
                    </button>
                    <button wire:click="exportarPDF"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:bg-gray-400"
                        @if($entregas->isEmpty()) disabled @endif>
                        Imprimir PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
