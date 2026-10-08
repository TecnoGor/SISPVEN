<div>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-4">Rutas</h1>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs text-default uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-center">Ruta</th>
                    <th class="px-4 py-3 text-center">Distancia km</th>
                    <th class="px-4 py-3 text-center">Origen</th>
                    <th class="px-4 py-3 text-center">Destino</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rutas as $ruta)
                <tr wire:key="{{ $ruta->ruta_id }}" class="border-b text-center">
                    <td class="px-4 py-3 font-medium text-black">{{ $ruta->ruta }}</td>
                    <td class="px-4 py-3 font-medium text-black">{{ $ruta->distancia }}</td>
                    <td class="px-4 py-3 font-medium text-black text-center">{{ $ruta->oficinaOrigen->nombre }}</td>
                    <td class="px-4 py-3 font-medium text-black">{{ $ruta->oficinaDestino->nombre}}</td>
                    <td class="px-4 py-3 font-medium text-black text-center">    
                            <button wire:click="desactivar({{$ruta->ruta_id }})" title="Quitar" class=" text-white p-2 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </button>                     
                    </td>
                </tr>
                @empty
                    <tr class="px-4 py-3 font-medium text-black text-left">
                        <td colspan="8" class="py-7 text-default text-2xl">No hay Rutas Asignadas</td>
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
                        Asignar una Ruta
                    </x-primary-button>
                </div>
            </div>
            
        </div>
        
    </div>
    <form wire:submit="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                Asignar Ruta
            </x-slot>

            <x-slot name="content">
                <div>
                    <x-input-label for="createForm.ruta_id" :value="__('Selecciona una Ruta')" />
                    <select id="createForm.ruta_id" class="block mt-2 w-full" wire:model="createForm.ruta_id">
                        <option value=""  selected>-- Selecciona una ruta --</option>
                        @foreach($allrutas as $allruta)
                            <option value="{{ $allruta->ruta_id }}">{{ $allruta->ruta }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('createForm.ruta_id')" class="mt-2" />
                    
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
</div>
