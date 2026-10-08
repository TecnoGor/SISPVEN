<table>
    <thead>
        <tr>
            <th>Ruta</th>
            <th>Origen</th>
            <th>Destino</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rutas as $ruta)
            <tr>
                <td>{{ $ruta->ruta }}</td>
                <td>{{ optional($ruta->oficinaOrigen)->nombre }}</td>
                <td>{{ optional($ruta->oficinaDestino)->nombre }}</td>
                <td>{{ $ruta->activo ? 'Activa' : 'Inactiva' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
