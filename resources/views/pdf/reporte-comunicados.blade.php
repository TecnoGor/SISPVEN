<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Comunicados</title>
    <style>
        @page { margin: 1.2cm 1cm; }
        body { font-family: 'Arial', 'Helvetica', sans-serif; font-size: 8pt; color: #000; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 10px; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; margin: 0; }
        .subtitle { font-size: 9pt; text-align: center; color: #555; margin-top: 2px; }
        .meta { font-size: 8pt; margin-top: 8px; }
        .meta-row { margin-bottom: 2px; }
        .meta-label { font-weight: bold; display: inline-block; min-width: 110px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 4px 5px; text-align: left; vertical-align: top; }
        th { background: #e5e7eb; font-size: 7.5pt; text-transform: uppercase; }
        td { font-size: 7.5pt; }
        .codigo { font-family: 'Courier New', monospace; font-weight: bold; white-space: nowrap; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .row-vencido { background: #fee2e2; }
        .row-proximo { background: #fef3c7; }
        .badge { display: inline-block; padding: 1px 4px; border-radius: 3px; font-size: 6.5pt; font-weight: bold; text-transform: uppercase; }
        .b-alta { background: #fee2e2; color: #991b1b; }
        .b-media { background: #fef3c7; color: #92400e; }
        .b-baja { background: #d1fae5; color: #065f46; }
        .b-default { background: #f3f4f6; color: #374151; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 7pt; color: #666; border-top: 1px solid #ccc; padding-top: 4px; }
        .resumen { margin-top: 10px; font-size: 8pt; }
        .resumen-item { display: inline-block; margin-right: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE COMUNICADOS</div>
        <div class="subtitle">IPOSTEL — Sistema de Gestión de Correspondencia</div>
        <div class="meta">
            <div class="meta-row"><span class="meta-label">Período:</span>
                {{ $fecha_inicio ? \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') : 'Inicio' }}
                al
                {{ $fecha_fin ? \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') : 'Hoy' }}
            </div>
            @if($tipo)
                <div class="meta-row"><span class="meta-label">Tipo:</span> {{ $tipo }}</div>
            @endif
            @if($estatus_nombre)
                <div class="meta-row"><span class="meta-label">Estatus:</span> {{ $estatus_nombre }}</div>
            @endif
            @if($search)
                <div class="meta-row"><span class="meta-label">Búsqueda:</span> "{{ $search }}"</div>
            @endif
            <div class="meta-row"><span class="meta-label">Generado por:</span> {{ $generado_por }} — {{ $generado_en }}</div>
            <div class="meta-row"><span class="meta-label">Total de registros:</span> {{ $rows->count() }}</div>
        </div>
    </div>

    @php
        $vencidosCount = $rows->where('vencido', true)->count();
        $proximosCount = $rows->where('proximo_vencer', true)->count();
    @endphp

    @if($vencidosCount > 0 || $proximosCount > 0)
    <div class="resumen">
        <span class="resumen-item"><strong>Vencidos:</strong> {{ $vencidosCount }}</span>
        <span class="resumen-item"><strong>Próximos a vencer (24h):</strong> {{ $proximosCount }}</span>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 9%;">Código</th>
                <th style="width: 8%;">Tipo</th>
                <th style="width: 18%;">Asunto</th>
                <th style="width: 6%;">Prioridad</th>
                <th style="width: 11%;">Creado por</th>
                <th style="width: 11%;">Dest. final</th>
                <th style="width: 11%;">Quien lo tiene ahora</th>
                <th style="width: 9%;">Estatus</th>
                <th style="width: 6%;" class="center">F. Creación</th>
                <th style="width: 6%;" class="center">F. Límite</th>
                <th style="width: 5%;" class="center">Días</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr class="{{ $r->vencido ? 'row-vencido' : ($r->proximo_vencer ? 'row-proximo' : '') }}">
                    <td class="codigo">{{ $r->codigo }}</td>
                    <td>{{ $r->tipo }}</td>
                    <td>{{ $r->asunto }}</td>
                    <td>
                        @php
                            $pClass = match(strtolower($r->prioridad ?? '')) {
                                'alta' => 'b-alta', 'media' => 'b-media', 'baja' => 'b-baja', default => 'b-default',
                            };
                        @endphp
                        <span class="badge {{ $pClass }}">{{ $r->prioridad ?? 'N/A' }}</span>
                    </td>
                    <td>{{ $r->creado_por }}</td>
                    <td>{{ $r->destinatario_final }}</td>
                    <td><strong>{{ $r->tiene_ahora }}</strong></td>
                    <td>{{ $r->estatus }}</td>
                    <td class="center nowrap">{{ $r->fecha_creacion }}</td>
                    <td class="center nowrap">{{ $r->fecha_limite }}</td>
                    <td class="center"><strong>{{ $r->dias_transcurridos }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="11" class="center">Sin registros para los filtros aplicados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado el {{ $generado_en }} — Página <span class="page-number"></span>
    </div>
</body>
</html>
