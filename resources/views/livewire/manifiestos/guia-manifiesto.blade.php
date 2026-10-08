<div>
    @section('titulo')
        Guia de Despacho (histórica)
    @endsection
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between mb-4">
            <h2 class="font-semibold text-xl text-primary leading-tight flex gap-3 ml-10">
                <x-return-link :href="route('manifiestos')" wire:navigate.hover/>
                    Volver
            </h2>
        </div>

        <div class="mb-4">
            <h1 class="text-lg font-bold">
                Guia de Despacho — {{ optional($manifiesto->oficinaDestino)->nombre ?? 'Sin destino' }}
                <span class="text-gray-500 font-normal">
                    · {{ $manifiesto->created_at?->format('d/m/Y') }}
                </span>
            </h1>
        </div>

        {{-- Aviso: este documento procede del modelo anterior. Se marca para que
             el operador sepa por que no tiene numero de despacho ni acciones. --}}
        <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <p class="text-sm text-amber-900">
                    <strong>Guía histórica.</strong> Corresponde a un despacho anterior al modelo
                    actual, por lo que se muestra a partir de su manifiesto. Refleja lo que salió
                    realmente en esa fecha y es de solo lectura.
                </p>
            </div>
        </div>

        <div class="mb-4 flex items-center">
            <button id="printPdf" class="px-4 py-2 bg-primary text-white rounded">
                IMPRIMIR
            </button>
        </div>

        <div id="contentToPrint">

            <img src="{{ asset('/images/cintillo.jpg') }}"
            alt="Encabezado"
            class="w-full max-h-12 object-cover mb-4">

            <table style="width: 100%; text-align: left; font-size: 12px; border-collapse: collapse; border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="7" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                            Guia de Despacho
                        </th>
                        <th colspan="2" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                            Fecha Despacho
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Origen</td>
                        <td colspan="3" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Destino</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Hora de Elaboracion</td>
                        <td style="border: 0.5px solid black; padding: 5px; text-align: center; font-weight: bold;">Fecha</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ optional($manifiesto->oficina)->nombre }}</td>
                        <td colspan="3" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ optional($manifiesto->oficinaDestino)->nombre }}</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ $manifiesto->created_at?->format('h:i A') }}</td>
                        <td style="border: 0.5px solid black; padding: 5px; text-align: center; font-weight: bold;">{{ $manifiesto->created_at?->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th style="border: 0.5px solid black; padding: 5px;">CÓDIGO</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Valija</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Al descubierto</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Oficina de Origen</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Oficina de Destino</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Categoría</th>
                        <th style="border: 0.5px solid black; padding: 5px;">
                            Peso Bruto Desp.<br>
                            <hr style="margin: 0; border-top: 1px solid black;">
                            (Gramos)
                        </th>
                        <th style="border: 0.5px solid black; padding: 5px;">N° de Precinto</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bultos as $bulto)
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;">
                                {{ $bulto['tipo'] }}: {{ $bulto['codigo'] }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;">
                                {{ $bulto['tipo'] === 'Valija' ? 'X' : '' }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;">
                                {{ $bulto['tipo'] === 'Envío' ? 'X' : '' }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px;">{{ optional($manifiesto->oficina)->nombre }}</td>
                            <td style="border: 1px solid black; padding: 5px;">{{ optional($manifiesto->oficinaDestino)->nombre }}</td>
                            <td style="border: 1px solid black; padding: 5px;">{{ $bulto['servicio'] }}</td>
                            <td style="border: 1px solid black; padding: 5px;">{{ $bulto['peso'] }}Gr</td>
                            <td style="border: 1px solid black; padding: 5px;">{{ $bulto['precinto'] }}</td>
                            <td style="border: 1px solid black; padding: 5px;"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="border: 1px solid black; text-align: center; font-style: italic; padding: 5px;">
                                Este manifiesto no tiene bultos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="border: 1px solid black; padding: 5px; text-align: right; font-weight: bold;">Total Peso:</td>
                        <td style="border: 1px solid black; padding: 5px; text-align: center;">{{ $pesoTotal }}Gr</td>
                        <td colspan="2" style="border: 1px solid black; padding: 5px;"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                            <strong>Unidad de Expedición <br>
                                Nombre y Apellido: <br>
                                C.I     <br>
                                Codigo  <br>
                                Firma   <br>
                            </strong>
                        </td>
                        <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                            <strong>Transportista <br>
                                Nombre y Apellido: <br>
                                C.I     <br>
                                Codigo  <br>
                                Firma   <br>
                            </strong>
                        </td>
                        <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                            <strong>Unidad de Recepción<br>
                                Nombre y Apellido: <br>
                                C.I     <br>
                                Codigo  <br>
                                Firma   <br>
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <p class="mt-4 text-sm text-gray-600">
            Total de bultos: <span class="font-semibold">{{ $totalBultos }}</span>
        </p>
    </div>

    @push('scripts')
        <script>
            document.getElementById('printPdf')?.addEventListener('click', function () {
                const contenido = document.getElementById('contentToPrint').innerHTML;
                const ventana = window.open('', '', 'height=800,width=1000');
                ventana.document.write('<html><head><title>Guia de Despacho</title></head><body>');
                ventana.document.write(contenido);
                ventana.document.write('</body></html>');
                ventana.document.close();
                ventana.print();
            });
        </script>
    @endpush
</div>