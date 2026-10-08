<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Oficina</th>
            <th>Usuario</th>
            <th>Monto Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registros as $registro)
            <tr>
                <td>{{ $registro->id }}</td>
                <td>{{ $registro->created_at }}</td>
                <td>{{ $registro->oficina->nombre ?? 'N/A' }}</td>
                <td>{{ $registro->usuario->name ?? 'N/A' }}</td>
                <td>{{ $registro->monto_total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
