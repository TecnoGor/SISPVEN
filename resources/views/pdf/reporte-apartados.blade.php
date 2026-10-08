{{-- filepath: resources/views/pdf/reporte-apartados.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Apartados Postales</title>
    <style>
        body { font-size: 11px; font-family: Arial, Helvetica, sans-serif; }
        .header { display: flex; align-items: center; margin-bottom: 20px; }
        .logo { height: 60px; margin-right: 20px; }
        .info { font-size: 14px; }
        .titulo { font-size: 18px; font-weight: bold; color: #002F6C; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #002F6C; padding: 6px 4px; }
        th {
            background-color: #002F6C;
            color: #fff;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #f3f6fa; }
        .subtitulo { font-size: 13px; color: #002F6C; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/cintillo.jpg') }}" class="logo" alt="Cintillo Ipostel">
        <div class="info">
            <div class="titulo">Ipostel</div>
            <div class="subtitulo">Reporte de Apartados Postales</div>
            <div>Oficina: {{ $oficinaNombre ?? 'Todas' }}</div>
            <div>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>CONDICIÓN</th>
                <th>NRO DOCUMENTO</th>
                <th>CLIENTE</th>
                <th>DÍAS RESTANTES</th>
                <th>ESTATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apartados as $ap)
                @php
                    $registro = $ap->registro_apartado()->where('activo', true)->latest()->first();
                @endphp
                <tr>
                    <td>{{ $ap->apartado ?: 'No disponible' }}</td>
                    <td>{{ $ap->operativo ? 'Operativo' : 'Inoperativo' }}</td>
                    <td>{{ $registro ? $registro->tipo_documento . '-' . $registro->documento : 'No existe cliente actual' }}</td>
                    <td>{{ $registro ? $registro->nombre . ' ' . $registro->apellido : 'No existe cliente actual' }}</td>
                    <td>{{ $ap->dias_restantes }}</td>
                    <td>{{ $ap->activo ? 'Activo' : 'Inactivo'}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>