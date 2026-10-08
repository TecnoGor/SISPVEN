<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Ruta</th>
            <th>Proveedor</th>
            <th>Vehículo</th>
            <th>Fecha de Salida</th>
            <th>Activo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($viajes as $viaje)
            <tr>
                <td>{{ $viaje->codigo }}</td>
                <td>{{ $viaje->ruta->ruta ?? '' }}</td>
                <td>{{ $viaje->vehiculo->proveedor->nombre ?? '' }}</td>
                <td>{{ $viaje->vehiculo->placa ?? '' }}</td>
                <td>{{ $viaje->fecha_salida }}</td>
                <td>{{ $viaje->activo ? 'Sí' : 'No' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
