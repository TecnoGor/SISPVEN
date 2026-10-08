{{-- filepath: resources/views/pdf/telegramas.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Telegramas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .cintillo { width: 100%; margin-bottom: 10px; }
        .titulo { font-size: 20px; font-weight: bold; color: #002F6C; text-align: center; margin-bottom: 4px; }
        .subtitulo { font-size: 13px; color: #444; text-align: center; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #222; padding: 6px; text-align: center; }
        th { background: #002F6C; color: #fff; font-size: 13px; }
        tr:nth-child(even) { background: #f7f7f7; }
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: right; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    {{-- Cintillo institucional --}}
    <img src="{{ public_path('images/cintillo.jpg') }}" class="cintillo">

    {{-- Título y subtítulo --}}
    <div class="titulo">REPORTE DE TELEGRAMAS {{ $seccion == 1 ? 'RECIBIDOS' : 'EMITIDOS' }}</div>
    <div class="subtitulo">
        Usuario: {{ auth()->user()->name ?? auth()->user()->email ?? '---' }}<br>
        Oficina: {{ auth()->user()->oficina->nombre ?? '---' }}<br>
        
        Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Oficina Origen</th>
                <th>Remitente</th>
                <th>Oficina Destino</th>
                <th>Destinatario</th>
                <th>GIT</th>
                @if($seccion == 2)
                    <th>Aprobación</th>
                @elseif($seccion == 1)
                    <th>Estatus</th>
                    <th>Cant. Avisos</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($telegramas as $telegrama)
                <tr>
                    <td>{{ $telegrama->oficinas->nombre }}</td>
                    <td>{{ $telegrama->nombre_rem . ' ' . $telegrama->apellido_rem }}</td>
                    <td>{{ $telegrama->oficinas_destino->nombre }}</td>
                    <td>{{ $telegrama->nombre_dest . ' ' . $telegrama->apellido_dest }}</td>
                    <td>{{ $telegrama->codigo_envio }}</td>
                    @if($seccion == 2)
                        <td>
                            {{ optional($telegrama->telegrama_recibido->first())->recibido ? 'Recibido' : 'En espera' }}
                        </td>
                    @elseif($seccion == 1)
                        <td>
                            {{ optional($telegrama->telegrama_recibido->first())->recibido ? 'Aprobado' : 'Sin Aprobar' }}
                        </td>
                        <td>
                            {{ optional($telegrama->avisos_telegrama)->count() ?? 0 }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por Ipostel - {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>