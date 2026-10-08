<div>
    @section('titulo')
        Gestión de Integrantes
    @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Gestión de Integrantes</h1>
        <p class="mt-1 text-sm text-gray-600">Administración de personal, asignación de oficinas y roles del sistema.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">
            
            {{-- BARRA DE BÚSQUEDA --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 transition duration-150"
                            placeholder="Buscar integrante por nombre o cédula...">
                    </div>
                </div>
            </div>

            {{-- TABLA DE RESULTADOS --}}
            @if($search)
                <div class="overflow-x-auto rounded-lg border border-gray-100 p-0 animate-fade-in">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre y Apellido</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Cédula</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Oficina</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Rol</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($usuarios as $usuario)
                                <tr wire:key="{{ $usuario->id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $usuario->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $usuario->cedula }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $usuario->oficina->nombre ?? 'Sin oficina' }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $usuario->getRoleNames()->first() ?? 'Sin rol' }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $usuario['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $usuario['activo'] ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <button wire:click="edit({{ $usuario->id }})" 
                                            class="text-primary hover:text-red-800 p-2 rounded transition hover:bg-red-50" 
                                            title="Gestionar Integrante">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b text-center">
                                    <td colspan="6" class="py-7 text-gray-500 text-lg italic bg-gray-50">No se encontraron integrantes con esos datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                {{-- ESTADO VACÍO CUANDO NO HAY BÚSQUEDA --}}
                <div class="py-12 flex flex-col items-center justify-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-lg">Realice una búsqueda para ver los integrantes</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE GESTIÓN --}}
    <form wire:submit="update">
        <x-dialog-modal wire:model="editForm.open">
            <x-slot name="title">
                <div class="bg-gray-100 -mx-6 -mt-4 px-6 py-4 flex items-center justify-between border-b border-gray-200 rounded-t-xl">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800"> Gestionar Integrante</h3>
                </div>
                <button type="button" wire:click="$set('editForm.open', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cambiar de Oficina<span class="text-red-500">*</span></label>
                        <select id="editForm.oficina" 
                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-primary focus:border-primary text-sm" 
                            wire:model.live="editForm.oficina">
                            <option value="">{{ __('Seleccione una oficina') }}</option>
                            @foreach ($oficinas as $oficina)
                                <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Asignar Rol<span class="text-red-500">*</span></label>
                        <select wire:model.live="editForm.role" id="editForm.role" 
                            class="w-full border border-gray-300 focus:ring-primary focus:border-primary rounded-lg shadow-sm text-sm py-2 px-3">
                            <option value="" disabled>-- Seleccionar --</option>
                            @foreach ($roles as $role)
                                @if (is_object($role) && isset($role->id, $role->name))
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('editForm.role')" class="mt-2" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <x-button type="submit" wire:loading.attr="disabled" wire:target="update"
                        class="px-8 py-2.5 bg-primary text-white hover:bg-red-800 rounded-lg font-bold text-sm transition shadow-md uppercase">
                        Guardar Cambios
                        <x-loading-button wire:target="update" />
                    </x-button>
                    <x-button type="button" wire:click="$set('editForm.open', false)" 
                        class="px-8 py-2.5 bg-gray-500 text-white hover:bg-gray-600 rounded-lg font-bold text-sm transition uppercase">
                        Cancelar
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
<script>
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
@endpush
