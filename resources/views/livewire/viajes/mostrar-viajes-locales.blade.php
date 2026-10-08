<div>
    <div>
        @section('titulo')
            Viajes
        @endsection
        
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Viajes</h1>
                        
                    </div>
            
                <div class="mb-6 sm:mb-0 mt-10">

                    @can('Crear Viajes locales')    
                        <x-primary-button wire:click="create">
                            Crear Viaje
                        </x-primary-button>
                     @endcan

                    <x-secondary-button wire:click="generateViajeReportPDF">
                        Imprimir PDF
                    </x-secondary-button> 

                    <x-secondary-button wire:click="exportViajesToExcel">
                        Descargar Excel
                    </x-secondary-button> 
                </div>

                </div>
                <div class="flex flex-col md:flex-row">
                    <x-tab-link :href="route('ver-rutas-locales')" :active="request()->routeIs('ver-rutas-locales')" wire:navigate.hover>
                        {{ __('Rutas') }}
                    </x-tab-link>
        
                    <x-tab-link :href="route('ver-viajes-locales')" :active="request()->routeIs('ver-viajes-locales')" wire:navigate.hover>
                        {{ __('Viajes') }}
                    </x-tab-link>
                </div>
                    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
                        <div class="flex items-center justify-between p-4">
                            <!-- Barra de búsqueda -->
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
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2"
                                        placeholder="Buscar...">
                                </div>
                            </div>
                        
                            <!-- Selector de semana -->
                            <div class="flex items-center w-full md:w-auto">
                                <div class="relative">
                                    <label for="week" class="block text-sm font-medium text-gray-700 mb-1">Semana</label>
                                    <input type="week" id="week" wire:model.live="week"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-3 p-2">
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
                                        @include('livewire.includes.sort-table', ['column' => 'fecha_salida', 'displayName' => 'Semana de Salida'])
                                        @include('livewire.includes.sort-table', ['column' => 'proveedor', 'displayName' => 'vehículos'])
                                        @include('livewire.includes.sort-table', ['column' => 'estado', 'displayName' => 'Estado'])
                                        <th class="px-4 py-3 text-left">Acciones</th>
                                        <th class="px-4 py-3 text-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($viajes as $viaje)
                                        <tr class="border-b bg-white hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $viaje->codigo }}</td>
                                            <td class="px-4 py-2">{{ $viaje->ruta?->ruta ?? 'Ruta no disponible' }}</td>
                                            <td class="px-4 py-2">{{ $viaje->diaSemana?->dia_semana ?? 'Sin día' }}</td>
                                            <td class="px-4 py-2">{{ $viaje->fecha_salida }}</td>
                                            <td class="px-4 py-2">
                                                @if($viaje->vehiculo)
                                                    {{ $viaje->vehiculo->placa ?? 'Sin placa' }} {{ $viaje->vehiculo->marca ?? 'Sin marca' }}
                                                @else
                                                    <span class="text-gray-400 italic">Sin vehículo</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 {{ $viaje->activo ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $viaje->activo ? 'Activo' : 'Inactivo' }}
                                            </td>
                                            <th class="px-4 py-2 font-medium flex space-x-2">
                                                
                                                <!-- Botones existentes (Editar, Activar/Desactivar) -->
                                                <button wire:click="edit({{ $viaje->viaje_id, $viaje->proveedor_id }})" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                      </svg>
                                                      
                                                </button>
                                
                                                @if ($viaje['activo'])
                                                    <button wire:click="desactivar({{ $viaje->viaje_id }})" title="Desactivar" class="text-white p-2 rounded">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button wire:click="activar({{ $viaje->viaje_id }})" title="Activar" class="text-white p-2 rounded">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </th>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No hay viajes para la semana seleccionada</td>
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
                        Registro de Viaje 
                    </x-slot>
            
                    <x-slot name="content">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="createForm.ruta" :value="__('Rutas')" />
                                <select id="createForm.ruta" class="block mt-2 w-full" wire:model.blur="createForm.ruta">
                                    <option value="">{{ __('Seleccione una ruta') }}</option>
                                    @foreach($rutaas as $ruta)
                                        <option value="{{ $ruta->ruta_id }}">{{ $ruta->ruta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="createForm.fecha" :value="__('Semana de Salida')" />
                                <x-text-input 
                                    id="createForm.fecha" 
                                    class="block mt-2 w-full" 
                                    type="week" 
                                    wire:model="createForm.fecha" 
                                    :value="old('createForm.fecha')" 
                                    min="{{ now()->format('Y-\WW') }}" 
                                    autocomplete="fecha" 
                                    required 
                                />
                                <x-input-error :messages="$errors->get('createForm.fecha')" class="mt-2" />
                            </div> 
                            
                            <!-- Ciclo para generar selects de viajes -->
                            @foreach($trips as $index => $trip)
                            <div class="col-span-2 grid grid-cols-3 gap-4 mt-4 relative">
                                <div>
                                    <x-input-label for="dia_semana_{{ $index }}" :value="__('Día de la Semana')" />
                                    <select id="dia_semana_{{ $index }}" class="block mt-2 w-full" wire:model.blur="trips.{{ $index }}.dia_semana" autocomplete="dia_semana" required>
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
                                    <x-input-label for="vehiculo_id_{{ $index }}" :value="__('Vehículo')" />
                                    <select id="vehiculo_id_{{ $index }}" class="block mt-2 w-full" wire:model="trips.{{ $index }}.vehiculo_id">
                                        <option value="">{{ __('Seleccione un vehículo') }}</option>
                                        @foreach($vehiculos as $vehiculo)
                                            <option value="{{ $vehiculo->vehiculo_id }}">{{ $vehiculo->marca ?? 'Sin marca' }} {{ $vehiculo->placa ?? 'Sin placa' }}</option>
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
                                    @foreach($rutaas as $ruta)
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
                                    type="week" 
                                    wire:model.blur="editForm.fecha" 
                                    :value="old('editForm.fecha')" 
                                    min="{{ now()->format('Y-\WW') }}" 
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
