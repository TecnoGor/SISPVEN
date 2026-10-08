@php
    $remMunicipioM = \App\Models\Municipio::find($envio->municipio_rem);
    $destMunicipioM = \App\Models\Municipio::find($envio->municipio_dest);
    $remCiudadM = !empty($envio->ciudad_rem) ? \App\Models\Ciudad::find($envio->ciudad_rem) : null;
    $destCiudadM = !empty($envio->ciudad_dest) ? \App\Models\Ciudad::find($envio->ciudad_dest) : null;
    $destpaisM = \App\Models\Pais::find($envio->pais_dest);
    $remCiudadNombre = optional($remCiudadM)->nombre ?: optional($remMunicipioM)->nombre;
    $destCiudadNombre = optional($destCiudadM)->nombre ?: optional($destMunicipioM)->nombre;
    $remparroquiaM = \App\Models\Parroquia::find($envio->parroquia_rem);
    $iva = $envio->coste ? round(($envio->coste * 0.16) / 1.16, 2) : 0;
    $precio_base = $envio->coste ? round($envio->coste / 1.16, 2) : 0;
@endphp

<html>

<head>
    <meta charset="utf-8">
    <title>Recibo EMS</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            font-size: 10px;
            color: #374151;
        }

        th {
            background: #e5e7eb;
            color: #1f2937;
            font-weight: bold;
            text-align: left;
        }

        .rc-container {
            width: 100%;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .rc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 16px;
        }

        .rc-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }

        .section-title {
            background: #f97316;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            padding: 4px 8px;
        }

        .cell-label {
            font-weight: bold;
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 0;
            /* <--- Cambiado de 2px a 0 */
            line-height: 1.1;
        }

        .checkbox-label {
            font-size: 8px;
            color: #374151;
            vertical-align: middle;
            white-space: nowrap;
            line-height: 1.1;
        }

        .checkbox-label input[type="checkbox"] {
            vertical-align: middle;
            margin: 0 2px 0 0;
            width: 10px;
            height: 10px;
        }

        .cell-value {
            font-size: 11px;
            color: #374151;
        }

        .barcode {
            margin-top: 6px;
            display: block;
            text-align: center;
        }

        .no-border {
            border: none !important;
        }

        .grid {
            display: grid;
            gap: 0;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        .p-2 {
            padding: 8px;
        }

        .border {
            border: 1px solid #d1d5db;
        }

        .border-b {
            border-bottom: 1px solid #d1d5db;
        }

        .border-r {
            border-right: 1px solid #d1d5db;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .text-xs {
            font-size: 10px;
        }

        .text-sm {
            font-size: 12px;
        }

        .text-lg {
            font-size: 18px;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-gray-900 {
            color: #1f2937;
        }

        .text-orange-600 {
            color: #ea580c;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .rounded-lg {
            border-radius: 8px;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="rc-container">
        <table>
            <tr>
                <td colspan="9">
                    <div class="rc-header">
                        <div class="rc-title">Recibo de consignación EMS</div>
                        <span style="font-size:13px;">Venezuela - Ipostel - www.ipostel.gob.ve</span>
                    </div>
                </td>

                <td>
                    <div class="cell-label">Código de barras (1.4)</div>
                    <span class="half-right cell-value"><br>
                        @if (!empty($barcodeFilePath))
                            <img src="file://{{ $barcodeFilePath }}" alt="barcode" width="200">
                        @endif
                    </span>
                </td>
            </tr>
            <tr>
                <th colspan="5" class="section-title">REMITENTE / SENDER</th>
                <th colspan="5" class="section-title">DESTINATARIO / ADDRESSEE</th>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="cell-label">Nombre / Name (4)</div>
                    <span class="cell-value">{{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</span>
                </td>
                <td>
                    <div class="cell-label">Oficina de Consignación / Receiving PO (5)</div>
                    <span class="cell-value">{{ $envio->oficinaOrigen->nombre ?? '' }}</span>
                </td>
                <td colspan="3">
                    <div class="cell-label">Nombre / Name (12)</div>
                    <span class="cell-value">{{ $envio->nombre_dest }} {{ $envio->apellido_dest }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">
                        <input type="checkbox">
                        Devolver al Remitente /return to recipient PO
                        &nbsp;&nbsp;&nbsp;&nbsp;(13)
                        <br>
                    </div>
                    <div class="cell-label">
                        <input type="checkbox"> Encomienda
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="cell-label">Dirección / Address (6)</div>
                    <span class="cell-value">{{ $envio->direccion_rem ?? '' }}</span>
                </td>
                <td colspan="5">
                    <div class="cell-label">Dirección / Address (14)</div>
                    <span class="cell-value">{{ $envio->direccion_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Ciudad / City (7)</div>
                    <span class="cell-value">{{ $remCiudadNombre ?? '' }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">País / Country (8)</div>
                    <span class="cell-value">VENEZUELA</span>
                </td>
                <td>
                    <div class="cell-label">Zona Postal / Postcode (9)</div>
                    <span class="cell-value">{{ $envio->codigo_postal_rem ?? '' }}</span>
                </td>
                <td>
                    <div class="cell-label">Ciudad / City (15)</div>
                    <span class="cell-value">{{ $destCiudadNombre ?? '' }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">País / Country (16)</div>
                    <span class="cell-value">{{ $destpaisM->nombre ?? '' }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">Zona Postal / Postcode (17)</div>
                    <span class="cell-value">{{ $envio->codigo_postal_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Número de teléfono / Contact number (10)</div>
                    <span class="cell-value">{{ $envio->telefono_rem ?? '' }}</span>
                </td>
                <td colspan="3">
                    <div class="cell-label">Correo Electrónico (2.10)</div>
                    <span class="cell-value">{{ $envio->correo_rem ?? '' }}</span>
                </td>
                <td colspan="2">
                    <div class="cell-label">Número de teléfono / Contact number (18)</div>
                    <span class="cell-value">{{ $envio->tlf_dest ?? '' }}</span>
                </td>
                <td colspan="3">
                    <div class="cell-label">Correo Electrónico / Email (19)</div>
                    <span class="cell-value">{{ $envio->correo_dest ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th colspan="10" class="section-title">DECLARACIÓN DE ADUANA / CUSTOMS DECLARATION</th>
            </tr>
            <tr>
                <td colspan="6" style="padding: 5px;">
                    <div class="cell-label" style="margin-bottom: 2px;">Contenido / contents (20)</div>
                    <div style="white-space: normal; padding: 2px 0;">
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:40px;">
                            <span class="checkbox-label"
                                style="font-size:7px; text-align:right; line-height:1; margin-right:2px;">
                                Doc<br>/Doc.
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px; margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:40px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:-2px;">
                                Merc<br>/Merch.
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px;margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:44px;">
                            <span class="checkbox-label" style="font-size:6px; text-align:right; line-height:1;">
                                Muestras<br>/Samples
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px;margin-top:-20px;">
                        </span>
                        <span
                            style="display:inline-flex; align-items:center; margin-right:4px; min-width:36px; margin-left:2px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:-1px;">
                                Regalo<br>/Gift
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px;margin-top:-20px; margin-left:5px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:48px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:2px;">
                                Ref.Prod<br>/Ref.Goods
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px;margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:60px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:2px;">
                                Doc.Anex<br>/Doc.Attach. (21)
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px;margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:36px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:2px;">
                                Fact<br>/Invol.
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px; margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:36px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:2px;">
                                Cert<br>/Cert.
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px; margin-top:-20px;">
                        </span>
                        <span style="display:inline-flex; align-items:center; margin-right:4px; min-width:48px;">
                            <span class="checkbox-label"
                                style="font-size:6px; text-align:right; line-height:1; margin-right:2px;">
                                Licencia<br>/License
                            </span>
                            <input type="checkbox"
                                style="vertical-align:middle; width:9px; height:9px; margin-top:-20px;">
                        </span>
                    </div>
                </td>
                <td colspan="2">
                    <div class="cell-label">Peso volumétrico / Volumetric weight (29)</div><br>
                    <span style="border-bottom:1px solid #2563eb;">
                        L &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        xAN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        xAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>=</span>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6000
                </td>
                <td colspan="2">
                    <div class="cell-label">Peso del Envío (Kg) / Item weight (Kg) (30)</div>
                    <span class="cell-value">{{ number_format($envio->peso / 1000, 2, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="cell-label">Descripción Detallada de c/art. Detailed description eac/item (22)
                    </div>
                </td>
                <td>
                    <div class="cell-label">Cantidad /Quantity (Kg) (23)</div>
                    <span class="cell-value">{{ $envio->cantidad ?? 'N/A' }}</span>
                </td>
                <td>
                    <div class="cell-label">Peso c/art. / weight eac/item (Kg) (24)</div>
                    <span class="cell-value">{{ number_format($envio->peso / 1000, 2, ',', '.') }}</span>
                </td>
                <td>
                    <div class="cell-label">Valor /Value (Kg) (25)</div>
                    <span class="cell-value">{{ number_format($envio->coste ?? 0, 2, ',', '.') }}</span>
                </td>
                <td>
                    <div class="cell-label">Código HS/HS (Code) (Kg) (26)</div>
                    <span class="cell-value">{{ $envio->codigo_hs ?? 'N/A' }}</span>
                </td>
                <td>
                    <div class="cell-label">País de Elaboración / Country of Origin (27)</div>
                    <span class="cell-value">VENEZUELA</span>
                </td>
                <td>
                    <div class="cell-label">Tasas totales (31)</div><span class="cell-value">&nbsp;&nbsp;</span>
                </td>
                <td>
                    <div class="cell-label">Seguro (32)</div><span class="cell-value"></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="cell-value">
                        {{ $envio->contenido ?? '' }}
                    </span>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2">
                    <div class="cell-label">Tasas</div>
                    <span class="cell-value"></span>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2">
                    <div class="cell-label">Oficina de origen (34)</div><span
                        class="cell-value">{{ $envio->oficinaOrigen->nombre ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div class="cell-label">Fecha/Hora (35/36)</div><span
                        class="cell-value">{{ optional($envio->created_at)->format('d/m/Y') }}</span>
                </td>
                <td>
                    <div class="cell-label">Fecha/Hora (37/38)</div>
                    <span class="cell-value">{{ optional($envio->created_at)->format('H:i') }}</span>
                </td>

            </tr>
            <tr>
                <td colspan="4">
                    <div class="cell-label" style="text-align: right">
                        Total
                    </div>
                </td>
                <td></td>
                <td></td>
                <td colspan="2"></td>
                <th colspan="2" class="section-title">INFORMACIÓN DE DISTRIBUCIÓN / DELIVERY INFORMATION</th>


            </tr>
            <tr>
                <th colspan="8" " class="section-title">RESPONSABILIDAD/LIABILITY</th>
                <td>
                    <div class="cell-label">Fecha/Hora (35/36)</div><span
                        class="cell-value">{{ optional($envio->created_at)->format('d/m/Y') }}</span>
                </td>
                <td>
                    <div class="cell-label">Fecha/Hora (37/38)</div>
                    <span class="cell-value">{{ optional($envio->created_at)->format('H:i') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="8" rowspan="2" style="font-size:9px; text-align:left;">
                    <span class="cell-value">Por medio de la presente certifico que los detalles indicados en la
                        presente declaración de aduana
                        son
                        ciertos, así como este envío no contiene objetos peligrosos o prohibidos según lo establecido
                        por
                        las
                        leyes de aduana de la Unión Postal Universal (UPU).</span>
                        <div style="white-space: nowrap; margin-top: 10px;">
                            <div style="display: inline-block; min-width: 90px; vertical-align: top;">
                                <div class="cell-label">Fecha (42)</div>
                                <span class="cell-value"></span>
                            </div>
                            <div style="display: inline-block; min-width: 90px; margin-left: 8rem; vertical-align: top;">
                                <div class="cell-label">Firma (43)</div>
                                <span class="cell-value"></span>
                            </div>
                        </div>
                </td>
                <td colspan="2">
                    <div class="cell-label">Nombre persona que recibe (39)</div><span class="cell-value"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="cell-label">Firma (40)</div><span class="cell-value"></span>
                </td>
            </tr>
            <tr>
                <th colspan=10" class="section-title" style="background:#f3f4f6; color:#111827;">COMPROBANTE DE
                    ENTREGA AL DESTINATARIO / PROOF OF DELIVERY</th>
            </tr>
        </table>
</body>

</html>
