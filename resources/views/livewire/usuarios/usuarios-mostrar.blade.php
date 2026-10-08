<div>
    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Cuentas</h1>
                <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Gestión de usuarios del sistema</p>
            </div>
            {{-- @can('Crear usuarios')
                <button wire:click="open"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-red-800 text-white font-black rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Crear Usuario
                </button>
            @endcan --}}
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">

            {{-- Filtros --}}
            <div class="p-4 border-b border-gray-300">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    {{-- Buscador --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre o cédula..."
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 pl-10 transition bg-white">
                        </div>
                    </div>

                    {{-- Filtro por rol --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rol</label>
                        <select wire:model.live="filter"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                            <option value="all">Todos los roles</option>
                            <option value="sin_rol">Sin rol asignado</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro por estado --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</label>
                        <select wire:model.live="filtroEstado"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                            <option value="">Todos</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro por oficina --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Oficina</label>
                        <select wire:model.live="filtroOficina"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white"
                            @if(!$filtroEstado) disabled @endif>
                            <option value="">Todas</option>
                            @foreach ($oficinas as $oficina)
                                <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Correo</th>
                            <th class="px-4 py-3 text-left">Cédula</th>
                            <th class="px-4 py-3 text-left">Oficina</th>
                            <th class="px-4 py-3 text-left">Rol</th>
                            <th class="px-4 py-3 text-center">Estatus</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($usuarios as $usuario)
                            <tr wire:key="{{ $usuario->id }}" class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $usuario->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $usuario->cedula }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $usuario->oficina->nombre ?? 'Sin oficina' }}</td>
                                <td class="px-4 py-3">
                                    @if($usuario->getRoleNames()->first())
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $usuario->getRoleNames()->first() }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                            Sin rol
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-black uppercase tracking-widest rounded-lg {{ $usuario->activo ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Editar --}}
                                        <button wire:click="abrirEditar({{ $usuario->id }})" title="Editar correo/contraseña"
                                            class="p-1.5 text-violet-500 hover:bg-violet-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                        </button>

                                        {{-- Asignar rol --}}
                                        @if($usuario->getRoleNames()->first() !== 'SuperAdmin')
                                            @can('Editar Usuarios')
                                                <button wire:click="abrirAsignarRol({{ $usuario->id }})" title="Asignar rol"
                                                    class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                                </button>
                                            @endcan

                                            {{-- Transferir --}}
                                            @can('Editar Usuarios')
                                                <button wire:click="abrirTransferir({{ $usuario->id }})" title="Transferir a otra oficina"
                                                    class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                                </button>
                                            @endcan

                                            {{-- Activar/Desactivar --}}
                                            @if ($usuario->activo)
                                                <button wire:click="desactivar({{ $usuario->id }})" title="Desactivar"
                                                    class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </button>
                                            @else
                                                <button wire:click="activar({{ $usuario->id }})" title="Activar"
                                                    class="p-1.5 text-green-500 hover:bg-green-50 rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-400 italic">No se encontraron usuarios</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="p-4 border-t border-gray-300 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Por página</label>
                    <select wire:model.live="perPage"
                        class="border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm p-1.5 bg-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CREAR USUARIO --}}
    {{-- ============================================================ --}}
    @if($isOpen)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Crear Usuario</h3>
                        <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                @error('name') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Correo <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="email" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                @error('email') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo <span class="text-red-500">*</span></label>
                                    <select wire:model="tipoCedula" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                        <option value="">-</option>
                                        <option value="V-">V-</option>
                                        <option value="E-">E-</option>
                                    </select>
                                    @error('tipoCedula') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Cédula <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="cedula" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                    @error('cedula') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Contraseña <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="password" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                @error('password') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                            <button type="submit" class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">Crear</button>
                            <button type="button" wire:click="close" class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: EDITAR USUARIO (solo email + contraseña) --}}
    {{-- ============================================================ --}}
    @if($modalEditar)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Editar Usuario</h3>
                        <button type="button" wire:click="cerrarEditar" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Info no editable --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nombre</p>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $editar_nombre }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cédula</p>
                                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $editar_cedula }}</p>
                            </div>
                        </div>

                        {{-- Campos editables --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Correo <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="editar_email" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                            @error('editar_email') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nueva Contraseña</label>
                            <input type="password" wire:model="editar_password" placeholder="Dejar vacío para mantener la actual"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                            @error('editar_password') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            <p class="text-[10px] text-gray-400 mt-1">Mínimo 8 caracteres. Si no escribe nada se mantiene la actual.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="guardarEdicion" class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">Guardar</button>
                        <button type="button" wire:click="cerrarEditar" class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: ASIGNAR ROL --}}
    {{-- ============================================================ --}}
    @if($modalRol)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Asignar Rol</h3>
                        <button type="button" wire:click="cerrarAsignarRol" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $rol_usuario_nombre }}</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Rol <span class="text-red-500">*</span></label>
                            @if(count($rolesDisponiblesOficina) > 0)
                                <select wire:model="rol_seleccionado"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                    <option value="">Seleccione un rol...</option>
                                    @foreach($rolesDisponiblesOficina as $rol)
                                        <option value="{{ $rol['id'] }}" {{ !$rol['disponible'] ? 'disabled' : '' }}>
                                            {{ $rol['name'] }} ({{ $rol['actual'] }}/{{ $rol['max'] }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <p class="text-sm text-gray-500 italic">No hay roles configurados para la oficina de este usuario.</p>
                            @endif
                            @error('rol_seleccionado') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="guardarRol" class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">Asignar</button>
                        <button type="button" wire:click="cerrarAsignarRol" class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: TRANSFERIR USUARIO --}}
    {{-- ============================================================ --}}
    @if($modalTransferir)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Transferir Usuario</h3>
                        <button type="button" wire:click="cerrarTransferir" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</p>
                            <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $transferir_usuario_nombre }}</p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Al transferir, el usuario perderá su rol actual. La oficina destino deberá asignarle uno nuevo.</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado Destino <span class="text-red-500">*</span></label>
                            <select wire:model.lazy="transferir_estado_destino"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                <option value="">Seleccione un estado...</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('transferir_estado_destino') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if(count($oficinasDestino) > 0)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Oficina Destino <span class="text-red-500">*</span></label>
                                <select wire:model="transferir_oficina_destino"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 bg-white">
                                    <option value="">Seleccione una oficina...</option>
                                    @foreach($oficinasDestino as $ofi)
                                        <option value="{{ $ofi['oficina_id'] }}">{{ $ofi['nombre'] }}</option>
                                    @endforeach
                                </select>
                                @error('transferir_oficina_destino') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="confirmarTransferencia" class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">Transferir</button>
                        <button type="button" wire:click="cerrarTransferir" class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 2000
            });
        })

        Livewire.on('alertError', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })
    </script>
    @endscript
@endpush
