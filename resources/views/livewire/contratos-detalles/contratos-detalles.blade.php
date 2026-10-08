<div>
    @section('titulo')
        Detalles de Contratos Corporativos
    @endsection

    {{-- Contenedor principal --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 text-left">
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Detalles de Contratos Corporativos</h1>
            <p class="mt-2 text-sm text-gray-600">Listado y gestión de los contratos corporativos.</p>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
        @endpush

        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4">

            <div class="flex flex-col md:flex-row items-start justify-between pb-4 gap-3">

                <div class="flex w-full md:w-2/3 items-center">
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400"
                                fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm
                                rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820]
                                block w-full pl-10 p-2.5 transition duration-150"
                            placeholder="Buscar Tipo Contrato...">
                    </div>
                </div>

                {{-- Espacio para botones/acciones --}}
                <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                    {{-- <x-button class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm bg-[#6b1820] text-white hover:bg-[#7b1f27]" wire:click="reporte_excel">
                        REPORTE EXCEL
                    </x-button> --}}
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-4 items-end justify-start">
                
                <div class="w-full md:w-1/3">
                    <label for="clientes" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Cliente</label>
                    <select id="clientes" wire:model.live="cliente" class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                        <option value="">Seleccionar Cliente</option>
                        @foreach ($clientes as $clie)
                            <option value="{{$clie->cliente_corporativo_id}}">{{$clie->razon_social}}</option>
                        @endforeach
                    </select>
                    @error('tipo_documento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo de Contrato</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha de Inicio</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha de Finalizacion</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cuotas</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Envíos</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($contratos as $index => $contrato)
                            <tr wire:key="{{ $contrato->contrato_corporativo_id }}" class="border-b text-left hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $contrato->tipo_contrato->descripcion}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $contrato->fecha_inicio}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ $contrato->fecha_fin }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    <button type="button" wire:click="verDetalles({{$contrato->contrato_corporativo_id }})" class="text-blue-600 hover:text-blue-800 transition duration-150" title="Ver Detalles de Cuotas">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    <button type="button" wire:click="verEnvios({{$contrato->contrato_corporativo_id }})" class="text-primary hover:text-red-700 transition duration-150" title="Ver Envíos del Contrato">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18L17.25 9m0 0v5.625M17.25 9h-5.625M10.5 18H5.25A2.25 2.25 0 0 1 3 15.75V5.25A2.25 2.25 0 0 1 5.25 3h10.5A2.25 2.25 0 0 1 18 5.25v5.25"/>
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if ($contrato->activo)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Activo</span>
                                    @else
                                        <button type="button" wire:click="activarContrato({{$contrato->contrato_corporativo_id}})" class="inline-flex items-center px-3 py-1 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full hover:bg-red-200 transition duration-150" title="Activar Contrato">Inactivo</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="6" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay contratos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
            </div>

        </div>
        
        <div class="mt-4">
            <div class="py-1 px-3 flex items-center justify-start gap-4">
                <label for="paginacion_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                <select wire:model.live="perPage" id="paginacion_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                </select>
            </div>
        </div>
    </div>


    {{-- MODAL PRINCIPAL DE CUOTAS --}}
    @if($modal_open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" wire:click.self="cerrarCuotas">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-[#8B1D1D]">DETALLE DE CUOTAS</h2>
                    <button wire:click="cerrarCuotas" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <table class="w-full text-sm text-center border rounded-lg overflow-hidden">
                        <thead class="bg-[#F8F9FA] text-[#6B7280] font-bold text-[11px] tracking-widest uppercase">
                            <tr>
                                <th class="px-4 py-3 border-b">Cuota</th>
                                <th class="px-4 py-3 border-b">Fecha Límite</th>
                                <th class="px-4 py-3 border-b">Fecha de Pago</th>
                                <th class="px-4 py-3 border-b">Estatus</th>
                                <th class="px-4 py-3 border-b">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($detalles as $index => $detalle)
                                <tr wire:key="{{ $detalle->contrato_corporativo_detalle_id }}" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-4 font-bold text-gray-700">
                                        {{ number_format($detalle->cuota, 2, ',', '.') }}
                                        <span class="text-xs text-gray-500">{{ $detalle->contrato->divisa->nombre ?? '' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">{{ $detalle->fecha_limite }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $detalle->fecha_cancelada ?: 'N/A' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $detalle->cancelada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $detalle->cancelada ? 'PAGADA' : 'SIN PAGAR' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-center gap-3">
                                            @if(!$detalle->cancelada)
                                                <button type="button" 
                                                    onclick="event.stopPropagation(); @this.call('pagar', {{ $detalle->contrato_corporativo_detalle_id }})"
                                                    class="text-[#8B1D1D] hover:scale-110 transition-transform" title="Pagar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                    </svg>
                                                </button>

                                                <button type="button"
                                                    onclick="event.stopPropagation(); @this.call('modificar_tarifa', {{ $detalle->contrato_corporativo_detalle_id }})"
                                                    class="text-blue-500 hover:scale-110 transition-transform" title="Modificar Tarifa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-green-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right">
                    <x-primary-button type="button" wire:click="cerrarCuotas" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-xs uppercase tracking-widest transition-colors">
                        Cerrar
                    </x-primary-button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DE PAGO --}}
    @if($modal_open2)
        <div wire:click.self="cerrarPago" class="fixed inset-0 z-[1000] overflow-y-auto" aria-labelledby="modal-pago" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-5xl overflow-hidden align-middle transition-all transform bg-white rounded-2xl shadow-2xl">
                    {{-- Encabezado --}}
                    <div class="bg-gradient-to-r from-[#8B1D1D] to-[#6f1616] px-6 py-4 flex items-center justify-between border-b border-white/20">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white tracking-wide uppercase" id="modal-pago">
                                Procesar Pago de Cuota
                            </h3>
                        </div>
                        <button type="button" wire:click="cerrarPago" class="text-white/70 hover:text-white hover:bg-white/10 p-1.5 rounded-full transition-colors focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Cuerpo del Modal --}}
                    <div class="p-6 bg-gray-50 flex flex-col md:flex-row gap-6">
                        
                        {{-- Sección Principal: Caja de Pagos --}}
                        <div class="w-full md:w-2/3 bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
                            <livewire:caja-de-pago.caja-de-pago/>
                        </div>

                        {{-- Sección Lateral: Resumen y Acciones --}}
                        <div class="w-full md:w-1/3 flex flex-col gap-5">
                            
                            {{-- Tarjeta de Monto --}}
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center">
                                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#8B1D1D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                    </svg>
                                </div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cuota Pendiente</h4>

                                {{-- Monto en divisa --}}
                                <p class="text-2xl font-black text-gray-700">
                                    {{ number_format($cuota_divisa ?? 0, 2, ',', '.') }}
                                    <span class="text-sm text-gray-500 font-bold">{{ $divisa_nombre }}</span>
                                </p>

                                {{-- Separador --}}
                                <div class="flex items-center gap-2 my-3 w-full">
                                    <div class="flex-1 h-px bg-gray-200"></div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Equivalente</span>
                                    <div class="flex-1 h-px bg-gray-200"></div>
                                </div>

                                {{-- Monto en Bs (lo que debe pagar) --}}
                                <p class="text-3xl font-black text-[#8B1D1D] drop-shadow-sm">
                                    {{ number_format($cuota_bs ?? 0, 2, ',', '.') }}
                                    <span class="text-base font-bold">Bs</span>
                                </p>

                                {{-- Tasa aplicada --}}
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-2">
                                    Tasa: {{ number_format($tasa_aplicada ?? 0, 2, ',', '.') }} Bs / {{ $divisa_nombre }}
                                </p>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="flex flex-col gap-3 mt-auto">
                                <button type="button" 
                                    wire:click="aprobar_pago" 
                                    wire:loading.attr="disabled" 
                                    wire:target="aprobar_pago"
                                    class="w-full bg-[#8B1D1D] hover:bg-[#6f1616] text-white font-bold py-4 px-6 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 group disabled:opacity-70 disabled:cursor-not-allowed">
                                    
                                    <span wire:loading.remove wire:target="aprobar_pago" class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        APROBAR PAGO
                                    </span>

                                    <span wire:loading wire:target="aprobar_pago" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Procesando...
                                    </span>
                                </button>

                                <button type="button" 
                                    wire:click="cerrarPago"
                                    class="w-full bg-white hover:bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-xl border border-gray-200 transition-colors">
                                    Cancelar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DE MODIFICAR TARIFA --}}
    @if($modal_open3)
        <div wire:click.self="cerrar_tarifa" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 pointer-events-auto">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 relative">
                
                <button type="button" wire:click="cerrar_tarifa" class="absolute top-4 right-4 p-1 rounded-full text-white/70 hover:text-white hover:bg-white/10 z-50 transition" aria-label="Cerrar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="px-6 py-4 bg-gradient-to-r from-[#8B1D1D] to-[#6f1616]">
                    <h2 class="text-white text-lg font-semibold uppercase tracking-wide text-center">
                        Modificar Cuota
                    </h2>
                </div>

                <div class="px-6 py-6 space-y-5">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">
                            Fecha límite asociada
                        </p>
                        <p class="text-base font-semibold text-gray-800">
                            {{ $tarifa['fecha_limite'] }}
                        </p>
                    </div>

                    <form wire:submit.prevent="cambiar_tarifa">
                        <div>
                            <label for="cuota" class="block text-sm font-semibold text-gray-600 mb-2 text-center">
                                Nueva Cuota a Pagar
                            </label>
                            <input 
                                id="cuota"
                                type="text"
                                wire:model="tarifa_nueva"
                                onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                oninput="formatCurrency(this)"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-center text-xl font-bold text-[#8B1D1D] focus:outline-none focus:ring-2 focus:ring-[#8B1D1D]/40 focus:border-[#8B1D1D] transition"
                            >
                            @error('tarifa_nueva')
                                <p class="text-red-500 text-xs mt-1 text-center">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200">
                    <x-button 
                        type="button" 
                        wire:click="cambiar_tarifa" 
                        wire:loading.attr="disabled" 
                        wire:target="cambiar_tarifa"
                        class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                    >
                        Guardar Cambios
                    </x-button>

                    <x-button 
                        type="button" 
                        wire:click="cerrar_tarifa"
                        class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                    >
                        Cerrar
                    </x-button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DE ENVIOS --}}
    @if($modal_open4)
        <div wire:click.self="cerrarCuotas" class="bg-gray-800 bg-opacity-25 fixed inset-0 z-40 flex justify-center overflow-y-auto pointer-events-auto">
            <div class="py-12">
                <div class="mx-auto sm:px-6 lg:px-8 mt-10 max-w-[350px] sm:max-w-[1000px]">
                    <div class="bg-white shadow rounded-lg p-5 max-h-[600px] overflow-y-auto relative">
                        <button type="button" wire:click="cerrarCuotas" class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-200 z-30" aria-label="Cerrar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <table class="w-full text-sm">
                            <thead class="text-sm text-red-700 uppercase bg-gray-100">
                                <tr>
                                <th class="px-4 py-3 text-center w-1/6 sticky top-0 bg-gray-100 z-10">Codigo</th>
                                    <th class="px-4 py-3 text-center w-1/6 sticky top-0 bg-gray-100 z-10">Tipo de Envio</th>
                                    <th class="px-4 py-3 text-center w-1/6 sticky top-0 bg-gray-100 z-10">Autorizado</th>
                                    <th class="px-4 py-3 text-center w-1/6 sticky top-0 bg-gray-100 z-10">Destinatario</th>
                                    <th class="px-4 py-3 text-center w-1/6 sticky top-0 bg-gray-100 z-10">Peso</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($envios as $index => $env)
                                    <tr wire:key="{{ $env->envio_id }}" class="border-b text-left {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-200' }}">
                                        <th class="px-4 py-3 font-medium text-black text-center">{{ $env->codigo_envio}}</th>
                                        <th class="px-4 py-3 font-medium text-black text-center">{{ $env->servicio->nombre}}</th>
                                        <th class="px-4 py-3 font-medium text-black text-center">{{ $env->autorizado->nombre ?? 'Sin Autorizado'}}</th>
                                        <th class="px-4 py-3 font-medium text-black text-center">{{ $env->nombre_dest.' '.$env->apellido_dest }}</th>
                                        <th class="px-4 py-3 font-medium text-black text-center">{{$env->peso}}Gr</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-right mt-4">
                            <x-primary-button type="button" class="mb-2 sm:mb-0 mt-2" wire:click="cerrarCuotas">
                                Cerrar
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        // success alert
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

         // success alert
        Livewire.on('alertSuccess3', message => {
            Swal.fire({
                position: "center",
                icon: "info",
                title: message.message,
                showConfirmButton: false,
                timer: 5000
            });
        })

        Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
    @endscript

    <script>
        function formatCurrency(input) {
                    // Eliminar caracteres no numéricos
                    let value = input.value.replace(/[^0-9]/g, '');

                    // Convertir a número y formatear
                    if (value.length === 0) {
                        input.value = '0,00';
                        return;
                    }

                    // Convertir a centimos
                    let cents = parseInt(value, 10);

                    // Formatear a bs y centimos
                    let bs = Math.floor(cents / 100);
                    let formattedCents = (cents % 100).toString().padStart(2, '0');

                    // // Agregar separador de miles
                    let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Actualizar el valor del input
                    input.value = `${formattedbs},${formattedCents}`;
                    console.log('entro');

                }
    </script>
@endpush
