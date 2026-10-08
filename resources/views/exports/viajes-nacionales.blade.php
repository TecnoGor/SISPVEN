<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Vehículo</th>
            <th>Ruta</th>
            <th>Fecha de Salida</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($viajes as $viaje)
            <tr>
                <td>{{ $viaje->codigo }}</td>
                <td>{{ $viaje->vehiculo->placa }}</td>
                <td>{{ $viaje->ruta->ruta }}</td>
                <td>{{ \Carbon\Carbon::parse($viaje->fecha_salida)->format('d/m/Y') }}</td>
                <td>{{ $viaje->activo ? 'Activo' : 'Inactivo' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
