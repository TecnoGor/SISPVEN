<!-- resources/views/rutas-excel.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Rutas</title>
</head>
<body>
    <h1>Reporte de Rutas</h1>
    <h3>Oficina: {{ $nombreOficinaUsuario }}</h3>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID Ruta</th>
                <th>Ruta</th>
                <th>Oficina Origen</th>
                <th>Oficina Destino</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rutas as $ruta)
                <tr>
                    <td>{{ $ruta->ruta_id }}</td>
                    <td>{{ $ruta->ruta }}</td>
                    <td>{{ $ruta->oficinaOrigen->nombre }}</td>
                    <td>{{ $ruta->oficinaDestino->nombre }}</td>
                    <td>{{ $ruta->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $ruta->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
