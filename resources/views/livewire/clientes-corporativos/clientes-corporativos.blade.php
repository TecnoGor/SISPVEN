<div>
    @section('titulo') Clientes Corporativos @endsection
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Clientes Corporativos</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de clientes corporativos.</p>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    {{-- Buscador --}}
                    <div class="flex items-center w-full md:w-1/3">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar...">
                        </div>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="modalOpen">
                            Agregar Cliente
                        </x-primary-button>

                        <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="modalOpenContrato">
                            Crear Contrato
                        </x-primary-button>

                        <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="modalOpenActualizar">
                            Actualizar Cliente
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start border-t border-gray-100 pt-4">
                <div class="w-full md:w-1/4">
                    <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 w-1/4">Razon Social</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 w-1/4">Documento</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 w-1/4">Agente Autorizado</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Contrato</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Direcciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($clientes as $index => $cliente)
                            <tr wire:key="{{ $cliente->cliente_corporativo_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    {{ $cliente->razon_social }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $cliente->tipo_documento . '-' . $cliente->numero_documento }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $cliente->agente_autorizado }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <button wire:click="verContrato({{ $cliente->cliente_corporativo_id }})" class="text-green-600 hover:text-green-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <button wire:click="abrirDirecciones({{ $cliente->cliente_corporativo_id }})"
                                        class="text-primary hover:text-red-800 transition" title="Ver direcciones">
                                        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="4" class="py-7 text-gray-500 text-lg italic bg-gray-50">
                                    No hay clientes disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>

    {{-- PAGINACIÓN INFERIOR --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom"
                class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>
    
   @if($modal_open)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1M9 13h1M9 17h1m4-10h1m4 6h1m-4 6h1" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                Registro de Cliente Corporativo
                            </h3>
                        </div>

                        <button type="button" wire:click="modalClose" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit="submit">
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">

                                <div class="col-span-1">
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo Doc.<span class="text-red-500">*</span></label>
                                    <select id="tipo_documento" wire:model.lazy="tipo_documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">Nro Documento<span class="text-red-500">*</span></label>
                                    <input type="number" id="documento" wire:model.live="documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-1">Razón Social<span class="text-red-500">*</span></label>
                                    <input type="text" id="razon_social" wire:model.lazy="razon_social" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('razon_social') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label for="agente" class="block text-sm font-medium text-gray-700 mb-1">Representante Legal<span class="text-red-500">*</span></label>
                                    <input type="text" id="agente" wire:model.live="agente_autorizado" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('agente_autorizado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado<span class="text-red-500">*</span></label>
                                    <select id="estado" wire:model.lazy="estado" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($estados as $est)
                                            <option value="{{$est->estado_id}}">{{$est->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio<span class="text-red-500">*</span></label>
                                    <select id="municipio" wire:model.lazy="municipio" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($municipios as $mun)
                                            <option value="{{$mun->municipio_id}}">{{$mun->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="parroquia" class="block text-sm font-medium text-gray-700 mb-1">Parroquia<span class="text-red-500">*</span></label>
                                    <select id="parroquia" wire:model.lazy="parroquia" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($parroquias as $par)
                                            <option value="{{$par->parroquia_id}}">{{$par->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Cód. Postal<span class="text-red-500">*</span></label>
                                    <select id="codigo_postal" wire:model.lazy="codigo_postal" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($codigos_postales as $cp)
                                            <option value="{{$cp}}">{{$cp}}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="latitud" class="block text-sm font-medium text-gray-700 mb-1">Latitud<span class="text-red-500">*</span></label>
                                    <input type="text" id="latitud" wire:model.live="latitud" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('latitud') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="longitud" class="block text-sm font-medium text-gray-700 mb-1">Longitud<span class="text-red-500">*</span></label>
                                    <input type="text" id="longitud" wire:model.live="longitud" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('longitud') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono<span class="text-red-500">*</span></label>
                                    <input type="text" id="telefono" wire:model="telefono" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('telefono') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <input type="email" id="correo" wire:model="correo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-4">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección Exacta</label>
                                    <textarea wire:model="direccion" id="direccion" rows="2" maxlength="150" style="resize: none;"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"></textarea>
                                    @error('direccion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button 
                                type="submit" 
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Guardar Registro
                            </x-button>

                            <x-button 
                                type="button" 
                                wire:click="modalClose" 
                                class="px-5 py-2.5 bg-gray-200 text-white hover:bg-gray-300 rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Cancelar
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    @if($actualizar_cliente)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">

                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                Actualizar Cliente Corporativo
                            </h3>
                        </div>

                        <button type="button" wire:click="modalClose" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="Actualizar">
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">

                                <div class="col-span-1 sm:col-span-2">
                                    <label for="cliente_actualizacion" class="block text-sm font-medium text-gray-700 mb-1">Cliente Corporativo<span class="text-red-500">*</span></label>
                                    <select id="cliente_actualizacion" wire:model.live="cliente_actualizacion" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($cliente_act as $cliente)
                                            <option value="{{$cliente->cliente_corporativo_id}}">{{$cliente->razon_social}}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente_actualizacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                                    <select id="tipo_documento" wire:model.live="tipo_documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Documento</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">Nro Documento</label>
                                    <input type="text" id="documento" wire:model.live="documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-1">Razón Social</label>
                                    <input type="text" id="razon_social" wire:model.live="razon_social" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('razon_social') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label for="agente" class="block text-sm font-medium text-gray-700 mb-1">Agente Autorizado</label>
                                    <input type="text" id="agente" wire:model.live="agente_autorizado" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('agente_autorizado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <select id="estado" wire:model.live="estado" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Estado</option>
                                        @foreach ($estados as $est)
                                            <option value="{{$est->estado_id}}">{{$est->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                                    <select id="municipio" wire:model.live="municipio" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Municipio</option>
                                        @foreach ($municipios as $mun)
                                            <option value="{{$mun->municipio_id}}">{{$mun->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="parroquia" class="block text-sm font-medium text-gray-700 mb-1">Parroquia</label>
                                    <select id="parroquia" wire:model.live="parroquia" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Parroquia</option>
                                        @foreach ($parroquias as $par)
                                            <option value="{{$par->parroquia_id}}">{{$par->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Cód. Postal</label>
                                    <select id="codigo_postal" wire:model.live="codigo_postal" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Codigo</option>
                                        @foreach ($codigos_postales as $cp)
                                            <option value="{{$cp}}">{{$cp}}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="latitud" class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                                    <input type="text" id="latitud" wire:model.live="latitud" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('latitud') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="longitud" class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                                    <input type="text" id="longitud" wire:model.live="longitud" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('longitud') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="text" id="telefono" wire:model.live="telefono" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('telefono') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <input type="email" id="correo" wire:model.live="correo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-4">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección Exacta</label>
                                    <textarea wire:model.live="direccion" id="direccion" rows="2" maxlength="150" style="resize: none;"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"></textarea>
                                    @error('direccion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button 
                                type="button" 
                                wire:click='Actualizar'
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Actualizar Datos
                            </x-button>

                            <x-button 
                                type="button" 
                                wire:click="modalClose" 
                                class="px-5 py-2.5 bg-gray-200 text-white hover:bg-gray-300 rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Cancelar
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif




    @if($mostrar_contrato)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Encabezado --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Contrato Activo</p>
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $cliente_razon }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="cerrarMostrar"
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Contenido --}}
                <div class="p-6 space-y-4">
                    @if($ultimo_contrato)
                        @php
                            $peso_restante    = $ultimo_contrato->peso_contrato - ($ultimo_contrato->peso_utilizado ?: 0);
                            $envios_restantes = $ultimo_contrato->cant_envios - $ultimo_contrato->cant_envios_utilizados;
                        @endphp

                        {{-- Sección 1: Info general --}}
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Información general</p>
                            </div>
                            <div class="grid grid-cols-2 divide-x divide-gray-100">
                                <div class="px-4 py-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo</p>
                                    <p class="text-sm font-bold text-gray-800">{{ $ultimo_contrato->tipo_contrato->descripcion }}</p>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estatus</p>
                                    <button wire:click="cambiar_estatus({{ $ultimo_contrato->contrato_corporativo_id }})"
                                        title="Click para cambiar estatus"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest transition active:scale-95
                                            {{ $ultimo_contrato->activo ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                        {{ $ultimo_contrato->activo ? 'Activo' : 'Inactivo' }}
                                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Coste total</p>
                                    <p class="text-sm font-bold text-green-700">
                                        {{ number_format($ultimo_contrato->tarifa, 2, ',', '.') }}
                                        {{ $ultimo_contrato->divisa->nombre ?? '' }}
                                    </p>
                                </div>
                                <div class="px-4 py-3 col-span-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Vigencia</p>
                                    <p class="text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($ultimo_contrato->fecha_inicio)->format('d/m/Y') }}
                                        <span class="text-gray-400 mx-1">→</span>
                                        {{ \Carbon\Carbon::parse($ultimo_contrato->fecha_fin)->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Sección 2: Peso --}}
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Peso</p>
                            </div>
                            <div class="grid grid-cols-3 divide-x divide-gray-100">
                                <div class="px-4 py-3 text-center">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contratado</p>
                                    <p class="text-sm font-bold text-gray-800">{{ number_format($ultimo_contrato->peso_contrato) }} <span class="text-xs text-gray-400">gr</span></p>
                                </div>
                                <div class="px-4 py-3 text-center">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Consumido</p>
                                    <p class="text-sm font-bold text-gray-800">{{ number_format($ultimo_contrato->peso_utilizado ?: 0) }} <span class="text-xs text-gray-400">gr</span></p>
                                </div>
                                <div class="px-4 py-3 text-center">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Restante</p>
                                    <p class="text-sm font-bold {{ $peso_restante <= 0 ? 'text-red-600' : 'text-blue-600' }}">{{ number_format($peso_restante) }} <span class="text-xs">gr</span></p>
                                </div>
                            </div>
                        </div>

                        {{-- Sección 3: Envíos (solo si aplica) --}}
                        @if($ultimo_contrato->cant_envios > 0)
                            <div class="rounded-xl border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cantidad de envíos</p>
                                </div>
                                <div class="grid grid-cols-3 divide-x divide-gray-100">
                                    <div class="px-4 py-3 text-center">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contratados</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $ultimo_contrato->cant_envios }}</p>
                                    </div>
                                    <div class="px-4 py-3 text-center">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Consumidos</p>
                                        <p class="text-sm font-bold text-gray-800">{{ $ultimo_contrato->cant_envios_utilizados }}</p>
                                    </div>
                                    <div class="px-4 py-3 text-center">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Restantes</p>
                                        <p class="text-sm font-bold {{ $envios_restantes <= 0 ? 'text-red-600' : 'text-blue-600' }}">{{ $envios_restantes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="py-10 text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">No hay contrato activo para este cliente.</p>
                        </div>
                    @endif
                </div>

                {{-- Pie --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" wire:click="cerrarMostrar"
                        class="px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-md shadow-primary/20 transition active:scale-95">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif


    @if($crear_contrato)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">


                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                Creación de Nuevo Contrato
                            </h3>
                        </div>
                        <button type="button" wire:click="cerrar" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                                <div class="md:col-span-1">
                                    <label for="cliente_corporativo" class="block text-sm font-medium text-gray-700 mb-1">Cliente Corporativo<span class="text-red-500">*</span></label>
                                    <select id="cliente_corporativo" wire:model.lazy="cliente_corporativo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($cliente_act as $cliente)
                                            <option value="{{$cliente->cliente_corporativo_id}}"> {{$cliente->razon_social}} </option>
                                        @endforeach
                                    </select>
                                    @error('cliente_corporativo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-1 p-2 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-center">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo de Contrato</label>
                                    <p class="text-sm font-bold text-green-700 mt-1">
                                        {{$tipo_contrato->descripcion ?? 'Ninguno'}}
                                    </p>
                                </div>

                                <div class="md:col-span-1">
                                    <label for="parametro_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Divisa<span class="text-red-500">*</span>
                                    </label>
                                    <select id="parametro_id" wire:model.live="parametro_id"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar divisa</option>
                                        @foreach($divisas_activas as $div)
                                            <option value="{{ $div->parametro_id }}">
                                                {{ $div->nombre }} (Tasa: {{ number_format($div->valor, 2, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parametro_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="peso" class="block text-sm font-medium text-gray-700 mb-1">Peso contratado (Kg)<span class="text-red-500">*</span></label>
                                    <input type="number" id="peso" wire:model.live="peso" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" placeholder="Ej: 50">
                                    @error('peso') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="cant_envios" class="block text-sm font-medium text-gray-700 mb-1">Cant. Máxima Envíos<span class="text-red-500">*</span></label>
                                    <input type="text" inputmode="numeric" id="cant_envios" wire:model.blur="cant_envios"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        placeholder="Ej: 100"
                                        maxlength="8"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    @error('cant_envios') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    @php
                                        $divisaSel = $parametro_id ? collect($divisas_activas)->firstWhere('parametro_id', (int)$parametro_id) : null;
                                        $simbolo = $divisaSel->nombre ?? '';
                                    @endphp
                                    <label for="tarifa" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tarifa Total @if($simbolo)({{ $simbolo }})@endif<span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="tarifa" wire:model.live="tarifa"
                                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 pl-3 pr-12 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                            onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                            oninput="formatCurrency(this)">
                                        @if($simbolo)
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-xs font-bold">{{ $simbolo }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($parametro_id && $preview_bs > 0)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Equivalente: <span class="font-bold text-[#6b1820]">{{ number_format($preview_bs, 2, ',', '.') }} Bs</span>
                                            <span class="text-gray-400">a tasa actual</span>
                                        </p>
                                    @endif
                                    @error('tarifa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button 
                            type="button" 
                            wire:click="crearContrato"
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                        >
                            Generar Contrato
                        </x-button>

                        <x-button 
                            type="button" 
                            wire:click="cerrar" 
                            class="px-5 py-2.5 bg-gray-200 text-white hover:bg-gray-300 rounded-lg font-medium text-sm transition shadow-sm"
                        >
                            Cancelar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal -->
    @if ($modal_estatus)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50 transition-opacity duration-300 ease-out opacity-100">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full transform transition-all duration-500 ease-out opacity-100 scale-100">
            <h2 class="text-xl text-center font-semibold text-gray-800 mb-4">¿Desea cambiar el estatus del contrato?</h2>
            <div class="flex justify-around">
                <button wire:click="actualizar_estatus" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                    Sí
                </button>
                <button wire:click="cerrar_estatus" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                    No
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: DIRECCIONES DEL CLIENTE --}}
    {{-- ============================================================ --}}
    @if($modal_direcciones)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Direcciones de Destino</p>
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $dir_cliente_razon }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="abrirCrearDireccion"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-md shadow-primary/20 transition active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Nueva
                        </button>
                        <button wire:click="cerrarDirecciones"
                            class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Listado --}}
                <div class="p-6 max-h-96 overflow-y-auto">
                    @php
                        $direcciones = \App\Models\ClienteCorporativoDirecciones::with(['estado', 'municipio', 'ciudad', 'parroquia'])
                            ->where('cliente_corporativo_id', $dir_cliente_id)->get();
                    @endphp

                    @forelse($direcciones as $dir)
                        <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-0 gap-3">
                            <div class="flex-1 min-w-0">
                                {{-- Alias + estatus --}}
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-black text-gray-800">{{ $dir->alias }}</p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                        {{ $dir->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $dir->activo ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </div>

                                {{-- Atención --}}
                                @if($dir->persona)
                                    <p class="text-xs text-gray-500 mb-1">
                                        <span class="font-semibold text-gray-600">Atención:</span> {{ $dir->persona }}
                                    </p>
                                @endif

                                {{-- Estado / Municipio / Ciudad --}}
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-primary bg-primary/5 px-2 py-0.5 rounded-md">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                        </svg>
                                        {{ $dir->estado->nombre ?? '—' }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $dir->municipio->nombre ?? '—' }}
                                        @if($dir->ciudad_id)
                                            <span class="text-gray-400">·</span> {{ $dir->ciudad->nombre }}
                                        @endif
                                        @if($dir->parroquia_id)
                                            <span class="text-gray-400">·</span> {{ $dir->parroquia->nombre }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Dirección --}}
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    {{ $dir->direccion }}
                                    @if($dir->codigo_postal)
                                        <span class="text-gray-300 mx-1">·</span>
                                        <span class="font-medium text-gray-500">CP {{ $dir->codigo_postal }}</span>
                                    @endif
                                </p>
                            </div>

                            <button wire:click="toggleDireccion({{ $dir->cliente_corporativo_direccion_id }})"
                                title="{{ $dir->activo ? 'Desactivar' : 'Activar' }}"
                                class="shrink-0 p-1.5 rounded-lg transition
                                    {{ $dir->activo ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $dir->activo ? 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88' : 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z' }}"/>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">No hay direcciones registradas.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" wire:click="cerrarDirecciones"
                        class="px-5 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: CREAR DIRECCIÓN --}}
    {{-- ============================================================ --}}
    @if($modal_crear_direccion)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Nueva Dirección</p>
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $dir_cliente_razon }}</p>
                        </div>
                    </div>
                    <button wire:click="cerrarCrearDireccion"
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Formulario --}}
                <form wire:submit="guardarDireccion">
                    <div class="p-6 grid grid-cols-2 gap-x-4 gap-y-4 max-h-[70vh] overflow-y-auto">

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alias <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.blur="dir_alias" maxlength="50"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Ej: Sede Central"/>
                            @error('dir_alias') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Persona de contacto</label>
                            <input type="text" wire:model.blur="dir_persona" maxlength="60"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Ej: Juan Pérez"/>
                            @error('dir_persona') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado <span class="text-red-500">*</span></label>
                            <select wire:model.live="dir_estado"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                <option value="">-- Seleccionar --</option>
                                @foreach($estados as $est)
                                    <option value="{{ $est->estado_id }}">{{ $est->nombre }}</option>
                                @endforeach
                            </select>
                            @error('dir_estado') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Municipio <span class="text-red-500">*</span></label>
                            <select wire:model.live="dir_municipio"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                @disabled(!$dir_estado)>
                                <option value="">-- Seleccionar --</option>
                                @foreach($dir_municipios as $mun)
                                    <option value="{{ $mun->municipio_id }}">{{ $mun->nombre }}</option>
                                @endforeach
                            </select>
                            @error('dir_municipio') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Ciudad <span class="text-red-500">*</span></label>
                            <select wire:model.blur="dir_ciudad"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                @disabled(!$dir_municipio)>
                                <option value="">-- Seleccionar --</option>
                                @foreach($dir_ciudades as $ciudad)
                                    <option value="{{ $ciudad->ciudad_id }}">{{ $ciudad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('dir_ciudad') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Parroquia <span class="text-red-500">*</span></label>
                            <select wire:model.live="dir_parroquia"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                @disabled(!$dir_municipio)>
                                <option value="">-- Seleccionar --</option>
                                @foreach($dir_parroquias as $par)
                                    <option value="{{ $par->parroquia_id }}">{{ $par->nombre }}</option>
                                @endforeach
                            </select>
                            @error('dir_parroquia') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Código Postal <span class="text-red-500">*</span></label>
                            <select wire:model.blur="dir_codigo_postal"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                @disabled(!$dir_parroquia)>
                                <option value="">-- Seleccionar --</option>
                                @foreach($dir_codigos_postales as $cp)
                                    <option value="{{ $cp }}">{{ $cp }}</option>
                                @endforeach
                            </select>
                            @error('dir_codigo_postal') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Dirección <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.blur="dir_direccion" maxlength="255"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Av. Principal, Edificio..."/>
                            @error('dir_direccion') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                            <input type="tel" wire:model.blur="dir_telefono" maxlength="20"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="04XX-XXXXXXX"/>
                            @error('dir_telefono') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Correo</label>
                            <input type="email" wire:model.blur="dir_correo" maxlength="100"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="correo@ejemplo.com"/>
                            @error('dir_correo') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" wire:click="cerrarCrearDireccion"
                            class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="guardarDireccion"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-md shadow-primary/20 transition active:scale-95 disabled:opacity-60">
                            <svg wire:loading wire:target="guardarDireccion" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Guardar Dirección
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        // success alert
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess3', message => {
            Swal.fire({
                position: "center",
                icon: "info",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

        Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
    @endscript

    <script>
        function formatCurrency(input) {
                    // Eliminar caracteres no numéricos
                    let value = input.value.replace(/[^0-9]/g, '');

                    // Convertir a número y formatear
                    if (value.length === 0) {
                        input.value = '0,00';
                        return;
                    }

                    // Convertir a centimos
                    let cents = parseInt(value, 10);

                    // Formatear a bs y centimos
                    let bs = Math.floor(cents / 100);
                    let formattedCents = (cents % 100).toString().padStart(2, '0');

                    // // Agregar separador de miles
                    let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Actualizar el valor del input
                    input.value = `${formattedbs},${formattedCents}`;
                    console.log('entro');

                }
    </script>
@endpush