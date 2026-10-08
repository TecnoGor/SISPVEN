<div>
    @section('titulo')
        Exporta Fácil
    @endsection
    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight uppercase">Exporta Fácil</h1>
        <p class="mt-1 text-sm text-gray-600">Control de Despachos y Envíos al Exterior.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-800 shadow-lg rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <form wire:submit.prevent="submit" class="space-y-8">
                
                <section class="space-y-6">
                    <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                                Datos del Envío
                            </h2>
                        </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @if($cliente_corporativo)
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-md border border-gray-200">
                                <input type="checkbox" id="recoleccion" wire:model.live="recoleccion"
                                    class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700">
                                <label for="recoleccion" class="text-sm font-bold text-gray-700 cursor-pointer">Recolección a Domicilio</label>
                            </div>

                            <div class="lg:col-span-1">
                                <label for="cliente_sel" class="block text-xs font-black text-gray-500 uppercase mb-1">Clientes Corporativos <span class="text-red-500">*</span></label>
                                <select wire:model.live="cliente_sel" id="cliente_sel" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="">Seleccionar:</option>
                                    @foreach ($corporativos as $corp)
                                        <option value="{{$corp->cliente_corporativo_id}}">{{$corp->razon_social}}</option>
                                    @endforeach
                                </select>
                                @error('cliente_sel') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Código de Rastreo --}}
                        <div class="lg:col-start-1">
                            <label for="codigo_rastreo" class="flex items-center gap-2 text-xs font-black text-gray-500 uppercase mb-1">
                                Código de Rastreo <span class="text-red-500">*</span>
                                <button type="button" wire:click="info_rastreo" class="text-blue-500 hover:text-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </label>
                            <input type="text" wire:model="codigo_rastreo" id="codigo_rastreo" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm uppercase font-semibold">
                            @error('codigo_rastreo') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- País Destino --}}
                        <div>
                            <label for="pais_destino" class="block text-xs font-black text-gray-500 uppercase mb-1">País de Destino <span class="text-red-500">*</span></label>
                            <select wire:model.live="pais_destino" id="pais_destino" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Seleccionar:</option>
                                @foreach ($paises as $pais)
                                    <option value="{{$pais->pais_id}}">{{$pais->nombre}}</option>
                                @endforeach
                            </select>
                            @error('pais_destino') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Clase Correo --}}
                        <div>
                            <label for="clase_correo" class="block text-xs font-black text-gray-500 uppercase mb-1">Clase de Correo <span class="text-red-500">*</span></label>
                            <select wire:model.live="clase_correo" id="clase_correo" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Seleccionar:</option>
                                @foreach ($tipos_correos as $correo => $valor)
                                    <option value="{{$valor}}">{{$correo}}</option>
                                @endforeach
                            </select>
                            @error('clase_correo') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Peso --}}
                        <div>
                            <label for="peso" class="block text-xs font-black text-gray-500 uppercase mb-1">Peso Total (Kg) <span class="text-red-500">*</span></label>
                            <input type="number" id="peso" wire:model.live="peso" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm font-bold text-red-700">
                            @error('peso') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Contenido del Envío --}}
                        <div class="md:col-span-2 lg:col-span-4">
                            <label for="contenido" class="block text-xs font-black text-gray-500 uppercase mb-1">Contenido del Envío <span class="text-red-500">*</span></label>
                            <textarea wire:model="contenido" id="contenido" maxlength="300" rows="2" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm resize-none" placeholder="Describa el contenido..."></textarea>
                            @error('contenido') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                <div>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                            Datos de Origen / Destino
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="flex flex-col gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <h3 class="text-lg font-bold text-red-800 dark:text-red-400 border-b border-red-200 pb-1">
                                Remitente:
                            </h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo Documento<span class="text-red-500">*</span></label>
                                    <select id="tipo_documento" wire:model.live="tipo_documento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="documento" class="block text-sm font-medium text-gray-700">Documento<span class="text-red-500">*</span></label>
                                    <input type="number" id="documento" wire:model.live="documento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('documento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre" wire:model.live="nombre" maxlength="40" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if(!$cliente_corporativo)
                                <div>
                                    <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                                    <input type="text" id="apellido" wire:model.live="apellido" maxlength="40" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @endif

                                @if($cliente_corporativo)
                                <div>
                                    <label for="agente" class="block text-sm font-medium text-gray-700">Agente Autorizado<span class="text-red-500">*</span></label>
                                    <input type="text" id="agente" wire:model.live="agente" maxlength="40" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('agente') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @endif
                            </div>

                            @if($recoleccion)
                            <div class="space-y-4 border-t border-gray-200 pt-4 mt-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="estado_id" class="block text-sm font-medium text-gray-700">Estado<span class="text-red-500">*</span></label>
                                        <select id="estado_id" wire:model.live="estado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="municipio_id" class="block text-sm font-medium text-gray-700">Municipio<span class="text-red-500">*</span></label>
                                        <select id="municipio_id" wire:model.live="municipio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione</option>
                                            @foreach ($municipios as $municipio)
                                                <option value="{{$municipio->municipio_id}}">{{$municipio->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="ciudad" class="block text-sm font-medium text-gray-700">Ciudad<span class="text-red-500">*</span></label>
                                        <select id="ciudad" wire:model.live="ciudad" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione</option>
                                            @foreach ($ciudades as $ciudad)
                                                <option value="{{$ciudad->ciudad_id}}">{{$ciudad->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="parroquia" class="block text-sm font-medium text-gray-700">Parroquia<span class="text-red-500">*</span></label>
                                        <select id="parroquia" wire:model.live="parroquia" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione</option>
                                            @foreach ($parroquias as $parroquia)
                                                <option value="{{$parroquia->parroquia_id}}">{{$parroquia->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="w-1/2">
                                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700">Código Postal<span class="text-red-500">*</span></label>
                                    <select id="codigo_postal" wire:model.live="codigo_postal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione</option>
                                        @foreach ($codigos_postales as $postal)
                                            <option value="{{$postal}}">{{$postal}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="direccion_rem" class="block text-sm font-medium text-gray-700">Dirección de Habitación<span class="text-red-500">*</span></label>
                                    <textarea wire:model="direccion" id="direccion_rem" rows="2" style="resize: none;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                    @error('direccion_hab_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @endif

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

                        <div class="flex flex-col gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <h3 class="text-lg font-bold text-red-800 dark:text-red-400 border-b border-red-200 pb-1">
                                Destinatario:
                            </h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre_dest" class="block text-sm font-medium text-gray-700">Nombre Completo<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre_dest" wire:model="nombre_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="estado_dest" class="block text-sm font-medium text-gray-700">Estado/Provincia<span class="text-red-500">*</span></label>
                                    <input type="text" id="estado_dest" wire:model="estado_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('estado_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ciudad_dest" class="block text-sm font-medium text-gray-700">Ciudad<span class="text-red-500">*</span></label>
                                    <input type="text" id="ciudad_dest" wire:model="ciudad_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('ciudad_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="parroquia_dest" class="block text-sm font-medium text-gray-700">Parroquia<span class="text-red-500">*</span></label>
                                    <input type="text" id="parroquia_dest" wire:model="parroquia_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('parroquia_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="w-1/2">
                                <label for="codigo_postal_dest" class="block text-sm font-medium text-gray-700">Código Postal<span class="text-red-500">*</span></label>
                                <input type="text" id="codigo_postal_dest" wire:model="codigo_postal_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                @error('codigo_postal_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="direccion_dest" class="block text-sm font-medium text-gray-700">Dirección de Habitación<span class="text-red-500">*</span></label>
                                <textarea wire:model="direccion_dest" id="direccion_dest" rows="2" style="resize: none;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                @error('direccion_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-auto">
                                <div>
                                    <label for="tlf_dest" class="block text-sm font-medium text-gray-700">Nro. Teléfono<span class="text-red-500">*</span></label>
                                    <input type="tel" id="tlf_dest" wire:model.live="telefono_dest" maxlength="11" class="mt-1 w-full block border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('telefono_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="correo_dest" class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                                    <input type="email" id="correo_dest" wire:model="correo_dest" class="mt-1 w-full block border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm" />
                                    @error('correo_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                <div class="grid grid-cols-1 sm:grid-cols-2 space-x-3 gap-3">
                    <livewire:caja-de-pago.caja-de-pago/>
                    {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!-- Metodos de Pago -->
                        <div class="mb-2">
                            <label for="metodos_pago" class="block text-sm text-center font-medium text-gray-700">Métodos de Pago</label>
                            @error('metodo_pago_seleccionado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <select id="metodos_pago" wire:model="metodo_pago_seleccionado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="">Seleccione un método:</option>
                                @foreach ($metodos_pago as $tipo_pago)
                                <option value="{{$tipo_pago->tipo_pago_id}}">{{$tipo_pago->nombre}}</option>
                                @endforeach
                            </select>
                            @error('metodos_pago') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                            <div class="mb-2">
                                <input type="text" wire:model="monto" placeholder="monto bs..."
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                oninput="formatCurrency(this)">
                                @error('monto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="">
                                <x-button type="button" wire:click="agregar_pago" class="bg-primary">Guardar</x-button>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="" class="block text-sm text-center">Pagos Realizados:</label>
                            <div class="flex justify-center">
                                <input type="text"
                                    disabled
                                    value="{{ $monto_pagado }} Bs"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm
                                    focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            </div>

                            <div class="mt-2">
                                <ul class="list-none p-0">
                                    @foreach ($pagos as $pago)
                                        <li class="flex items-center justify-between p-2 mb-2 border-b border-gray-300 rounded-md bg-gray-50">
                                            <span class="text-sm font-medium text-gray-700">{{ $pago['nombre'] }} - {{ $pago['monto'] }} bs</span>
                                            
                                            <!-- Botón de basurero rojo para eliminar el pago -->
                                            <button 
                                                type="button" wire:click="eliminar_pago({{ $pago['tipo_pago_id'] }})" 
                                                class="text-red-500 hover:text-red-700 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>  
                            </div>
                        </div>
                    </div> --}}

                    {{-- TICKET SUMMARY --}}
                    <div class="w-full self-start">
                        <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden flex flex-col relative">
                            <div class="h-2 bg-primary w-full"></div>
                            <div class="p-6 space-y-6">
                                <div class="text-center">
                                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Resumen de Venta</h3>
                                    <div class="mt-4 flex flex-col items-center">
                                        <span class="text-4xl font-black text-gray-900">{{ number_format((float)($total_pagar ?: 0), 2, ',', '.') }}</span>
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Bolívares</span>
                                    </div>
                                </div>

                                <div class="space-y-3 pt-6 border-t border-dashed border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-bold text-gray-500">Precio</span>
                                        <span class="text-sm font-black text-gray-800">{{ number_format((float)($precio_total ?: 0), 2, ',', '.') }} Bs</span>
                                    </div>
                                    @if($recoleccion)
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-bold text-gray-500">Recolección</span>
                                            <span class="text-sm font-black text-gray-800">{{ number_format((float)($total_recolec ?: 0), 2, ',', '.') }} Bs</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center text-blue-600">
                                        <span class="text-sm font-bold opacity-80">I.V.A (16%)</span>
                                        <span class="text-sm font-black">{{ number_format((float)($iva ?: 0), 2, ',', '.') }} Bs</span>
                                    </div>
                                    <div class="pt-4 border-t border-gray-100 mt-2 flex justify-between items-center">
                                        <span class="text-lg font-black text-gray-900">Total a pagar</span>
                                        <span class="text-lg font-black text-green-600 underline decoration-green-200 decoration-4 underline-offset-4">{{ number_format((float)($total_pagar ?: 0), 2, ',', '.') }} Bs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between px-2 pb-1">
                                @for($i=0; $i<15; $i++)
                                    <div class="w-3 h-3 rounded-full bg-gray-100 -mb-2"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-0 h-0.5 bg-red-600 shadow-md" />


                <div class="md:flex md:gap-3 justify-center ">
                    <div class="mb-2">
                        <x-button type="button" class="bg primary"
                                wire:click="habilitar">
                            CREAR
                        </x-button>
                    </div>
                </div>
        </form>
        </div>
    </div>
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
                timer: 1000
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
                timer: 10000
            });
        })

        Livewire.on('envio_registrado', () => {
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