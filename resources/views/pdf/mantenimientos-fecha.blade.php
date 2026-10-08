<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimientos</title>
    <style>
        @page {
            margin: 120px 30px 80px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
        }

        header img {
            width: 100px;
            height: auto;
            float: left;
        }

        .header-text {
            margin-top: 10px;
        }

        .header-text h1 {
            font-size: 18px;
            margin: 0;
        }

        .header-text p {
            font-size: 12px;
            margin: 2px 0 0 0;
        }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #aaa;
        }

        .vehiculo-card {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }

        .vehiculo-info {
            background-color: #e9f0f5;
            padding: 12px;
            font-weight: bold;
            border-left: 4px solid #3a86ff;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .mantenimiento-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .mantenimiento-table th,
        .mantenimiento-table td {
            border: 1px solid #ccc;
            padding: 8px 10px;
        }

        .mantenimiento-table th {
            background-color: #f3f3f3;
            text-align: left;
            font-size: 12px;
        }

        .mantenimiento-table td {
            font-size: 11px;
        }

        .servicio-item {
            padding-left: 20px;
        }

        .no-data {
            color: #777;
            font-style: italic;
            padding: 10px 0;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/ipostel.png') }}" alt="Logo Ipostel">
    <div class="header-text">
        <h1>Reporte de Mantenimientos de Flota</h1>
        <p>Desde {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} hasta {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</p>
    </div>
</header>

<footer>
    Ipostel - Sistema de Gestión de Flota Vehicular
</footer>

<main>
    @forelse ($vehiculos as $vehiculo)
        @php
            $vehiculoMantenimientos = $mantenimientos->where('vehiculo_id', $vehiculo->vehiculo_id);
        @endphp

        <div class="vehiculo-card">
            <div class="vehiculo-info">
                {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }} | Año: {{ $vehiculo->año ?? 'N/A' }} | Oficina: {{ $vehiculo->oficina->nombre ?? 'N/A' }}
            </div>

            @forelse ($vehiculoMantenimientos as $mantenimiento)
                <table class="mantenimiento-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Fecha</th>
                            <th style="width: 75%;">Descripción del Mantenimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mantenimiento->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $mantenimiento->descripcion }}</td>
                        </tr>
                    </tbody>
                </table>

                @if ($mantenimiento->detalles->count())
                    <table class="mantenimiento-table">
                        <thead>
                            <tr>
                                <th>Servicios Aplicados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mantenimiento->detalles as $detalle)
                                <tr>
                                    <td class="servicio-item">• {{ $detalle->servicioFlota->nombre ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="no-data">No se encontraron servicios asociados.</p>
                @endif
            @empty
                <p class="no-data">Este vehículo no tiene mantenimientos registrados en el rango.</p>
            @endforelse
        </div>

    @empty
        <p class="no-data">No se encontraron vehículos con mantenimientos en el rango seleccionado.</p>
    @endforelse
</main>

</body>
</html>
