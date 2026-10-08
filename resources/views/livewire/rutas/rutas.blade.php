<div>
@section('titulo')
    Rutas
@endsection

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-100 font-bold mt-10">Rutas</h1>
                
            </div>
            @can('Crear usuarios')    
                <div class="mb-6 sm:mb-0 mt-10">
                    <x-primary-button wire:click="create">
                       Crear Ruta   
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
                            <tr class="">
                                @include('livewire.includes.sort-table', ['column' => 'origen', 'displayName' => 'Plataforma de Origen'])
                                @include('livewire.includes.sort-table', ['column' => 'email', 'displayName' => 'Destino'])
                                @include('livewire.includes.sort-table', ['column' => 'siglas', 'displayName' => 'Siglas'])
                                @include('livewire.includes.sort-table', ['column' => 'distancia', 'displayName' => 'Distancian en KM'])
                                @include('livewire.includes.sort-table', ['column' => 'activo', 'displayName' => 'Estatus'])
                                <th class="px-4 py-3 text-center">Acciones</th>
                                <th class="px-4 py-3 text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rutas as $ruta)
                                <tr wire:key="{{ $ruta->ruta_id }}" class="border-b text-left">
                                    <th class="px-4 py-3 font-medium text-black">{{ $ruta->oficinaOrigen->nombre }} </th>
                                    <th class="px-4 py-3 font-medium text-black">{{ $ruta->oficinaDestino->nombre }} </th>
                                    <th class="px-4 py-3 font-medium text-black">{{ $ruta->siglas }}</th>
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
                                        <button wire:click="edit({{ $ruta->ruta_id }})" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                                              </svg>     
                                        </button>
                                    </th>
                                </tr>
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
                <div class="flex flex-col items-center gap-4">
                    <div class="flex flex-wrap justify-center gap-4 mb-2">
                        <!-- Plataforma de Origen -->
                        <div class="w-full sm:w-auto flex flex-col items-center">
                            <x-input-label for="createForm.origen" :value="__('Plataforma de origen')" class="text-center mb-1" />
                            <select id="createForm.origen" class="block w-48 text-center max-h-48 overflow-y-auto" wire:model.defer="createForm.origen" required>
                                    <option value="">{{ __('Seleccione un estado') }}</option>
                                    @foreach ($oficinas as $oficina)
                                        <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                    @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('createForm.origen')" class="mt-1" />
                        </div>
                        
                        
                    
                        <!-- Siglas 1 -->
                        <div class="w-full sm:w-auto flex flex-col items-center">
                            <x-input-label for="createForm.siglas1" :value="__('Siglas')" class="text-center mb-1" />
                            <x-text-input id="createForm.siglas1" class="block w-24 text-center" type="tel" wire:model.blur="createForm.siglas1" placeholder="" />
                            <x-input-error :messages="$errors->get('createForm.siglas1')" class="mt-1" />
                        </div>
                    
                        <!-- Punto de Destino -->
                        <div class="w-full sm:w-auto flex flex-col items-center">
                            <x-input-label for="createForm.destino" :value="__('Punto de Destino')" class="text-center mb-1" />
                            <select id="createForm.destino" class="block w-48 text-center" wire:model.blur="createForm.destino" required>
                                <option value="">{{ __('Seleccione una plataforma') }}</option>
                                @foreach ($oficinas as $oficina)
                                <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                            @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('createForm.destino')" class="mt-1" />
                        </div>
                    
                        <!-- Siglas 2 -->
                        <div class="w-full sm:w-auto flex flex-col items-center">
                            <x-input-label for="createForm.siglas2" :value="__('Siglas')" class="text-center mb-1" />
                            <x-text-input id="createForm.siglas2" class="block w-24 text-center" type="tel" wire:model.blur="createForm.siglas2" placeholder="" />
                            <x-input-error :messages="$errors->get('createForm.siglas2')" class="mt-1" />
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
                    
                            <!-- Campo para Sigla 3 -->
                            <div class="w-1/4">
                                <x-input-label for="createForm.sigla3.{{ $index }}" :value="__('Siglas')" />
                                <x-text-input id="createForm.sigla3.{{ $index }}" class="block w-full mt-1" type="text" wire:model.defer="createForm.sigla3.{{ $index }}" placeholder="Siglas" />
                                <x-input-error :messages="$errors->get('sigla3.'.$index)" class="mt-1" />
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
                            <x-text-input id="createForm.distancia" class="block w-1/2 mt-1" type="text" wire:model.defer="createForm.distancia" placeholder="" />
                            <x-input-error :messages="$errors->get('createForm.distancia')" class="mt-1" />
                        </div>
                        <div class="w-1/2 flex flex-col items-center">
                            <x-input-label for="createForm.tiempo" :value="__('Tiempo Estimado (Horas)')" />
                            <x-text-input id="createForm.tiempo" class="block w-1/2 mt-1" type="tel" wire:model.defer="createForm.tiempo" placeholder="" />
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
                        <x-input-label for="editForm.siglas" :value="__('Siglas')" />
                        <x-text-input id="editForm.siglas" class="block mt-2 w-1/2" type="tel" wire:model.blur="editForm.siglas" :value="old('createForm.siglas')" placeholder="" />
                        <x-input-error :messages="$errors->get('editForm.siglas')" class="mt-2" />
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
