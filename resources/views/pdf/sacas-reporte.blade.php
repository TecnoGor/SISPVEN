<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Valijas</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
            height: auto;
        }
        .header .info {
            text-align: right;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo">
        <div class="info">
            <p><strong>Oficina:</strong> {{ $usuario->oficina->nombre ?? 'No definida' }}</p>
            @php
                $inicio = \Carbon\Carbon::now()->startOfWeek()->format('d/m/Y');
                $fin = \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y');
            @endphp
            <p><strong>Semana:</strong> {{ $inicio }} al {{ $fin }}</p>
        </div>
    </div>
    
    <h2>
        Reporte de Valijas {{ $sacas->first()?->cerrado ? 'Cerradas' : 'Abiertas' }}
    </h2>

    <table>
        <thead>
            <tr>
                <th>Código de Valija</th>
                <th>Tipo</th>
                <th>Usuario</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sacas as $saca)
                <tr>
                    <td>{{ $saca->codigo_saca }}</td>
                    <td>
                        {{ $saca->tipoSaca?->nombre ?? 'Sin Tipo' }}
                        @if ($saca->tipoSaca?->certificado)
                            @php
                                $tipoAnterior = \App\Models\TipoSaca::find($saca->tipoSaca->tipo_saca_id - 1);
                            @endphp
                            @if ($tipoAnterior)
                                / {{ $tipoAnterior->nombre }}
                            @endif
                        @endif
                    </td>
                    <td>{{ $saca->usuario->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($saca->created_at)->format('d/m/Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
