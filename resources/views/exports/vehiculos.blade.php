<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #bdbdbd;
            padding: 6px 4px;
            text-align: left;
        }
        th {
            background-color: #e3eafc;
            color: #1a237e;
            font-size: 13px;
        }
        tr:nth-child(even) { background-color: #f5f7fa; }
        h2 {
            text-align: center;
            color: #1a237e;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #bdbdbd;
            margin-top: 10px;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <h2>Reporte de control de flota de la oficina: {{ $oficinaNombre }}</h2>
    <table>
        <thead>
            <tr>
                <th>Vehículo</th>
                <th>Placa</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Oficina</th>
                <th>Número de Póliza</th>
                <th>Vencimiento de Póliza</th>
                <th>Carga Máxima Kg</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vehiculos as $vehiculo)
                <tr>
                    <td>
                        @if(is_array($vehiculo) ? ($vehiculo['imagen'] ?? null) : ($vehiculo->imagen ?? null))
                            Imagen asignada
                        @else
                            Sin imagen
                        @endif
                    </td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['placa'] ?? 'N/A') : ($vehiculo->placa ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['color'] ?? 'N/A') : ($vehiculo->color ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['marca'] ?? 'N/A') : ($vehiculo->marca ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['modelo'] ?? 'N/A') : ($vehiculo->modelo ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['año'] ?? 'N/A') : ($vehiculo->año ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? (isset($vehiculo['oficina']['nombre']) ? $vehiculo['oficina']['nombre'] : 'Sin oficina') : (isset($vehiculo->oficina->nombre) ? $vehiculo->oficina->nombre : 'Sin oficina') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['num_poliza'] ?? 'N/A') : ($vehiculo->num_poliza ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['fecha_vencimiento'] ?? 'N/A') : ($vehiculo->fecha_vencimiento ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? ($vehiculo['capacidad_carga'] ?? 'N/A') : ($vehiculo->capacidad_carga ?? 'N/A') }}</td>
                    <td>{{ is_array($vehiculo) ? (isset($vehiculo['Activo']) && $vehiculo['Activo'] ? 'Activo' : 'Inactivo') : (isset($vehiculo->Activo) && $vehiculo->Activo ? 'Activo' : 'Inactivo') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center; color:#888;">No hay vehículos para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Generado por: {{ Auth::user()->name }} | Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
