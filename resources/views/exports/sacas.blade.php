<table>
    <thead>
        <!-- Título Principal -->
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">{{ $titulo }}</th>
        </tr>
        <!-- Subtítulo con Usuario y Oficina -->
        <tr>
            <th colspan="4" style="text-align: center; font-weight: normal; font-size: 14px;">
                Usuario: {{ $usuario->name }} <br> Oficina: {{ $oficina->nombre }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: normal; font-size: 14px;">
             Oficina: {{ $oficina->nombre }}
            </th>
        </tr>
        <!-- Cabecera de la Tabla -->
        <tr>
            <th>Código</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sacas as $saca)
            <tr>
                <td>{{ $saca->codigo_saca }}</td>
                <td>{{ $saca->tipoSaca->nombre ?? 'Sin Tipo' }}</td>
                <td>{{ $saca->cerrado ? 'Cerrada' : 'Abierta' }}</td>
                <td>{{ $saca->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
