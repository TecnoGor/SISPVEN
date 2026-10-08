{{-- Vista de solo lectura para comunicados enviados (Actividad Reciente) --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200" wire:key="detail-sent-doc-card-{{ $active_doc['id'] }}">

    {{-- Header del Comunicado --}}
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
                            class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded hover:bg-blue-100 hover:text-blue-800 transition cursor-pointer flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Respuesta a: {{ $active_doc['referencia'] }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col items-end gap-2">
            <button wire:click="{{ $previous_doc_id ? 'go_back_to_previous' : 'open_dashboard' }}"
                class="text-gray-400 hover:text-red-600 transition p-1 bg-white rounded-full shadow-sm"
                title="{{ $previous_doc_id ? 'Volver al comunicado anterior' : 'Volver al Panel' }}">
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
            <p class="text-[10px] text-gray-400 font-black uppercase mb-1 tracking-widest">Enviado</p>
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

    {{-- Cuerpo del Comunicado (Vista Real PDF o Correo) --}}
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
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Pie — Botones de acción --}}
    <div class="p-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
        <button wire:click="open_dashboard"
            class="px-5 py-2 border border-gray-300 text-slate-700 bg-white hover:bg-gray-100 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al Panel
        </button>

        <div class="flex items-center gap-3">
            {{-- (Corrección eliminada — el flujo usa Responder/Remitir) --}}

            @if(in_array($current_role, ['Presidente', 'Director']) && isset($active_doc['es_real']) && $active_doc['es_real'] && isset($active_doc['db_id']))
                @if(!empty($active_doc['seguimiento']))
                    <button wire:click="toggle_seguimiento({{ $active_doc['db_id'] }})"
                        class="px-5 py-2 bg-amber-500 text-white hover:bg-amber-600 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        En Seguimiento
                    </button>
                @else
                    <button wire:click="toggle_seguimiento({{ $active_doc['db_id'] }})"
                        class="px-5 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Dar Seguimiento
                    </button>
                @endif
            @endif

            @if($previous_doc_id)
                <button wire:click="go_back_to_previous"
                    class="px-5 py-2 bg-primary text-white hover:bg-blue-800 rounded-lg transition font-bold text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Volver al Comunicado Respuesta
                </button>
            @endif
        </div>
    </div>
</div>
