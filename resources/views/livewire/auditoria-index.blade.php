<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Auditoría de Administración
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Historial completo de acciones realizadas por los administradores del módulo
            </p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="{{ route('correspondencia.admin') }}"
                class="px-4 py-2 text-sm font-bold rounded-lg bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-slate-700 shadow-sm transition-all flex items-center gap-2 group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Regresar
            </a>
        </div>
    </div>

    <div class="min-h-[400px]">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            
            {{-- Toolbar --}}
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col md:flex-row gap-3 justify-between items-center">
                <div class="relative w-full max-w-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        class="w-full pl-9 pr-4 py-2 rounded-lg bg-white border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition text-sm text-slate-700 placeholder:text-gray-400"
                        placeholder="Buscar por acción, descripción o administrador...">
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3">Fecha / Hora</th>
                            <th class="px-5 py-3">Administrador</th>
                            <th class="px-5 py-3">Acción</th>
                            <th class="px-5 py-3">Descripción</th>
                            <th class="px-5 py-3 text-center">IP / Origen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition {{ $loop->even ? 'bg-gray-50/40' : 'bg-white' }}">
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-700">{{ $log->created_at->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $log->created_at->format('h:i:s A') }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-slate-500 font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 capitalize leading-none">{{ strtolower($log->user->name ?? 'Sistema') }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-tight">{{ $log->user->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $badgeClasses = 'bg-gray-100 text-gray-700 border-gray-200';
                                        if (str_contains($log->accion, 'CREAR')) $badgeClasses = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                        elseif (str_contains($log->accion, 'EDITAR')) $badgeClasses = 'bg-blue-50 text-blue-700 border-blue-100';
                                        elseif (str_contains($log->accion, 'DESACTIVAR')) $badgeClasses = 'bg-red-50 text-red-700 border-red-100';
                                        elseif (str_contains($log->accion, 'CAMBIO_ROL')) $badgeClasses = 'bg-violet-50 text-violet-700 border-violet-100';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeClasses }} uppercase tracking-wider">
                                        {{ str_replace('_', ' ', $log->accion) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-slate-600 text-xs leading-relaxed max-w-md">
                                        {{ $log->descripcion }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="font-mono text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                        {{ $log->ip_address }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 mb-3">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-slate-600">No se encontraron registros de auditoría</p>
                                    <p class="text-gray-400 text-sm mt-1">Intenta ajustar tu búsqueda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer con paginación --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-400">
                        Mostrando registros de actividad administrativa
                    </p>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
