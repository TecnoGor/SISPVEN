<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Flota Vehicular</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; margin: 30px; }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #bdbdbd; padding: 6px 4px; text-align: left; }
        th { background-color: #e3eafc; color: #1a237e; font-size: 13px; }
        tr:nth-child(even) { background-color: #f5f7fa; }
        .footer {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            text-align: right;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #bdbdbd;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo Ipostel">
        <div class="title">Reporte de Flota Vehicular</div>
        <div class="subtitle">
            @if($usuario->oficina && $usuario->oficina->nombre)
                Oficina del Usuario: <strong>{{ $usuario->oficina->nombre }}</strong>
            @else
                Usuario: <strong>{{ $usuario->name }}</strong>
            @endif
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th>Oficina</th>
                <th>Póliza</th>
                <th>Vencimiento</th>
                <th>Carga Máx</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vehiculos as $vehiculo)
                <tr>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['placa'] ?? 'N/A') : ($vehiculo->placa ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['marca'] ?? 'N/A') : ($vehiculo->marca ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['modelo'] ?? 'N/A') : ($vehiculo->modelo ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['año'] ?? 'N/A') : ($vehiculo->año ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['color'] ?? 'N/A') : ($vehiculo->color ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? (isset($vehiculo['oficina']['nombre']) ? $vehiculo['oficina']['nombre'] : 'N/A') : (isset($vehiculo->oficina->nombre) ? $vehiculo->oficina->nombre : 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['num_poliza'] ?? 'N/A') : ($vehiculo->num_poliza ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['fecha_vencimiento'] ?? 'N/A') : ($vehiculo->fecha_vencimiento ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['capacidad_carga'] ?? 'N/A') : ($vehiculo->capacidad_carga ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? (isset($vehiculo['Activo']) && $vehiculo['Activo'] ? 'Activo' : 'Inactivo') : (isset($vehiculo->Activo) && $vehiculo->Activo ? 'Activo' : 'Inactivo') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; color:#888;">No hay vehículos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Generado por: {{ $usuario->name }} | Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
