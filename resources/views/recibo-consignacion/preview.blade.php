@extends('layouts.app_yield')

@section('titulo')
    Previsualización Recibo de consignación
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header con botones de acción -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Previsualización Recibo de consignación</h1>
                <p class="text-sm text-gray-600 mt-1">Vista previa del documento que se generará en PDF (media hoja A4)</p>
            </div>
            <div class="flex gap-3">
                <button type="button" id="printPreview"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </button>
                <a href="{{ route('recibo-consignacion.pdf', $envio->envio_id) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Descargar PDF
                </a>
            </div>
        </div>

        <!-- Contenedor del documento - Media hoja A4 -->
        <div class="flex justify-center">
            <div class="bg-white shadow-lg border border-gray-200" style="width: 210mm; height: 148.5mm; max-width: 100%;">
                <!-- Documento principal -->
                <div id="contentToPrint" class="h-full flex flex-col">
                    <!-- Header del documento -->
                    <div class="bg-white border-b-2 border-gray-200 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-gray-900 uppercase">
                                Recibo de consignación
                                @if($envio->servicio && $envio->servicio->servicio_id == 1)
                                    <span class="inline-flex items-center px-1.5 py-0.5 ml-2 text-xs font-medium bg-green-100 text-green-800 rounded">
                                        EMS
                                    </span>
                                @endif
                            </h2>
                        </div>
                    </div>

                    <!-- Sección de datos de oficina -->
                    <div class="flex border-b border-gray-200">
                        <!-- Datos de oficina -->
                        <div class="w-1/2 p-2">
                            @php($of = $envio->oficinaOrigen)
                            <div class="overflow-hidden">
                                <div class="bg-red-700 text-white px-2 py-1 text-xs font-bold uppercase">
                                    Datos oficina
                                </div>
                                <table class="w-full text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 34%">Oficina de origen (1.1)</th>
                                            <th class="border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">Fecha de consignación (1.2)</th>
                                            <th class="border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">Hora de consignación (1.3)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-gray-300 px-1 py-1">{{ $of->nombre ?? 'N/A' }}</td>
                                            <td class="border border-gray-300 px-1 py-1">{{ optional($envio->created_at)->format('d/m/Y') }}</td>
                                            <td class="border border-gray-300 px-1 py-1">{{ optional($envio->created_at)->format('H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Código de barras -->
                        <div class="w-1/2 p-2">
                            <div class="overflow-hidden">
                                <div class="bg-gray-50 px-2 py-1 text-xs font-semibold">
                                    Código de barras (1.4)
                                </div>
                                <div class="border border-gray-300 border-t-0 p-1 text-right">
                                    @if (!empty($barcode))
                                        <img src="{{ $barcode }}" alt="barcode" class="h-10 mx-auto">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de datos del remitente y destinatario -->
                    <div class="flex-1">
                        <div class="bg-red-700 text-white px-2 py-1 text-xs font-bold uppercase">
                            Datos del remitente y destinatario
                        </div>
                        
                        <!-- Tabla principal de remitente y destinatario -->
                        <div class="overflow-hidden">
                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr>
                                        <th class="bg-red-700 text-white px-1 py-1 text-left font-bold border border-red-700" colspan="2">Remitente</th>
                                        <th class="bg-red-700 text-white px-1 py-1 text-left font-bold border border-red-700" colspan="2">Destinatario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Nombre y Apellido</th>
                                        <td class="border border-gray-300 px-1 py-1">{{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</td>
                                        <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Nombre y Apellido</th>
                                        <td class="border border-gray-300 px-1 py-1">{{ $envio->nombre_dest }} {{ $envio->apellido_dest }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Documento</th>
                                        <td class="border border-gray-300 px-1 py-1">{{ $envio->tipo_documento_rem }}-{{ $envio->documento_rem }}</td>
                                        <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Documento</th>
                                        <td class="border border-gray-300 px-1 py-1">{{ $envio->tipo_documento_dest }}-{{ $envio->documento_dest }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Direcciones -->
                        <div class="flex">
                            @php(
                                $remMunicipioM = \App\Models\Municipio::find($envio->municipio_rem)
                            )
                            @php(
                                $destMunicipioM = \App\Models\Municipio::find($envio->municipio_dest)
                            )
                            @php(
                                $remCiudadM = !empty($envio->ciudad_rem) ? \App\Models\Ciudad::find($envio->ciudad_rem) : null
                            )
                            @php(
                                $destCiudadM = !empty($envio->ciudad_dest) ? \App\Models\Ciudad::find($envio->ciudad_dest) : null
                            )
                            @php(
                                $remCiudadNombre = optional($remCiudadM)->nombre ?: optional($remMunicipioM)->nombre
                            )
                            @php(
                                $destCiudadNombre = optional($destCiudadM)->nombre ?: optional($remMunicipioM)->nombre
                            )

                            <!-- Dirección del remitente -->
                            <div class="w-1/2 border-r border-gray-200">
                                <div class="overflow-hidden">
                                    <table class="w-full text-xs border-collapse">
                                        <tbody>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Dirección / Address (6)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1 h-4">{{ $envio->direccion_rem }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 34%">Ciudad / City (7)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">País / Country (8)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">Zona Postal / Postcode (9)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1">{{ $remCiudadNombre ?? '' }}</td>
                                                <td class="border border-gray-300 px-1 py-1">VENEZUELA</td>
                                                <td class="border border-gray-300 px-1 py-1">{{ $envio->codigo_postal_rem ?? 'N/A'}}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 50%">Número de teléfono / Contact number (10)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 50%" colspan="2">Correo Electrónico / Email (11)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1">{{ $envio->telefono_rem }}</td>
                                                <td class="border border-gray-300 px-1 py-1" colspan="2">{{ $envio->correo_rem }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Dirección del destinatario -->
                            <div class="w-1/2">
                                <div class="overflow-hidden">
                                    <table class="w-full text-xs border-collapse">
                                        <tbody>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Dirección / Address (14)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1 h-4">{{ $envio->direccion_dest }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 34%">Ciudad / City (15)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">País / Country (16)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 33%">Zona Postal / Postcode (17)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1">{{ $destCiudadNombre ?? 'N/A' }}</td>
                                                <td class="border border-gray-300 px-1 py-1">VENEZUELA</td>
                                                <td class="border border-gray-300 px-1 py-1">{{ $envio->codigo_postal_dest ?? 'N/A'  }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 50%">Número de teléfono / Contact number (18)</th>
                                                <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 50%" colspan="2">Correo Electrónico / Email (19)</th>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 px-1 py-1">{{ $envio->tlf_dest }}</td>
                                                <td class="border border-gray-300 px-1 py-1" colspan="2">{{ $envio->correo_dest }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de datos del envío -->
                        <div class="border-t border-gray-200">
                            <div class="bg-red-700 text-white px-2 py-1 text-xs font-bold uppercase">
                                Datos del envío
                                @if($envio->servicio && $envio->servicio->servicio_id == 1)
                                    <span class="ml-1 bg-green-600 text-white px-1 py-0.5 rounded text-xs">EMS</span>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <table class="w-full text-xs border-collapse">
                                    <tbody>
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 25%">Servicio</th>
                                            <td class="border border-gray-300 px-1 py-1" style="width: 25%">{{ optional($envio->servicio)->nombre }}</td>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 25%">Tipo de envío</th>
                                            <td class="border border-gray-300 px-1 py-1" style="width: 25%">{{ strtoupper($envio->tipo_envio) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Peso (g)</th>
                                            <td class="border border-gray-300 px-1 py-1">{{ $envio->peso }}</td>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Contenido</th>
                                            <td class="border border-gray-300 px-1 py-1">{{ $envio->contenido }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Coste</th>
                                            <td class="border border-gray-300 px-1 py-1">{{ $envio->coste }}</td>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Usuario que registra</th>
                                            <td class="border border-gray-300 px-1 py-1">{{ optional($envio->users)->name }}</td>
                                        </tr>
                                        @if($envio->servicio && $envio->servicio->servicio_id == 1)
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Número de seguimiento EMS</th>
                                            <td class="border border-gray-300 px-1 py-1" colspan="3">{{ $envio->codigo_envio ?? 'N/A' }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sección de comprobante de entrega -->
                        <div class="border-t border-gray-200">
                            <div class="bg-red-700 text-white px-2 py-1 text-xs font-bold uppercase">
                                Comprobante de entrega al destinatario
                            </div>
                            <div class="overflow-hidden">
                                <table class="w-full text-xs border-collapse">
                                    <tbody>
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 25%">Nombre y Apellido</th>
                                            <td class="border border-gray-300 px-1 py-1" style="width: 25%"></td>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold" style="width: 25%">N° Cédula / Doc.</th>
                                            <td class="border border-gray-300 px-1 py-1" style="width: 25%"></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Firma</th>
                                            <td class="border border-gray-300 px-1 py-1"></td>
                                            <th class="bg-gray-50 border border-gray-300 px-1 py-1 text-left font-semibold">Fecha y Hora</th>
                                            <td class="border border-gray-300 px-1 py-1"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 border-t-2 border-gray-200 px-2 py-1 mt-auto">
                        <p class="text-xs text-gray-600">
                            Este documento es una previsualización. El PDF descargado contendrá la misma estructura.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printPreview').addEventListener('click', function() {
            var element = document.getElementById('contentToPrint');
            html2pdf()
                .from(element)
                .set({
                    filename: 'recibo-consignacion.pdf',
                    margin: 0,
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: [210, 148.5],
                        orientation: 'portrait'
                    }
                })
                .toPdf()
                .get('pdf')
                .then(function(pdf) {
                    pdf.autoPrint();
                    var pdfDataUrl = pdf.output('bloburl');
                    var iframe = document.createElement('iframe');
                    iframe.style.position = 'absolute';
                    iframe.style.width = '0px';
                    iframe.style.height = '0px';
                    iframe.style.border = 'none';
                    iframe.src = pdfDataUrl;
                    document.body.appendChild(iframe);
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                });
        });
    });
</script>
@endsection