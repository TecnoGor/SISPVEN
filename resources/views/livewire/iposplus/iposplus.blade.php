<div>
    @section('titulo')
        Iposplus
    @endsection
    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight uppercase">Iposplus</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de envíos Iposplus por lotes.</p>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush


    {{-- Contenedor principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-800 shadow-lg rounded-xl p-6 border border-slate-200 dark:border-slate-700">

        <form wire:submit.prevent="agregarAlLote" class="space-y-6">

            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                    Datos del Envío
                </h2>

                {{-- Toggle modo cálculo de peso --}}
                <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                    <span class="text-xs font-bold uppercase tracking-widest {{ $modo_peso === 'manual' ? 'text-[#6b1820]' : 'text-gray-400' }}">
                        Peso Manual
                    </span>

                    <button type="button"
                        wire:click="$set('modo_peso', '{{ $modo_peso === 'manual' ? 'volumetrico' : 'manual' }}')"
                        class="relative inline-flex items-center cursor-pointer">
                        <div class="w-12 h-6 rounded-full transition-colors {{ $modo_peso === 'volumetrico' ? 'bg-[#6b1820]' : 'bg-gray-300' }}">
                            <div class="absolute top-[2px] left-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform {{ $modo_peso === 'volumetrico' ? 'translate-x-6' : '' }}"></div>
                        </div>
                    </button>

                    <span class="text-xs font-bold uppercase tracking-widest {{ $modo_peso === 'volumetrico' ? 'text-[#6b1820]' : 'text-gray-400' }}">
                        Volumétrico
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <section class="flex flex-col gap-4 sm:flex-row w-full">
                    {{-- Peso --}}
                    <div class="w-full">
                        <label for="peso" class="block text-xs font-black text-gray-500 uppercase mb-1">
                            Peso Total (Kg)<span class="text-red-500">*</span>
                            @if($modo_peso === 'volumetrico')
                                <span class="text-[10px] font-bold text-blue-600 normal-case">(calculado)</span>
                            @endif
                        </label>
                        <input type="text" inputmode="decimal" id="peso" wire:model.live="peso"
                            @if($modo_peso === 'volumetrico') readonly @endif
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm font-bold text-red-700 {{ $modo_peso === 'volumetrico' ? 'bg-gray-100 cursor-not-allowed' : '' }}" />
                        @error('peso') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alto --}}
                    <div class="w-full">
                        <label for="alto" class="block text-xs font-black text-gray-500 uppercase mb-1">Alto (cm)@if($modo_peso === 'volumetrico')<span class="text-red-500">*</span>@endif</label>
                        <input type="number" id="alto" wire:model.live="alto" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                        @error('alto') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Largo --}}
                    <div class="w-full">
                        <label for="largo" class="block text-xs font-black text-gray-500 uppercase mb-1">Largo (cm)@if($modo_peso === 'volumetrico')<span class="text-red-500">*</span>@endif</label>
                        <input type="number" id="largo" wire:model.live="largo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                        @error('largo') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ancho --}}
                    <div class="w-full">
                        <label for="ancho" class="block text-xs font-black text-gray-500 uppercase mb-1">Ancho (cm)@if($modo_peso === 'volumetrico')<span class="text-red-500">*</span>@endif</label>
                        <input type="number" id="ancho" wire:model.live="ancho" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                        @error('ancho') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>
                </section>

                {{-- Indicador de la suma de dimensiones (máx 300) --}}
                @php
                    $sumaDimensiones = (float)$alto + (float)$ancho + (float)$largo;
                @endphp
                @if($sumaDimensiones > 0)
                    <div class="text-xs font-bold {{ $sumaDimensiones > 300 ? 'text-red-600' : 'text-gray-500' }}">
                        Suma de dimensiones: {{ number_format($sumaDimensiones, 2, ',', '.') }} / 300 cm
                        @if($sumaDimensiones > 300)
                            <span class="ml-2 px-2 py-0.5 bg-red-100 rounded">¡Supera el máximo permitido!</span>
                        @endif
                    </div>
                @endif

                {{-- Advertencia: peso supera la tarifa máxima activa --}}
                @if($excede_tarifa_max && $kilo_max_tarifa > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-xs font-bold text-amber-700">
                            El peso supera el máximo de la tarifa activa ({{ number_format($kilo_max_tarifa, 2, ',', '.') }} kg).
                            Se aplicará la tarifa máxima como referencia para el cálculo del costo.
                        </p>
                    </div>
                @endif

                {{-- Contenido del Envio --}}
                <div class="sm:col-span-1">
                    <label for="contenido" class="block text-xs font-black text-gray-500 uppercase mb-1">Contenido del Envío<span class="text-red-500">*</span></label>
                    <textarea wire:model="contenido" id="contenido" maxlength="300" rows="2" style="resize: none;" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                    @error('contenido') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                    Datos de Origen/Destino
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Remitente --}}
                <div class="flex flex-col gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-400 border-b border-red-200 pb-1">Remitente:</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento<span class="text-red-500">*</span></label>
                            <select id="tipo_documento" wire:model.live="tipo_documento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" selected>Seleccione un documento</option>
                                @foreach ($documentos as $doc)
                                    <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="documento" class="block text-sm font-medium text-gray-700">Documento<span class="text-red-500">*</span></label>
                            <input type="number" id="documento" wire:model.live="documento" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('documento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                            <input type="text" id="nombre" wire:model.live="nombre" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                            <input type="text" id="apellido" wire:model.live="apellido" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tlf" class="block text-sm font-medium text-gray-700">Nro. Teléfono<span class="text-red-500">*</span></label>
                            <input type="tel" id="tlf" wire:model.live="telefono" maxlength="11" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="correo" class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                            <input type="email" id="correo" wire:model="correo" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm" />
                            @error('correo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Destinatario --}}
                <div class="flex flex-col gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-400 border-b border-red-200 pb-1">Destinatario:</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tipo_documento_dest" class="block text-sm font-medium text-gray-700">Tipo de Documento<span class="text-red-500">*</span></label>
                            <select id="tipo_documento_dest" wire:model.live="tipo_documento_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" selected>Seleccione un documento</option>
                                @foreach ($documentos_dest as $doc)
                                    <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="documento_dest" class="block text-sm font-medium text-gray-700">Documento<span class="text-red-500">*</span></label>
                            <input type="number" id="documento_dest" wire:model.live="documento_dest" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('documento_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nombre_dest" class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                            <input type="text" id="nombre_dest" wire:model.live="nombre_dest" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('nombre_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="apellido_dest" class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                            <input type="text" id="apellido_dest" wire:model.live="apellido_dest" maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('apellido_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="estado_dest" class="block text-sm font-medium text-gray-700">Estado<span class="text-red-500">*</span></label>
                                <select id="estado_dest" wire:model.live="estado_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" selected>Seleccione un estado</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('estado_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="municipio_dest" class="block text-sm font-medium text-gray-700">Municipio<span class="text-red-500">*</span></label>
                                <select id="municipio_dest" wire:model.live="municipio_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" selected>Seleccione un municipio</option>
                                    @foreach ($municipios_dest as $municipio)
                                        <option value="{{$municipio->municipio_id}}">{{$municipio->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('municipio_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="ciudad_dest" class="block text-sm font-medium text-gray-700">Ciudad<span class="text-red-500">*</span></label>
                                <select id="ciudad_dest" wire:model.live="ciudad_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" selected>Seleccione una Ciudad</option>
                                    @foreach ($ciudades_dest as $ciudad)
                                        <option value="{{$ciudad->ciudad_id}}">{{$ciudad->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('ciudad_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="parroquia_dest" class="block text-sm font-medium text-gray-700">Parroquia<span class="text-red-500">*</span></label>
                                <select id="parroquia_dest" wire:model.live="parroquia_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" selected>Seleccione una Parroquia</option>
                                    @foreach ($parroquias_dest as $parroquia)
                                        <option value="{{$parroquia->parroquia_id}}">{{$parroquia->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('parroquia_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="w-1/2">
                            <label for="codigo_postal_dest" class="block text-sm font-medium text-gray-700">Código Postal<span class="text-red-500">*</span></label>
                            <select id="codigo_postal_dest" wire:model.live="codigo_postal_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" selected>Seleccione un Código</option>
                                @foreach ($codigos_postales_dest as $postal)
                                    <option value="{{$postal}}">{{$postal}}</option>
                                @endforeach
                            </select>
                            @error('codigo_postal_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="direccion_dest" class="block text-sm font-medium text-gray-700">Dirección de Habitación<span class="text-red-500">*</span></label>
                            <textarea wire:model="direccion_dest" id="direccion_dest" maxlength="200" rows="2" style="resize: none; font-size: 12px;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                            @error('direccion_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tlf_dest" class="block text-sm font-medium text-gray-700">Nro. Teléfono<span class="text-red-500">*</span></label>
                            <input type="tel" id="tlf_dest" wire:model.live="telefono_dest" maxlength="11" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                            @error('telefono_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="correo_dest" class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                            <input type="email" id="correo_dest" wire:model="correo_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm" />
                            @error('correo_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

            {{-- RESUMEN DEL ENVÍO ACTUAL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Ticket del envío actual --}}
                <div class="w-full">
                    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden flex flex-col relative">
                        <div class="h-2 bg-primary w-full"></div>
                        <div class="p-6 space-y-6">
                            <div class="text-center">
                                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Resumen del Envío Actual</h3>
                                <div class="mt-4 flex flex-col items-center">
                                    <span class="text-4xl font-black text-gray-900">{{ number_format($total_pagar ?? 0, 2, ',', '.') }}</span>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Bolívares</span>
                                </div>
                            </div>

                            <div class="space-y-3 pt-6 border-t border-dashed border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-500">Precio</span>
                                    <span class="text-sm font-black text-gray-800">{{ number_format($precio_total ?? 0, 2, ',', '.') }} Bs</span>
                                </div>
                                <div class="flex justify-between items-center text-blue-600">
                                    <span class="text-sm font-bold opacity-80">I.V.A (16%)</span>
                                    <span class="text-sm font-black">{{ number_format($iva ?? 0, 2, ',', '.') }} Bs</span>
                                </div>
                                <div class="pt-4 border-t border-gray-100 mt-2 flex justify-between items-center">
                                    <span class="text-lg font-black text-gray-900">Subtotal envío</span>
                                    <span class="text-lg font-black text-green-600 underline decoration-green-200 decoration-4 underline-offset-4">{{ number_format($total_pagar ?? 0, 2, ',', '.') }} Bs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen del LOTE --}}
                <div class="w-full">
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl shadow-lg border-2 border-emerald-200 overflow-hidden flex flex-col">
                        <div class="h-2 bg-emerald-600 w-full"></div>
                        <div class="p-6 space-y-4">
                            <div class="text-center">
                                <h3 class="text-sm font-black text-emerald-700 uppercase tracking-widest">Resumen del Lote</h3>
                                <div class="mt-3 flex items-baseline justify-center gap-2">
                                    <span class="text-5xl font-black text-emerald-900">{{ $cantidad_envios }}</span>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">envío{{ $cantidad_envios === 1 ? '' : 's' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2 pt-4 border-t border-dashed border-emerald-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-500">Subtotal</span>
                                    <span class="text-sm font-black text-gray-800">{{ number_format($subtotal_lote, 2, ',', '.') }} Bs</span>
                                </div>
                                <div class="flex justify-between items-center text-blue-600">
                                    <span class="text-sm font-bold opacity-80">I.V.A (16%)</span>
                                    <span class="text-sm font-black">{{ number_format($iva_lote, 2, ',', '.') }} Bs</span>
                                </div>
                                <div class="pt-3 border-t border-emerald-100 mt-2 flex justify-between items-center">
                                    <span class="text-base font-black text-gray-900">Total a pagar</span>
                                    <span class="text-2xl font-black text-emerald-700">{{ number_format($total_lote, 2, ',', '.') }} Bs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <x-button type="submit" class="bg-primary" wire:loading.attr="disabled" wire:target="agregarAlLote">
                    <span wire:loading.remove wire:target="agregarAlLote">+ AGREGAR AL LOTE</span>
                    <span wire:loading wire:target="agregarAlLote">Procesando...</span>
                </x-button>

                <x-button type="button" class="bg-gray-500" wire:click="limpiarFormulario">
                    LIMPIAR FORMULARIO
                </x-button>

                @if($cantidad_envios > 0)
                    <x-button type="button" class="bg-emerald-700 hover:bg-emerald-800" wire:click="abrirPago">
                        PAGAR LOTE ({{ number_format($total_lote, 2, ',', '.') }} Bs)
                    </x-button>
                @endif
            </div>
        </form>

        {{-- LISTA DE ENVÍOS EN EL LOTE --}}
        @if($cantidad_envios > 0)
            <div class="mt-8 border-t-2 border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-emerald-300 pb-2 mb-4">
                    Envíos en el lote ({{ $cantidad_envios }})
                </h2>

                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remitente</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Destinatario</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($envios_lote as $index => $envio)
                                <tr wire:key="lote-{{ $envio['uid'] ?? $index }}" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-center text-xs font-bold text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ trim(($envio['nombre_rem'] ?? '') . ' ' . ($envio['apellido_rem'] ?? '')) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ trim(($envio['nombre_dest'] ?? '') . ' ' . ($envio['apellido_dest'] ?? '')) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-bold text-emerald-700 text-right">
                                        {{ number_format($envio['total_pagar'] ?? 0, 2, ',', '.') }} Bs
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button"
                                            onclick="confirmarEliminarLote('{{ $envio['uid'] ?? '' }}')"
                                            title="Eliminar del lote"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>


    {{-- MODAL DE PAGO --}}
    @if($modal_pago)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 p-2 rounded-xl text-emerald-700">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Pago del Lote</h3>
                                <p class="text-xs text-gray-500">{{ $cantidad_envios }} envío{{ $cantidad_envios === 1 ? '' : 's' }} en el lote</p>
                            </div>
                        </div>
                        <button type="button" wire:click="cerrarPago" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            {{-- Caja de pago --}}
                            <div class="lg:col-span-7 border-r border-gray-200 lg:pr-6">
                                <livewire:caja-de-pago.caja-de-pago />
                            </div>

                            {{-- Resumen --}}
                            <div class="lg:col-span-5 flex flex-col justify-center space-y-4">
                                <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-200 space-y-3">
                                    <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Total del Lote</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Envíos</span>
                                        <span class="text-base font-bold text-gray-800">{{ $cantidad_envios }}</span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Subtotal</span>
                                        <span class="text-base font-bold text-gray-800">{{ number_format($subtotal_lote, 2, ',', '.') }} Bs</span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">IVA</span>
                                        <span class="text-base font-bold text-gray-800">{{ number_format($iva_lote, 2, ',', '.') }} Bs</span>
                                    </div>

                                    <div class="border-t border-emerald-200 pt-3 flex justify-between items-center">
                                        <span class="text-xs font-black text-emerald-700 uppercase tracking-widest">A pagar</span>
                                        <span class="text-2xl font-black text-emerald-700">{{ number_format($total_lote, 2, ',', '.') }} Bs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="finalizarLote" wire:loading.attr="disabled" wire:target="finalizarLote"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white font-black py-3 px-8 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span wire:loading.remove wire:target="finalizarLote">Procesar Lote y Cobrar</span>
                            <span wire:loading wire:target="finalizarLote">Procesando...</span>
                        </button>
                        <button type="button" wire:click="cerrarPago"
                            class="bg-white border-2 border-gray-200 text-gray-600 font-bold py-3 px-6 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
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
                timer: 3000
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
    </script>
    @endscript

    @script
    <script>
        let _eliminandoLote = false;

        window.confirmarEliminarLote = function (uid) {
            if (_eliminandoLote || !uid) return;
            _eliminandoLote = true;

            Swal.fire({
                title: '¿Eliminar envío del lote?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarDelLote(uid).finally(() => {
                        _eliminandoLote = false;
                    });
                } else {
                    _eliminandoLote = false;
                }
            });
        };
    </script>
    @endscript
@endpush