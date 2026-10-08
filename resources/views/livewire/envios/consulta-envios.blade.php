@section('titulo')
    Consulta de Envios
@endsection

<div>
    <div class="flex justify-center items-center mb-4 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('envios.listado-ventas')" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-primary font-bold">Consulta de Envíos</h1>
    </div>
    
    <div class="py-12 max-w-[90%] mx-auto">
        @if (session()->has('mensaje'))
            <x-alert class="bg-green-100 border-green-600 text-green-600 mb-4">
                {{ session('mensaje') }}
            </x-alert>
        @endif
    </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg rounded-lg overflow-hidden border border-gray-200 pt-6">
                <div class="px-10 mx-auto">
                    <label for="buscar" class="">Buscar por:</label><br>
                </div>
                <div class="px-10 pb-3 mx-auto flex">
                    <select wire:model.blur="buscar" id="buscar" class="py-2 rounded-l-lg w-3/12">
                        <option selected>--Seleccione--</option>
                        <option value="1">Código de Envío</option>
                        <option value="2">Cédula de Identidad</option>
                    </select><input 
                        type="{{ $isNumericInput ? 'number' : 'text' }}" 
                        wire:model.blur="search" 
                        id="search" 
                        class=" py-2 w-8/12" 
                        maxlength="20"
                        @if($isNumericInput) oninput="this.value = this.value.replace(/[^0-9]/g, '')" @endif
                        wire:keydown.enter="getEnvio"
                    ><x-button 
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                        class="py-2 rounded-r-lg w-1/12 bg-primary font-bold text-white border border-primary" 
                        wire:loading.attr="disabled" 
                        wire:target="getEnvio" 
                        wire:click="getEnvio"
                    >
                        
                            <svg wire:loading.attr="class" wire:loading.attr.class="hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        
                        <x-loading-button wire:target="getEnvio"  />
                        
                    </x-button>
                    
                </div>     
                <div class="px-10 mx-auto mb-5 grid grid-cols-2">
                    <div class="mx-5">
                    <label for="desde" class="block text-sm font-medium text-gray-700">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full">
                        </div>
                        <div class="mx-5">
                        <label for="desde" class="block text-sm font-medium text-gray-700">Hasta</label>
                        <input id="hasta" type="date" wire:model.live="hasta"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full">
                    </div>
                </div>

                @if($envio)
                    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">    
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs text-default uppercase bg-gray-100">
                                    <tr class="">
                                        @if ($envio->pluck('created_at')->filter()->isNotEmpty())
                                        @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Creado'])
                                        @endif
                                        @if ($envio->pluck('codigo_envio')->filter()->isNotEmpty())
                                            @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código de Envío'])
                                        @endif
                                        @if ($envio->pluck('nombre_rem')->filter()->isNotEmpty())
                                        @include('livewire.includes.sort-table', ['column' => 'nombre_rem', 'displayName' => 'Remitente'])
                                        @endif
                                        @if ($envio->pluck('nombre_dest')->filter()->isNotEmpty())
                                            @include('livewire.includes.sort-table', ['column' => 'nombre_dest', 'displayName' => 'Destinatario'])
                                        @endif
                                        @if ($envio->pluck('oficina_id')->filter()->isNotEmpty())
                                        @include('livewire.includes.sort-table', ['column' => 'oficinas->nombre', 'displayName' => 'Oficina Origen'])
                                        @endif
                                        @if ($envio->pluck('contenido')->filter()->isNotEmpty())
                                            @include('livewire.includes.sort-table', ['column' => 'contenido', 'displayName' => 'Descripción'])
                                        @endif
                                        @if ($envio->pluck('peso')->filter()->isNotEmpty())
                                            @include('livewire.includes.sort-table', ['column' => 'peso', 'displayName' => 'Peso'])
                                        @endif
                                        @if ($envio->pluck('coste')->filter()->isNotEmpty())
                                            @include('livewire.includes.sort-table', ['column' => 'coste', 'displayName' => 'Coste'])
                                        @endif
                                        @if ($envio->isNotEmpty())
                                            <th class="px-4 py-3 text-center text-black">
                                                Acciones
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($envio->isEmpty())
                                        <tr class="border-b text-center">
                                            <th colspan="5" class="py-7 text-default text-2xl">No hay Envíos</th>
                                        </tr>
                                    @endif
                                    @foreach($envio as $envi)
                                        <tr wire:key="{{ $envi->envio_id }}" class="border-b text-left">
                                            @if ($envio->pluck('created_at')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->created_at }}</th>
                                            @endif
                                            @if ($envio->pluck('codigo_envio')->filter()->isNotEmpty())
                                                <th class="px-4 py-3 font-medium text-black">{{ $envi->codigo_envio }}</th>
                                            @endif
                                            @if ($envio->pluck('nombre_rem')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->nombre_rem }} {{ $envi->apellido_rem }} - {{ $envi->documento_rem }}</th>
                                            @endif
                                            @if ($envio->pluck('nombre_dest')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->nombre_dest }} {{ $envi->apellido_dest }} - {{ $envi->documento_dest }}</th>
                                            @endif
                                            @if ($envio->pluck('oficina_id')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->oficinas->nombre }}</th>
                                            @endif
                                            @if ($envio->pluck('contenido')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->contenido }}</th>
                                            @endif
                                            @if ($envio->pluck('peso')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->peso }} g</th>
                                            @endif  
                                            @if ($envio->pluck('coste')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->coste }} Bs.</th>
                                            @endif
                                            <th class="px-4 py-3 font-medium flex items-center justify-center gap-4">
                                                <button href="{{ route('envios.detalles-envios', ['servicio_id' => $servicio_id, 'envio' => $envi->envio_id]) }}" title="Detalles" wire:navigate.hover>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                    </svg>
                                                </button>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
