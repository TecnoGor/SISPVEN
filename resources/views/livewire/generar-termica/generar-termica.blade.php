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

    <div class="flex justify-center p-8" style="transform: scale(1)">
        <div id="contentToPrint" class="bg-white rounded-lg shadow-md p-2" style="width:10cm; height:10cm;">

            <div class="mt-7">
                <div class="bg-white rounded-lg p-2 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 font-semibold text-[9px]">

                        <div class="col-start-1 col-span-3">
                            <p><strong>Rem:</strong> {{$envio->nombre_rem}} {{$envio->apellido_rem}}</p>
                        </div>

                        <div class="col-start-4 col-span-3">
                            <p>{{$fecha}}</p>
                        </div>

                        <div class="col-start-1 col-span-2">
                            <p><strong>Codigo P:</strong> {{$envio->codigo_postal_rem}}</p>
                        </div>

                        <div class="col-start-3 col-span-2">
                            <p>{{$envio->tipo_documento_rem}}-{{$envio->documento_rem}}</p>
                        </div>

                        <div class="col-start-5">
                            <p><strong></strong> {{$envio->telefono_rem}}</p>
                        </div>

                        <div class="col-start-1 col-span-3">
                            <p><strong>Origen:</strong> {{$oficina_or->codigo}} - {{$estado_of_or->nombre}}</p>
                        </div>

                        <div class="col-start-4 col-span-3">
                            <p><strong>Destino:</strong> {{$oficina_dest->codigo}} - {{$estado_of_dest->nombre}}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 text-white p-0.5 rounded-lg"></div>

                <div class="bg-white rounded-lg p-2 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 font-semibold text-[9px]">
                        <div class="col-start-1">
                            <strong>DESTINO:</strong>
                        </div>
                        <div class="col-start-2 col-span-3">
                            <span class="text-gray-800">{{$envio->nombre_dest}}  {{$envio->apellido_dest}}</span>
                        </div>
                        <div class="col-start-5 col-span-2">
                            <strong>Codigo P:</strong>
                            <span class="text-gray-800">{{$envio->codigo_postal_dest}}</span>
                        </div>
                        <div  class="col-start-1 col-span-2">
                            <span class="text-gray-800">{{$envio->tlf_dest}}</span>
                        </div>
                        <div  class="col-start-3 col-span-2">
                            <span class="text-gray-800"> {{$envio->tipo_documento_dest}} - {{$envio->documento_dest}}</span>
                        </div>
                        <div  class="col-start-5 col-span-2">
                            <span class="text-gray-800">{{$estado_of_dest->nombre}}</span>
                        </div>
                        <div  class="col-start-1 col-span-2">
                            <strong>Mun:</strong>
                            <span class="text-gray-800">{{$municipio_dest->nombre}}</span>
                        </div>
                        <div  class="col-start-3 col-span-2">
                            <strong>Parr:</strong>
                            <span class="text-gray-800">{{$parroquia_dest->nombre}}</span>
                        </div>
                        <div class="col-start-5 col-span-2">
                            <strong>Ciudad:</strong>
                            <span class="text-gray-800">{{$ciudad->nombre ?? 'N/A'}}</span>
                        </div>
                        <div  class="col-start-1 col-span-6">
                            <strong>Direccion:</strong>
                            <span class="text-gray-800"> {{$envio->direccion_dest}}</span>
                        </div>

                        @if($envio->oficina_dest_id)
                        <div class="col-span-3">
                            <strong>OPT Destino:</strong>
                            <span class="text-gray-800">{{$oficina_dest_id}}</span>
                        </div>
                        @endif

                        @if($envio->apartado_postal)
                        <div class="col-start-4 col-span-2">
                            <strong>Apartado Postal:</strong>
                            <span class="text-gray-800">{{$apartado_postal}}</span>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="bg-gray-900 text-white p-0.5 rounded-lg"></div>

                <div class="bg-white rounded-lg p-2 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 font-semibold text-[8px]">
                        <div class="col-start-1 col-span-3">
                            <strong>Servicio:</strong>
                            <span class="text-gray-800">{{$servicio->nombre}}</span>
                        </div>
                        <div class="col-start-4 col-span-3" >
                            <strong class="text-[10px]">Valija:</strong>
                            <span class="text-gray-900">{{$envio->tipoSaca->nombre}}</span>
                        </div>
                        <div class="col-span-2">
                            <strong class="text-[10px]">Costo:</strong>
                            <span class="text-[10px]">{{$datos_costo['base']}} Bs</span>
                        </div>
                        <div class="col-span-2">
                            <strong class="text-[10px]">IVA:</strong>
                            <span class="text-[10px]">{{$datos_costo['iva']}} Bs</span>
                        </div>
                        <div class="col-span-2">
                            <strong class="text-[10px]">Total:</strong>
                            <span class="text-[10px]">{{$datos_costo['total']}} Bs</span>
                        </div>
                        <div class="col-start-1 col-span-2">
                            <strong class="text-[10px]">Peso (GR):</strong>
                            <span class="text-[10px]">{{$envio->peso}}</span>
                        </div>
                        <div class="col-start-3 col-span-2">
                            <span class="text-[10px]">{{$envio->codigo_envio}}</span>
                        </div>
                        @php($iposplus = $envio->iposplus->first())
                        @if($iposplus)
                        <div class="col-start-1 col-span-2">
                            <strong class="text-[10px]">Alto:</strong>
                            <span class="text-[10px]">{{ $iposplus->alto }} cm</span>
                        </div>
                        <div class="col-start-3 col-span-2">
                            <strong class="text-[10px]">Largo:</strong>
                            <span class="text-[10px]">{{ $iposplus->largo }} cm</span>
                        </div>
                        <div class="col-start-5 col-span-2">
                            <strong class="text-[10px]">Ancho:</strong>
                            <span class="text-[10px]">{{ $iposplus->ancho }} cm</span>
                        </div>
                        @endif
                    </div>
                    @if(isset($codigo_barras))
                    <div class="flex justify-center mt-2">
                        <span class="w-3/4"><img src="data:image/png;base64,{{ $codigo_barras }}" alt="Código de Barras" /></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

            <!-- Modal de confirmación de impresión -->
            @if ($mostrar_modal)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 flex justify-center items-center z-50 transition-opacity duration-300 ease-out backdrop-blur-sm">
                <!-- Modal Box -->
                <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-500 ease-out scale-100">

                    <!-- Ícono de impresora -->
                    <div class="flex justify-center mb-5">
                        <div class="bg-teal-100 rounded-full p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.25 7.034V3.375" />
                            </svg>
                        </div>
                    </div>

                    <!-- Título -->
                    <h2 class="text-xl text-center font-bold text-gray-800 mb-2">
                        ¿Se imprimió correctamente la térmica?
                    </h2>

                    <!-- Subtexto explicativo -->
                    <p class="text-sm text-center text-gray-500 mb-6">
                        Confirma si la etiqueta se imprimió correctamente para continuar con el proceso del envío.
                    </p>

                    <!-- Botones -->
                    <div class="flex justify-center gap-4">
                        <button wire:click="actualizarEstatus('si')"
                            class="flex items-center gap-2 bg-green-500 text-white px-5 py-2.5 rounded-xl hover:bg-green-600 transition-colors duration-200 font-medium shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Sí, se imprimió bien
                        </button>
                        <button wire:click="actualizarEstatus('no')"
                            class="flex items-center gap-2 bg-red-400 text-white px-5 py-2.5 rounded-xl hover:bg-red-500 transition-colors duration-200 font-medium shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                            </svg>
                            No, reimprimir
                        </button>
                    </div>
                </div>
            </div>
            @endif


        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>


        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Livewire.on('alertError', ({ message }) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('envios.listado-ventas') }}";
                });
            });

            // Delegación de evento: escuchamos clicks en el documento entero
            document.addEventListener('click', function(event) {
                if (event.target && event.target.id === 'printPdf') {
                    event.preventDefault();

                    var element = document.getElementById('contentToPrint');
                    if (!element) {
                        console.error('No se encontró el elemento contentToPrint');
                        return;
                    }

                    console.log('Iniciando generación PDF');

                    html2pdf()
                        .from(element)
                        .set({
                            filename: 'documento.pdf',
                            margin: 0,
                            image: { type: 'jpeg', quality: 1 },
                            html2canvas: { scale: 3 },
                            jsPDF: {
                                unit: 'mm',
                                format: [100, 100],
                                orientation: 'portrait'
                            }
                        })
                        .toPdf()
                        .get('pdf')
                        .then(function (pdf) {
                            pdf.autoPrint();

                            var pdfDataUrl = pdf.output('bloburl');
                            var iframe = document.createElement('iframe');
                            iframe.style.position = 'absolute';
                            iframe.style.width = '0px';
                            iframe.style.height = '0px';
                            iframe.style.border = 'none';

                            // Esperar a que el PDF termine de cargar en el iframe antes
                            // de imprimir. Sin esto el diálogo puede abrirse sobre un
                            // iframe todavía vacío y la térmica sale en blanco.
                            iframe.onload = function () {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();

                                // Liberar iframe y blob una vez el usuario terminó con
                                // el diálogo, para no acumularlos en cada impresión.
                                setTimeout(function () {
                                    if (iframe.parentNode) {
                                        document.body.removeChild(iframe);
                                    }
                                    URL.revokeObjectURL(pdfDataUrl);
                                }, 60000);
                            };

                            // El src se asigna después del onload para no perder el
                            // evento si la carga resuelve de inmediato.
                            iframe.src = pdfDataUrl;
                            document.body.appendChild(iframe);
                        })
                        .catch(function (error) {
                            console.error('Error al generar la térmica', error);
                            alert('No se pudo generar la térmica. Intente de nuevo.');
                        });
                }
            });
        </script>
        @endpush

</div>
