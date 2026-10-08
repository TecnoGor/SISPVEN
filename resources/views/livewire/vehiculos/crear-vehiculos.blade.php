<div>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-4 mt-10">Vehiculos</h1>
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Marca</th>
                        <th class="px-4 py-3 text-left">Modelo</th>
                        <th class="px-4 py-3 text-left">Placa</th>
                        <th class="px-4 py-3 text-left">Color</th>
                        <th class="px-4 py-3 text-left">Año</th>
                        <th class="px-4 py-3 text-left">chofer</th>
                        <th class="px-4 py-3 text-left">Estatus</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                        <th class="px-4 py-3 text-left"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $vehiculo)
                    <tr wire:key="{{ $vehiculo->vehiculo_id }}" class="border-b text-left">
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->marca }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->modelo }}</td>
                        <td class="px-4 py-3 font-medium text-black text-center">{{ $vehiculo->placa }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->color }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->año }}</td>
                        <td class="px-4 py-3 font-medium text-black">
                            {{ $vehiculo->chofer ? $vehiculo->chofer->nombre : 'Ninguno' }}
                        </td>
                        <td class="px-4 py-3 font-medium {{ $vehiculo['Activo'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $vehiculo['Activo'] ? 'Activo' : 'Inactivo' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-black text-left">    
                            <button  wire:click="edit({{ $vehiculo->vehiculo_id }})" title="Editar" wire:navigate.hover>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                            </button>

                             <button wire:click="edit2({{ $vehiculo->vehiculo_id }})" title="Asignar Chofer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6  text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                      </svg>
                                </button>
                            @if ($vehiculo['Activo'])
                            <!-- Botón para desactivar -->
                                <button wire:click="desactivar({{$vehiculo->vehiculo_id }})" title="Desactivar" class=" text-white p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>
                            @else
                                <!-- Botón para activar -->
                                <button wire:click="activar({{ $vehiculo->vehiculo_id }})" title="Activar" class=" text-white p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>                                      
                                </button>
                               
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr class="px-4 py-3 font-medium text-black text-left">
                            <td colspan="8" class="py-7 text-default text-2xl">No hay Vehiculos</td>
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
                            Crear Vehiculo
                        </x-primary-button>
                    </div>
                </div>
            </div>
            <form wire:submit="store">
                <x-dialog-modal wire:model="createForm.open">
                    <x-slot name="title">
                        Crear Vehiculo
                    </x-slot>
        
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="createForm.placa" :value="__('Placa')" />
                                <x-text-input 
                                    id="createForm.placa" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.placa" 
                                    :value="old('createForm.placa')" 
                                    placeholder=""
                                    maxlength="8" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.placa')" class="mt-2" />
                            </div>
                            
                    
                            <div>
                                <x-input-label for="createForm.color" :value="__('Color')" />
                                <x-text-input 
                                    id="createForm.color" 
                                    class="block mt-2 w-full" 
                                    type="text"  
                                    wire:model.blur="createForm.color" 
                                    :value="old('createForm.color')" 
                                    placeholder=""
                                    maxlength="12" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.color')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.año" :value="__('Año')" />
                                <select 
                                    id="createForm.año" 
                                    class="block mt-2 w-full" 
                                    wire:model.blur="createForm.año" 
                                    required
                                >
                                    <option value="" selected disabled>Selecciona un año</option>
                                    @foreach(range(date('Y'), 1960) as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('createForm.año')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.fecha_vecimiento" :value="__('Fecha de Vencimiento De la Poliza')" />
                                <x-text-input id="createForm.fecha_vecimiento" class="block mt-2 w-full" type="date" wire:model="createForm.fecha_vecimiento" :value="old('createForm.fecha_vecimiento')" placeholder="" />
                                <x-input-error :messages="$errors->get('createForm.fecha_vecimiento')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="createForm.poliza" :value="__('Número de Póliza')" />
                                <x-text-input 
                                    id="createForm.poliza" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.poliza" 
                                    :value="old('createForm.poliza')" 
                                    placeholder=""
                                    maxlength="20"  
                                    required 
                                />


                                <x-input-error :messages="$errors->get('createForm.poliza')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.capacidad" :value="__('Capacidad en Kg')" />
                                <x-text-input 
                                    id="createForm.capacidad" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.capacidad" 
                                    :value="old('createForm.capacidad')" 
                                    placeholder=""
                                    maxlength="12" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.capacidad')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.marca" :value="__('Marca')" />
                                <x-text-input 
                                    id="createForm.marca" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.marca" 
                                    :value="old('createForm.marca')" 
                                    placeholder="" 
                                    maxlength="14" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.marca')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="createForm.modelo" :value="__('Modelo')" />
                                <x-text-input 
                                    id="createForm.modelo" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="createForm.modelo" 
                                    :value="old('createForm.modelo')" 
                                    placeholder="" 
                                    maxlength="14" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.modelo')" class="mt-2" />
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
                <x-dialog-modal wire:model="editForm.open">
                    <x-slot name="title">
                       Editar vehiculo
                    </x-slot>
        
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="editForm.placa" :value="__('Placa')" />
                                <x-text-input 
                                    id="editForm.placa" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.placa" 
                                    :value="old('editForm.placa')" 
                                    placeholder="" 
                                    required 
                                    maxlength="8"  
                                />
                                <x-input-error :messages="$errors->get('editForm.placa')" class="mt-2" />
                            </div>
                            
                    
                            <div>
                                <x-input-label for="editForm.color" :value="__('Color')" />
                                <x-text-input 
                                    id="editForm.color" 
                                    class="block mt-2 w-full" 
                                    type="tel" 
                                    wire:model.blur="editForm.color" 
                                    :value="old('editForm.color')" 
                                    placeholder="" 
                                    maxlength="12" 
                                />
                                <x-input-error :messages="$errors->get('editForm.color')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.año" :value="__('Año')" />
                                <select id="editForm.año" class="block mt-2 w-full" wire:model="editForm.año">
                                    @for ($year = 1960; $year <= date('Y'); $year++)
                                        <option value="{{ $year }}" {{ old('editForm.año') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('editForm.año')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.fecha_vecimiento" :value="__('Fecha de Vencimiento De la Poliza')" />
                                <x-text-input id="editForm.fecha_vecimiento" class="block mt-2 w-full" type="date" wire:model="editForm.fecha_vecimiento" :value="old('editForm.fecha_vecimiento')" placeholder="" />
                                <x-input-error :messages="$errors->get('editForm.fecha_vecimiento')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="editForm.poliza" :value="__('Numero de Poliza')" />
                                <x-text-input 
                                    id="editForm.poliza" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.poliza" 
                                    :value="old('editForm.poliza')" 
                                    placeholder="" 
                                    maxlength="20"  
                                />
                                <x-input-error :messages="$errors->get('editForm.poliza')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.capacidad" :value="__('Capacidad en Kg')" />
                                <x-text-input 
                                    id="editForm.capacidad" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.capacidad" 
                                    :value="old('editForm.capacidad')" 
                                    placeholder="" 
                                    maxlength="12" 
                                    pattern="[0-9]{1,12}"  
                                    inputmode="numeric" 
                                />
                                <x-input-error :messages="$errors->get('editForm.capacidad')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.marca" :value="__('Marca')" />
                                <x-text-input 
                                    id="editForm.marca" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.marca" 
                                    :value="old('editForm.marca')" 
                                    placeholder="" 
                                    maxlength="14"  
                                />
                                <x-input-error :messages="$errors->get('editForm.marca')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.modelo" :value="__('Modelo')" />
                                <x-text-input 
                                    id="editForm.modelo" 
                                    class="block mt-2 w-full" 
                                    type="text" 
                                    wire:model.blur="editForm.modelo" 
                                    :value="old('editForm.modelo')" 
                                    placeholder="" 
                                    maxlength="14"  
                                />
                                <x-input-error :messages="$errors->get('editForm.modelo')" class="mt-2" />
                            </div>
                            
                        </div>
                    </x-slot>
        
                    <x-slot name="footer">
                        <div class="flex justify-end">
                            <x-danger-button class="mr-2" wire:click="$set('editForm.open', false)" type="button">
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

            <form wire:submit.prevent="update2">
                <x-dialog-modal wire:model="editForm2.open" class="bg-gray-100 rounded-lg shadow-lg p-6">
                    <x-slot name="title">
                        <div class="text-center text-2xl font-semibold text-gray-800">Asignar Chofer</div>
                    </x-slot>
            
                    <x-slot name="content">
                        <div class="flex justify-center mt-4">
                            <div class="w-3/4">
                                <x-input-label for="editForm2.chofer_id" :value="__('Seleccione un Chofer')" class="text-lg text-gray-700" />
                                <select id="editForm2.chofer_id" class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" wire:model="editForm2.chofer_id">
                                    <option value="">{{ __('Seleccione un Chofer') }}</option>
                                    @foreach($choferes as $chofer)
                                        <option value="{{ $chofer->chofer_id }}">{{ $chofer->nombre }} - {{ $chofer->cedula }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('editForm2.chofer_id')" class="mt-2 text-red-500" />
                            </div>
                        </div>
                    </x-slot>
            
                    <x-slot name="footer">
                        <div class="flex justify-end space-x-3">
                            <x-danger-button class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition ease-in-out duration-200" wire:click="$set('editForm2.open', false)" type="button">
                                Cancelar
                            </x-danger-button>
            
                            <x-primary-button wire:loading.attr="disabled" wire:target="update" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition ease-in-out duration-200">
                                Guardar
                                <x-loading-button wire:target="update"/>
                            </x-primary-button>
                        </div>
                    </x-slot>
                </x-dialog-modal>
            </form>
            
            
</div>
