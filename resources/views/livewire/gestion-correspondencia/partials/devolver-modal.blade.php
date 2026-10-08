{{-- Modal para Devolver Comunicado --}}
@if ($show_devolver_form && $devolver_comunicado_id === ($active_doc['id'] ?? null))
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 overflow-y-auto shadow-2xl"
        x-data="{ modalOpen: true }"
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        wire:key="devolver-modal-{{ $devolver_comunicado_id }}">

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Devolver para Corregir</h3>
                </div>
                <button wire:click="cancel_devolver" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-full transition-all duration-200">
                    <svg class="w-6 h-6 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6 bg-white">

                {{-- Selector de destino: solo para Director --}}
                @if($current_role === 'Director')
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        1. Devolver a
                    </label>
                    @if($active_doc['emisor_id'] && $active_doc['emisor_id'] != $active_doc['remitente_id'])
                        <select wire:model="devolver_destino"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-500/10 text-sm font-bold p-3 transition-all">
                            <option value="emisor">{{ $active_doc['sender'] }} (quien lo remitio)</option>
                            <option value="creador">{{ $active_doc['sender_original'] }} (creador original)</option>
                        </select>
                    @else
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-sm text-slate-700 font-bold">{{ $active_doc['sender_original'] ?? $active_doc['sender'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Creador original del comunicado</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Motivo de la devolucion --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        {{ $current_role === 'Director' ? '2.' : '1.' }} Motivo de la devolucion
                    </label>
                    <textarea wire:model="observacion_devolucion"
                        rows="4"
                        placeholder="Indique que debe corregirse..."
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-500/10 text-sm font-bold p-3 transition-all placeholder:text-gray-400 resize-none"></textarea>
                    @error('observacion_devolucion')
                        <span class="text-xs text-red-600 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl shadow-inner">
                    <p class="text-[10px] text-slate-500 font-bold leading-relaxed italic flex gap-2">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>El comunicado sera devuelto para que el responsable realice las correcciones necesarias. Debe indicar el motivo de la devolucion.</span>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                <button wire:click="cancel_devolver"
                    class="px-6 py-2.5 text-sm font-black text-gray-400 hover:text-slate-700 transition-all uppercase tracking-widest">
                    Cancelar
                </button>
                <button wire:click="devolver_para_corregir"
                    class="px-8 py-3 bg-primary text-white rounded-2xl hover:bg-blue-800 transition-all shadow-lg hover:shadow-red-200 transform active:scale-95 font-black text-xs uppercase tracking-widest flex items-center gap-2 border border-red-400/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirmar Devolucion
                </button>
            </div>
        </div>
    </div>
@endif
