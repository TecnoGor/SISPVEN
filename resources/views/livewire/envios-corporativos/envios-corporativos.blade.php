<div>
    @section('titulo') Envíos Corporativos @endsection

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Envíos Corporativos</h1>
        <p class="mt-1 text-sm text-gray-600">Registro de envíos bajo contrato corporativo.</p>
    </div>

    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- ── SECCIÓN 1: DATOS DEL ENVÍO ── --}}
            <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Datos del Envío</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Peso --}}
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                            Peso Total (gr) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" wire:model.live="peso" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm font-bold text-primary py-2 px-3 bg-gray-50">
                        @error('peso') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Contenido --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                            Contenido del Envío <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="contenido" maxlength="300" rows="2"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm resize-none py-2 px-3 bg-gray-50"></textarea>
                        @error('contenido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Insumos --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Insumos</label>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 max-h-40 overflow-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @forelse ($inventarios as $inventario)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox"
                                        value="{{ $inventario->insumo_id }}"
                                        wire:model.live="insumo_sel"
                                        class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary"
                                        @if ($inventario->cantidad == 0) disabled @endif>
                                    <span class="{{ $inventario->cantidad == 0 ? 'text-gray-400' : 'text-gray-700' }}">
                                        {{ $inventario->insumo->descripcion }}
                                        @if ($inventario->cantidad == 0)
                                            <span class="text-red-400 text-xs">(0)</span>
                                        @else
                                            <span class="text-green-600 text-xs font-semibold">({{ $inventario->cantidad }})</span>
                                        @endif
                                    </span>
                                </label>
                            @empty
                                <p class="text-xs text-gray-400 col-span-full">Sin insumos disponibles.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── SECCIÓN 2: REMITENTE + DESTINATARIO ── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ── REMITENTE ── --}}
                <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Remitente</h2>
                    </div>
                    <div class="p-6 space-y-4">

                        {{-- Cliente --}}
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                Cliente Corporativo <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="cliente"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clientes as $cli)
                                    <option value="{{ $cli->cliente_corporativo_id }}">{{ $cli->razon_social }}</option>
                                @endforeach
                            </select>
                            @error('cliente') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Razón social + Representante --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Razón Social</label>
                                <input type="text" wire:model.lazy="nombre" readonly
                                    class="block w-full border border-gray-200 rounded-lg text-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Representante Legal</label>
                                <input type="text" wire:model.live="representante" readonly
                                    class="block w-full border border-gray-200 rounded-lg text-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                            </div>
                        </div>

                        {{-- Teléfono + Correo --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Teléfono</label>
                                <input type="tel" wire:model.live="telefono" readonly
                                    class="block w-full border border-gray-200 rounded-lg text-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                                @error('telefono') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Correo</label>
                                <input type="email" wire:model="correo" readonly
                                    class="block w-full border border-gray-200 rounded-lg text-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                                @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Documento autorizado + Nombre --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Doc. Autorizado <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.live="documento_autorizado"
                                    @if(!$cliente) disabled @endif
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                @error('documento_autorizado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Autorizado <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live="autorizado" readonly
                                    class="block w-full border border-gray-200 rounded-lg text-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                                @error('autorizado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Estado del contrato --}}
                        <div class="rounded-xl border p-4 {{ $contrato ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            @if(!$contrato)
                                <p class="text-xs font-black uppercase tracking-widest text-red-600 mb-1">Sin contrato activo</p>
                                <p class="text-xs text-red-500">Seleccione un cliente con contrato vigente para continuar.</p>
                            @else
                                <p class="text-xs font-black uppercase tracking-widest text-green-700 mb-2">Contrato validado</p>
                                <div class="grid grid-cols-3 gap-3 text-center">
                                    <div class="bg-white rounded-lg py-2 px-1 border border-green-100">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Tipo</p>
                                        <p class="text-xs font-black text-green-700 mt-0.5">{{ $contrato->tipo_contrato->descripcion }}</p>
                                    </div>
                                    <div class="bg-white rounded-lg py-2 px-1 border border-green-100">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Peso disp.</p>
                                        <p class="text-xs font-black text-green-700 mt-0.5">{{ $contrato->peso_contrato - $contrato->peso_utilizado }} gr</p>
                                    </div>
                                    <div class="bg-white rounded-lg py-2 px-1 border border-green-100">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Envíos disp.</p>
                                        <p class="text-xs font-black text-green-700 mt-0.5">{{ $contrato->cant_envios - $contrato->cant_envios_utilizados }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- ── DESTINATARIO ── --}}
                <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Destinatario</h2>

                        {{-- Toggle modo --}}
                        @if($cliente && count($direcciones_cliente) > 0)
                            <div class="ml-auto flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                <button type="button" wire:click="cambiarModoDestino('manual')"
                                    class="px-3 py-1 text-xs font-black uppercase rounded-md transition
                                        {{ $modo_destino === 'manual' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    Manual
                                </button>
                                <button type="button" wire:click="cambiarModoDestino('direccion')"
                                    class="px-3 py-1 text-xs font-black uppercase rounded-md transition
                                        {{ $modo_destino === 'direccion' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    Dirección guardada
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 space-y-4">

                        {{-- Selector de dirección guardada --}}
                        @if($modo_destino === 'direccion' && $cliente)
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Dirección del cliente <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="direccion_sel"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                    <option value="">Seleccione una dirección</option>
                                    @foreach($direcciones_cliente as $dir)
                                        <option value="{{ $dir['cliente_corporativo_direccion_id'] }}">
                                            {{ $dir['alias'] }}
                                            @if($dir['persona']) — {{ $dir['persona'] }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($direccion_sel)
                                @php
                                    $dirSel = collect($direcciones_cliente)->firstWhere('cliente_corporativo_direccion_id', (int)$direccion_sel);
                                @endphp
                                @if($dirSel)
                                    <div class="rounded-lg border border-primary/20 bg-primary/5 p-3 text-xs text-gray-600 space-y-1">
                                        <p><span class="font-semibold text-gray-700">Estado:</span> {{ $dirSel['estado']['nombre'] ?? '—' }}</p>
                                        <p><span class="font-semibold text-gray-700">Municipio:</span> {{ $dirSel['municipio']['nombre'] ?? '—' }} · {{ $dirSel['parroquia']['nombre'] ?? '—' }}</p>
                                        <p><span class="font-semibold text-gray-700">Dirección:</span> {{ $dirSel['direccion'] }}</p>
                                        <p><span class="font-semibold text-gray-700">CP:</span> {{ $dirSel['codigo_postal'] }}</p>
                                    </div>
                                @endif
                            @endif
                        @endif

                        {{-- Identidad del destinatario (siempre manual) --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Tipo Documento <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="tipo_documento_dest"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                    <option value="">Seleccione</option>
                                    @foreach ($documentos as $doc)
                                        <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Documento <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.live="documento_dest"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                @error('documento_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live="nombre_dest" maxlength="45"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                @error('nombre_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live="apellido_dest" maxlength="20"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                @error('apellido_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Ubicación: manual o bloqueada si viene de dirección --}}
                        <div class="space-y-4 {{ $modo_destino === 'direccion' && $direccion_sel ? 'opacity-60 pointer-events-none' : '' }}">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="estado_dest"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                        <option value="">Seleccione</option>
                                        @foreach ($estados as $est)
                                            <option value="{{ $est->estado_id }}">{{ $est->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                        Municipio <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="municipio_dest" wire:key="mun-dest-{{ $direccion_sel ?: 'manual' }}"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                        <option value="">Seleccione</option>
                                        @foreach ($municipios_dest as $mun)
                                            <option value="{{ $mun->municipio_id }}">{{ $mun->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipio_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                        Ciudad <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="ciudad_dest" wire:key="ciu-dest-{{ $direccion_sel ?: 'manual' }}"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                        <option value="">Seleccione</option>
                                        @foreach ($ciudades_dest as $ciu)
                                            <option value="{{ $ciu->ciudad_id }}">{{ $ciu->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudad_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                        Parroquia <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="parroquia_dest" wire:key="par-dest-{{ $direccion_sel ?: 'manual' }}"
                                        class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                        <option value="">Seleccione</option>
                                        @foreach ($parroquias_dest as $par)
                                            <option value="{{ $par->parroquia_id }}">{{ $par->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquia_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="w-1/2">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Código Postal <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="codigo_postal_dest" wire:key="cp-dest-{{ $direccion_sel ?: 'manual' }}"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                    <option value="">Seleccione</option>
                                    @foreach ($codigos_postales_dest as $postal)
                                        <option value="{{ $postal }}">{{ $postal }}</option>
                                    @endforeach
                                </select>
                                @error('codigo_postal_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Puntos de referencia --}}
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Puntos de Referencia</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($parametro as $key => $val)
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ $key }}</label>
                                            <input type="text" wire:model.live.debounce.500ms="parametro.{{ $key }}"
                                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Dirección de habitación --}}
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Dirección de Habitación <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="direccion_dest" maxlength="300" rows="2"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm resize-none py-2 px-3 bg-gray-50"></textarea>
                                @error('direccion_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        {{-- Teléfono + Correo destinatario --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" wire:model.live="telefono_dest" maxlength="11"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                @error('telefono_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">
                                    Correo <span class="text-red-500">*</span>
                                </label>
                                <input type="email" wire:model="correo_dest"
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm py-2 px-3 bg-gray-50">
                                @error('correo_dest') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── BOTÓN SUBMIT ── --}}
            <div class="flex justify-center pb-6">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-red-800 text-white text-sm font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Crear Envío
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({ position: "center", icon: "success", title: message.message, showConfirmButton: false, timer: 1000 });
        });
        Livewire.on('alertSuccess2', message => {
            Swal.fire({ position: "center", icon: "error", title: message.message, showConfirmButton: false, timer: 3000 });
        });
        Livewire.on('alertSuccess3', message => {
            Swal.fire({ position: "center", icon: "info", title: message.message, showConfirmButton: false, timer: 10000 });
        });
        Livewire.on('envio_registrado', () => {
            setTimeout(() => { location.reload(); }, 1000);
        });
    </script>
    @endscript
@endpush