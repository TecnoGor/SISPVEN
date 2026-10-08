<div>
    @section('titulo')
        Almacenamiento
    @endsection
    
    <!-- Cabecera -->
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">ALMACENAMIENTO</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de contratos y espacios de almacenamiento.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <!-- Contenedor principal -->
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            <!-- Barra superior: búsqueda + botón -->
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm" wire:click="modalOpen">
                            Generar Contrato
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Tabla de contratos -->
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cliente</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Espacio</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Finalización</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cuotas</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($clientes as $index => $cliente)
                            <tr wire:key="{{ $cliente->contrato_almacenamiento_id }}" class="border-b hover:bg-gray-50 transition duration-150 {{ $index % 2 == 0 ? '' : '' }}">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $cliente->cliente->razon_social}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $cliente->espacio}} m²</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $cliente->fecha_fin ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('d/m/Y') : '' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <button wire:click="mostrarCuotas({{ $cliente->contrato_almacenamiento_id }})" class="inline-flex items-center justify-center p-2 rounded text-green-700 hover:bg-gray-50 transition">
                                        <!-- icono -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="4" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay Contratos Activos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="py-2 px-3">
                {{$clientes->links()}}
            </div>

        </div>
    </div>

    <!-- SELECT -->
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
        </div>
    </div>

    @if($modal_crear)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">Contrato de Almacenamiento</h3>
                        </div>

                        <button type="button" wire:click="modalClose" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit="submit">
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                <div class="col-span-1">
                                    <label for="cliente_corporativo" class="block text-sm font-medium text-gray-700 mb-1">Cliente Corporativo<span class="text-red-500">*</span></label>
                                    <select id="cliente_corporativo" wire:model.live="cliente_corporativo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Cliente:</option>
                                        @foreach ($clientes_corporativos as $clientes)
                                            <option value="{{$clientes->cliente_corporativo_id}}"> {{$clientes->razon_social}} </option>
                                        @endforeach
                                    </select>
                                    @error('cliente_corporativo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="metros_cuadrados" class="block text-sm font-medium text-gray-700 mb-1">Metros Cuadrados<span class="text-red-500">*</span></label>
                                    <input type="text" inputmode="decimal" id="metros_cuadrados" wire:model.live="metros_cuadrados"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        oninput="this.value = this.value.replace(/[^0-9.,]/g, '').replace(/([.,].*)[.,]/g, '$1');">
                                    @error('metros_cuadrados') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="parametro_id" class="block text-sm font-medium text-gray-700 mb-1">Divisa<span class="text-red-500">*</span></label>
                                    <select id="parametro_id" wire:model.live="parametro_id" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar Divisa:</option>
                                        @foreach ($divisas_activas as $divisa)
                                            <option value="{{ $divisa->parametro_id }}">
                                                {{ $divisa->nombre }} — {{ number_format($divisa->valor, 2, ',', '.') }} Bs
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parametro_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="tarifa" class="block text-sm font-medium text-gray-700 mb-1">Tarifa<span class="text-red-500">*</span></label>
                                    <input type="text" id="tarifa" wire:model.live="tarifa" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                        oninput="formatCurrency(this)">
                                    @error('tarifa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label for="cuotas" class="block text-sm font-medium text-gray-700 mb-1">Cuotas<span class="text-red-500">*</span></label>
                                    <input type="text" inputmode="numeric" id="cuotas" wire:model.live="cuotas"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    @error('cuotas') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Equivalente en Bs</label>
                                    <div class="block w-full border border-gray-200 rounded-lg bg-gray-50 py-2 px-3 text-sm text-gray-700">
                                        {{ number_format($preview_bs, 2, ',', '.') }} Bs
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button
                                type="button"
                                wire:click='crear_contrato'
                                wire:loading.attr="disabled"
                                wire:target="crear_contrato"
                                class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="crear_contrato">Aceptar</span>
                                <span wire:loading wire:target="crear_contrato">Procesando...</span>
                            </x-button>

                            <x-button 
                                type="button" 
                                wire:click="modalClose" 
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                            >Cerrar</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($ver_detalles)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-[#8B1D1D]">DETALLE DE CUOTAS</h2>
                <button wire:click="modalClose" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <table class="w-full text-sm text-center border rounded-lg overflow-hidden">
                    <thead class="bg-[#F8F9FA] text-[#6B7280] font-bold text-[11px] tracking-widest uppercase">
                        <tr>
                            <th class="px-4 py-3">Cuota</th>
                            <th class="px-4 py-3">Fecha Límite</th>
                            <th class="px-4 py-3">Estatus</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($detalles as $detalle)
                        <tr>
                            <td class="px-4 py-4 font-bold text-gray-700">
                                {{ number_format($detalle->cuota, 2, ',', '.') }}
                                {{ $detalle->almacenamiento && $detalle->almacenamiento->divisa ? $detalle->almacenamiento->divisa->nombre : '' }}
                            </td>
                            <td class="px-4 py-4 text-gray-600 text-center">
                                {{ $detalle->fecha_limite ? \Carbon\Carbon::parse($detalle->fecha_limite)->format('d/m/Y') : '' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $detalle->cancelada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $detalle->cancelada ? 'PAGADO' : 'PENDIENTE' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 flex justify-center gap-3">
                                @if(!$detalle->cancelada)
                                <button wire:click="pagar({{$detalle->contrato_corporativo_detalle_id }})" class="text-[#8B1D1D] hover:scale-110 transition-transform" title="Pagar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                </button>
                                <button wire:click="modificar_tarifa({{$detalle->contrato_corporativo_detalle_id }})" class="text-blue-500 hover:scale-110 transition-transform" title="Modificar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($modal_pagar)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-6xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">

                    {{-- Header --}}
                    <div class="bg-gray-50/80 px-8 py-5 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-xl text-primary">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Registro de Pago</h3>
                        </div>
                        <button type="button" wire:click="cerrar_pago" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-200/50 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                            {{-- Caja de pago --}}
                            <div class="lg:col-span-7 border-r border-gray-200 pr-8">
                                <livewire:caja-de-pago.caja-de-pago />
                            </div>

                            {{-- Detalle de cuota --}}
                            <div class="lg:col-span-5 flex flex-col justify-center space-y-5">
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 space-y-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Detalle de Cuota</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Cuota</span>
                                        <span class="text-base font-bold text-gray-800">
                                            {{ number_format($cuota_divisa ?? 0, 2, ',', '.') }} {{ $divisa_nombre }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Tasa</span>
                                        <span class="text-base font-bold text-gray-800">
                                            {{ number_format($tasa_aplicada ?? 0, 2, ',', '.') }} Bs
                                        </span>
                                    </div>

                                    <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest">A pagar</span>
                                        <span class="text-xl font-black text-primary">
                                            {{ number_format($cuota_bs ?? 0, 2, ',', '.') }} Bs
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50/50 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-200">
                        <button type="button" wire:click="aprobar_pago" wire:loading.attr="disabled" wire:target="aprobar_pago"
                            class="bg-primary hover:bg-red-800 text-white font-black py-3.5 px-8 rounded-2xl shadow-lg flex-1 transition active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Aprobar Pago
                        </button>
                        <button type="button" wire:click="cerrar_pago"
                            class="bg-white border-2 border-gray-200 text-gray-500 font-bold py-3.5 px-6 rounded-2xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($modal_modificar_tarifa)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
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
                        {{ $this->detalle_modificar && $this->detalle_modificar->fecha_limite ? \Carbon\Carbon::parse($this->detalle_modificar->fecha_limite)->format('d/m/Y') : '' }}
                    </p>

                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2 text-center">
                        Nueva Cuota
                    </label>
                    <input 
                        type="text"
                        wire:model="tarifa_nueva"
                        oninput="formatCurrency(this)"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-center text-xl font-bold text-[#8B1D1D] focus:outline-none focus:ring-2 focus:ring-[#8B1D1D]/40 focus:border-[#8B1D1D]"
                    >
                    @error('tarifa_nueva')
                        <p class="text-red-500 text-xs mt-1 text-center">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200">
                <x-button 
                    type="button"
                    wire:click="cambiar_tarifa"
                    class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                >
                    Guardar Cambios
                </x-button>

                <x-button
                    type="button"
                    wire:click="cerrar_modificar_tarifa"
                    class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                >
                    Cerrar
                </x-button>
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
                timer: 1500
            });
        })

         // success alert
         Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
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

        Livewire.on('cliente_registrado', () => {
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