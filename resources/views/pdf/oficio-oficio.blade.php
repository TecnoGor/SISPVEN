<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Oficio - Tipo Oficio</title>
    <style>
        @page {
            /* Tamaño Oficio (Legal) */
            size: legal;
            margin: 4.5cm 2cm 2cm 4cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .mppt-header {
            font-weight: bold;
            font-size: 12pt;
            float: left;
        }
        .header-date {
            text-align: right;
            font-weight: bold;
            float: right;
        }
        .clear {
            clear: both;
        }
        .recipient-section {
            margin-top: 40px;
            margin-bottom: 30px;
        }
        .recipient-section div {
            margin-bottom: 2px;
        }
        .bold-text {
            font-weight: bold;
        }
        .content {
            text-align: justify;
            margin-bottom: 40px;
        }
        .closing {
            text-align: center;
            margin-top: 50px;
        }
        .signer-info {
            text-align: center;
            line-height: 1.2;
            margin-top: 75px;
        }
        .signer-name {
            font-weight: bold;
            font-size: 12pt;
        }
        .signer-cargo {
            font-size: 9pt;
            font-style: italic;
        }
        .initials {
            font-size: 8pt;
            margin-top: 30px;
            line-height: 1.2;
        }
        .footer-extras {
            margin-top: 20px;
            font-size: 8pt;
        }
        .footer-address {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 8pt;
            padding-bottom: 20px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="mppt-header">
            {{ $data['oficio_carta_numero'] ?? 'S/N' }}
        </div>
        <div class="header-date">
            Caracas, {{ \Carbon\Carbon::now()->format('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de {{ \Carbon\Carbon::now()->format('Y') }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="recipient-section">
        <div>Ciudadano:</div>
        <div class="bold-text">{{ strtoupper($data['oficio_carta_para_nombre'] ?? 'NOMBRES Y APELLIDOS') }}</div>
        <div class="bold-text">{{ strtoupper($data['oficio_carta_para_cargo'] ?? 'CARGO DEL TITULAR') }}</div>
        @if(!empty($data['oficio_carta_entidad']))
            <div class="bold-text">{{ strtoupper($data['oficio_carta_entidad']) }}</div>
        @endif
        <div>Su Despacho.-</div>
    </div>

    <div class="content">
        @php
            $accion = $data['oficio_carta_accion'] ?? 'notificarle';
            $accion_sustantivo = match($accion) {
                'notificarle' => 'Notificación',
                'remitirle' => 'Remisión',
                'solicitarle' => 'Solicitud',
                default => 'Notificación'
            };
        @endphp
        <p>
            Tengo el agrado de dirigirme a usted, en la oportunidad de extenderle un cordial saludo Bolivariano, Revolucionario, Antiimperialista y radicalmente Chavista, a la vez <strong>{{ $accion }}</strong> {{ $data['oficio_carta_contenido'] ?? '...' }}
        </p>

        <p>
            <strong>{{ $accion_sustantivo }}</strong> que hago llegar, reiterándole mi más alta consideración y estima y quedando a sus gratas órdenes para seguir trabajando día a día por una <strong>Patria Grande, Libre, Soberana y Socialista</strong>, fortaleciendo así el desarrollo de la <strong>Revolución Bolivariana</strong>.
        </p>
    </div>

    <div class="closing">
        Atentamente,
    </div>

    <div class="signer-info">
        <div class="signer-name">{{ $data['firmante_nombre'] ?? 'Msc. Olga Y. Pereira J.' }}</div>
        <div class="signer-cargo">{{ $data['firmante_cargo'] ?? 'Presidenta (E) del Instituto Postal Telegráfico de Venezuela' }}</div>
    </div>

    <div class="footer-extras">
        <div><strong>ANEXO:</strong> {{ $data['oficio_carta_anexos_lista'] ?? 'N/A' }}</div>
        
        <div class="initials">
            {{ $data['oficio_carta_visado_part'] ?? 'OP/XX' }}/{{ strtolower(\Carbon\Carbon::now()->translatedFormat('F')) }}.{{ \Carbon\Carbon::now()->format('Y') }}
        </div>

        @if(!empty($data['oficio_carta_cc']))
            <div style="margin-top: 5px;"><strong>c.c.</strong> {{ $data['oficio_carta_cc'] }}</div>
        @endif
    </div>

    <div class="footer-address">Av. José Ángel Lamas, Centro Postal Caracas, San Martín, Piso 3, Caracas, 1020. Teléfono (58) 212-405-3203, www.ipostel.gob.ve</div>
</body>
</html>
