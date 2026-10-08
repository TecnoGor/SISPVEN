<table>
    <thead>
        <tr><th colspan="4"><strong>{{ $titulo }}</strong></th></tr>
        <tr><th colspan="4">Usuario: {{ $usuario->name }} | Oficina: {{ $oficina->nombre }}</th></tr>
        <tr><td colspan="4"></td></tr>
        <tr>
            <th>Código de Envío</th>
            <th>Contenido</th>
            <th>Peso (gr)</th>
            <th>{{ $titulo == 'Reporte de Envíos Entregados' ? 'Fecha de Salida' : 'Fecha de Entrada' }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($envios as $envio)
            <tr>
                <td>{{ $envio->codigo_envio }}</td>
                <td>{{ $envio->contenido }}</td>
                <td>{{ $envio->peso }}</td>
                <td>
                    {{ $titulo == 'Reporte de Envíos Entregados'
                        ? \Carbon\Carbon::parse($envio->almacenAduana->Salida)->format('d/m/Y')
                        : \Carbon\Carbon::parse($envio->almacenAduana->Entrada)->format('d/m/Y') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
