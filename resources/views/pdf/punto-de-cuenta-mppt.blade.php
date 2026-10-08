<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Punto de Cuenta - MPPT</title>
    <style>
        @page {
            margin: 0.8cm 1cm 1.2cm 1cm;
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
        }

        /* ── TÍTULO SUPERIOR ── */
        .cintillo-title {
            font-weight: bold;
            font-size: 13px;
            line-height: 1.3;
        }

        /* ── HEADER PRESENTANTE ── */
        .header-block {
            border: 1px solid #000;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        .header-block-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .header-block-inner td {
            padding: 3px 6px;
            vertical-align: middle;
        }
        .hb-left {
            width: 12%;
            border-right: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        .hb-center {
            width: 60%;
            text-align: center;
            font-size: 9px;
        }
        .hb-right {
            width: 28%;
            border-left: 1px solid #000;
        }
        .hb-right-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .hb-right-inner td {
            padding: 2px 4px;
            font-size: 9px;
        }
        .hb-right-inner .label-r {
            font-weight: bold;
        }

        /* ── SECCIONES ROJAS ── */
        .section-red {
            background-color: #cc0000;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 8px;
            text-transform: uppercase;
            border: 1px solid #000;
            border-bottom: none;
        }
        .section-box {
            border: 1px solid #000;
            border-top: none;
            padding: 8px;
            min-height: 20px;
            vertical-align: top;
            text-align: justify;
            font-size: 10px;
        }
        .section-box-asunto {
            min-height: 30px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .section-box-argumentacion {
            min-height: 80px;
        }
        .section-box-propuesta {
            min-height: 60px;
        }
        .section-box-comentarios {
            min-height: 50px;
        }
        .section-box-comentario-ministro {
            min-height: 50px;
        }

        /* ── DECISIÓN ── */
        .decision-red {
            background-color: #cc0000;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 8px;
            text-transform: uppercase;
            border: 1px solid #000;
            border-bottom: none;
        }
        .decision-box {
            border: 1px solid #000;
            border-top: none;
            padding: 8px 15px;
        }
        .decision-table {
            width: 100%;
            border-collapse: collapse;
        }
        .decision-table td {
            text-align: center;
            padding: 6px 5px;
            font-weight: bold;
            font-size: 10px;
            vertical-align: middle;
        }
        .checkbox {
            border: 1px solid #000;
            display: inline-block;
            width: 13px;
            height: 13px;
            vertical-align: middle;
            margin-bottom: 4px;
        }

        /* ── FOOTER ── */
        .footer-line {
            border-top: 1px solid #000;
            margin-top: 8px;
            padding-top: 3px;
            font-size: 8px;
        }
        .footer-table {
            width: 100%;
        }
        .footer-table td {
            font-size: 8px;
        }

        /* ── PAGE 2: FIRMAS ── */
        .page-break {
            page-break-after: always;
        }
        .firma-block {
            width: 48%;
            text-align: center;
            vertical-align: bottom;
            padding: 10px 5px 5px 5px;
        }
        .firma-line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 90%;
            padding-top: 3px;
        }
        .firma-name {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .firma-cargo {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .firma-decreto {
            font-size: 7px;
            font-weight: bold;
            line-height: 1.2;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .firma-ministro-block {
            text-align: center;
            margin-top: 60px;
        }
        .firma-ministro-line {
            border-top: 1px solid #000;
            width: 60%;
            margin: 0 auto;
            padding-top: 3px;
        }
    </style>
</head>
<body>

{{-- ═══════════════ PÁGINA 1 ═══════════════ --}}
<div class="main-container">

    {{-- TÍTULO --}}
    <div class="cintillo-title" style="text-align: center; margin-bottom: 6px;">
        PUNTO DE CUENTA AL MINISTRO DEL PODER POPULAR<br>
        PARA EL TRANSPORTE<br>
        V/A ANÍBAL EDUARDO CORONADO MILLÁN
    </div>

    {{-- BLOQUE PRESENTANTE / FECHA / N° --}}
    <div class="header-block">
        <table class="header-block-inner">
            <tr>
                <td class="hb-left">
                    N°<br>
                    <span style="font-size: 12px;">{{ $data['pcmppt_numero_corto'] ?? '' }}</span>
                </td>
                <td class="hb-center">
                    <div style="font-weight: bold; font-size: 9px;">Presentante:</div>
                    <div style="font-weight: bold; font-size: 10px; margin-top: 2px;">MSC. OLGA YOMIRA PEREIRA JAIMES</div>
                    <div style="font-size: 8px;">Presidenta del Instituto Postal Telegráfico de Venezuela<br>IPOSTEL</div>
                </td>
                <td class="hb-right">
                    <table class="hb-right-inner">
                        <tr>
                            <td class="label-r">Fecha:</td>
                            <td>{{ $data['date'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-r">Página:</td>
                            <td>Pag 1/2</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ASUNTO --}}
    <div class="section-red">ASUNTO</div>
    <div class="section-box section-box-asunto">
        SE SOMETE A CONSIDERACIÓN DEL CIUDADANO V/A ANÍBAL EDUARDO CORONADO MILLÁN, MINISTRO DEL PODER POPULAR PARA EL TRANSPORTE, {{ $data['pcmppt_asunto'] ?? 'XXXXXXXX' }}
    </div>

    {{-- ARGUMENTACIÓN --}}
    <div class="section-red" style="margin-top: 4px;">ARGUMENTACIÓN</div>
    <div class="section-box section-box-argumentacion">
        {{ $data['pcmppt_argumentacion'] ?? '' }}
    </div>

    {{-- PROPUESTA --}}
    <div class="section-red" style="margin-top: 4px;">PROPUESTA</div>
    <div class="section-box section-box-propuesta">
        <span style="color: #cc0000; font-style: italic;">{{ $data['pcmppt_propuesta'] ?? 'Resumen de lo solicitado' }}</span>, se solicita al ciudadano <strong>V/A Aníbal Eduardo Coronado Millán, Ministro del Poder Popular Para el Transporte,</strong>
    </div>

    {{-- COMENTARIOS DEL VICEMINISTRO --}}
    <div class="section-red" style="margin-top: 4px;">COMENTARIOS DEL CIUDADANO VICEMINISTRO DEL SECTOR</div>
    <div class="section-box section-box-comentarios"></div>

    {{-- DECISIÓN DEL CIUDADANO MINISTRO --}}
    <div class="decision-red" style="margin-top: 4px;">DECISIÓN DEL CIUDADANO MINISTRO</div>
    <div class="decision-box">
        <table class="decision-table">
            <tr>
                <td>
                    <div class="checkbox"></div><br>
                    APROBADO
                </td>
                <td>
                    <div class="checkbox"></div><br>
                    NEGADO
                </td>
                <td>
                    <div class="checkbox"></div><br>
                    VISTO
                </td>
                <td>
                    <div class="checkbox"></div><br>
                    DIFERIDO
                </td>
                <td>
                    <div class="checkbox"></div><br>
                    OTRO
                </td>
            </tr>
        </table>
    </div>

    {{-- COMENTARIO DEL CIUDADANO MINISTRO --}}
    <div class="section-red" style="margin-top: 4px;">COMENTARIO DEL CIUDADANO MINISTRO</div>
    <div class="section-box section-box-comentario-ministro"></div>

    {{-- PIE DE PÁGINA 1 --}}
    <div class="footer-line">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">ORIGINAL</td>
                <td style="text-align: right;">{{ $data['pcmppt_codigo_pie'] ?? '' }}</td>
            </tr>
        </table>
    </div>

</div>

<div class="page-break"></div>

{{-- ═══════════════ PÁGINA 2 ═══════════════ --}}
<div class="main-container">

    {{-- TÍTULO (repetido) --}}
    <div class="cintillo-title" style="text-align: center; margin-bottom: 6px;">
        PUNTO DE CUENTA AL MINISTRO DEL PODER POPULAR<br>
        PARA EL TRANSPORTE<br>
        V/A ANÍBAL EDUARDO CORONADO MILLÁN
    </div>

    {{-- BLOQUE PRESENTANTE / FECHA (repetido) --}}
    <div class="header-block">
        <table class="header-block-inner">
            <tr>
                <td class="hb-left">
                    N°<br>
                    <span style="font-size: 12px;">{{ $data['pcmppt_numero_corto'] ?? '' }}</span>
                </td>
                <td class="hb-center">
                    <div style="font-weight: bold; font-size: 9px;">Presentante:</div>
                    <div style="font-weight: bold; font-size: 10px; margin-top: 2px;">MSC. OLGA YOMIRA PEREIRA JAIMES</div>
                    <div style="font-size: 8px;">Presidenta del Instituto Postal Telegráfico de Venezuela<br>IPOSTEL</div>
                </td>
                <td class="hb-right">
                    <table class="hb-right-inner">
                        <tr>
                            <td class="label-r">Fecha:</td>
                            <td>{{ $data['date'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="label-r">Página:</td>
                            <td>Pag 2/2</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ESPACIO PARA FIRMAS --}}
    <div style="margin-top: 80px;">
        <table style="width: 100%;">
            <tr>
                {{-- FIRMA PRESIDENTA IPOSTEL --}}
                <td class="firma-block">
                    <div class="firma-line">
                        <div class="firma-name">MSC. OLGA YOMIRA PEREIRA JAIMES</div>
                        <div class="firma-cargo">PRESIDENTA DEL INSTITUTO POSTAL TELEGRÁFICO DE<br>VENEZUELA IPOSTEL</div>
                        <div class="firma-decreto">
                            DECRETO N° 3.877, DE FECHA 21 DE JUNIO DE 2019.<br>
                            PUBLICADO EN LA GACETA OFICIAL DE LA REPÚBLICA BOLIVARIANA DE<br>
                            VENEZUELA N° 41.660 DE FECHA 21 DE JUNIO DE 2019.
                        </div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                {{-- FIRMA VICEMINISTRO --}}
                <td class="firma-block">
                    <div class="firma-line">
                        <div class="firma-name">ALM. ELADIO JOSÉ GREGORIO JIMÉNEZ RATTIA</div>
                        <div class="firma-cargo">VICEMINISTRO DE PLANIFICACIÓN Y DESARROLLO<br>INTEGRAL DEL TRANSPORTE</div>
                        <div class="firma-decreto">
                            DECRETO N° 4.776 DE FECHA 01 DE MARZO DE 2023<br>
                            PUBLICADO EN LA GACETA OFICIAL DE LA REPÚBLICA BOLIVARIANA DE<br>
                            VENEZUELA N° 42.575 DE FECHA 01 DE MARZO DE 2023.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FIRMA MINISTRO (centrada abajo) --}}
    <div class="firma-ministro-block">
        <div class="firma-ministro-line">
            <div class="firma-name">V/A ANÍBAL EDUARDO CORONADO MILLÁN</div>
            <div class="firma-cargo">MINISTRO DEL PODER POPULAR PARA EL TRANSPORTE</div>
            <div class="firma-decreto">
                DECRETO N° 5.211, DE FECHA 16 DE ENERO DE 2026<br>
                PUBLICADO EN LA GACETA OFICIAL DE LA REPÚBLICA BOLIVARIANA<br>
                DE VENEZUELA N° 6.964 DE FECHA 16 DE ENERO DE 2026.
            </div>
        </div>
    </div>

    {{-- PIE DE PÁGINA 2 --}}
    <div class="footer-line" style="margin-top: 80px;">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">ORIGINAL</td>
                <td style="text-align: right;">{{ $data['pcmppt_codigo_pie'] ?? '' }}</td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
