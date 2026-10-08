<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Envíos</title>
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

        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo">
    </div>

    <h2>{{ $tituloReporte }}</h2>
    <p>Generado por: {{ $usuario->name }} ({{ $usuario->email }})</p>
    <p>Fecha de reporte: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    @php
        $esDisponible = str_contains($tituloReporte, 'disponible');
        $labelFecha = $esDisponible ? 'Fecha de Entrada' : 'Fecha de Salida';
    @endphp

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>{{ $labelFecha }}</th>
                <th>Contenido</th>
                <th>Peso (kg)</th>
                <th>Días en Almacén</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($envios as $envio)
                <tr>
                    <td>{{ $envio->codigo_envio }}</td>
                    <td>{{ \Carbon\Carbon::parse($envio->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $envio->contenido ?? 'N/A' }}</td>
                    <td>{{ number_format($envio->peso ?? 0, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($envio->created_at)->diffInDays(now()) }}</td>
                    <td>{{ $envio->estatus ? 'Disponible' : 'No Disponible' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
