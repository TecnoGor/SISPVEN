<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- FORMULARIO DE INGRESO DE PAGO --}}
        <div class="space-y-5 bg-white/50 p-1 rounded-2xl">
            <div class="space-y-1">
                <label for="metodos_pago" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Seleccionar Método</label>
                <div class="relative group">
                    <select id="metodos_pago" wire:model.live="metodo_pago_seleccionado" 
                        class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3.5 transition bg-white font-bold text-gray-700">
                        <option value="">— Método de pago —</option>
                        @foreach ($metodos_pago as $tipo_pago)
                            <option value="{{$tipo_pago->tipo_pago_id}}">{{$tipo_pago->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                @error('metodo_pago_seleccionado') <span class="text-red-700/80 text-[10px] font-black uppercase pl-1 tracking-tighter">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="monto" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Monto (BS)</label>
                    <input type="text" id="monto" wire:model="monto" placeholder="0,00"
                        class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3.5 transition bg-white font-black text-primary"
                        onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                        oninput="formatCurrency(this)">
                    @error('monto') <span class="text-red-700/80 text-[10px] font-black uppercase pl-1 tracking-tighter">{{ $message }}</span> @enderror
                </div>

                @if(in_array($metodo_pago_seleccionado, [3,4]))
                    <div class="space-y-1">
                        <label for="numero_referencia" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Referencia</label>
                        <input type="text" id="numero_referencia" wire:model.live="numero_referencia" placeholder="8 dígitos..."
                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3.5 transition bg-white font-bold text-gray-800">
                        @error('numero_referencia') <span class="text-red-700/80 text-[10px] font-black uppercase pl-1 tracking-tighter">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>


            <button type="button" wire:click="agregar_pago" 
                class="w-full bg-gray-900 hover:bg-black text-white font-black py-3.5 rounded-xl shadow-lg transform transition active:scale-95 uppercase tracking-widest text-[11px] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Registrar Pago
            </button>
        </div>

        {{-- LISTA DE PAGOS REALIZADOS --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Pagos Realizados</label>
                
                {{-- BALANCE PRINCIPAL (MÁS PROMINENTE) --}}
                <div class="flex items-center gap-2.5 bg-green-600 text-white px-4 py-2.5 rounded-2xl shadow-lg shadow-green-100 border-2 border-green-500/20 transition-all hover:scale-105">
                    <div class="bg-white/20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-[10px] font-black uppercase tracking-tighter opacity-80">Abonado Total</span>
                        <span class="text-base font-black tracking-tight">{{ number_format($montoPagado, 2, ',', '.') }} Bs</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50/50 rounded-2xl p-2 border border-gray-100 min-h-[180px]">

                @if(count($pagos) > 0)
                    <div class="space-y-2">
                        @foreach ($pagos as $pago)
                            <div class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm group hover:border-primary/30 transition">
                                <div class="flex items-center gap-3">
                                    <div class="bg-primary/5 p-2 rounded-lg text-primary group-hover:bg-primary group-hover:text-white transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[14px] font-black text-gray-800">{{ $pago['nombre'] }}</p>
                                        <div class="flex items-center gap-2.5 mt-1">
                                            <p class="text-[15px] font-black text-primary tracking-tight">{{ number_format($pago['monto'], 2, ',', '.') }} <span class="text-[11px] opacity-70">Bs</span></p>
                                            @if(!empty($pago['numero_referencia']))
                                                <div class="flex items-center gap-1.5 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 shadow-sm">
                                                    <span class="text-[10px] font-black text-blue-400">REF:</span>
                                                    <span class="text-[13px] font-black text-blue-700 tracking-tight">{{ $pago['numero_referencia'] }}</span>
                                                </div>
                                            @endif
                                        </div>


                                    </div>

                                </div>
                                
                                <button type="button" wire:click="eliminar_pago({{ $pago['tipo_pago_id'] }})" 
                                    class="p-2 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center py-10 opacity-30 grayscale">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-[10px] font-black uppercase tracking-widest">Sin pagos registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL PAGO MOVIL --}}
    @if($showModalPagoMovil)
        <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-white/20 transform transition-all">
                    {{-- HEADER --}}
                    <div class="bg-gray-50/80 px-8 py-6 flex items-center justify-between border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-xl text-primary">
                                <svg class="w-6 h-6 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 tracking-tight uppercase tracking-widest text-sm">Validar Pago Móvil</h3>
                        </div>
                        <button type="button" wire:click="$set('showModalPagoMovil', false)" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-200/50 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- BODY --}}
                    <div class="p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Teléfono Pagador</label>
                                <input type="text" wire:model.live="pm_telefono" placeholder="Ej: 04121234567"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50">
                                @error('pm_telefono') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Teléfono Destino</label>
                                <input type="text" wire:model.live="pm_telefono_dest" placeholder="Ej: 04147654321"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50">
                                @error('pm_telefono_dest') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Cédula Pagador</label>
                                <input type="text" wire:model.live="pm_cedula" placeholder="12345678"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50">
                                @error('pm_cedula') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Banco Origen</label>
                                <select wire:model.live="pm_banco"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50">
                                    <option value="">Seleccione banco...</option>
                                    @foreach($entidades_bancarias as $banco)
                                        <option value="{{ $banco->codigo }}">{{ $banco->codigo }} - {{ $banco->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('pm_banco') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Referencia</label>
                                <input type="text" wire:model.live="pm_referencia" placeholder="Últimos 6 u 8 dígitos"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50 text-primary">
                                @error('pm_referencia') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Fecha del Pago</label>
                                <input type="date" wire:model.live="pm_fecha"
                                    class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm p-4 font-bold transition bg-gray-50/50">
                                @error('pm_fecha') <span class="text-red-700/80 text-[10px] font-bold pl-1 uppercase tracking-tighter">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="bg-gray-50/50 px-8 py-6 flex flex-row-reverse gap-4 border-t border-gray-100">
                        <button type="button" wire:click="verificarPagoMovil" wire:loading.attr="disabled"
                            class="bg-primary hover:bg-red-800 text-white font-black py-4 px-8 rounded-2xl shadow-lg shadow-red-100 flex-1 transition active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="verificarPagoMovil" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Verificar Pago
                            </span>
                            <span wire:loading wire:target="verificarPagoMovil" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Procesando...
                            </span>
                        </button>
                        <button type="button" wire:click="$set('showModalPagoMovil', false)" 
                            class="bg-white border-2 border-gray-200 text-gray-500 font-bold py-4 px-6 rounded-2xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @pushOnce('scripts')
        <script>
            function formatCurrency(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                if (value.length === 0) {
                    input.value = '0,00';
                    return;
                }
                let cents = parseInt(value, 10);
                let bs = Math.floor(cents / 100);
                let formattedCents = (cents % 100).toString().padStart(2, '0');
                let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                input.value = `${formattedbs},${formattedCents}`;
            }
        </script>
    @endpushOnce

    @push('scripts')
        @script
        <script>
            Livewire.on('alertSuccess', message => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#ffffff',
                    customClass: { title: 'text-gray-800 font-bold' }
                });
            });

            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: true,
                    confirmButtonColor: '#6b1820',
                });
            });
        </script>
        @endscript
    @endpush

