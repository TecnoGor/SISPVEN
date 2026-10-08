@section('titulo')
    Circuitos Judiciales / Tribunales
@endsection
<div>
    {{-- ENCABEZADO DE LA PÁGINA --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Circuitos Judiciales / Tribunales</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y registro de lugares de emisión para telegramas.</p>
    </div>

    @if (session()->has('message'))
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                <p class="font-bold">Éxito</p>
                <p>{{ session('message') }}</p>
            </div>
        </div>
    @endif

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA SUPERIOR: BUSCADOR Y BOTÓN AGREGAR --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar circuito o tribunal...">
                        </div>
                    </div>

                    {{-- Botón para abrir el Modal --}}
                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button
                            class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm inline-flex items-center justify-center"
                            wire:click="$set('showModal', true)"
                            type="button"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Nuevo
                        </x-primary-button>
                    </div>

                </div>
            </div>

            {{-- TABLA DE RESULTADOS --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo de Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre del Circuito / Tribunal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado Actual</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($listado as $item)
                            <tr wire:key="{{ $item->circuito_judicial_tribunal_telegramas_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ $item->nombre_lugar ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if ($item->activo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button wire:click="toggleActivo({{ $item->circuito_judicial_tribunal_telegramas_id }})" 
                                        class="text-gray-500 hover:text-gray-900 focus:outline-none p-1 rounded-full hover:bg-gray-100 transition"
                                        title="{{ $item->activo ? 'Desactivar' : 'Activar' }}">
                                        @if($item->activo)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 whitespace-nowrap text-center text-lg italic text-gray-500 bg-gray-50">
                                    No hay registros de Circuitos Judiciales o Tribunales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

    {{-- MODAL DE REGISTRO --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenido del Modal --}}
                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    
                    <form wire:submit.prevent="guardar">
                        
                        {{-- ENCABEZADO DEL MODAL --}}
                        <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1M9 13h1M9 17h1m4-10h1m4 6h1m-4 6h1m-4 6h1" />
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                    Nuevo Circuito Judicial / Tribunal
                                </h3>
                            </div>
                            {{-- Botón de Cierre (X) --}}
                            <button type="button" wire:click="$set('showModal', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="lugar_emision_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Registro<span class="text-red-500">*</span></label>
                                <select id="lugar_emision_id" wire:model.defer="lugar_emision_id" 
                                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 bg-white text-gray-800 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm transition duration-150">
                                    <option value="" selected hidden>Seleccionar Tipo</option>
                                    @foreach ($tiposEmision as $tipo)
                                        <option value="{{ $tipo->lugar_emision_telegramas_id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('lugar_emision_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Circuito/Tribunal<span class="text-red-500">*</span></label>
                                <input type="text" id="nombre" wire:model.defer="nombre" 
                                    class="mt-1 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2.5 px-3 text-gray-800 transition duration-150" 
                                    placeholder="Ej: Circuito Judicial Penal 1 de Caracas">
                                @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        {{-- PIE DE MODAL (ACCIONES) --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            {{-- Botón Guardar --}}
                            <x-button
                                type="submit"
                                class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Guardar Registro
                            </x-button>

                            {{-- Botón Cancelar --}}
                            <x-button
                                type="button"
                                wire:click="$set('showModal', false)"
                                class="px-4 py-2 bg-gray-200 text-white hover:bg-gray-300 rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Cancelar
                            </x-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
</div>