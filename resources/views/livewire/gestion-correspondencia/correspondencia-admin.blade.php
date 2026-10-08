<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                @if($current_view == 'dashboard') Gestión Administrativa Correspondencia
                @elseif($current_view == 'users') Directorio de Colaboradores
                @elseif($current_view == 'create' || $current_view == 'edit') Asignación de Privilegios
                @endif
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Rol Activo: <span class="font-bold text-red-600">{{ $current_role }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button wire:click="open_dashboard"
                class="px-4 py-2 text-sm font-bold rounded-lg transition {{ $current_view === 'dashboard' ? 'bg-red-600 text-white shadow hover:bg-red-700' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }} flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </button>
            <button wire:click="open_users"
                class="px-4 py-2 text-sm font-bold rounded-lg transition {{ $current_view === 'users' ? 'bg-red-600 text-white shadow hover:bg-red-700' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' }} flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Listado
            </button>
            <button wire:click="open_create"
                class="px-4 py-2 text-sm font-bold rounded-lg bg-red-600 text-white shadow hover:bg-red-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo
            </button>

            @can('Ver Reportes Correspondencia')
                <a href="{{ route('correspondencia.reportes') }}"
                    class="px-3 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow hover:bg-emerald-700 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Reportes
                </a>
            @endcan

            @can('Ver Auditoria Correspondencia')
                <a href="{{ route('correspondencia.auditoria') }}"
                    class="px-3 py-2 bg-slate-800 text-white font-bold rounded-lg shadow-md hover:bg-slate-900 transition-all flex items-center gap-2 text-sm border border-slate-700">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Auditoría
                </a>
            @endcan
        </div>
    </div>

    <div class="min-h-[400px]">

        {{-- ===================== DASHBOARD ===================== --}}
        @if($current_view === 'dashboard')
            <div class="space-y-6">

                {{-- STATS CARDS --}}
                <div class="grid grid-cols-3 gap-4">
                    {{-- Card 1: Habilitados --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Habilitados</p>
                            <p class="text-3xl font-extrabold text-slate-800 leading-none mt-1">{{ $dashboard_stats['total_habilitados'] }}</p>
                        </div>
                    </div>

                    {{-- Card 2: Sin Asignar --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Sin Asignar</p>
                            <p class="text-3xl font-extrabold text-slate-800 leading-none mt-1">{{ $dashboard_stats['candidatos'] }}</p>
                        </div>
                    </div>

                    {{-- Card 3: Roles --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Roles</p>
                            <p class="text-3xl font-extrabold text-slate-800 leading-none mt-1">5</p>
                        </div>
                    </div>
                </div>

                {{-- DISTRIBUCIÓN Y ÚLTIMAS ASIGNACIONES --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Distribución de Roles --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                            Distribución de Roles
                        </h3>
                        <div class="space-y-4">
                            @php
                                $roles_data = [
                                    ['rol' => 'Presidente', 'count' => $dashboard_stats['presidentes'], 'color' => 'bg-red-500', 'track' => 'bg-red-100'],
                                    ['rol' => 'Director(a)', 'count' => $dashboard_stats['directores'], 'color' => 'bg-blue-500', 'track' => 'bg-blue-100'],
                                    ['rol' => 'Gerente', 'count' => $dashboard_stats['gerentes'], 'color' => 'bg-orange-400', 'track' => 'bg-orange-100'],
                                    ['rol' => 'Analista', 'count' => $dashboard_stats['analistas'], 'color' => 'bg-green-500', 'track' => 'bg-green-100'],
                                    ['rol' => 'Usuario', 'count' => $dashboard_stats['usuarios_reg'], 'color' => 'bg-violet-500', 'track' => 'bg-violet-100'],
                                ];
                                $max = max(array_column($roles_data, 'count')) ?: 1;
                            @endphp
                            @foreach($roles_data as $rd)
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-sm font-semibold text-slate-600">{{ $rd['rol'] }}</span>
                                        <span class="text-sm font-bold text-slate-800">{{ $rd['count'] }}</span>
                                    </div>
                                    <div class="w-full {{ $rd['track'] }} rounded-full h-2.5 overflow-hidden">
                                        <div class="{{ $rd['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ ($rd['count'] / $max) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Últimas Asignaciones --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Últimas Asignaciones
                            </h3>
                            <button wire:click="open_users" class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wide">Ver Todo</button>
                        </div>
                        <div class="space-y-3">
                            @forelse($dashboard_stats['ultimas_altas'] as $u)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-red-50 hover:border-red-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-slate-500 font-bold text-sm group-hover:bg-red-600 group-hover:text-white group-hover:border-red-600 transition">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 text-sm capitalize">{{ strtolower($u->name) }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                                                @foreach($correspondencia_roles as $cr)
                                                    @if($u->hasRole($cr))
                                                        <span class="text-red-500">{{ str_replace(' Correspondencia', '', $cr) }}</span>
                                                    @endif
                                                @endforeach
                                                &nbsp;· {{ $u->created_at->format('d/m/y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-7 h-7 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400">
                                    <p class="font-semibold text-sm">Sin asignaciones recientes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        {{-- ===================== TABLA DE USUARIOS ===================== --}}
        @elseif($current_view === 'users')
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Toolbar --}}
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col md:flex-row gap-3 justify-between items-center">
                    <div class="relative w-full max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search_query" type="text"
                            class="w-full pl-9 pr-4 py-2 rounded-lg bg-white border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition text-sm text-slate-700 placeholder:text-gray-400"
                            placeholder="Buscar por nombre o correo...">
                    </div>
                    <select wire:model.live="filter_rol"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition text-sm font-semibold text-slate-700 w-full md:w-56">
                        <option value="">Todos los Roles</option>
                        @foreach($correspondencia_roles as $role)
                            <option value="{{ $role }}">{{ str_replace(' Correspondencia', '', $role) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3">Colaborador</th>
                                <th class="px-5 py-3">Contacto</th>
                                <th class="px-5 py-3">Perfil Correspondencia</th>
                                <th class="px-5 py-3">Roles Sistema</th>
                                <th class="px-5 py-3 text-center">Gestión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($usuarios_filtrados as $user)
                                <tr class="hover:bg-gray-50 transition {{ $loop->even ? 'bg-gray-50/40' : 'bg-white' }}" wire:key="user-{{ $user->id }}">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-slate-500 font-bold text-sm flex-shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800 capitalize leading-none">{{ strtolower($user->name) }}</p>
                                                <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-tight">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-medium text-slate-600 truncate max-w-[180px] block">{{ $user->email }}</span>
                                        <span class="text-[10px] uppercase font-bold text-gray-300">Correo Institucional</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @php
                                            $currentRole = '';
                                            foreach($correspondencia_roles as $cr) {
                                                if($user->hasRole($cr)) { $currentRole = $cr; break; }
                                            }
                                            $rolColors = [
                                                'Presidente Correspondencia' => 'bg-red-100 text-red-700 border-red-200',
                                                'Director Correspondencia'   => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'Gerente Correspondencia'    => 'bg-orange-100 text-orange-700 border-orange-200',
                                                'Analista Correspondencia'   => 'bg-green-100 text-green-700 border-green-200',
                                                'Usuario Correspondencia'    => 'bg-violet-100 text-violet-700 border-violet-200',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $rolColors[$currentRole] ?? 'bg-gray-100 text-gray-500 border-gray-200' }} uppercase tracking-wide">
                                            {{ str_replace(' Correspondencia', '', $currentRole) ?: 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @php $sysRoles = $user->getRoleNames()->filter(fn($r) => !in_array($r, $correspondencia_roles)); @endphp
                                            @foreach($sysRoles as $sr)
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-tight">{{ $sr }}</span>
                                            @endforeach
                                            @if($sysRoles->isEmpty()) <span class="text-gray-300 font-medium italic text-xs">Ninguno</span> @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="edit_user({{ $user->id }})"
                                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center hover:bg-blue-600 hover:text-white transition"
                                                title="Editar Rol">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="confirm_delete({{ $user->id }})"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                                title="Revocar Acceso">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 mb-3">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <p class="font-semibold text-slate-600">No se encontraron resultados</p>
                                        <p class="text-gray-400 text-sm mt-1">Ajusta los filtros de búsqueda e intenta nuevamente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer con paginación --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Por página</label>
                            <select wire:model.live="perPage"
                                class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-2 focus:ring-red-100 focus:border-red-400 block w-24 p-2 transition">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                            <p class="text-xs font-semibold text-gray-400">
                                {{ $usuarios_filtrados->firstItem() ?? 0 }}–{{ $usuarios_filtrados->lastItem() ?? 0 }}
                                de {{ $usuarios_filtrados->total() }} colaboradores
                            </p>
                        </div>
                        <div>
                            {{ $usuarios_filtrados->links() }}
                        </div>
                    </div>
                </div>
            </div>

        {{-- ===================== FORMULARIO CREAR / EDITAR ===================== --}}
        @elseif($current_view === 'create' || $current_view === 'edit')
            <div class="max-w-3xl mx-auto py-4">
                <form wire:submit.prevent="save_user" class="space-y-6">

                    {{-- Selección de Colaborador --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-1 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Selección de Colaborador
                        </h3>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-5">Paso 01 — Identificación en la base de datos</p>

                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Colaborador del Sistema</label>
                            <div class="relative flex items-center">
                                <input
                                    wire:model.live="user_search"
                                    @focus="open = true"
                                    @input="open = true"
                                    @keydown.escape="open = false"
                                    @if($current_view === 'edit') disabled @endif
                                    class="w-full h-12 pl-10 pr-4 rounded-lg border border-gray-200 bg-gray-50 font-medium text-slate-700 text-sm outline-none focus:border-red-400 focus:bg-white focus:ring-2 focus:ring-red-100 transition disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                                    placeholder="Escribe el nombre o correo del colaborador..."
                                    autocomplete="off"
                                >
                                <div class="absolute left-3 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>

                            {{-- Dropdown sugerencias --}}
                            <div x-show="open && $wire.user_search.length > 0 && @js($available_users->count() > 0)"
                                 x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden"
                                 style="max-height: 260px; overflow-y: auto;"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                            >
                                <div class="p-1 space-y-0.5">
                                    @foreach($available_users as $user)
                                        <button
                                            type="button"
                                            wire:click="select_suggested_user({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                            @click="open = false"
                                            class="w-full px-4 py-3 text-left flex items-center gap-3 hover:bg-red-50 rounded-lg transition group/item"
                                        >
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-sm group-hover/item:bg-red-600 group-hover/item:text-white transition flex-shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800 text-sm capitalize group-hover/item:text-red-700 transition">{{ strtolower($user->name) }}</p>
                                                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-tight">{{ $user->email }}</p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('user_id') <p class="text-red-500 text-xs font-semibold mt-1.5 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Selección de Rol --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-1">
                            Privilegios de Correspondencia
                        </h3>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-5">Paso 02 — Selecciona el rol que se le asignará al colaborador</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @php
                                $roles_config = [
                                    ['value' => 'Presidente Correspondencia', 'label' => 'Presidente',   'desc' => 'Máxima autoridad del módulo. Auditoría global y seguimiento.'],
                                    ['value' => 'Director Correspondencia',   'label' => 'Director',     'desc' => 'Emisión de instrucciones y asignación de prioridades.'],
                                    ['value' => 'Gerente Correspondencia',    'label' => 'Gerente',      'desc' => 'Supervisión de flujo y validación de comunicaciones.'],
                                    ['value' => 'Analista Correspondencia',   'label' => 'Analista',     'desc' => 'Gestión operativa diaria y registro de documentos.'],
                                    ['value' => 'Usuario Correspondencia',    'label' => 'Usuario',      'desc' => 'Consulta de bandeja de entrada y confirmación de recepción.'],
                                ];
                            @endphp
                            @foreach($roles_config as $rc)
                                @php $selected = $rol_correspondencia === $rc['value']; @endphp
                                <label class="cursor-pointer block">
                                    <input type="radio" wire:model.live="rol_correspondencia" value="{{ $rc['value'] }}" class="sr-only">
                                    <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-all
                                        {{ $selected
                                            ? 'border-red-500 bg-red-50 shadow-sm'
                                            : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}">
                                        <div>
                                            <p class="font-bold text-sm {{ $selected ? 'text-red-700' : 'text-slate-700' }}">
                                                {{ $rc['label'] }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5 leading-snug">{{ $rc['desc'] }}</p>
                                        </div>
                                        <div class="ml-3 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center border-2 transition-all
                                            {{ $selected ? 'border-red-500 bg-red-500' : 'border-gray-300 bg-white' }}">
                                            @if($selected)
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('rol_correspondencia') <p class="text-red-500 text-xs font-semibold mt-3 text-center">{{ $message }}</p> @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-between pt-2">
                        <button type="button" wire:click="open_users"
                            class="px-5 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-700 transition flex items-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-red-600 text-white text-sm font-bold rounded-lg shadow hover:bg-red-700 active:scale-95 transition-all flex items-center gap-2">
                            {{ $current_view === 'create' ? 'Conceder Privilegios' : 'Actualizar Privilegios' }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

    {{-- MODAL CONFIRMAR ELIMINACIÓN --}}
    @if($show_delete_confirm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
             x-data x-init="document.body.classList.add('overflow-hidden')"
             @click.self="$wire.cancel_delete()">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full overflow-hidden border border-gray-100">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">¿Revocar Acceso?</h3>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">Acción Crítica</p>
                    <p class="text-sm text-gray-500 mt-4 leading-relaxed">
                        Estás a punto de eliminar el rol de correspondencia para este colaborador.
                        <span class="font-semibold text-slate-700">Sus roles del sistema permanecerán intactos.</span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 p-4 bg-gray-50 border-t border-gray-100">
                    <button wire:click="cancel_delete"
                        class="px-4 py-2.5 rounded-lg font-bold text-sm text-gray-500 hover:bg-gray-200 transition">
                        Volver
                    </button>
                    <button wire:click="delete_user"
                        class="px-4 py-2.5 bg-red-600 rounded-lg font-bold text-sm text-white hover:bg-red-700 shadow-sm active:scale-95 transition-all">
                        Revocar Ahora
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
