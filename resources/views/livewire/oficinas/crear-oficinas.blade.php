<div class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm overflow-y-auto w-full h-full flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        
        {{-- HEADER DEL MODAL --}}
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 text-red-700 p-2.5 rounded-xl shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-gray-800 tracking-tight leading-tight">Registro de Oficina</h2>
                    <p class="text-xs text-gray-500 font-medium">Complete los campos requeridos para habilitar una nueva sede física u operativa.</p>
                </div>
            </div>
            <button wire:click="$dispatch('cerrar-modal-crear')" type="button" class="text-gray-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- FORMULARIO CON SCROLL INTERNO --}}
        <form wire:submit="save" class="flex flex-col flex-1 overflow-hidden">
            <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full p-6 md:p-8 space-y-10">

                {{-- SECCION 1: DATOS GEOGRAFICOS --}}
                <section>
                    <div class="flex items-center gap-2 mb-5 border-b border-gray-100 pb-2">
                        <div class="w-2 h-6 bg-red-700 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-widest text-sm">1. Ubicación Geográfica</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Estado <span class="text-red-500">*</span></label>
                            <select wire:model.live="estadoss" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm">
                                <option value="">Seleccione...</option>
                                @foreach ($estados as $estado) <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option> @endforeach
                            </select>
                            @error('estadoss') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- Municipio -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Municipio <span class="text-red-500">*</span></label>
                            <select wire:model.live="municipioss" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm">
                                <option value="">Seleccione...</option>
                                @foreach ($municipios as $municipio) <option value="{{$municipio->municipio_id}}">{{$municipio->nombre}}</option> @endforeach
                            </select>
                            @error('municipioss') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- Parroquia -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Parroquia <span class="text-red-500">*</span></label>
                            <select wire:model.live="parroquiass" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm">
                                <option value="">Seleccione...</option>
                                @foreach ($parroquias as $parroquia) <option value="{{$parroquia->parroquia_id}}">{{$parroquia->nombre}}</option> @endforeach
                            </select>
                            @error('parroquiass') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- CP Ubicacion -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Código Postal Base <span class="text-red-500">*</span></label>
                            <select wire:model="cubicacion" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach ($codigos_postales as $codigo_postal) <option value="{{$codigo_postal}}">{{$codigo_postal}}</option> @endforeach
                            </select>
                            @error('cubicacion') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{$message}}</span> @enderror
                        </div>
                        
                        <!-- Direccion -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Dirección Físca Exacta</label>
                            <textarea wire:model="direccion" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm resize-none" placeholder="Av / Calle / Av / Edificio ..."></textarea>
                            @error('direccion') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- Lat / Lng -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Latitud</label>
                            <input type="text" wire:model.live="latitud" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm font-mono" placeholder="Ej: 10.4806" />
                            @error('latitud') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Longitud</label>
                            <input type="text" wire:model.live="longitud" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 sm:text-sm font-mono" placeholder="Ej: -66.9036" />
                            @error('longitud') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>


                {{-- SECCION 2: INFORMACION GENERAL --}}
                <section>
                    <div class="flex items-center gap-2 mb-5 border-b border-gray-100 pb-2">
                        <div class="w-2 h-6 bg-blue-700 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-widest text-sm">2. Información General</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
                        <!-- Nombre -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre Institucional de la Sede <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.lazy="nombre" maxlength="50" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 sm:text-sm" placeholder="Ej: OPT PRINCIPAL CARACAS" />
                            @error('nombre') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Clasificación / Tipo <span class="text-red-500">*</span></label>
                            <select wire:model.live="tipo_oficina" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 sm:text-sm">
                                <option value="">Seleccione...</option>
                                @foreach ($tipos_oficinas as $toficina) <option value="{{$toficina->tipo_oficina_id}}">{{$toficina->nombre}}</option> @endforeach
                            </select>
                            @error('tipo_oficina') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- Estatus -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Estatus de Oficina <span class="text-red-500">*</span></label>
                            <select wire:model="estatus_seleccionado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 sm:text-sm">
                                <option value="">Seleccione...</option>
                                @foreach ($estatus as $estat) <option value="{{$estat->estatus_oficina_id}}">{{$estat->estatus}}</option> @endforeach
                            </select>
                            @error('estatus_seleccionado') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Correo -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Correo Electrónico Institucional</label>
                            <input type="email" wire:model="correo" maxlength="40" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 sm:text-sm" placeholder="oficina@ipostel.gob.ve" />
                            @error('correo') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- TLF -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Teléfono Base</label>
                            <input type="text" wire:model="tlf" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 sm:text-sm font-mono" placeholder="Ej: 04141234567" />
                            @error('tlf') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        <!-- ZEE -->
                        <div class="flex flex-col justify-center pt-5">
                            <label class="inline-flex items-center cursor-pointer group">
                                <input type="checkbox" wire:model="zona_economica_especial" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 group-hover:bg-gray-300 transition-colors"></div>
                                <span class="ms-3 text-sm font-bold text-gray-700 select-none">Zona Econ. Especial</span>
                            </label>
                            @error('zona_economica_especial') <span class="text-red-500 text-xs font-bold mt-1.5 block text-center">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>


                {{-- SECCION 3: CONDICIONES ADMINISTRATIVAS --}}
                @if($tipo_oficina != 7)
                <section>
                    <div class="flex items-center gap-2 mb-5 border-b border-gray-100 pb-2">
                        <div class="w-2 h-6 bg-amber-500 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-widest text-sm">3. Estatus Administrativo de Semáforo Postal</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 items-start">
                        <!-- Condicion -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Condición de Tenencia <span class="text-red-500">*</span></label>
                            <select wire:model.live="condicion_sel" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach ($condiciones as $condicion) <option value="{{$condicion}}">{{$condicion}}</option> @endforeach
                            </select>
                            @error('condicion_sel') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Fechas (Solo Arrendada/Comodato) -->
                        @if($condicion_sel === 'Arrendada' || $condicion_sel === 'En Comodato')
                            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-5 p-4 bg-amber-50 border border-amber-100 rounded-xl relative">
                                <span class="absolute -top-3 left-4 bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">Vigencia de Contrato</span>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Fecha de Inicio <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model="fecha_inicio" class="w-full border-amber-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" />
                                    @error('fecha_inicio') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Fecha de Finalización <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model="fecha_fin" class="w-full border-amber-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" />
                                    @error('fecha_fin') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
                @endif


                {{-- SECCION 4: CAPACIDADES OPERATIVAS Y ROLES --}}
                <section>
                    <div class="flex items-center gap-2 mb-5 border-b border-gray-100 pb-2">
                        <div class="w-2 h-6 bg-emerald-600 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-widest text-sm">4. Capacidades Operativas y Personal</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                        <!-- SERVICIOS OPERATIVOS -->
                        @if($tipo_oficina == 1 || $tipo_oficina == 2 || $tipo_oficina == 3)
                        <div class="bg-gray-50 flex flex-col rounded-xl border border-gray-200 overflow-hidden">
                            <h4 class="bg-gray-100 text-gray-800 text-sm font-bold px-4 py-3 border-b border-gray-200">Servicios Operativos <span class="text-red-500">*</span></h4>
                            <div class="p-3 overflow-y-auto max-h-48 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full space-y-1">
                                @foreach ($servicios_operativos as $servicio)
                                    <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer border border-transparent hover:border-gray-200 hover:shadow-sm transition-all">
                                        <input type="checkbox" wire:model.live="servicios" value="{{ $servicio->servicio_operativo_id }}" class="form-checkbox h-4 w-4 text-emerald-600 rounded focus:ring-emerald-500 border-gray-300">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">{{ $servicio->servicio_operativo }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('servicios') <div class="bg-red-50 px-4 py-2 border-t border-red-100"><span class="text-red-500 text-xs font-bold">{{ $message }}</span></div> @enderror
                        </div>

                        <!-- MÉTODOS DE PAGO -->
                        <div class="bg-gray-50 flex flex-col rounded-xl border border-gray-200 overflow-hidden">
                            <h4 class="bg-gray-100 text-gray-800 text-sm font-bold px-4 py-3 border-b border-gray-200">Métodos de Pago <span class="text-red-500">*</span></h4>
                            <div class="p-3 overflow-y-auto max-h-48 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full space-y-1">
                                @foreach ($tipos_pagos as $tipo_pago)
                                    <label class="flex items-center p-2.5 hover:bg-white rounded-lg cursor-pointer border border-transparent hover:border-gray-200 hover:shadow-sm transition-all">
                                        <input type="checkbox" wire:model="tipo_pago" value="{{ $tipo_pago->tipo_pago_id }}" class="form-checkbox h-4 w-4 text-emerald-600 rounded focus:ring-emerald-500 border-gray-300">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">{{ $tipo_pago->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tipo_pago') <div class="bg-red-50 px-4 py-2 border-t border-red-100"><span class="text-red-500 text-xs font-bold">{{ $message }}</span></div> @enderror
                        </div>
                        @endif

                        <!-- CODIGOS POSTALES DE INFLUENCIA -->
                        @if (in_array($serviciorecibir, $servicios) && in_array($tipo_oficina, [1,2,3]))
                        <div class="bg-gray-50 flex flex-col rounded-xl border border-gray-200 overflow-hidden xl:col-span-2">
                            <h4 class="bg-gray-100 text-gray-800 text-sm font-bold px-4 py-3 border-b border-gray-200">Códigos Postales para Distribución <span class="text-red-500">*</span></h4>
                            
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                                <!-- Selector múltiple -->
                                <div class="bg-white border border-gray-300 rounded-lg max-h-40 overflow-y-auto p-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full shadow-inner">
                                    <div class="text-xs text-gray-400 font-bold mb-2 px-1">DISPONIBLES:</div>
                                    @foreach ($codigos_postales_disponibles as $codigoPostal)
                                        <label class="flex items-center p-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" wire:model.live="codigos_postales_seleccionados" value="{{ $codigoPostal->codigo_postal_id }}" class="form-checkbox h-4 w-4 text-emerald-600 rounded focus:ring-emerald-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700 font-mono font-bold">{{ $codigoPostal->codigo_postal }}</span>
                                        </label>
                                    @endforeach
                                    @if(count($codigos_postales_disponibles) === 0)
                                        <div class="text-sm text-gray-400 italic p-2 text-center">No hay códigos disponibles en esta zona.</div>
                                    @endif
                                </div>

                                <!-- Visualización seleccionados -->
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Seleccionados:</span>
                                    <div class="flex flex-wrap gap-2 overflow-y-auto max-h-40 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full pr-1 content-start">
                                        @php $hasSelected = false; @endphp
                                        @foreach ($codigos_postales_disponibles as $codigoPostal)
                                            @if (is_array($codigos_postales_seleccionados) && in_array($codigoPostal->codigo_postal_id, $codigos_postales_seleccionados))
                                                @php $hasSelected = true; @endphp
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 font-mono border border-emerald-200 drop-shadow-sm">
                                                    {{ $codigoPostal->codigo_postal }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if(!$hasSelected)
                                            <div class="w-full text-center text-sm text-gray-400 italic bg-gray-100/50 py-4 rounded-lg border border-dashed border-gray-300">Ningún código elegido</div>
                                        @endif
                                    </div>
                                    @error('codigos_postales_seleccionados') <span class="text-red-500 text-xs font-bold mt-3 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- ROLES Y PERSONAL -->
                        @if(!empty($tipo_oficina) && $tipo_oficina != 6 && $tipo_oficina != 7 )
                        <div class="bg-blue-50/60 rounded-xl border border-blue-200 md:col-span-2 xl:col-span-4 mt-2 overflow-hidden shadow-sm">
                            <h4 class="bg-blue-100/80 text-blue-900 text-sm font-bold px-5 py-3 border-b border-blue-200 flex justify-between items-center">
                                Asignación de Roles y Capacidad de Personal <span class="text-red-500">*</span>
                            </h4>
                            
                            <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                                @forelse ($roles as $rol)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all hover:shadow-md" wire:key="role-{{ $rol->id }}">
                                        
                                        {{-- CHECKBOX ROL --}}
                                        <label for="role-{{ $rol->id }}" class="flex items-center flex-1 cursor-pointer pr-4">
                                            <input id="role-{{ $rol->id }}" type="checkbox" value="{{ $rol->id }}" wire:model="rol_asignado" class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300 transition-all shadow-sm">
                                            <span class="ml-3 text-sm font-extrabold text-slate-700 leading-tight">{{ $rol->name }}</span>
                                        </label>

                                        {{-- INPUT CANTIDAD --}}
                                        <div class="flex items-center shrink-0 border-l border-gray-100 pl-4">
                                            <span class="text-xs text-gray-400 mr-2 font-black uppercase tracking-wider title-cant">Cant:</span>
                                            <input 
                                                type="number" 
                                                wire:model="cantidad_por_rol.{{ $rol->id }}" 
                                                min="1" 
                                                class="w-16 h-8 text-sm border-gray-300 bg-gray-50 rounded-lg shadow-inner focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-0 text-center font-black text-blue-800 placeholder-gray-300 transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                                placeholder="0">
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-8 text-center text-gray-500 italic bg-white rounded-xl border border-dashed border-gray-300">
                                        No hay roles disponibles para este tipo de oficina.
                                    </div>
                                @endforelse
                            </div>

                            @if($errors->has('rol_asignado') || $errors->has('error_roles'))
                            <div class="px-5 py-3 bg-red-50 border-t border-red-100">
                                @error('rol_asignado') <span class="text-red-500 text-xs font-bold block">{{ $message }}</span> @enderror
                                @error('error_roles') <span class="text-red-500 text-xs font-bold block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>
                        @endif

                    </div>
                </section>

            </div>

            {{-- FOOTER FIJO ABAJO --}}
            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 shrink-0 border-t border-gray-200">
                <button type="button" wire:click="$dispatch('cerrar-modal-crear')" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all font-bold text-sm shadow-sm">
                    Cerrar Modal
                </button>

                <button type="submit" class="px-8 py-2.5 bg-red-700 text-white rounded-lg hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-all font-bold text-sm shadow-md flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save">Confirmar y Guardar Oficina</span>
                    <span wire:loading.flex wire:target="save" class="items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </form>

</div>