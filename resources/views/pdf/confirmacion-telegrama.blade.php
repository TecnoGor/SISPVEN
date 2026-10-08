<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Telegrama {{ $envio->codigo_envio }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; color: #333; }
        .cintillo { width: 100%; margin-bottom: 10px; }
        .titulo { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 15px; color: #002F6C; text-transform: uppercase; }
        .section-title { font-weight: bold; background-color: #f1f5f9; padding: 4px; border: 1px solid #cbd5e1; margin-top: 10px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td { border: 1px solid #cbd5e1; padding: 5px; vertical-align: top; }
        .label { font-weight: bold; width: 30%; background-color: #f8fafc; }
        .content { width: 70%; }
        .text-center { text-align: center; }
        .mensaje-box { border: 1px solid #cbd5e1; padding: 10px; min-height: 100px; background-color: #fff; margin-bottom: 10px; font-family: 'Courier New', Courier, monospace; font-size: 12px;}
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    @php
        $tipo_remitente = \App\Models\TipoRemitenteTelegrama::where('tipos_remitente_telegramas_id', $telegramaRecibido->tipo_remitente)->value('nombre') ?? 'N/A';
        $lugar_emision = \App\Models\LugarEmisionTelegrama::where('lugar_emision_telegramas_id', $telegramaRecibido->lugar_emision_rem)->value('nombre') ?? 'N/A';
        $sitio_emision = \App\Models\CircuitoJudicialTribunalTelegrama::where('circuito_judicial_tribunal_telegramas_id', $telegramaRecibido->sitio_especifico_emision)->value('nombre') ?? 'N/A';
        $circuito_dest = \App\Models\CircuitoJudicialTribunalTelegrama::where('circuito_judicial_tribunal_telegramas_id', $telegramaRecibido->circuito_judicial_dest)->value('nombre') ?? 'N/A';
        $oficina_destino = \App\Models\Oficina::find($envio->oficina_dest_id)?->nombre ?? 'N/A';
    @endphp

    <img src="{{ public_path('images/cintillo.jpg') }}" class="cintillo">
    
    <div class="titulo">TELEGRAMA: {{ $envio->codigo_envio }}</div>

    <table style="width: 100%; border: none; margin-bottom: 10px; padding: 0;">
        <tr>
            <td style="width: 49%; vertical-align: top; border: none; padding: 0; padding-right: 1%;">
                <div class="section-title">DATOS DEL REMITENTE (ORIGEN)</div>
                <table>
                    <tr>
                        <td class="label">Nombre:</td>
                        <td class="content">{{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</td>
                    </tr>
                    <tr>
                        <td class="label">Doc:</td>
                        <td class="content">{{ $envio->tipo_documento_rem }}-{{ $envio->documento_rem }}</td>
                    </tr>
                    <tr>
                        <td class="label">Telf:</td>
                        <td class="content">{{ $envio->telefono_rem }}</td>
                    </tr>
                    <tr>
                        <td class="label">Correo:</td>
                        <td class="content">{{ $envio->correo_rem ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Oficina:</td>
                        <td class="content">{{ $envio->oficinas->nombre ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td class="content">{{ $envio->direccion_rem }}</td>
                    </tr>
                </table>
            </td>
            
            <td style="width: 49%; vertical-align: top; border: none; padding: 0; padding-left: 1%;">
                <div class="section-title">DATOS DEL DESTINATARIO (DESTINO)</div>
                <table>
                    <tr>
                        <td class="label">Nombre:</td>
                        <td class="content">{{ $envio->nombre_dest }} {{ $envio->apellido_dest }}</td>
                    </tr>
                    <tr>
                        <td class="label">Doc:</td>
                        <td class="content">{{ $envio->tipo_documento_dest }}-{{ $envio->documento_dest }}</td>
                    </tr>
                    <tr>
                        <td class="label">Telf:</td>
                        <td class="content">{{ $envio->tlf_dest }}</td>
                    </tr>
                    <tr>
                        <td class="label">Correo:</td>
                        <td class="content">{{ $envio->correo_dest ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Oficina:</td>
                        <td class="content">{{ $oficina_destino }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dirección:</td>
                        <td class="content">{{ $envio->direccion_dest }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">DETALLES DE EMISIÓN JUDICIAL Y TARIFA</div>
    <table>
        <tr>
            <td class="label" style="width: 20%;">Tipo Remitente:</td>
            <td class="content" style="width: 30%;">{{ $tipo_remitente }}</td>
            <td class="label" style="width: 20%;">Lugar de Emisión:</td>
            <td class="content" style="width: 30%;">{{ $lugar_emision }}</td>
        </tr>
        <tr>
            <td class="label">Sitio Específico:</td>
            <td class="content">{{ $sitio_emision }}</td>
            <td class="label">Circuito Destino:</td>
            <td class="content">{{ $circuito_dest }}</td>
        </tr>
    </table>

    <div class="section-title">CONTENIDO DEL TELEGRAMA</div>
    <div class="mensaje-box">
        {!! nl2br(e($telegramaRecibido->contenido_telegrama)) !!}
    </div>

    <div class="footer">
        Generado por Ipostel - {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>