@section('titulo')
    Mi Oficina
@endsection

<div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO --}}
    {{-- ============================================================ --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Mi Oficina</h1>
            <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Panel de gestión de oficina</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('Operaciones')
                <button wire:click="cambiar_operacion"
                    title="{{ $oficina->operaciones ? 'Cerrar operaciones' : 'Abrir operaciones' }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition active:scale-95 shadow-sm
                        {{ $oficina->operaciones
                            ? 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'
                            : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                    </svg>
                    Operaciones {{ $oficina->operaciones ? 'Abiertas' : 'Cerradas' }}
                </button>
            @endcan

            {{-- @can('Crear oficinas')
                @if(in_array($oficina->tipo_oficina_id, [1, 2, 3]))
                    <button wire:click="edit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-red-800 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-primary/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Asignar Jefe
                    </button>
                @endif
            @endcan --}}

            @can('Crear Integrantes de Oficina')
                <button wire:click="create"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-red-800 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-primary/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Crear Integrante
                </button>
            @endcan

            @php $usuario = auth()->user(); @endphp
            @if($usuario->hasRole('Gerente de Estado') || $usuario->hasRole('Jefe de OPT'))
                <button wire:click="create2" title="Registrar Vehículo"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-red-800 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-primary/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4-8v2m-6 6h2m10 0h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Vehículo
                </button>

                <button wire:click="vehiculo_externo({{$oficina->oficina_id}})" title="Registrar Vehículo Externo"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-emerald-600/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Externo
                </button>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TARJETA DE INFORMACIÓN DE OFICINA --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-1 h-5 bg-primary rounded-full"></div>
                <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Información de la Oficina</h2>
            </div>
            @if($usuario->hasRole('Jefe de OPT') && $usuario->oficina_id == $oficina->oficina_id)
                <button wire:click="modificar({{$oficina->oficina_id}})" title="Modificar Información"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-primary border border-primary/30 bg-primary/5 rounded-lg hover:bg-primary/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                    Editar
                </button>
            @endif
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Nombre --}}
            <div class="lg:col-span-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nombre</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->nombre }}</p>
            </div>

            {{-- Código --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Código</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->codigo }}</p>
            </div>

            {{-- Tipo --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipo de Oficina</p>
                <p class="text-sm font-bold text-gray-800">
                    @switch($oficina->tipo_oficina_id)
                        @case(1) OPT (Pequeña) @break
                        @case(2) OPT (Mediana) @break
                        @case(3) OPT (Grande) @break
                        @case(4) COP @break
                        @case(5) CENTRALIZADORA @break
                        @case(6) CPI @break
                        @default Desconocido
                    @endswitch
                </p>
            </div>

            {{-- Jefe --}}
            @php
                $jefe_id = $oficina->jefe_oficina;
                $jefe = null;
                if (is_numeric($jefe_id) && $jefe_id !== null) {
                    $jefe = \App\Models\User::find($jefe_id);
                }
            @endphp
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Jefe de Oficina</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina ? $oficina->jefe_oficina : 'No asignado' }}</p>
            </div>

            {{-- Correo --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Correo</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->correo ?? '—' }}</p>
            </div>

            {{-- Teléfono --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Teléfono</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->telefono ?? '—' }}</p>
            </div>

            {{-- Código ubicación --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Código Ubicación</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->codigo_ubicacion ?? '—' }}</p>
            </div>

            {{-- Estado --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</p>
                <p class="text-sm font-bold text-gray-800">{{ $estadoNombre }}</p>
            </div>

            {{-- Municipio --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Municipio</p>
                <p class="text-sm font-bold text-gray-800">{{ $municipioNombre }}</p>
            </div>

            {{-- Parroquia --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Parroquia</p>
                <p class="text-sm font-bold text-gray-800">{{ $parroquiaNombre }}</p>
            </div>

            {{-- Dirección --}}
            <div class="lg:col-span-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dirección</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->direccion ?? '—' }}</p>
            </div>

            {{-- ZEE --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Zona Económica Especial</p>
                <p class="text-sm font-bold text-gray-800">{{ $oficina->zona_economica_especial ? 'Sí' : 'No' }}</p>
            </div>

            {{-- Estatus --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estatus</p>
                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                    {{ $oficina->estatus_id == 1 ? 'bg-green-100 text-green-700' : '' }}
                    {{ $oficina->estatus_id == 2 ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $oficina->estatus_id == 3 ? 'bg-red-100 text-red-700' : '' }}">
                    {{ $oficina->estatus_id == 1 ? 'ACTIVA' : ($oficina->estatus_id == 2 ? 'INOPERATIVA' : ($oficina->estatus_id == 3 ? 'INACTIVA' : 'NO APLICA')) }}
                </span>
            </div>

            {{-- Fecha creación --}}
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Creación</p>
                <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($oficina->created_at)->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- USUARIOS SIN EMPLEADO VINCULADO --}}
    {{-- ============================================================ --}}
    @if($usuariosSinEmpleado->isNotEmpty() && $usuario->hasRole('Jefe de OPT') && $usuario->oficina_id == $oficina->oficina_id)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-amber-200 flex items-center gap-3">
                <div class="w-1 h-5 bg-amber-500 rounded-full"></div>
                <h3 class="text-xs font-black text-amber-800 uppercase tracking-widest">Usuarios sin empleado vinculado ({{ $usuariosSinEmpleado->count() }})</h3>
            </div>
            <div class="p-4 space-y-2">
                @foreach($usuariosSinEmpleado as $sinEmp)
                    <div class="flex items-center justify-between bg-white rounded-xl border border-amber-200 p-3" wire:key="sin-emp-{{ $sinEmp->id }}">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $sinEmp->name }}</p>
                            <p class="text-xs text-gray-500">{{ $sinEmp->cedula }} — {{ $sinEmp->email }}</p>
                        </div>
                        <button type="button" wire:click="abrirVincular({{ $sinEmp->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-primary bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Vincular
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TABS --}}
    {{-- ============================================================ --}}
    @if($usuario->hasRole('Gerente de Estado') || $usuario->hasRole('Jefe de Opt') || $usuario->can('Ver Integrantes de Oficina'))
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
            {{-- Tab bar --}}
            <div class="flex border-b border-gray-200">
                <button wire:click="setTab('integrantes')"
                    class="relative px-6 py-3.5 text-xs font-black uppercase tracking-widest transition
                        {{ $tab === 'integrantes'
                            ? 'text-primary border-b-2 border-primary bg-primary/5'
                            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Integrantes
                    </span>
                </button>
                <button wire:click="setTab('vehiculos')"
                    class="relative px-6 py-3.5 text-xs font-black uppercase tracking-widest transition
                        {{ $tab === 'vehiculos'
                            ? 'text-primary border-b-2 border-primary bg-primary/5'
                            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4-8v2m-6 6h2m10 0h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Vehículos
                    </span>
                </button>
                <button wire:click="setTab('roles')"
                    class="relative px-6 py-3.5 text-xs font-black uppercase tracking-widest transition
                        {{ $tab === 'roles'
                            ? 'text-primary border-b-2 border-primary bg-primary/5'
                            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Roles
                    </span>
                </button>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB: INTEGRANTES --}}
            {{-- ============================================================ --}}
            @if($tab === 'integrantes')
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Nombre</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cédula</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Teléfono</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Rol</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estatus</th>
                                <th class="px-5 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($oficinausuarios as $oficinausuario)
                                <tr wire:key="int-{{ $oficinausuario->id }}" class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-3 text-sm font-semibold text-gray-800">{{ $oficinausuario->name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $oficinausuario->email }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $oficinausuario->cedula }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $oficinausuario->telefono ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $oficinausuario->getRoleNames()->first() ?? 'Sin rol' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                                            {{ $oficinausuario->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $oficinausuario->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="editUsuario({{ $oficinausuario->id }})" title="Editar"
                                                class="p-1.5 rounded-lg text-violet-500 hover:bg-violet-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                            </button>
                                            @if($oficinausuario->getRoleNames()->first() !== 'SuperAdmin')
                                                @if ($oficinausuario->activo)
                                                    <button wire:click="desactivarUsuario({{ $oficinausuario->id }})" title="Desactivar"
                                                        class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                    </button>
                                                @else
                                                    <button wire:click="activarUsuario({{ $oficinausuario->id }})" title="Activar"
                                                        class="p-1.5 rounded-lg text-green-500 hover:bg-green-50 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm uppercase tracking-widest">No hay integrantes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Usuarios sin rol --}}
                @if(isset($usuariosSinRol) && $usuariosSinRol->isNotEmpty())
                    <div class="border-t border-gray-200 bg-amber-50/50">
                        <div class="px-6 py-3 flex items-center gap-3">
                            <div class="w-1 h-5 bg-amber-500 rounded-full"></div>
                            <h3 class="text-xs font-black text-amber-800 uppercase tracking-widest">Usuarios sin rol asignado ({{ $usuariosSinRol->count() }})</h3>
                        </div>
                        <div class="px-6 pb-4 space-y-2">
                            @foreach($usuariosSinRol as $sinRol)
                                <div class="flex items-center justify-between bg-white rounded-xl border border-amber-200 p-3" wire:key="sin-rol-{{ $sinRol->id }}">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $sinRol->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $sinRol->cedula }} — {{ $sinRol->email }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <select wire:model="rol_asignar.{{ $sinRol->id }}"
                                            class="border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-2 bg-white">
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
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-primary hover:bg-red-800 text-white font-bold rounded-xl text-xs uppercase tracking-widest transition active:scale-95">
                                            Asignar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            {{-- ============================================================ --}}
            {{-- TAB: VEHÍCULOS --}}
            {{-- ============================================================ --}}
            @if($tab === 'vehiculos')
                {{-- Search bar --}}
                <div class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row items-center gap-3">
                    <div class="relative w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm pl-10 p-2.5 bg-white"
                            placeholder="Buscar por placa...">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                        </div>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Por página</span>
                        <select wire:model.live="perPage" class="border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-2 bg-white">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Imagen</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Marca</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Modelo</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Placa</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Año</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Chofer</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Km</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estatus</th>
                                <th class="px-5 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($vehiculos as $vehiculo)
                                <tr wire:key="veh-{{ $vehiculo->vehiculo_id }}" class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-3">
                                        @if($vehiculo->imagen)
                                            <img src="{{ Storage::url($vehiculo->imagen) }}" alt="Vehículo" class="w-20 h-16 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sin imagen</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-sm font-semibold text-gray-800">{{ $vehiculo->marca }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $vehiculo->modelo }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $vehiculo->TipoVehiculo->tipo }}</td>
                                    <td class="px-5 py-3 text-sm font-bold text-gray-800">{{ $vehiculo->placa }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $vehiculo->año }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ optional($vehiculo->user)->name ?? 'Ninguno' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $vehiculo->kilometraje_actual }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                                            {{ $vehiculo->Activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $vehiculo->Activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="editVehiculo({{ $vehiculo->vehiculo_id }})" title="Editar"
                                                class="p-1.5 rounded-lg text-green-500 hover:bg-green-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
                                            </button>
                                            <button wire:click="editChofer({{ $vehiculo->vehiculo_id }})" title="Asignar Chofer"
                                                class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                            </button>
                                            @if ($vehiculo->Activo)
                                                <button wire:click="desactivarVehiculo({{ $vehiculo->vehiculo_id }})" title="Desactivar"
                                                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </button>
                                            @else
                                                <button wire:click="activarVehiculo({{ $vehiculo->vehiculo_id }})" title="Activar"
                                                    class="p-1.5 rounded-lg text-green-500 hover:bg-green-50 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </button>
                                            @endif
                                            <button wire:click="openCombustibleModal({{ $vehiculo->vehiculo_id }})" title="Combustible"
                                                class="p-1.5 rounded-lg text-primary hover:bg-red-50 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M3 19V4C3 3.44772 3.44772 3 4 3H13C13.5523 3 14 3.44772 14 4V12H16C17.1046 12 18 12.8954 18 14V18C18 18.5523 18.4477 19 19 19C19.5523 19 20 18.5523 20 18V11H18C17.4477 11 17 10.5523 17 10V6.41421L15.3431 4.75736L16.7574 3.34315L21.7071 8.29289C21.9024 8.48816 22 8.74408 22 9V18C22 19.6569 20.6569 21 19 21C17.3431 21 16 19.6569 16 18V14H14V19H15V21H2V19H3ZM5 5V11H12V5H5Z"></path></svg>
                                            </button>
                                            <button wire:click="createMantenimiento({{ $vehiculo->vehiculo_id }})" title="Mantenimiento"
                                                class="p-1.5 rounded-lg text-purple-600 hover:bg-purple-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-10 text-center text-gray-400 text-sm uppercase tracking-widest">No hay vehículos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($vehiculos) && $vehiculos->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $vehiculos->links() }}
                    </div>
                @endif
            @endif

            {{-- ============================================================ --}}
            {{-- TAB: ROLES --}}
            {{-- ============================================================ --}}
            @if($tab === 'roles')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest">Roles de la Oficina</h3>
                    <button wire:click="createRol"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-primary border border-primary/30 bg-primary/5 rounded-lg hover:bg-primary/10 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar Roles
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Rol</th>
                                <th class="px-5 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cantidad Máxima</th>
                                <th class="px-5 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($personalOficina as $rol)
                                <tr wire:key="rol-{{ $rol->oficina_personal_id }}" class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-3 text-sm font-semibold text-gray-800">{{ $rol->rol->name }}</td>
                                    <td class="px-5 py-3">
                                        @if ($editingId === $rol->oficina_personal_id)
                                            <div class="flex items-center gap-2">
                                                <input type="number" wire:model="cantidadMaxEdit" min="0"
                                                    class="w-20 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-primary focus:border-primary">
                                                <button wire:click="updateCantidadMax({{ $rol->oficina_personal_id }})"
                                                    class="px-2.5 py-1.5 text-[10px] font-black uppercase text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                                                    ✓
                                                </button>
                                                <button wire:click="cancelEdit"
                                                    class="px-2.5 py-1.5 text-[10px] font-black uppercase text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                                                    ✕
                                                </button>
                                            </div>
                                            @if($errors->has('cantidadMaxEdit'))
                                                <p class="text-red-500 text-[10px] font-black uppercase mt-1">{{ $errors->first('cantidadMaxEdit') }}</p>
                                            @endif
                                        @else
                                            <span class="text-sm font-bold text-gray-800">{{ $rol->cantidad_max }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="editRolCantidad({{ $rol->oficina_personal_id }}, {{ $rol->cantidad_max }})" title="Editar"
                                                class="p-1.5 rounded-lg text-green-500 hover:bg-green-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                            </button>
                                            <button wire:click="$dispatch('mostrarAlertaRol', {{ $rol->oficina_personal_id }})" title="Eliminar"
                                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-gray-400 text-sm uppercase tracking-widest">No hay roles disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODALES --}}
    {{-- ============================================================ --}}

    {{-- Modal vincular empleado --}}
    @if($modalVincular)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Vincular Empleado</h3>
                        <button type="button" wire:click="cerrarVincular" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Buscar empleado por cédula</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="vincular_cedula_buscar" placeholder="Ej: 12345678"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white"
                                    wire:keydown.enter="buscarEmpleado">
                                <button type="button" wire:click="buscarEmpleado"
                                    class="shrink-0 px-4 py-2.5 bg-primary hover:bg-red-800 text-white font-bold rounded-xl text-xs uppercase transition active:scale-95">
                                    Buscar
                                </button>
                            </div>
                            @error('vincular_cedula_buscar') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($vincular_empleado_encontrado)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Empleado encontrado</p>
                                <p class="text-sm font-bold text-gray-800">{{ $vincular_empleado_encontrado->nombre }} {{ $vincular_empleado_encontrado->apellido }}</p>
                                <p class="text-xs text-gray-600">Cédula: {{ $vincular_empleado_encontrado->tipo_documento }}{{ $vincular_empleado_encontrado->documento }}</p>
                                <p class="text-xs text-gray-600">Correo: {{ $vincular_empleado_encontrado->correo }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        @if($vincular_empleado_encontrado)
                            <button type="button" wire:click="confirmarVinculacion"
                                class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">
                                Vincular
                            </button>
                        @endif
                        <button type="button" wire:click="cerrarVincular"
                            class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal asignar jefe --}}
    <form wire:submit="update">
        <x-dialog-modal wire:model.blur="EditForm.open">
            <x-slot name="title">Asignar Jefe de Oficina</x-slot>
            <x-slot name="content">
                <div class="mt-4">
                    <x-input-label for="EditForm.jefe" :value="__('Seleccione su Jefe de Oficina')" />
                    <select wire:model.blur="EditForm.jefe" id="EditForm.jefe" class="w-full text-center border-gray-300 focus:border-secondary focus:ring-gray-500 rounded-md shadow-sm">
                        <option selected>-- Seleccionar --</option>
                        @foreach ($usuarios as $usr)
                            <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('EditForm.estado')" class="mt-2" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('EditForm.open', false)" type="button">Cancelar</x-danger-button>
                    <x-primary-button wire:loading.attr="disabled" wire:target="update">Guardar <x-loading-button wire:target="update"/></x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal crear integrante --}}
    <x-modal-crear-usuario
        :empleadosDisponibles="$empleadosDisponibles"
        :rolescantidad="$rolescantidad"
        :rolesCount="$rolesCount"
        :rolesNombres="$rolesNombres"
        :tiposDocumento="$tiposDocumento" />

    {{-- Modal editar usuario (tab integrantes) --}}
    <form wire:submit="updateUsuario">
        <x-dialog-modal wire:model.blur="editForm.open">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Editar Usuario</p>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Actualizar información del integrante</p>
                    </div>
                </div>
            </x-slot>
            <x-slot name="content">
                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-4">

                    {{-- Nombre --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre</label>
                        <input type="text" wire:model.blur="editForm.name"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Nombre completo"/>
                        <x-input-error :messages="$errors->get('editForm.name')" class="mt-1" />
                    </div>

                    {{-- Email --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Email</label>
                        <input type="email" wire:model.blur="editForm.email"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="correo@ejemplo.com"/>
                        <x-input-error :messages="$errors->get('editForm.email')" class="mt-1" />
                    </div>

                    {{-- Cédula (tipo + número) --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Cédula</label>
                        <div class="flex gap-2">
                            <select wire:model.blur="editForm.tipo_documento"
                                class="w-20 border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-2 py-2.5 bg-white">
                                <option value="V">V</option>
                                <option value="E">E</option>
                                <option value="J">J</option>
                                <option value="G">G</option>
                                <option value="P">P</option>
                            </select>
                            <input type="text" wire:model.blur="editForm.numero_documento" maxlength="10"
                                class="flex-1 block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="12345678"/>
                        </div>
                        <x-input-error :messages="$errors->get('editForm.tipo_documento')" class="mt-1" />
                        <x-input-error :messages="$errors->get('editForm.numero_documento')" class="mt-1" />
                        <x-input-error :messages="$errors->get('editForm.cedula')" class="mt-1" />
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                        <input type="text" wire:model.blur="editForm.telefono" maxlength="15"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: 04121234567"/>
                        <x-input-error :messages="$errors->get('editForm.telefono')" class="mt-1" />
                    </div>

                    {{-- Nueva clave --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Nueva Contraseña <span class="text-gray-300 normal-case">(dejar vacío para no cambiar)</span>
                        </label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'"
                                wire:model.blur="editForm.password"
                                autocomplete="new-password"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 pr-10 bg-white"
                                placeholder="••••••••"/>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('editForm.password')" class="mt-1" />
                    </div>

                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('editForm.open', false)"
                        class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateUsuario"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-violet-600 hover:bg-violet-700 rounded-xl shadow-md shadow-violet-600/20 transition active:scale-95 disabled:opacity-60">
                        <svg wire:loading wire:target="updateUsuario" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal registrar vehículo --}}
    <form wire:submit="store2">
        <x-dialog-modal wire:model="CreateForm2.open">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-4-8v2m-6 6h2m10 0h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Registrar Vehículo</p>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Nuevo vehículo de la oficina</p>
                    </div>
                </div>
            </x-slot>
            <x-slot name="content">
                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-4">

                    {{-- Placa --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Placa <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.blur="CreateForm2.placa" maxlength="10"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white uppercase"
                            placeholder="Ej: ABC123"/>
                        <x-input-error :messages="$errors->get('CreateForm2.placa')" class="mt-1" />
                    </div>

                    {{-- Color --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Color <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.blur="CreateForm2.color" maxlength="10"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Blanco"/>
                        <x-input-error :messages="$errors->get('CreateForm2.color')" class="mt-1" />
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Marca</label>
                        <input type="text" wire:model.blur="CreateForm2.marca" maxlength="12"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Toyota"/>
                        <x-input-error :messages="$errors->get('CreateForm2.marca')" class="mt-1" />
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Modelo</label>
                        <input type="text" wire:model.blur="CreateForm2.modelo" maxlength="20"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Hilux"/>
                        <x-input-error :messages="$errors->get('CreateForm2.modelo')" class="mt-1" />
                    </div>

                    {{-- Año --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Año <span class="text-red-500">*</span></label>
                        <select wire:model.blur="CreateForm2.año"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                            <option value="" disabled selected>Selecciona un año</option>
                            @foreach(range(date('Y'), 1960) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('CreateForm2.año')" class="mt-1" />
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo <span class="text-red-500">*</span></label>
                        <select wire:model.blur="CreateForm2.tipo"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                            <option value="" disabled selected>Selecciona un tipo</option>
                            @foreach($tiposvehiculos as $tiposvehiculo)
                                <option value="{{ $tiposvehiculo->tipo_vehiculo_id }}">{{ $tiposvehiculo->tipo }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('CreateForm2.tipo')" class="mt-1" />
                    </div>

                    {{-- Capacidad --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Capacidad (Kg)</label>
                        <input type="text" wire:model.blur="CreateForm2.capacidad" maxlength="12"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="0"/>
                        <x-input-error :messages="$errors->get('CreateForm2.capacidad')" class="mt-1" />
                    </div>

                    {{-- Kilometraje --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kilometraje Actual</label>
                        <input type="text" wire:model.blur="CreateForm2.kilometraje_actual" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="0"/>
                        <x-input-error :messages="$errors->get('CreateForm2.kilometraje_actual')" class="mt-1" />
                    </div>

                    {{-- Nro. Póliza --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nro. Póliza <span class="text-gray-300">(opcional)</span></label>
                        <input type="text" wire:model.blur="CreateForm2.poliza" maxlength="20"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: POL-00123"/>
                        <x-input-error :messages="$errors->get('CreateForm2.poliza')" class="mt-1" />
                    </div>

                    {{-- Fecha venc. póliza --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Venc. Póliza <span class="text-gray-300">(opcional)</span></label>
                        <input type="date" wire:model="CreateForm2.fecha_vecimiento"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"/>
                        <x-input-error :messages="$errors->get('CreateForm2.fecha_vecimiento')" class="mt-1" />
                    </div>

                    {{-- Imagen --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Imagen <span class="text-gray-300">(opcional)</span></label>
                        <input type="file" wire:model="CreateForm2.imagen" accept="image/*"
                            class="block w-full text-sm text-gray-600 border border-gray-300 rounded-xl shadow-sm bg-white
                                file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:uppercase
                                file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"/>
                        <x-input-error :messages="$errors->get('CreateForm2.imagen')" class="mt-1" />
                    </div>

                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('CreateForm2.open', false)"
                        class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="store2"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-md shadow-primary/20 transition active:scale-95 disabled:opacity-60">
                        <svg wire:loading wire:target="store2" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Registrar Vehículo
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal editar vehículo --}}
    <form wire:submit="updateVehiculo">
        <x-dialog-modal wire:model="editFormVehiculo.open">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Editar Vehículo</p>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Actualizar información del vehículo</p>
                    </div>
                </div>
            </x-slot>
            <x-slot name="content">
                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-4">

                    {{-- Placa --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Placa <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.blur="editFormVehiculo.placa" maxlength="10"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white uppercase"
                            placeholder="Ej: ABC123"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.placa')" class="mt-1" />
                    </div>

                    {{-- Color --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Color <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.blur="editFormVehiculo.color" maxlength="10"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Blanco"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.color')" class="mt-1" />
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Marca</label>
                        <input type="text" wire:model.blur="editFormVehiculo.marca" maxlength="12"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Toyota"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.marca')" class="mt-1" />
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Modelo</label>
                        <input type="text" wire:model.blur="editFormVehiculo.modelo" maxlength="20"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: Hilux"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.modelo')" class="mt-1" />
                    </div>

                    {{-- Año --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Año <span class="text-red-500">*</span></label>
                        <select wire:model.blur="editFormVehiculo.año"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                            <option value="" disabled selected>Selecciona un año</option>
                            @foreach(range(date('Y'), 1960) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('editFormVehiculo.año')" class="mt-1" />
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo <span class="text-red-500">*</span></label>
                        <select wire:model.blur="editFormVehiculo.tipo"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                            <option value="" disabled selected>Selecciona un tipo</option>
                            @foreach($tiposvehiculos as $tiposvehiculo)
                                <option value="{{ $tiposvehiculo->tipo_vehiculo_id }}">{{ $tiposvehiculo->tipo }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('editFormVehiculo.tipo')" class="mt-1" />
                    </div>

                    {{-- Capacidad --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Capacidad (Kg)</label>
                        <input type="text" wire:model.blur="editFormVehiculo.capacidad" maxlength="12"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="0"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.capacidad')" class="mt-1" />
                    </div>

                    {{-- Kilometraje --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kilometraje Actual <span class="text-red-500">*</span></label>
                        <input type="number" min="0" wire:model.blur="editFormVehiculo.kilometraje_actual"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="0"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.kilometraje_actual')" class="mt-1" />
                    </div>

                    {{-- Nro. Póliza --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nro. Póliza</label>
                        <input type="text" wire:model.blur="editFormVehiculo.poliza" maxlength="20"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: POL-00123"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.poliza')" class="mt-1" />
                    </div>

                    {{-- Fecha venc. póliza --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Venc. Póliza</label>
                        <input type="date" wire:model="editFormVehiculo.fecha_vecimiento"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.fecha_vecimiento')" class="mt-1" />
                    </div>

                    {{-- Imagen --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Imagen</label>
                        <input type="file" wire:model="editFormVehiculo.imagen" accept="image/*"
                            class="block w-full text-sm text-gray-600 border border-gray-300 rounded-xl shadow-sm bg-white
                                file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:uppercase
                                file:bg-green-100 file:text-green-700 hover:file:bg-green-200 cursor-pointer"/>
                        <x-input-error :messages="$errors->get('editFormVehiculo.imagen')" class="mt-1" />
                    </div>

                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('editFormVehiculo.open', false)"
                        class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateVehiculo"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-md shadow-green-600/20 transition active:scale-95 disabled:opacity-60">
                        <svg wire:loading wire:target="updateVehiculo" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal asignar chofer --}}
    <form wire:submit.prevent="updateChofer">
        <x-dialog-modal wire:model="editFormChofer.open">
            <x-slot name="title">Asignar Chofer</x-slot>
            <x-slot name="content">
                <div class="flex justify-center mt-4">
                    <div class="w-3/4">
                        <x-input-label for="editFormChofer.user_id" :value="__('Seleccione un Chofer')" />
                        <select id="editFormChofer.user_id" class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm" wire:model="editFormChofer.user_id">
                            <option value="">Seleccione un Chofer</option>
                            @if(isset($choferes))
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}">{{ $chofer->name }} - {{ $chofer->email }}</option>
                                @endforeach
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('editFormChofer.user_id')" class="mt-2" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('editFormChofer.open', false)" type="button">Cancelar</x-danger-button>
                    <x-primary-button wire:loading.attr="disabled" wire:target="updateChofer">Guardar <x-loading-button wire:target="updateChofer"/></x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal mantenimiento --}}
    <form wire:submit.prevent="storeMantenimiento">
        <x-dialog-modal wire:model="CreateForm3Vehiculo.open">
            <x-slot name="title">Registrar Mantenimiento</x-slot>
            <x-slot name="content">
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="CreateForm3Vehiculo.kilometraje" :value="__('Kilometraje')" />
                        <x-text-input id="CreateForm3Vehiculo.kilometraje" type="number" min="0" wire:model="CreateForm3Vehiculo.kilometraje" class="block mt-2 w-full" required />
                        <x-input-error :messages="$errors->get('CreateForm3Vehiculo.kilometraje')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="CreateForm3Vehiculo.costo_total" :value="__('Costo Total')" />
                        <x-text-input id="CreateForm3Vehiculo.costo_total" type="number" min="0" step="0.01" wire:model="CreateForm3Vehiculo.costo_total" class="block mt-2 w-full" required />
                        <x-input-error :messages="$errors->get('CreateForm3Vehiculo.costo_total')" class="mt-2" />
                    </div>
                </div>
                @foreach ($servicios as $index => $servicio)
                    <div class="mb-4 grid grid-cols-2 gap-4 border-b pb-4 relative">
                        <div>
                            <x-input-label :for="'servicio_id_' . $index" :value="__('Servicio')" />
                            <select wire:model="CreateForm3Vehiculo.servicios.{{ $index }}.servicio_id" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" disabled selected>Seleccione</option>
                                @if(isset($serviciosActivos))
                                    @foreach($serviciosActivos as $serv)
                                        <option value="{{ $serv->servicios_flota_id }}">{{ $serv->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <x-input-label :for="'fecha_' . $index" :value="__('Fecha')" />
                            <x-text-input type="date" wire:model="CreateForm3Vehiculo.servicios.{{ $index }}.fecha" class="block mt-2 w-full" />
                        </div>
                        @if($index > 0)
                            <button type="button" class="absolute top-0 right-0 text-red-500" wire:click="removeServicio({{ $index }})">✕</button>
                        @endif
                    </div>
                @endforeach
                <x-primary-button type="button" class="mt-2" wire:click="addServicio">Agregar más servicios +</x-primary-button>
                <div class="mt-4">
                    <x-input-label for="CreateForm3Vehiculo.descripcion" :value="__('Descripción')" />
                    <textarea wire:model="CreateForm3Vehiculo.descripcion" class="block mt-2 w-full border-gray-300 rounded-md shadow-sm" rows="3" placeholder="Descripción..."></textarea>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('CreateForm3Vehiculo.open', false)" type="button">Cancelar</x-danger-button>
                    <x-primary-button wire:loading.attr="disabled" wire:target="storeMantenimiento">Crear <x-loading-button wire:target="storeMantenimiento"/></x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal combustible --}}
    <x-dialog-modal wire:model.defer="combustibleModal.open">
        <x-slot name="title">
            <span class="text-primary font-bold">Registrar Carga de Combustible</span>
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="combustibleForm.fecha" :value="__('Fecha')" />
                    <x-text-input id="combustibleForm.fecha" type="date" class="block w-full mt-1" wire:model.defer="combustibleForm.fecha" required />
                    <x-input-error :messages="$errors->get('combustibleForm.fecha')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.litros" :value="__('Litros (L)')" />
                    <x-text-input id="combustibleForm.litros" type="number" step="0.01" min="0" class="block w-full mt-1" wire:model.defer="combustibleForm.litros" required />
                    <x-input-error :messages="$errors->get('combustibleForm.litros')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.costo_total" :value="__('Costo Total (Bs)')" />
                    <x-text-input id="combustibleForm.costo_total" type="number" step="0.01" min="0" class="block w-full mt-1" wire:model.defer="combustibleForm.costo_total" required />
                    <x-input-error :messages="$errors->get('combustibleForm.costo_total')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="combustibleForm.kilometraje" :value="__('Kilometraje (Km)')" />
                    <x-text-input id="combustibleForm.kilometraje" type="number" min="0" class="block w-full mt-1" wire:model.defer="combustibleForm.kilometraje" required />
                    <x-input-error :messages="$errors->get('combustibleForm.kilometraje')" class="mt-2" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-secondary-button wire:click="$set('combustibleModal.open', false)">Cancelar</x-secondary-button>
                <x-primary-button wire:click="saveCombustible">Guardar</x-primary-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal agregar roles --}}
    <form wire:submit="storeRol">
        <x-dialog-modal wire:model="CreateForm3Rol.open">
            <x-slot name="title">Roles Disponibles</x-slot>
            <x-slot name="content">
                <div class="mt-2 mb-2">
                    <div class="overflow-y-auto max-h-[60vh] border border-gray-200 sm:rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 relative">
                            <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">Elegir</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nombre del Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-32">Máx. usos</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if(isset($roleOficina))
                                    @forelse($roleOficina as $rolDisp)
                                        <tr class="hover:bg-indigo-50 transition cursor-pointer" onclick="document.getElementById('rol_{{ $rolDisp->id }}').click()">
                                            <td class="px-6 py-3 text-center" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="rol_{{ $rolDisp->id }}" wire:model="CreateForm3Rol.roles.{{ $rolDisp->id }}.selected" value="{{ $rolDisp->id }}" class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary"/>
                                            </td>
                                            <td class="px-6 py-3"><div class="text-sm font-medium text-gray-700">{{ $rolDisp->name }}</div></td>
                                            <td class="px-6 py-3" onclick="event.stopPropagation()">
                                                <input type="number" wire:model="CreateForm3Rol.roles.{{ $rolDisp->id }}.maxUso" class="w-full text-sm border-gray-300 rounded-lg py-1.5" placeholder="Cant." min="1" max="99"/>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">No hay roles disponibles.</td></tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('CreateForm3Rol.open', false)" type="button">Cancelar</x-danger-button>
                    <x-primary-button wire:loading.attr="disabled" wire:target="storeRol">Crear <x-loading-button wire:target="storeRol"/></x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- Modal modificar oficina --}}
    @if($mod)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-2xl shadow-2xl w-1/2">
                <button wire:click="$set('mod', false)" class="float-right text-red-500 hover:text-red-700 transition">✖</button>
                @livewire('modificar-informacion-oficina.modificar-informacion-oficina', ['id' => $this->modificar_oficina_id])
            </div>
        </div>
    @endif

    {{-- Modal vehículo externo --}}
    @if($modal_vehiculo)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-2xl shadow-2xl w-1/2">
                <button wire:click="$set('modal_vehiculo', false)" class="float-right text-red-500 hover:text-red-700 transition">✖</button>
                @livewire('añadir-vehiculo.añadir-vehiculo', ['id' => $this->oficina_vehiculo])
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertError', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('mostrarAlertaRol', oficina_personal_id => {
            Swal.fire({
                title: "Eliminar Rol?",
                text: "Desea eliminar este rol de la oficina?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteRol', { rol: oficina_personal_id });
                }
            });
        });

        Livewire.on('tarifaUpdated', () => {
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
    </script>
@endpush
