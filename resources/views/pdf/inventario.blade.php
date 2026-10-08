{{-- filepath: resources/views/pdf/inventario.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Insumos - {{ $oficina->nombre ?? '' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .cintillo { width: 100%; margin-bottom: 10px; }
        .titulo { font-size: 20px; font-weight: bold; color: #002F6C; text-align: center; margin-bottom: 4px; }
        .subtitulo { font-size: 13px; color: #444; text-align: center; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #222; padding: 8px; text-align: center; }
        th { background: #002F6C; color: #fff; font-size: 13px; }
        tr:nth-child(even) { background: #f7f7f7; }
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: right; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    {{-- Cintillo institucional --}}
    <img src="{{ public_path('images/cintillo.jpg') }}" class="cintillo">

    {{-- Título y subtítulo --}}
    <div class="titulo">INVENTARIO DE INSUMOS - {{ $oficina->nombre ?? '' }}</div>
    <div class="subtitulo">
        Usuario: {{ auth()->user()->name ?? auth()->user()->email ?? '---' }}<br>
        Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>INSUMO</th>
                <th>CANTIDAD DISPONIBLE</th>
                <th>ÚLTIMA ACTUALIZACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inventario)
                @foreach($inventario->insumos as $insumo)
                    <tr>
                        <td>{{ $insumo->descripcion ?: 'No disponible' }}</td>
                        <td>{{ $inventario->cantidad ?: 'Sin existencias' }}</td>
                        <td>{{ $inventario->updated_at ? $inventario->updated_at->format('d-m-Y') : 'No disponible' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por Ipostel - {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>