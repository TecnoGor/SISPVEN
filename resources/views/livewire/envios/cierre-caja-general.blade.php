@section('titulo')
    Cierre de Caja General
@endsection

<div>
    <div class="flex justify-center items-center mb-1 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('envios.listado-ventas')" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-primary font-bold">Cierre de Caja</h1>
    </div>

    <div class="py-12 max-w-[90%] mx-auto">
        @if (session()->has('mensaje'))
            <x-alert class="bg-green-100 border-green-600 text-green-600 mb-4">
                {{ session('mensaje') }}
            </x-alert>
        @endif
    </div>         

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(array_sum($cantidad) > 0)
            <button id="printPdf" class="px-4 py-2 bg-primary text-white rounded">
                IMPRIMIR
            </button>
            <button wire:click="exportExcel" class="px-4 py-2 bg-primary text-white rounded">Exportar a Excel</button>
            
            <button wire:click="export043" wire:loading.attr='disabled' wire:target='export043' 
            class="px-4 py-2 bg-primary text-white rounded">Planilla 043</button>
            <button wire:click="export044" wire:loading.attr='disabled' wire:target='export044' 
            class="px-4 py-2 bg-primary text-white rounded">Planilla 044</button>
            <button wire:click="export047" wire:loading.attr='disabled' wire:target='export047' 
            class="px-4 py-2 bg-primary text-white rounded">Planilla 047</button>

            @if(auth()->user()->hasRole('Promotor Integral') && $desde == $hasta && $desde == now()->format('Y-m-d'))
            <button wire:click="selectedConfirmarCierre" class="px-4 py-2 bg-primary text-white rounded">
                CONFIRMAR CIERRE DE CAJA
            </button>
            @endif

            <div class="w-1/4">
                <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Fecha de Formato 044-047</label>
                <input id="desde" type="week" wire:model.live="fecha044"
                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>
        @endif

        <div id="contentToPrint">
            <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200 p-4">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('/images/ipostel.png') }}" 
                    alt="Encabezado" 
                    class="w-48 h-auto object-contain" id="pdfLogo">
                    <p>Usuario: {{ $usuarioNombre }}</p>
                </div>

                <div class="text-center mb-4">
                    <p class="text-lg font-semibold">Cierre de Caja</p>
                    <p class="text-sm text-gray-600">Desde: <strong>{{ $desde }}</strong> - Hasta: <strong>{{ $hasta }}</strong></p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="dataTable">
                        @if(array_sum($cantidad) > 0)
                            <thead class="text-xs text-default uppercase bg-gray-100">
                                <tr>
                                    @include('livewire.includes.sort-table', ['column' => 'servicio', 'displayName' => 'Servicio'])
                                    @include('livewire.includes.sort-table', ['column' => 'cantidad', 'displayName' => 'Cantidad'])
                                    @include('livewire.includes.sort-table', ['column' => 'iva', 'displayName' => 'Tarifa Base'])

                                    @php
                                        $allSubservicios = [];
                                        foreach ($subservicios as $servicioId => $subservicio) {
                                            $allSubservicios = array_merge($allSubservicios, array_keys($subservicio));
                                        }
                                        $allSubservicios = array_unique($allSubservicios);
                                    @endphp

                                    @foreach($allSubservicios as $nombreSubservicio)
                                        @include('livewire.includes.sort-table', ['column' => 'subservicio', 'displayName' => $nombreSubservicio])
                                    @endforeach

                                    @include('livewire.includes.sort-table', ['column' => 'iva', 'displayName' => 'IVA'])
                                    @include('livewire.includes.sort-table', ['column' => 'sobrante', 'displayName' => 'Sobrante'])
                                    @include('livewire.includes.sort-table', ['column' => 'total', 'displayName' => 'Total'])
                                </tr>
                            </thead>
                            <tbody>
                                <tr wire:key="" class="border-b text-left">
                                    @foreach($servicios as $servicio)
                                        @if(isset($cantidad[$servicio->servicio_id]) && $cantidad[$servicio->servicio_id] > 0)
                                            <th class="px-4 py-3 font-medium text-black">{{ $servicio->nombre }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $cantidad[$servicio->servicio_id] }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $envio_base[$servicio->servicio_id] }} Bs.</th>
                                            @foreach($allSubservicios as $nombreSubservicio)
                                                <th class="px-4 py-3 font-medium text-black">
                                                    {{ $subservicios[$servicio->servicio_id][$nombreSubservicio] ?? 0 }} Bs.
                                                </th>
                                            @endforeach
                                            <th class="px-4 py-3 font-medium text-black">{{ $iva[$servicio->servicio_id] }} Bs.</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $sobrante[$servicio->servicio_id] }} Bs.</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $total_pagado[$servicio->servicio_id] }} Bs.</th>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>

                        @if (!array_sum($cantidad) > 0)
                        <tr class="border-b text-center">
                            <th colspan="6" class="py-7 text-default text-2xl">No hay Envíos</th>
                        </tr>
                    @else
                        <tr>
                            @include('livewire.includes.sort-table', ['column' => 'total_internacional', 'displayName' => 'Total Internacional'])
                            <th class="px-4 py-3 font-medium text-black">{{$total_internacional ?? 0}} Bs.</th>
                            @include('livewire.includes.sort-table', ['column' => 'total_nacional', 'displayName' => 'Total Nacional'])
                            <th class="px-4 py-3 font-medium text-black">{{$total_nacional ?? 0}} Bs.</th>
                            @include('livewire.includes.sort-table', ['column' => 'total_total', 'displayName' => 'Total'])
                            <th class="px-4 py-3 font-medium text-black">{{$total_total ?? 0}} Bs.</th>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/SheetJS/0.17.5/xlsx.full.min.js"></script>

@push('scripts')

<script>
    document.getElementById('printPdf').addEventListener('click', function() {
        document.getElementById('pdfLogo').classList.remove('hidden');
        html2pdf().from(document.getElementById('contentToPrint')).save().then(() => {
            document.getElementById('pdfLogo').classList.add('hidden');
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('exportExcel').addEventListener('click', function() {
            var table = document.getElementById('dataTable'); // Solo la tabla de datos
            
            // Convertimos la tabla a una hoja de Excel
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(table); 
            
            // Aseguramos que los datos numéricos de IVA, sobrante y total sean reconocidos en Excel
            var range = XLSX.utils.decode_range(ws['!ref']);
            for (var R = range.s.r; R <= range.e.r; ++R) { // Recorremos las filas
                for (var C = range.s.c; C <= range.e.c; ++C) { // Recorremos las columnas
                    var cell_address = { c: C, r: R };
                    var cell_ref = XLSX.utils.encode_cell(cell_address);
                    
                    if (ws[cell_ref] && ws[cell_ref].v) {
                        var cellValue = ws[cell_ref].v;
                        if (!isNaN(parseFloat(cellValue)) && isFinite(cellValue)) {
                            ws[cell_ref].t = 'n'; // Asegura que sea tipo numérico
                        }
                    }
                }
            }

            // Ajustamos los anchos de las columnas automáticamente
            ws['!cols'] = [
                { wch: 20 }, // Servicio
                { wch: 15 }, // Cantidad
                { wch: 15 }, // Tarifa Base
                { wch: 15 }, // IVA
                { wch: 15 }, // Sobrante
                { wch: 15 }  // Total
            ];

            // Agregamos la hoja al libro de Excel
            XLSX.utils.book_append_sheet(wb, ws, "Cierre de Caja");

            // Descargamos el archivo Excel
            XLSX.writeFile(wb, "cierre_caja.xlsx");
        });
    });
</script>





@endpush

