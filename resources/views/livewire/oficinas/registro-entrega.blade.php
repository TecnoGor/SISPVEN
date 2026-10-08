<div class="min-h-screen bg-gray-50/50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="w-full max-w-6xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">

        <!-- HEADER -->
        <div class="bg-gradient-to-r from-red-700 to-red-900 px-8 py-6 relative overflow-hidden flex flex-col items-center">
            <x-return-link :href="route('ver-almacen')" class="self-start text-white/80 hover:text-white" wire:navigate.hover />
            <div class="text-center mt-2">
                <h1 class="text-3xl font-black text-white tracking-tight uppercase">Registro de Entrega</h1>
                <p class="text-red-100 mt-2 font-medium text-sm">
                    @if(count($lineas) > 1)
                        Confirmar Entrega de <span class="font-black text-white bg-white/20 px-3 py-1 rounded-lg ml-1 shadow-inner">{{ count($lineas) }} envíos</span> en un solo pago
                    @else
                        Confirmar Entrega de Envío Nro: <span class="font-black text-white bg-white/20 px-3 py-1 rounded-lg ml-1 shadow-inner">{{ $lineas[0]['codigo_envio'] ?? '—' }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="px-6 py-8 md:px-10 space-y-8 bg-gray-50/30">

            <!-- AVISO: DESTINATARIOS DISTINTOS -->
            @if($destinatarios_distintos)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
                    <div class="bg-amber-100 p-2 rounded-xl text-amber-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-amber-900 uppercase tracking-widest">Destinatarios distintos</p>
                        <p class="text-xs text-amber-800 font-medium mt-1">Los envíos seleccionados no tienen el mismo destinatario. Verifica la identidad y la autorización de quien retira antes de procesar la entrega conjunta.</p>
                    </div>
                </div>
            @endif

            <!-- LISTA DE ENVÍOS A ENTREGAR -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-2 h-6 bg-primary rounded-full"></div>
                    <h3 class="text-lg font-extrabold text-gray-800 tracking-tight">
                        Envíos a Entregar <span class="text-gray-400 font-bold text-sm">({{ count($lineas) }})</span>
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($lineas as $index => $linea)
                        <div class="p-6 hover:bg-gray-50/50 transition">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                {{-- Datos del envío --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="font-black text-gray-900 text-lg tracking-tight">{{ $linea['codigo_envio'] }}</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg {{ $linea['modo_envio'] ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                            {{ $linea['modo_envio'] ? 'Nacional' : 'Internacional' }}
                                        </span>
                                        @if($linea['carga_masiva'])
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600">Carga masiva</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 font-medium flex-wrap">
                                        <span>Dest: <span class="font-bold text-gray-700">{{ $linea['nombre_dest'] ?: '—' }}</span></span>
                                        <span>Peso: <span class="font-bold text-gray-700">{{ $linea['peso'] }} Gr</span></span>
                                        <span>Almacenaje: <span class="font-bold text-gray-700">{{ $linea['dias'] }} días</span></span>
                                        <span>Avisos: <span class="font-bold text-gray-700">{{ $linea['total_avisos'] }}</span></span>
                                        <span>Servicio: <span class="font-bold text-gray-700">{{ $linea['nombre_servicio'] }}</span></span>
                                    </div>
                                </div>

                                {{-- Total de la línea --}}
                                <div class="text-right shrink-0">
                                    @if($linea['cobrable'])
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total envío</p>
                                        <p class="text-2xl font-mono font-black text-gray-900">{{ number_format((float)$linea['total'], 2, ',', '.') }} <span class="text-xs text-gray-400">Bs</span></p>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Sin cobro
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Desglose de conceptos: solo si el envío genera algún cobro. --}}
                            @if($linea['cobrable'])
                                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                                    {{-- Almacenaje: editable en carga masiva --}}
                                    <div class="bg-gray-50 rounded-xl p-3">
                                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Almacenaje</p>
                                        @if($linea['carga_masiva'])
                                            <input type="text" wire:model.live="almacenaje_manual.{{ $linea['envio_id'] }}"
                                                class="w-full mt-1 bg-white border border-gray-200 rounded-lg focus:ring-primary focus:border-primary text-sm font-mono font-bold px-2 py-1"
                                                onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                                oninput="formatCurrency(this)">
                                        @else
                                            <p class="text-sm font-mono font-black text-gray-800 mt-1">{{ number_format((float)$linea['monto_almacenaje'], 2, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3">
                                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Avisos de L.</p>
                                        <p class="text-sm font-mono font-black text-gray-800 mt-1">{{ number_format((float)$linea['monto_aviso'], 2, ',', '.') }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3">
                                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">L. Correo</p>
                                        <p class="text-sm font-mono font-black text-gray-800 mt-1">{{ number_format((float)$linea['monto_lista_correo'], 2, ',', '.') }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-3">
                                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Costos Admin.</p>
                                        <p class="text-sm font-mono font-black text-gray-800 mt-1">{{ number_format((float)$linea['monto_costos_admin'], 2, ',', '.') }}</p>
                                    </div>

                                    {{-- Aduana: solo si el envío realmente pasó por Aduana.
                                         Ya no es decisión del operador; se deriva de que
                                         exista su paso registrado en almacen_aduana. --}}
                                    @if($linea['paso_por_aduana'])
                                        <div class="bg-gray-50 rounded-xl p-3">
                                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Aduana</p>
                                            <p class="text-sm font-mono font-black text-gray-800 mt-1">
                                                {{ number_format((float)$linea['monto_aduana'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Excedente por peso --}}
                                    @if((float)$linea['monto_tipo_envio'] > 0)
                                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                                            <p class="text-amber-600 text-[10px] font-bold uppercase tracking-widest">
                                                @if($linea['tipo_cobro_extra'] === 'nacional 2kg') Excedente (&gt;2Kg)
                                                @elseif($linea['tipo_cobro_extra'] === 'internacional 500gr') Excedente (&gt;500Gr)
                                                @else Excedente @endif
                                            </p>
                                            <p class="text-sm font-mono font-black text-amber-700 mt-1">{{ number_format((float)$linea['monto_tipo_envio'], 2, ',', '.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- GRAN TOTAL --}}
                @if(bccomp((string)$gran_total, '0', 2) > 0)
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 px-6 py-5 text-white">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-6">
                                <div>
                                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Subtotal Bruto</p>
                                    <p class="text-lg font-mono font-black text-red-400">{{ number_format((float)$gran_subtotal, 2, ',', '.') }} Bs</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">IVA (16%)</p>
                                    <p class="text-lg font-mono font-bold text-gray-300">{{ number_format((float)$gran_iva, 2, ',', '.') }} Bs</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 bg-emerald-900/40 px-5 py-3 rounded-lg border border-emerald-500/30 w-full md:w-auto justify-center">
                                <p class="text-emerald-400 text-[11px] font-black uppercase tracking-widest">Monto Neto a Pagar</p>
                                <p class="text-3xl font-mono font-black text-emerald-400">{{ number_format((float)$gran_total, 2, ',', '.') }} <span class="text-sm font-bold opacity-70">Bs</span></p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- SECCIÓN: RECEPCIÓN AUTORIZADA -->
            <div class="bg-indigo-50/40 rounded-2xl p-6 border border-indigo-100 shadow-sm transition-all relative overflow-hidden {{ $autorizado ? 'ring-2 ring-indigo-400 ring-offset-2 bg-indigo-50' : 'hover:border-indigo-300 hover:shadow-md' }}">
                @if($autorizado) <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-200 opacity-30 rounded-bl-full pointer-events-none"></div> @endif

                <label for="autorizado" class="flex items-center cursor-pointer group w-max relative z-10">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" id="autorizado" wire:model.live="autorizado" class="sr-only peer" />
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </div>
                    <span class="ml-4 text-sm font-black text-indigo-900 uppercase tracking-widest group-hover:text-indigo-700 transition">Retiro por Tercero (Persona Autorizada)</span>
                </label>

                @if ($autorizado)
                    <div class="mt-6 pt-6 border-t border-indigo-200/50 grid grid-cols-1 sm:grid-cols-3 gap-6 relative z-10">
                        <div class="space-y-1.5">
                            <label for="nombre" class="block text-[10px] font-black text-indigo-800/80 uppercase tracking-widest pl-1">Nombre Completo</label>
                            <input type="text" id="nombre" wire:model="nombre"
                                class="w-full bg-white border-indigo-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-3.5 font-bold text-gray-800 transition placeholder-gray-300" placeholder="Ej: Perez Maria" />
                        </div>
                        <div class="space-y-1.5">
                            <label for="tipo_documento" class="block text-[10px] font-black text-indigo-800/80 uppercase tracking-widest pl-1">Tipo de Identificación</label>
                            <select id="tipo_documento" wire:model.live="tipo_documento"
                                class="w-full bg-white border-indigo-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-3.5 font-bold text-gray-800 transition">
                                <option value="">Seleccionar:</option>
                                @foreach ($documentos as $doc)
                                    <option value="{{ $doc->documento_id }}">{{ $doc->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label for="cedula" class="block text-[10px] font-black text-indigo-800/80 uppercase tracking-widest pl-1">Nro. de Documento</label>
                            <input type="text" id="cedula" wire:model="cedula"
                                class="w-full bg-white border-indigo-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-3.5 font-bold text-gray-800 transition placeholder-gray-300" placeholder="Ej: 12345678" />
                        </div>
                    </div>
                @endif
            </div>

            @if(bccomp((string)$gran_total, '0', 2) > 0)
            <!-- CAJA DE PAGO -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 relative">
                <div class="flex justify-between items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-6 bg-primary rounded-full"></div>
                        <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Registro de Pago</h3>
                    </div>
                    <div class="hidden sm:block text-[10px] uppercase font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                        Total Factura: {{ number_format((float)$gran_total, 2, ',', '.') }} Bs
                    </div>
                </div>

                <div class="w-full">
                    <livewire:caja-de-pago.caja-de-pago/>
                </div>
            </div>
            @endif

            <!-- FOOTER DE ACCIONES -->
            <div class="bg-white border-2 border-gray-100/50 shadow-lg rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-center gap-4 mt-8">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider text-center sm:text-left">
                    @if(bccomp((string)$gran_total, '0', 2) > 0)
                        <span class="block">Verifique los montos cancelados antes de asentar.</span>
                        <span class="text-red-500/80">Requisito para entrega: Pago completo.</span>
                    @else
                        <span class="block">Estos envíos no generan cobro al destinatario.</span>
                        <span class="text-emerald-700">Puedes proceder directamente con la entrega.</span>
                    @endif
                </div>

                <button type="button" wire:click="store" wire:loading.attr="disabled" wire:target="store" @if(bccomp((string)$gran_total, '0', 2) > 0 && intval($monto_pagado) < intval($gran_total)) disabled @endif class="w-full sm:w-auto px-10 py-4 bg-emerald-700 text-white rounded-xl shadow-xl shadow-emerald-500/50 hover:bg-emerald-700 transition-all transform active:scale-95 font-black text-xs uppercase tracking-widest disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="store" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @if(bccomp((string)$gran_total, '0', 2) > 0) Procesar Entrega Definitiva @else Entregar @endif
                    </span>
                    <span wire:loading wire:target="store" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Asentando Registro...
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
        <script>
            // success alert
            Livewire.on('alertSuccess', message => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 3500,
                    customClass: { title: 'text-gray-800 font-black' }
                });
            })

            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: true,
                    confirmButtonColor: '#6b1820',
                    customClass: { title: 'text-gray-800 font-bold' }
                });
            })
        </script>
    @endscript

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
@endpush