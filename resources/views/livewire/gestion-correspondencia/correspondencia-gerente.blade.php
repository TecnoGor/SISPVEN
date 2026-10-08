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
            <div wire:key="dashboard-view-gerente">
                @php
                    $urgentes = $dashboard_stats['urgentes'] ?? [];
                    $rastreo = $dashboard_stats['rastreo_remisiones'] ?? [];
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
                            <div x-data x-init="setTimeout(() => gc_render_gerente_chart(), 50)" x-on:chart-refresh.window="gc_render_gerente_chart()" class="flex flex-col items-center">
                                <div wire:ignore class="relative w-full flex justify-center" style="max-height: 220px;">
                                    <canvas id="chartGerenteSinRemitir" width="220" height="220" style="max-width:220px;max-height:220px;"></canvas>
                                </div>
                                <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">Menos de 24h <span class="font-bold text-gray-800">({{ $sr_menos24 }})</span></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">24 - 48h <span class="font-bold text-gray-800">({{ $sr_24_48 }})</span></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                                        <span class="text-gray-600 font-semibold">Más de 48h <span class="font-bold text-gray-800">({{ $sr_mas48 }})</span></span>
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

                    {{-- Urgentes del Día --}}
                    <div class="bg-white rounded-xl border {{ count($urgentes) > 0 ? 'border-red-200' : 'border-gray-200' }} shadow-sm p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500 {{ count($urgentes) > 0 ? 'animate-pulse' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Urgentes del Día
                            @if(count($urgentes) > 0)
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">{{ count($urgentes) }}</span>
                            @endif
                        </h3>
                        @if(count($urgentes) > 0)
                            <div class="space-y-2 overflow-y-auto max-h-[280px]">
                                @foreach($urgentes as $urg)
                                    <div wire:click="open_detail('{{ $urg['id'] }}')" class="flex items-center justify-between p-3 bg-red-50 rounded-lg hover:bg-red-100 cursor-pointer transition border border-red-100">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-slate-800 truncate">{{ $urg['subject'] }}</p>
                                            <span class="text-[10px] font-mono text-gray-400">{{ $urg['id'] }} — {{ $urg['sender'] ?? '' }}</span>
                                        </div>
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $urg['priority'] === 'Urgente' ? 'bg-red-200 text-red-800' : 'bg-orange-200 text-orange-800' }}">
                                            {{ $urg['priority'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-3">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="font-semibold text-sm text-green-700">Sin urgencias</p>
                                <p class="text-xs text-gray-400 mt-1">No hay comunicados urgentes pendientes.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Seguimiento de Remisiones --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Seguimiento de Remisiones
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
                                                        {{ $env['nivel'] === 'Mi bandeja' ? 'bg-emerald-100 text-emerald-800' : ($env['nivel'] === 'Presidencia' ? 'bg-yellow-100 text-yellow-800' : ($env['nivel'] === 'Director' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700')) }}">
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
                            <p class="font-semibold text-sm">No ha remitido comunicados aún.</p>
                        </div>
                    @endif
                </div>

                @include('livewire.gestion-correspondencia.partials.actividad-reciente')
            </div>
        @elseif($current_view === 'inbox')
            <div wire:key="inbox-view-gerente">
                @include('livewire.gestion-correspondencia.partials.inbox')
            </div>
        @elseif($current_view === 'create')
            <div wire:key="create-view-gerente">
                @include('livewire.gestion-correspondencia.partials.create')
            </div>
        @elseif($current_view === 'detail' && $active_doc)
            <div wire:key="detail-view-gerente-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail')
            </div>
        @elseif($current_view === 'detail_sent' && $active_doc)
            <div wire:key="detail-sent-view-gerente-{{ $active_doc['id'] }}">
                @include('livewire.gestion-correspondencia.partials.detail-sent')
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    var gc_chart_gerente_sin_remitir = null;

    function gc_render_gerente_chart() {
        const ctx = document.getElementById('chartGerenteSinRemitir');
        if (!ctx) return;
        if (gc_chart_gerente_sin_remitir) gc_chart_gerente_sin_remitir.destroy();

        const menos24 = {{ $dashboard_stats['sin_remitir_menos_24h'] ?? 0 }};
        const entre24_48 = {{ $dashboard_stats['sin_remitir_24_48h'] ?? 0 }};
        const mas48 = {{ $dashboard_stats['sin_remitir_mas_48h'] ?? 0 }};

        if (menos24 === 0 && entre24_48 === 0 && mas48 === 0) return;

        gc_chart_gerente_sin_remitir = new Chart(ctx, {
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

    document.addEventListener('DOMContentLoaded', gc_render_gerente_chart);
    document.addEventListener('livewire:navigated', gc_render_gerente_chart);
</script>
@endpush

