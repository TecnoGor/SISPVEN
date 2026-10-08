<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Punto de Información</title>
    <style>
        @page {
            margin: 1.5cm;
            size: letter;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
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
            font-size: 14px;
            padding: 8px;
            border-bottom: 1px solid #000;
            background-color: #f2f2f2;
        }

        .header-left {
            width: 80%;
            border-right: 1px solid #000;
            vertical-align: top;
        }

        .header-right {
            width: 20%;
            vertical-align: top;
        }

        .header-info-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }

        .header-info-table td {
            padding: 5px;
            border-bottom: 1px solid #000;
            overflow: hidden;
            word-wrap: break-word;
        }

        .header-info-table tr:last-child td {
            border-bottom: none;
        }

        .label-bold {
            font-weight: bold;
            width: 120px;
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
            margin-bottom: 0;
            /* Let them touch if needed, or leave 20px */
        }

        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 6px;
            border-bottom: 1px solid #000;
        }

        .sintesis-box {
            padding: 6px;
            height: 150px;
            vertical-align: top;
            text-align: justify;
        }

        .recomendaciones-box {
            padding: 6px;
            height: 110px;
            vertical-align: top;
            text-align: justify;
        }

        .instructions-box {
            padding: 6px;
            height: 150px;
            vertical-align: top;
        }

        .highlight {
            background-color: yellow;
        }

        /* FOOTER STRUCTURE */
        .footer-wrapper {
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
            margin-top: -5px;
            /* Pull it up to touch the content table above */
            margin-bottom: 10px;
        }

        .anexos-row {
            padding: 4px 6px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures-table th {
            text-align: left;
            padding: 4px 6px;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            background-color: #f2f2f2;
            width: 33.33%;
            font-weight: bold;
        }

        .signatures-table th:last-child {
            border-right: none;
        }

        .signatures-table td {
            vertical-align: bottom;
            text-align: center;
            height: 100px;
            /* Space for signature */
            border-right: 1px solid #000;
            padding: 5px;
        }

        .signatures-table td:last-child {
            border-right: none;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .signature-title {
            font-size: 9px;
            font-style: italic;
            line-height: 1.1;
        }

        /* LEGAL FOOTER */
        .legal-disclaimer {
            border: 1px solid #000;
            padding: 4px 6px;
            margin-top: 5px;
            font-size: 8px;
            line-height: 1.1;
            text-align: justify;
        }

        .legal-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .contact-info {
            text-align: center;
            font-size: 9px;
            margin-top: 15px;
            color: #333;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <div class="main-container">

        <table class="header-table">
            <tr>
                <td colspan="2" class="header-title">PUNTO DE INFORMACIÓN A LA PRESIDENCIA DE IPOSTEL</td>
            </tr>
            <tr>
                <td class="header-left" style="padding: 0;">
                    <table class="header-info-table">
                        <tr>
                            <td class="label-bold">Presentado por</td>
                            <td class="value-cell uppercase" style="font-weight: bold;">
                                {{ $data['pi_presentado_por'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-bold">ASUNTO:</td>
                            <td class="value-cell" style="text-transform: uppercase;">{{ $data['pi_asunto_pdf'] ?? $data['subject'] ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="header-right" style="padding: 0;">
                    <table class="control-table">
                        <tr>
                            <td>{{ $data['pi_numero'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Fecha: {{ $data['date'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Página 1 de 1</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="content-table" style="margin-top: 20px; margin-bottom: 0;">
            <tr>
                <td class="section-title">SÍNTESIS</td>
            </tr>
            <tr>
                <td class="sintesis-box">
                    Ante todo reciba un cordial saludo, Revolucionario, Madurista y Bolivariano, extensivo a todo su
                    equipo de trabajo. A su vez hago propicia la ocasión, para informarle que
                    {{ $data['pi_sintesis'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="section-title" style="border-top: 2px solid #000;">RECOMENDACIONES</td>
            </tr>
            <tr>
                <td class="recomendaciones-box">
                    Muy respetuosamente se recomienda a la ciudadana Olga Y. Pereira J., en su carácter de Presidenta
                    (E) del Instituto Postal Telegráfico de Venezuela IPOSTEL, {{ $data['pi_recomendaciones'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="section-title" style="border-top: 2px solid #000;">INSTRUCCIONES</td>
            </tr>
            <tr>
                <td class="instructions-box">

                </td>
            </tr>
        </table>

        <div class="footer-wrapper">
            <div class="anexos-row">
                Anexo(s): No (<span>{{ ($data['pi_has_anexo'] ?? 'No') === 'No' ? 'X' : '' }}</span>) Sí (<span>{{ ($data['pi_has_anexo'] ?? 'No') === 'Sí' ? 'X' : '' }}</span>)
            </div>
            <table class="signatures-table">
                <tr>
                    <th>Preparado por:</th>
                    <th>Verificado por:</th>
                    <th>Aprobado por:</th>
                </tr>
                <tr>
                    <td>
                        <div class="signature-name">{{ mb_strtoupper($data['sender_name'] ?? 'Axxxx B. Cxxxx D.') }}
                        </div>
                        <div class="signature-title">Director (a)
                            {{ $data['pi_presentado_por'] ?? 'presentante' }}<br>Instituto Postal Telegráfico de
                            Venezuela (IPOSTEL),<br>Providencia Administrativa N°</div>
                    </td>
                    <td>
                        <div class="signature-name">Abg. Yonathan A. Jaimes V.</div>
                        <div class="signature-title">Director del Despacho de la Presidencia<br>Instituto Postal
                            Telegráfico de Venezuela (IPOSTEL),<br>Providencia Administrativa N° DCJ-DGH/003-2025</div>
                    </td>
                    <td>
                        <div class="signature-name">OLGA Y. PEREIRA J.</div>
                        <div class="signature-title">Presidenta (E) del Instituto Postal<br>Telegráfico de Venezuela
                            IPOSTEL, según Decreto N° 3.877,<br>publicado en Gaceta Oficial N° 41.660 de fecha
                            21/06/2019.</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="legal-disclaimer">
            <span class="legal-title">USO INDEBIDO DE INFORMACIÓN O DATOS RESERVADOS</span>
            Ley Contra la Corrupción. Artículo 73. El funcionario público que utilice, para sí o para otro,
            informaciones o datos de carácter reservado de los cuales tenga conocimiento en razón de su cargo, será
            penado con prisión de uno (1) a seis (6) años y multa de hasta el cincuenta por ciento (50%) del beneficio
            perseguido u obtenido, siempre que el hecho no constituya otro delito. Si del hecho resultare algún
            perjuicio a la Administración Pública, la pena será aumentada de un tercio (1/3) a la mitad (1/2).
        </div>

        <div class="contact-info">Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve</div>
    </div>

</body>

</html>
