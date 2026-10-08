{{-- Devolver para corregir --}}
<div class="flex flex-col gap-2 border-t border-gray-100 pt-3">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Devolver para corregir</p>

    @if(!$show_devolver_form || $devolver_comunicado_id !== $active_doc['id'])
        <button wire:click="open_devolver('{{ $active_doc['id'] }}')"
            class="w-fit px-4 py-2 border border-yellow-400 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition font-bold text-sm shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            Devolver para corregir
        </button>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 space-y-3">

            {{-- Selector de destino: solo para Director --}}
            @if($current_role === 'Director')
                <div>
                    <label class="text-xs font-bold text-yellow-800 block mb-2">Devolver a:</label>
                    <div class="flex flex-col gap-2">
                        @if($active_doc['emisor_id'] && $active_doc['emisor_id'] != $active_doc['remitente_id'])
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="devolver_destino" value="emisor" class="text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-slate-700">
                                    <span class="font-bold">{{ $active_doc['sender'] }}</span>
                                    <span class="text-xs text-gray-500">(quien lo remitio)</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="devolver_destino" value="creador" class="text-yellow-500 focus:ring-yellow-500">
                                <span class="text-sm text-slate-700">
                                    <span class="font-bold">{{ $active_doc['sender_original'] }}</span>
                                    <span class="text-xs text-gray-500">(creador original del comunicado)</span>
                                </span>
                            </label>
                        @else
                            <p class="text-xs text-gray-500 italic">Se devolvera a: <span class="font-bold text-slate-700">{{ $active_doc['sender_original'] }}</span></p>
                        @endif
                    </div>
                </div>
            @endif

            <div>
                <label class="text-xs font-bold text-yellow-800 block mb-1">
                    Motivo de la devolucion (obligatorio)
                </label>
                <textarea wire:model="observacion_devolucion"
                    rows="3"
                    placeholder="Indique que debe corregirse..."
                    class="w-full border-yellow-300 rounded-lg shadow-sm text-sm focus:border-yellow-500 focus:ring-yellow-500 placeholder-yellow-400"></textarea>
                @error('observacion_devolucion')
                    <span class="text-xs text-red-600 font-semibold">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="devolver_para_corregir"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-bold text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirmar devolucion
                </button>
                <button wire:click="cancel_devolver"
                    class="px-4 py-2 border border-gray-300 text-gray-600 bg-white rounded-lg hover:bg-gray-50 transition font-bold text-sm shadow-sm">
                    Cancelar
                </button>
            </div>
        </div>
    @endif
</div>
