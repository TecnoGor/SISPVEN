<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Punto de Cuenta - Directorio</title>
    <style>
        @page {
            margin: 1.5cm;
            size: letter;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .main-container {
            width: 100%;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 5px;
        }

        /* HEADER TABLE */
        .header-table {
            border: 2px solid #000;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            padding: 6px;
            border-bottom: 1px solid #000;
            background-color: #f2f2f2;
        }
        .header-left {
            width: 75%;
            border-right: 1px solid #000;
            vertical-align: top;
        }
        .header-right {
            width: 25%;
            vertical-align: top;
        }

        .header-info-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }
        .header-info-table td {
            padding: 4px;
            border-bottom: 1px solid #000;
            overflow: hidden;
            word-wrap: break-word;
        }
        .header-info-table tr:last-child td {
            border-bottom: none;
        }
        .label-bold {
            font-weight: bold;
            width: 110px;
            border-right: 1px solid #000;
            white-space: nowrap;
        }
        .value-cell {
            padding-left: 5px;
            overflow: hidden;
            word-wrap: break-word;
            word-break: break-word;
        }

        .control-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            text-align: center;
        }
        .control-table td {
            padding: 4px;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        .control-table tr:last-child td {
            border-bottom: none;
            font-weight: normal;
        }

        /* CONTENT TABLE */
        .content-table {
            border: 2px solid #000;
            margin-top: 10px;
            margin-bottom: 0;
        }
        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 6px;
            border-bottom: 1px solid #000;
        }
        .sintesis-box {
            padding: 6px;
            height: 140px;
            vertical-align: top;
            text-align: justify;
        }
        .propuesta-box {
            padding: 6px;
            height: 90px;
            vertical-align: top;
            text-align: justify;
        }
        
        .decision-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        .decision-table td {
            padding: 4px;
            border-right: 1px solid #000;
        }
        .decision-table td:last-child {
            border-right: none;
        }
        .checkbox {
            border: 1px solid #000;
            display: inline-block;
            width: 10px;
            height: 10px;
            vertical-align: middle;
            margin-right: 4px;
        }

        .observaciones-box {
            padding: 6px;
            height: 80px;
            vertical-align: top;
        }

        /* BOARD TABLE */
        .board-title {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px;
            border: 2px solid #000;
            border-bottom: none;
            margin-top: 10px;
        }
        .board-table {
            border: 2px solid #000;
        }
        .board-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 4px;
            border: 1px solid #000;
            text-align: center;
        }
        .board-table td {
            padding: 4px;
            border: 1px solid #000;
            height: 25px;
        }
        .board-col-name { width: 45%; }
        .board-col-sign { width: 30%; }
        .board-col-seal { width: 25%; }

        /* FOOTER STRUCTURE */
        .footer-signatures-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: -1px;
        }
        .footer-signatures-table th {
            text-align: left;
            padding: 3px 6px;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            background-color: #f2f2f2;
            width: 33.33%;
            font-weight: bold;
        }
        .footer-signatures-table th:last-child {
            border-right: none;
        }
        .footer-signatures-table td {
            vertical-align: bottom;
            text-align: center;
            height: 80px;
            border-right: 1px solid #000;
            padding: 5px;
        }
        .footer-signatures-table td:last-child {
            border-right: none;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10px;
        }
        .signature-title {
            font-size: 8px;
            font-style: italic;
            line-height: 1.1;
        }

        /* LEGAL FOOTER */
        .legal-disclaimer {
            border: 1px solid #000;
            padding: 4px 6px;
            margin-top: 5px;
            font-size: 7px;
            line-height: 1.1;
            text-align: justify;
        }
        .legal-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .page-break {
            page-break-after: always;
        }
        .contact-info {
            text-align: center;
            font-size: 8px;
            margin-top: 15px;
            color: #333;
            white-space: nowrap;
        }
    </style>
</head>
<body>

<!-- PAGE 1 -->
<div class="main-container">
    <table class="header-table">
        <tr>
            <td colspan="2" class="header-title">PUNTO DE CUENTA AL DIRECTORIO DE IPOSTEL</td>
        </tr>
        <tr>
            <td class="header-left" style="padding: 0;">
                <table class="header-info-table">
                    <tr>
                        <td class="label-bold">Presentado por:</td>
                        <td class="value-cell uppercase" style="font-weight: bold;">{{ $data['pcd_presentado_por'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-bold">Asunto:</td>
                        <td class="value-cell" style="text-transform: uppercase;">{{ $data['pcd_asunto_pdf'] ?? $data['subject'] ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td class="header-right" style="padding: 0;">
                <table class="control-table">
                    <tr>
                        <td>{{ $data['pcd_numero'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Fecha: {{ $data['date'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Página 1 de 2</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="content-table">
        <tr>
            <td class="section-title">SÍNTESIS</td>
        </tr>
        <tr>
            <td class="sintesis-box">
                Se somete muy respetuosamente a consideración del Directorio del Instituto Postal Telegráfico de Venezuela IPOSTEL, en ejercicio de sus atribuciones conferida en los literales "a" y "l" del artículo 16 de la Ley de Reforma Parcial de la Ley que crea el Instituto Postal Telegráfico de Venezuela, publicada en Gaceta Oficial Extraordinaria N° 5.398, de fecha 26 de octubre de 1999;; {{ $data['pcd_sintesis'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="section-title" style="border-top: 2px solid #000;">PROPUESTA</td>
        </tr>
        <tr>
            <td class="propuesta-box">
                Se somete a consideración de los miembros del Directorio del Instituto Postal Telegráfico de Venezuela IPOSTEL, la aprobación de {{ $data['pcd_propuesta'] ?? '' }}, y se solicita delegar en la ciudadana Olga Y. Pereira J., en su condición de Presidenta Encargada de IPOSTEL, la facultad de suscribir la documentación que se derive de la presente solicitud, conforme a lo establecido en los literales "e" y "f" del artículo 17 del Decreto con Rango y Fuerza de Ley de Reforma Parcial de la Ley que crea el Instituto Postal Telegráfico de Venezuela.
            </td>
        </tr>
        <tr>
            <td class="section-title" style="border-top: 2px solid #000;">DECISIÓN</td>
        </tr>
        <tr>
            <td style="padding: 0;">
                <table class="decision-table">
                    <tr>
                        <td><span class="checkbox"></span> APROBADO</td>
                        <td><span class="checkbox"></span> NEGADO</td>
                        <td><span class="checkbox"></span> VISTO</td>
                        <td><span class="checkbox"></span> DIFERIDO</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="section-title">OBSERVACIONES:</td>
        </tr>
        <tr>
            <td class="observaciones-box"></td>
        </tr>
    </table>

    <div style="border: 2px solid #000; border-top: none; padding: 4px; font-weight: bold; font-size: 9px;">
        Anexo(s): No (<span>{{ ($data['pcd_has_anexo'] ?? 'No') === 'No' ? 'X' : '' }}</span>) Sí (<span>{{ ($data['pcd_has_anexo'] ?? 'No') === 'Sí' ? 'X' : '' }}</span>)
    </div>

    <div class="board-title">MIEMBROS DEL DIRECTORIO</div>
    <table class="board-table">
        <thead>
            <tr>
                <th class="board-col-name">Nombres y Apellidos</th>
                <th class="board-col-sign">Firma</th>
                <th class="board-col-seal">Sello</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Presidenta Olga Yomira Pereira Jaimes</td>
                <td></td>
                <td rowspan="6"></td>
            </tr>
            <tr>
                <td>Directora Principal Yasmín Ávila Castro</td>
                <td></td>
            </tr>
            <tr>
                <td>Director Principal Cleiver Guacache</td>
                <td></td>
            </tr>
            <tr>
                <td>Directora Principal Anakarina Mora Ramírez</td>
                <td></td>
            </tr>
            <tr>
                <td>Directora Suplente Anireny Yépez Martínez</td>
                <td></td>
            </tr>
            <tr>
                <td>Director Suplente Rogers Mosquera León</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="page-break"></div>

<!-- PAGE 2 -->
<div class="main-container">
    <table class="header-table">
        <tr>
            <td colspan="2" class="header-title">PUNTO DE CUENTA AL DIRECTORIO DE IPOSTEL</td>
        </tr>
        <tr>
            <td class="header-left" style="padding: 0;">
                <table class="header-info-table">
                    <tr>
                        <td class="label-bold">Presentado por:</td>
                        <td class="value-cell uppercase" style="font-weight: bold;">{{ $data['pcd_presentado_por'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-bold">Asunto:</td>
                        <td class="value-cell" style="text-transform: uppercase;">{{ $data['pcd_asunto_pdf'] ?? $data['subject'] ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td class="header-right" style="padding: 0;">
                <table class="control-table">
                    <tr>
                        <td>{{ $data['pcd_numero'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Fecha: {{ $data['date'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Página 2 de 2</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="board-table" style="margin-top: 10px;">
        <tbody>
            <tr>
                <td class="board-col-name">Director Suplente Rogers Mosquera León</td>
                <td class="board-col-sign"></td>
                <td class="board-col-seal" rowspan="2"></td>
            </tr>
            <tr>
                <td class="board-col-name">Directora Suplente Anyhaicar Guape</td>
                <td class="board-col-sign"></td>
            </tr>
        </tbody>
    </table>

    <table class="footer-signatures-table" style="margin-top: 10px;">
        <tr>
            <th>Realizado por:</th>
            <th>Revisado por:</th>
            <th>Aprobado por:</th>
        </tr>
        <tr>
            <td>
                <div class="signature-name">{{ mb_strtoupper($data['sender_name'] ?? 'Axxxx B. Cxxxx D.') }}</div>
                <div class="signature-title">{{ $data['pcd_presentado_por_cargo'] ?? ('Director (a) ' . ($data['pcd_presentado_por'] ?? '')) }}<br>IPOSTEL</div>
            </td>
            <td>
                <div class="signature-name">{{ mb_strtoupper($data['pcd_revisado_nombre'] ?? 'Abg. Yonathan A. Jaimes V.') }}</div>
                <div class="signature-title">{{ $data['pcd_revisado_cargo'] ?? 'Director del Despacho de la Presidencia' }}<br>IPOSTEL</div>
            </td>
            <td>
                <div class="signature-name">{{ mb_strtoupper($data['pcd_aprobado_nombre'] ?? 'OLGA Y. PEREIRA J.') }}</div>
                <div class="signature-title">{{ $data['pcd_aprobado_cargo'] ?? 'Presidenta (E) del Instituto Postal Telegráfico de Venezuela IPOSTEL' }}<br>IPOSTEL</div>
            </td>
        </tr>
    </table>

    <div class="legal-disclaimer">
        <span class="legal-title">USO INDEBIDO DE INFORMACIÓN O DATOS RESERVADOS</span>
        Ley Contra la Corrupción. Artículo 73. El funcionario público que utilice, para sí o para otro, informaciones o datos de carácter reservado de los cuales tenga conocimiento en razón de su cargo, será penado con prisión de uno (1) a seis (6) años y multa de hasta el cincuenta por ciento (50%) del beneficio perseguido u obtenido, siempre que el hecho no constituya otro delito. Si del hecho resultare algún perjuicio a la Administración Pública, la pena será aumentada de un tercio (1/3) a la mitad (1/2).
    </div>

    <div class="contact-info">Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve</div>
</div>

</body>
</html>
