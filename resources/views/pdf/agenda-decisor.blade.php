<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <script type="text/php">
        if (isset($pdf)) {
            $x = 450;
            $y = 48;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;
            $char_space = 0.0;
            $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
    <title>{{ $data['subject'] ?? 'Comunicación Oficial' }}</title>
    <style>
        @page {
            margin: 15mm 20mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
        }

        .logo-placeholder {
            font-weight: bold;
            color: #b91c1c;
            font-size: 18px;
        }

        .doc-info {
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .doc-type {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .meta-data {
            margin-bottom: 30px;
        }

        .meta-data table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-data td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 80px;
        }

        .content-body {
            text-align: justify;
            min-height: 400px;
            margin-bottom: 50px;
            white-space: pre-wrap;
        }

        .footer-signatures {
            width: 100%;
            margin-top: 50px;
        }

        .signature-box {
            text-align: center;
            width: 50%;
            margin: 0 auto;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }

        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            color: white;
            background-color: #666;
        }

        .priority-Alta {
            background-color: #ef4444;
        }

        .priority-Urgente {
            background-color: #b91c1c;
        }

        .priority-Normal {
            background-color: #3b82f6;
        }

    </style>
</head>

<body>
    @if (($data['type'] ?? '') === 'Agenda al Decisor')
        {{-- Layout Agenda al Decisor --}}
        <div style="border: 2px solid #000; margin-bottom: 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td
                        style="width: 60%; padding: 10px; border-right: 2px solid #000; text-align: center; vertical-align: middle;">
                        <h1 style="margin: 0; font-size: 24px; letter-spacing: 2px;">AGENDA AL DECISOR</h1>
                    </td>
                    <td style="width: 20%; border-right: 2px solid #000; vertical-align: top;">
                        <div style="border-bottom: 1px solid #000; padding: 2px 5px; font-size: 10px;">N°
                            {{ $data['codigo_control'] ?? 'AD-DXX-000' }}</div>
                        <div style="padding: 2px 5px; font-size: 10px;">Página 1 de 1</div>
                    </td>
                    <td style="width: 20%; vertical-align: top;">
                        <div style="border-bottom: 1px solid #000; padding: 2px 5px; font-size: 10px;">Agenda N° -
                            {{ $data['agenda_numero'] ?? '202__' }}</div>
                        <div style="padding: 2px 5px; font-size: 10px;">Fecha: {{ now()->format('d / m / Y') }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: center; font-weight: bold; font-size: 10px; padding: 2px;">
                        PRESENTADO</td>
                </tr>
                <tr>
                    <td
                        style="border-top: 2px solid #000; border-right: 2px solid #000; padding: 5px; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 11px;">Presentante:</div>
                        <div style="margin-top: 5px; font-size: 11px;">{{ $data['presentante'] ?? '---' }}</div>
                        @if(!empty($data['presentante_cargo']))
                            <div style="font-size: 9px; color: #555; margin-top: 2px;">{{ $data['presentante_cargo'] }}</div>
                        @endif
                    </td>
                    <td colspan="2" style="border-top: 2px solid #000; padding: 5px; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 11px;">Para:</div>
                        <div style="margin-top: 5px; font-size: 11px;">
                            <strong>{{ mb_strtoupper($data['agenda_para_nombre'] ?? 'MSC. OLGA Y. PEREIRA J.') }}</strong>
                        </div>
                        @if(!empty($data['agenda_para_cargo']))
                            <div style="font-size: 9px; color: #555; margin-top: 2px;">{{ $data['agenda_para_cargo'] }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: center; font-weight: bold; font-size: 10px; padding: 2px;">
                        ASUNTO A SER SOMETIDO A CONSIDERACIÓN DE LA PRESIDENCIA</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td
                                    style="width: 150px; border-right: 1px solid #000; padding: 10px; vertical-align: top;">
                                    <div
                                        style="font-weight: bold; text-decoration: underline; margin-bottom: 10px; font-size: 11px;">
                                        Secuencia:</div>
                                    <div style="font-size: 11px; line-height: 1.6;">
                                        Relación: <span
                                            style="border-bottom: 1px solid #000; display: inline-block; width: 30px; text-align: center;">{{ ($data['secuencia'] ?? '') === 'Relación' ? 'X' : ' ' }}</span><br>
                                        Propuesta: <span
                                            style="border-bottom: 1px solid #000; display: inline-block; width: 30px; text-align: center;">{{ ($data['secuencia'] ?? '') === 'Propuesta' ? 'X' : ' ' }}</span><br>
                                        Anexo: <span
                                            style="border-bottom: 1px solid #000; display: inline-block; width: 30px; text-align: center;">{{ ($data['secuencia'] ?? '') === 'Anexo' ? 'X' : ' ' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 10px; vertical-align: top;">
                                    <div style="text-align: justify; font-size: 11px; min-height: 100px;">
                                        {{ $data['texto_asunto'] ?? '---' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: left; font-weight: bold; font-size: 10px; padding: 2px 10px;">
                        ACCIONES A REALIZAR</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; padding: 10px; vertical-align: top;">
                        <div style="text-align: justify; font-size: 11px; line-height: 1.4; min-height: 80px;">
                            Se presenta para conocimiento, consideración y fines pertinentes de la Ciudadana Presidenta
                            de IPOSTEL; MSC. OLGA Y. PEREIRA J., AGENDA AL DECISOR a través del cual se
                            <span>{{ $data['cuerpo_resumen'] ?? '(indicar el asunto...)' }}</span>.
                            <br><br>
                            Finalmente, se propone
                            {{ $data['cuerpo_propuesta'] ?? '(en conclusión el motivo de dicha presentación)' }}.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: left; font-weight: bold; font-size: 10px; padding: 2px 10px;">
                        COMENTARIOS DE LA PRESIDENCIA:</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; height: 60px;"></td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: left; font-weight: bold; font-size: 10px; padding: 2px 10px;">
                        DECISIÓN DE LA PRESIDENCIA</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; padding: 0;">
                        <table
                            style="width: 100%; border-collapse: collapse; text-align: center; font-size: 10px; font-weight: bold;">
                            <tr>
                                <td style="border-right: 1px solid #000; padding: 5px; width: 25%;">
                                    <span
                                        style="border: 1px solid #000; display: inline-block; width: 12px; height: 12px; vertical-align: middle;"></span>
                                    APROBADO
                                </td>
                                <td style="border-right: 1px solid #000; padding: 5px; width: 25%;">
                                    <span
                                        style="border: 1px solid #000; display: inline-block; width: 12px; height: 12px; vertical-align: middle;"></span>
                                    NEGADO
                                </td>
                                <td style="border-right: 1px solid #000; padding: 5px; width: 25%;">
                                    <span
                                        style="border: 1px solid #000; display: inline-block; width: 12px; height: 12px; vertical-align: middle;"></span>
                                    VISTO
                                </td>
                                <td style="padding: 5px; width: 25%;">
                                    <span
                                        style="border: 1px solid #000; display: inline-block; width: 12px; height: 12px; vertical-align: middle;"></span>
                                    DIFERIDO
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; background: #eee; text-align: left; font-weight: bold; font-size: 10px; padding: 2px 10px;">
                        Firmas:</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
                            <tr>
                                <td
                                    style="width: 33.33%; border-right: 1px solid #000; padding: 5px; height: 120px; vertical-align: top; text-align: center;">
                                    <div
                                        style="font-weight: bold; text-align: left; border-bottom: 1px solid #eee; margin-bottom: 40px;">
                                        Aprobado por:</div>
                                    <div style="font-weight: bold; font-size: 10px;">{{ mb_strtoupper($data['agenda_aprobado_nombre'] ?? 'MSC. OLGA Y. PEREIRA J.') }}</div>
                                    <div style="font-size: 8px; line-height: 1.2;">
                                        {{ $data['agenda_aprobado_cargo'] ?? 'Presidenta (E) del Instituto Postal Telegráfico de Venezuela IPOSTEL, según Decreto N° 3.877 de fecha 21/06/2019, publicado en Gaceta Oficial N° 41.660 de fecha 21/06/2019.' }}
                                    </div>
                                </td>
                                <td
                                    style="width: 33.33%; border-right: 1px solid #000; padding: 5px; height: 120px; vertical-align: top; text-align: center;">
                                    <div
                                        style="font-weight: bold; text-align: left; border-bottom: 1px solid #eee; margin-bottom: 40px;">
                                        Verificado por:</div>
                                    <div style="font-weight: bold; font-size: 10px;">{{ mb_strtoupper($data['agenda_verificado_nombre'] ?? 'Abg. Yonathan A. Jaimes V.') }}</div>
                                    <div style="font-size: 8px; line-height: 1.2;">
                                        {{ $data['agenda_verificado_cargo'] ?? 'Director del Despacho de la Presidencia Instituto Postal Telegráfico de Venezuela (IPOSTEL), Providencia Administrativa N° DCJ-DGH/003-2025 de fecha 20/01/2025' }}
                                    </div>
                                </td>
                                <td
                                    style="width: 33.33%; padding: 5px; height: 120px; vertical-align: top; text-align: center;">
                                    <div
                                        style="font-weight: bold; text-align: left; border-bottom: 1px solid #eee; margin-bottom: 40px;">
                                        Presentado por:</div>
                                    <div style="font-weight: bold; font-size: 10px;">
                                        {{ mb_strtoupper($data['presentante'] ?? '---') }}</div>
                                    <div style="font-size: 8px; line-height: 1.2;">
                                        {{ $data['presentante_cargo'] ?? 'Director (a) de Unidad Administrativa Instituto Postal Telegráfico de Venezuela (IPOSTEL)' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border-top: 2px solid #000; padding: 5px; font-weight: bold; font-size: 11px;">
                        Anexo(s): No ({{ ($data['agenda_has_anexo'] ?? 'No') === 'No' ? 'X' : ' ' }}) Sí ({{ ($data['agenda_has_anexo'] ?? 'No') === 'Sí' ? 'X' : ' ' }}) :
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 1px solid #000; padding: 5px;">
                        <div style="font-size: 7px; text-align: justify; line-height: 1.1;">
                            <strong>USO INDEBIDO DE INFORMACIÓN O DATOS RESERVADOS</strong><br>
                            <strong>Ley Contra la Corrupción. Artículo 73.</strong> El funcionario público que utilice,
                            para sí o para otro, informaciones o datos de carácter reservado de los cuales tenga
                            conocimiento en razón de su cargo, será penado con prisión de uno (1) a seis (6) años y
                            multa de hasta el cincuenta por ciento (50%) del beneficio perseguido u obtenido, siempre
                            que el hecho no constituya otro delito. Si del hecho resultare algún perjuicio a la
                            Administración Pública, la pena será aumentada de un tercio (1/3) a la mitad (1/2).
                        </div>
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 5px; font-size: 8px; color: #555;">
                Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58)
                212-405-3203, www.ipostel.gob.ve
            </div>
        </div>
    @else
        {{-- Layout General (Puntos, Oficios, Memorando, etc.) --}}
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="logo-placeholder">IPOSTEL</div>
                        <div style="font-size: 9px;">Instituto Postal Telegráfico de Venezuela</div>
                    </td>
                    <td class="doc-info">
                        <div>FECHA: {{ now()->format('d/m/Y') }}</div>
                        <div>Nro: {{ $data['id'] ?? 'S/N' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="title-section">
            <div class="doc-type">{{ $data['type'] ?? 'Comunicación' }}</div>
        </div>

        <div class="meta-data">
            <table>
                <tr>
                    <td class="label">PARA:</td>
                    <td>{{ $data['destinatario'] ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">DE:</td>
                    <td>{{ $data['sender'] ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">ASUNTO:</td>
                    <td><strong>{{ $data['subject'] ?? 'Sin Asunto' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">FECHA:</td>
                    <td>{{ now()->format('d \d\e F \d\e Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="content-body">
            {{ $data['cuerpo'] ?? '' }}
        </div>

        <div class="footer-signatures">
            <div class="signature-box">
                <div class="signature-line">
                    <strong>{{ $data['sender'] ?? 'Autorizado' }}</strong><br>
                    <span>Firma y Sello</span>
                </div>
            </div>
        </div>
    @endif
</body>

</html>
