<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                @if($current_view == 'dashboard') Mi Dashboard de Correspondencia
                @elseif($current_view == 'inbox') Bandeja de Entrada
                @elseif($current_view == 'create') Redacción de Comunicados
                @elseif($current_view == 'detail') Revisión de Comunicado
                @elseif($current_view == 'detail_sent') Comunicados Enviados
                @endif
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Rol Activo: <span class="font-bold text-red-600">{{ $current_role }}</span>
            </p>
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button wire:click="open_create" class="px-4 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Crear Comunicación Nueva
            </button>
            @can('Atender Asignaciones')
                <a href="{{ route('correspondencia.asignaciones-analista') }}"
                    class="px-3 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Mis Asignaciones
                </a>
            @endcan

            <button wire:click="open_inbox" class="relative p-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm group">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                @if($notifications_count > 0)
                    <div class="absolute -top-2 -right-2 bg-primary text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                        {{ $notifications_count }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <div class="min-h-[400px]">

        @if($current_view === 'dashboard')
            <div wire:key="dashboard-view-analista">
                @php
                    $devueltos = $dashboard_stats['devueltos'] ?? [];
                    $rastreo = $dashboard_stats['rastreo_envios'] ?? [];
                    $hoy = $dashboard_stats['correlativos_hoy'] ?? 0;
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

                    {{-- Columna Izquierda: Comunicados Creados Hoy --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center justify-center">
                        <div class="w-full mb-4">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Mi Actividad Hoy
                            </h3>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center py-4">
                            <div class="inline-flex items-center justify-center w-36 h-36 rounded-full bg-gradient-to-br from-red-500 to-red-700 shadow-xl mb-5">
                                <span class="text-7xl font-extrabold text-white">{{ $hoy }}</span>
                            </div>
                            <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Comunicados Creados</p>
                            <p class="text-xs text-gray-400 mt-1">{{ now()->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Columna Derecha: Devueltos para Corregir --}}
                    <div class="lg:col-span-3 bg-white rounded-xl border {{ count($devueltos) > 0 ? 'border-red-200' : 'border-gray-200' }} shadow-sm flex flex-col">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                Devueltos / Corregir
                            </h3>
                            @if(count($devueltos) > 0)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">{{ count($devueltos) }}</span>
                            @endif
                        </div>
                        @if(count($devueltos) > 0)
                            <div class="divide-y divide-gray-100 overflow-y-auto max-h-[400px]">
                                @foreach($devueltos as $dev)
                                    @php
                                        $historial = $dev['trazabilidad'] ?? [];
                                        $ultimo = collect($historial)->last();
                                        $motivo = $ultimo['observacion'] ?? null;
                                    @endphp
                                    <div wire:click="open_detail('{{ $dev['id'] }}')" class="flex items-start gap-3 px-5 py-4 hover:bg-red-50 cursor-pointer transition group">
                                        {{-- Indicador lateral + ícono --}}
                                        <div class="flex-shrink-0 flex flex-col items-center gap-1 pt-0.5">
                                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <span class="text-[9px] font-bold text-red-500 uppercase">Devuelto</span>
                                        </div>
                                        {{-- Contenido --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-red-700 transition truncate">{{ $dev['subject'] }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono text-gray-400">{{ $dev['id'] }}</span>
                                                <span class="text-[10px] text-gray-300">|</span>
                                                <span class="text-[10px] font-semibold text-gray-400">{{ $dev['type'] }}</span>
                                            </div>
                                            @if($motivo)
                                                <p class="text-[11px] text-red-600 mt-1.5 italic leading-snug">
                                                    <span class="font-bold not-italic">Motivo:</span> {{ Str::limit($motivo, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                        {{-- Botón --}}
                                        <div class="flex-shrink-0 self-center">
                                            <div class="w-8 h-8 rounded-full bg-primary group-hover:bg-blue-800 flex items-center justify-center transition shadow-sm">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center py-10">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-3">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-green-700">Sin correcciones pendientes</p>
                                <p class="text-xs text-gray-400 mt-1">Todos sus comunicados están en orden.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Rastreador de Mis Envíos --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Rastreador de Mis Comunicados
                    </h3>
                    @if(count($rastreo) > 0)
                        <div class="overflow-auto max-h-64 rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Comunicado</th>
                                        <th class="px-4 py-3">Ubicación actual</th>
                                        <th class="px-4 py-3 text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($rastreo as $env)
                                        <tr wire:click="open_detail_sent('{{ $env['id'] }}')" class="hover:bg-blue-50 cursor-pointer transition">
                                            <td class="px-4 py-3">
                                                <p class="font-semibold text-slate-800 truncate max-w-[250px]">{{ $env['asunto'] }}</p>
                                                <span class="text-[10px] font-mono text-gray-400">{{ $env['id'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                                        {{ $env['nivel'] === 'Mi bandeja' ? 'bg-emerald-100 text-emerald-800' : ($env['nivel'] === 'Presidencia' ? 'bg-yellow-100 text-yellow-800' : ($env['nivel'] === 'Director' ? 'bg-purple-100 text-purple-800' : ($env['nivel'] === 'Gerente' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700'))) }}">
                                                        {{ $env['nivel'] }}
                                                    </span>
                                                    @if($env['quien'] !== 'Yo')
                                                        <span class="text-xs text-gray-500">{{ $env['quien'] }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border
                                                    {{ $env['estatus_id'] == 6 ? 'bg-teal-100 text-teal-800 border-teal-200' : ($env['estatus_id'] == 5 ? 'bg-purple-100 text-purple-800 border-purple-200' : ($env['estatus_id'] == 7 ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : ($env['estatus_id'] == 8 ? 'bg-gray-100 text-gray-700 border-gray-200' : 'bg-red-100 text-red-800 border-red-200'))) }}">
                                                    {{ $env['estatus'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400">
                            <p class="font-semibold text-sm">No ha enviado comunicados aún.</p>
                        </div>
                    @endif
                </div>

                @include('livewire.gestion-correspondencia.partials.actividad-reciente')
            </div>

        @elseif($current_view === 'inbox')
            @include('livewire.gestion-correspondencia.partials.inbox')

        @elseif($current_view === 'create')
            @include('livewire.gestion-correspondencia.partials.create')

        @elseif($current_view === 'detail' && $active_doc)
            @include('livewire.gestion-correspondencia.partials.detail')

        @elseif($current_view === 'detail_sent' && $active_doc)
            @include('livewire.gestion-correspondencia.partials.detail-sent')
        @endif

    </div>
</div>

