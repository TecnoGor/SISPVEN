<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>MEMORANDO - {{ $data['memo_correlativo'] ?? 'S/N' }}</title>
    <style>
        @page {
            margin: 3cm 2cm 2cm 4cm; /* Superior 3cm, Derecho 2cm, Inferior 2cm, Izquierdo 4cm */
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .correlativo-section {
            margin-bottom: 15px;
            position: relative;
        }

        .correlativo-label {
            display: inline-block;
            font-weight: bold;
        }

        .metadata-section {
            width: 100%;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .metadata-table {
            width: 100%;
            border-collapse: collapse;
        }

        .metadata-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .label-cell {
            font-weight: bold;
            width: 100px;
        }

        .value-cell {
            padding-left: 10px;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .content-body {
            text-align: justify;
            margin-bottom: 20px;
            min-height: 250px;
        }

        .content-body p {
            text-indent: 1cm; /* Sangría de primera línea */
            margin-top: 0;
            margin-bottom: 10px;
        }

        .closing {
            text-align: center;
            margin-bottom: 75px; /* Espacio equilibrado para la firma */
        }

        .signer-section {
            text-align: center;
            line-height: 1.2;
        }

        .signer-name {
            font-weight: bold;
            text-transform: none; /* Mayúsculas y minúsculas y en negrillas */
        }

        .signer-cargo {
            font-weight: bold;
            text-transform: none; /* Mayúsculas y minúsculas y en negrillas */
        }

        .footer-extras {
            position: fixed;
            bottom: 40px;
            left: 0;
            width: 100%;
            font-size: 9pt;
        }

        .anexos {
            margin-bottom: 5px;
        }

        .visado {
            margin-bottom: 0;
        }

        .footer-address {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 7.5pt;
            color: #333;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header-title">
        MEMORANDO
    </div>

    <div class="correlativo-section">
        <span class="correlativo-label">{{ $data['memo_correlativo'] ?? 'S/N' }}</span>
    </div>

    <div class="metadata-section">
        <table class="metadata-table">
            <tr>
                <td class="label-cell">PARA:</td>
                <td class="value-cell uppercase"><strong>{{ $data['memo_para_nombre'] ?? '...' }}</strong><br>{{ $data['memo_para_cargo'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label-cell">DE:</td>
                <td class="value-cell"><strong>{{ $data['memo_de_nombre'] ?? '...' }}</strong><br>{{ $data['memo_de_cargo'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label-cell">ASUNTO:</td>
                <td class="value-cell">{{ $data['memo_asunto_pdf'] ?? 'S/N' }}</td>
            </tr>
            <tr>
                <td class="label-cell">FECHA:</td>
                <td class="value-cell">{{ $data['date'] ?? now()->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="content-body">
        <p>
            Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano,
            Revolucionario, Antiimperialista y radicalmente Chavista, a la vez solicitar/remitir {{ $data['memo_cuerpo_detalle'] ?? '...' }}
        </p>

        <p>
            {{ $data['memo_accion'] ?? 'Solicitud/Remisión' }} que hago llegar, agradeciendo de antemano toda la atención que
            sirva dispensar a la presente, reiterándole mi más alta consideración y estima y quedando a
            sus gratas órdenes para seguir trabajando día a día por una <strong>Patria Grande, Libre, Soberana
            y Socialista</strong>, fortaleciendo así el desarrollo de la <strong>Revolución Bolivariana</strong>.
        </p>
    </div>

    <div class="closing">
        Atentamente,
    </div>

    <div class="signer-section">
        <div class="signer-name">{{ $data['firmante_nombre'] ?? ($data['sender_name'] ?? '...') }}</div>
        <div class="signer-cargo">{{ $data['firmante_cargo'] ?? ($data['memo_de_cargo'] ?? ($data['sender_cargo'] ?? '')) }}</div>
        <div>Instituto Postal Telegráfico de Venezuela (IPOSTEL)</div>
    </div>

    <div class="footer-extras">
        <div class="anexos">
            <strong>Anexo:</strong> {{ $data['memo_anexos_lista'] ?? 'N/A' }}
        </div>
        <div class="visado">
            {{ $data['memo_visado'] ?? 'XX/xxxxx' }}
        </div>
    </div>

    <div class="footer-address">Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve</div>

</body>

</html>
