@section('titulo')
    Reportes Presidencia
@endsection
<div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
                Reportes Presidencia
            </h1>
        </div>
    </div>

    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
        {{-- <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
            <div class="flex w-full md:w-auto">
                <div class="relative w-full flex items-center">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                            fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2"
                                    placeholder="Buscar...">
                </div>
            </div>
        </div> --}}

        <div class="flex flex-col md:flex-row gap-2 mb-2 ml-2">
            <div>
                <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Desde</label>
                <input id="desde" type="date" wire:model.live="desde"
                    class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>
            <div>
                <label for="hasta" class="block text-sm font-medium text-gray-700 mt-4">Hasta</label>
                <input id="hasta" type="date" wire:model.live="hasta"
                    class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mt-4">Estado</label>
                <select wire:model.live="estado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" id="estado">
                    <option value="">Seleccionar Estado</option>
                    @foreach ($estados as $estado)
                        <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="oficina" class="block text-sm font-medium text-gray-700 mt-4">Oficina</label>
                <select wire:model.live="oficina" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" id="oficina">
                    <option value="">Seleccionar Oficina</option>
                    @foreach ($oficinas as $oficina)
                        <option value="{{$oficina->oficina_id}}">{{$oficina->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-2 mt-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 ml-2">
                {{--
                    Cada boton usa el mismo patron: mientras corre su accion se
                    deshabilita, la leyenda "Descargar reporte" se reemplaza por
                    "Generando..." y aparece un spinner SVG animado con Tailwind.
                    No se usa Font Awesome: el proyecto no lo tiene instalado, por
                    lo que los <i class="fas ..."> anteriores no renderizaban nada.
                --}}
                @php
                    $reportes = [
                        ['metodo' => 'estado_a_estado',   'icono' => '📊', 'titulo' => 'Master de Estado a Estado', 'color' => 'bg-blue-100 text-blue-900'],
                        ['metodo' => 'semaforo_postal',   'icono' => '📈', 'titulo' => 'Semaforo Postal',           'color' => 'bg-green-100 text-green-900'],
                        ['metodo' => 'control_de_gastos', 'icono' => '📅', 'titulo' => 'Control de Gastos',         'color' => 'bg-yellow-100 text-yellow-900'],
                        ['metodo' => 'dclm23',            'icono' => '🧾', 'titulo' => 'DCLM23',                    'color' => 'bg-red-100 text-red-900'],
                        ['metodo' => 'devoluciones',      'icono' => '📦', 'titulo' => 'Devoluciones',              'color' => 'bg-blue-100 text-blue-900'],
                        ['metodo' => 'css_estado',        'icono' => '🔁', 'titulo' => 'CSS-Estados',               'color' => 'bg-green-100 text-green-900'],
                        ['metodo' => 'entrega',           'icono' => '📤', 'titulo' => 'Entregas',                  'color' => 'bg-yellow-100 text-yellow-900'],
                        ['metodo' => 'envios_consignados','icono' => '🧾', 'titulo' => 'Envios Consignados',        'color' => 'bg-red-100 text-red-900'],
                        ['metodo' => 'envios_recibidos',  'icono' => '📥', 'titulo' => 'Envios Recibidos',          'color' => 'bg-blue-100 text-blue-900'],
                    ];
                @endphp

                @foreach ($reportes as $reporte)
                    <button wire:click="{{ $reporte['metodo'] }}" wire:loading.attr="disabled" wire:target="{{ $reporte['metodo'] }}"
                        class="border border-gray-300 rounded p-4 text-center hover:opacity-90 transition disabled:opacity-60 disabled:cursor-wait {{ $reporte['color'] }}">
                        {{ $reporte['icono'] }} <strong>{{ $reporte['titulo'] }}</strong><br>

                        <span class="text-sm" wire:loading.remove wire:target="{{ $reporte['metodo'] }}">
                            Descargar reporte
                        </span>

                        <span class="text-sm inline-flex items-center justify-center gap-2" wire:loading wire:target="{{ $reporte['metodo'] }}">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Generando...
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        // success alert Livewire
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>

@endpush

