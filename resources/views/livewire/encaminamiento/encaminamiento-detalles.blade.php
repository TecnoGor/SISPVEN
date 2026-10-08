@section('titulo')
    Detalles de Rastreo y Seguimiento
@endsection

<div>
    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-left w-full md:w-auto">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-black tracking-tight uppercase">
                Rastreo y Seguimiento
            </h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">
                Historial de movimientos del envío
            </p>
        </div>

        {{-- Botón regresar --}}
        <a href="{{ route('encaminamiento.encaminamiento') }}"
           class="inline-flex items-center gap-2 bg-white border-2 border-gray-200 shadow-sm
                  rounded-xl px-5 py-2.5 text-sm font-bold text-gray-600
                  transition-all duration-200
                  hover:border-primary hover:text-primary hover:shadow-md hover:bg-gray-50 active:scale-95 uppercase tracking-widest min-w-max">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            Volver
        </a>
    </div>

    {{-- Contenedor --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 mb-10">

        {{-- Código Badge --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="bg-primary/10 p-2.5 rounded-xl text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-[10px] uppercase font-black tracking-widest text-gray-400">Código de Envío</p>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3 flex-wrap">
                    {{ $envio->codigo_envio }}

                    {{-- envios.devolucion indica que el envio esta actualmente en
                         devolucion. Se muestra en ambar para diferenciarlo del
                         verde del recorrido normal. --}}
                    @if ($envio->devolucion)
                        <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 border border-amber-300 rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4" />
                            </svg>
                            En Devolución
                        </span>
                    @endif
                </h2>
            </div>
        </div>

        <div class="bg-white shadow-xl shadow-gray-200/50 rounded-3xl border border-gray-100 overflow-hidden">
            {{-- Sección Info Envío --}}
            <div class="bg-gray-50/80 px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Información del Envío
                </h3>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50 space-y-1 hover:border-gray-200 transition-colors">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo de Envío</span>
                        <span class="block text-sm font-bold text-gray-800">{{ strtoupper($envio->tipo_envio) }}</span>
                    </div>

                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50 space-y-1 sm:col-span-2 lg:col-span-2 hover:border-gray-200 transition-colors">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Contenido</span>
                        <span class="block text-sm font-bold text-gray-800">{{ $envio->contenido }}</span>
                    </div>

                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50 space-y-1 hover:border-gray-200 transition-colors">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Peso</span>
                        <span class="block text-sm font-bold text-gray-800">
                            @if($envio->peso) {{ $envio->peso }} g @else <span class="text-gray-400 italic font-medium">N/A</span> @endif
                        </span>
                    </div>

                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50 space-y-1 hover:border-gray-200 transition-colors">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Costo</span>
                        <span class="block text-[15px] font-black text-primary tracking-tight">
                            @if($envio->coste) 
                                {{ number_format($envio->coste, 2, ',', '.') }} <span class="text-[11px] opacity-70">Bs</span> 
                            @else 
                                <span class="text-gray-400 italic font-medium">N/A</span> 
                            @endif
                        </span>
                    </div>

                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50 space-y-1 hover:border-gray-200 transition-colors">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Origen</span>
                        <span class="block text-sm font-bold text-gray-800">{{ $envio->oficinas->nombre }}</span>
                    </div>

                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-300 shadow-sm shadow-blue-50 space-y-1 sm:col-span-2 lg:col-span-2 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 text-blue-100/50">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <span class="block text-[10px] font-black text-blue-500 uppercase tracking-widest">Estatus Actual</span>
                            <div class="flex items-center gap-2.5 mt-0.5">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                                </span>
                                <span class="block flex-1 text-sm font-black text-blue-800">
                                    @if($status->envio_estatus->estatus === 'Salida hacia COP' && $status->oficina_externa?->externa)
                                        SALIDA HACIA ALIADO
                                    @else
                                        {{ strtoupper($status->envio_estatus->estatus) }}
                                    @endif
                                    @if($status->oficina_externa->nombre ?? '')
                                        <span class="text-blue-400 font-bold mx-1">-</span> {{ strtoupper($status->oficina_externa->nombre) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-sección: Destino del Envío --}}
                <div class="mt-8 pt-6 border-t-2 border-dashed border-gray-300">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="p-1.5 bg-red-50 rounded-lg border border-red-100 text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <h4 class="text-xs font-black text-gray-600 uppercase tracking-widest">Destino del Envío</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-red-50/30 p-4 rounded-2xl border border-red-100/60 space-y-1 hover:border-red-200 transition-colors">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</span>
                            <span class="block text-sm font-bold text-gray-800">
                                @if($envio->estadoDestino?->nombre)
                                    {{ $envio->estadoDestino->nombre }}
                                @else
                                    <span class="text-gray-400 italic font-medium">N/A</span>
                                @endif
                            </span>
                        </div>

                        <div class="bg-red-50/30 p-4 rounded-2xl border border-red-100/60 space-y-1 hover:border-red-200 transition-colors">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Municipio</span>
                            <span class="block text-sm font-bold text-gray-800">
                                @php
                                    $municipioDest = $envio->municipio_dest
                                        ? \App\Models\Municipio::find($envio->municipio_dest)
                                        : null;
                                @endphp
                                @if($municipioDest?->nombre)
                                    {{ $municipioDest->nombre }}
                                @else
                                    <span class="text-gray-400 italic font-medium">N/A</span>
                                @endif
                            </span>
                        </div>

                        <div class="bg-red-50/30 p-4 rounded-2xl border border-red-100/60 space-y-1 hover:border-red-200 transition-colors">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Parroquia</span>
                            <span class="block text-sm font-bold text-gray-800">
                                @php
                                    $parroquiaDest = $envio->parroquia_dest
                                        ? \App\Models\Parroquia::find($envio->parroquia_dest)
                                        : null;
                                @endphp
                                @if($parroquiaDest?->nombre)
                                    {{ $parroquiaDest->nombre }}
                                @else
                                    <span class="text-gray-400 italic font-medium">N/A</span>
                                @endif
                            </span>
                        </div>

                        <div class="bg-red-50/30 p-4 rounded-2xl border border-red-100/60 space-y-1 hover:border-red-200 transition-colors">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Código Postal</span>
                            <span class="block text-sm font-bold text-gray-800">
                                @if($envio->codigo_postal_dest)
                                    {{ $envio->codigo_postal_dest }}
                                @else
                                    <span class="text-gray-400 italic font-medium">N/A</span>
                                @endif
                            </span>
                        </div>

                        <div class="bg-red-50/30 p-4 rounded-2xl border border-red-100/60 space-y-1 sm:col-span-2 lg:col-span-4 hover:border-red-200 transition-colors">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Dirección</span>
                            <span class="block text-sm font-medium text-gray-700">
                                @if($envio->direccion_dest)
                                    {{ $envio->direccion_dest }}
                                @else
                                    <span class="text-gray-400 italic font-medium">N/A</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-400/80">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Remitente --}}
                    <div class="bg-gray-50/60 rounded-2xl p-6 border border-gray-200 relative overflow-hidden group hover:border-gray-200 transition-colors">
                        <div class="absolute -right-4 -top-4 text-gray-100/60 group-hover:text-gray-100 transition-colors">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-200/60 pb-3 mb-5 flex items-center gap-2 relative z-10">
                            <span class="p-1.5 bg-white rounded-lg border border-gray-200 shadow-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            Datos del Remitente
                        </h4>
                        <div class="space-y-6 relative z-10">
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nombre y Apellido</span>
                                <span class="block text-[15px] font-bold text-gray-800">{{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Documento</span>
                                <span class="block text-[15px] font-medium text-gray-600">
                                    {{ $envio->tipo_documento_rem ? $envio->tipo_documento_rem . '-' : '' }}{{ $envio->documento_rem }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Teléfono</span>
                                <span class="block text-[15px] font-medium text-gray-600">{{ $envio->telefono_rem ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Correo Electrónico</span>
                                <span class="block text-[15px] font-medium text-gray-600 break-all">{{ $envio->correo_rem ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Destinatario --}}
                    <div class="bg-gray-50/60 rounded-2xl p-6 border border-gray-200 relative overflow-hidden group hover:border-gray-200 transition-colors">
                        <div class="absolute -right-4 -top-4 text-gray-100/60 group-hover:text-gray-100 transition-colors">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-200/60 pb-3 mb-5 flex items-center gap-2 relative z-10">
                            <span class="p-1.5 bg-white rounded-lg border border-gray-200 shadow-sm text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </span>
                            Datos del Destinatario
                        </h4>
                        <div class="space-y-6 relative z-10">
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nombre y Apellido</span>
                                <span class="block text-[15px] font-bold text-gray-800">{{ $envio->nombre_dest }} {{ $envio->apellido_dest }}</span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Documento</span>
                                <span class="block text-[15px] font-medium text-gray-600">
                                    {{ $envio->tipo_documento_dest ? $envio->tipo_documento_dest . '-' : '' }}{{ $envio->documento_dest }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Teléfono</span>
                                <span class="block text-[15px] font-medium text-gray-600">{{ $envio->tlf_dest ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Correo Electrónico</span>
                                <span class="block text-[15px] font-medium text-gray-600 break-all">{{ $envio->correo_dest ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rastreo --}}
            <div class="bg-gray-50/80 px-8 py-5 border-y border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-widest text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Encaminamiento
                </h3>
            </div>

            <div class="p-6 md:p-8 bg-gray-50/30">
                <div class="relative max-w-4xl mx-auto">
                    {{-- Línea vertical de fondo (continua, gruesa y verde para indicar camino completado) --}}
                    {{-- Calculado para centrarse con el icono de 10x10 (w-10 = 40px) -> Centro = 20px --}}
                    <div class="absolute left-[18px] top-5 bottom-5 w-0 border-l-[3.5px] border-solid border-green-500/60"></div>

                    <div class="space-y-5 relative">
                        @foreach ($results as $index => $tr)
                            @php
                                $isLatest = $index === 0;
                                $statusStr = strtoupper($tr->envio_estatus->estatus);
                                $isSalidaAliado = ($tr->envio_estatus->estatus === 'Salida hacia COP' && $tr->oficina_externa?->externa);
                                
                                if($isSalidaAliado) {
                                    $statusStr = 'SALIDA HACIA ALIADO';
                                }

                                // envios_encaminamiento.devolucion marca los movimientos
                                // ocurridos con el envio en devolucion. Se pintan en ambar
                                // para distinguirlos del recorrido normal (verde).
                                $esDevolucion = (bool) $tr->devolucion;
                                $c = $esDevolucion
                                    ? ['icono_bg' => 'bg-amber-100', 'icono_sombra' => 'shadow-amber-100/50', 'icono_txt' => 'text-amber-600',
                                       'borde' => 'border-amber-300', 'sombra' => 'shadow-amber-100/50', 'acento' => 'bg-amber-500',
                                       'txt' => 'text-amber-600', 'fecha_bg' => 'bg-amber-100/80']
                                    : ['icono_bg' => 'bg-green-100', 'icono_sombra' => 'shadow-green-100/50', 'icono_txt' => 'text-green-600',
                                       'borde' => 'border-green-300', 'sombra' => 'shadow-green-100/50', 'acento' => 'bg-green-500',
                                       'txt' => 'text-green-600', 'fecha_bg' => 'bg-green-100/80'];
                            @endphp

                            <div class="flex items-start gap-4 lg:gap-6 relative group">
                                {{-- Icono del Timeline (Más pequeño) --}}
                                <div class="flex-shrink-0 w-10 h-10 {{ $c['icono_bg'] }} rounded-xl flex items-center justify-center border-[3px] border-white shadow-md {{ $c['icono_sombra'] }} relative z-10 transform transition-transform duration-300 group-hover:scale-110 mt-1">
                                    <svg class="w-5 h-5 {{ $c['icono_txt'] }} relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        @if ($esDevolucion)
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        @endif
                                    </svg>
                                </div>

                                {{-- Tarjeta de Contenido --}}
                                <div class="flex-1 bg-white rounded-xl border {{ $c['borde'] }} shadow-md {{ $c['sombra'] }} pt-4 pb-4 px-5 transition-all duration-300 relative overflow-hidden group-hover:-translate-y-0.5 group-hover:shadow-lg">

                                    {{-- Acento lateral: verde en recorrido normal, ambar en devolucion --}}
                                    <div class="absolute top-0 left-0 w-1.5 h-full {{ $c['acento'] }}"></div>

                                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-3">
                                        {{-- Lado Izquierdo: Estatus y Oficina --}}
                                        <div class="space-y-1">
                                            <h4 class="font-black text-gray-900 text-[16px] md:text-[18px] tracking-tight leading-tight uppercase relative -top-0.5">
                                                {{ $statusStr }}
                                                
                                                @if($tr->oficina_externa->nombre ?? '')
                                                    <span class="text-gray-300 font-black mx-1">|</span>
                                                    <span class="text-gray-500 font-bold text-sm">{{ strtoupper($tr->oficina_externa->nombre) }}</span>
                                                @endif

                                                {{-- Etiqueta ademas del color: el estado no debe
                                                     depender unicamente de la percepcion cromatica. --}}
                                                @if ($esDevolucion)
                                                    <span class="align-middle ml-2 inline-flex items-center bg-amber-100 text-amber-800 border border-amber-300 rounded px-2 py-0.5 text-[10px] font-black uppercase tracking-widest">
                                                        Devolución
                                                    </span>
                                                @endif
                                            </h4>
                                            
                                            <p class="text-xs font-bold text-gray-500 flex items-center gap-1.5 tracking-widest uppercase">
                                                <svg class="w-3.5 h-3.5 {{ $c['txt'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $tr->oficinas->nombre }}
                                            </p>
                                        </div>

                                        {{-- Lado Derecho: Fecha y Hora --}}
                                        <div class="flex-shrink-0 flex flex-row xl:flex-col items-center xl:items-end gap-3 xl:gap-0 mt-2 xl:mt-0">
                                            <span class="text-[12px] font-black {{ $c['txt'] }} {{ $c['fecha_bg'] }} px-2.5 py-1 rounded border border-transparent flex items-center gap-1.5 tracking-widest uppercase shadow-sm">
                                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ \Carbon\Carbon::parse($tr->created_at)->format('d M, Y') }}
                                            </span>
                                            <span class="text-[13px] font-bold text-gray-500 flex items-center gap-1 mt-1 xl:mt-0.5">
                                                <svg class="w-3.5 h-3.5 opacity-70 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ \Carbon\Carbon::parse($tr->created_at)->format('h:i:s A') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
