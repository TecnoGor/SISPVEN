<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Minuta de Reunión - {{ $data['id'] }}</title>
    <style>
        @page {
            size: letter landscape;
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .header-table, .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td, .main-table td, .main-table th {
            border: 1px solid #000;
            padding: 5px;
        }
        .logo-section {
            width: 20%;
            text-align: center;
        }
        .title-section {
            width: 60%;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-section {
            width: 20%;
            font-size: 8pt;
        }
        .info-subtable {
            width: 100%;
            border-collapse: collapse;
        }
        .info-subtable td {
            border: 1px solid #000;
            text-align: center;
        }
        .bg-gray {
            background-color: #f0f0f0;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .section-title {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            padding: 5px;
            text-transform: uppercase;
        }

        .row-n { width: 30px; text-align: center; font-weight: bold; }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 7pt;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        .page-break {
            page-break-after: always;
        }

        .mppt-logo {
            float: right;
            height: 40px;
        }
        .ipostel-logo {
            height: 40px;
        }
    </style>
</head>
<body>

    {{-- PAGE 1 --}}
    <div class="container">
        <div style="width: 100%; margin-bottom: 10px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Logo_MPPT_2016.png/250px-Logo_MPPT_2016.png" class="mppt-logo">
        </div>

        <table class="header-table">
            <tr>
                <td class="logo-section">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Ipostel.png" class="ipostel-logo">
                </td>
                <td class="title-section">MINUTA</td>
                <td class="info-section">
                    <table class="info-subtable">
                        <tr>
                            <td colspan="2" class="bg-gray">N°</td>
                            <td class="bg-gray">PÁGINAS</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-weight: bold;">{{ $data['id'] }}</td>
                            <td>1 de 2</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="bg-gray">FECHA</td>
                        </tr>
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('m') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('Y') }}</td>
                        </tr>
                        <tr>
                            <td>DIA</td>
                            <td>MES</td>
                            <td>AÑO</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <td colspan="2" class="bg-gray text-center font-bold">FACILITADOR</td>
            </tr>
            <tr>
                <td width="50%">
                    <div style="font-size: 7pt;">FACILITADOR</div>
                    <div class="font-bold">{{ $data['minuta_facilitador'] }}</div>
                </td>
                <td width="50%">
                    <div style="font-size: 7pt;">DEPENDENCIA</div>
                    <div class="font-bold">{{ $data['minuta_dependencia'] }}</div>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <td class="row-n bg-gray">N°</td>
                <td class="bg-gray text-center font-bold">PUNTO (S) TRATADO (S)</td>
            </tr>
            @foreach($data['minuta_puntos'] as $i => $punto)
            <tr>
                <td class="row-n">{{ $i + 1 }}</td>
                <td>{{ $punto }}</td>
            </tr>
            @endforeach
        </table>

        <table class="main-table">
            <thead>
                <tr class="bg-gray">
                    <td class="row-n">N°</td>
                    <td class="text-center font-bold">PARTICIPANTES</td>
                    <td class="text-center font-bold">UBICACIÓN</td>
                    <td class="text-center font-bold">CORREO ELECTRONICO</td>
                    <td class="text-center font-bold">TELEFONO</td>
                    <td class="text-center font-bold">FIRMA</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data['minuta_participantes'] as $i => $p)
                <tr>
                    <td class="row-n">{{ $i + 1 }}</td>
                    <td>{{ $p['nombre'] }}</td>
                    <td>{{ $p['ubicacion'] }}</td>
                    <td>{{ $p['correo'] }}</td>
                    <td>{{ $p['telefono'] }}</td>
                    <td style="width: 80px;"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="main-table">
            <tr>
                <td class="row-n bg-gray">N°</td>
                <td class="bg-gray text-center font-bold">PLANTEAMIENTOS</td>
            </tr>
            @foreach($data['minuta_planteamientos'] as $i => $planteamiento)
            <tr>
                <td class="row-n">{{ $i + 1 }}</td>
                <td>{{ $planteamiento }}</td>
            </tr>
            @endforeach
        </table>

        <div class="footer">
            Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve
        </div>
    </div>

    <div class="page-break"></div>

    {{-- PAGE 2 --}}
    <div class="container">
        <div style="width: 100%; margin-bottom: 10px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Logo_MPPT_2016.png/250px-Logo_MPPT_2016.png" class="mppt-logo">
        </div>

        <table class="header-table">
            <tr>
                <td class="logo-section">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Ipostel.png" class="ipostel-logo">
                </td>
                <td class="title-section">MINUTA</td>
                <td class="info-section">
                    <table class="info-subtable">
                        <tr>
                            <td colspan="2" class="bg-gray">N°</td>
                            <td class="bg-gray">PÁGINAS</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-weight: bold;">{{ $data['id'] }}</td>
                            <td>2 de 2</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="bg-gray">FECHA</td>
                        </tr>
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('m') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data['minuta_fecha'])->format('Y') }}</td>
                        </tr>
                        <tr>
                            <td>DIA</td>
                            <td>MES</td>
                            <td>AÑO</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <td class="row-n bg-gray">N°</td>
                <td class="bg-gray text-center font-bold">ACUERDOS</td>
            </tr>
            @foreach($data['minuta_acuerdos'] as $i => $acuerdo)
            <tr>
                <td class="row-n">{{ $i + 1 }}</td>
                <td>{{ $acuerdo }}</td>
            </tr>
            @endforeach
        </table>

        <table class="main-table">
            <thead>
                <tr class="bg-gray">
                    <td colspan="4" class="text-center font-bold">PUNTOS PENDIENTES Y TAREAS POR REALIZAR</td>
                </tr>
                <tr class="bg-gray">
                    <td class="row-n">N°</td>
                    <td class="text-center font-bold">TAREAS POR REALIZAR</td>
                    <td class="text-center font-bold">FECHA COMPROMISO</td>
                    <td class="text-center font-bold">RESPONSABLE DE LA ACCION</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data['minuta_tareas'] as $i => $t)
                <tr>
                    <td class="row-n">{{ $i + 1 }}</td>
                    <td>{{ $t['tarea'] }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ $t['responsable'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="main-table">
            <tr class="bg-gray">
                <td width="25%" class="text-center font-bold">ELABORADO POR:</td>
                <td width="25%" class="text-center font-bold">REVISADO POR:</td>
                <td width="35%" class="text-center font-bold">PRESENTADO A:</td>
                <td width="15%" class="text-center font-bold">ANEXOS:</td>
            </tr>
            <tr>
                <td style="height: 55px; text-align: center; vertical-align: top; padding-top: 4px;">
                    <div style="font-size: 8pt; font-weight: bold;">{{ $data['minuta_elaborado'] }}</div>
                    <div style="font-size: 8pt; color: #000;">{{ $data['minuta_elaborado_cargo'] ?? '' }}</div>
                </td>
                <td style="height: 55px; text-align: center; vertical-align: top; padding-top: 4px;">
                    <div style="font-size: 8pt; font-weight: bold;">{{ $data['minuta_revisado'] }}</div>
                    <div style="font-size: 8pt; color: #000;">{{ $data['minuta_revisado_cargo'] ?? '' }}</div>
                </td>
                <td style="height: 55px; text-align: center; vertical-align: top; padding-top: 4px;">
                    <div style="font-size: 8pt; font-weight: bold;">{{ $data['minuta_presentado_a'] }}</div>
                    <div style="font-size: 8pt; color: #000;">{{ $data['minuta_presentado_a_cargo'] ?? '' }}</div>
                </td>
                <td>
                    <div style="font-size: 8pt;">
                        SI <span style="border: 1px solid #000; padding: 1px 4px;">{{ $data['minuta_anexos'] === 'SI' ? 'X' : ' ' }}</span> 
                        &nbsp;
                        NO <span style="border: 1px solid #000; padding: 1px 4px;">{{ $data['minuta_anexos'] === 'NO' ? 'X' : ' ' }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve
        </div>
    </div>

</body>
</html>
