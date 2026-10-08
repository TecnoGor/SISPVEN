<div>
    @section('titulo')
        Alianza y Recaudacion
    @endsection
    
    <div class="mb-4 sm:mb-0 text-left px-6 sm:px-8 lg:px-12 max-w-7xl mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Alianza y Recaudacion</h1>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL (LA TARJETA BLANCA) --}}
        
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

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
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                
                <div class="flex flex-col md:flex-row gap-4 w-full items-start md:items-center">

                    <div class="flex flex-col h-full pt-4 md:pt-0"> 
                        
                        {{-- <div class="flex items-center">
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <span class="text-sm text-default">Inactivos</span>
                                <input type="checkbox" class="sr-only peer" wire:model.live="estatus_aut">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-2 focus:ring-[#6b1820] after:content-[''] 
                                    after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                </div>
                                <span class="text-sm text-default">Activos</span>
                            </label>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Documento</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Teléfono</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Correo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo de Alianza</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Porcentaje</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Alianza</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Pago</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($autorizados as $index => $aut)
                            <tr wire:key="{{ $aut->cliente_corporativo_id }}" class="border-b hover:bg-gray-50 transition duration-150 {{ $index % 2 == 0 ? '' : '' }}">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $aut->razon_social}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $aut->tipo_documento.'-'.$aut->numero_documento}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{$aut->telefono}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{$aut->correo}}</td>
                                {{-- <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($aut->activo)
                                        <button wire:click="inoperativo({{ $aut->cliente_corporativo_autorizado_id }})"
                                            title="Inoperativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-700">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="operativo({{ $aut->cliente_corporativo_autorizado_id }})"
                                            title="Operativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td> --}}
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($aut->alianza)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $aut->alianza->alianza?->nombre ?? 'Tipo no definido' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Sin alianza
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($aut->alianza)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ number_format((float) $aut->alianza->porcentaje, 2, ',', '.') }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-blue-800 text-center">
                                    <button wire:click='alianza({{$aut->cliente_corporativo_id}})'
                                        title="{{ $aut->alianza ? 'Editar alianza' : 'Registrar alianza' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($aut->alianza)
                                        <button wire:click='registrarPago({{$aut->cliente_corporativo_id}})'
                                            title="Registrar pago" class="text-[#8B1D1D] hover:scale-110 transition-transform">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300" title="Requiere alianza registrada">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="8" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay aliados autorizadas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
                {{ $autorizados->links() }}
            </div>

        </div> 
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4"> 
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenido del Modal --}}
                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    
                    <form wire:submit.prevent="registrar_alianza">
                        
                        {{-- ENCABEZADO DEL MODAL --}}
                        <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1M9 13h1M9 17h1m4-10h1m4 6h1m-4 6h1m-4 6h1" />
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                    {{ $alianza_id ? 'Editar Alianza' : 'Nueva Alianza' }} {{ $cliente?->razon_social }}
                                </h3>
                            </div>
                            {{-- Botón de Cierre (X) --}}
                            <button type="button" wire:click="cerrarModal" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="tipo_alianza" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Alianza<span class="text-red-500">*</span></label>
                                <select id="tipo_alianza" wire:model.live="tipo_alianza" 
                                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 bg-white text-gray-800 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm transition duration-150">
                                    <option value="" selected hidden>Seleccionar Tipo</option>
                                    @foreach ($tipos_alianzas as $tipo)
                                        <option value="{{ $tipo->tipo_alianza_id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_alianza') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="porcentaje" class="block text-sm font-medium text-gray-700 mb-1">Porcentaje de Ipostel:<span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <input type="text" id="porcentaje" wire:model.blur="porcentaje"
                                        inputmode="decimal" maxlength="6" placeholder="0,00"
                                        class="focus:ring-[#6b1820] focus:border-[#6b1820] block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2.5 pl-3 pr-8 text-gray-800 transition duration-150"
                                        oninput="formatPorcentaje(this)">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 sm:text-sm pointer-events-none">%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Valor entre 1 y 100, hasta 2 decimales (ej. 12,50).</p>
                                @error('porcentaje') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        {{-- PIE DE MODAL (ACCIONES) --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            {{-- Botón Guardar --}}
                            <x-button
                                type="button"
                                wire:click="registrar_alianza"
                                class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                            >
                                Guardar Registro
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: REGISTRO DE PAGO DEL ALIADO --}}
    @if($showModalPago && $pago_cliente && $pago_alianza)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-pago-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-4xl my-8 overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">

                    {{-- ENCABEZADO --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800" id="modal-pago-title">
                                    Registrar Pago
                                </h3>
                                <p class="text-xs text-gray-500">{{ $pago_cliente->razon_social }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="cerrarModalPago" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- DATOS DE LA ALIANZA --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aliado</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $pago_cliente->razon_social }}</p>
                                <p class="text-xs text-gray-500">{{ $pago_cliente->tipo_documento.'-'.$pago_cliente->numero_documento }}</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tipo de Alianza</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $pago_alianza->alianza?->nombre ?? 'Tipo no definido' }}</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Porcentaje IPOSTEL</p>
                                <p class="text-sm font-semibold text-[#6b1820] mt-1">{{ number_format($this->porcentajeAlianza, 2, ',', '.') }}%</p>
                            </div>
                        </div>

                        {{-- CAJA DE PAGO (componente compartido) --}}
                        <div class="border border-gray-200 rounded-lg p-4">
                            <livewire:caja-de-pago.caja-de-pago />
                        </div>

                        {{-- DISTRIBUCION DEL MONTO --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-gray-800 text-white rounded-lg p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Monto Total Cobrado</p>
                                <p class="text-xl font-bold mt-1">{{ number_format((float) $monto_pagado, 2, ',', '.') }} Bs</p>
                            </div>
                            <div class="bg-[#6b1820] text-white rounded-lg p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">
                                    Corresponde a IPOSTEL ({{ number_format($this->porcentajeAlianza, 2, ',', '.') }}%)
                                </p>
                                <p class="text-xl font-bold mt-1">{{ number_format($this->montoComision, 2, ',', '.') }} Bs</p>
                            </div>
                            <div class="bg-green-700 text-white rounded-lg p-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Corresponde al Aliado</p>
                                <p class="text-xl font-bold mt-1">{{ number_format($this->montoAliado, 2, ',', '.') }} Bs</p>
                            </div>
                        </div>

                        {{-- OBSERVACION --}}
                        <div>
                            <label for="observacion_pago" class="block text-sm font-medium text-gray-700 mb-1">Observación (opcional)</label>
                            <textarea id="observacion_pago" wire:model.blur="observacion_pago" rows="2" maxlength="255"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 text-gray-800 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm transition duration-150"
                                placeholder="Detalle del pago recibido..."></textarea>
                        </div>

                        {{-- AVISO: AUN NO SE PERSISTE --}}
                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <p class="text-xs text-amber-800">
                                <span class="font-semibold">Vista preliminar.</span>
                                El cálculo se muestra en pantalla pero el pago todavía no se guarda en base de datos.
                            </p>
                        </div>
                    </div>

                    {{-- PIE --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <button type="button" wire:click="cerrarModalPago"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium text-sm transition duration-150">
                            Cancelar
                        </button>
                        <x-button
                            type="button"
                            wire:click="guardar_pago"
                            class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition duration-150 shadow-sm"
                        >
                            Registrar Pago
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Livewire 3 expone 'livewire:init' (en v2 era 'livewire:load').
        document.addEventListener('livewire:init', function () {
            Livewire.on('alertSuccess', message => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('alertSuccess3', message => {
                Swal.fire({
                    position: "center",
                    icon: "info",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        });
    </script>

    <script>
        // Formatea el input como porcentaje (no como moneda): la parte entera se
        // escribe tal cual y la coma separa hasta 2 decimales. Tope en 100.
        function formatPorcentaje(input) {
            // Solo dígitos y una coma como separador decimal.
            let value = input.value.replace(/[^0-9,]/g, '');

            const partes = value.split(',');
            let entera = partes[0] ?? '';
            let decimal = partes.length > 1 ? partes.slice(1).join('').slice(0, 2) : null;

            // Sin ceros a la izquierda ("05" -> "5"), pero conservando "0".
            if (entera.length > 1) {
                entera = entera.replace(/^0+/, '') || '0';
            }

            // Tope: nada por encima de 100.
            if (entera !== '' && parseInt(entera, 10) > 100) {
                entera = '100';
                decimal = decimal !== null ? '00' : null;
            }
            if (entera === '100' && decimal !== null && parseInt(decimal.padEnd(2, '0'), 10) > 0) {
                decimal = '00';
            }

            input.value = decimal !== null ? `${entera},${decimal}` : entera;
        }
    </script>
    @endpush
</div>
