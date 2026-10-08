<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Rutas Nacionales</title>
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
    </div>

    <h2>Reporte de Rutas Nacionales</h2>
    @if($nombreOficinaUsuario)
        <p><strong>Oficina del Usuario:</strong> {{ $nombreOficinaUsuario }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ruta</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Activo</th>
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
                    <td>{{ $ruta->activo ? 'Sí' : 'No' }}</td>
                    <td>
                        @foreach($ruta->puntosEntrega as $punto)
                            • {{ $punto->oficina->nombre ?? 'N/A' }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
