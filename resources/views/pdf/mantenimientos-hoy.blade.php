<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimientos - {{ now()->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            margin: 30px;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header img {
            width: 120px;
            height: auto;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            text-align: left;
        }

        ul {
            padding-left: 16px;
            margin: 0;
        }

        ul li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo Ipostel">
    </div>

    <h1>Reporte de Mantenimientos del {{ \Carbon\Carbon::now()->format('d/m/Y') }}</h1>

    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Oficina</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Servicios Aplicados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mantenimientos as $mantenimiento)
                @php
                    $vehiculo = $vehiculos->firstWhere('vehiculo_id', $mantenimiento->vehiculo_id);
                @endphp
                <tr>
                    <td>{{ $vehiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $vehiculo->marca ?? 'N/A' }}</td>
                    <td>{{ $vehiculo->modelo ?? 'N/A' }}</td>
                    <td>{{ $vehiculo->oficina->nombre ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($mantenimiento->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $mantenimiento->descripcion }}</td>
                    <td>
                        <ul>
                            @foreach($mantenimiento->detalles as $detalle)
                                <li>{{ $detalle->servicioFlota->nombre ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
