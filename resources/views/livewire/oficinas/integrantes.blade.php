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
            <x-primary-button wire:click="edit2">
                <Ri:a>nuevo jefe de oficina</Ri:a>
            </x-primary-button>
        @endif
    
        @endcan
        <div class="flex gap-2">  
            @can('Crear Integrantes de Oficina')
                <x-primary-button wire:click="create">
                    <Ri:a>Crear Integrantes</Ri:a>
                </x-primary-button>
            @endcan
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
            <p class="mt-2">Nombre:@if($editMode)
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
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr class="">
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Nombre'])
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Email'])
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Cedula'])
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Teléfono'])
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Rol'])
                        @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Estatus'])
                        @include('livewire.includes.sort-table', ['column' => 'name', 'displayName' => 'Acciones'])
                    </tr>
                </thead>
                <tbody>
                    @forelse ($oficinausuarios as $oficinausuario)
                    <tr wire:key="{{ $oficinausuario->id }}" class="border-b text-left">
                        <th class="px-4 py-3 font-medium text-black">{{ $oficinausuario->name }}</th>
                        <th class="px-4 py-3 font-medium text-black">{{ $oficinausuario->email }}</th>
                        <th class="px-4 py-3 font-medium text-black">{{ $oficinausuario->cedula }}</th>
                        <th class="px-4 py-3 font-medium text-black">{{ $oficinausuario->telefono ?? 'N/A' }}</th>
                        <th class="px-4 py-3 font-medium text-black">{{ $oficinausuario->getRoleNames()->first() ?? 'Sin rol asignado' }} </th>
                        <th class="px-4 py-3 font-medium {{ $oficinausuario['activo'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $oficinausuario['activo'] ? 'Activo' : 'Inactivo' }}
                        </th>
                        <th class="px-4 py-3 font-medium text-black">
                            <button wire:click="edit({{ $oficinausuario->id }})" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-violet-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                  </svg>
                   
                            </button>
                            @if( $oficinausuario->getRoleNames()->first() !== 'SuperAdmin') 
                                @if ($oficinausuario['activo'])
                                <!-- Botón para desactivar -->
                                    <button wire:click="desactivar({{ $oficinausuario->id }})" title="Desactivar" class=" text-white p-2 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </button>
                                @else
                                    <!-- Botón para activar -->
                                    <button wire:click="activar({{  $oficinausuario->id }})" title="Activar" class=" text-white p-2 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>                                      
                                    </button>
                                
                                @endif
                            @endif
                    </th>  
          
                    </tr>
            @empty
                    <tr class="border-b text-center">
                        <th colspan="6" class="py-7 text-default text-2xl">No hay Integrantes</th>
                    </tr>
            @endforelse
                </tbody>

            </table>
        </div>
            
        {{-- Usuarios sin rol asignado --}}
        @if($usuariosSinRol->isNotEmpty())
            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-amber-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-sm font-bold text-amber-800">Usuarios sin rol asignado ({{ $usuariosSinRol->count() }})</h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($usuariosSinRol as $sinRol)
                        <div class="flex items-center justify-between bg-white rounded-lg border border-amber-200 p-3" wire:key="sin-rol-{{ $sinRol->id }}">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $sinRol->name }}</p>
                                <p class="text-xs text-gray-500">{{ $sinRol->cedula }} — {{ $sinRol->email }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select wire:model="rol_asignar.{{ $sinRol->id }}"
                                    class="border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm p-2 bg-white">
                                    <option value="">Seleccionar rol...</option>
                                    @foreach($rolescantidad as $rc)
                                        @php
                                            $rolNombre = $rolesNombres[$rc->rol_id] ?? 'Rol ' . $rc->rol_id;
                                            $actual = $rolesCount[$rc->rol_id] ?? 0;
                                            $disponible = $actual < $rc->cantidad_max;
                                        @endphp
                                        <option value="{{ $rc->rol_id }}" {{ !$disponible ? 'disabled' : '' }}>
                                            {{ $rolNombre }} ({{ $actual }}/{{ $rc->cantidad_max }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="asignarRol({{ $sinRol->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-2 bg-primary hover:bg-red-800 text-white font-bold rounded-lg text-xs uppercase tracking-widest transition active:scale-95">
                                    Asignar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="overflow-x-auto mt-4">
        </div>
        <form wire:submit="update">
            <x-dialog-modal wire:model.blur="EditForm2.open">
                <x-slot name="title">
                    Asignar Oficina
                </x-slot>
        
                <x-slot name="content">
                    <div class="mt-5">
                        <div class="mt-4">
                            <x-input-label for="EditForm2.jefe" :value="__('Seleccione su Jefe de Oficina')" />
                            <select wire:model.blur="EditForm2.jefe" id="EditForm2.jefe" class="w-full text-center border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm" autocomplete="Jefe de Oficina">
                                <option selected>-- Seleccionar --</option>
                                    @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                    @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('EditForm2.estado')" class="mt-2" />
                        </div>
                    </div>
                </x-slot>
        
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('EditForm2.open', false)" type="button">
                            Cancelar
                        </x-danger-button>
        
                        <x-primary-button wire:loading.attr="disabled" wire:target="update2">
                            Guardar
                            <x-loading-button wire:target="update2"/>
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        </form>
        
        <x-modal-crear-usuario
            :empleadosDisponibles="$empleadosDisponibles"
            :rolescantidad="$rolescantidad"
            :rolesCount="$rolesCount"
            :rolesNombres="$rolesNombres" />

        <form wire:submit="update">
            <x-dialog-modal wire:model.blur="editForm.open">
                <x-slot name="title">
                    Actualizar Usuario
                </x-slot>
    
                <x-slot name="content">
                    <div class="mt-5">
                        <x-input-label for="editForm.name" :value="__('Nombre')" />
                        <x-text-input id="editForm.name" class="block mt-1 w-full"  wire:model.blur="editForm.name" :value="old('editForm.name')" autocomplete="name" />
                        <x-input-error :messages="$errors->get('editForm.name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="editForm.email" :value="__('Email')" />
                        <x-text-input id="editForm.email" class="block mt-1 w-full" type="email" wire:model.blur="editForm.email" :value="old('editForm.email')" autocomplete="email" />
                        <x-input-error :messages="$errors->get('editForm.email')" class="mt-2" />
                    </div>

                    
                    <div class="mt-4">
                        <x-input-label for="editForm.cedula" :value="__('Cédula')" />
                        <x-text-input 
                            id="editForm.cedula" 
                            class="block mt-1 w-full" 
                            wire:model.blur="editForm.cedula" 
                            maxlength="10" 
                            placeholder="Ingrese la cédula" 
                        />
                        <x-input-error :messages="$errors->get('editForm.cedula')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="editForm.telefono" :value="__('Teléfono')" />
                        <x-text-input id="editForm.telefono" class="block mt-1 w-full" type="text" wire:model.blur="editForm.telefono" placeholder="Ej: 04121234567" maxlength="15" />
                        <x-input-error :messages="$errors->get('editForm.telefono')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="editForm.password" :value="__('Nueva clave')" />
                        
                        <div class="relative">
                            <!-- Campo de contraseña -->
                            <x-text-input id="editForm.password" 
                                          class="block mt-1 w-full pr-10" 
                                          type="password" 
                                          wire:model.blur="editForm.password" 
                                          :value="old('editForm.password')" 
                                          autocomplete="new-password" />
                            
                            <!-- Botón para mostrar/ocultar la contraseña -->
                            <button type="button" 
                                    onclick="togglePasswordVisibility('editForm.password', this)" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                                <svg id="eye-icon-editForm.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.928-.682 1.805-1.208 2.583m-2.178 2.178A9.956 9.956 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 011.208-2.583m2.178-2.178A9.96 9.96 0 0112 7c1.892 0 3.642.53 5.042 1.458" />
                                </svg>
                            </button>
                        </div>
                        
                        <x-input-error :messages="$errors->get('editForm.password')" class="mt-2" />
                    </div>
                    
                    <script>
                        function togglePasswordVisibility(inputId, button) {
                            const input = document.getElementById(inputId);
                            
                            if (input.type === 'password') {
                                input.type = 'text'; // Cambia a texto para mostrar la clave
                                button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A8.232 8.232 0 0112 19c-4.755 0-8.65-3.136-10-7 1.35-3.864 5.245-7 10-7 4.755 0 8.65 3.136 10 7a9.963 9.963 0 01-1.45 2.2"/>
                                                    </svg>`;
                            } else {
                                input.type = 'password'; // Cambia a contraseña para ocultar la clave
                                button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7"/>
                                                    </svg>`;
                            }
                        }
                    </script>
                    
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


        <form wire:submit="store2">
            <x-dialog-modal wire:model="CreateForm2.open">
                <x-slot name="title">
                    Registrar Vehiculo
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
                        <div>
                            <x-input-label for="CreateForm2.kilometraje_actual" :value="__('Kilometraje Actual')" />
                            <x-text-input id="CreateForm2.kilometraje_actual" class="block mt-2 w-full" type="number" min="0" wire:model.defer="CreateForm2.kilometraje_actual" :value="old('CreateForm2.kilometraje_actual')" placeholder="Ej: 120000" required />
                            <x-input-error :messages="$errors->get('CreateForm2.kilometraje_actual')" class="mt-2" />
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
    </div>

    @if($mod)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-1/2">
                <button wire:click="$set('mod', false)" class="float-right text-red-500">✖</button>

                @livewire('modificar-informacion-oficina.modificar-informacion-oficina', ['id' => $this->modificar_oficina_id])
            </div>
        </div>
    @endif

</div>

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
