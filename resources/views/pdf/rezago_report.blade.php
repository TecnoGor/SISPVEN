<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Rezago</title>
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
    <p>Oficina: {{ $nombreOficinaUsuario }}</p>
    <p>Generado por: {{ $usuario->name }} ({{ $usuario->email }})</p>
    <p>Fecha de reporte: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>N° Envío</th>
                <th>Usuario</th>
                <th>Contenido</th>
                <th>Servicio</th>
                <th>Peso (gr)</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Días en Rezago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($envios as $e)
                <tr>
                    <td>{{ optional($e->envio)->codigo_envio ?? 'N/A' }}</td>
                    <td>{{ optional(optional($e->envio)->users)->name ?? 'N/A' }}</td>
                    <td>{{ optional($e->envio)->contenido ?? 'N/A' }}</td>
                    <td>{{ optional(optional($e->envio)->servicio)->nombre ?? 'No disponible' }}</td>
                    <td>{{ optional($e->envio)->peso ?? 0 }}</td>
                    <td>{{ $e->Entrada ? \Carbon\Carbon::parse($e->Entrada)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $e->Salida ? \Carbon\Carbon::parse($e->Salida)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if ($e->Entrada)
                            {{ (int) \Carbon\Carbon::parse($e->Entrada)->diffInDays($e->Salida ?? now()) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
