<div>
    @section('titulo')
        Servicios Flota
    @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Servicios de Flota</h1>
        <p class="mt-1 text-sm text-gray-600">Administración y catálogo de servicios para la flota vehicular.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y BOTÓN --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar servicio...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button wire:click="create()" class="min-w-[140px] px-4 py-2.5 text-sm rounded-md shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Registrar Servicio
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Nombre'])
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Estatus
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($servicios as $servicio)
                            <tr wire:key="{{ $servicio->parametro_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-left">
                                    {{ $servicio->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex justify-center items-center">
                                        @if ($servicio['activo'])
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                Inactivo
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <div class="flex justify-end gap-3">
                                        {{-- BOTÓN EDICIÓN --}}
                                        <button wire:click="edit({{ $servicio->servicios_flota_id }})" 
                                            class="text-blue-500 hover:text-blue-700 transition" 
                                            title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        @if ($servicio['activo'])
                                            <button wire:click="desactivar({{$servicio->servicios_flota_id }})" 
                                                class="text-red-600 hover:text-orange-700 transition" title="Desactivar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $servicio->servicios_flota_id}})" 
                                                class="text-green-500 hover:text-green-700 transition" title="Activar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="3" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay servicios disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-4">
                {{ $servicios->links() }}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="paginacion" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="paginacion" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
        </div>
    </div>

    {{-- MODAL: REGISTRAR --}}
    <form wire:submit="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">
                            Registrar Servicio de Flota
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('createForm.open', false)" class="text-gray-400 hover:text-gray-600 transition duration-150">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-5 pt-4">
                    <div>
                        <x-input-label for="createForm.nombre" class="text-sm font-medium text-gray-700 mb-1">
                            {{ __('Nombre del servicio') }} <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input id="createForm.nombre" 
                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" 
                            type="text" wire:model.blur="createForm.nombre" placeholder="Ej: Cambio de Aceite..." required maxlength="20" />
                        <x-input-error :messages="$errors->get('createForm.nombre')" class="mt-2" />
                    </div>
                    <br><br>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                    <x-button type="button" wire:click="$set('createForm.open', false)" 
                        class="px-5 py-2.5 bg-gray-500 text-white hover:bg-gray-600 rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                        Cancelar
                    </x-button>
                    <x-primary-button class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none" 
                        wire:loading.attr="disabled" wire:target="store">
                        <span wire:loading.remove wire:target="store">Crear Servicio</span>
                        <span wire:loading wire:target="store">Procesando...</span>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- MODAL: ACTUALIZAR --}}
    <form wire:submit="update">
        <x-dialog-modal wire:model="editForm.open">
            <x-slot name="title">
                <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">
                            Actualizar Servicio
                        </h3>
                    </div>
                    <button type="button" wire:click="$set('editForm.open', false)" class="text-gray-400 hover:text-gray-600 transition duration-150">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-5 pt-4">
                    <div>
                        <x-input-label for="editForm.nombre" class="text-sm font-medium text-gray-700 mb-1">
                            {{ __('Nombre del Servicio') }} <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input id="editForm.nombre" 
                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" 
                            type="text" wire:model.blur="editForm.nombre" placeholder="Ej: Cambio de Aceite..." required maxlength="20" />
                        <x-input-error :messages="$errors->get('editForm.nombre')" class="mt-2" />
                    </div>
                    <br><br>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                    <x-button type="button" wire:click="$set('editForm.open', false)" 
                        class="px-5 py-2.5 bg-gray-500 text-white hover:bg-gray-600 rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                        Cancelar
                    </x-button>
                    <x-primary-button class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none" 
                        wire:loading.attr="disabled" wire:target="update">
                        <span wire:loading.remove wire:target="update">Guardar Cambios</span>
                        <span wire:loading wire:target="update">Procesando...</span>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>
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
                timer: 1500
            });
        })
    </script>
    @endscript
@endpush