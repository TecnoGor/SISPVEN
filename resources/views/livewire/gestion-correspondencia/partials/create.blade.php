<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="p-6 border-b border-gray-100 bg-gray-50 rounded-t-xl flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-slate-800">
                @if($editando_comunicado_id ?? false)
                    Corregir Comunicado
                @elseif($respuesta_a)
                    Responder Comunicación
                @else
                    Nueva Comunicación
                @endif
            </h3>
            @if ($editando_comunicado_id ?? false)
                <p class="text-sm text-yellow-700 mt-1 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editando comunicado existente — al guardar se corregira y re-enviara automaticamente
                </p>
            @elseif ($respuesta_a)
                <p class="text-sm text-gray-500 mt-1">En respuesta a: <span
                        class="font-mono font-bold text-red-600">{{ $respuesta_a['id'] }}</span> -
                    {{ $respuesta_a['subject'] }}</p>
            @endif
        </div>
        <button wire:click="$set('current_view', 'dashboard')" class="text-gray-400 hover:text-red-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <form wire:submit.prevent="save_document" class="p-6 space-y-5">
        @if (!$respuesta_a && !($editando_comunicado_id ?? false))
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tipo de Comunicación</label>
                @if($tipo_bloqueado ?? false)
                    <div class="w-full rounded-lg border border-gray-300 p-2.5 bg-gray-100 text-slate-700 font-bold text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Indicaciones (Redactar Texto)
                    </div>
                @else
                    <select wire:model.live="selected_doc_type"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2.5 bg-gray-50">
                        <option value="">Seleccione el tipo de comunicado...</option>
                        <option value="Agenda al Decisor">Agenda al Decisor</option>
                        <option value="Circular">Circular</option>
                        <option value="MEMORANDO">MEMORANDO</option>
                        <option value="Minuta Horizontal">Minuta Horizontal</option>
                        <option value="Oficio - Tipo Carta">Oficio - Tipo Carta</option>
                        <option value="Oficio - Tipo Oficio">Oficio - Tipo Oficio</option>
                        <option value="Punto de Cuenta - Directorio">Punto de Cuenta - Directorio</option>
                        <option value="Punto de Cuenta - Presidencia IPOSTEL">Punto de Cuenta - Presidencia IPOSTEL</option>
                        <option value="Punto de Información - Presidencia IPOSTEL">Punto de Información - Presidencia IPOSTEL</option>
                        <option value="Punto-de-Cuenta-MPPT">Punto de Cuenta - MPPT</option>
                        <option value="Punto-de-Informacion-MPPT">Punto de Información - MPPT</option>
                        <option value="OTRO">Indicaciones (Redactar Texto)</option>
                    </select>
                @endif
            </div>
        @endif

        @if ($selected_doc_type || $respuesta_a)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Columna Remitente --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-lg font-bold text-red-700 border-b border-red-200 pb-1 uppercase">Remitente /
                        Procedencia</h3>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Nombre del
                            Remitente</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" wire:model="form_data.remitente"
                                class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-100 text-sm font-semibold"
                                readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Rol / Cargo</label>
                        <div
                            class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600">
                            {{ $current_role }}
                        </div>
                    </div>
                </div>

                {{-- Columna Destinatario --}}
                @if(!in_array($selected_doc_type, ['Agenda al Decisor', 'Punto de Cuenta - Directorio']) || !$respuesta_a)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                        <h3 class="text-lg font-bold text-red-700 border-b border-red-200 pb-1 uppercase">Destinatario /
                            Destino
                        </h3>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1">Filtrar por Rol</label>
                                <select wire:model.live="filtro_rol"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm font-semibold">
                                    <option value="">Todos los Roles Permitidos</option>
                                    @if ($current_role === 'Presidente')
                                        <option value="Presidente Correspondencia">Presidente</option>
                                        <option value="Director Correspondencia">Director</option>
                                        <option value="Gerente Correspondencia">Gerente</option>
                                        <option value="Analista Correspondencia">Analista</option>
                                        <option value="Usuario Correspondencia">Usuario</option>
                                    @elseif($current_role === 'Director')
                                        <option value="Presidente Correspondencia">Presidente</option>
                                        <option value="Director Correspondencia">Director</option>
                                        <option value="Gerente Correspondencia">Gerente</option>
                                        <option value="Analista Correspondencia">Analista</option>
                                        <option value="Usuario Correspondencia">Usuario</option>
                                    @elseif($current_role === 'Gerente')
                                        <option value="Director Correspondencia">Director</option>
                                        <option value="Gerente Correspondencia">Gerente</option>
                                        <option value="Analista Correspondencia">Analista</option>
                                        <option value="Usuario Correspondencia">Usuario</option>
                                    @elseif($current_role === 'Analista')
                                        <option value="Gerente Correspondencia">Gerente</option>
                                        <option value="Analista Correspondencia">Analista</option>
                                        <option value="Usuario Correspondencia">Usuario</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1">Tipo de Envío</label>
                                <select wire:model.live="tipo_destino"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm font-semibold"
                                    {{ $respuesta_a ? 'disabled' : '' }}>
                                    <option value="Individual">Envío Individual (Específico)</option>
                                    @if ($current_role === 'Presidente')
                                        <option value="Todos los Roles">A todos los Directores y Usuarios</option>
                                    @elseif($current_role === 'Director')
                                        <option value="Presidente y Gerentes">Al Presidente, Gerentes y Usuarios</option>
                                    @elseif($current_role === 'Gerente')
                                        <option value="Director y Analistas">Al Director, Analistas y Usuarios</option>
                                    @elseif($current_role === 'Analista')
                                        <option value="Todos los Gerentes">A todos los Gerentes y Usuarios</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        @if ($tipo_destino === 'Individual')
                            <div wire:key="destinatario-individual-field">
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1">Seleccionar
                                    Destinatario</label>
                                <div class="relative">
                                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                        <!-- Input de Búsqueda -->
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                @if ($respuesta_a)
                                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 002 2h1a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2h1zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <input 
                                                type="text" 
                                                wire:model.live.debounce.300ms="destinatario_search"
                                                @focus="open = true"
                                                placeholder="Escriba nombre o correo del destinatario..."
                                                class="w-full pl-10 pr-10 py-2 bg-white border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all duration-200 text-sm placeholder:text-gray-400 font-medium"
                                                {{ $respuesta_a ? 'readonly' : '' }}
                                                autocomplete="off"
                                            >
                                            
                                            @if ($destinatario_email && !$respuesta_a)
                                                <button 
                                                    type="button"
                                                    wire:click="$set('destinatario_email', ''); $set('destinatario_search', ''); load_destinatarios();"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-600 transition-colors"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Sugerencias -->
                                        <div 
                                            x-show="open && !$wire.respuesta_a"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            class="absolute z-[100] w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
                                            style="display: none;"
                                        >
                                            <div class="max-h-64 overflow-y-auto custom-scrollbar">
                                                @if (!empty($destinatarios_lista))
                                                    @foreach ($destinatarios_lista as $dest)
                                                        <button 
                                                            type="button"
                                                            wire:click="select_suggested_destinatario({{ $dest['id'] }}, '{{ addslashes($dest['name']) }}', '{{ $dest['email'] }}')"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-3 p-3 hover:bg-red-50 transition-colors text-left border-b border-gray-50 last:border-0 group"
                                                        >
                                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center text-red-700 font-bold group-hover:scale-110 transition-transform">
                                                                {{ strtoupper(substr($dest['name'], 0, 1)) }}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-red-700 transition-colors">
                                                                    {{ $dest['name'] }}
                                                                </p>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-tighter">
                                                                        {{ $dest['role_name'] ?? 'Sin Rol' }}
                                                                    </span>
                                                                    <span class="text-xs text-gray-400 truncate">{{ $dest['email'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                @else
                                                    <div class="p-8 text-center bg-gray-50/50">
                                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm font-bold text-gray-500">No se encontraron colaboradores</p>
                                                        <p class="text-xs text-gray-400 mt-1">Pruebe con otro nombre o correo</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" wire:model="destinatario_email">
                                    </div>
                                    @error('destinatario_email')
                                        <span class="text-xs text-red-600 font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="p-2 bg-red-100/50 border border-red-200 rounded-lg">
                                <p class="text-[11px] text-red-700 font-bold leading-tight uppercase">
                                    <i class="fas fa-info-circle mr-1"></i> Canal Masivo: {{ $tipo_destino }}
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Info de Flujo Automático para Agenda/PCD --}}
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 space-y-4 flex flex-col justify-center">
                        <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-1 uppercase">Flujo de Trámite</h3>
                        <div class="p-4 bg-white/80 rounded-lg border border-blue-100 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-900 font-bold leading-tight">Retorno Automático</p>
                                    <p class="text-[11px] text-blue-600 mt-1">Este documento se enviará automáticamente al remitente original o al nivel superior correspondiente una vez sea firmado.</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-blue-50">
                                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest text-center">No requiere selección manual</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @php
                // La lista $usuariosFinales se carga una sola vez en el componente (load_memo_usuarios),
                // no se re-consulta aqui en cada render para no ralentizar cada interaccion de Livewire.
                $tiposConDestinatarioFinal = ['MEMORANDO', 'Oficio - Tipo Carta', 'Minuta Horizontal'];
                $requiereDestinatarioFinal = in_array($selected_doc_type, $tiposConDestinatarioFinal);
            @endphp



            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-500 uppercase mb-1">Asunto de la
                        Comunicación</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="text" wire:model="form_data.asunto"
                            class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-semibold"
                            placeholder="Ej: Solicitud de Insumos">
                        @error('form_data.asunto')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase mb-1">Prioridad</label>
                    <select wire:model="form_data.prioridad"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm font-semibold">
                        <option value="Normal">Normal</option>
                        <option value="Alta">Alta</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                    @error('form_data.prioridad')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase mb-1">Límite Respuesta</label>
                    <input type="date" wire:model="form_data.fecha_limite"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm font-semibold">
                    @error('form_data.fecha_limite')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if ($selected_doc_type === 'Agenda al Decisor')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos Específicos: Agenda al Decisor</h3>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Agenda N°</label>
                        <input type="text" wire:model="form_data.agenda_numero"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                            placeholder="Ej: 001">
                        @error('form_data.agenda_numero')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white/50 p-3 rounded border border-gray-100">
                    {{-- PARA --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1 tracking-tight italic border-b border-gray-200">PARA (Nombre)</label>
                        <select wire:model.live="agendaParaSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Destinatario —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form_data.agenda_para_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                        @if($form_data['agenda_para_nombre'] ?? '')
                            <div class="px-3 py-1.5 bg-white/80 rounded border border-gray-100">
                                <p class="text-[11px] font-bold text-red-700 truncate">{{ $form_data['agenda_para_nombre'] }}</p>
                                <p class="text-[9px] text-gray-500 uppercase">{{ $form_data['agenda_para_cargo'] ?? '' }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- PRESENTANTE --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1 tracking-tight italic border-b border-gray-200">PRESENTADO POR</label>
                        <select wire:model.live="agendaPresentadoSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Usuario —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form_data.presentante')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                        @if($form_data['presentante'] ?? '')
                            <div class="px-3 py-1.5 bg-white/80 rounded border border-gray-100">
                                <p class="text-[11px] font-bold text-red-700 truncate">{{ $form_data['presentante'] }}</p>
                                <p class="text-[9px] text-gray-500 uppercase">{{ $form_data['presentante_cargo'] ?? '' }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- VERIFICADO --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1 tracking-tight italic border-b border-gray-200">VERIFICADO POR</label>
                        <select wire:model.live="agendaVerificadoSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Usuario —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form_data.agenda_verificado_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                        @if($form_data['agenda_verificado_nombre'] ?? '')
                            <div class="px-3 py-1.5 bg-white/80 rounded border border-gray-100">
                                <p class="text-[11px] font-bold text-red-700 truncate">{{ $form_data['agenda_verificado_nombre'] }}</p>
                                <p class="text-[9px] text-gray-500 uppercase">{{ $form_data['agenda_verificado_cargo'] ?? '' }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- APROBADO --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1 tracking-tight italic border-b border-gray-200">APROBADO POR</label>
                        <select wire:model.live="agendaAprobadoSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Usuario —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form_data.agenda_aprobado_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                        @if($form_data['agenda_aprobado_nombre'] ?? '')
                            <div class="px-3 py-1.5 bg-white/80 rounded border border-gray-100">
                                <p class="text-[11px] font-bold text-red-700 truncate">{{ $form_data['agenda_aprobado_nombre'] }}</p>
                                <p class="text-[9px] text-gray-500 uppercase">{{ $form_data['agenda_aprobado_cargo'] ?? '' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Secuencia</label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio"
                                    wire:model="form_data.secuencia" value="Relación"
                                    class="text-red-600 focus:ring-red-500">
                                Relación</label>
                            <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio"
                                    wire:model="form_data.secuencia" value="Propuesta"
                                    class="text-red-600 focus:ring-red-500">
                                Propuesta</label>
                            <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio"
                                    wire:model="form_data.secuencia" value="Anexo"
                                    class="text-red-600 focus:ring-red-500">
                                Anexo</label>
                        </div>
                        @error('form_data.secuencia')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Texto del Asunto</label>
                    <textarea wire:model="form_data.texto_asunto" rows="4"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                        placeholder="Desarrollo central del asunto sometido a consideración..."></textarea>
                    @error('form_data.texto_asunto')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Resumen del Asunto (Indicar el
                        asunto...)</label>
                    <textarea wire:model="form_data.cuerpo_resumen" rows="3"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Describa el asunto detalladamente..."></textarea>
                    @error('form_data.cuerpo_resumen')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Propuesta Conclusiva (Finalmente, se
                        propone...)</label>
                    <textarea wire:model="form_data.cuerpo_propuesta" rows="2"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="¿Qué se espera obtener?"></textarea>
                    @error('form_data.cuerpo_propuesta')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="p-3 bg-red-50/50 rounded border border-red-100">
                    <label class="block text-sm font-bold text-red-700 mb-2 italic small-caps">¿Tiene Anexos /
                        Adjuntos?</label>
                    <div class="flex gap-8">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model.live="form_data.agenda_has_anexo" value="Sí"
                                class="text-red-600 focus:ring-red-500">
                            <span
                                class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">SÍ,
                                Posee Anexos</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" wire:model.live="form_data.agenda_has_anexo" value="No"
                                class="text-red-600 focus:ring-red-500">
                            <span
                                class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">NO
                                posee</span>
                        </label>
                    </div>
                </div>
            @elseif($selected_doc_type === 'MEMORANDO')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: MEMORANDO</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-white/50 rounded border border-red-100/50">
                        {{-- DESTINATARIO (PARA) --}}
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-red-700 border-b border-red-100 pb-1">DESTINATARIO (PARA)</p>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Seleccionar Usuario</label>
                                <select wire:model.live="memoPara"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach($memo_usuarios as $u)
                                        <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('form_data.memo_para_nombre')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($form_data['memo_para_nombre'] ?? '')
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Nombre</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $form_data['memo_para_nombre'] }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Cargo</p>
                                    <p class="text-sm text-gray-600">{{ $form_data['memo_para_cargo'] }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- REMITENTE (DE) --}}
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-red-700 border-b border-red-100 pb-1">REMITENTE (DE)</p>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Seleccionar Usuario</label>
                                <select wire:model.live="memoDe"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach($memo_usuarios as $u)
                                        <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('form_data.memo_de_nombre')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($form_data['memo_de_nombre'] ?? '')
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Nombre</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $form_data['memo_de_nombre'] }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Cargo</p>
                                    <p class="text-sm text-gray-600">{{ $form_data['memo_de_cargo'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-white/50 rounded border border-red-100/50">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Acción del Memorando</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.memo_accion" value="Solicitar"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Solicitar
                                    Recurso/Acción</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.memo_accion" value="Remitir"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Remitir
                                    Comunicado/Informe</span>
                            </label>
                        </div>
                        @error('form_data.memo_accion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO: (Para el cuerpo del
                                PDF)</label>
                            <input type="text" wire:model="form_data.memo_asunto_pdf"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Ej: Remisión de Informes Mensuales">
                            @error('form_data.memo_asunto_pdf')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">VISADO: (Formato
                                XX/xxxxx)</label>
                            <input type="text" wire:model="form_data.memo_visado"
                                x-data
                                x-on:input="
                                    let raw = $event.target.value.replace(/[^a-zA-Z]/g, '');
                                    let prefijo = raw.slice(0, 2).toUpperCase();
                                    let sufijo = raw.slice(2).toLowerCase();
                                    let formatted = prefijo;
                                    if (raw.length > 2) formatted += '/' + sufijo;
                                    else if (raw.length === 2) formatted += '/';
                                    $event.target.value = formatted;
                                    $wire.set('form_data.memo_visado', formatted);
                                "
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Ej: AC/mg">
                            @error('form_data.memo_visado')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Cuerpo / Detalle Técnico</label>
                        <textarea wire:model="form_data.memo_cuerpo_detalle" rows="5"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Describa el detalle técnico de lo que pide o envía..."></textarea>
                        @error('form_data.memo_cuerpo_detalle')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-3 bg-amber-50 rounded border border-amber-200 space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Firmante <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-amber-700">Seleccione el usuario cuyo nombre y cargo aparecerán bajo el "Atentamente," en el PDF.</p>
                        <select wire:model.live="firmanteMemSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Firmante —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @if($form_data['firmante_nombre'] ?? '')
                            <div class="px-3 py-2 bg-white rounded border border-amber-200">
                                <p class="text-sm font-semibold text-gray-800">{{ $form_data['firmante_nombre'] }}</p>
                                <p class="text-xs text-gray-500">{{ $form_data['firmante_cargo'] }}</p>
                            </div>
                        @endif
                        @error('form_data.firmante_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @elseif($selected_doc_type === 'Circular')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: CIRCULAR</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Título de la
                                Comunicación</label>
                            <input type="text" wire:model="form_data.circular_titulo"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                                placeholder="Ej: NUEVO HORARIO DE ATENCIÓN">
                            @error('form_data.circular_titulo')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="p-3 bg-white/50 rounded border border-red-100/50">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Acción de la Circular</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.circular_accion" value="comunica"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Comunica</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.circular_accion" value="notifica"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Notifica</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.circular_accion" value="informa"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Informa</span>
                            </label>
                        </div>
                        @error('form_data.circular_accion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Contenido Detallado</label>
                        <textarea wire:model="form_data.circular_contenido" rows="6"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Redacte de forma detallada la instrucción o información..."></textarea>
                        @error('form_data.circular_contenido')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">VISADO: (Formato
                                XX/xxxxx)</label>
                            <input type="text" wire:model="form_data.circular_visado"
                                x-data
                                x-on:input="
                                    let raw = $event.target.value.replace(/[^a-zA-Z]/g, '');
                                    let prefijo = raw.slice(0, 2).toUpperCase();
                                    let sufijo = raw.slice(2).toLowerCase();
                                    let formatted = prefijo;
                                    if (raw.length > 2) formatted += '/' + sufijo;
                                    else if (raw.length === 2) formatted += '/';
                                    $event.target.value = formatted;
                                    $wire.set('form_data.circular_visado', formatted);
                                "
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Ej: AC/mg">
                            @error('form_data.circular_visado')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50 rounded border border-amber-200 space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Firmante <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-amber-700">Seleccione el usuario cuyo nombre y cargo aparecerán bajo el "Atentamente," en el PDF.</p>
                        <select wire:model.live="firmanteCircSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Firmante —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @if($form_data['firmante_nombre'] ?? '')
                            <div class="px-3 py-2 bg-white rounded border border-amber-200">
                                <p class="text-sm font-semibold text-gray-800">{{ $form_data['firmante_nombre'] }}</p>
                                <p class="text-xs text-gray-500">{{ $form_data['firmante_cargo'] }}</p>
                            </div>
                        @endif
                        @error('form_data.firmante_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @elseif($selected_doc_type === 'Oficio - Tipo Carta' || $selected_doc_type === 'Oficio - Tipo Oficio')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: {{ strtoupper($selected_doc_type) }}</h3>



                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-white/50 rounded border border-red-100/50">
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-red-700 border-b border-red-100 pb-1">DESTINATARIO (PARA)</p>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nombre</label>
                                <input type="text" wire:model="form_data.oficio_carta_para_nombre"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Nombre completo del destinatario...">
                                @error('form_data.oficio_carta_para_nombre')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cargo</label>
                                <input type="text" wire:model="form_data.oficio_carta_para_cargo"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Cargo del destinatario...">
                                @error('form_data.oficio_carta_para_cargo')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-red-700 border-b border-red-100 pb-1">ADICIONAL</p>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase">Entidad</label>
                                <input type="text" wire:model="form_data.oficio_carta_entidad"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Nombre de la institución/empresa...">
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white/50 rounded border border-red-100/50">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Acción Principal</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.oficio_carta_accion" value="notificarle"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Notificarle</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.oficio_carta_accion" value="remitirle"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Remitirle</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="form_data.oficio_carta_accion" value="solicitarle"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition">Solicitarle</span>
                            </label>
                        </div>
                        @error('form_data.oficio_carta_accion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Contenido del Asunto</label>
                        <textarea wire:model="form_data.oficio_carta_contenido" rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Escriba el asunto detallado inmediatamente después del verbo seleccionado..."></textarea>
                        @error('form_data.oficio_carta_contenido')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-red-700 uppercase mb-1">Visado (Iniciales)</label>
                            <input type="text" wire:model="form_data.oficio_carta_visado_part"
                                class="w-full rounded border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Ej: OP/LS/as">
                            <span class="text-[9px] text-gray-500">Ej: OP/XX/ (Mayúsculas redactor, minúsculas transcriptor)</span>
                            @error('form_data.oficio_carta_visado_part')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-red-700 uppercase mb-1">c.c. (Con copia a)</label>
                            <input type="text" wire:model="form_data.oficio_carta_cc"
                                class="w-full rounded border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Ej: Dirección de RRHH">
                            @error('form_data.oficio_carta_cc')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50 rounded border border-amber-200 space-y-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Firmante <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-amber-700">Seleccione el usuario cuyo nombre y cargo aparecerán bajo el "Atentamente," en el PDF.</p>
                        <select wire:model.live="firmanteOficioSel"
                            class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                            <option value="">— Seleccionar Firmante —</option>
                            @foreach($memo_usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @if($form_data['firmante_nombre'] ?? '')
                            <div class="px-3 py-2 bg-white rounded border border-amber-200">
                                <p class="text-sm font-semibold text-gray-800">{{ $form_data['firmante_nombre'] }}</p>
                                <p class="text-xs text-gray-500">{{ $form_data['firmante_cargo'] }}</p>
                            </div>
                        @endif
                        @error('form_data.firmante_nombre')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @elseif($selected_doc_type === 'Punto de Información - Presidencia IPOSTEL')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: PUNTO DE INFORMACIÓN</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Presentado por
                                (Dirección)</label>
                            <input type="text" wire:model="form_data.pi_presentado_por"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Ej. Dirección de Tecnología">
                            @error('form_data.pi_presentado_por')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO: <span class="text-red-500 font-normal text-xs">(Texto que aparecerá en el encabezado del PDF)</span></label>
                        <input type="text" wire:model="form_data.pi_asunto_pdf"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                            placeholder="Ej. SOLICITUD DE MANTENIMIENTO PREVENTIVO DE EQUIPOS">
                        @error('form_data.pi_asunto_pdf')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Síntesis (Hechos)</label>
                        <textarea wire:model="form_data.pi_sintesis" rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Redacte de forma clara y cronológica la situación..."></textarea>
                        @error('form_data.pi_sintesis')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Recomendaciones
                            (Sugerencias)</label>
                        <textarea wire:model="form_data.pi_recomendaciones" rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Proponga qué acciones deberían tomarse..."></textarea>
                        @error('form_data.pi_recomendaciones')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-3 bg-red-50/50 rounded border border-red-100">
                        <label class="block text-sm font-bold text-red-700 mb-2 italic small-caps">¿Tiene Anexos /
                            Adjuntos?</label>
                        <div class="flex gap-8">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pi_has_anexo" value="Sí"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">SÍ,
                                    Posee Anexos</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pi_has_anexo" value="No"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">NO
                                    posee</span>
                            </label>
                        </div>
                        @error('form_data.pi_has_anexo')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-red-100/50 p-3 rounded border border-red-200">
                        <p class="text-[10px] font-bold text-red-800 tracking-tight">NOTA: La sección de
                            "Instrucciones"
                            se dejará en blanco para uso exclusivo de la Presidenta.</p>
                    </div>
                </div>
            @elseif($selected_doc_type === 'Punto-de-Informacion-MPPT')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos Específicos: PUNTO DE INFORMACIÓN - MPPT</h3>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO <span class="text-red-500 font-normal text-xs">(Texto que se colocará en la sección de Asunto del PDF)</span></label>
                        <textarea wire:model="form_data.pimppt_asunto" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                            placeholder="Ej. SE COLOCA LO QUE SE DESEA INFORMAR..."></textarea>
                        @error('form_data.pimppt_asunto')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ARGUMENTACIÓN</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacte la argumentación detallada que sustenta la información presentada al Ministro.</p>
                        <textarea wire:model="form_data.pimppt_argumentacion" rows="5"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Describa detalladamente los argumentos..."></textarea>
                        @error('form_data.pimppt_argumentacion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">RECOMENDACIÓN</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacte las recomendaciones pertinentes.</p>
                        <textarea wire:model="form_data.pimppt_recomendacion" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Recomendaciones..."></textarea>
                        @error('form_data.pimppt_recomendacion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-red-100/50 p-3 rounded border border-red-200">
                        <p class="text-[10px] font-bold text-red-800 tracking-tight">NOTA: Las secciones de "DECISIÓN DEL CIUDADANO MINISTRO", "COMENTARIOS DEL VICEMINISTRO" y "COMENTARIO DEL CIUDADANO MINISTRO" se dejan en blanco para uso exclusivo de las autoridades correspondientes.</p>
                    </div>
                </div>
            @elseif($selected_doc_type === 'Punto de Cuenta - Presidencia IPOSTEL')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: PUNTO DE CUENTA</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Presentado por
                                (Dirección)</label>
                            <input type="text" wire:model="form_data.pc_presentado_por"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Ej. Dirección de Tecnología">
                            @error('form_data.pc_presentado_por')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO: <span class="text-red-500 font-normal text-xs">(Texto que aparecerá en el encabezado del PDF)</span></label>
                        <input type="text" wire:model="form_data.pc_asunto_pdf"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                            placeholder="Ej. APROBACIÓN DE CONTRATACIÓN DE SERVICIOS">
                        @error('form_data.pc_asunto_pdf')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Síntesis (Justificación)</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Describa detalladamente la necesidad, el
                            problema
                            y la solución técnica propuesto.</p>
                        <textarea wire:model="form_data.pc_sintesis" rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Se señala lo que se solicita para la aprobación..."></textarea>
                        @error('form_data.pc_sintesis')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Propuesta (Acción Concreta)</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacte la petición de forma ejecutiva (Ej.
                            "...la aprobación de la contratación de...")</p>
                        <textarea wire:model="form_data.pc_propuesta" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="La aprobación de lo que se propuso en la síntesis..."></textarea>
                        @error('form_data.pc_propuesta')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-3 bg-red-50/50 rounded border border-red-100">
                        <label class="block text-sm font-bold text-red-700 mb-2 italic small-caps">¿Tiene Anexos /
                            Adjuntos?</label>
                        <div class="flex gap-8">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pc_has_anexo" value="Sí"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">SÍ,
                                    Posee Anexos</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pc_has_anexo" value="No"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">NO
                                    posee</span>
                            </label>
                        </div>
                        @error('form_data.pc_has_anexo')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-red-100/50 p-3 rounded border border-red-200">
                        <p class="text-[10px] font-bold text-red-800 tracking-tight">NOTA: Las secciones de "DECISIÓN"
                            y
                            "OBSERVACIONES" son de uso exclusivo de la Presidenta.</p>
                    </div>
                </div>
            @elseif($selected_doc_type === 'Punto-de-Cuenta-MPPT')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos Específicos: PUNTO DE CUENTA - MPPT</h3>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO <span class="text-red-500 font-normal text-xs">(Texto que se colocará en la sección de Asunto del PDF)</span></label>
                        <textarea wire:model="form_data.pcmppt_asunto" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                            placeholder="Ej. APROBACIÓN DE CONTRATACIÓN DE SERVICIOS DE MENSAJERÍA..."></textarea>
                        @error('form_data.pcmppt_asunto')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ARGUMENTACIÓN</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacte la argumentación detallada que sustenta la solicitud al Ministro.</p>
                        <textarea wire:model="form_data.pcmppt_argumentacion" rows="5"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Describa detalladamente los argumentos que sustentan esta solicitud..."></textarea>
                        @error('form_data.pcmppt_argumentacion')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">PROPUESTA</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Resuma lo que se está solicitando al ciudadano Ministro.</p>
                        <textarea wire:model="form_data.pcmppt_propuesta" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Resumen de lo solicitado..."></textarea>
                        @error('form_data.pcmppt_propuesta')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-red-100/50 p-3 rounded border border-red-200">
                        <p class="text-[10px] font-bold text-red-800 tracking-tight">NOTA: Las secciones de "DECISIÓN DEL CIUDADANO MINISTRO", "COMENTARIOS DEL VICEMINISTRO" y "COMENTARIO DEL CIUDADANO MINISTRO" se dejan en blanco para uso exclusivo de las autoridades correspondientes.</p>
                    </div>
                </div>
            @elseif($selected_doc_type === 'Punto de Cuenta - Directorio')
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1">
                        Campos
                        Específicos: PUNTO DE CUENTA AL DIRECTORIO</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Presentado por:</label>
                            <select wire:model.live="pcdPresentadoPor"
                                class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                <option value="">— Seleccionar Usuario —</option>
                                @foreach($memo_usuarios as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form_data.pcd_presentado_por')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                            @if($form_data['pcd_presentado_por'] ?? '')
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ $form_data['pcd_presentado_por'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $form_data['pcd_presentado_por_cargo'] ?? '' }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Revisado por:</label>
                            <select wire:model.live="pcdRevisadoSel"
                                class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                <option value="">— Seleccionar Usuario —</option>
                                @foreach($memo_usuarios as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                @endforeach
                            </select>
                            @if($form_data['pcd_revisado_nombre'] ?? '')
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ $form_data['pcd_revisado_nombre'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $form_data['pcd_revisado_cargo'] ?? '' }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Aprobado por:</label>
                            <select wire:model.live="pcdAprobadoSel"
                                class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                <option value="">— Seleccionar Usuario —</option>
                                @foreach($memo_usuarios as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                @endforeach
                            </select>
                            @if($form_data['pcd_aprobado_nombre'] ?? '')
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ $form_data['pcd_aprobado_nombre'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $form_data['pcd_aprobado_cargo'] ?? '' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ASUNTO: <span class="text-red-500 font-normal text-xs">(Texto que aparecerá en el encabezado del PDF)</span></label>
                        <input type="text" wire:model="form_data.pcd_asunto_pdf"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm uppercase"
                            placeholder="Ej. APROBACIÓN DE CONTRATACIÓN DE SERVICIOS">
                        @error('form_data.pcd_asunto_pdf')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Síntesis (Justificación Técnica y
                            Legal)</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacte la exposición de motivos justificando
                            que
                            el Directorio completo tome la decisión.</p>
                        <textarea wire:model="form_data.pcd_sintesis" rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="Se señala lo que se solicita para la aprobación..."></textarea>
                        @error('form_data.pcd_sintesis')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Propuesta (La Petición)</label>
                        <p class="text-[10px] text-slate-500 mb-1 italic">Redacción formal de lo que se desea aprobar.
                        </p>
                        <textarea wire:model="form_data.pcd_propuesta" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-serif"
                            placeholder="La aprobación de lo que se propuso en la síntesis..."></textarea>
                        @error('form_data.pcd_propuesta')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-3 bg-red-50/50 rounded border border-red-100">
                        <label class="block text-sm font-bold text-red-700 mb-2 italic small-caps">¿Tiene Anexos /
                            Adjuntos?</label>
                        <div class="flex gap-8">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pcd_has_anexo" value="Sí"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">SÍ,
                                    Posee Anexos</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model.live="form_data.pcd_has_anexo" value="No"
                                    class="text-red-600 focus:ring-red-500">
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-red-600 transition tracking-tight">NO
                                    posee</span>
                            </label>
                        </div>
                        @error('form_data.pcd_has_anexo')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-red-100/50 p-3 rounded border border-red-200">
                        <p class="text-[10px] font-bold text-red-800 tracking-tight">NOTA: Las secciones de MIEMBROS
                            DEL
                            DIRECTORIO, DECISIÓN y OBSERVACIONES se generan automáticamente.</p>
                    </div>
                </div>
            @elseif($selected_doc_type === 'Minuta Horizontal')
                <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 space-y-6">
                    <h3
                        class="text-sm font-black uppercase text-red-600 tracking-widest border-b border-red-100 pb-1 italic">
                        Formato
                        Oficial: MINUTA DE REUNIÓN</h3>

                    {{-- 1. Datos Generales --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Fecha de Reunión</label>
                            <input type="date" wire:model="form_data.minuta_fecha"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                            @error('form_data.minuta_fecha')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Facilitador</label>
                            <input type="text" wire:model="form_data.minuta_facilitador"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Nombre completo...">
                            @error('form_data.minuta_facilitador')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Dependencia</label>
                            <input type="text" wire:model="form_data.minuta_dependencia"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                placeholder="Unidad a la que pertenece...">
                            @error('form_data.minuta_dependencia')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. Puntos Tratados --}}
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1 uppercase">Punto(s)
                            Tratado(s) <span class="text-[10px] text-red-500 font-normal">(Obligatorio: 2 Temas)</span>
                        </p>
                        @foreach ($form_data['minuta_puntos'] as $index => $punto)
                            <div class="flex gap-3 items-center">
                                <span class="text-xs font-bold text-slate-400 w-4">{{ $index + 1 }}.</span>
                                <input type="text" wire:model="form_data.minuta_puntos.{{ $index }}"
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                    placeholder="Título o tema principal...">
                            </div>
                            @error('form_data.minuta_puntos.' . $index)
                                <span class="text-xs text-red-600 ml-7">{{ $message }}</span>
                            @enderror
                        @endforeach
                    </div>

                    {{-- 3. Participantes --}}
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1 uppercase">
                            Participantes
                            <span class="text-[10px] text-red-500 font-normal">(Obligatorio: 5 Registros)</span>
                        </p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 border-collapse">
                                <thead>
                                    <tr class="text-[10px] uppercase bg-red-100 text-red-600">
                                        <th class="px-2 py-2 border w-8">N°</th>
                                        <th class="px-2 py-2 border">Nombre</th>
                                        <th class="px-2 py-2 border">Ubicación</th>
                                        <th class="px-2 py-2 border">Correo</th>
                                        <th class="px-2 py-2 border">Teléfono</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($form_data['minuta_participantes'] as $index => $participante)
                                        <tr>
                                            <td class="px-2 py-1 border text-center font-bold">{{ $index + 1 }}
                                            </td>
                                            <td class="px-2 py-1 border"><input type="text"
                                                    wire:model="form_data.minuta_participantes.{{ $index }}.nombre"
                                                    class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                    placeholder="...">
                                            </td>
                                            <td class="px-2 py-1 border"><input type="text"
                                                    wire:model="form_data.minuta_participantes.{{ $index }}.ubicacion"
                                                    class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                    placeholder="...">
                                            </td>
                                            <td class="px-2 py-1 border"><input type="email"
                                                    wire:model="form_data.minuta_participantes.{{ $index }}.correo"
                                                    class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                    placeholder="...">
                                            </td>
                                            <td class="px-2 py-1 border"><input type="text"
                                                    wire:model="form_data.minuta_participantes.{{ $index }}.telefono"
                                                    class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                    placeholder="...">
                                            </td>
                                        </tr>
                                        @if ($errors->has("form_data.minuta_participantes.{$index}.*"))
                                            <tr>
                                                <td colspan="5" class="text-[10px] text-red-600 px-2 italic">
                                                    Complete
                                                    todos los datos del participante {{ $index + 1 }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. Planteamientos --}}
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1 uppercase">
                            Planteamientos
                            <span class="text-[10px] text-red-500 font-normal">(Obligatorio: 4 Párrafos)</span>
                        </p>
                        @foreach ($form_data['minuta_planteamientos'] as $index => $planteamiento)
                            <div class="flex gap-3">
                                <span class="text-xs font-bold text-slate-400 mt-2">{{ $index + 1 }}.</span>
                                <textarea wire:model="form_data.minuta_planteamientos.{{ $index }}" rows="2"
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                    placeholder="Resumen de ideas o situaciones..."></textarea>
                            </div>
                            @error('form_data.minuta_planteamientos.' . $index)
                                <span class="text-xs text-red-600 ml-7">{{ $message }}</span>
                            @enderror
                        @endforeach
                    </div>

                    <div class="border-t border-slate-200 pt-6 space-y-6">
                        {{-- 5. Acuerdos --}}
                        <div class="space-y-3">
                            <p
                                class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1 uppercase text-right">
                                Resultados y Compromisos: ACUERDOS <span
                                    class="text-[10px] text-red-500 font-normal">(Obligatorio:
                                    4
                                    Decisiones)</span></p>
                            @foreach ($form_data['minuta_acuerdos'] as $index => $acuerdo)
                                <div class="flex gap-3">
                                    <span class="text-xs font-bold text-slate-400 mt-2">{{ $index + 1 }}.</span>
                                    <textarea wire:model="form_data.minuta_acuerdos.{{ $index }}" rows="2"
                                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                        placeholder="Decisión final tomada..."></textarea>
                                </div>
                                @error('form_data.minuta_acuerdos.' . $index)
                                    <span class="text-xs text-red-600 ml-7">{{ $message }}</span>
                                @enderror
                            @endforeach
                        </div>

                        {{-- 6. Tareas --}}
                        <div class="space-y-3">
                            <p
                                class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1 uppercase text-right">
                                Puntos Pendientes y Tareas <span
                                    class="text-[10px] text-red-500 font-normal">(Obligatorio:
                                    4 Tareas)</span></p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 border-collapse">
                                    <thead>
                                        <tr class="text-[10px] uppercase bg-red-100 text-red-600">
                                            <th class="px-2 py-2 border w-8">N°</th>
                                            <th class="px-2 py-2 border">Tarea por Realizar</th>
                                            <th class="px-2 py-2 border w-32">Fecha Compromiso</th>
                                            <th class="px-2 py-2 border">Responsable</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($form_data['minuta_tareas'] as $index => $tarea)
                                            <tr>
                                                <td class="px-2 py-1 border text-center font-bold">{{ $index + 1 }}
                                                </td>
                                                <td class="px-2 py-1 border"><input type="text"
                                                        wire:model="form_data.minuta_tareas.{{ $index }}.tarea"
                                                        class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                        placeholder="...">
                                                </td>
                                                <td class="px-2 py-1 border"><input type="date"
                                                        wire:model="form_data.minuta_tareas.{{ $index }}.fecha"
                                                        class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500">
                                                </td>
                                                <td class="px-2 py-1 border"><input type="text"
                                                        wire:model="form_data.minuta_tareas.{{ $index }}.responsable"
                                                        class="w-full border-none p-1 text-xs focus:ring-1 focus:ring-red-500"
                                                        placeholder="...">
                                                </td>
                                            </tr>
                                            @if ($errors->has("form_data.minuta_tareas.{$index}.*"))
                                                <tr>
                                                    <td colspan="4" class="text-[10px] text-red-600 px-2 italic">
                                                        Complete todos los datos de la tarea {{ $index + 1 }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 7. Control de Cierre --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-100/50 p-4 rounded-lg">
                            {{-- Elaborado por: automático, no editable --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Elaborado por:</label>
                                <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->getRoleNames()->first() }}</p>
                                </div>
                                @error('form_data.minuta_elaborado')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Revisado por:</label>
                                <select wire:model.live="minutaRevisadoSel"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                    <option value="">— Seleccionar Usuario —</option>
                                    @foreach($memo_usuarios as $u)
                                        <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('form_data.minuta_revisado')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                                @if($form_data['minuta_revisado'] ?? '')
                                    <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                        <p class="text-sm font-semibold text-gray-800">{{ $form_data['minuta_revisado'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $form_data['minuta_revisado_cargo'] ?? '' }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Presentado a: <span class="text-red-500">*</span></label>
                                <p class="text-[11px] text-amber-700">Este usuario recibirá el comunicado una vez firmado.</p>
                                <select wire:model.live="minutaPresentadoASel"
                                    class="w-full rounded border-gray-200 text-sm focus:border-red-500 focus:ring-red-500 bg-white">
                                    <option value="">— Seleccionar Usuario —</option>
                                    @foreach($usuariosFinales as $u)
                                        <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                    @endforeach
                                </select>
                                @if($form_data['minuta_presentado_a'] ?? '')
                                    <div class="px-3 py-2 bg-white rounded border border-gray-200">
                                        <p class="text-sm font-semibold text-gray-800">{{ $form_data['minuta_presentado_a'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $form_data['minuta_presentado_a_cargo'] ?? '' }}</p>
                                    </div>
                                @endif
                                @error('form_data.minuta_presentado_a')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">¿Adjunta Anexos?</label>
                                <div class="flex gap-6 mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="form_data.minuta_anexos" value="SI"
                                            class="text-red-600 focus:ring-red-500">
                                        <span class="text-sm font-medium text-gray-700">SI</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="form_data.minuta_anexos" value="NO"
                                            class="text-red-600 focus:ring-red-500">
                                        <span class="text-sm font-medium text-gray-700">NO</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Cuerpo del Comunicado /
                        Instrucciones</label>
                    <textarea wire:model="form_data.cuerpo" rows="6"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-3 font-serif"
                        placeholder="Redacte el contenido oficial del comunicado aquí..."></textarea>
                    @error('form_data.cuerpo')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            @if (
                ($selected_doc_type !== 'Agenda al Decisor' || ($form_data['agenda_has_anexo'] ?? 'No') === 'Sí') &&
                ($selected_doc_type !== 'Punto de Información - Presidencia IPOSTEL' || ($form_data['pi_has_anexo'] ?? 'No') === 'Sí') &&
                ($selected_doc_type !== 'Punto de Cuenta - Presidencia IPOSTEL' || ($form_data['pc_has_anexo'] ?? 'No') === 'Sí') &&
                ($selected_doc_type !== 'Punto de Cuenta - Directorio' || ($form_data['pcd_has_anexo'] ?? 'No') === 'Sí')
            )
                {{-- Zona de Adjuntos / Anexos Digitales --}}
                <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-5 space-y-3">
                    <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Adjuntos / Anexos Digitales <span class="text-xs text-gray-400 font-normal">(Opcional - PDF,
                            Imágenes, Excel)</span>
                    </h4>
                    <div>
                        <input type="file" wire:model="adjuntos" multiple
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer"
                            accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                    </div>
                    @if (!empty($adjuntos))
                        <div class="space-y-1 mt-2">
                            @foreach ($adjuntos as $index => $adjunto)
                                <div
                                    class="flex items-center justify-between bg-white px-3 py-2 rounded-lg border border-gray-200 text-sm">
                                    <span class="text-slate-700 font-medium truncate">
                                        <svg class="w-4 h-4 inline text-red-500 mr-1" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        {{ $adjunto->getClientOriginalName() }}
                                    </span>
                                    <span
                                        class="text-xs text-gray-400">{{ number_format($adjunto->getSize() / 1024, 1) }}
                                        KB</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('adjuntos.*')
                        <span class="text-xs text-red-600 font-bold">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" wire:click="$set('current_view', 'dashboard')"
                    class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-bold transition">
                    Cancelar y Descartar
                </button>
                <button type="submit"
                    wire:target="save_document"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 text-white bg-primary rounded-lg hover:bg-blue-800 font-bold shadow-md transition flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                    {{ !$respuesta_a && !$selected_doc_type ? 'disabled' : '' }}>
                    {{-- Estado normal --}}
                    <span wire:loading.remove wire:target="save_document" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Generar y Enviar Comunicación
                    </span>
                    {{-- Estado enviando: evita doble envio y que se dispare con datos aun sin sincronizar --}}
                    <span wire:loading wire:target="save_document" class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Enviando…
                    </span>
                </button>
            </div>
        @endif
    </form>
</div>
