<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimientos - {{ $vehiculo->placa }}</title>
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


        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 30px;
        }

        h1, h2 {
            text-align: center;
            margin: 0;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 16px;
            margin-bottom: 25px;
        }

        .info-table, .mantenimiento-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .info-table th, .info-table td,
        .mantenimiento-table th, .mantenimiento-table td {
            border: 1px solid #aaa;
            padding: 8px;
        }

        .info-table th {
            background-color: #f0f0f0;
            width: 25%;
        }

        .section-title {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #aaa;
        }

        .servicios-title {
            background-color: #f9f9f9;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
        }

        .servicio-item {
            padding-left: 15px;
        }

    </style>
</head>
<body>


    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo Ipostel">
    </div>


    <h1>Reporte de Mantenimientos</h1>
    <h2>Vehículo: {{ is_array($vehiculo) ? ($vehiculo['placa'] ?? 'N/A') : ($vehiculo->placa ?? 'N/A') }} - {{ is_array($vehiculo) ? ($vehiculo['modelo'] ?? 'N/A') : ($vehiculo->modelo ?? 'N/A') }}</h2>

    {{-- Información del Vehículo --}}
    <table class="info-table">
        <tr>
            <th>Placa</th>
            <td>{{ is_array($vehiculo) ? ($vehiculo['placa'] ?? 'N/A') : ($vehiculo->placa ?? 'N/A') }}</td>
        </tr>
        <tr>
            <th>Marca</th>
            <td>{{ is_array($vehiculo) ? ($vehiculo['marca'] ?? 'N/A') : ($vehiculo->marca ?? 'N/A') }}</td>
        </tr>
        <tr>
            <th>Modelo</th>
            <td>{{ is_array($vehiculo) ? ($vehiculo['modelo'] ?? 'N/A') : ($vehiculo->modelo ?? 'N/A') }}</td>
        </tr>
        <tr>
            <th>Año</th>
            <td>{{ is_array($vehiculo) ? ($vehiculo['año'] ?? 'N/A') : ($vehiculo->año ?? 'N/A') }}</td>
        </tr>
        <tr>
            <th>Oficina</th>
            <td>{{ is_array($vehiculo) ? (isset($vehiculo['oficina']['nombre']) ? $vehiculo['oficina']['nombre'] : 'N/A') : (isset($vehiculo->oficina->nombre) ? $vehiculo->oficina->nombre : 'N/A') }}</td>
        </tr>
    </table>

    {{-- Mantenimientos --}}
    @php
        $mants = $mantenimientos instanceof \Illuminate\Pagination\AbstractPaginator ? $mantenimientos->items() : $mantenimientos;
    @endphp
    @foreach($mants as $mantenimiento)
        <div class="section-title">
            Mantenimiento del {{ \Carbon\Carbon::parse($mantenimiento->fecha)->format('d/m/Y') }} <br>  Descripcion: {{ $mantenimiento->descripcion }}
            <br> Costo: {{ $mantenimiento->costo ?? 'N/A' }} | Kilometraje: {{ $mantenimiento->kilometraje ?? 'N/A' }}
        </div>
        <table class="mantenimiento-table">
            <thead>
                <tr class="servicios-title">
                    <th>Servicio Aplicado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mantenimiento->detalles as $detalle)
                    <tr>
                        <td class="servicio-item">
                            • {{ $detalle->servicioFlota->nombre ?? 'N/A' }}
                        </td>
                        <td>{{ isset($detalle->fecha) ? \Carbon\Carbon::parse($detalle->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
