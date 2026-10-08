@section('titulo')
    Consulta de Rastreo y Seguimiento
@endsection

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex gap-3">
            <x-return-link :href="route('dashboard')" wire:navigate.hover />
            {{ __('Consultar Rastreo y Seguimiento') }}
        </h2>
    </x-slot>

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto mt-8">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Consulta de Rastreo y Seguimiento</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y localización de envíos para telegramas.</p>
    </div>

    <div class="max-w-[95%] mx-auto">
        @if (session()->has('mensaje'))
            <div class="sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('mensaje') }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">
            
            <div class="px-4 mb-2">
                <label for="buscar" class="text-sm font-medium text-gray-700">Buscar por:</label>
            </div>

            {{-- BARRA DE BÚSQUEDA INTEGRADA --}}
            <div class="px-4 pb-3 mx-auto mb-5 flex">
                {{-- SELECT --}}
                <select wire:model.blur="buscar" id="buscar" 
                    class="py-2.5 bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-l-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] w-3/12 transition duration-150">
                    <option selected>--Seleccione--</option>
                    <option value="1">Código de Envío</option>
                    <option value="2">C.I. Remitente</option>
                    <option value="3">C.I. Destinatario</option>
                </select>

                {{-- INPUT --}}
                <input 
                    type="{{ $isNumericInput ? 'number' : 'text' }}" 
                    wire:model.debounce.500ms="search" 
                    id="search" 
                    class="py-2.5 bg-gray-50 border-t border-b border-gray-300 text-gray-800 text-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] w-8/12 transition duration-150" 
                    maxlength="20"
                    placeholder="@if($buscar == 1) Inserte Número de Envío @endif @if($buscar == 2) Inserte Cédula de Identidad del Remitente @endif @if($buscar == 3) Inserte Cédula de Identidad del Destinatario @endif"
                    @if($isNumericInput) oninput="this.value = this.value.replace(/[^0-9]/g, '')" @endif
                    wire:keydown.enter="getPathing"
                >

                {{-- BOTÓN --}}
                <x-button 
                    style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                    class="py-2 rounded-r-lg w-1/12 bg-primary font-bold text-white border border-primary" 
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
            @if($envio)
                <div class="overflow-x-auto rounded-lg border border-gray-100 p-0 mt-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Creado'])
                                @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código de Envío'])
                                @include('livewire.includes.sort-table', ['column' => 'nombre_rem', 'displayName' => 'Remitente'])
                                @include('livewire.includes.sort-table', ['column' => 'nombre_dest', 'displayName' => 'Destinatario'])
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($envio as $envi)
                                <tr wire:key="{{ $envi->envio_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $envi->created_at ? \Carbon\Carbon::parse($envi->created_at)->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $envi->codigo_envio }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $envi->nombre_rem }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $envi->nombre_dest }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button href="{{ route('encaminamiento.encaminamiento-detalles', $envi->envio_id) }}" 
                                            class="text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50 transition"
                                            title="Detalles" wire:navigate.hover>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>