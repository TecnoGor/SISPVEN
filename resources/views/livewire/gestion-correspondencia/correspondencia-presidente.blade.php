<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                @if ($current_view == 'dashboard')
                    Mi Dashboard de Correspondencia
                @elseif($current_view == 'inbox')
                    Bandeja de Entrada
                @elseif($current_view == 'create')
                    Redacción de Comunicados
                @elseif($current_view == 'detail')
                    Revisión de Comunicado
                @elseif($current_view == 'detail_sent')
                    Comunicados Enviados
                @endif
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Rol Activo: <span class="font-bold text-red-600">{{ $current_role }}</span>
            </p>
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button wire:click="open_create"
                class="px-4 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
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

            @can('Ver Auditoria Correspondencia')
                <a href="{{ route('correspondencia.auditoria') }}"
                    class="px-3 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Auditoría
                </a>
            @endcan

            <button wire:click="open_inbox"
                class="relative p-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm group">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-red-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                    </path>
                </svg>
                @if ($notifications_count > 0)
                    <div
                        class="absolute -top-2 -right-2 bg-primary text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                        {{ $notifications_count }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <div class="min-h-[400px]">

        @if ($current_view === 'dashboard')
            <div wire:key="dashboard-view-presidente">

                @php
                    $hoy = now()->format('d/m/Y');
                    $para_firma = collect($comunicaciones)->filter(fn($c) => in_array($c['estatus_id'] ?? 0, [1, 5]) && ($c['date'] ?? '') === $hoy)->values();
                    $resoluciones = collect($comunicaciones)->filter(fn($c) => in_array($c['estatus_id'] ?? 0, [6, 8]) && ($c['date'] ?? '') === $hoy)->sortByDesc('date')->values();
                    $comunicaciones_hoy = collect($comunicaciones)->filter(fn($c) => ($c['date'] ?? '') === $hoy);
                    $dist_tipos = $comunicaciones_hoy->groupBy('type')->map(fn($g) => $g->count())->toArray();
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    {{-- Para Firma / Aprobación --}}
                    <div class="bg-white rounded-xl border {{ $para_firma->count() > 0 ? 'border-green-200' : 'border-gray-200' }} shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Para Firma / Aprobación — Hoy
                            @if($para_firma->count() > 0)
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-700">{{ $para_firma->count() }}</span>
                            @endif
                        </h3>
                        @if($para_firma->count() > 0)
                            <div class="space-y-2 overflow-y-auto max-h-[320px]">
                                @foreach($para_firma as $doc)
                                    <div wire:click="open_detail('{{ $doc['id'] }}')"
                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-green-50 cursor-pointer transition border border-transparent hover:border-green-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-slate-800 truncate">{{ $doc['subject'] }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono text-gray-400">{{ $doc['id'] }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $doc['type'] }} — {{ $doc['sender'] }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if(in_array($doc['priority'] ?? '', ['Urgente', 'Alta']))
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $doc['priority'] === 'Urgente' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">{{ $doc['priority'] }}</span>
                                            @endif
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-green-700">Sin comunicados pendientes de firma</p>
                                <p class="text-xs text-gray-400 mt-1">Todos los comunicados han sido procesados.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Resoluciones Recientes --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Resoluciones de Hoy
                        </h3>
                        @if($resoluciones->count() > 0)
                            <div class="space-y-2 overflow-y-auto max-h-[320px]">
                                @foreach($resoluciones as $doc)
                                    <div wire:click="open_detail('{{ $doc['id'] }}')" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-slate-800 truncate">{{ $doc['subject'] }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono text-gray-400">{{ $doc['id'] }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $doc['date'] }}</span>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border
                                            {{ ($doc['estatus_id'] ?? 0) == 6 ? 'bg-teal-100 text-teal-800 border-teal-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                            {{ ($doc['estatus_id'] ?? 0) == 6 ? 'Aprobado' : 'Rechazado' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 text-gray-400">
                                <p class="font-semibold text-sm">Sin resoluciones recientes.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Tipos de Comunicados Recibidos (Gráfica Pastel) --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Tipos de Comunicados Recibidos Hoy
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ array_sum($dist_tipos) }} hoy)</span>
                    </h3>
                    @if(count($dist_tipos) > 0)
                        <div x-data x-init="setTimeout(() => gc_render_presidente_chart(), 50)" x-on:chart-refresh.window="gc_render_presidente_chart()" class="flex flex-col items-center">
                            <div wire:ignore class="relative w-full flex justify-center" style="max-height: 260px;">
                                <canvas id="chartPresidenteTipos" width="260" height="260" style="max-width:260px;max-height:260px;"></canvas>
                            </div>
                            <div class="flex flex-wrap items-center justify-center gap-3 mt-4 text-xs">
                                @foreach($dist_tipos as $tipo => $cantidad)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ ['#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#6366f1','#f43f5e'][($loop->index) % 10] }}"></span>
                                        <span class="text-gray-600 font-semibold">{{ $tipo === 'OTRO' ? 'Indicaciones' : $tipo }} <span class="font-bold text-gray-800">({{ $cantidad }})</span></span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <p class="font-semibold text-sm">Sin comunicados recibidos en este periodo.</p>
                        </div>
                    @endif
                </div>

            {{-- Seguimiento de Correcciones Devueltas --}}
            @php $correcciones = $dashboard_stats['seguimiento_correcciones'] ?? []; @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Seguimiento de Correcciones
                    @if (count($correcciones) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">{{ count($correcciones) }}</span>
                    @endif
                </h3>
                @if (count($correcciones) > 0)
                    <div class="space-y-3">
                        @foreach ($correcciones as $corr)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-yellow-300 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-mono font-bold text-gray-400">{{ $corr['codigo'] }}</span>
                                            <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $corr['tipo'] }}</span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $corr['asunto'] }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                Devuelto a: <strong class="text-slate-700">{{ $corr['devuelto_a'] }}</strong>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $corr['fecha_devolucion'] }}
                                            </span>
                                        </div>
                                        @if (!empty($corr['observacion']))
                                            <div class="mt-2 pl-3 border-l-2 border-yellow-300">
                                                <p class="text-[11px] text-yellow-800 bg-yellow-50 px-2 py-1 rounded italic">"{{ $corr['observacion'] }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 flex flex-col items-end gap-1.5">
                                        @if ($corr['estatus_id_actual'] == 7)
                                            {{-- Aún esperando corrección --}}
                                            <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 uppercase tracking-wider">
                                                Esperando corrección
                                            </span>
                                        @elseif ($corr['estatus_id_actual'] == 5)
                                            {{-- Ya fue corregido y remitido --}}
                                            <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200 uppercase tracking-wider">
                                                Remitido
                                            </span>
                                        @elseif ($corr['estatus_id_actual'] == 1)
                                            {{-- Remitido pero pendiente de lectura --}}
                                            <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-800 border border-red-200 uppercase tracking-wider">
                                                Pendiente
                                            </span>
                                        @elseif ($corr['estatus_id_actual'] == 6)
                                            {{-- Ya aprobado --}}
                                            <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-teal-100 text-teal-800 border border-teal-200 uppercase tracking-wider">
                                                Aprobado / Firmado
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-200 uppercase tracking-wider">
                                                {{ $corr['estado_actual'] }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Con: {{ $corr['quien_tiene'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-semibold text-sm">Sin correcciones pendientes</p>
                        <p class="text-xs mt-1">No ha devuelto comunicados para corrección recientemente.</p>
                    </div>
                @endif
            </div>

            @include('livewire.gestion-correspondencia.partials.actividad-reciente')
            </div>
        @elseif($current_view === 'inbox')
            <div wire:key="inbox-view-presidente">
                @include('livewire.gestion-correspondencia.partials.inbox')
            </div>
        @elseif($current_view === 'create')
            <div wire:key="create-view-presidente">
                @include('livewire.gestion-correspondencia.partials.create')
            </div>
        @elseif($current_view === 'detail' && $active_doc)
            <div wire:key="detail-view-presidente-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail')
            </div>
        @elseif($current_view === 'detail_sent' && $active_doc)
            <div wire:key="detail-sent-view-presidente-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail-sent')
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    var gc_chart_presidente_tipos = null;

    function gc_render_presidente_chart() {
        const ctx = document.getElementById('chartPresidenteTipos');
        if (!ctx) return;
        if (gc_chart_presidente_tipos) gc_chart_presidente_tipos.destroy();

        const dist = @json($dashboard_stats['distribucion_tipos'] ?? []);
        const labels = Object.keys(dist).map(k => k === 'OTRO' ? 'Indicaciones' : k);
        const data = Object.values(dist);

        if (data.length === 0) return;

        const colors = ['#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#6366f1','#f43f5e'];

        gc_chart_presidente_tipos = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '55%',
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

    document.addEventListener('DOMContentLoaded', gc_render_presidente_chart);
    document.addEventListener('livewire:navigated', gc_render_presidente_chart);
</script>
@endpush

