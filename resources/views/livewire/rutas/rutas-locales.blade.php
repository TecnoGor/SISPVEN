<div>
    @section('titulo')
        Rutas
    @endsection
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Rutas</h1>
                    
                </div>
        
                <div class="mb-6 sm:mb-0 mt-10">

                    @can('Crear rutas locales')    
   
                    <x-primary-button wire:click="create">
                       Crear Ruta 
                    </x-primary-button>
         
                 @endcan

                    <x-secondary-button wire:click="exportarPDF">
                        Imprimir PDF
                    </x-secondary-button> 

                    <x-secondary-button wire:click="exportarExcel">
                        Imprimir Excel
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
                                <tr class="">
                                    @include('livewire.includes.sort-table', ['column' => 'origen', 'displayName' => 'Ruta'])
                                    @include('livewire.includes.sort-table', ['column' => 'origen', 'displayName' => 'Plataforma de Origen'])
                                    @include('livewire.includes.sort-table', ['column' => 'email', 'displayName' => 'Destino'])
                                    @include('livewire.includes.sort-table', ['column' => 'distancia', 'displayName' => 'Distancian en KM'])
                                    @include('livewire.includes.sort-table', ['column' => 'activo', 'displayName' => 'Estatus'])
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                    <th class="px-4 py-3 text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rutas as $ruta)
                                    <tr wire:key="{{ $ruta->ruta_id }}" class="border-b text-left">
                                        <th class="px-4 py-3 font-medium text-black">{{ $ruta->ruta}} </th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $ruta->oficinaOrigen->nombre }} </th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $ruta->oficinaDestino->nombre }} </th>
                                        <th class="px-4 py-3 font-medium text-black">{{ $ruta->distancia }}</th>
                                        <td class="px-4 py-3 font-medium {{ $ruta['activo'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $ruta['activo'] ? 'Activo' : 'Inactivo' }}
                                        </td>
                                        <th class="px-4 py-2 font-medium flex space-x-2">   

                                            @if ($ruta['activo'])
                                            <!-- Botón para desactivar -->
                                                <button wire:click="desactivar({{ $ruta->ruta_id }})" title="Desactivar" class=" text-white p-2 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <!-- Botón para activar -->
                                                <button wire:click="activar({{  $ruta->ruta_id }})" title="Activar" class=" text-white p-2 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>                                      
                                                </button>
                                               
                                            @endif
                                            
  
                                            <button wire:click="togglePuntosEntrega({{ $ruta->ruta_id }})" class="text-black">
                                                @if (in_array($ruta->ruta_id, $expandedRutas))
                                                    ▲ 
                                                @else
                                                    ▼ 
                                                @endif
                                            </button>
                                        </th>
                                    </tr>
                                
                                    @if (in_array($ruta->ruta_id, $expandedRutas))
                                        <div class="ml-8 mt-2">
                                            <tr class="border-b text-left">
                                                <td colspan="5" class="px-4 py-3 text-black text-left">
                                                    <h4 class="text-sm font-semibold">{{ __('Toques:') }}</h4>
                                                    <ul class="list-disc list-inside text-sm text-black">
                                                        @foreach ($ruta->puntosEntrega as $punto)
                                                            <li>{{ $punto->oficina->nombre }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        </div>
                                    @endif                                 
                                @empty
                                    <tr class="border-b text-center">
                                        <th colspan="5" class="py-7 text-default text-2xl">No hay Rutas</th>
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
                        {{ $rutas->links() }}
                    </div>
        </div> 
        <form wire:submit="store">
            <x-dialog-modal wire:model="createForm.open">
                <x-slot name="title">
                    Crear Ruta
                </x-slot>
        
                <x-slot name="content">

            <div class="w-full flex flex-col items-center mb-4">
                <div class="w-full flex flex-col items-center mb-4">
                    <x-input-label for="createForm.nombre" :value="__('Nombre de la Ruta')" class="text-center mb-1" />
                    <x-text-input 
                        id="createForm.nombre" 
                        class="block w-full sm:w-2/3 text-center" 
                        type="text" 
                        wire:model.defer="createForm.nombre" 
                        placeholder="Ingrese el nombre de la ruta" 
                        required 
                        pattern="[a-zA-Z0-9\s]+" 
                        title="Solo se permiten letras, números y espacios."
                    />
                    <x-input-error :messages="$errors->get('createForm.nombre')" class="mt-1" />
                </div>


                    <div class="flex flex-col items-center gap-4">
                        <div class="flex flex-wrap justify-center gap-4 mb-2">
                            <!-- Plataforma de Origen -->
                            <div class="w-full sm:w-auto flex flex-col items-center">
                                <x-input-label for="createForm.origen" :value="__('Plataforma de origen')" class="text-center mb-1" />
                                <div class="w-48" wire:ignore x-data x-init="
                                    new TomSelect($refs.selectOrigen, {
                                        dropdownParent: 'body',
                                        onChange(value) {
                                            @this.set('createForm.origen', value)
                                        }
                                    })
                                ">
                                    <select x-ref="selectOrigen" id="createForm.origen" class="block w-48 text-center">
                                        <option value="">{{ __('Origen') }}</option>
                                        @if (!empty($oficinasConEstado) && $oficinasConEstado->count())
                                            @foreach ($oficinasConEstado as $oficinaestado)
                                                <option value="{{ $oficinaestado->oficina_id }}" @selected($createForm->origen == $oficinaestado->oficina_id)>{{ $oficinaestado->nombre }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>{{ __('No hay oficinas disponibles') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('createForm.origen')" class="mt-1" />
                            </div>
                            
  
                      
                    
                            <!-- Punto de Destino -->
                            <div class="w-full sm:w-auto flex flex-col items-center">
                                <x-input-label for="createForm.destino" :value="__('Punto de Destino')" class="text-center mb-1" />
                                <div class="w-48" wire:ignore x-data x-init="
                                    new TomSelect($refs.selectDestino, {
                                        dropdownParent: 'body',
                                        onChange(value) {
                                            @this.set('createForm.destino', value)
                                        }
                                    })
                                ">
                                    <select x-ref="selectDestino" id="createForm.destino" class="block w-48 text-center">
                                        <option value="">{{ __('Destino') }}</option>
                                        @foreach ($oficinas as $oficina)
                                            <option value="{{ $oficina->oficina_id }}" @selected($createForm->destino == $oficina->oficina_id)>{{ $oficina->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('createForm.destino')" class="mt-1" />
                            </div>
                        
                            
                        </div>
                
                        <!-- Botón para añadir punto de entrega -->
                        <div class="flex justify-center mt-4">
                            <x-primary-button wire:click.prevent="addPuntoEntrega">
                                + Añadir Punto de Entrega
                            </x-primary-button>
                        </div>
                        
                          <!-- Selects de Puntos de Entrega dinámicos -->
                          @foreach ($puntosEntrega as $index => $punto)
                          <div class="flex items-center gap-4 w-full justify-center">
                              <div class="w-1/2">
                                  <x-input-label for="createForm.puntosEntrega.{{ $index }}" :value="__('Punto de Entrega #'.($index + 1))" />
                                  <select id="createForm.puntosEntrega.{{ $index }}" class="block w-full mt-1" wire:model.defer="createForm.puntosEntrega.{{ $index }}">
                                      <option value="">{{ __('Seleccione una ubicación') }}</option>
                                      @foreach ($oficinas as $oficina)
                                          <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                      @endforeach
                                  </select>
                                  <x-input-error :messages="$errors->get('puntosEntrega.'.$index)" class="mt-1" />
                              </div>
                              <!-- Botón para eliminar punto de entrega -->
                              <button wire:click.prevent="removePuntoEntrega({{ $index }})" class="text-lg font-bold mt-6 ml-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-red-500">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                  </svg>
                              </button>
                          </div>
                      @endforeach
                                            <!-- Distancia y Tiempo Estimado -->
                                            <div class="flex gap-4 justify-center w-full">
                                                <div class="w-1/2 flex flex-col items-center">
                                                    <x-input-label for="createForm.distancia" :value="__('Distancia Km')" />
                                                    <x-text-input 
                                                        id="createForm.distancia" 
                                                        class="block w-1/2 mt-1" 
                                                        type="number" 
                                                        wire:model.defer="createForm.distancia" 
                                                        placeholder="Ej. 150" 
                                                        min="1" 
                                                        required 
                                                    />
                                                    <x-input-error :messages="$errors->get('createForm.distancia')" class="mt-1" />
                                                </div>
                                                <div class="w-1/2 flex flex-col items-center">
                                                    <x-input-label for="createForm.tiempo" :value="__('Tiempo Estimado (Horas)')" />
                                                    <x-text-input 
                                                        id="createForm.tiempo" 
                                                        class="block w-1/2 mt-1" 
                                                        type="number" 
                                                        wire:model.defer="createForm.tiempo" 
                                                        placeholder="Ej. 3" 
                                                        min="1" 
                                                        required 
                                                    />
                                                    <x-input-error :messages="$errors->get('createForm.tiempo')" class="mt-1" />
                                                </div>
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
                    Actualizar Ruta
                </x-slot>
    
                <x-slot name="content">
                    <div class="mb-4 grid grid-cols-2 gap-4">

                        <div>
                            <x-input-label for="editForm.nombre" :value="__('Nombre de la ruta')" />
                            <x-text-input id="editForm.nombre" type="text" class="block mt-2 w-full" 
                                wire:model.blur="editForm.nombre" 
                                placeholder="Escribe el nombre de la ruta" required />
                            <x-input-error :messages="$errors->get('editForm.nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="editForm.origen" :value="__('Plataforma de origen')" />
                            <select id="editForm.origen" class="block mt-2 w-full" wire:model.blur="editForm.origen" required>
                                <option value="">{{ __('Seleccione un estado') }}</option>
                                @foreach ($oficinas as $oficina)
                                            <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                            @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('editForm.origen')" class="mt-2" />
                        </div> 
                       
                        <div>
                            <x-input-label for="editForm.destino" :value="__('Punto de Destino')" />
                            <select id="editForm.destino" class="block mt-2 w-full" wire:model.blur="editForm.destino" required>
                                <option value="">{{ __('Seleccione una ubicación') }}</option>
                                @foreach ($oficinas as $oficina)
                                            <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('createForm.destino')" class="mt-2" />
                        </div> 
                        <div>
                            <x-input-label for="editForm.distancia" :value="__('Distancia Km')" />
                            <x-text-input id="editForm.distancia" class="block mt-2 w-full" type="text" wire:model.blur="editForm.distancia" :value="old('editForm.distancia')" placeholder="" />
                            <x-input-error :messages="$errors->get('editForm.distancia')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.tiempo" :value="__('Tiempo Estimado')" />
                            <x-text-input id="editForm.tiempo" class="block mt-2 w-1/2" type="tel" wire:model.blur="editForm.tiempo" :value="old('editForm.tiempo')" placeholder="" />
                            <x-input-error :messages="$errors->get('editForm.tiempo')" class="mt-2" />
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
    