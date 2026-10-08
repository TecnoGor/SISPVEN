<div>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-4">Choferes</h1>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-default uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Cédula</th>
                            <th class="px-4 py-3 text-left">RIF</th>
                            <th class="px-4 py-3 text-left">Dirección</th>
                            <th class="px-4 py-3 text-left">Teléfono</th>
                            <th class="px-4 py-3 text-left">Correo</th>
                            <th class="px-4 py-3 text-left">Estatus</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                            <th class="px-4 py-3 text-left"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($choferes as $chofer)
                        <tr wire:key="{{ $chofer->chofer_id }}" class="border-b text-left">
                            <td class="px-4 py-3 font-medium text-black">{{ $chofer->nombre }}</td>
                            <td class="px-4 py-3 font-medium text-black">{{ $chofer->cedula }}</td>
                            <td class="px-4 py-3 font-medium text-black text-center">{{ $chofer->rif }}</td>
                            <td class="px-4 py-3 font-medium text-black">{{ $chofer->direccion }}</td>
                            <td class="px-4 py-3 font-medium text-black">{{ $chofer->telefono }}</td>
                            <td class="px-4 py-3 font-medium text-black">{{ $chofer->correo }}</td>
                            <td class="px-4 py-3 font-medium {{ $chofer['activo'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $chofer['activo'] ? 'Activo' : 'Inactivo' }}
                            </td>
                            <td class="px-4 py-3 font-medium text-black text-center">
                                <button wire:click="edit({{  $chofer->chofer_id  }})" title="Editar" wire:navigate.hover>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </button>
                                @if ($chofer['activo'])
                                <!-- Botón para desactivar -->
                                    <button wire:click="desactivar({{$chofer->chofer_id }})" title="Desactivar" class=" text-white p-2 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </button>
                                @else
                                    <!-- Botón para activar -->
                                    <button wire:click="activar({{ $chofer->chofer_id}})" title="Activar" class=" text-white p-2 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>                                      
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr class="px-4 py-3 font-medium text-black text-left">
                                <td colspan="8" class="py-7 text-default text-2xl">No hay Choferes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-4 px-3">
                    <div class="flex space-x-4 items-center mb-3">
                        <label class="w-32 text-sm font-medium text-gray-900">Por página</label>
                        <select
                            wire:model.live="perPage"
                            class="md:max-w-36 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 ">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                        </select>
                        <div class="flex justify-end mb-6 mt-4 ml-auto">
                            <x-primary-button wire:click="create" title="Crear" wire:navigate.hover>
                                Crear Chofer
                            </x-primary-button>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            <form wire:submit="store">
                <x-dialog-modal wire:model="createForm.open">
                    <x-slot name="title">
                        Crear Chofer
                    </x-slot>
        
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="createForm.nombre" :value="__('Nombre')" />
                                <x-text-input id="createForm.nombre" class="block mt-2 w-full" type="text" wire:model.blur="createForm.nombre" :value="old('createForm.nombre')" placeholder="" required maxlength="8"/>
                                <x-input-error :messages="$errors->get('createForm.nombre')" class="mt-2" />
                            </div>
                    
                            <div>
                                <x-input-label for="createForm.cedula" :value="__('Cédula')" />
                                <x-text-input 
                                    id="createForm.cedula" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.cedula" 
                                    :value="old('createForm.cedula')" 
                                    placeholder=""
                                    maxlength="10" 
                                    inputmode="numeric" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.cedula')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.rif" :value="__('RIF')" />
                                <x-text-input 
                                    id="createForm.rif" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.rif" 
                                    :value="old('createForm.rif')" 
                                    placeholder=""
                                    maxlength="14" 
                                    inputmode="text" 
                                />
                                
                                <x-input-error :messages="$errors->get('createForm.rif')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.direccion" :value="__('Direccion')" />
                                <x-text-input id="createForm.direccion" class="block mt-2 w-full"  wire:model="createForm.direccion" :value="old('createForm.direccion')" placeholder="" required />
                                <x-input-error :messages="$errors->get('createForm.direccion')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="createForm.telefono" :value="__('Teléfono')" />
                                <x-text-input 
                                    id="createForm.telefono" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.telefono" 
                                    :value="old('createForm.telefono')" 
                                    placeholder=""
                                    maxlength="12" 
                                    inputmode="numeric" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.telefono')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.correo" :value="__('Correo')" />
                                <x-text-input id="createForm.correo" class="block mt-2 w-full" type="text" wire:model.blur="createForm.correo" :value="old('createForm.correo')" placeholder="" required />
                                <x-input-error :messages="$errors->get('createForm.correo')" class="mt-2" />
                            </div>
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
            <form wire:submit="update">
                <x-dialog-modal wire:model.blur="editForm.open">
                    <x-slot name="title">
                        Actualizar Chofer
                    </x-slot>
        
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                    <x-input-label for="editForm.nombre" :value="__('Nombre')" />
                                    <x-text-input id="editForm.nombre" class="block mt-2 w-full" type="text" wire:model.blur="editForm.nombre" :value="old('editForm.nombre')" placeholder="" required />
                                    <x-input-error :messages="$errors->get('createForm.nombre')" class="mt-2" />
                            </div> 
                            <div>
                                <x-input-label for="editForm.cedula" :value="__('Cédula')" />
                                <x-text-input 
                                    id="editForm.cedula" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.cedula" 
                                    :value="old('editForm.cedula')" 
                                    placeholder=""
                                    maxlength="10" 
                                    inputmode="numeric" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('editForm.cedula')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.rif" :value="__('RIF')" />
                                <x-text-input 
                                    id="editForm.rif" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.rif" 
                                    :value="old('editForm.rif')" 
                                    placeholder=""
                                    maxlength="14" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('editForm.rif')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.direccion" :value="__('Direccion')" />
                                <x-text-input id="editForm.direccion" class="block mt-2 w-full"  wire:model="editForm.direccion" :value="old('editForm.direccion')" placeholder="" />
                                <x-input-error :messages="$errors->get('editForm.direccion')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="editForm.telefono" :value="__('Teléfono')" />
                                <x-text-input 
                                    id="editForm.telefono" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.telefono" 
                                    :value="old('editForm.telefono')" 
                                    placeholder=""
                                    maxlength="12" 
                                    inputmode="numeric" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required 
                                />
                                <x-input-error :messages="$errors->get('editForm.telefono')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.correo" :value="__('Correo')" />
                                <x-text-input id="editForm.correo" class="block mt-2 w-full" type="text" wire:model.blur="editForm.correo" :value="old('editForm.correo')" placeholder="" />
                                <x-input-error :messages="$errors->get('editForm.correo')" class="mt-2" />
                            </div>
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
