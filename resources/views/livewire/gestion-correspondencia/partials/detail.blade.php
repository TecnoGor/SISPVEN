<div class="bg-white rounded-xl shadow-sm border border-gray-200" wire:key="detail-doc-card-{{ $active_doc['id'] }}">
    {{-- Header del Comunicado Formal --}}
    <div class="p-6 border-b border-gray-200 bg-gray-50 rounded-t-xl flex justify-between items-start">
        <div class="flex gap-4">
            <div class="bg-red-100 p-3 rounded-lg text-red-700 mt-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                    {{ $active_doc['type'] === 'OTRO' ? 'INDICACIONES' : $active_doc['type'] . ' OFICIAL' }}
                </p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $active_doc['subject'] }}</h3>
                <div class="flex items-center gap-4 mt-2 flex-wrap">
                    <span
                        class="font-mono bg-white border border-gray-300 px-3 py-1 rounded text-sm text-gray-700 font-bold">Ref:
                        {{ $active_doc['id'] }}</span>
                    @if(($active_doc['correcciones'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-bold rounded bg-amber-100 text-amber-700 border border-amber-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Correc. {{ $active_doc['correcciones'] }}
                        </span>
                    @endif
                    @if ($active_doc['referencia'])
                        <button wire:click="open_detail_ref('{{ $active_doc['referencia'] }}')"
                            class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded hover:bg-red-100 hover:text-red-800 transition cursor-pointer flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Respuesta a: {{ $active_doc['referencia'] }}
                        </button>
                    @endif
                    @if (!empty($active_doc['documento_raiz_codigo']))
                        <button wire:click="open_detail_from_root('{{ $active_doc['documento_raiz_codigo'] }}')"
                            class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded hover:bg-amber-100 hover:text-amber-800 transition cursor-pointer flex items-center gap-1 border border-amber-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Comunicado original: {{ $active_doc['documento_raiz_codigo'] }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col items-end gap-2">
            <button wire:click="{{ $previous_doc_id ? 'go_back_to_previous' : 'open_inbox' }}"
                class="text-gray-400 hover:text-red-600 transition p-1 bg-white rounded-full shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <span
                class="px-3 py-1 text-sm font-bold rounded-full {{ $active_doc['priority'] == 'Urgente' ? 'bg-red-100 text-red-700 border border-red-200' : ($active_doc['priority'] == 'Alta' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-gray-100 text-gray-600 border border-gray-200') }}">
                Prioridad: {{ $active_doc['priority'] }}
            </span>
        </div>
    </div>

    {{-- Metadatos del Comunicado --}}
    <div
        class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 border-b border-gray-200 divide-y md:divide-y-0 md:divide-x divide-gray-200">
        <div class="p-4 hover:bg-gray-50 transition">
            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">De (Remitente)</p>
            <p class="font-bold text-slate-700 text-sm">{{ $active_doc['sender'] }}</p>
            @if(!empty($active_doc['sender_rol']))
                <p class="text-[10px] text-gray-500 mt-0.5">Rol: {{ $active_doc['sender_rol'] }}</p>
            @endif
            @if(!empty($active_doc['sender_original']) && $active_doc['sender'] !== $active_doc['sender_original'])
                <p class="text-[10px] text-gray-400 mt-0.5">Autor: {{ $active_doc['sender_original'] }}@if(!empty($active_doc['sender_original_rol'])) · {{ $active_doc['sender_original_rol'] }}@endif</p>
            @endif
        </div>
        <div class="p-4 hover:bg-gray-50 transition">
            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Para (Destinatario)</p>
            <p class="font-bold text-slate-700 text-sm">{{ $active_doc['destinatario'] }}</p>
            @if(!empty($active_doc['destinatario_rol']))
                <p class="text-[10px] text-gray-500 mt-0.5">Rol: {{ $active_doc['destinatario_rol'] }}</p>
            @endif
        </div>
        <div class="p-4 hover:bg-gray-50 transition">
            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Recepción</p>
            <p class="font-bold text-slate-700 text-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $active_doc['date'] }}
            </p>
        </div>
        <div class="p-4 hover:bg-gray-50 transition">
            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Estatus</p>
            <p class="font-bold text-slate-700 text-sm flex items-center gap-2">
                <span class="px-3 py-1 text-[11px] font-bold rounded-full border {{ $active_doc['status_color'] ?? 'bg-gray-100 text-gray-800 border-gray-200' }} uppercase tracking-wider">
                    {{ $active_doc['status'] }}
                </span>
            </p>
        </div>
        <div class="p-4 bg-red-50/30 group transition border-l-4 border-red-500 md:border-l-0">
            <p class="text-[10px] text-red-600 font-black uppercase mb-1 tracking-widest">Límite Respuesta</p>
            <p class="font-black text-red-700 text-sm flex items-center gap-1.5">
                @if ($active_doc['fecha_limite'])
                    <svg class="w-3.5 h-3.5 text-red-500 group-hover:animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $active_doc['fecha_limite'] }}
                @else
                    <span class="text-gray-300 font-normal italic lowercase">sin límite</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Vista Real del Comunicado (PDF o Correo) --}}
    <div class="p-0 bg-gray-100 min-h-[600px] relative">
        @if($active_doc['type'] === 'OTRO')
            {{-- Vista Estilo Correo / Mensaje para Indicaciones --}}
            <div class="p-8 bg-white h-full overflow-y-auto min-h-[600px]">
                <div class="max-w-3xl mx-auto">
                    <div class="mb-4 pb-2 border-b border-gray-100">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Cuerpo del Comunicado / Instrucciones</h3>
                    </div>
                    <div class="prose prose-slate max-w-none text-lg leading-relaxed text-slate-700 whitespace-pre-line font-medium">
                        {{ $active_doc['cuerpo'] ?? $active_doc['cuerpo_detalle'] ?? 'Sin contenido registrado.' }}
                    </div>
                    <div class="mt-12 pt-6 border-t border-gray-100 text-[10px] text-gray-400 italic">
                        Este es un mensaje de indicaciones directas del Módulo de Correspondencia Digital IPOSTEL.
                    </div>
                </div>
            </div>
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 z-0">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-gray-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-400 font-medium">Cargando visualización oficial...</p>
                </div>
            </div>
            <iframe src="{{ $pdf_url }}" class="w-full h-[900px] border-none relative z-10 shadow-inner" type="application/pdf"></iframe>
        @endif
    </div>

    {{-- Modal de Previsualización de PDF --}}
    @if ($show_pdf_preview)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 class="font-bold text-slate-800">Visualizando: {{ $active_doc['subject'] }}</h3>
                    <div class="flex gap-2">
                        @if($active_doc['type'] !== 'OTRO')
                        <button wire:click="download_pdf('{{ $active_doc['id'] }}')"
                            class="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 transition">Descargar
                            PDF</button>
                        @endif
                        <button wire:click="close_pdf_preview" class="text-gray-500 hover:text-red-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-grow p-0">
                    @if($active_doc['type'] === 'OTRO')
                        {{-- Vista Estilo Correo / Mensaje para Indicaciones --}}
                        <div class="p-8 bg-white h-full overflow-y-auto min-h-[600px]">
                            <div class="max-w-3xl mx-auto">
                                <div class="mb-4 pb-2 border-b border-gray-100">
                                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Cuerpo del Comunicado / Instrucciones</h3>
                                </div>
                                <div class="prose prose-slate max-w-none text-lg leading-relaxed text-slate-700 whitespace-pre-line font-medium">
                                    {{ $active_doc['cuerpo'] ?? $active_doc['cuerpo_detalle'] ?? 'Sin contenido registrado.' }}
                                </div>
                                <div class="mt-12 pt-6 border-t border-gray-100 text-[10px] text-gray-400 italic">
                                    Este es un mensaje de indicaciones directas del Módulo de Correspondencia Digital IPOSTEL.
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Vista PDF Estándar para otros comunicados --}}
                        <iframe src="{{ $pdf_url }}" class="w-full h-full border-none rounded-b-xl"
                            type="application/pdf"></iframe>
                    @endif
                </div>
        </div>
    @endif


    {{-- Adjuntos / Anexos del Comunicado --}}
    @if(!empty($active_doc['adjuntos']))
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                Anexos Digitales ({{ count($active_doc['adjuntos']) }})
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($active_doc['adjuntos'] as $adjunto)
                    <a href="{{ asset('storage/' . $adjunto['ruta_archivo']) }}" target="_blank"
                        class="flex items-center gap-3 bg-white px-4 py-3 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition group">
                        <div class="bg-red-100 p-2 rounded-lg group-hover:bg-red-200 transition">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-700 truncate group-hover:text-red-700">{{ $adjunto['nombre_original'] }}</p>
                            <p class="text-xs text-gray-400">{{ $adjunto['tamano_formateado'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Trazabilidad / Recorrido del Comunicado --}}
    @if(!empty($active_doc['trazabilidad']) && count($active_doc['trazabilidad']) > 0)
    <div x-data="{ abierto: false }" class="border-t border-gray-200">
        <button @click="abierto = !abierto" class="w-full px-4 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition">
            <span class="text-sm font-bold text-slate-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Recorrido del Comunicado ({{ count($active_doc['trazabilidad']) }})
            </span>
            <svg :class="abierto ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="abierto" x-transition class="px-4 pb-4 bg-gray-50">
            <div class="relative pl-4 border-l-2 border-gray-200 space-y-3">
                @foreach($active_doc['trazabilidad'] as $i => $paso)
                    <div class="relative">
                        {{-- Punto en la línea --}}
                        <div class="absolute -left-[calc(1rem+5px)] top-1 w-2.5 h-2.5 rounded-full border-2 border-white {{ $i === count($active_doc['trazabilidad']) - 1 ? 'bg-red-500' : 'bg-gray-300' }}"></div>
                        <div class="flex items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700">{{ $paso['emisor'] ?? $active_doc['sender_original'] }}</span>
                                        @if($paso['emisor_rol'] ?? ($active_doc['sender_original_rol'] ?? null))
                                            <span class="text-[10px] text-gray-500">Rol: {{ $paso['emisor_rol'] ?? $active_doc['sender_original_rol'] }}</span>
                                        @endif
                                    </div>
                                    <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700">{{ $paso['destinatario'] }}</span>
                                        @if(!empty($paso['destinatario_rol']))
                                            <span class="text-[10px] text-gray-500">Rol: {{ $paso['destinatario_rol'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $paso['fecha'] }}</p>
                                @if(!empty($paso['observacion']))
                                    <div class="mt-1.5 pl-3 border-l-2 border-yellow-300">
                                        <p class="text-[11px] text-yellow-800 bg-yellow-50 px-2 py-1 rounded italic leading-relaxed">
                                            "{{ $paso['observacion'] }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Acciones del Comunicado --}}
    @php
        $devuelto = ($active_doc['motivo_id'] ?? null) == 7;
        // estatus_id=7 significa que el usuario devolvió el comunicado (ya no lo tiene)
        $ya_devolvio = ($active_doc['estatus_id'] ?? null) == 7;
        // Estatus que indican que el usuario ya no tiene que confirmar o ya no posee el comunicado
        $estatus_finales = ['Confirmación de Recibido', 'Respondido', 'Aprobado / Firmado', 'Rechazado / Archivado', 'Remitido', 'Completado'];
        $necesita_confirmacion = !$ya_devolvio
            && !in_array($active_doc['status'], $estatus_finales);
        $ya_confirmado = in_array($active_doc['status'], ['Confirmación de Recibido', 'Aprobado / Firmado', 'Rechazado / Archivado', 'Remitido', 'Completado']);
    @endphp
    <div class="p-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex flex-col gap-3">

        {{-- ═══ PASO 1: Confirmar Recepción (TODOS los roles) ═══ --}}
        @if($necesita_confirmacion)
        <div class="flex flex-wrap gap-2">
            <button wire:click="confirmar_recepcion('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Confirmar Recepción
            </button>
        </div>
        @endif

        {{-- ═══ DEVUELTO PARA CORREGIR ═══ --}}

        {{-- Analista: Editar corrección --}}
        @if($current_role === 'Analista' && $devuelto && $ya_confirmado)
        <div class="bg-white border border-yellow-200 rounded-lg p-4 flex flex-col gap-3">
            <h4 class="text-sm font-bold text-yellow-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                Comunicado devuelto para corrección
            </h4>
            @php $ultimo_paso = collect($active_doc['trazabilidad'])->last(); @endphp
            @if($ultimo_paso && !empty($ultimo_paso['observacion']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs font-bold text-yellow-700 mb-1">Motivo de la devolución:</p>
                    <p class="text-sm text-yellow-900 italic">"{{ $ultimo_paso['observacion'] }}"</p>
                </div>
            @endif
            <div class="flex flex-wrap gap-2">
                @if(($active_doc['remitente_id'] ?? null) == auth()->id())
                    <button wire:click="open_edit_correccion('{{ $active_doc['id'] }}')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar y corregir comunicado
                    </button>
                @endif
            </div>
        </div>
        @endif

        {{-- Gerente: Devolver al creador o Responder al que devolvió, o Editar si es el creador --}}
        @if($current_role === 'Gerente' && $devuelto && $ya_confirmado && !$ya_devolvio && (($active_doc['emisor_id'] ?? null) == auth()->id() || ($active_doc['remitente_id'] ?? null) == auth()->id()))
        <div class="bg-white border border-yellow-200 rounded-lg p-4 flex flex-col gap-3">
            <h4 class="text-sm font-bold text-yellow-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                Comunicado devuelto para corrección
            </h4>
            @php $ultimo_paso = collect($active_doc['trazabilidad'])->last(); @endphp
            @if($ultimo_paso && !empty($ultimo_paso['observacion']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs font-bold text-yellow-700 mb-1">Motivo de la devolución:</p>
                    <p class="text-sm text-yellow-900 italic">"{{ $ultimo_paso['observacion'] }}"</p>
                </div>
            @endif
            <div class="flex flex-wrap gap-2">
                @if(($active_doc['remitente_id'] ?? null) == auth()->id())
                    <button wire:click="open_edit_correccion('{{ $active_doc['id'] }}')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar y corregir comunicado
                    </button>
                @else
                    <button wire:click="open_devolver('{{ $active_doc['id'] }}')"
                        class="px-4 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Devolver al creador
                    </button>
                    <button wire:click="open_reply('{{ $active_doc['id'] }}')"
                        class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Responder al remitente
                    </button>
                @endif
            </div>
        </div>
        @include('livewire.gestion-correspondencia.partials.devolver-modal')
        @endif

        {{-- Director: Editar corrección (cuando es el creador original y le devuelven) --}}
        @if($current_role === 'Director' && $devuelto && $ya_confirmado && !$ya_devolvio && ($active_doc['remitente_id'] ?? null) == auth()->id())
        <div class="bg-white border border-yellow-200 rounded-lg p-4 flex flex-col gap-3">
            <h4 class="text-sm font-bold text-yellow-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                Comunicado devuelto para corrección
            </h4>
            @php $ultimo_paso_dir = collect($active_doc['trazabilidad'])->last(); @endphp
            @if($ultimo_paso_dir && !empty($ultimo_paso_dir['observacion']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs font-bold text-yellow-700 mb-1">Motivo de la devolución:</p>
                    <p class="text-sm text-yellow-900 italic">"{{ $ultimo_paso_dir['observacion'] }}"</p>
                </div>
            @endif
            <div class="flex flex-wrap gap-2">
                <button wire:click="open_edit_correccion('{{ $active_doc['id'] }}')"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar y corregir documento
                </button>
            </div>
        </div>
        @endif


        {{-- ═══ PASO 2: Acciones post-confirmación (solo si ya confirmó) ═══ --}}

        {{-- ── USUARIO GENERAL: Completar ─────────────────────────────── --}}
        @if($current_role === 'Usuario General' && $ya_confirmado && $active_doc['status'] !== 'Completado')
        <div class="flex flex-wrap gap-2">
            <button wire:click="completar_comunicado('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Marcar como Completado
            </button>
        </div>
        @endif
        @if($current_role === 'Usuario General' && $active_doc['status'] === 'Completado')
        <div class="flex flex-wrap gap-2">
            <span class="px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-lg font-bold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Comunicado completado
            </span>
        </div>
        @endif

        {{-- ── ANALISTA: Responder (sin remitir ni devolver) ───────────── --}}
        @if($current_role === 'Analista' && $ya_confirmado)
        <div class="flex flex-wrap gap-2">
            <button wire:click="open_reply('{{ $active_doc['id'] }}')"
                class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Responder con el mismo tipo de comunicado
            </button>
        </div>
        @endif

        {{-- ── GERENTE: Responder / Remitir / Devolver ─────────────────── --}}
        @if($current_role === 'Gerente' && $ya_confirmado && !$ya_devolvio)
        <div class="flex flex-wrap gap-2 items-center">
            <button wire:click="open_reply('{{ $active_doc['id'] }}')"
                class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Responder con el mismo tipo de comunicado
            </button>
            <button wire:click="open_remitir_modal('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Remitir
            </button>
            <button wire:click="open_devolver('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Devolver
            </button>
            @include('livewire.gestion-correspondencia.partials.remitir-modal')
        </div>
        @include('livewire.gestion-correspondencia.partials.devolver-modal')
        @endif

        {{-- ── DIRECTOR: Responder / Remitir / Devolver ────────────────── --}}
        @if($current_role === 'Director' && $ya_confirmado && !$ya_devolvio)
        <div class="flex flex-wrap gap-2 items-center">
            <button wire:click="open_reply('{{ $active_doc['id'] }}')"
                class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Responder con el mismo tipo de comunicado
            </button>
            <button wire:click="open_remitir_modal('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Remitir
            </button>
            <button wire:click="open_devolver('{{ $active_doc['id'] }}')"
                class="px-4 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Devolver
            </button>
            @include('livewire.gestion-correspondencia.partials.remitir-modal')
        </div>
        @include('livewire.gestion-correspondencia.partials.devolver-modal')
        @endif

        {{-- ── PRESIDENTE: Responder / Aprobar / Archivar / Devolver ───── --}}
        @if($current_role === 'Presidente' && $ya_confirmado && !$ya_devolvio)
        <div class="flex flex-wrap gap-2 items-center">
            @if(!($active_doc['firmado'] ?? false))
            <button wire:click="open_reply('{{ $active_doc['id'] }}')"
                class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Responder con el mismo tipo de comunicado
            </button>
            @endif
            @if(!($active_doc['firmado'] ?? false))
                <button wire:click="aprobarYFirmar({{ $active_doc['db_id'] }})"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aprobar y Firmar
                </button>
            @else
                <div class="px-4 py-2 bg-teal-50 text-teal-700 rounded-lg font-bold text-sm flex items-center gap-2 border border-teal-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Firmado por {{ $active_doc['firmado_por'] ?? '' }}
                    @if(!empty($active_doc['fecha_firma'])) el {{ $active_doc['fecha_firma'] }} @endif
                </div>

                @php
                    $tiposFinalizanAlFirmar = [
                        'Punto de Información - Presidencia IPOSTEL',
                        'Punto de Cuenta - Presidencia IPOSTEL',
                        'Punto de Cuenta - Directorio',
                        'Punto-de-Cuenta-MPPT',
                        'Punto-de-Informacion-MPPT',
                        'Oficio - Tipo Carta',
                        'Oficio - Tipo Oficio',
                    ];
                    $finalizaAlFirmar = in_array($active_doc['type'] ?? '', $tiposFinalizanAlFirmar);
                @endphp
                @if(!$finalizaAlFirmar)
                    @if(!($active_doc['enviado_a_final'] ?? false))
                        <button wire:click="abrirModalEnviarFinal({{ $active_doc['db_id'] }})"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-sm shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Enviar al Destinatario Final
                        </button>
                    @else
                        @php
                            $finalUser = !empty($active_doc['destinatario_final_id'])
                                ? \App\Models\User::find($active_doc['destinatario_final_id'])
                                : null;
                        @endphp
                        <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg font-bold text-sm flex items-center gap-2 border border-blue-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Enviado a: {{ $finalUser?->name ?? 'destinatario final' }}
                        </div>
                    @endif
                @endif

                {{-- Modal envío al destinatario final --}}
                @if($show_enviar_final_modal && $enviar_final_db_id == $active_doc['db_id'])
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800">Enviar al Destinatario Final</h3>
                                <p class="text-sm text-gray-500">
                                    @if($enviar_final_fijo ?? false)
                                        El destinatario fue definido por el creador del documento
                                    @else
                                        Verifique o cambie el destinatario antes de enviar
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest">Destinatario Final</label>
                            @if($enviar_final_fijo ?? false)
                                {{-- Destinatario fijo: solo lectura --}}
                                <div class="w-full rounded-lg border border-blue-200 bg-blue-50 p-3 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <div>
                                        <p class="font-black text-slate-800 text-sm">{{ $enviar_final_nombre }}</p>
                                        <p class="text-xs text-gray-500">{{ $enviar_final_email }}</p>
                                    </div>
                                </div>
                                <p class="text-[11px] text-blue-600 font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Este destinatario no puede ser modificado
                                </p>
                            @else
                                {{-- Destinatario editable --}}
                                @if($enviar_final_nombre)
                                    <p class="text-[11px] text-blue-600 font-semibold mb-1">
                                        Asignado por el creador del documento: <span class="font-black">{{ $enviar_final_nombre }}</span>
                                    </p>
                                @else
                                    <p class="text-[11px] text-amber-600 font-semibold mb-1">
                                        Este documento no tiene destinatario final asignado. Seleccione uno.
                                    </p>
                                @endif
                                @php $usuariosFinales = \App\Models\User::role('Usuario Correspondencia')->orderBy('name')->get(['id','name','email']); @endphp
                                <select wire:model.live="enviar_final_seleccionado_id"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">— {{ $enviar_final_nombre ? 'Cambiar destinatario...' : 'Seleccione el destinatario' }} —</option>
                                    @foreach($usuariosFinales as $u)
                                        <option value="{{ $u->id }}" @selected($u->id == $enviar_final_seleccionado_id)>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($enviar_final_seleccionado_id && $enviar_final_nombre && $enviar_final_seleccionado_id != ($active_doc['destinatario_final_id'] ?? ''))
                                    <p class="text-[11px] text-amber-600 font-bold mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Está cambiando el destinatario original
                                    </p>
                                @endif
                            @endif
                        </div>

                        <div class="flex gap-2 justify-end mt-2">
                            <button wire:click="cerrarModalEnviarFinal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button wire:click="enviarDestinatarioFinal({{ $enviar_final_db_id }})"
                                @if(!($enviar_final_fijo ?? false) && !$enviar_final_nombre && !$enviar_final_seleccionado_id) disabled @endif
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Confirmar Envío
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Modal envío Circular (múltiples destinatarios) --}}
                @if(($show_enviar_circular_modal ?? false) && $enviar_circular_db_id == $active_doc['db_id'])
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800">Enviar Circular</h3>
                                <p class="text-sm text-gray-500">Seleccione uno o más destinatarios</p>
                            </div>
                        </div>

                        @php
                            $usuariosCircular = \App\Models\User::role('Usuario Correspondencia')->orderBy('name')->get(['id','name','email']);
                        @endphp

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest">Destinatarios</label>
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @foreach($usuariosCircular as $u)
                                    <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox"
                                            wire:model.live="circular_destinatarios_seleccionados"
                                            value="{{ $u->id }}"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <div>
                                            <p class="text-sm font-bold text-slate-700">{{ $u->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @if(!empty($circular_destinatarios_seleccionados))
                                <p class="text-[11px] text-purple-600 font-semibold">
                                    {{ count($circular_destinatarios_seleccionados) }} destinatario(s) seleccionado(s)
                                </p>
                            @endif
                        </div>

                        <div class="flex gap-2 justify-end mt-2">
                            <button wire:click="cerrarModalCircular"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button wire:click="enviarCircular({{ $enviar_circular_db_id }})"
                                @if(empty($circular_destinatarios_seleccionados)) disabled @endif
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg font-bold text-sm hover:bg-purple-700 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Enviar Circular
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            @endif
            @if(!($active_doc['firmado'] ?? false))
                @if($active_doc['archivado'] ?? false)
                    <div class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold text-sm flex items-center gap-2 border border-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Rechazado por {{ $active_doc['archivado_por'] ?? '' }}
                        @if(!empty($active_doc['fecha_archivo'])) el {{ $active_doc['fecha_archivo'] }} @endif
                    </div>
                @else
                    <button wire:click="negarYArchivar('{{ $active_doc['id'] }}')"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-bold text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Rechazar
                    </button>
                @endif
                <button wire:click="open_devolver('{{ $active_doc['id'] }}')"
                    class="px-4 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Devolver
                </button>
            @endif
        </div>
        @include('livewire.gestion-correspondencia.partials.devolver-modal')
        @endif

    </div>

</div> </div>
</div>
