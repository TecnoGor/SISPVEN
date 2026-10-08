<div>
    @section('titulo')
        Servicios de Alianzas
    @endsection
    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Gestión de Servicios de Alianzas</h1>
        <p class="mt-1 text-sm text-gray-600">Administración de Entes Aliados, Tipos de Alianzas y Catálogo de Servicios.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- Botones de selección y Nuevo registro --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="inline-flex p-1 bg-gray-100 rounded-lg shadow-sm border border-gray-200">
                        <button wire:click="cambiarTipo('entes')"
                            class="px-5 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $tipo === 'entes' ? 'bg-[#6b1820] text-white shadow-md' : 'text-gray-600 hover:bg-gray-200' }}">
                            Entes Aliados
                        </button>

                        <button wire:click="cambiarTipo('tipos')"
                            class="px-5 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $tipo === 'tipos' ? 'bg-[#6b1820] text-white shadow-md' : 'text-gray-600 hover:bg-gray-200' }}">
                            Tipos de Alianza
                        </button>

                        <button wire:click="cambiarTipo('catalogo')"
                            class="px-5 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $tipo === 'catalogo' ? 'bg-[#6b1820] text-white shadow-md' : 'text-gray-600 hover:bg-gray-200' }}">
                            Catálogo de Servicios
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button wire:click="create" class="min-w-[140px] px-4 py-2.5 text-sm rounded-md shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($registros as $registro)
                            <tr wire:key="{{ $registro->getKey() }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $registro->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if ($registro->activo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="edit({{ $registro->getKey() }})"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50 transition"
                                            title="Editar registro">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button wire:click="toggleActivo({{ $registro->getKey() }})"
                                            class="p-1 rounded-md transition {{ $registro->activo ? 'text-green-600 hover:bg-green-50' : 'text-red-600 hover:bg-red-50' }}"
                                            title="{{ $registro->activo ? 'Desactivar' : 'Activar' }}">
                                            @if($registro->activo)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="3" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay registros disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: crear / editar --}}
    @if($modal_open)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    
                    {{-- ENCABEZADO DEL MODAL --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                {{ $editing_id ? 'Editar Registro' : 'Nuevo Registro' }}
                            </h3>
                        </div>
                        <button type="button" wire:click="$set('modal_open', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- FORMULARIO --}}
                    <div class="p-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Registro<span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="form_registro.nombre" 
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                @error('form_registro.nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col"> 
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus del Registro:</label>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                        {{-- Lado Inactivo --}}
                                        <span class="text-sm {{ !($form_registro['activo'] ?? false) ? 'text-red-600 font-bold' : 'text-gray-400' }}">Inactivo</span>
                                        <input type="checkbox" class="sr-only peer" wire:model.live="form_registro.activo">                                       
                                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-2 focus:ring-[#6b1820] 
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all 
                                            peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                        </div>
                                        {{-- Lado Activo --}}
                                        <span class="text-sm {{ ($form_registro['activo'] ?? false) ? 'text-[#6b1820] font-bold' : 'text-gray-400' }}">Activo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button 
                            wire:click="save" 
                            type="button" 
                            wire:loading.attr="disabled" 
                            wire:target="save"
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $editing_id ? 'Actualizar Registro' : 'Guardar Registro' }}
                            </span>
                            <span wire:loading wire:target="save">Procesando...</span>
                        </x-button>

                        <x-button 
                            type="button" 
                            wire:click="$set('modal_open', false)" 
                            class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                        >
                            Cancelar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        // success alert
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1000
            });
        })

        // error alert
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })

        // info alert
        Livewire.on('alertSuccess3', message => {
            Swal.fire({
                position: "center",
                icon: "info",
                title: message.message,
                showConfirmButton: false,
                timer: 10000
            });
        })

        Livewire.on('envio_registrado', () => {
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
    </script>
    @endscript
@endpush