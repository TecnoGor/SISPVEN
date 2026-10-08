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
                Administración de rangos, medidas y montos (Servicio Expreso).
            </p>
        </div>

        {{-- Botón regresar --}}
        <div class="flex items-start">
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
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4">
            <div class="flex justify-end mb-4">
                @can('Crear roles')
                    <x-primary-button wire:click="createexpreso()" class="min-w-[140px] px-4 py-2 text-sm rounded-md shadow-sm">
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
                            @include('livewire.includes.sort-table', ['column' => 'tipo_expreso', 'displayName' => 'Tipo'])
                            @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Fecha de creación'])
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($informacion as $item)
                            <tr wire:key="{{ $item['tarifa_expreso_bolivariano_id'] }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $item['desde'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item['hasta'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item['medida_id'] == 1 ? 'Gramos' : ($item['medida_id'] == 2 ? 'Kilos' : '') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $item['monto'] }} Bs</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center">
                                        @if ($item['activo'])
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

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['tipo_expreso'] }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ !empty($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') : '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-3">
                                        <button wire:click="editexpreso({{ $item['tarifa_expreso_bolivariano_id'] }})"
                                                class="text-blue-600 hover:text-blue-900 transition"
                                                title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        @if ($item['activo'])
                                            <button wire:click="desactivarexpreso({{ $item['tarifa_expreso_bolivariano_id'] }})"
                                                    class="text-red-500 hover:text-red-700 transition"
                                                    title="Inactivar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button wire:click="activarexpreso({{ $item['tarifa_expreso_bolivariano_id'] }})"
                                                    class="text-green-500 hover:text-green-700 transition"
                                                    title="Activar">
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
                                <td colspan="8" class="py-12 text-gray-400 text-base italic bg-gray-50 text-center">
                                    No hay Tarifas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal: Crear Tarifa --}}
        <form wire:submit="storeexpreso">
            <x-dialog-modal wire:model="createForm3.open">
                <x-slot name="title">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                        <div class="flex items-center">
                            <div class="bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 ml-3">Crear Tarifa</h3>
                        </div>
                        <button type="button" wire:click="$set('createForm3.open', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="col-span-1">
                            <x-input-label for="createForm3.desde" class="text-sm font-medium text-gray-700 mb-1">{{ __('Desde') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="createForm3.desde" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="createForm3.desde" placeholder="Desde..." required />
                            <x-input-error :messages="$errors->get('createForm3.desde')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="createForm3.hasta" class="text-sm font-medium text-gray-700 mb-1">{{ __('Hasta') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="createForm3.hasta" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="createForm3.hasta" placeholder="Hasta..." required />
                            <x-input-error :messages="$errors->get('createForm3.hasta')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="createForm3.monto" class="text-sm font-medium text-gray-700 mb-1">{{ __('Monto (Bs)') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="createForm3.monto" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="createForm3.monto" placeholder="Monto Bs" required />
                            <x-input-error :messages="$errors->get('createForm3.monto')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="createForm3.tipo" class="text-sm font-medium text-gray-700 mb-1">{{ __('Tipo') }} <span class="text-red-500">*</span></x-input-label>
                            <select id="createForm3.tipo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" wire:model.blur="createForm3.tipo">
                                <option value="">{{ __('Seleccione un tipo') }}</option>
                                <option value="Urbano">{{ __('Urbano') }}</option>
                                <option value="Intraestatal">{{ __('Intraestatal') }}</option>
                                <option value="Nacional">{{ __('Nacional') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('createForm3.tipo')" class="mt-1" />
                        </div>
                        <br>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                        <x-button wire:loading.attr="disabled" wire:target="storeexpreso" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            <span wire:loading.remove wire:target="storeexpreso">Crear</span>
                            <span wire:loading wire:target="storeexpreso">Procesando...</span>
                        </x-button>
                        <x-button type="button" wire:click="$set('createForm3.open', false)" class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            Cancelar
                        </x-button>             
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>

        {{-- Modal: Actualizar Tarifa --}}
        <form wire:submit="updateexpreso">
            <x-dialog-modal wire:model="editForm3.open">
                <x-slot name="title">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                        <div class="flex items-center">
                            <div class="bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 ml-3">Actualizar Tarifa</h3>
                        </div>
                        <button type="button" wire:click="$set('editForm3.open', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="col-span-1">
                            <x-input-label for="editForm3.desde" class="text-sm font-medium text-gray-700 mb-1">{{ __('Desde') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="editForm3.desde" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="editForm3.desde" placeholder="Desde..." required />
                            <x-input-error :messages="$errors->get('editForm3.desde')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="editForm3.hasta" class="text-sm font-medium text-gray-700 mb-1">{{ __('Hasta') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="editForm3.hasta" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="editForm3.hasta" placeholder="Hasta..." required />
                            <x-input-error :messages="$errors->get('editForm3.hasta')" class="mt-1" />
                        </div>

                        <div class="col-span-1">
                            <x-input-label for="editForm3.monto" class="text-sm font-medium text-gray-700 mb-1">{{ __('Monto (Bs)') }} <span class="text-red-500">*</span></x-input-label>
                            <x-text-input id="editForm3.monto" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" type="text" wire:model.blur="editForm3.monto" placeholder="Monto en Bs" required />
                            <x-input-error :messages="$errors->get('editForm3.monto')" class="mt-1" />
                        </div>
                        <br>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                        <x-button wire:loading.attr="disabled" wire:target="updateexpreso" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            <span wire:loading.remove wire:target="updateexpreso">Guardar</span>
                            <span wire:loading wire:target="updateexpreso">Procesando...</span>
                        </x-button>
                        <x-button type="button" wire:click="$set('editForm3.open', false)" class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none">
                            Cancelar
                        </x-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>
    </div>
</div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @script
        <script>
            // success alert
            Livewire.on('alertSuccess', message => {
                Swal.fire({
                    position: "top-center",
                    icon: "success",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            Livewire.on('tarifaUpdated', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        </script>
        @endscript
    @endpush
    
    
    