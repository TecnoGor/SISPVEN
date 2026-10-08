<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte de Viajes Nacional</title>
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
        h1 {
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
        .sub-table {
            margin-top: 5px;
        }
        .puntos-title {
            margin-top: 10px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 11px;
            color: #fff;
            border-radius: 4px;
        }
        .activo {
            background-color: #28a745;
        }
        .inactivo {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo">
        <div class="info">
            <p><strong>Oficina:</strong> {{ $nombreOficinaUsuario }}</p>
            @if ($semana)
                @php
                    [$inicio, $fin] = explode(' - ', \Carbon\Carbon::parse($semana)->startOfWeek()->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($semana)->endOfWeek()->format('d/m/Y'));
                @endphp
                <p><strong>Semana:</strong> {{ $inicio }} al {{ $fin }}</p>
            @else
                <p><strong>Semana:</strong> {{ \Carbon\Carbon::now()->startOfWeek()->format('d/m/Y') }} al {{ \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <h2>Reporte de Viajes Nacionales</h2>

    <table>
        <thead>
            <tr>
                <th>Código de Viaje</th>
                <th>Vehículo</th>
                <th>Fecha de Salida</th>
                <th>Ruta</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($viajes as $viaje)
                <tr>
                    <td>{{ $viaje->codigo }}</td>
                    <td>{{ $viaje->vehiculo->placa }}</td>
                    <td>{{ \Carbon\Carbon::parse($viaje->fecha_salida)->format('d/m/Y') }}</td>
                    <td>{{ $viaje->ruta->ruta }}</td>
                    <td>
                        <span class="badge {{ $viaje->activo ? 'activo' : 'inactivo' }}">
                            {{ $viaje->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
