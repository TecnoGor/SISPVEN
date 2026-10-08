@section('titulo')
    Consulta de Valijas
@endsection

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex gap-3">
            <x-return-link :href="route('dashboard')" wire:navigate.hover />
            {{ __('Consulta de Valijas') }}
        </h2>
    </x-slot>

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto mt-8">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Consulta de Valijas</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y localización de valijas (sacas).</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">
            
            <div class="px-4 mb-2">
                <label for="search" class="text-sm font-medium text-gray-700">Buscar por Código de Valija:</label>
            </div>

            {{-- BARRA DE BÚSQUEDA INTEGRADA --}}
            <div class="px-4 pb-3 mx-auto mb-5 flex">

                {{-- INPUT --}}
                <input 
                    type="text" 
                    wire:model.debounce.500ms="search" 
                    id="search" 
                    class="py-2.5 bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-l-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] w-11/12 transition duration-150" 
                    maxlength="50"
                    placeholder="Inserte Código de Valija"
                    wire:keydown.enter="getPathing"
                >

                {{-- BOTÓN --}}
                <x-button 
                    style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                    class="py-2 rounded-r-lg w-1/12 bg-primary font-bold text-white border border-primary flex justify-center items-center" 
                    wire:loading.attr="disabled" 
                    wire:target="getPathing" 
                    wire:click="getPathing"
                >
                    <svg wire:loading.attr="class" wire:loading.attr.class="hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <x-loading-button wire:target="getPathing"  />
                </x-button>
            </div>     

            {{-- TABLA DE RESULTADOS --}}
            @if($sacas)
                <div class="overflow-x-auto rounded-lg border border-gray-100 p-0 mt-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Creado'])
                                @include('livewire.includes.sort-table', ['column' => 'codigo_saca', 'displayName' => 'Código Valija'])
                                @include('livewire.includes.sort-table', ['column' => 'oficina_id', 'displayName' => 'Oficina Origen'])
                                @include('livewire.includes.sort-table', ['column' => 'oficina_destino_id', 'displayName' => 'Oficina Destino'])
                                @include('livewire.includes.sort-table', ['column' => 'peso', 'displayName' => 'Peso (gr)'])
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        @foreach($sacas as $saca)
                            <tbody x-data="{ open: false }" class="bg-white divide-y divide-gray-200">
                                <tr wire:key="row-{{ $saca->saca_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $saca->created_at ? \Carbon\Carbon::parse($saca->created_at)->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $saca->codigo_saca }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $saca->oficinaOrigen ? $saca->oficinaOrigen->nombre : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $saca->oficinaDestino ? $saca->oficinaDestino->nombre : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $saca->peso ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($saca->cerrado)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Cerrada</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Abierta</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button @click="open = !open" type="button" class="text-[#6b1820] hover:text-red-900 focus:outline-none flex items-center justify-center mx-auto gap-1">
                                            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>

                                            <svg x-show="open" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                            <span x-show="!open" class="text-xs">Ver Envíos</span>
                                            <span x-show="open" style="display: none;" class="text-xs">Ocultar Envíos</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- EXPANDABLE ROW -->
                                <tr x-show="open" style="display: none;" wire:key="envios-{{ $saca->saca_id }}" class="bg-gray-50 border-b">
                                    <td colspan="7" class="px-6 py-4">
                                        {{-- ENCAMINAMIENTO DE LA VALIJA (creada / tránsito / recibida).
                                            Se muestra siempre, incluso para la valija de peso que no tiene envíos. --}}
                                        <div class="text-sm text-gray-800 mb-5">
                                            <div class="font-bold border-b border-gray-200 pb-2 mb-3 text-[#6b1820]">
                                                Encaminamiento de la valija ({{ $saca->encaminamientos->count() }}):
                                            </div>
                                            @if($saca->encaminamientos && $saca->encaminamientos->count() > 0)
                                                <div class="overflow-x-auto pb-2">
                                                    <ol class="flex items-start min-w-max">
                                                        @foreach($saca->encaminamientos as $mov)
                                                            <li class="flex flex-col items-center text-center px-4 relative">
                                                                {{-- Línea conectora hacia el siguiente hito (no en el último) --}}
                                                                @unless($loop->last)
                                                                    <div class="absolute top-1.5 left-1/2 w-full h-0.5 bg-gray-300"></div>
                                                                @endunless
                                                                {{-- Punto del hito --}}
                                                                <div class="relative z-10 w-3 h-3 bg-[#6b1820] rounded-full mb-2"></div>
                                                                <span class="font-semibold">{{ $mov->sacaEstatus->nombre ?? 'N/A' }}</span>
                                                                <span class="text-xs text-gray-600">
                                                                    {{ $mov->oficina->nombre ?? 'N/A' }}
                                                                </span>
                                                                <span class="text-xs text-gray-500 mt-1">
                                                                    {{ $mov->created_at ? \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i:s') : '-' }}
                                                                </span>
                                                                @if($mov->usuario)
                                                                    <span class="text-xs text-gray-400">{{ $mov->usuario->name ?? '' }}</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500 italic p-2">Esta valija aún no registra movimientos de encaminamiento.</div>
                                            @endif
                                        </div>

                                        @if($saca->envios && $saca->envios->count() > 0)
                                            <div class="text-sm text-gray-800">
                                                <div class="font-bold border-b border-gray-200 pb-2 mb-3 text-[#6b1820]">
                                                    Envíos contenidos en la valija ({{ $saca->envios->count() }}):
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach($saca->envios as $envio)
                                                        <a href="{{ route('encaminamiento.encaminamiento-detalles', $envio->envio_id) }}" target="_blank" class="flex items-center gap-2 p-2 bg-white rounded shadow-sm border border-gray-200 hover:border-[#6b1820] hover:bg-red-50 transition-colors group cursor-pointer">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 group-hover:text-[#6b1820] transition-colors">
                                                              <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                                            </svg>
                                                            <div class="flex flex-col">
                                                                <span class="font-semibold group-hover:text-[#6b1820] transition-colors">{{ $envio->codigo_envio }}</span>
                                                                <span class="text-xs text-gray-500">Peso: {{ $envio->peso }} gr | Último Estatus: {{ $envio->envio_encaminamientos->sortBy('envios_encaminamiento_id')->last()->envio_estatus->estatus ?? 'N/A' }}</span>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500 italic p-4 text-center">No hay envíos registrados o contenidos en esta valija actualmente.</div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
