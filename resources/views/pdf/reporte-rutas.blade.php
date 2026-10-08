<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Rutas</title>
    <style>

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 120px;
            height: auto;
        }
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo">
        <div class="info">

            <h2>Reporte de Rutas Locales</h2>
            @if($nombreOficinaUsuario)
                <p><strong>Oficina del Usuario:</strong> {{ $nombreOficinaUsuario }}</p>
            @endif
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ruta</th>
                <th>Oficina Origen</th>
                <th>Oficina Destino</th>
                <th>Estado</th>
                <th>Puntos de Entrega</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rutas as $ruta)
                <tr>
                    <td>{{ $ruta->ruta_id }}</td>
                    <td>{{ $ruta->ruta }}</td>
                    <td>{{ $ruta->oficinaOrigen->nombre ?? 'N/A' }}</td>
                    <td>{{ $ruta->oficinaDestino->nombre ?? 'N/A' }}</td>
                    <td>
                        @if ($ruta->activo)
                            <span class="badge activo">Activo</span>
                        @else
                            <span class="badge inactivo">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        @if ($ruta->puntosEntrega && count($ruta->puntosEntrega))
                            <table class="sub-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Oficina</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ruta->puntosEntrega as $index => $punto)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $punto->oficina->nombre ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <em>Sin puntos</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
