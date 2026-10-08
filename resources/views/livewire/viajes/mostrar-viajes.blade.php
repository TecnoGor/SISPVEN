<div>
    <div>
        @section('titulo')
            Viajes
        @endsection
        
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl md:text-3xl text-gray-100 font-bold mt-10">Rutas</h1>
                        
                    </div>
                    @can('Crear usuarios')    
                        <div class="mb-6 sm:mb-0 mt-10">
                            <x-primary-button wire:click="create">
                               Crear Viaje 
                            </x-primary-button>
                        </div>
                    @endcan
            
            
                </div>
                <div class="flex flex-col md:flex-row">
                    <x-tab-link :href="route('ver-rutas')" :active="request()->routeIs('ver-rutas')" wire:navigate.hover>
                        {{ __('Rutas') }}
                    </x-tab-link>
        
                    <x-tab-link :href="route('ver-viajes')" :active="request()->routeIs('ver-viajes')" wire:navigate.hover>
                        {{ __('Viajes') }}
                    </x-tab-link>
                </div>
                    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
                        <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
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
                            <table class="w-full text-sm table-auto border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                                    <tr>
                                        @include('livewire.includes.sort-table', ['column' => 'codigo', 'displayName' => 'Código'])
                                        @include('livewire.includes.sort-table', ['column' => 'ruta', 'displayName' => 'Ruta'])
                                        @include('livewire.includes.sort-table', ['column' => 'dia_salida', 'displayName' => 'Día de Salida'])
                                        @include('livewire.includes.sort-table', ['column' => 'fecha_salida', 'displayName' => 'Fecha de Salida'])
                                        @include('livewire.includes.sort-table', ['column' => 'proveedor', 'displayName' => 'Proveedor'])
                                        @include('livewire.includes.sort-table', ['column' => 'vehiculo', 'displayName' => 'Vehículo'])
                                        @include('livewire.includes.sort-table', ['column' => 'estado', 'displayName' => 'Estado'])
                                        <th class="px-4 py-3 text-left">Acciones</th>
                                        <th class="px-4 py-3 text-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($viajes as $viaje)
                                        <tr wire:key="{{ $viaje->viaje_id }}" class="border-b text-left bg-white hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->codigo }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->ruta->ruta ?? 'Ruta no disponible' }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->diaSemana->dia_semana }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->fecha_salida }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->proveedor->razon_social }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $viaje->vehiculo->placa }} {{ $viaje->vehiculo->marca }}</td>
                                            <td class="px-4 py-2 font-medium {{ $viaje->activo ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $viaje->activo ? 'Activo' : 'Inactivo' }}
                                            </td>
                                            <th class="px-4 py-2 font-medium flex space-x-2">                           
                                                <button wire:click="edit({{ $viaje->viaje_id, $viaje->proveedor_id  }})" title="Editar">
                                                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                                         <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                                                       </svg>     
                                                 </button>
                                      
                                                     @if ($viaje['activo'])
                                                     <!-- Botón para desactivar -->
                                                         <button wire:click="desactivar({{ $viaje->viaje_id}})" title="Desactivar" class=" text-white p-2 rounded">
                                                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                             </svg>
                                                         </button>
                                                     @else
                                                         <!-- Botón para activar -->
                                                         <button wire:click="activar({{  $viaje->viaje_id }})" title="Activar" class=" text-white p-2 rounded">
                                                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                             </svg>                                      
                                                         </button>
                                                        
                                                     @endif
                                            </th>  
                                        </tr>
                                    @empty
                                        <tr class="border-b text-center">
                                            <td colspan="8" class="py-7 text-gray-500 text-lg">No hay Viajes</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                
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
                            </div>
                            {{ $viajes->links() }}
                        </div>
            </div> 
            <form wire:submit="store">
                <x-dialog-modal wire:model="createForm.open">
                    <x-slot name="title">
                        Crear Viaje 
                    </x-slot>
            
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="createForm.ruta" :value="__('Rutas')" />
                                <select id="createForm.ruta" class="block mt-2 w-full" wire:model.blur="createForm.ruta">
                                    <option value="">{{ __('Seleccione una ruta') }}</option>
                                    @foreach($rutas as $ruta)
                                        <option value="{{ $ruta->ruta_id }}">{{ $ruta->ruta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="createForm.fecha" :value="__('Semana de Salida')" />
                                <x-text-input 
                                    id="createForm.fecha" 
                                    class="block mt-2 w-full" 
                                    type="date" 
                                    wire:model="createForm.fecha" 
                                    :value="old('createForm.fecha')" 
                                    autocomplete="fecha" 
                                    required 
                                    min="{{ now()->format('Y-m-d') }}"
                                />
                                <x-input-error :messages="$errors->get('createForm.fecha')" class="mt-2" />
                            </div>
                            
            
                            <!-- Ciclo para generar selects de viajes -->
                            @foreach($trips as $index => $trip)
                            <div class="col-span-2 grid grid-cols-3 gap-4 mt-4 relative">
                                <div>
                                    <x-input-label for="dia_semana_{{ $index }}" :value="__('Día de la Semana')" />
                                    <select id="dia_semana_{{ $index }}" class="block mt-2 w-full" wire:model="trips.{{ $index }}.dia_semana" autocomplete="dia_semana" required>
                                        <option value="">{{ __('Seleccione un día') }}</option>
                                        <option value="1">{{ __('Lunes') }}</option>
                                        <option value="2">{{ __('Martes') }}</option>
                                        <option value="3">{{ __('Miércoles') }}</option>
                                        <option value="4">{{ __('Jueves') }}</option>
                                        <option value="5">{{ __('Viernes') }}</option>
                                        <option value="6">{{ __('Sábado') }}</option>
                                        <option value="7">{{ __('Domingo') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('trips.'.$index.'.dia_semana')" class="mt-2" />
                                </div>
                        
            
                                <div>
                                    <x-input-label for="proveedor_id_{{ $index }}" :value="__('Proveedor')" />
                                    <select id="proveedor_id_{{ $index }}" class="block mt-2 w-full" wire:model.blur="trips.{{ $index }}.proveedor_id">
                                        <option value="">{{ __('Seleccione un proveedor') }}</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->proveedor_id }}">{{ $proveedor->representante_legal }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div>
                                    <x-input-label for="vehiculo_id_{{ $index }}" :value="__('Vehículo')" />
                                    <select id="vehiculo_id_{{ $index }}" class="block mt-2 w-full" wire:model="trips.{{ $index }}.vehiculo_id">
                                        <option value="">{{ __('Seleccione un vehículo') }}</option>
                                        @foreach($this->getVehiculosByProveedor($trip['proveedor_id']) as $vehiculo)
                                            <option value="{{ $vehiculo->vehiculo_id }}">{{ $vehiculo->marca }} {{ $vehiculo->placa }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                    <button type="button" class="absolute top-0 right-0 text-red-500 hover:text-red-700"
                                    wire:click.prevent="removeTrip({{ $index }})" title="Eliminar este viaje">
                                &#x2716;
                            </button>
                        </div>
                    @endforeach
            
                            <!-- Botón para agregar otro viaje -->
                            <div class="mt-4">
                                <x-primary-button wire:click.prevent="addAnotherTrip" type="button">
                                   Generar Viajes +
                                </x-primary-button>
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
                       Editar Viaje
                    </x-slot>
        
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="editForm.ruta" :value="__('Rutas')" />
                                <select id="editForm.ruta" class="block mt-2 w-full" wire:model.blur="editForm.ruta" :value="old('editForm.ruta')" autocomplete="ruta"  required>
                                    <option value="">{{ __('Seleccione una ruta') }}</option>
                                    @foreach($rutas as $ruta)
                                        <option value="{{ $ruta->ruta_id }}">{{ $ruta->ruta }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('editForm.ruta')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('editForm.proveedor_id')" class="mt-2" />
                                
                            </div> 

                            <div>
                                <x-input-label for="editForm.fecha" :value="__('Fecha de Salida')" />
                                <x-text-input 
                                    id="editForm.fecha" 
                                    class="block mt-2 w-full" 
                                    type="date" 
                                    wire:model.blur="editForm.fecha" 
                                    :value="old('editForm.fecha')" 
                                    min="{{ now()->format('Y-m-d') }}" 
                                    autocomplete="fecha" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('editForm.fecha')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="editForm.dia_semana" :value="__('Día de la Semana')" />
                                <select id="editForm.dia_semana" class="block mt-2 w-full" wire:model.blur="editForm.dia_semana" :value="old('editForm.dia_semana')" autocomplete="dia_semana" required>
                                    <option value="">{{ __('Seleccione un día') }}</option>
                                    <option value="1">{{ __('Lunes') }}</option>
                                    <option value="2">{{ __('Martes') }}</option>
                                    <option value="3">{{ __('Miércoles') }}</option>
                                    <option value="4">{{ __('Jueves') }}</option>
                                    <option value="5">{{ __('Viernes') }}</option>
                                    <option value="6">{{ __('Sábado') }}</option>
                                    <option value="7">{{ __('Domingo') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('editForm.dia_semana')" class="mt-2" />
                            </div>
                        </div>
                        
                    </x-slot>
        
                    <x-slot name="footer">
                        <div class="flex justify-end">
                            <x-danger-button class="mr-2" wire:click="$set('editForm.open', false)" type="button">
                                Cancelar
                            </x-danger-button>
        
                            <x-primary-button wire:loading.attr="disabled" wire:target="update">
                                Crear
        
                                <x-loading-button wire:target="update"/>
                            </x-primary-button>
                        </div>
                    </x-slot>
                </x-dialog-modal>
            </form> 
        </div>
        
</div>
