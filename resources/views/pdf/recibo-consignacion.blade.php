<html>

<head>
    <meta charset="utf-8">
    <title>Recibo de consignación</title>
    <style>
        @page {
            margin: 12mm 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background: #ffffff;
            margin: 0;
            padding: 0
        }

        .rc-container {
            width: 100%;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            overflow: hidden;
            height: auto;
        }

        .rc-header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px 16px;
            text-align: center;
        }

        .rc-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase
        }

        .rc-section-title {
            background: #b91c1c;
            color: #fff;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 10px;
            color: #111827;
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        th {
            background: #f9fafb;
            font-weight: 700;
            text-align: left
        }

        .barcode {
            margin-top: 6px
        }

        .muted {
            color: #6b7280
        }

        .rc-right {
            text-align: right
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .cell-label {
            font-weight: 700;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .cell-value {
            font-size: 11px;
        }

        .half-left {
            width: 50%;
            float: left;
            box-sizing: border-box;
            padding-right: 6px
        }

        .half-right {
            float: right;
            box-sizing: border-box;
            padding-left: 6px
        }
    </style>
</head>

<body>

    @php($of = $envio->oficina_id)
    <div class="rc-container">

        <table style="width:100%; border-collapse:collapse;">
            @php($remMunicipioM = \App\Models\Municipio::find($envio->municipio_rem))
            @php($destMunicipioM = \App\Models\Municipio::find($envio->municipio_dest))
            @php($remCiudadM = (is_numeric($envio->ciudad_rem) && !empty($envio->ciudad_rem)) 
                    ? \App\Models\Ciudad::find((int)$envio->ciudad_rem) 
                    : null)
            @php($destCiudadM = !empty($envio->ciudad_dest) ? \App\Models\Ciudad::find($envio->ciudad_dest) : null)
            @php($remCiudadNombre = optional($remCiudadM)->nombre ?: optional($remMunicipioM)->nombre)
            @php($remEstadoM = \App\Models\Estado::find($envio->estado_rem))
            @php($destEstadoM = \App\Models\Estado::find($envio->estado_dest))
            @php($destCiudadNombre = optional($destCiudadM)->nombre ?: optional($destMunicipioM)->nombre)
            @php($remparroquiaM = \App\Models\Parroquia::find($envio->parroquia_rem))
            @php($iva = ($envio->coste * 0.16) / 1.16)
            @php($precio_base = $envio->coste ? round($envio->coste / 1.16, 2) : 0)
            @php($title = 'Recibo de consignación - ' . ($envio->servicio->nombre ?? ''))


            <tr>
                <td colspan="4">
                    <div class="rc-header">
                        <div class="rc-title">{{ $title }}</div>
                    </div>
                </td>
                <td colspan="3" rowspan="3">
                    <div class="cell-label">Código de barras (1.4)</div>
                    <span class="half-right"><br>
                        @if (!empty($barcodeFilePath))
                            <img src="file://{{ $barcodeFilePath }}" alt="barcode" width="500">
                        @endif
                    </span>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="rc-section-title">DATOS OFICINA (1)</th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Oficina de origen (1.1)</div>
                    <span class="cell-value">{{ $envio->oficinas->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Fecha de consignación (1.2)</div><span
                        class="cell-value">{{ optional($envio->created_at)->format('d/m/Y') }}</span>
                </td>
                <td>
                    <div class="cell-label">Hora de consignación (1.3)</div><span
                        class="cell-value">{{ optional($envio->created_at)->format('H:i') }}</span>
                </td>
            </tr>
            <tr>
                <th colspan="3" class="rc-section-title">DATOS DEL REMITENTE (2)</th>
                <th colspan="4" class="rc-section-title">DATOS DEL DESTINATARIO (3)</th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Nombre y Apellido / Razón Social (2.1)</div>
                    <span class="cell-value">{{ $envio->nombre_rem ?? '' }} {{ $envio->apellido_rem ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Número de Cédula / Rif (2.2)</div>
                    <span class="cell-value">{{ $envio->documento_rem ?? '' }}</span>
                </td>
                <td colspan="3">
                    <div class="cell-label">Nombre y Apellido / Razón Social (3.1)</div>
                    <span class="cell-value">{{ $envio->nombre_dest ?? '' }}
                        {{ $envio->apellido_dest ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Número de Cédula / Rif (3.2)</div>
                    <span class="cell-value">{{ $envio->documento_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Dirección de Remitente (2.3)<br>Localidad Inmueble</div>
                    <span class="cell-value">{{ $envio->direccion_rem ?? '' }}</span>
                </td>
                <td colspan="4" rowspan="2">
                    <div class="cell-label">Dirección de Destinatario (3.3)<br>Localidad, Inmueble u Oficina de
                        destino</div>
                    <span class="cell-value">{{ $envio->direccion_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="cell-label">Parroquia (2.4)</div>
                    <span class="cell-value">{{ optional($remparroquiaM)->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Municipio (2.5)</div>
                    <span class="cell-value">{{ optional($remMunicipioM)->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Ciudad o pueblo (2.6)</div>
                    <span class="cell-value">{{ $remCiudadNombre }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Estado (2.7)</div>
                    <span class="cell-value">{{ optional($remEstadoM)->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Zona postal (2.8)</div>
                    <span class="cell-value">{{ $envio->codigo_postal_rem ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Parroquia (3.4)</div>
                    <span
                        class="cell-value">{{ optional(\App\Models\Parroquia::find($envio->parroquia_dest))->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Municipio (3.5)</div>
                    <span class="cell-value">{{ optional($destMunicipioM)->nombre ?? '' }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">Ciudad o pueblo (3.6)</div>
                    <span class="cell-value">{{ $destCiudadNombre }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Teléfono celular o local (2.9)</div>
                    <span class="cell-value">{{ $envio->telefono_rem ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Correo Electrónico (2.10)</div>
                    <span class="cell-value">{{ $envio->correo_rem ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Estado (3.7)</div>
                    <span class="cell-value">{{ optional($destEstadoM)->nombre ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Zona postal (3.8)</div>
                    <span class="cell-value">{{ $envio->codigo_postal_dest ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Teléfono celular o local (3.9)</div>
                    <span class="cell-value">{{ $envio->tlf_dest ?? '' }}</span>
                </td>

            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Firma del Remitente (2.11)</div>
                    <span class="cell-value"></span>
                </td>
                <td colspan="4">
                    <div class="cell-label">Correo Electrónico (3.10)</div>
                    <span class="cell-value">{{ $envio->correo_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="rc-section-title">
                    SOLO USO PERSONAL IPOSTEL (4)
                </th>
                <th colspan="3" class="rc-section-title" style="text-align:center;">
                    DATOS DE LA ENTREGA (5)
                </th>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="cell-label">Tipo de Envío (4.1)</div>
                    <input type="checkbox" {{ $envio->tipo_envio == 'documento' ? 'checked' : '' }}> Documento<br>
                    <input type="checkbox" {{ $envio->tipo_envio == 'encomienda' ? 'checked' : '' }}> Encomienda
                </td>
                <td rowspan="2">
                    <div class="cell-label">Peso volumétrico / Volumetric weight (4.2)</div>
                    <span style="border-bottom:1px solid #2563eb;">L &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;xAN
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;xAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <br>Pv = <span style="border-bottom:1px solid #2563eb;">{{ $envio->peso_volumetrico ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Peso (Kg) (4.3)</div>
                    <span class="cell-value">{{ number_format($envio->peso / 1000, 2, ',', '.') }}</span>

                </td>

                <td>
                    <div class="cell-label">Precio base (4.5)</div>
                    <span class="cell-value">{{ number_format($precio_base, 2, ',', '.') }}</span>
                </td>

                <td>
                    <div class="cell-label">Fecha de entrega (5.1)</div>
                    <span class="cell-value"></span>
                </td>
                <td>
                    <div class="cell-label">Hora de entrega (5.2)</div>
                    <span class="cell-value"></span>
                </td>
                <td rowspan="4">
                    <div class="cell-label">Datos persona que recibe (5.4)</div><br>
                    <div style="margin-top:10px;text-align:center;">
                        <span style=" width:48%;text-align:center;">_______________________<br>
                            <span style="font-size:10px;text-align:center;">Nombre y Apellido</span>
                        </span><br><br><br>
                        <span style="width:48%;text-align:center;">_______________________<br>
                            <span style="font-size:10px;text-align:center;">Nº Cédula de Identidad</span>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="cell-label">Seguro (4.4)</div>
                    <span class="cell-value">{{ $envio->seguro ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">IVA (4.5)</div>
                    <span class="cell-value">{{ number_format($iva, 2, ',', '.') }}</span>
                </td>
                <td rowspan="3" colspan="2">
                    <div class="cell-label">Datos del personal de Ipostel que realiza la entrega (5.3)</div>
                    <div style="margin-top:10px;text-align:center;">
                        <span style=" width:48%; text-align:center;">_____________________________<br>
                            <span style="font-size:10px;text-align:center;">Nombre y Apellido</span>
                        </span><br><br><br>
                        <span style="width:48%;text-align:center;">_____________________________<br>
                            <span style="font-size:10px;text-align:center;">Nº Cédula de Identidad</span>
                        </span>
                    </div>
                </td>

            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Descripción Contenido del envío (4.8)</div>
                    <span class="cell-value">{{ $envio->contenido ?? '' }}</span>
                </td>
                <td rowspan="2">
                    <div class="cell-label">Total a pagar (4.7)</div>
                    <span class="cell-value">{{ $envio->coste ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Nombre Operador Postal (Taquillero) (4.9)</div>
                    <span class="cell-value">{{ optional($envio->users)->name ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <div class="rc-header">
                        <div class="rc-title">Comprobante de entrega al Destinatario</div>
                    </div>
                </td>
            </tr>
            <!-- Puedes seguir agregando filas para las demás secciones -->
        </table>
    </div>
</body>

</html>