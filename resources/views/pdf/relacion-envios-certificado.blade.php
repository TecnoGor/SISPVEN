<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Relación de Envíos Certificados</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            margin: 10px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            border: 1px solid black;
            padding: 1px 3px;
            line-height: 1.2;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header-image {
            width: 100%;
            height: auto;
            max-height: 50px;
            object-fit: cover;
            margin-bottom: 8px;
        }

        .signature {
            height: 50px;
            vertical-align: top;
        }

        .spacer {
            height: 10px;
            border: none;
        }

        .bold {
            font-weight: bold;
        }

        .firmas-section {
            page-break-inside: avoid;
            margin-top: 15px;
        }

        h2 {
            font-size: 12px;
            margin: 5px 0;
        }
    </style>
</head>
<body>

<img src="{{ public_path('images/cintillo.jpg') }}" alt="Encabezado" class="header-image">

<h2 style="text-align: center;">Relación de Envíos Certificados</h2>

@php
    $primerPaquete = $paquetesCertificados->first();
@endphp

<table>
    <thead>
        <tr>
            <th colspan="7">RELACIÓN DE ENVÍOS CERTIFICADOS</th>
        </tr>
        <tr>
            <td colspan="2"><strong>HORA</strong></td>
            <td colspan="3"><strong>PÁGINA</strong></td>
            <td colspan="2"><strong>FECHA</strong></td>
        </tr>
        @if($primerPaquete)
        <tr>
            <td colspan="2">{{ now()->format('H:i') }}</td>
            <td colspan="3">1</td>
            <td colspan="2">{{ now()->format('d/m/Y') }}</td>
        </tr>
        @endif
        <tr class="spacer"><td colspan="7"></td></tr>
        <tr>
            <th style="width: 5%;">N°</th>
            <th style="width: 10%;">TIPO</th>
            <th style="width: 20%;">CÓDIGO</th>
            <th style="width: 30%;">CONTENIDO</th>
            <th style="width: 10%;">PESO (Gr)</th>
            <th style="width: 25%;" colspan="2">PRECINTO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($paquetesCertificados as $index => $paquete)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>N/A</td>
                <td>{{ $paquete['codigo_envio'] ?? $paquete['codigo'] ?? 'N/A' }}</td>
                <td style="text-align: left;">{{ $paquete['contenido'] ?? 'N/A' }}</td>
                <td>{{ $paquete['peso'] ?? 'N/A' }}</td>
                <td colspan="2">{{ $paquete['precinto'] ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Sección de firmas separada para no robar espacio a los datos --}}
<div class="firmas-section">
    <table>
        <tr class="spacer"><td colspan="4" style="border: none;"></td></tr>
        <tr>
            <th colspan="4">FIRMAS Y SELLOS</th>
        </tr>
        <tr>
            <td>ELABORADO POR</td>
            <td>SUPERVISADO POR</td>
            <td>RECIBIDO POR</td>
            <td>SUPERVISADO POR</td>
        </tr>
        <tr>
            <td>NOMBRE Y APELLIDO</td>
            <td>NOMBRE Y APELLIDO</td>
            <td>NOMBRE Y APELLIDO</td>
            <td>NOMBRE Y APELLIDO</td>
        </tr>
        <tr class="signature">
            <td><strong><br>C.I<br><br>FIRMA</strong></td>
            <td><strong><br>C.I<br><br>FIRMA</strong></td>
            <td><strong><br>C.I<br><br>FIRMA</strong></td>
            <td><strong><br>C.I<br><br>FIRMA</strong></td>
        </tr>
        <tr>
            <td colspan="2">UNIDAD EMISORA</td>
            <td colspan="2">UNIDAD RECEPTORA</td>
        </tr>
    </table>
</div>

<p style="text-align: center; font-size: 8px; margin-top: 10px;">
    Este es un documento generado automáticamente. No requiere firma.
</p>

</body>
</html>
