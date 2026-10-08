<div>
@section('titulo')
    Oficina
@endsection

<div class="max-w-9xl mx-auto sm:px-6 lg:px-8 mt-10">
    @can('Ver Oficinas-admin')
        <x-return-link :href="route('oficinas-mostrar')" wire:navigate.hover />
    @endcan
    <h1 class="text-3xl text-primary font-bold">Detalles de la Oficina</h1>
    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">

        @can('Crear oficinas')
        @if(in_array($oficina->tipo_oficina_id, [1, 2, 3])) <!-- Verifica si el tipo de oficina es 1, 2 o 3 -->
            <x-primary-button wire:click="edit">
                <Ri:a>nuevo jefe de oficina</Ri:a>
            </x-primary-button>
        @endif
    
        @endcan
    
        <div class="flex gap-2">
        @can('Crear vehiculos')
            <x-primary-button title="Registrar Vehículo" wire:click="create2">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" class="size-8" viewBox="0 0 48 48">
                    <path fill="white" d="M 16.347656 6 C 13.355656 6 10.730344 8.0616562 10.027344 10.972656 L 9.296875 14 L 7.5117188 14 C 6.9727188 13.992 6.4683125 14.277188 6.1953125 14.742188 C 5.9223125 15.211187 5.9223125 15.788812 6.1953125 16.257812 C 6.4683125 16.722812 6.9727187 17.008 7.5117188 17 L 8.5703125 17 L 8.1445312 18.777344 C 6.8945312 19.582344 6.0117188 20.918 6.0117188 22.5 L 6.0117188 39 C 6.0117188 40.105 6.9067188 41 8.0117188 41 L 10.011719 41 C 11.116719 41 12.011719 40.105 12.011719 39 L 12.011719 37 L 22.179688 37 C 22.078688 36.346 22.011719 35.682 22.011719 35 C 22.011719 34.662 22.035547 34.331 22.060547 34 L 9.0117188 34 L 9.0117188 22.5 C 9.0117188 21.652 9.6637187 21 10.511719 21 L 37.511719 21 C 38.359719 21 39.011719 21.652 39.011719 22.5 L 39.011719 22.634766 C 40.079719 22.979766 41.084719 23.463594 42.011719 24.058594 L 42.011719 22.5 C 42.011719 20.918 41.128813 19.582344 39.882812 18.777344 L 39.451172 17 L 40.511719 17 C 41.050719 17.008 41.553172 16.722813 41.826172 16.257812 C 42.099172 15.788813 42.099172 15.211188 41.826172 14.742188 C 41.553172 14.277188 41.050719 13.992 40.511719 14 L 38.726562 14 L 37.996094 10.972656 C 37.292094 8.0616563 34.667781 6 31.675781 6 L 16.347656 6 z M 16.347656 9 L 31.675781 9 C 33.300781 9 34.694172 10.101781 35.076172 11.675781 L 36.603516 18 L 11.417969 18 L 12.943359 11.675781 C 13.326359 10.101781 14.722656 9 16.347656 9 z M 14.011719 24 A 2 2 0 0 0 14.011719 28 A 2 2 0 0 0 14.011719 24 z M 35.011719 24 C 28.936719 24 24.011719 28.925 24.011719 35 C 24.011719 41.075 28.936719 46 35.011719 46 C 41.086719 46 46.011719 41.075 46.011719 35 C 46.011719 28.925 41.086719 24 35.011719 24 z M 35.011719 27 C 35.563719 27 36.011719 27.448 36.011719 28 L 36.011719 34 L 42.011719 34 C 42.563719 34 43.011719 34.448 43.011719 35 C 43.011719 35.552 42.563719 36 42.011719 36 L 36.011719 36 L 36.011719 42 C 36.011719 42.552 35.563719 43 35.011719 43 C 34.459719 43 34.011719 42.552 34.011719 42 L 34.011719 36 L 28.011719 36 C 27.459719 36 27.011719 35.552 27.011719 35 C 27.011719 34.448 27.459719 34 28.011719 34 L 34.011719 34 L 34.011719 28 C 34.011719 27.448 34.459719 27 35.011719 27 z M 20.511719 28 C 19.972719 27.992 19.468313 28.277188 19.195312 28.742188 C 18.922313 29.211188 18.922312 29.788813 19.195312 30.257812 C 19.468312 30.722812 19.972719 31.008 20.511719 31 L 22.644531 31 C 22.989531 29.932 23.473359 28.927 24.068359 28 L 20.511719 28 z"></path>
                </svg>
            </x-primary-button>
        @endcan
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <h2 class="text-2xl font-semibold">Información General
                @role('SuperAdmin|Gerente de Estado|Jefe de OPT')
                    {{-- <button wire:click="modificar({{$oficina->oficina_id}})" title="Modificar Informacion" class="text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button> --}}
                @endrole
            </h2>
            <p class="mt-2">Nombre: @if($editMode)
                <input type="text" wire:model="nombre" class="border-gray-300 rounded w-full">
            @else
                <strong>{{ $oficina->nombre }}</strong>
            @endif
        </p>
            <p>Código: <strong>{{ $oficina->codigo }}</strong></p>
            <p>Tipo de oficina:
                <strong>
                    @switch($oficina->tipo_oficina_id)
                        @case(1) OPT (Pequeña) @break
                        @case(2) OPT (Mediana) @break
                        @case(3) OPT (Grande) @break
                        @case(4) COP @break
                        @case(5) CENTRALIZADORA @break
                        @case(6) CPI @break
                        @default Desconocido
                    @endswitch
                </strong>
            </p>
            @php
            $jefe_id = $oficina->jefe_oficina;
            $jefe = null;
        
            if (is_numeric($jefe_id) && $jefe_id !== null) {
                $jefe = \App\Models\User::find($jefe_id);
            }
        @endphp
        
        @if (in_array($oficina->tipo_oficina_id, [1, 2, 3]))
            <p>Jefe de Oficina:
                <strong>
                    {{ $jefe ? $jefe->name : 'No tiene jefe' }}
                </strong>
            </p>
        @endif

<p>Correo:
    @if($editMode)
        <input type="email" wire:model="correo" class="border-gray-300 rounded w-full">
    @else
        <strong>{{ $oficina->correo }}</strong>
    @endif
</p>
<p>Teléfono:
    @if($editMode)
        <input type="text" wire:model="telefono" class="border-gray-300 rounded w-full">
    @else
        <strong>{{ $oficina->telefono }}</strong>
    @endif
</p>
            <p>Código de Ubicación: <strong>{{ $oficina->codigo_ubicacion }}</strong></p>
        </div>

        <div class="space-y-4">
            <h2 class="text-2xl font-semibold">Ubicación y Estatus</h2>
            <p>Estado: <strong>{{ $estadoNombre }}</strong></p>
            <p>Municipio: <strong>{{ $municipioNombre }}</strong></p>
            <p>Parroquia: <strong>{{ $parroquiaNombre }}</strong></p>
            <p>Dirección: <strong>{{ $oficina->direccion }}</strong></p>
            <p>Zona Económica Especial: <strong>{{ $oficina->zona_economica_especial ? 'Sí' : 'No' }}</strong></p>
            <p>Longitud: <strong>{{ $oficina->longitud }}</strong></p>
            <p>Latitud: <strong>{{ $oficina->latitud }}</strong></p>
            <p>Estatus:
                <strong class="
                    {{ $oficina->estatus_id == 1 ? 'text-green-500' : '' }}
                    {{ $oficina->estatus_id == 2 ? 'text-orange-500' : '' }}
                    {{ $oficina->estatus_id == 3 ? 'text-red-800' : '' }}
                ">
                    {{ $oficina->estatus_id == 1 ? 'ACTIVA' : ($oficina->estatus_id == 2 ? 'INACTIVA' : ($oficina->estatus_id == 3 ? 'CERRADA' : '')) }}
                </strong>
            </p>
            <p>Fecha de Creación: <strong>{{ \Carbon\Carbon::parse($oficina->created_at)->format('d/m/Y') }}</strong></p>
        </div>

        @if($editMode)
            <div class="flex gap-2 mt-2">
                <button wire:click="save" class="bg-green-800 text-white px-4 py-2 rounded">Guardar</button>
                <button wire:click="toggleEditMode" class="bg-primary text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        @endif
    </div>

    @if(!$oficina)
        <p class="text-red-500 mt-4">No se encontró la oficina.</p>
    @endif
    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
        <div class="flex flex-full md:flex-row w-full">
                <x-tab-link :href="route('integrantes', ['oficina_id' => $oficina_id])" :active="request()->routeIs('integrantes')" wire:navigate.hover>
                    {{ __('Integrantes de Oficina') }}
                </x-tab-link>
    
                <x-tab-link :href="route('ver-vehiculos', ['oficina_id' => $oficina_id])" :active="request()->routeIs('ver-vehiculos')" wire:navigate.hover>
                    {{ __('Vehículos de Oficina ') }}
                </x-tab-link>
    
                <x-tab-link :href="route('ver-roles-oficinas', ['oficina_id' => $oficina_id])" :active="request()->routeIs('ver-roles-oficinas')" wire:navigate.hover>
                    {{ __('Roles') }}
                </x-tab-link>
        </div>
        <div class="overflow-x-auto">
            <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-4 mt-10 ml-4">Vehículos </h1>

            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">vehículos </th>
                        <th class="px-4 py-3 text-left">Marca</th>
                        <th class="px-4 py-3 text-left">Modelo</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Placa</th>
                        <th class="px-4 py-3 text-left">Color</th>
                        <th class="px-4 py-3 text-left">Año</th>
                        <th class="px-4 py-3 text-left">Chofer</th>
                        <th class="px-4 py-3 text-left">Kilometraje act</th>
                        <th class="px-4 py-3 text-left">Estatus</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                        <th class="px-4 py-3 text-left"></th>
                        <th class="px-4 py-3 text-left"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $vehiculo)
                    <tr wire:key="{{ $vehiculo->vehiculo_id }}" class="border-b text-left">
                        <td class="px-4 py-3 text-center">
                            @if($vehiculo->imagen)
                                <img src="{{ Storage::url($vehiculo->imagen) }}" alt="Imagen del vehículo" class="w-32 h-28 object-cover rounded-md">
                            @else
                                <span class="text-gray-500">Sin imagen</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->marca }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->modelo }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->TipoVehiculo->tipo }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->placa }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->color }}</td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->año }}</td>
                        <td class="px-4 py-3 font-medium text-black">
                            {{ optional($vehiculo->user)->name ?? 'Ninguno' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-black">{{ $vehiculo->kilometraje_actual }}</td>
                        <td class="px-4 py-3 font-medium {{ $vehiculo['Activo'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $vehiculo['Activo'] ? 'Activo' : 'Inactivo' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-black text-center">    
                            <button  wire:click="edit({{ $vehiculo->vehiculo_id }})" title="Editar" wire:navigate.hover>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                            </button>

                             <button wire:click="edit2({{ $vehiculo->vehiculo_id }})" title="Asignar vehiculo">
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
                                <button wire:click="activar({{ $vehiculo->vehiculo_id}})" title="Activar" class=" text-white p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>                                      
                                </button>
                               
                            @endif
                            <!-- Botón para registrar carga de combustible -->
                            <button wire:click="openCombustibleModal({{ $vehiculo->vehiculo_id }})" title="Registrar Carga de Combustible" class="ml-1 p-2 rounded focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-primary"><path d="M3 19V4C3 3.44772 3.44772 3 4 3H13C13.5523 3 14 3.44772 14 4V12H16C17.1046 12 18 12.8954 18 14V18C18 18.5523 18.4477 19 19 19C19.5523 19 20 18.5523 20 18V11H18C17.4477 11 17 10.5523 17 10V6.41421L15.3431 4.75736L16.7574 3.34315L21.7071 8.29289C21.9024 8.48816 22 8.74408 22 9V18C22 19.6569 20.6569 21 19 21C17.3431 21 16 19.6569 16 18V14H14V19H15V21H2V19H3ZM5 5V11H12V5H5Z"></path></svg>
                            </button>
                        </td>
                        <td>
                            <button wire:click="create3({{ $vehiculo->vehiculo_id}})" title="Crear Mantenimiento" class=" text-white p-2 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-purple-800">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                  </svg>
                            </button>  
                        </td>    
                    </tr>
                    @empty
                        <tr class="px-4 py-3 font-medium text-black text-left">
                            <td colspan="8" class="py-7 text-default text-2xl">No hay Vehículos </td>
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
                </div>
            </div>
        </div>
            
        <div class="overflow-x-auto mt-4">
        </div>


        <form wire:submit.prevent="store3">
            <x-dialog-modal wire:model="CreateForm3.open">
                <x-slot name="title">
                    Registrar Mantenimiento
                </x-slot>
        
                <x-slot name="content">
                    <!-- Campos fijos: Kilometraje y Costo Total -->
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="CreateForm3.kilometraje" :value="__('Kilometraje')" />
                            <x-text-input id="CreateForm3.kilometraje" type="number" min="0" wire:model="CreateForm3.kilometraje" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" required />
                            <x-input-error :messages="$errors->get('CreateForm3.kilometraje')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm3.costo_total" :value="__('Costo Total del Mantenimiento')" />
                            <x-text-input id="CreateForm3.costo_total" type="number" min="0" step="0.01" wire:model="CreateForm3.costo_total" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" required />
                            <x-input-error :messages="$errors->get('CreateForm3.costo_total')" class="mt-2" />
                        </div>
                    </div>
                    @foreach ($servicios as $index => $servicio)
                        <div class="mb-4 grid grid-cols-2 gap-4 border-b pb-4 relative">
                            <!-- Select de servicios -->
                            <div>
                                <x-input-label :for="'servicio_id_' . $index" :value="__('Servicio')" />
                                <select id="{{ 'CreateForm3.servicio_id' . $index }}"  wire:model="CreateForm3.servicios.{{ $index }}.servicio_id" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="" disabled selected>Seleccione un servicio</option>
                                    @foreach($serviciosActivos as $servicio)
                                        <option value="{{ $servicio->servicios_flota_id }}">{{ $servicio->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('servicios.' . $index . '.servicio_id')" class="mt-2" />
                            </div>
        
                            <!-- Campo de fecha -->
                            <div>
                                <x-input-label :for="'fecha_' . $index" :value="__('Fecha de Mantenimiento')" />
                                <x-text-input id="{{ 'CreateForm3.fecha_' . $index }}" type="date" wire:model="CreateForm3.servicios.{{ $index }}.fecha" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" />
                                <x-input-error :messages="$errors->get('servicios.' . $index . '.fecha')" class="mt-2" />
                            </div>
        
                            <!-- Botón para eliminar la entrada -->
                            @if($index > 0)
                                <button type="button" class="absolute top-0 right-0 text-red-500" wire:click="removeServicio({{ $index }})">
                                    ✕
                                </button>
                            @endif
                        </div>
                    @endforeach
        
                    <!-- Botón para agregar más servicios -->
                    <x-primary-button type="button" class="mt-2" wire:click="addServicio">
                        Agregar más servicios +
                    </x-primary-button>
        
                    <!-- Campo de descripción -->
                    <div class="mt-4">
                        <x-input-label for="descripcion" :value="__('Descripción')" />
                        <textarea id="CreateForm3.descripcion" wire:model="CreateForm3.descripcion" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" rows="4" placeholder="Escribe una descripción..."></textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>
                </x-slot>
        
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('CreateForm3.open', false)" type="button">
                            Cancelar
                        </x-danger-button>
        
                        <x-primary-button wire:loading.attr="disabled" wire:target="store3">
                            Crear
                            <x-loading-button wire:target="store3"/>
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>
        
        
        
        


        <form wire:submit="update">
            <x-dialog-modal wire:model="editForm.open">
                <x-slot name="title">
                   Editar Vehículo
                </x-slot>
    
                <x-slot name="content">
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="editForm.placa" :value="__('Placa')" />
                            <x-text-input id="editForm.placa" class="block mt-2 w-full" type="text" wire:model.blur="editForm.placa" :value="old('editForm.placa')" placeholder="" required  maxlength="10"/>
                            <x-input-error :messages="$errors->get('editForm.placa')" class="mt-2" />
                        </div>
                
                        <div>
                            <x-input-label for="editForm.color" :value="__('Color')" />
                            <x-text-input id="editForm.color" class="block mt-2 w-full" type="tel" wire:model.blur="editForm.color" :value="old('editForm.color')" placeholder="" required  maxlength="10"/>
                            <x-input-error :messages="$errors->get('editForm.color')" class="mt-2" />
                        </div>

                        <div>
                        <x-input-label for="editForm.año" :value="__('Año')" />
                        <select 
                            id="editForm.año" 
                            class="block mt-2 w-full" 
                            wire:model.blur="editForm.año" 
                            required
                        >
                            <option value="" selected disabled>Selecciona un año</option>
                            @foreach(range(date('Y'), 1960) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('editForm.año')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="editForm.fecha_vecimiento" :value="__('Fecha de Vencimiento De la Poliza (opcional)')" />
                            <x-text-input id="editForm.fecha_vecimiento" class="block mt-2 w-full" type="date" wire:model="editForm.fecha_vecimiento" :value="old('editForm.fecha_vecimiento')" placeholder="" />
                            <x-input-error :messages="$errors->get('editForm.fecha_vecimiento')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.poliza" :value="__('Numero de Poliza (opcional)')" />
                            <x-text-input id="editForm.poliza" class="block mt-2 w-full" type="text" wire:model.blur="editForm.poliza" :value="old('editForm.poliza')" placeholder="" maxlength="20"  type="text" />
                            <x-input-error :messages="$errors->get('editForm.poliza')" class="mt-2" />
                        </div>


                        <div class="mb-4">
                            <x-input-label for="editForm.tipo" :value="__('Tipo de Vehiculo')" />
                            <select id="editForm.tipo" class="block mt-2 w-full" wire:model.blur="editForm.tipo" required>
                                <option value="" selected disabled>Selecciona un año</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->tipo_vehiculo_id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('editForm.tipo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="editForm.capacidad" :value="__('Capacidad en Kg')" />
                            <x-text-input id="editForm.capacidad" class="block mt-2 w-full" type="text" wire:model.blur="editForm.capacidad" :value="old('editForm.capacidad')" placeholder=""  oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="12"   type="text" />
                            <x-input-error :messages="$errors->get('editForm.capacidad')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.marca" :value="__('Marca')" />
                            <x-text-input id="editForm.marca" class="block mt-2 w-full" type="text" wire:model.blur="editForm.marca" :value="old('editForm.marca')" placeholder="" maxlength="12"/>
                            <x-input-error :messages="$errors->get('editForm.marca')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.modelo" :value="__('modelo')" />
                            <x-text-input id="editForm.modelo" class="block mt-2 w-full" type="text" wire:model.blur="editForm.modelo" :value="old('editForm.modelo')" placeholder="" maxlength="20"   type="text"/>
                            <x-input-error :messages="$errors->get('editForm.modelo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.kilometraje_actual" :value="__('Kilometraje Actual')" />
                            <x-text-input id="editForm.kilometraje_actual" class="block mt-2 w-full" type="number" min="0" wire:model.blur="editForm.kilometraje_actual" :value="old('editForm.kilometraje_actual')" placeholder="Ej: 120000" required />
                            <x-input-error :messages="$errors->get('editForm.kilometraje_actual')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="editForm.imagen" :value="__('Imagen actual')" />
                            @if($editForm->vehiculo_id)
                                @php
                                    $vehiculo = \App\Models\Vehiculo::find($editForm->vehiculo_id);
                                @endphp
                                @if($vehiculo && $vehiculo->imagen)
                                    <img src="{{ Storage::url($vehiculo->imagen) }}" alt="Imagen actual" class="w-32 h-24 object-cover rounded mb-2">
                                @else
                                    <span class="text-gray-400">Sin imagen</span>
                                @endif
                            @endif
                            <input id="editForm.imagen" type="file" wire:model="editForm.imagen" accept="image/*" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('editForm.imagen')" class="mt-2" />
                            @if($editForm->imagen)
                                <div class="mt-2">
                                    <span class="text-xs text-gray-500">Previsualización nueva imagen:</span>
                                    <img src="{{ $editForm->imagen->temporaryUrl() }}" class="w-32 h-24 object-cover rounded border mt-1">
                                </div>
                            @endif
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
        
        <form wire:submit.prevent="store">
            <x-dialog-modal wire:model="CreateForm.open">
                <x-slot name="title">
                    Crear Usuario
                </x-slot>
                
                <x-slot name="content">
                    @if (session()->has('error'))
                        <div class="bg-red-500 text-white p-4 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if (session()->has('alert'))
                        <div class="bg-yellow-500 text-white p-4 rounded mb-4">
                            {{ session('alert') }}
                        </div>
                    @endif
                    
                    @if (session()->has('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.name" :value="__('Nombre')" />
                        <x-text-input id="CreateForm.name" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.name" :value="old('CreateForm.name')" placeholder="Nombre" required/>
                        <x-input-error :messages="$errors->get('CreateForm.name')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.correo" :value="__('Correo')" />
                        <x-text-input id="CreateForm.correo" class="block mt-1 w-full" type="text" style="text-transform: lowercase;" wire:model.blur="CreateForm.correo" :value="old('CreateForm.correo')" placeholder="Correo" required/>
                        <x-input-error :messages="$errors->get('CreateForm.correo')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4 text-center">                       
                        <x-input-label for="CreateForm.tipoCedula" :value="__('tipo')" />
                        <div class="flex items-center justify-center space-x-2">
                            <select name="tipoCedula" id="CreateForm.tipoCedula" wire:model.blur="CreateForm.tipoCedula" class="w-20 p-2 border border-gray-300 rounded-md">
                                <option value=""></option>
                                <option value="V-">V-</option>
                                <option value="E-">E-</option>
                            </select>
                            <x-input type="text" class="w-60" wire:model="CreateForm.cedula" required pattern="[0-9]*" inputmode="numeric" maxlength="8" placeholder="Ingrese la cédula" />
                        </div>
                
                    </div>

                    <div class="mb-4">
                        <x-input-label for="CreateForm.telefono" :value="__('Teléfono')" />
                        <x-text-input id="CreateForm.telefono" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.telefono" placeholder="Ej: 04121234567" maxlength="15" />
                        <x-input-error :messages="$errors->get('CreateForm.telefono')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="CreateForm.clave" :value="__('Clave')" />
                        <div class="relative">
                            <x-text-input 
                                id="CreateForm.clave" 
                                class="block mt-1 w-full pr-10" 
                                type="password" 
                                wire:model.blur="CreateForm.clave" 
                                :value="old('CreateForm.clave')" 
                                placeholder="Clave"
                                required
                            />
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-3">
                                <!-- Ícono de ojo (puedes usar SVG o un ícono de librería como FontAwesome) -->
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.012.03-.024.06-.036.09m-18.97 0c1.274 4.057 5.064 7 9.542 7s8.268-2.943 9.542-7" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('CreateForm.clave')" class="mt-2" />
                    </div>
                    
                    <script>
                        function togglePassword() {
                            const passwordInput = document.getElementById('CreateForm.clave');
                            const eyeIcon = document.getElementById('eyeIcon');
                            
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.012.03-.024.06-.036.09m-18.97 0c1.274 4.057 5.064 7 9.542 7s8.268-2.943 9.542-7" />`;
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7" />`;
                            }
                        }
                    </script>
                    
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.role_id" :value="__('Selecciona un rol')" />
                        
                
                        
                        <select name="roles" id="CreateForm.role_id" wire:model.blur="CreateForm.role_id" wire:change="validarRolSeleccionado" class="w-full text-center border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm">
                            <option value="">---SELECCIONE---</option>
                            @foreach($rolescantidad as $rol)
                                @php
                                    $cantidadActual = $rolesCount[$rol->rol_id] ?? 0;
                                    $estaLleno = $cantidadActual >= $rol->cantidad_max;
                                @endphp
                                <option value="{{ $rol->rol_id }}" {{ $estaLleno ? 'disabled' : '' }}>
                                    {{ $rolesNombres[$rol->rol_id] ?? $rol->rol_id }}    
                                    ({{ $cantidadActual }}/{{ $rol->cantidad_max }})
                                    @if($estaLleno)
                                        - LÍMITE ALCANZADO
                                    @endif
                                </option>
                            @endforeach   
                        </select>
                        
                        @if(session()->has('error'))
                            <div class="mt-2 text-sm text-red-600">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <x-input-error :messages="$errors->get('CreateForm.role_id')" class="mt-2" />
                    </div>
                </x-slot>
        
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('CreateForm.open', false)" type="button">
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

        <form wire:submit="store2">
            <x-dialog-modal wire:model="CreateForm2.open">
                <x-slot name="title">
                    Registrar Vehículo
                </x-slot>
    
                <x-slot name="content">
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="CreateForm2.placa" :value="__('Placa')" />
                            <x-text-input id="CreateForm2.placa" class="block mt-2 w-full" type="text" wire:model.blur="CreateForm2.placa" :value="old('CreateForm2.placa')" placeholder="" required  maxlength="10"/>
                            <x-input-error :messages="$errors->get('CreateForm2.placa')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.color" :value="__('Color')" />
                            <x-text-input id="CreateForm2.color" class="block mt-2 w-full" type="tel" wire:model.blur="CreateForm2.color" :value="old('CreateForm2.color')" placeholder=""  required  maxlength="10"/>
                            <x-input-error :messages="$errors->get('CreateForm2.color')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.año" :value="__('Año')" />
                            <select 
                                id="CreateForm2.año" 
                                class="block mt-2 w-full" 
                                wire:model.blur="CreateForm2.año" 
                                required
                            >
                                <option value="" selected disabled>Selecciona un año</option>
                                @foreach(range(date('Y'), 1960) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('CreateForm2.año')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.fecha_vecimiento" :value="__('Fecha de Vencimiento De la Poliza (opcional)')" />
                            <x-text-input id="CreateForm2.fecha_vecimiento" class="block mt-2 w-full" type="date" wire:model="CreateForm2.fecha_vecimiento" :value="old('CreateForm2.fecha_vecimiento')" placeholder="" />
                            <x-input-error :messages="$errors->get('CreateForm2.fecha_vecimiento')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.poliza" :value="__('Numero de Poliza (opcional)')" />
                            <x-text-input id="CreateForm2.poliza" class="block mt-2 w-full" type="text" wire:model.blur="CreateForm2.poliza" :value="old('CreateForm2.poliza')" placeholder=""  maxlength="20"  type="text"/>
                            <x-input-error :messages="$errors->get('CreateForm2.poliza')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="CreateForm2.tipo" :value="__('Tipo de Vehiculo')" />
                            <select id="CreateForm2.tipo" class="block mt-2 w-full" wire:model.blur="CreateForm2.tipo" required>
                                <option value="" selected disabled>Selecciona un año</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->tipo_vehiculo_id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('CreateForm2.tipo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="CreateForm2.capacidad" :value="__('Capacidad en Kg')" />
                            <x-text-input id="CreateForm2.capacidad" class="block mt-2 w-full" type="text" wire:model.blur="CreateForm2.capacidad" :value="old('CreateForm2.capacidad')" placeholder=""  oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="12"   type="text" />
                            <x-input-error :messages="$errors->get('CreateForm2.capacidad')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.marca" :value="__('Marca')" />
                            <x-text-input id="CreateForm2.marca" class="block mt-2 w-full" type="text" wire:model.blur="CreateForm2.marca" :value="old('CreateForm2.marca')" placeholder="" maxlength="12" />
                            <x-input-error :messages="$errors->get('CreateForm2.marca')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.modelo" :value="__('modelo')" />
                            <x-text-input id="CreateForm2.modelo" class="block mt-2 w-full" type="text" wire:model.blur="CreateForm2.modelo" :value="old('CreateForm2.modelo')" placeholder="" maxlength="20"   type="text"/>
                            <x-input-error :messages="$errors->get('CreateForm2.modelo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.kilometraje_actual" :value="__('Kilometraje Actual')" />
                            <x-text-input id="CreateForm2.kilometraje_actual" class="block mt-2 w-full" type="number" min="0" wire:model.blur="CreateForm2.kilometraje_actual" :value="old('CreateForm2.kilometraje_actual')" placeholder="Ej: 120000" required />
                            <x-input-error :messages="$errors->get('CreateForm2.kilometraje_actual')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="CreateForm2.imagen" :value="__('Agregar imagen (opcional)')" />
                            <input 
                                id="CreateForm2.imagen" 
                                class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                type="file" 
                                wire:model="CreateForm2.imagen" 
                                accept="image/*"
                            />
                            <x-input-error :messages="$errors->get('CreateForm2.imagen')" class="mt-2" />
                        </div>
                    </div>
                </x-slot>
    
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('CreateForm2.open', false)" type="button">
                            Cancelar
                        </x-danger-button>
    
                        <x-primary-button wire:loading.attr="disabled" wire:target="store2">
                            Crear
    
                            <x-loading-button wire:target="store2"/>
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form> 

        <form wire:submit.prevent="update2">
            <x-dialog-modal wire:model="editForm3.open" class="bg-gray-100 rounded-lg shadow-lg p-6">
                <x-slot name="title">
                    <div class="text-center text-2xl font-semibold text-gray-800">Asignar Chofer</div>
                </x-slot>
        
                <x-slot name="content">
                    <div class="flex justify-center mt-4">
                        <div class="w-3/4">
                            <x-input-label for="editForm3.user_id" :value="__('Seleccione un Chofer')" class="text-lg text-gray-700" />
                            <select id="editForm3.user_id" class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" wire:model="editForm3.user_id">
                                <option value="">{{ __('Seleccione un Chofer') }}</option>
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}">{{ $chofer->name }} - {{ $chofer->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('editForm3.user_id')" class="mt-2 text-red-500" />
                        </div>
                    </div>
                </x-slot>
        
                <x-slot name="footer">
                    <div class="flex justify-end space-x-3">
                        <x-danger-button class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition ease-in-out duration-200" wire:click="$set('editForm3.open', false)" type="button">
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
</div>

{{-- Modal de carga de combustible --}}
<x-dialog-modal wire:model.defer="combustibleModal.open">
    <x-slot name="title">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-primary"><path d="M3 19V4C3 3.44772 3.44772 3 4 3H13C13.5523 3 14 3.44772 14 4V12H16C17.1046 12 18 12.8954 18 14V18C18 18.5523 18.4477 19 19 19C19.5523 19 20 18.5523 20 18V11H18C17.4477 11 17 10.5523 17 10V6.41421L15.3431 4.75736L16.7574 3.34315L21.7071 8.29289C21.9024 8.48816 22 8.74408 22 9V18C22 19.6569 20.6569 21 19 21C17.3431 21 16 19.6569 16 18V14H14V19H15V21H2V19H3ZM5 5V11H12V5H5Z"></path></svg>
            <span class="text-primary font-bold text-lg">Registrar Carga de Combustible</span>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="bg-white p-2 rounded">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="combustibleForm.fecha" :value="__('Fecha de carga')" class="text-primary font-semibold" />
                    <x-text-input id="combustibleForm.fecha" type="date" class="block w-full mt-1 rounded-md border-gray-300 focus:ring-primary focus:border-primary" wire:model.defer="combustibleForm.fecha" required />
                    <x-input-error :messages="$errors->get('combustibleForm.fecha')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.litros" :value="__('Litros (L)')" class="text-primary font-semibold" />
                    <x-text-input id="combustibleForm.litros" type="number" step="0.01" min="0" class="block w-full mt-1 rounded-md border-gray-300 focus:ring-primary focus:border-primary" wire:model.defer="combustibleForm.litros" placeholder="Ej: 40.5" required />
                    <x-input-error :messages="$errors->get('combustibleForm.litros')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.costo_total" :value="__('Costo Total (Bs)')" class="text-primary font-semibold" />
                    <x-text-input id="combustibleForm.costo_total" type="number" step="0.01" min="0" class="block w-full mt-1 rounded-md border-gray-300 focus:ring-primary focus:border-primary" wire:model.defer="combustibleForm.costo_total" placeholder="Ej: 1200.00" required />
                    <x-input-error :messages="$errors->get('combustibleForm.costo_total')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.kilometraje" :value="__('Kilometraje (Km)')" class="text-primary font-semibold" />
                    <x-text-input id="combustibleForm.kilometraje" type="number" min="0" class="block w-full mt-1 rounded-md border-gray-300 focus:ring-primary focus:border-primary" wire:model.defer="combustibleForm.kilometraje" placeholder="Ej: 120000" required />
                    <x-input-error :messages="$errors->get('combustibleForm.kilometraje')" class="mt-2" />
                </div>
            </div>
        </div>
    </x-slot>

    @if($mod)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-1/2">
                <button wire:click="$set('mod', false)" class="float-right text-red-500">✖</button>

                @livewire('modificar-informacion-oficina.modificar-informacion-oficina', ['id' => $this->modificar_oficina_id])
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex justify-end gap-2">
            <x-secondary-button wire:click="$set('combustibleModal.open', false)">
                Cancelar
            </x-secondary-button>
            <x-primary-button wire:click="saveCombustible" class="ml-2">
                Guardar
            </x-primary-button>
        </div>
    </x-slot>
</x-dialog-modal>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('mostrarAlerta', oficina_id => {
            Swal.fire({
                title: "Eliminar Oficina?",
                text: `Una oficina eliminada no se puede recuperar oficina_id: ${oficina_id}!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminar', { oficina: oficina_id });
                }
            });
        });

        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('tarifaUpdated', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });


    </script>
@endpush
