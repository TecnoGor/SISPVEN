<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .header img {
            width: 150px;
            height: auto;
        }
        .info {
            text-align: right;
            font-size: 12px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border: 1px solid #ccc;
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
            <p><strong>Usuario:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Oficina:</strong> {{ $oficina->nombre }}</p>
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <h1>{{ $titulo }}</h1>

    <p><strong>Total de Envíos:</strong> {{ count($envios) }}</p>

    <table>
        <thead>
            <tr>
                <th>Código de Envío</th>
                <th>Contenido</th>
                <th>Peso</th>
                <th>{{ $titulo === 'Reporte de Envíos Entregados' ? 'Fecha de Salida' : 'Fecha de Entrada' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($envios as $envio)
                <tr>
                    <td>{{ $envio->codigo_envio }}</td>
                    <td>{{ $envio->contenido }}</td>
                    <td>{{ $envio->peso }} gr</td>
                    <td>
                        {{ $titulo === 'Reporte de Envíos Entregados'
                            ? \Carbon\Carbon::parse($envio->almacenAduana->Salida)->format('d/m/Y')
                            : \Carbon\Carbon::parse($envio->almacenAduana->Entrada)->format('d/m/Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
