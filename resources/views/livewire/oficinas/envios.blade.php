@section('titulo')
    Oficina
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-10">
    <x-return-link :href="route('oficinas-mostrar')" wire:navigate.hover />
    <h1 class="text-3xl text-gray-100 font-bold">Detalles de la Oficina</h1>
    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">

        @can('Crear oficinas')    
            @if($oficina->tipo_oficina_id === 1) <!-- Verifica si el tipo de oficina es OPT -->
                <x-primary-button wire:click="edit">
                    <Ri:a>nuevo jefe de oficina</Ri:a>
                </x-primary-button>
            @endif
        @endcan

    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <h2 class="text-2xl font-semibold">Información General</h2>
            <p class="mt-2">Nombre: <strong>{{ $oficina->nombre }}</strong></p>
            <p>Código: <strong>{{ $oficina->codigo }}</strong></p>
            <p>Tipo de oficina:
                <strong>
                    @switch($oficina->tipo_oficina_id)
                        @case(1) OPT @break
                        @case(2) COP @break
                        @case(3) CENTRALIZADORA @break
                        @case(4) CPI @break
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

        <p>Jefe de Oficina: 
            <strong>
                {{ $jefe ? $jefe->name : 'No tiene jefe' }} 
            </strong>
        </p>

            <p>Correo: <strong>{{ $oficina->correo }}</strong></p>
            <p>Teléfono: <strong>{{ $oficina->telefono }}</strong></p>
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
    </div>


    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
        <div class="flex flex-full md:flex-row w-full">
            <x-tab-link :href="route('integrantes', ['oficina_id' => $oficina_id])" :active="request()->routeIs('integrantes')" wire:navigate.hover>
                {{ __('Integrantes de Oficina') }}
            </x-tab-link>

            <x-tab-link :href="route('envios', ['oficina_id' => $oficina_id])" :active="request()->routeIs('envios')" wire:navigate.hover>
                {{ __('Envios hechos') }}
            </x-tab-link>
    </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-default uppercase bg-gray-100">
                    <tr class="">
                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código de Envío'])
                        @include('livewire.includes.sort-table', ['column' => 'nombre_rem', 'displayName' => 'Remitente'])
                        @include('livewire.includes.sort-table', ['column' => 'estado_dest', 'displayName' => 'Oficina Origen'])
                        @include('livewire.includes.sort-table', ['column' => 'contenido', 'displayName' => 'Descripción'])
                        <th class="px-4 py-3 text-center text-black">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if ($envio->isEmpty())
                                        <tr class="border-b text-center">
                                            <th colspan="5" class="py-7 text-default text-2xl">No hay Envíos</th>
                                        </tr>
                                    @endif
                                    @foreach($envio as $envi)
                                        <tr wire:key="{{ $envi->envio_id }}" class="border-b text-left">
                                            @if ($envio->pluck('created_at')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->created_at }}</th>
                                            @endif
                                            @if ($envio->pluck('codigo_envio')->filter()->isNotEmpty())
                                                <th class="px-4 py-3 font-medium text-black">{{ $envi->codigo_envio }}</th>
                                            @endif
                                            @if ($envio->pluck('nombre_rem')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->nombre_rem }} {{ $envi->apellido_rem }} - {{ $envi->documento_rem }}</th>
                                            @endif
                                            @if ($envio->pluck('nombre_dest')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->nombre_dest }} {{ $envi->apellido_dest }} - {{ $envi->documento_dest }}</th>
                                            @endif
                                            @if ($envio->pluck('oficina_id')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->oficinas->nombre }}</th>
                                            @endif
                                            @if ($envio->pluck('contenido')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->contenido }}</th>
                                            @endif
                                            @if ($envio->pluck('peso')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->peso }} g</th>
                                            @endif  
                                            @if ($envio->pluck('coste')->filter()->isNotEmpty())
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->coste }} Bs.</th>
                                            @endif
                                            <th class="px-4 py-3 font-medium flex items-center justify-center gap-4">
                                                <button href="{{ route('envios.detalles-envios', ['servicio_id' => $servicio_id, 'envio' => $envi->envio_id]) }}" title="Detalles" wire:navigate.hover>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                    </svg>
                                                </button>
                                            </th>
                                        </tr>
                                    @endforeach
                </tbody>
            </table>
        </div>
            
        <div class="overflow-x-auto mt-4">
        </div>
        <form wire:submit="update">
            <x-dialog-modal wire:model.blur="EditForm.open">
                <x-slot name="title">
                    Asignar Oficina
                </x-slot>
        
                <x-slot name="content">
                    <div class="mt-5">
                        <div class="mt-4">
                            <x-input-label for="EditForm.jefe" :value="__('Seleccione su Jefe de Oficina')" />
                            <select wire:model.blur="EditForm.jefe" id="EditForm.jefe" class="w-full text-center border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm" autocomplete="Jefe de Oficina">
                                <option selected>-- Seleccionar --</option>
                                    @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                    @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('EditForm.estado')" class="mt-2" />
                        </div>
                    </div>
                </x-slot>
        
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-danger-button class="mr-2" wire:click="$set('EditForm.open', false)" type="button">
                            Cancelar
                        </x-danger-button>
        
                        <x-primary-button wire:loading.attr="disabled" wire:target="update2">
                            Guardar
                            <x-loading-button wire:target="update"/>
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
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.name" :value="__('Nombre')" />
                        <x-text-input id="CreateForm.name" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.name" :value="old('CreateForm.name')" placeholder="Nombre"/>
                        <x-input-error :messages="$errors->get('CreateForm.name')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.correo" :value="__('Correo')" />
                        <x-text-input id="CreateForm.correo" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.correo" :value="old('CreateForm.correo')" placeholder="Correo"/>
                        <x-input-error :messages="$errors->get('CreateForm.correo')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.clave" :value="__('Clave')" />
                        <x-text-input id="CreateForm.clave" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.clave" :value="old('CreateForm.clave')" placeholder="Clave"/>
                        <x-input-error :messages="$errors->get('CreateForm.clave')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="CreateForm.role_id" :value="__('Selecciona un rol')" />
                        <select name="roles" id="CreateForm.role_id" wire:model.blur="CreateForm.role_id" class="w-full text-center border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm">
                            <option value="">---SELECCIONE---</option>
                            @foreach($rolescantidad as $rol)
                                <option value="{{ $rol->rol_id }}">
                                    {{ $rolesNombres[$rol->rol_id] ?? $rol->rol_id }}    
                                    {{ $rolesCount[$rol->rol_id]?? 0 }}/{{ $rol->cantidad_max }}
                                </option>
                            @endforeach   
                        </select>
                        
                        
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
    </div>
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
