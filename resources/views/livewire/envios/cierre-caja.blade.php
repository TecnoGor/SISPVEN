@section('titulo')
    Reporte por Servicio
@endsection

<div>
    <div class="flex justify-center items-center mb-1 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('envios.listado-ventas')" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-primary font-bold">Reporte por Servicio</h1>
    </div>

    <div class="py-12 max-w-[90%] mx-auto">
        @if (session()->has('mensaje'))
            <x-alert class="bg-green-100 border-green-600 text-green-600 mb-4">
                {{ session('mensaje') }}
            </x-alert>
        @endif
    </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">       
                <div class="overflow-x-auto w-full">
                        
                        <table class="w-full text-sm">

                            @if($registros->isNotEmpty())
                                <thead class="text-xs text-default uppercase bg-gray-100">
                                
                                    <tr class="">
                                    @include('livewire.includes.sort-table', ['column' => 'servicio', 'displayName' => 'Servicio'])
                                    @include('livewire.includes.sort-table', ['column' => 'cantidad', 'displayName' => 'Cantidad'])
                                    @include('livewire.includes.sort-table', ['column' => 'iva', 'displayName' => 'Tarifa Base'])
                        
                                    @if($subservicios)
                                        @foreach($subservicios as $nombre => $monto)
                                            @include('livewire.includes.sort-table', ['column' => 'subservicio', 'displayName' => $nombre])
                                        @endforeach
                                    @endif

                                    @include('livewire.includes.sort-table', ['column' => 'iva', 'displayName' => 'IVA'])
                                    @include('livewire.includes.sort-table', ['column' => 'sobrante', 'displayName' => 'Sobrante'])
                                    @include('livewire.includes.sort-table', ['column' => 'total', 'displayName' => 'Total'])

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr wire:key="" class="border-b text-left">
                                        <th class="px-4 py-3 font-medium text-black">{{ $servicio->nombre }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $cantidad }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $envio_base }} Bs.</th>
                                            @if($subservicios)
                                                @foreach($subservicios as $nombre => $monto)
                                                    <th class="px-4 py-3 font-medium text-black">{{ $monto }} Bs. 
                                                @endforeach
                                            @endif
                                            <th class="px-4 py-3 font-medium text-black">{{ $iva }} Bs.</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $sobrante }} Bs.</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $total_pagado }} Bs.</th>
                                        @endif
                                    </tr>
                                </tbody>
                                @if (empty($cantidad))
                                        <tr class="border-b text-center">
                                            <th colspan="5" class="py-7 text-default text-2xl">No hay Envíos</th>
                                        </tr>
                                    @endif
                            </table>
                        </div>
                        

                        @if($registros->isNotEmpty())
                        <br>
                        <center><h1 class="text-2xl md:text-3xl text-primary font-bold">Métodos de Pago</h1></center>
                        <div class="overflow-x-auto mt-2">
                            <table class="w-full text-sm">

                                <thead class="text-xs text-default uppercase bg-gray-100">
                                
                                    <tr class="">
                                        @foreach($metodos_pago as $nombre => $mont)
                                            @include('livewire.includes.sort-table', ['column' => 'metodos de pago', 'displayName' => $nombre])
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr wire:key="" class="border-b text-left">
                                        @foreach($metodos_pago as $name => $monto)
                                            <th class="px-4 py-3 font-medium text-black">{{ $monto ?? 0}} Bs. 
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
