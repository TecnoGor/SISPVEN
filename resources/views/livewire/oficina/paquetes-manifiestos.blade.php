<div>
    @section('titulo')
        Guia de Despacho
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
                Guia de Despacho N° {{ optional($despacho)->numero_despacho }}
            </h1>
        </div>

        {{-- Despacho del modelo anterior con VARIAS salidas fisicas: cada una tiene
             su propio manifiesto, asi que el operador elige cual consultar. --}}
        @if (!empty($manifiestosHistoricos))
            <div class="rounded-lg border border-amber-300 bg-amber-50 p-4 mb-4">
                <p class="text-sm text-amber-900">
                    <strong>Guía histórica.</strong> Este despacho es del modelo anterior y
                    agrupa <strong>{{ count($manifiestosHistoricos) }} salidas</strong> distintas.
                    Seleccione la que desea consultar.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold">Destino</th>
                            <th class="px-4 py-2.5 text-left font-semibold">Fecha</th>
                            <th class="px-4 py-2.5 text-right font-semibold">Bultos</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($manifiestosHistoricos as $m)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 text-gray-900">
                                    {{ optional($m->oficinaDestino)->nombre ?? 'Sin destino' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-700 tabular-nums">
                                    {{ $m->created_at?->format('d/m/Y h:i A') }}
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-900 tabular-nums">
                                    {{ $m->paquetes_count }}
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <a href="{{ route('guia-manifiesto', $m->manifiesto_id) }}"
                                       class="inline-flex items-center rounded-md bg-primary px-3 py-1.5 text-white text-xs font-semibold hover:opacity-90">
                                        Ver guía
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else


        <div class="mb-4 flex items-center">
            <button id="printPdf" class="px-4 py-2 bg-primary text-white rounded">
                IMPRIMIR
            </button>
            @if($despacho && $despacho->activo) <!-- Solo se puede confirmar un despacho abierto -->
                {{-- Confirmar cierra el despacho: abre el aviso en vez de ejecutar directo. --}}
                <button wire:click="$set('mostrarAvisoConfirmar', true)" class="px-4 py-2 bg-primary text-white rounded ml-10">
                    Confirmar Guia
                </button>
            @elseif($despacho && !$despachoYaSalio)
                {{-- Guia confirmada pero el despacho aun no ha salido: se puede reabrir
                     para volver a agregar o quitar valijas y envios al descubierto. --}}
                <button wire:click="reabrirGuia" wire:loading.attr="disabled" wire:target="reabrirGuia"
                    class="px-4 py-2 border border-primary text-primary rounded ml-10 hover:bg-primary-light disabled:opacity-60 disabled:cursor-wait">
                    <span wire:loading.remove wire:target="reabrirGuia">Reabrir Guia</span>
                    <span wire:loading wire:target="reabrirGuia">Reabriendo...</span>
                </button>
            @endif

        </div>

        {{-- Aviso previo a confirmar la guia. Una vez confirmada, el despacho queda
             cerrado: no admite valijas nuevas ni envios al descubierto. --}}
        @if($mostrarAvisoConfirmar)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                 role="dialog" aria-modal="true" aria-labelledby="tituloAvisoConfirmar">
                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div>
                            <h3 id="tituloAvisoConfirmar" class="text-lg font-bold text-gray-900">
                                ¿Confirmar la guía del despacho N° {{ optional($despacho)->numero_despacho }}?
                            </h3>
                            <p class="mt-2 text-sm text-gray-700">
                                Una vez confirmada, este despacho <strong>no admitirá más valijas ni
                                envíos al descubierto</strong>. Las valijas nuevas hacia ese destino
                                abrirán un despacho distinto.
                            </p>
                            <p class="mt-2 text-sm text-gray-700">
                                Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="$set('mostrarAvisoConfirmar', false)"
                                class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="button" wire:click="confirmarGuia"
                                wire:loading.attr="disabled" wire:target="confirmarGuia"
                                class="px-4 py-2 rounded bg-primary text-white hover:opacity-90 disabled:opacity-60 disabled:cursor-wait">
                            <span wire:loading.remove wire:target="confirmarGuia">Sí, confirmar</span>
                            <span wire:loading wire:target="confirmarGuia">Confirmando...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div id="contentToPrint">

            <img src="{{ asset('/images/cintillo.jpg') }}"
            alt="Encabezado"
            class="w-full max-h-12 object-cover mb-4">


            <table style="width: 100%; text-align: left; font-size: 12px; border-collapse: collapse; border: 1px solid black;">
                <thead>
                    <tr>
                        <th colspan="8" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                            Guia de Despacho
                        </th>
                        <th colspan="3" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                            Fecha Despacho
                        </th>
                    </tr>
                    <tr>
                        <td colspan="10" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">
                            <span style="padding-right: 100px;">Red Aérea</span>
                            <span>Red Terrestre</span>
                        </td>

                        <td style="border: 0.5px solid black; padding: 5px; text-align: center; font-weight: bold;">Fecha</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Origen</td>
                        <td colspan="3" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Destino</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Hora de Elaboracion</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Hora de Salida</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">Hora de Llegada</td>
                        <td style="border: 0.5px solid black; padding: 5px; text-align: center; font-weight: bold;">{{ optional($despacho)->created_at?->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ optional(optional($despacho)->oficina)->nombre }}</td>
                        <td colspan="3" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ optional(optional($despacho)->oficinaDestino)->nombre }}</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">{{ optional($despacho)->created_at?->format('h:i A') }}</td>
                        <td colspan="1" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;"></td>
                        <td colspan="2" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;"></td>
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 0.5px solid black; padding: 5px;">CÓDIGO</th>
                        <th style="border: 0.5px solid black; padding: 5px;">N° de Despacho</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Valija</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Sobre</th>
                        <th style="border: 0.5px solid black; padding: 5px;">CAJA</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Oficina de Origen</th>
                        <th style="border: 0.5px solid black; padding: 5px;">Oficina de Destino</th>
                        <th style="border: 0.5px solid black; padding: 5px;">C</th>
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
                    @php
                        $pesoTotal = 0;
                    @endphp
                    @forelse ($sacas as $saca)
                        @php
                            $pesoTotal += (float) ($saca->peso ?? 0);
                            // El servicio/categoria sale de la pivote tipo_saca_servicio (primer servicio),
                            // con fallback al nombre referencial del tipo de saca.
                            $categoria = optional(optional($saca->tipoSaca)->servicios?->first())->nombre
                                ?? optional($saca->tipoSaca)->nombre_referencial
                                ?? 'Sin servicio';
                        @endphp
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;">
                                Valija: {{ $saca->codigo_saca }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;">
                                N°{{ optional($saca->numeroDespacho)->numero_despacho }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;">X</td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;"></td>
                            <td style="border: 1px solid black; padding: 5px; text-align: center;"></td>
                            <td style="border: 1px solid black; padding: 5px;">{{ optional($saca->oficinaOrigen)->nombre ?? optional(optional($despacho)->oficina)->nombre }}</td>
                            <td style="border: 1px solid black; padding: 5px;">
                                {{ optional($saca->oficinaDestino)->nombre ?? optional(optional($despacho)->oficinaDestino)->nombre }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px;"></td>
                            <td style="border: 1px solid black; padding: 5px;">{{ $categoria }}</td>
                            <td style="border: 1px solid black; padding: 5px;">{{ $saca->peso }}Gr</td>
                            <td style="border: 1px solid black; padding: 5px;">
                                {{ $saca->numero_precinto ?? 'N/A' }}
                            </td>
                            <td style="border: 1px solid black; padding: 5px;"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="border: 1px solid black; text-align: center; font-style: italic; padding: 5px;">
                                No hay valijas registradas en este despacho.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="border: 1px solid black; padding: 5px; text-align: right; font-weight: bold;">Total Peso:</td>
                        <td style="border: 1px solid black; padding: 5px; text-align: center;">{{ $pesoTotal }}Gr</td>
                        <td colspan="3" style="border: 1px solid black; padding: 5px;"></td>
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
                            <strong>Empresa Aérea<br>
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
            <div>

            @if($paquetesCertificados->isNotEmpty())

                    <button id="printCertifiedTable" class="px-4 py-2 bg-primary text-white rounded mb-4 mt-10">
                       IMPRIMIR RELACION DE ENVIO CERTIFICADO
                    </button>
    <div id="certifiedTable">
        <img src="{{ asset('/images/cintillo.jpg') }}"
            alt="Encabezado"
            class="w-full max-h-12 object-cover mb-4 mt-10">

        <table style="width: 100%; text-align: left; border-collapse: collapse; border: 1px solid black;">
            <thead>
                <tr>
                    <th colspan="9" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                        Relación de Envios Certificados
                    </th>
                    <th colspan="3" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">
                        Fecha Despacho
                    </th>
                </tr>
                <tr>
                    <td colspan="2" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">UNIDAD DE ORIGEN</td>
                    <td colspan="2" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">UNIDAD DE DESTINO</td>
                    <td colspan="2" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">HORA</td>
                    <td colspan="3" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">PÁGINA</td>
                    <td colspan="2" style="border: 0.5px solid black; text-align: center; background-color: #ffffff;">FECHA</td>
                </tr>
                <tr>
                    <th colspan="2" style="border: 1px solid black; padding: 5px;">N°</th>
                    <th colspan="1" style="border: 1px solid black; padding: 5px;">TIPO</th>
                    <th colspan="1" style="border: 1px solid black; padding: 5px;">CÓDIGO</th>
                    <th colspan="1" style="border: 1px solid black; padding: 5px;">ORIGEN</th>
                    <th colspan="1" style="border: 1px solid black; padding: 5px;">DESTINO</th>
                    <th colspan="3" style="border: 1px solid black; padding: 5px;">PESO</th>
                    <th colspan="4" style="border: 1px solid black; padding: 5px;">PRECINTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paquetesCertificados as $index => $paquete)
                    <tr>
                        <td colspan="2" style="border: 1px solid black; padding: 5px; text-align: center;">
                            {{ $loop->iteration }}
                        </td>
                        <td colspan="1" style="border: 1px solid black; padding: 5px;">
                            {{ ucfirst($paquete['tipo']) }}
                        </td>
                        <td colspan="1" style="border: 1px solid black; padding: 5px;">
                            Envío: {{ $paquete['codigo'] }}
                        </td>
                        <td colspan="1" style="border: 1px solid black; padding: 5px;">
                            {{ $paquete['OficinaOrigen'] }}
                        </td>
                        <td colspan="1" style="border: 1px solid black; padding: 5px;">
                            {{ $paquete['OficinaDestino'] }}
                        </td>
                        <td colspan="1" style="border: 1px solid black; padding: 5px;">
                            {{ $paquete['peso'] }} Gr
                        </td>
                        <td colspan="4" style="border: 1px solid black; padding: 5px;">
                            {{ $paquete['precinto'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" style="border: 0.5px solid black; padding: 20px; text-align: center;"></td>
                </tr>
                <tr>
                    <td colspan="12" style="border: 0.5px solid black; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; background-color: #f2f2f2;">FIRMAS Y SELLOS</td>
                </tr>
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">ELAVORADO POR</td>
                    <td colspan="1" style="border: 1px solid black; padding: 5px; text-align: center;">SUPERVISADO POR</td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">RECIBIDO POR</td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">SUPERVISADO POR</td>
                </tr>
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">NOMBRE Y APELLIDO</td>
                    <td colspan="1" style="border: 1px solid black; padding: 5px; text-align: center;">NOMBRE Y APELLIDO</td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">NOMBRE Y APELLIDO</td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: center;">NOMBRE Y APELLIDO</td>
                </tr>
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                        <strong>
                            <br>
                            C.I
                            <br><br>
                            FIRMA
                        </strong>
                    </td>
                    <td colspan="1" style="border: 1px solid black; padding: 5px; text-align: left;">
                        <strong>
                            <br>
                            C.I
                            <br><br>
                            FIRMA
                        </strong>
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                        <strong>
                            <br>
                            C.I
                            <br><br>
                            FIRMA
                        </strong>
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 5px; text-align: left;">
                        <strong>
                            <br>
                            C.I
                            <br><br>
                            FIRMA
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: center;">UNIDAD EMISORA</td>
                    <td colspan="8" style="border: 1px solid black; padding: 5px; text-align: center;">UNIDAD RECEPTORA</td>
                </tr>
            </tfoot>
        </table>

    </div>
@else
    <p>No hay paquetes certificados disponibles.</p>
@endif

    @endif {{-- fin del bloque: guia normal vs seleccion de manifiesto historico --}}

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

@push('scripts')
    {{-- Sin estos listeners los avisos del componente (reabrirGuia, confirmarGuia)
         se emitian pero no se veian: parecia que el boton no hacia nada. --}}
    {{-- El payload llega como objeto ({ message: '...' }), no como string: hay que
         desestructurarlo o Swal pinta "[object Object]". Mismo patron que el resto
         de vistas del modulo. --}}
    <script>
        Livewire.on('alertSuccess', ({ message }) => {
            Swal.fire({
                icon: 'success',
                title: 'Listo',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        });

        Livewire.on('alertError', ({ message }) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                showConfirmButton: true
            });
        });
    </script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printPdf').addEventListener('click', function() {
            var element = document.getElementById('contentToPrint');

            html2pdf()
                .from(element)
                .set({
                    filename: 'guia_despacho.pdf',
                    margin: 10,
                    image: { type: 'jpeg', quality: 1 },
                    html2canvas: { scale: 3 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                })
                .save();
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const certBtn = document.getElementById('printCertifiedTable');
        if (certBtn) {
            certBtn.addEventListener('click', function() {
                var element = document.getElementById('certifiedTable');

                html2pdf()
                    .from(element)
                    .set({
                        filename: 'relacion_envios_certificado.pdf',
                        margin: 10,
                        image: { type: 'jpeg', quality: 1 },
                        html2canvas: { scale: 3 },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                    })
                    .save();
            });
        }
    });
</script>
@endpush