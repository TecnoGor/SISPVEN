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
            @can('Asignar Comunicados')
                <a href="{{ route('correspondencia.asignaciones-emisor') }}"
                    class="px-3 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Asignar Instrucción
                </a>
            @endcan

            @can('Ver Reportes Correspondencia')
                <a href="{{ route('correspondencia.reportes') }}"
                    class="px-3 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Reportes
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
            <div wire:key="dashboard-view-director">
                @php
                    $indicaciones = $dashboard_stats['rastreo_indicaciones'] ?? [];
                    $seguimiento = $dashboard_stats['seguimiento'] ?? [];
                    $sr_total = $dashboard_stats['sin_remitir_total'] ?? 0;
                    $sr_menos24 = $dashboard_stats['sin_remitir_menos_24h'] ?? 0;
                    $sr_24_48 = $dashboard_stats['sin_remitir_24_48h'] ?? 0;
                    $sr_mas48 = $dashboard_stats['sin_remitir_mas_48h'] ?? 0;
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    {{-- Comunicados sin Remitir (Torta) --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Comunicados sin Remitir
                            @if($sr_total > 0)
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold rounded-full bg-orange-100 text-orange-700">{{ $sr_total }}</span>
                            @endif
                        </h3>
                        @if($sr_total > 0)
                            <div x-data x-init="setTimeout(() => gc_render_director_chart(), 50)" x-on:chart-refresh.window="gc_render_director_chart()" class="flex flex-col items-center">
                                <div wire:ignore class="relative w-full flex justify-center" style="max-height: 220px;">
                                    <canvas id="chartDirectorSinRemitir" width="220" height="220" style="max-width:220px;max-height:220px;"></canvas>
                                </div>
                                <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">&lt; 24h <span class="font-bold text-gray-800">({{ $sr_menos24 }})</span></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">24-48h <span class="font-bold text-gray-800">({{ $sr_24_48 }})</span></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">&gt; 48h <span class="font-bold text-gray-800">({{ $sr_mas48 }})</span></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-3">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-green-700">Todo remitido</p>
                                <p class="text-xs text-gray-400 mt-1">No tiene comunicados pendientes de remitir.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Rastreo de Asignaciones (Indicaciones) --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Rastreo de Asignaciones
                            @if(count($indicaciones) > 0)
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-700">{{ count($indicaciones) }}</span>
                            @endif
                        </h3>
                        @if(count($indicaciones) > 0)
                            <div class="space-y-2 overflow-y-auto max-h-[320px]">
                                @foreach($indicaciones as $ind)
                                    <div wire:click="open_detail_sent('{{ $ind['id'] }}')" class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer transition border border-transparent hover:border-blue-200">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-sm text-slate-800 truncate">{{ $ind['asunto'] }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[10px] font-mono text-gray-400">{{ $ind['id'] }}</span>
                                                    <span class="text-[10px] text-gray-400">
                                                        Para: <strong>{{ $ind['destinatario'] }}</strong>
                                                        @if($ind['rol_destino'])
                                                            <span class="text-gray-300">|</span> {{ $ind['rol_destino'] }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="flex-shrink-0 px-2 py-0.5 text-[10px] font-bold rounded-full border
                                                {{ $ind['estatus_id'] == 6 ? 'bg-teal-100 text-teal-800 border-teal-200' : ($ind['estatus_id'] == 5 ? 'bg-purple-100 text-purple-800 border-purple-200' : ($ind['estatus_id'] == 4 ? 'bg-orange-100 text-orange-800 border-orange-200' : ($ind['estatus_id'] == 3 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($ind['estatus_id'] == 7 ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : ($ind['estatus_id'] == 8 ? 'bg-gray-100 text-gray-700 border-gray-200' : 'bg-red-100 text-red-800 border-red-200'))))) }}">
                                                {{ $ind['estatus'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 mb-3">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-gray-500">Sin indicaciones enviadas</p>
                                <p class="text-xs text-gray-400 mt-1">No ha asignado indicaciones recientemente.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Seguimiento de Comunicados --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Seguimiento de Comunicados
                        @if(count($seguimiento) > 0)
                            <span class="ml-1 px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">{{ count($seguimiento) }}</span>
                        @endif
                    </h3>
                    @if(count($seguimiento) > 0)
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Comunicado</th>
                                        <th class="px-4 py-3">Ubicación actual</th>
                                        <th class="px-4 py-3 text-center">Estatus</th>
                                        <th class="px-4 py-3 text-center w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($seguimiento as $seg)
                                        <tr class="hover:bg-indigo-50 transition group">
                                            <td wire:click="open_detail_sent('{{ $seg['id'] }}')" class="px-4 py-3 cursor-pointer">
                                                <p class="font-semibold text-slate-800 truncate max-w-[250px]">{{ $seg['asunto'] }}</p>
                                                <span class="text-[10px] font-mono text-gray-400">{{ $seg['id'] }}</span>
                                            </td>
                                            <td wire:click="open_detail_sent('{{ $seg['id'] }}')" class="px-4 py-3 cursor-pointer">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                                        {{ $seg['nivel'] === 'Mi bandeja' ? 'bg-emerald-100 text-emerald-800' : ($seg['nivel'] === 'Presidencia' ? 'bg-yellow-100 text-yellow-800' : ($seg['nivel'] === 'Director' ? 'bg-purple-100 text-purple-800' : ($seg['nivel'] === 'Gerente' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700'))) }}">
                                                        {{ $seg['nivel'] }}
                                                    </span>
                                                    @if($seg['quien'] !== 'Yo')
                                                        <span class="text-xs text-gray-500">{{ $seg['quien'] }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td wire:click="open_detail_sent('{{ $seg['id'] }}')" class="px-4 py-3 text-center cursor-pointer">
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border
                                                    {{ $seg['estatus_id'] == 6 ? 'bg-teal-100 text-teal-800 border-teal-200' : ($seg['estatus_id'] == 5 ? 'bg-purple-100 text-purple-800 border-purple-200' : ($seg['estatus_id'] == 4 ? 'bg-orange-100 text-orange-800 border-orange-200' : ($seg['estatus_id'] == 3 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($seg['estatus_id'] == 7 ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : ($seg['estatus_id'] == 8 ? 'bg-gray-100 text-gray-700 border-gray-200' : 'bg-red-100 text-red-800 border-red-200'))))) }}">
                                                    {{ $seg['estatus'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button wire:click.stop="toggle_seguimiento({{ $seg['db_id'] }})"
                                                    class="text-gray-400 hover:text-red-500 transition" title="Quitar del seguimiento">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <p class="font-semibold text-sm">Sin comunicados en seguimiento</p>
                            <p class="text-xs mt-1">Marque comunicados enviados desde la Actividad Reciente para darles seguimiento.</p>
                        </div>
                    @endif
                </div>

                @include('livewire.gestion-correspondencia.partials.actividad-reciente')
            </div>

        @elseif($current_view === 'inbox')
            <div wire:key="inbox-view-director">
                @include('livewire.gestion-correspondencia.partials.inbox')
            </div>

        @elseif($current_view === 'create')
            <div wire:key="create-view-director">
                @include('livewire.gestion-correspondencia.partials.create')
            </div>

        @elseif($current_view === 'detail' && $active_doc)
            <div wire:key="detail-view-director-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail')
            </div>

        @elseif($current_view === 'detail_sent' && $active_doc)
            <div wire:key="detail-sent-view-director-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail-sent')
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    var gc_chart_director_sin_remitir = null;

    function gc_render_director_chart() {
        const ctx = document.getElementById('chartDirectorSinRemitir');
        if (!ctx) return;
        if (gc_chart_director_sin_remitir) gc_chart_director_sin_remitir.destroy();

        const menos24 = {{ $dashboard_stats['sin_remitir_menos_24h'] ?? 0 }};
        const entre24_48 = {{ $dashboard_stats['sin_remitir_24_48h'] ?? 0 }};
        const mas48 = {{ $dashboard_stats['sin_remitir_mas_48h'] ?? 0 }};

        if (menos24 === 0 && entre24_48 === 0 && mas48 === 0) return;

        gc_chart_director_sin_remitir = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Menos de 24h', '24 - 48h', 'Más de 48h'],
                datasets: [{
                    data: [menos24, entre24_48, mas48],
                    backgroundColor: ['#4ade80', '#fb923c', '#ef4444'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let t = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                return ctx.label + ': ' + ctx.parsed + ' (' + Math.round(ctx.parsed / t * 100) + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', gc_render_director_chart);
    document.addEventListener('livewire:navigated', gc_render_director_chart);
</script>
@endpush

