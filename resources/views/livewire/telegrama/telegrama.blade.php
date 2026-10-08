<div>
    @section('titulo')
        Telegramas
    @endsection
    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight">Creación de Telegrama</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de envíos ordinarios y urgentes.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    {{-- Contenedor Principal --}}
    <div class="max-w-[95%] mx-auto bg-white rounded-lg shadow-lg mt-6 mb-6 border border-gray-200 px-4 sm:px-6 lg:px-8 py-6">
        
        <form wire:submit.prevent="submit" class="space-y-8">
            
            {{-- SECCIÓN: DATOS DEL TELEGRAMA --}}
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2">Datos del Telegrama</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Seleccione Prioridad</label>
                        <div class="space-y-3 flex-grow">
                            <label class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:border-red-400 hover:bg-red-50 transition-all duration-200 cursor-pointer h-[52px]">
                                <input type="radio" wire:model.live="tipo_telegrama" value="4" class="h-5 w-5 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-3 text-gray-700 font-bold text-sm">Telegrama Ordinario</span>
                            </label>
                            <label class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:border-red-400 hover:bg-red-50 transition-all duration-200 cursor-pointer h-[52px]">
                                <input type="radio" wire:model.live="tipo_telegrama" value="5" class="h-5 w-5 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-3 text-gray-700 font-bold text-sm">Telegrama Urgente</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm flex flex-col">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Tarifas Disponibles</label>
                        <div class="space-y-3 flex-grow">
                            @foreach ($tarifas as $index => $tarifa)
                                <label wire:key="tarifa-item-{{ $tarifa->tarifa_conceptos_id }}" 
                                    class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:border-red-400 hover:bg-red-50 transition-all duration-200 cursor-pointer h-[52px] {{ $tipo_telegrama == 5 && $tarifa->tarifa_conceptos_id == 7 ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}">
                                    
                                    <input type="checkbox"
                                        value="{{ $tarifa->tarifa_conceptos_id }}"
                                        wire:model.live="tarifas_selec"
                                        wire:change="actualizarTarifa({{ $tarifa->tarifa_conceptos_id }})"
                                        @if($tipo_telegrama == 5 && $tarifa->tarifa_conceptos_id == 7) disabled @endif
                                        class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700 transition duration-150">
                                    
                                    <span class="ml-3 text-sm font-bold text-gray-700 leading-tight">{{ $tarifa->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tarifa_encon') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

            {{-- SECCIÓN: DATOS DEL REMITENTE --}}
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2">Datos del Remitente</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo Documento<span class="text-red-500">*</span></label>
                        <select wire:model.lazy="tipo_documento_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2 transition">
                            <option value="">Seleccionar</option>
                            @foreach ($tipos_documentos as $doc)
                                <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nro Documento<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="documento_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('documento_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo Remitente</label>
                        <select wire:model.lazy="tipo_remitente" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="" selected hidden>Seleccionar</option> 
                            @foreach ($tiposRemitente as $remitente)
                                <option value="{{ $remitente->tipos_remitente_telegramas_id }}">{{ $remitente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_remitente') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lugar Emisión</label>
                        <select wire:model.lazy="emision" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="" selected hidden>Seleccionar</option>
                            @foreach ($lugaresEmision as $lugar)
                                <option value="{{ $lugar->lugar_emision_telegramas_id }}">{{ $lugar->nombre }}</option>
                            @endforeach
                        </select>
                        @error('emision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">C. Judicial / Tribunal</label>
                        <select wire:model.lazy="lugar_emision" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="" selected hidden>Seleccionar</option>
                            @foreach ($centrosJudiciales as $centro)
                                <option value="{{ $centro->circuito_judicial_tribunal_telegramas_id }}">{{ $centro->nombre }}</option>
                            @endforeach
                        </select>
                        @error('lugar_emision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="nombre_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('nombre_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="apellido_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('apellido_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono<span class="text-red-500">*</span></label>
                        <input type="text" wire:model="telefono_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('telefono_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                        <input type="email" wire:model.lazy="correo_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('correo_rem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

            {{-- SECCIÓN: DATOS DEL DESTINATARIO --}}
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2">Datos del Destinatario</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo Documento<span class="text-red-500">*</span></label>
                        <select wire:model.lazy="tipo_documento_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="">Seleccionar</option>
                            @foreach ($tipos_documentos as $doc)
                                <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nro Documento<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="documento_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('documento_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="nombre_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('nombre_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="apellido_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('apellido_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado<span class="text-red-500">*</span></label>
                        <select wire:model.live="estado_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="">Seleccionar</option>
                            @foreach ($estados_dest as $est)
                                <option value="{{$est->estado_id}}">{{$est->nombre}}</option>
                            @endforeach
                        </select>
                        @error('estado_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Oficina<span class="text-red-500">*</span></label>
                        <select wire:model.live="oficina_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="">Seleccionar</option>
                            @foreach ($oficinas as $oficina)
                                <option value="{{$oficina->oficina_id}}">{{$oficina->nombre}}</option>
                            @endforeach
                        </select>
                        @error('oficina_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono<span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="telefono_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('telefono_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                        <input type="email" wire:model.lazy="correo_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                        @error('correo_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Circuito Judicial</label>
                        <select wire:model.lazy="circuito_judicial_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2">
                            <option value="">Seleccionar</option>
                            @foreach ($cj_dest as $cj)
                                <option value="{{ $cj->circuito_judicial_tribunal_telegramas_id }}">{{ $cj->nombre }}</option>
                            @endforeach
                        </select>
                        @error('circuito_judicial_dest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    {{-- Parámetros --}}
                    @foreach ($parametro as $key => $direccion_dest_val)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{$key}}<span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live.debounce.500ms="parametro.{{$key}}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2" />
                            @error("parametro.$key") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>

                <div class="col-span-full">
                    <label class="block text-sm font-medium text-gray-700">Dirección del Destinatario<span class="text-red-500">*</span></label>
                    <textarea wire:model.live="direccion_dest" style="resize: none;" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm h-20 p-2"></textarea>
                    @error('direccion_dest') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

            {{-- SECCIÓN: REDACCIÓN --}}
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-800 border-b-2 border-gray-200 pb-2">Telegrama</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Redacción del Telegrama<span class="text-red-500">*</span></label>
                    <textarea wire:model.live.debounce.300ms="redaccion_telegrama" style="resize: none;" 
                        class="h-40 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm p-3 font-mono"></textarea>
                    @error('redaccion_telegrama') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                {{-- <div class="flex flex-wrap gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-700 uppercase text-xs">Palabras tasables:</span>
                        <span class="bg-red-700 text-white px-3 py-1 rounded-full text-sm font-black">{{$cantidad_palabras}}</span>
                        @error('cantidad_palabras') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-700 uppercase text-xs">Palabras reales:</span>
                        <span class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm font-black">{{$cantidad_palabras_reales}}</span>
                    </div>
                </div>--}}
            </div>

            <hr class="border-0 h-0.5 bg-red-600 shadow-md" />
            <div class="md:flex md:gap-3 justify-center ">
                {{-- <div class="mb-2">
                    <x-button type="button" class="bg primary"
                            wire:click="Habilitar">
                        CREAR
                    </x-button>
                </div> --}}

                <div class="mb-2">
                    <x-button type="button" class="bg primary"
                            wire:click="calcular_palabras_totales">
                        Calcular Pago
                    </x-button>
                </div>
            </div>
            <div class="text-center">
                <span class="text-red-500">{{ $mensaje_pago }}</span>
            </div>

    </div>
        </form>

        @if($mostrar_caja)
            <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl relative overflow-hidden">
                    
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-700">Calcular Pago</h3>
                        <button wire:click="$set('mostrar_caja', false)" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                            
                            <div class="md:col-span-6 space-y-4 border-r border-gray-100 pr-8">
                                <livewire:caja-de-pago.caja-de-pago/>
                                
                                {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
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
                                    </div> --}}
                                {{-- </div> --}}
                            </div>

                            <div class="md:col-span-3">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Servicios Detallados</h4>
                                <div class="max-h-[350px] overflow-y-auto pr-2 space-y-3">
                                    @if(empty($tarifa_selec))
                                        <div class="text-center py-8 border-2 border-dashed border-gray-100 rounded-xl text-gray-400 text-sm">
                                            No hay servicios
                                        </div>
                                    @else
                                        @foreach ($tarifa_selec as $tarifas_total)
                                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm hover:bg-white transition-colors">
                                                <div class="flex flex-col gap-2">
                                                    <span class="text-xs font-bold text-blue-600 uppercase tracking-tight">Servicio</span>
                                                    <div class="flex justify-between items-end">
                                                        <p class="text-sm font-semibold text-gray-700 pr-2 leading-snug">
                                                            {{ $tarifas_total['nombre'] }}
                                                        </p>
                                                        <span class="text-sm font-black text-gray-900 whitespace-nowrap">
                                                            {{$tarifas_total['monto']}} <small class="text-[10px]">Bs</small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-5">
                                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 text-center">Resumen de Cobro</h4>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-500">Coste:</span>
                                            <span class="font-bold text-gray-700">{{$precio_total}} Bs</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-500">I.V.A:</span>
                                            <span class="font-bold text-red-600">{{$iva}} Bs</span>
                                        </div>
                                        
                                        <div class="pt-4 border-t border-gray-100 flex flex-col items-center">
                                            <span class="text-[11px] font-bold text-gray-400 uppercase mb-1">Total Coste</span>
                                            <div class="flex items-baseline gap-1 text-green-600">
                                                <span class="text-4xl font-black tracking-tighter">{{$total_pagar}}</span>
                                                <span class="text-sm font-bold uppercase text-green-700">Bs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN DE PALABRAS INSERTADA CON TÍTULO Y SEPARACIÓN --}}
                        <div class="mt-8">
                            <div class="flex items-center gap-4 mb-3">
                                <span class="text-xs font-bold uppercase tracking-widest text-gray-400 whitespace-nowrap">Métricas de Redacción</span>
                                <div class="h-px bg-gray-200 w-full"></div>
                            </div>
                            <div class="flex flex-wrap gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-700 uppercase text-xs">Palabras tasables:</span>
                                    <span class="bg-red-700 text-white px-3 py-1 rounded-full text-sm font-black">{{$cantidad_palabras}}</span>
                                    @error('cantidad_palabras') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-700 uppercase text-xs">Palabras reales:</span>
                                    <span class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm font-black">{{$cantidad_palabras_reales}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-5 border-t border-gray-100 flex justify-end">
                            <x-button type="button" wire:click="Habilitar" wire:loading.attr='disabled' wire:target='Habilitar, submit' class="bg primary">
                               Generar
                            </x-button>
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

        Livewire.on('servicioAceptado', () => {
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
