<div>
    <title>Informe PDF</title>
    <link rel="stylesheet" href="{{ asset('css/pdf-style.css') }}">

    <div class="flex justify-center space-x-24">
        <x-button type="button" id="printPdf" class="bg primary">
            IMPRIMIR
        </x-button>

        <button type="button" id="confirmar" wire:click="confirmarTermica"
        class="bg-red-700 text-white px-2 py-2 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2">
            Confirmar
        </button>
    </div>

    <div id="contentToPrint" class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <h1 class="text-xl font-bold">Instituto Postal Telegrafico (IPOSTEL)</h1>
            </div>
            <br>
            @if(isset($codigo_barras))
            <div class="mt-4 flex justify-center ">
                <img src="data:image/png;base64,{{ $codigo_barras }}" alt="Código de Barras" />
            </div>
            @endif
            <br>
            <div>
                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                        <div class="col-start-1 col-span-2">
                            <p><strong>REMITENTE:</strong> {{$envio->nombre_rem}} {{$envio->apellido_rem}}</p>
                        </div>

                        <div class="col-start-4 col-span-2">
                            <p><strong>FECHA:</strong>{{$fecha}}</p>
                        </div>

                        <div class="col-start-1 col-span-2">
                            <p><strong>CODIGO POSTAL:</strong> {{$envio->codigo_postal_rem}}</p>
                        </div>

                        <div class="col-start-4">
                            <p><strong>CI/RIF:</strong> {{$envio->tipo_documento_rem}}-{{$envio->documento_rem}}</p>
                        </div>

                        <div class="col-start-5">
                            <p><strong>TLF:</strong> {{$envio->telefono_rem}}</p>
                        </div>

                        <div class="col-start-1 col-span-2">
                            <p><strong>ORIGEN:</strong> {{$oficina_or->codigo}} - {{$estado_of_or->nombre}}</p>
                        </div>

                        <div class="col-start-4 col-span-2">
                            <p><strong>DESTINO:</strong> {{$pais->nombre}}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 text-white p-0.5 rounded-lg"></div>

                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="col-start-1 col-span-2">
                            <strong >DESTINATARIO:</strong>
                            <span class="text-gray-800">{{$envio->nombre_dest}}  {{$envio->apellido_dest}}</span>
                        </div>
                        {{-- <div class="col-start-3 col-span-2">
                            <strong >CODIGO POSTAL:</strong>
                            <span class="text-gray-800">{{$envio->codigo_postal_dest}}</span>
                        </div> --}}
                        <div  class="col-start-5 col-span-2">
                            <strong>TLF:</strong>
                            <span class="text-gray-800">{{$envio->tlf_dest}}</span>
                        </div>
                        <div  class="col-start-1 col-span-6">
                            <strong>DIRECCION:</strong>
                            <span class="text-gray-800"> {{$envio->direccion_dest}}</span>
                        </div>
                        {{-- <div  class="col-start-1 col-span-2">
                            <strong>ESTADO:</strong>
                            <span class="text-gray-800">{{$estado_of_dest->nombre}}</span>
                        </div>
                        <div  class="col-start-3 col-span-2">
                            <strong>MUNICIPIO:</strong>
                            <span class="text-gray-800">{{$municipio_dest->nombre}}</span>
                        </div>
                        <div class="col-start-5 col-span-2">
                            <strong>CIUDAD:</strong>
                            <span class="text-gray-800">{{$ciudad->nombre}}</span>
                        </div> --}}

                        @if($envio->servicio_id == 2)
                        <div class="col-start-3 col-span-2">
                            <strong>Nro Apartado Postal:</strong>
                            <span class="text-gray-800">{{$envio->apartado_postal}}</span>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="bg-gray-900 text-white p-0.5 rounded-lg"></div>

                <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="col-start-1 col-span-4">
                            <strong>SERVICIO:</strong>
                            <span class="text-gray-800">{{$servicio->nombre}}</span>
                        </div>
                        <div class="col-start-1 col-span-3">
                            @if($servicio_id == 8)
                            <strong class="text-G">PESO (KG):</strong>
                            @else
                            <strong class="tetx-G">PESO (GR):</strong>
                            @endif
                            <span class="text-G">{{$envio->peso}}</span>
                        </div>
                        <div class="col-start-3 col-span-2">
                            <span class="text-M text-center">{{$envio->codigo_envio}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <!-- Modal -->
            @if ($mostrar_modal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50 transition-opacity duration-300 ease-out opacity-100">
                <!-- Modal Box -->
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full transform transition-all duration-500 ease-out opacity-100 scale-100"
                    x-transition:enter="transition transform ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition transform ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">¿Se imprimió correctamente la térmica?</h2>
                    <div class="flex justify-around">
                        <button wire:click="actualizarEstatus('si')" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Sí
                        </button>
                        <button wire:click="actualizarEstatus('no')" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                            No
                        </button>
                    </div>
                </div>
            </div>
            @endif


        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

        @push('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('printPdf').addEventListener('click', function() {
                    var element = document.getElementById('contentToPrint');

                    // Crear el PDF con html2pdf.js
                    html2pdf()
                        .from(element)
                        .toPdf()
                        .get('pdf')
                        .then(function (pdf) {
                            // Usamos window.print para abrir el cuadro de impresión
                            pdf.autoPrint();

                            // Abre el cuadro de diálogo de impresión sin abrir una nueva pestaña
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

        @endpush

</div>
