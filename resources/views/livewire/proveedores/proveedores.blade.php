@section('titulo')
    Proveedores
@endsection
<div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-100 font-bold mt-10">Proveedores</h1>
                
            </div>
            @can('Crear usuarios')    
                <div class="mb-6 sm:mb-0 mt-10">
                    <x-primary-button wire:click="create">
                       Crear Proveedor
                    </x-primary-button>
                </div>
            @endcan
    
    
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
                                @include('livewire.includes.sort-table', ['column' => 'representante', 'displayName' => 'Representante'])
                                @include('livewire.includes.sort-table', ['column' => 'representante', 'displayName' => 'RIF'])
                                @include('livewire.includes.sort-table', ['column' => 'razon_social', 'displayName' => 'Razon Social'])
                                @include('livewire.includes.sort-table', ['column' => 'cedula', 'displayName' => 'Direccion Fiscal'])
                                @include('livewire.includes.sort-table', ['column' => 'telefono', 'displayName' => 'Cedula'])
                                @include('livewire.includes.sort-table', ['column' => 'activo', 'displayName' => 'Estatus'])
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proveedores as $proveedor)
                            <tr wire:key="{{ $proveedor->proveedor_id }}" class="border-b text-left">
                                <th class="px-4 py-3 font-medium text-black text-left">{{ $proveedor->representante_legal }}</th>
                                <th class="px-4 py-3 font-medium text-black text-left">{{ $proveedor->rif }}</th>
                                <th class="px-4 py-3 font-medium text-black text-center">{{ $proveedor->razon_social }}</th>
                                <th class="px-4 py-3 font-medium text-black text-left">{{ $proveedor->direccion_fiscal }}</th>
                                <th class="px-4 py-3 font-medium text-black text-left">{{ $proveedor->cedula }}</th>
                                <th class="px-4 py-3 font-medium {{ $proveedor['activo'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $proveedor['activo'] ? 'Activo' : 'Inactivo' }}
                                </th> 

                                <th class="px-4 py-2 font-medium flex space-x-2">    
                                    <button href="{{ route('detalle-proveedor', $proveedor->proveedor_id ) }}" title="Detalles" wire:navigate.hover>
                                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                          </svg>
                                    </button>
                               
                                    @if ($proveedor['activo'])
                                    <!-- Botón para desactivar -->
                                        <button wire:click="desactivar({{ $proveedor->proveedor_id }})" title="Desactivar" class=" text-white p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <!-- Botón para activar -->
                                        <button wire:click="activar({{ $proveedor->proveedor_id }})" title="Activar" class=" text-white p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>                                      
                                        </button>
                                       
                                    @endif
                                    <button wire:click="edit({{ $proveedor->proveedor_id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                                      </svg>   
                                     </button>
                                </th>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <th colspan="8" class="py-7 text-default text-2xl">No hay Proveedores</th>
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
                </div>
    </div> 

    <form wire:submit="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                Crear Proveedor
            </x-slot>
    
            <x-slot name="content">
                <!-- Sección de Datos del Proveedor -->
                <h3 class="text-lg font-semibold mb-2">Datos del Proveedor</h3>
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="createForm.rif" :value="__('Rif')" />
                        <x-text-input id="createForm.rif" class="block mt-2 w-full" type="text" wire:model.blur="createForm.rif" :value="old('createForm.rif')" placeholder="" required required maxlength="14" />
                        <x-input-error :messages="$errors->get('createForm.rif')" class="mt-2" />
                    </div>
    
                    <div>
                        <x-input-label for="createForm.rsocial" :value="__('Razón Social')" />
                        <x-text-input id="createForm.rsocial" class="block mt-2 w-full" type="text" wire:model.blur="createForm.rsocial" :value="old('createForm.rsocial')" placeholder="" required required maxlength="16" />
                        <x-input-error :messages="$errors->get('createForm.rsocial')" class="mt-2" />
                    </div>
    
                    <div>
                        <x-input-label for="createForm.direccion" :value="__('Dirección Fiscal')" />
                        <x-text-input id="createForm.direccion" class="block mt-2 w-full" type="text" wire:model.blur="createForm.direccion" :value="old('createForm.direccion')" placeholder="" required />
                        <x-input-error :messages="$errors->get('createForm.direccion')" class="mt-2" />
                    </div>
    
                    <div>
                        <x-input-label for="createForm.retencion" :value="__('Retención (opcional)')" />
                        <x-text-input id="createForm.retencion" class="block mt-2 w-full" type="text" wire:model.blur="createForm.retencion" :value="old('createForm.retencion')" placeholder="" />
                        <x-input-error :messages="$errors->get('createForm.retencion')" class="mt-2" />
                    </div>
    
                    <div>
                        <x-input-label for="createForm.contribuyente" :value="__('Contribuyente Especial (opcional)')" />
                        <x-text-input id="createForm.contribuyente" class="block mt-2 w-full" type="text" wire:model.blur="createForm.contribuyente" :value="old('createForm.contribuyente')" placeholder="" />
                        <x-input-error :messages="$errors->get('createForm.contribuyente')" class="mt-2" />
                    </div>
                </div>
    
                <!-- Sección de Datos del Representante -->
                <h3 class="text-lg font-semibold mb-2">Datos del Representante</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="createForm.representante" :value="__('Representante Legal')" />
                        <x-text-input id="createForm.representante" class="block mt-2 w-full" type="text" wire:model.blur="createForm.representante" :value="old('createForm.representante')" placeholder=""  required maxlength="14" />
                        <x-input-error :messages="$errors->get('createForm.representante')" class="mt-2" />
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
                        <x-input-label for="createForm.cedula" :value="__('Cédula')" />
                        <x-text-input 
                            id="createForm.cedula" 
                            class="block mt-2 w-full" 
                            type="text" 
                            wire:model.blur="createForm.cedula" 
                            :value="old('createForm.cedula')" 
                            placeholder=""
                            maxlength="14" 
                            inputmode="numeric" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"  
                            required 
                        />
                        <x-input-error :messages="$errors->get('createForm.cedula')" class="mt-2" />
                    </div>
                    
    
                    <div>
                        <x-input-label for="createForm.correo" :value="__('Correo')" />
                        <x-text-input id="createForm.correo" class="block mt-2 w-full" type="text" wire:model.blur="createForm.correo" :value="old('createForm.correo')" placeholder="" required/>
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
        <x-dialog-modal wire:model="EditForm.open">
            <x-slot name="title">
                Actualizar Proveedor
            </x-slot>
    
            <x-slot name="content">
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="EditForm.representante_legal" :value="__('Representante legal')" />
                        <x-text-input id="EditForm.representante_legal" 
                                      class="block mt-2 w-full" 
                                      type="text" 
                                      wire:model.blur="EditForm.representante_legal" 
                                      :value="old('EditForm.representante_legal')" 
                                      placeholder="" 
                                      required 
                                      maxlength="14" />
                        <x-input-error :messages="$errors->get('EditForm.representante_legal')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label for="EditForm.telefono" :value="__('Teléfono')" />
                        <x-text-input id="EditForm.telefono" 
                                      class="block mt-2 w-full" 
                                      type="tel" 
                                      wire:model.blur="EditForm.telefono" 
                                      :value="old('EditForm.telefono')" 
                                      placeholder="" 
                                      pattern="[0-9]{1,12}" 
                                      inputmode="numeric" 
                                      required 
                                      maxlength="12" />
                        <x-input-error :messages="$errors->get('EditForm.telefono')" class="mt-2" />
                    </div>
                    
            
                    <div>
                        <x-input-label for="EditForm.cedula" :value="__('Cédula')" />
                        <x-text-input 
                            id="EditForm.cedula" 
                            class="block mt-2 w-full" 
                            type="text" 
                            wire:model.blur="EditForm.cedula" 
                            :value="old('EditForm.cedula')" 
                            placeholder="" 
                            maxlength="14" 
                            inputmode="numeric" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                            required 
                        />
                        <x-input-error :messages="$errors->get('EditForm.cedula')" class="mt-2" />
                    </div>
                    
                    
            
                    <div>
                        <x-input-label for="EditForm.correo" :value="__('Correo')" />
                        <x-text-input id="EditForm.correo" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.correo" :value="old('EditForm.correo')" placeholder="" />
                        <x-input-error :messages="$errors->get('EditForm.correo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="EditForm.rif" :value="__('Rif')" />
                        <x-text-input id="EditForm.rif" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.rif" :value="old('EditForm.rif')" placeholder="" maxlength="14" />
                        <x-input-error :messages="$errors->get('EditForm.rif')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="EditForm.razon_social" :value="__('Razon Social')" />
                        <x-text-input id="EditForm.razon_social" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.razon_social" :value="old('EditForm.razon_social')" placeholder="" maxlength="16"/>
                        <x-input-error :messages="$errors->get('EditForm.razon_social')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="EditForm.direccion_fiscal" :value="__('Direccion Fiscal')" />
                        <x-text-input id="EditForm.direccion_fiscal" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.direccion_fiscal" :value="old('EditForm.direccion_fiscal')" placeholder="" />
                        <x-input-error :messages="$errors->get('EditForm.direccion_fiscal')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="EditForm.retencion" :value="__('Retencion (opcional)')" />
                        <x-text-input id="EditForm.retencion" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.retencion" :value="old('createForm.retencion')" placeholder="" />
                        <x-input-error :messages="$errors->get('EditForm.retencion')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="EditForm.contribuyente_especial" :value="__('Contribuyente Especial (opcional)')" />
                        <x-text-input id="EditForm.contribuyente_especial" class="block mt-2 w-full" type="text" wire:model.blur="EditForm.contribuyente_especial" :value="old('EditForm.contribuyente_especial')" placeholder="" />
                        <x-input-error :messages="$errors->get('EditForm.contribuyente_especial')" class="mt-2" />
                    </div>
                </div>
            </x-slot>
    
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('EditForm.open', false)" type="button">
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
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
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