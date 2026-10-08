<table>
    <thead>
        <tr>
            <th colspan="4" style="font-size: 16px; font-weight: bold; padding: 10px;">
                Historial de Mantenimientos del Vehículo: {{ is_array($vehiculo) ? ($vehiculo['placa'] ?? 'N/A') : ($vehiculo->placa ?? 'N/A') }}
            </th>
        </tr>
        <tr>
            <th style="text-align: left;">N°</th>
            <th style="text-align: left;">Descripción del Mantenimiento</th>
            <th style="text-align: left;">Fecha del Registro</th>
            <th style="text-align: left;">Costo</th>
            <th style="text-align: left;">Kilometraje</th>
            <th style="text-align: left;">Servicios Realizados</th>
        </tr>
    </thead>
    <tbody>
        @php
            $mants = $mantenimientos instanceof \Illuminate\Pagination\AbstractPaginator ? $mantenimientos->items() : $mantenimientos;
        @endphp
        @foreach ($mants as $index => $mantenimiento)
            <tr style="background-color: #f0f0f0;">
                <td style="vertical-align: top;">{{ $index + 1 }}</td>
                <td style="vertical-align: top;">
                    <strong>{{ ucfirst($mantenimiento->descripcion) }}</strong>
                </td>
                <td style="vertical-align: top;">
                    {{ \Carbon\Carbon::parse($mantenimiento->created_at)->format('d/m/Y h:i A') }}
                </td>
                <td style="vertical-align: top;">{{ $mantenimiento->costo ?? 'N/A' }}</td>
                <td style="vertical-align: top;">{{ $mantenimiento->kilometraje ?? 'N/A' }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 10px;">
                        @foreach ($mantenimiento->detalles as $i => $detalle)
                            <li>
                                {{ $detalle->servicioFlota->nombre }}
                                <span style="color: #555;">({{ \Carbon\Carbon::parse($detalle->fecha)->format('d/m/Y') }})</span>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
