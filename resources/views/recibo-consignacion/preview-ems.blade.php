@extends('layouts.app_yield')

@section('titulo')
    Previsualización Recibo de consignación EMS
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header con botones de acción -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Previsualización Recibo de consignación EMS</h1>
                    <p class="text-sm text-gray-600 mt-1">Vista previa del formulario EMS (media hoja en alto)</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="printPreviewEms"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Imprimir
                    </button>
                    <a href="{{ route('recibo-consignacion.pdf', $envio->envio_id) }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Descargar PDF
                    </a>
                </div>
            </div>

            <!-- Contenedor de media hoja (alto) -->
            <div class="flex justify-center">
                <div id="emsContainer" class="bg-white shadow-lg border-2 border-gray-800"
                    style="width: 210mm; height: 148.5mm; max-width: 100%; overflow: hidden; position: relative;">
                    <style>
                        .ems-scaled {
                            position: absolute;
                            top: 0;
                            width: 396.5mm;
                            margin-top: -1rem;
                            transform-origin: top left;
                            /* contenido original pensado para A4 */
                        }
                    </style>
                    <!-- Documento principal escalado para caber en media hoja de alto -->
                    <div id="contentToPrintEms" class="ems-scaled">
                        <!-- Header con logos y tracking -->
                        <div class="flex justify-between items-start p-2 border-b-2 border-gray-800">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="text-orange-600 text-lg font-bold">EMS</div>
                                    <div class="text-xs text-gray-600">Venezuela - ipostel - www.ipostel.gob.ve</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-blue-600 text-sm font-bold">Ipostel</div>
                                <div class="text-sm font-bold">{{ $envio->codigo_envio ?? 'EE002978928VE' }}</div>
                                <div class="mt-1">
                                    @if (!empty($barcode))
                                        <img src="{{ $barcode }}" alt="barcode" class="h-8 mx-auto">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sección Remitente -->
                        <div class="border-b border-gray-300">
                            <div class="bg-gray-100 font-bold p-2 text-xs border-b border-gray-300">
                                REMITENTE / SENDER
                            </div>
                            <div class="grid grid-cols-3 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Nombre / Name (4)</div>
                                    <div class="text-sm">{{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Oficina de Consignación / Receiving PO (5)</div>
                                    <div class="text-sm">{{ $envio->oficinaOrigen->nombre ?? 'N/A' }}</div>
                                </div>
                                <div class="p-2">
                                    <div class="text-xs font-bold">Dirección / Address (6)</div>
                                    <div class="text-sm">{{ $envio->direccion_rem }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Ciudad / City (7)</div>
                                    <div class="text-sm">
                                        @php($remMunicipioM = \App\Models\Municipio::find($envio->municipio_rem))
                                        @php($remCiudadM = !empty($envio->ciudad_rem) ? \App\Models\Ciudad::find($envio->ciudad_rem) : null)
                                        @php($remCiudadNombre = optional($remCiudadM)->nombre ?: optional($remMunicipioM)->nombre)
                                        {{ $remCiudadNombre ?? '' }}
                                    </div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">País / Country (8)</div>
                                    <div class="text-sm">VENEZUELA</div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Zona Postal / Postcode (9)</div>
                                    <div class="text-sm">{{ $envio->codigo_postal_rem ?? 'N/A' }}</div>
                                </div>
                                <div class="p-2">
                                    <div class="text-xs font-bold">Teléfono / Contact (10)</div>
                                    <div class="text-sm">{{ $envio->telefono_rem }}</div>
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-300">
                                <div class="text-xs font-bold">Correo Electrónico / Email (11)</div>
                                <div class="text-sm">{{ $envio->correo_rem }}</div>
                            </div>
                        </div>

                        <!-- Sección Destinatario -->
                        <div class="border-b border-gray-300">
                            <div class="bg-gray-100 font-bold p-2 text-xs border-b border-gray-300">
                                DESTINATARIO / ADDRESSEE
                            </div>
                            <div class="grid grid-cols-2 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Nombre / Name (12)</div>
                                    <div class="text-sm">{{ $envio->nombre_dest }} {{ $envio->apellido_dest }}</div>
                                </div>
                                <div class="p-2">
                                    <div class="text-xs font-bold">Opciones (13)</div>
                                    <div class="flex flex-col gap-1 mt-1">
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3">
                                            Devolver al Remitente / Return to recipient
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3">
                                            Tratar como abandono en caso de no efectuarse la entrega
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-300">
                                <div class="text-xs font-bold">Dirección / Address (14)</div>
                                <div class="text-sm">{{ $envio->direccion_dest }}</div>
                            </div>
                            <div class="grid grid-cols-4 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Ciudad / City (15)</div>
                                    <div class="text-sm">
                                        @php($destMunicipioM = \App\Models\Municipio::find($envio->municipio_dest))
                                        @php($destCiudadM = !empty($envio->ciudad_dest) ? \App\Models\Ciudad::find($envio->ciudad_dest) : null)
                                        @php($destCiudadNombre = optional($destCiudadM)->nombre ?: optional($destMunicipioM)->nombre)
                                        {{ $destCiudadNombre ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">País / Country (16)</div>
                                    <div class="text-sm">VENEZUELA</div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Zona Postal / Postcode (17)</div>
                                    <div class="text-sm">{{ $envio->codigo_postal_dest ?? 'N/A' }}</div>
                                </div>
                                <div class="p-2">
                                    <div class="text-xs font-bold">Teléfono / Contact (18)</div>
                                    <div class="text-sm">{{ $envio->tlf_dest }}</div>
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-300">
                                <div class="text-xs font-bold">Correo Electrónico / Email (19)</div>
                                <div class="text-sm">{{ $envio->correo_dest }}</div>
                            </div>
                        </div>

                        <!-- Declaración de Aduana / Totales -->
                        <div class="border-b border-gray-300">
                            <div class="bg-gray-100 font-bold p-2 text-xs border-b border-gray-300">
                                DECLARACIÓN DE ADUANA / CUSTOMS DECLARATION
                            </div>
                            <div class="grid grid-cols-2 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Contenido / Contents (20)</div>
                                    <div class="grid grid-cols-2 gap-1 mt-2">
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Doc / Doc
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Merc / Merch
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Muestras / Samples
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Regalo / Gift
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Ref. Prod / Ref. Goods
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Doc. Anex / Doc. Attach (21)
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Fact / Cert
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" class="w-3 h-3"> Licencia / License
                                        </label>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <div class="text-xs font-bold">Total / Total (28)
                                    </div>
                                    <div class="text-lg font-bold">{{ $envio->coste ?? '0.00' }}</div>
                                </div>
                            </div>
                            <!-- Tabla simple de artículos -->
                            <div class="border-t border-gray-300">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border border-gray-300 p-1 text-left" style="width: 40%;">
                                                Descripción (22)</th>
                                            <th class="border border-gray-300 p-1 text-left" style="width: 15%;">Cantidad
                                                (23)</th>
                                            <th class="border border-gray-300 p-1 text-left" style="width: 15%;">Peso (24)
                                            </th>
                                            <th class="border border-gray-300 p-1 text-left" style="width: 15%;">Valor
                                                (25)</th>
                                            <th class="border border-gray-300 p-1 text-left" style="width: 15%;">Código HS
                                                (26)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-gray-300 p-1">{{ $envio->contenido ?? '' }}</td>
                                            <td class="border border-gray-300 p-1 text-center">1</td>
                                            <td class="border border-gray-300 p-1 text-center">{{ $envio->peso ?? '0' }}
                                            </td>
                                            <td class="border border-gray-300 p-1 text-center">
                                                {{ $envio->coste ?? '0.00' }}</td>
                                            <td class="border border-gray-300 p-1"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Información de Aceptación / Distribución / Responsabilidad (resumen) -->
                        <div class="border-b border-gray-300">
                            <div class="bg-gray-100 font-bold p-2 text-xs border-b border-gray-300">
                                INFORMACIÓN DE ACEPTACIÓN / ACCEPTANCE INFORMATION
                            </div>
                            <div class="grid grid-cols-5 gap-0">
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Peso (Kg)</div>
                                    <div class="text-sm font-bold">{{ $envio->peso ?? '0' }}</div>
                                </div>
                                <div class="border-r border-gray-300 p-2">
                                    <div class="text-xs font-bold">Tasas</div>
                                    <div class="text-sm font-bold">{{ $envio->coste ?? '0.00' }}</div>
                                </div>
                                <div class="p-2 col-span-3">
                                    <div class="text-xs font-bold">Usuario</div>
                                    <div class="text-sm">{{ optional($envio->users)->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 p-3 text-center border-t-2 border-gray-800">
                            <div class="text-sm font-bold">COMPROBANTE DE ENTREGA AL DESTINATARIO / PROOF OF DELIVERY</div>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-bold rounded">
                                    EMS
                                </span>
                                <span class="ml-2 text-xs">- Servicio Express Mail Service</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script>
        function mmToPx(mm) {
            const div = document.createElement('div');
            div.style.width = mm + 'mm';
            div.style.position = 'absolute';
            div.style.visibility = 'hidden';
            document.body.appendChild(div);
            const px = div.getBoundingClientRect().width;
            document.body.removeChild(div);
            return px;
        }

        function fitEmsToContainer() {
            const container = document.getElementById('emsContainer');
            const content = document.getElementById('contentToPrintEms');
            if (!container || !content) return;

            const baseWidthPx = mmToPx(395);     // contenido base A4
            const baseHeightPx = mmToPx(250); // <-- volver a 297, no 210

            const containerWidthPx = container.clientWidth;
            const containerHeightPx = container.clientHeight; // 148.5mm en px

            if (!baseWidthPx || !baseHeightPx) return;

            const scaleW = containerWidthPx / baseWidthPx;
            const scaleH = containerHeightPx / baseHeightPx;
            const scale = Math.min(scaleW, scaleH);

            content.style.transform = `scale(${scale})`;

            const contentWidthPx = baseWidthPx * scale;
            const contentHeightPx = baseHeightPx * scale;

            const leftPx = Math.max(0, (containerWidthPx - contentWidthPx) / 2);
            const topPx = Math.max(0, (containerHeightPx - contentHeightPx) / 2);

            content.style.left = leftPx + 'px';
            content.style.top = topPx + 'px';
        }

        document.addEventListener('DOMContentLoaded', () => {
            fitEmsToContainer();
            window.addEventListener('resize', fitEmsToContainer);

            document.getElementById('printPreviewEms').addEventListener('click', function() {
                const element = document.getElementById('emsContainer'); // imprime el wrapper
                html2pdf()
                    .from(element)
                    .set({
                        filename: 'recibo-consignacion-ems.pdf',
                        margin: 0,
                        image: { type: 'jpeg', quality: 1 },
                        html2canvas: { scale: 2, useCORS: true },
                        pagebreak: { mode: ['avoid-all'] },
                        jsPDF: { unit: 'mm', format: [210, 148.5], orientation: 'portrait' }
                    })
                    .save();
            });
        });
    </script>
@endsection
