
@section('titulo')
    Roles
@endsection

<div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Roles</h1>
            
        </div>
        @can('Crear roles')    
            <div class="mb-6 sm:mb-0 mt-10">
                <x-primary-button wire:click="create()">
                    Crear Rol
                </x-primary-button>
            </div>
        @endcan


    </div>

    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">

        <div class="flex flex-col md:flex-row gap-2 items-center justify-between d p-4">
            <div class="flex w-full md:w-auto">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                            fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input  type="text" wire:model.live.debounce.300ms="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2 "
                        placeholder="Buscar...">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr class="">
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Rol'])
                        @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Fecha de creación'])
                        <th class="px-4 py-3 text-center text-black">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr wire:key="{{ $role->id }}" class="border-b text-left">
                            <th class="px-4 py-3 font-medium text-black">{{ $role->name }}</th>
                            <th class="px-4 py-3 font-medium text-black">{{ $role->created_at?->format('d/m/Y') }}</th>
                            <th class="px-4 py-3 font-medium flex items-center justify-center gap-4">
                                @if(auth()->user()->can('Editar Permisos') && $role->name !== 'SuperAdmin')
                                    <button href="{{ route('roles.permisos', $role->id) }}" title="Permisos" wire:navigate.hover>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                          </svg>
                                          
                                    </button>
                                @endif
                                @if (auth()->user()->can('Editar Roles') && $role->name !== 'SuperAdmin')
                                    <button href wire:click="edit({{ $role->id }})" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                          </svg>
                                          
                                    </button>
                                @endif
                                @if (auth()->user()->can('Eliminar Roles') && $role->name !== 'SuperAdmin')
                                    <button wire:click="$dispatch('mostrarAlerta', {{ $role->id }})" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-primary">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                          </svg>    
                                    </button>
                                @endif
                            </th>
                        </tr>
                    @empty
                        <tr class="border-b text-center">
                            <th colspan="5" class="py-7 text-default text-2xl">No hay roles</th>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <form wire:submit="store">
            <x-dialog-modal wire:model="createForm.open">
                <x-slot name="title">
                    Crear Rol
                </x-slot>
    
                <x-slot name="content">
                    <div class="mb-4">
                        <x-input-label for="createForm.name" :value="__('Nombre del Rol')" />
                        <x-text-input id="createForm.name" class="block mt-1 w-full" type="text" wire:model.blur="createForm.name" :value="old('createForm.name')" 
                            placeholder="Admin, Super Usuario, Gerente..." required maxlength="30" />
                        <x-input-error :messages="$errors->get('createForm.name')" class="mt-2" />
                    </div>

                        <div class="mb-4">
                            <x-input-label :value="__('Tipos de Oficina')" />
                            <div class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                @foreach ($oficinas as $index => $oficina)
                                    <label wire:key="oficina-{{ $oficina->oficina_id }}" class="flex items-center space-x-2 mt-2">
                                        <input type="checkbox"
                                            value="{{ $oficina->tipo_oficina_id }}"
                                            wire:model="createForm.tipo_oficina"
                                            class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                                        <span class="text-gray-800">{{ $oficina->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('createForm.tipo_oficina_ids')" class="mt-2" />
                        </div>
                </x-slot>

    
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('createForm.open', false)" type="button">
                            Cancelar
                        </x-danger-button>
    
                        <x-primary-button wire:loading.attr="disabled" wire:target="store">
                            Crear
    
                            <x-loading-button wire:target="store"/>
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>
        <div class="py-4 px-3">
            <div class="flex space-x-4 items-center mb-3">
                <label class="w-32 text-sm font-medium text-gray-900">Por página</label>
                <select
                    wire:model.live="perPage"
                    class="md:max-w-36 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full p-2.5 ">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>
            {{ $roles->links() }}
        </div>
    </div>

    <form wire:submit="update">
        <x-dialog-modal wire:model="editForm.open">
            <x-slot name="title">
                Actualizar Rol
            </x-slot>

            <x-slot name="content">
                <div class="mb-4">
                    <x-input-label for="editForm.name" :value="__('Nombre del Rol')" />
                    <x-text-input id="editForm.name" class="block mt-1 w-full" type="text" wire:model.blur="editForm.name" :value="old('editForm.name')" placeholder="Admin, Super Usuario, Gerente..." required maxlength="14" />
                    <x-input-error :messages="$errors->get('editForm.name')" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('editForm.open', false)" type="button">
                        Cancelar
                    </x-danger-button>

                    <x-primary-button wire:loading.attr="disabled" wire:target="update">
                        Guardar

                        <x-loading-button wire:target="update"/>
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
        Livewire.on('mostrarAlerta', role_id => {
                Swal.fire({
                title: "Eliminar Rol?",
                text: "Un rol eliminado no se puede recuperar!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // eliminar el rol
                    Livewire.dispatch('delete', {role: role_id});
                }
            });
        });
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
