<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Entregas Internacionales</title>
    <style>
        /* Estilos básicos para el PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
            height: auto;
        }
        .header .info {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" alt="Logo">
        <div class="info">
            <p><strong>Usuario:</strong> {{ $usuario->name }}</p>
            <p><strong>Desde:</strong> {{ date('d/m/Y', strtotime($desde)) }}</p>
            <p><strong>Hasta:</strong> {{ date('d/m/Y', strtotime($hasta)) }}</p>
        </div>
    </div>

    <h1>Reporte de Entregas Internacionales</h1>

    <table>
        <thead>
            <tr>
                <th>Código Envío</th>
                <th>Cédula Remitente</th>
                <th>Nombre Remitente</th>
                <th>Costo Total (Bs)</th>
                <th>Costo Aviso (Bs)</th>
                <th>Costo Almacenaje (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entregas as $entrega)
                <tr>
                    <td>{{ $entrega->codigo_envio }}</td>
                    <td>{{ $entrega->cedula_remitente ?? 'N/A' }}</td>
                    <td>{{ $entrega->nombre_remitente ?? 'N/A' }}</td>
                    <td>{{ number_format($entrega->costo_total, 2, ',', '.') }} Bs</td>
                    <td>{{ number_format($entrega->coste_aviso, 2, ',', '.') }} Bs</td>
                    <td>{{ number_format($entrega->coste_almacenaje, 2, ',', '.') }} Bs</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p><strong>Total de envíos:</strong> {{ number_format($cantidad, 0, ',', '.') }}</p>
        <p><strong>Total de costos de envíos:</strong> {{ number_format($total_costo_envios, 2, ',', '.') }} Bs</p>
    </div>

</body>
</html>
