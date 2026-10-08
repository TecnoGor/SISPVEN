{{-- Modal para Remitir Comunicado (Solo Gerente) --}}
@if ($show_remitir_modal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 overflow-y-auto shadow-2xl"
        x-data="{ modalOpen: true }" 
        x-show="modalOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100"
        wire:key="remitir-modal-{{ $remitir_comunicado_id }}">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-gray-100"
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0 text-left">
            
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-red-100 rounded-xl text-red-600 shadow-sm border border-red-200">
                        <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Remitir Comunicado</h3>
                </div>
                <button wire:click="close_remitir_modal" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-full transition-all duration-200">
                    <svg class="w-6 h-6 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6 bg-white min-h-[350px]">
                {{-- Filtro por Rol --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        1. Filtrar por Nivel de Gestión
                    </label>
                    <select wire:model.live="remitir_rol_filtro"
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-500/10 text-sm font-bold p-3 transition-all">
                        <option value="">Todos los destinatarios permitidos</option>
                        @if($current_role === 'Director')
                            <option value="Presidente Correspondencia">Presidente Correspondencia</option>
                            <option value="Director Correspondencia">Director Correspondencia</option>
                        @elseif($current_role === 'Gerente')
                            <option value="Director Correspondencia">Director Correspondencia</option>
                            <option value="Gerente Correspondencia">Gerente Correspondencia</option>
                        @endif
                    </select>
                </div>

                {{-- Buscador de Destinatario --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        2. Seleccionar Destinatario
                    </label>
                    
                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="remitir_search"
                                @focus="open = true"
                                placeholder="Escriba nombre o correo del destinatario..."
                                class="w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-600/10 focus:border-red-600 transition-all text-sm font-bold placeholder:text-gray-400 shadow-inner"
                                autocomplete="off"
                            >

                            @if ($remitir_email)
                                <button 
                                    type="button"
                                    wire:click="$set('remitir_email', ''); $set('remitir_search', ''); load_remitir_destinatarios();"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-600 transition-colors"
                                >
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Sugerencias List --}}
                        <div 
                            x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute z-[100] w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden max-h-60 overflow-y-auto custom-scrollbar ring-1 ring-black ring-opacity-5"
                            style="display: none;"
                        >
                            @if (!empty($remitir_lista))
                                @foreach ($remitir_lista as $dest)
                                    <button 
                                        type="button"
                                        wire:key="remitir-dest-{{ $dest['id'] }}"
                                        wire:click="select_remitir_destinatario({{ $dest['id'] }}, '{{ addslashes($dest['name']) }}', '{{ $dest['email'] }}')"
                                        @click="open = false"
                                        class="w-full flex items-center gap-4 p-4 hover:bg-red-50 transition-colors text-left border-b border-gray-50 last:border-0 group"
                                    >
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center text-red-700 font-black text-sm group-hover:scale-110 transition-transform shadow-sm">
                                            {{ strtoupper(substr($dest['name'], 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black text-slate-800 truncate group-hover:text-red-700 transition-colors">
                                                {{ $dest['name'] }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-gray-100 text-gray-500 uppercase tracking-widest border border-gray-200">
                                                    {{ $dest['role_name'] ?? 'Colaborador' }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 truncate italic font-medium">{{ $dest['email'] }}</span>
                                            </div>
                                        </div>
                                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-200">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            @else
                                <div class="p-8 text-center bg-gray-50/50">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-black text-gray-500 uppercase tracking-tighter">No hay resultados</p>
                                    @if($remitir_search)
                                        <p class="text-xs text-gray-400 mt-1 italic">Para "{{ $remitir_search }}"</p>
                                    @else
                                        @if($current_role === 'Director')
                                            <p class="text-xs text-gray-400 mt-1 italic font-medium">Escriba para filtrar presidente y directores...</p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1 italic font-medium">Escriba para filtrar directores y gerentes...</p>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl shadow-inner">
                    <p class="text-[10px] text-slate-500 font-bold leading-relaxed italic flex gap-2">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 
                        @if($current_role === 'Director')
                            <span>La remisión de comunicados debe ser solo hacia sus superiores (Presidente) o pares (Directores) para dar continuidad al flujo administrativo.</span>
                        @else
                            <span>La remisión de comunicados debe ser solo hacia sus superiores (Directores) o pares (Gerentes) para dar continuidad al flujo administrativo.</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                <button wire:click="close_remitir_modal"
                    class="px-6 py-2.5 text-sm font-black text-gray-400 hover:text-slate-700 transition-all uppercase tracking-widest">
                    Cancelar
                </button>
                <button wire:click="remitir_documento_final"
                    @if(!$remitir_email) disabled @endif
                    class="px-8 py-3 bg-primary text-white rounded-2xl hover:bg-blue-800 transition-all shadow-lg hover:shadow-red-200 transform active:scale-95 font-black text-xs uppercase tracking-widest flex items-center gap-2 disabled:bg-gray-200 disabled:shadow-none disabled:text-gray-400 disabled:cursor-not-allowed border border-red-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirmar Remisión
                </button>
            </div>
        </div>
    </div>
@endif
