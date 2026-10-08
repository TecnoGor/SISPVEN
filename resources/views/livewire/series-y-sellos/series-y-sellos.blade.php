<div>
    @section('titulo')
        Parámetros de Filatelia
    @endsection
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Parámetros de Filatelia</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de series y sellos postales.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y BOTÓN --}}
            <div class="mt-2 mb-4">
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
                                placeholder="Buscar sello...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm" wire:click="crear_serie">
                            Crear Serie y Sello
                        </x-primary-button>

                        <x-primary-button wire:click="gestionar_series"
                            class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm">
                            Gestionar Series
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                <div class="w-full md:w-1/4">
                    <label for="serie" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Serie</label>
                    <select id="serie" wire:model.live="serie"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                        <option value="">Todas las Series</option>
                        @foreach ($series as $ser)
                            <option value="{{$ser->serie_filatelia_id}}">{{$ser->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Serie</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Descripción</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Coste</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($sellos as $index => $sello)
                            <tr wire:key="{{ $sello->sello_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $sello->serie->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $sello->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ number_format($sello->coste, 2) }} Bs</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <button wire:click="estatus({{ $sello->sello_id }})" class="inline-flex items-center justify-center p-2 rounded transition hover:bg-gray-100">
                                        @if($sello->activo)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    {{-- BOTÓN DE EDICIÓN --}}
                                    <button wire:click="edit_sello({{ $sello->sello_id }})" class="text-[#6b1820] hover:text-[#7b1f27] p-2 rounded transition hover:bg-red-50" title="Editar Sello">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay sellos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{$sellos->links()}}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="paginacion" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="paginacion" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
        </div>
    </div>


    {{-- MODAL 1: CREACIÓN DE SERIES Y SELLOS --}}
    @if($modal_open)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    {{-- ENCABEZADO CON ICONO --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Creación de Series y Sellos</h3>
                        </div>
                        <button type="button" wire:click="$set('modal_open', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        {{-- SECCIÓN SERIE --}}
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end border-b pb-6 mb-6">
                            <div class="col-span-1 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Serie<span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nombre_serie" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                @error('nombre_serie') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-1">
                                <x-button class="w-full py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-xs uppercase" type="button" wire:click="ingresar_serie">
                                    Aceptar
                                </x-button>
                            </div>
                        </div>

                        {{-- SECCIÓN SELLOS --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Series Disponibles<span class="text-red-500">*</span></label>
                                <select wire:model.live="serie_sello" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm">
                                    <option value="">Seleccionar Serie</option>
                                    @foreach ($series as $ser)
                                        <option value="{{$ser->serie_filatelia_id}}">{{$ser->nombre}}</option>
                                    @endforeach
                                </select>
                                @error("serie_sello") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @foreach ($sellos_modal as $index => $sello)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nombre de Sello<span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="sellos_modal.{{ $index }}.nombre" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm">
                                        @error("sellos_modal.$index.nombre") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Precio<span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="sellos_modal.{{ $index }}.precio" placeholder="0.00"
                                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm"
                                            onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                            oninput="formatCurrency(this)">
                                        @error("sellos_modal.$index.precio") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex justify-start">
                                <button type="button" wire:click="agregar_sello" class="flex items-center gap-2 text-sm font-bold text-green-700 hover:text-green-800 transition">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 border border-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </div>
                                    Añadir otro sello
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase" type="button" wire:click="crear_sellos">
                            Crear Sellos
                        </x-button>
                        <x-button type="button" wire:click="$set('modal_open', false)" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Cerrar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 2: CREACIÓN RÁPIDA DE SERIE --}}
    @if($modal_open2)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-md overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800">Nueva Serie</h3>
                        </div>
                    </div>
                    <form wire:submit="submit">
                        <div class="p-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Serie<span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nombre_serie" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                            @error('nombre_serie') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button class="px-6 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm uppercase" type="button" wire:click="ingresar_serie">
                                Aceptar
                            </x-button>
                            <x-button class="px-6 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm uppercase" wire:click="modalClose" type="button">
                                Cancelar
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL PARA EDITAR SELLO --}}
    @if ($is_edit)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Modificar Serie y Sello</h3>
                        </div>
                        <button type="button" wire:click="$set('is_edit', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Serie<span class="text-red-500">*</span></label>
                                <select wire:model="serie_sello" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm">
                                    @foreach ($series as $ser)
                                        <option value="{{$ser->serie_filatelia_id}}">{{$ser->nombre}}</option>
                                    @endforeach
                                </select>
                                @error("serie_sello") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nombre de Sello<span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nombre_sello_edit" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm">
                                    @error("nombre_sello_edit") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Precio<span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="precio_sello_edit" placeholder="0.00"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm"
                                        oninput="formatCurrency(this)">
                                    @error("precio_sello_edit") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase" type="button" wire:click="actualizar_sello">
                            Guardar Cambios
                        </x-button>
                        <x-button type="button" wire:click="$set('is_edit', false)" class="px-8 py-2.5 bg-gray-500 text-white hover:bg-gray-600 rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Cerrar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: GESTIONAR SERIES --}}
    @if($modal_series_open)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm" wire:click="modal_series_close">
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl" wire:click.stop>
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0m-9.75 0h9.75" />
                            </svg>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Gestionar Series</h3>
                                <p class="text-xs text-gray-500 uppercase font-medium tracking-wider">Renombrar o habilitar / inhabilitar series</p>
                            </div>
                        </div>
                        <button type="button" wire:click="modal_series_close" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        {{-- TABLA DE SERIES --}}
                        <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nombre de la Serie</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Estatus</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($series_manage_list as $s)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                                {{ $s->nombre }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($s->activo)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                        Habilitada
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                        Inhabilitada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center flex justify-center gap-2">
                                                <button wire:click="cargar_serie_editar({{ $s->serie_filatelia_id }})" 
                                                    class="p-2 rounded-lg bg-gray-100 hover:bg-[#6b1820] text-gray-600 hover:text-white transition shadow-sm" title="Editar nombre">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487L18.55 2.8a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z" />
                                                    </svg>
                                                </button>

                                                <button wire:click="toggle_serie({{ $s->serie_filatelia_id }})" 
                                                    class="p-2 rounded-lg bg-gray-100 transition shadow-sm" title="Cambiar Estatus">
                                                    @if($s->activo)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600 hover:text-red-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 hover:text-green-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($series_manage_list) === 0)
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">
                                                No hay series registradas en el sistema.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- FORMULARIO EDICIÓN --}}
                        @if($selected_serie_id)
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 animate-fadeIn">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="flex h-2 w-2 rounded-full bg-[#6b1820]"></span>
                                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Editar Serie Seleccionada</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-6">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nuevo Nombre</label>
                                        <input type="text" wire:model.defer="selected_serie_name_edit" 
                                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-[#6b1820] focus:border-[#6b1820] transition">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estatus Actual</label>
                                        <button wire:click="toggle_selected_serie_active" type="button" 
                                            class="w-full flex items-center justify-between px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition shadow-sm">
                                            <span class="font-medium {{ $selected_serie_active_edit ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $selected_serie_active_edit ? 'Habilitada' : 'Inhabilitada' }}
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="md:col-span-3">
                                        <x-button wire:click="actualizar_serie" 
                                            class="w-full py-2.5 bg-green-700 text-white hover:bg-green-800 rounded-lg font-bold text-xs uppercase shadow-md transition">
                                            Guardar Cambios
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button type="button" wire:click="modal_series_close" 
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Cerrar Gestión
                        </x-button>
                    </div>

                </div>
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
