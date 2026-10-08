<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Entregas Nacionales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
        }
        h1 {
            text-align: center;
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
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
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

    <h1>Reporte de Entregas Nacionales</h1>
    <p><strong>Total de Envíos:</strong> {{ number_format($totalEnvios ?? 0, 0, ',', '.') }}</p>
    <p><strong>Total Monto:</strong> {{ number_format($totalMontos ?? 0, 2, ',', '.') }} Bs.</p>

    <table>
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Cantidad</th>
                <th>Total Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entregas as $entrega)
                <tr>
                    <td>{{ htmlspecialchars($entrega->servicio->nombre ?? 'Sin Nombre', ENT_QUOTES, 'UTF-8') }}</td>
                    <td>{{ number_format((int) $entrega->total_envios, 0, ',', '.') }}</td>
                    <td>{{ number_format((float) $entrega->costo_total, 2, ',', '.') }} Bs.</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
