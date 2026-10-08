<div>
    @section('titulo')
        Tarifas
    @endsection

    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto mt-6 flex items-start justify-between gap-4">
        <div class="text-left">
            <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight uppercase">
                {{ __('Tarifas') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Administración de rangos, medidas y montos nacionales.
            </p>
        </div>

        {{-- Botón regresar --}}
        <a href="{{ route('tarifas') }}"
           wire:navigate.hover
           class="inline-flex items-center gap-2 bg-white border border-gray-300 shadow-sm 
                  rounded-lg px-4 py-2 mt-1
                  text-sm font-semibold text-gray-700 
                  transition-all duration-200 
                  hover:bg-[#6b1820] hover:text-white hover:shadow-md hover:-translate-x-1 group">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 transition-transform duration-200"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7" />
            </svg>
            <span>Volver</span>
        </a>
    </div>

    {{-- Si no hay información: mostrar SOLO el mensaje grande --}}
    @if(isset($noInformacion) && $noInformacion === true)
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mt-12 bg-white border border-gray-200 rounded-xl shadow-md p-10 flex flex-col items-center justify-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-yellow-500 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z" />
                </svg>

                <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-3">
                    No se encontró una tarifa asociada al servicio seleccionado
                </h2>

                <p class="text-sm md:text-base text-gray-500 max-w-[70%]">
                    Actualmente no hay tarifas registradas para el servicio que seleccionaste.
                    Si crees que debería existir una tarifa, verifica en el módulo de administración de tarifas o contacta al administrador.
                </p>
            </div>
        </div>
    @else
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4">
                <div class="flex justify-end mb-4">
                    @can('Crear roles')
                        <x-primary-button wire:click="create()" class="min-w-[140px] px-4 py-2 text-sm rounded-md shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Crear Tarifa
                        </x-primary-button>
                    @endcan
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                @include('livewire.includes.sort-table', ['column' => 'desde', 'displayName' => 'Desde'])
                                @include('livewire.includes.sort-table', ['column' => 'hasta', 'displayName' => 'Hasta'])
                                @include('livewire.includes.sort-table', ['column' => 'medida_id', 'displayName' => 'Medida'])
                                @include('livewire.includes.sort-table', ['column' => 'monto', 'displayName' => 'Monto'])
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Estatus
                                </th>
                                @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Fecha'])
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($informacion as $item)
                                <tr wire:key="{{ $item['tarifa_nacional_rango_id'] ?? $loop->index }}" class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $item['desde'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item['hasta'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ ($item['medida_id'] ?? null) == 1 ? 'Gramos' : ((($item['medida_id'] ?? null) == 2) ? 'Kilos' : '') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $item['monto'] ?? '-' }} Bs</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center">
                                            @if (!empty($item['activo']))
                                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ !empty($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-3">
                                            <button wire:click="edit({{ $item['tarifa_nacional_rango_id'] ?? 'null' }})"
                                                class="text-blue-600 hover:text-blue-900 transition"
                                                title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            @if (!empty($item['activo']))
                                                <button wire:click="desactivar({{ $item['tarifa_nacional_rango_id'] ?? 'null' }})"
                                                    class="text-red-500 hover:text-red-700 transition" title="Inactivar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button wire:click="activar({{ $item['tarifa_nacional_rango_id'] ?? 'null' }})"
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
                                <tr>
                                    <td colspan="7" class="py-12 text-gray-400 text-base italic bg-gray-50 text-center">
                                        No hay registros de tarifas disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        
        {{-- Modales --}}
        <form wire:submit.prevent="store">
            <x-dialog-modal wire:model="createForm.open">
                <x-slot name="title">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                        <div class="flex items-center">
                            <div class="bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Crear Nueva Tarifa</h3>
                        </div>
                        <button type="button" wire:click="$set('createForm.open', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="col-span-1">
                            <x-input-label for="createForm.desde" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Rango Desde') }} <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="createForm.desde"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="createForm.desde" placeholder="0.00" required />
                            <x-input-error :messages="$errors->get('createForm.desde')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="createForm.hasta" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Rango Hasta') }} <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="createForm.hasta"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="createForm.hasta" placeholder="500.00" required />
                            <x-input-error :messages="$errors->get('createForm.hasta')" class="mt-1" />
                        </div>

                        <div class="col-span-full">
                            <x-input-label for="createForm.monto" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Monto de la Tarifa (Bs)') }} <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="createForm.monto"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="createForm.monto" placeholder="0.00" required />
                            <x-input-error :messages="$errors->get('createForm.monto')" class="mt-1" />
                        </div>
                    </div>
                    <br>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                        <x-button wire:loading.attr="disabled" wire:target="store"
                                class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            <span wire:loading.remove wire:target="store">Crear Tarifa</span>
                            <span wire:loading wire:target="store">Procesando...</span>
                        </x-button>
                        <x-button type="button" wire:click="$set('createForm.open', false)"
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            Cancelar
                        </x-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>

        {{-- Modal actualizar --}}
        <form wire:submit.prevent="update">
            <x-dialog-modal wire:model="editForm.open">
                <x-slot name="title">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                        <div class="flex items-center">
                            <div class="bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Actualizar Tarifa</h3>
                        </div>
                        <button type="button" wire:click="$set('editForm.open', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div>
                            <x-input-label for="editForm.desde" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Desde') }}
                            </x-input-label>
                            <x-text-input id="editForm.desde"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="editForm.desde" />
                            <x-input-error :messages="$errors->get('editForm.desde')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="editForm.hasta" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Hasta') }}
                            </x-input-label>
                            <x-text-input id="editForm.hasta"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="editForm.hasta" />
                            <x-input-error :messages="$errors->get('editForm.hasta')" class="mt-1" />
                        </div>

                        <div class="col-span-full">
                            <x-input-label for="editForm.monto" class="text-sm font-medium text-gray-700 mb-1">
                                {{ __('Monto (Bs)') }}
                            </x-input-label>
                            <x-text-input id="editForm.monto"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        type="number" step="0.01" wire:model.defer="editForm.monto" />
                            <x-input-error :messages="$errors->get('editForm.monto')" class="mt-1" />
                        </div>
                    </div>
                    <br>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                        <x-button wire:loading.attr="disabled" wire:target="update"
                                class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            <span wire:loading.remove wire:target="update">Guardar Cambios</span>
                            <span wire:loading wire:target="update">Procesando...</span>
                        </x-button>
                        <x-button type="button" wire:click="$set('editForm.open', false)"
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            Cancelar
                        </x-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        Livewire.on('alertSuccess', (event) => {
            Swal.fire({
                position: "top-center",
                icon: "success",
                title: event.message, 
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertError', (event) => {
            Swal.fire({
                icon: "error",
                title: "Ups!",
                text: event.message,
                confirmButtonColor: '#800020' 
            });
        });
    </script>
    @endscript
@endpush
