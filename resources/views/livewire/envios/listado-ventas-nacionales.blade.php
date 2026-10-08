@section('titulo')
    Listado de Ventas Nacionales
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
                    {{ __('Entregas Internacional') }}
                </x-tab-link>
            </div>
    </div>
    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">

    <div class="flex md:flex-row items-end justify-between p-4">
            @if(auth()->user()->hasRole('Gerente de Estado'))
                <div>
                    <label for="oficina_id" class="block text-sm font-medium text-gray-700">Oficina</label> 
                    <select id="oficina_id" wire:model.live="oficina_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="" selected>Seleccione:</option>
                        <option value="{{ auth()->user()->oficina_id }}" selected>{{ auth()->user()->oficina->nombre }}</option>
                        @foreach ($oficinas as $ofi)
                            <option value="{{ $ofi->oficina_id }}">{{ $ofi->nombre }}</option>
                        @endforeach
                    </select>
                    @error('oficina_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif
            @if(auth()->user()->hasRole('Jefe de OPT'))
                <div>
                    <label for="usuario_id" class="block text-sm font-medium text-gray-700 mt-4">Usuario</label>
                    <select id="usuario_id" wire:model.live.debounce.300ms="usuario_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="" selected>Seleccione:</option>
                        <option value="">Todos los Usuarios</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                    @error('usuario_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif
            <div>
                <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Desde</label>
                <input id="desde" type="date" wire:model.live="desde"
                    class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>
            <div>
                <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Hasta</label>
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
                        <th class="px-4 py-3 text-center text-black">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todos_servicios as $index => $servicio)
                    @if ($envios[$servicio->servicio_id])
                        <tr wire:key="{{ $servicio->servicio_id }}" class="border-b text-left">
                            <th class="px-4 py-3 font-medium text-black">{{ $servicio->nombre }}</th>
                            <th class="px-4 py-3 font-medium text-black">{{ $envios[$servicio->servicio_id] }}</th>
                            @if (!$montos[$servicio->servicio_id])
                                <th class="px-4 py-3 font-medium text-black">0 Bs.</th>
                            @else
                                <th class="px-4 py-3 font-medium text-black">{{ $montos[$servicio->servicio_id] }} Bs.</th>
                            @endif
                            <th class="px-4 py-3 font-medium flex items-center justify-center gap-4">
                                <button href="{{ route('envios.consulta-envios', $servicio->servicio_id) }}" title="Ver Detalles" wire:navigate.hover>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </button>
                                @if(auth()->user()->hasRole('Promotor Integral') || auth()->user()->hasRole('Gerente de Estado') || auth()->user()->hasRole('Jefe de OPT'))
                                <button href="{{ route('envios.cierre-caja', [$servicio->servicio_id, $desde, $hasta, $oficina_id, $usuario_id]) }}" title="Reporte por Servicio" wire:navigate.hover>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </button>
                                @endif
                            </th>
                        </tr>
                    @endif
                    @endforeach
                    @if(array_sum($envios) <= 0)
                        <tr class="border-b text-center">
                            <th colspan="5" class="py-7 text-default text-2xl">
                                @if(auth()->user()->hasRole('Gerente de Estado') && !$oficina_id)
                                    Seleccione una oficina para continuar.
                                @elseif(auth()->user()->hasRole('Jefe de OPT') && (!$usuario_id || $usuario_id == 0))
                                    Seleccione un usuario para continuar.
                                @else
                                No hay envíos
                                @endif
                            </th>
                        </tr>
                    @endif
                </tbody>

            </table>
            
            <div class="bg-gray-100 p-4 grid grid-cols-2 content-center">
                <div class="content-center">
                <div class="text-lg font-bold text-gray-800">Monto Total: {{ $totalCosto }} Bs.</div>
                <div class="text-lg font-bold text-gray-800">Total de envíos: {{ $totalEnvios }}</div>
                </div>
                <div class="justify-self-end content-center">
                @if(auth()->user()->hasRole('Promotor Integral') || auth()->user()->hasRole('Gerente de Estado') || auth()->user()->hasRole('Jefe de OPT'))
                @if(!array_sum($envios) <= 0)
                        <a href="{{ route('envios.cierre-caja-general', [$desde, $hasta, $oficina_id, $usuario_id]) }}">
                        <x-primary-button>
                        Ver Reporte
                        </x-primary-button>
                        </a>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>


