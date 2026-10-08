@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Combustible - {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }})</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
        .header { display: flex; align-items: center; margin-bottom: 20px; }
        .logo { height: 50px; margin-right: 20px; }
        .title { font-size: 22px; font-weight: bold; color: #1a4d2e; }
        .subtitle { font-size: 16px; color: #555; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #bbb; padding: 7px 5px; text-align: center; }
        th { background: #e6f4ea; color: #1a4d2e; font-weight: bold; }
        tr:nth-child(even) { background: #f8f8f8; }
        .footer { margin-top: 30px; text-align: right; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/ipostel.png') }}" class="logo" alt="Logo Ipostel">
        <div>
            <div class="title">Historial de Combustible</div>
            <div class="subtitle">
                <strong>Marca:</strong> {{ $vehiculo->marca }} &nbsp; | &nbsp;
                <strong>Modelo:</strong> {{ $vehiculo->modelo }} &nbsp; | &nbsp;
                <strong>Placa:</strong> {{ $vehiculo->placa }}
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Litros</th>
                <th>Costo</th>
                <th>Kilometraje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cargas as $carga)
                <tr>
                    <td>{{ Carbon::parse($carga->fecha)->format('d/m/Y') }}</td>
                    <td>{{ number_format($carga->litros, 2) }}</td>
                    <td>{{ number_format($carga->costo_total, 2) }}</td>
                    <td>{{ $carga->kilometraje }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Sin cargas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html> 