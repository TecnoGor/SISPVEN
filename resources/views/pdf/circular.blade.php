<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Circular - {{ $data['circular_numero'] ?? 'S/N' }}</title>
    <style>
        @page {
            margin: 3cm 2cm 2cm 4cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
        }

        .header-date {
            text-align: right;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .circular-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .circular-number {
            font-size: 14pt;
            font-weight: bold;
        }

        .circular-number i {
            font-style: italic;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            font-size: 14pt;
            text-transform: uppercase;
            margin-bottom: 50px;
        }

        .content {
            text-align: justify;
            margin-bottom: 60px;
        }
        .content p {
            text-indent: 1cm;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .action-verb {
            font-weight: bold;
        }

        .signature-section {
            text-align: center;
            margin-top: 40px;
        }

        .atentamente {
            margin-bottom: 75px;
        }

        .signer-info {
            line-height: 1.2;
        }

        .signer-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signer-cargo {
            font-weight: bold;
        }

        .institution-name {
            font-size: 10pt;
        }

        .providencia {
            font-size: 10pt;
        }

        .footer-address {
            position: fixed;
            bottom: 10px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 8pt;
            color: #000;
            white-space: nowrap;
        }

        .visado {
            position: fixed;
            bottom: 200px;
            left: 20pt;
            font-size: 9pt;
            font-weight: bold;
            text-align: left;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <div class="visado">
        {{ $data['circular_visado'] ?? '' }}
    </div>
    <div class="header-date">
        Caracas, {{ \Carbon\Carbon::now()->format('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de
        {{ \Carbon\Carbon::now()->format('Y') }}
    </div>

    <div class="circular-header">
        <div class="circular-number"><i>CIRCULAR</i> N° {{ $data['circular_numero'] ?? '___-2025' }}</div>
    </div>

    <div class="title">
        {{ $data['circular_titulo'] ?? 'Título comunicación / notificación o información' }}
    </div>

    <div class="content">
        @php
            $accion = $data['circular_accion'] ?? 'comunica';
            $inf_verb = match ($accion) {
                'comunica' => 'comunicar',
                'notifica' => 'notificar',
                'informa' => 'informar',
                default => 'comunicar',
            };
        @endphp
        <p>Por medio de la presente circular, se <span class="action-verb">{{ $accion }}</span> a todas las
        Direcciones, Gerentes, Jefes de División, Coordinadores del Instituto Postal Telegráfico de Venezuela (IPOSTEL),
        que {!! nl2br(e($data['circular_contenido'] ?? '')) !!}</p>
    </div>

    <div class="signature-section">
        <div class="atentamente">Atentamente,</div>

        <div class="signer-info">
            <div class="signer-name"><strong>{{ $data['firmante_nombre'] ?? ($data['sender_name'] ?? 'Aaaaaa B. Cccccc D.') }}</strong></div>
            <div class="signer-cargo"><strong>{{ $data['firmante_cargo'] ?? ($data['circular_cargo'] ?? ($data['sender_cargo'] ?? 'Director (a) de XXXXXXXXXX')) }}</strong></div>
            <div class="institution-name">Instituto Postal Telegráfico de Venezuela (IPOSTEL)</div>
        </div>
    </div>

    <div class="footer-address">
        Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve
    </div>
</body>

</html>
