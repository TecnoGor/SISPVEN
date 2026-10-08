<div>
    @section('titulo')
        Filatelia
    @endsection

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Filatelia</h1>
        <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Gestión y registro de compras de filatelia</p>
    </div>

    {{-- Contenedor Principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- ============================================================ --}}
            {{-- 1. DESCRIPCIÓN DEL PEDIDO --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-primary rounded-full"></div>
                        <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Descripción del Pedido</h2>
                    </div>
                    <button type="button" wire:click="agregar_pedido"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-primary bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar Línea
                    </button>
                </div>

                <div class="p-6 space-y-3">
                    {{-- Etiquetas de columna --}}
                    <div class="hidden md:grid grid-cols-6 gap-4 px-2">
                        <div class="col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Serie</div>
                        <div class="col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Sello</div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Cantidad</div>
                        <div></div>
                    </div>

                    @foreach($pedidos as $index => $pedido)
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-center bg-gray-50 p-3 rounded-xl border border-gray-300 transition hover:shadow-sm"
                            wire:key="pedido-field-{{ $index }}">

                            {{-- Serie --}}
                            <div class="col-span-2">
                                <label class="block md:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Serie</label>
                                <select class="w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white"
                                        wire:model.live="pedidos.{{ $index }}.serie">
                                    <option value="">Seleccione serie</option>
                                    @foreach($series as $serie)
                                        <option value="{{ $serie->serie_filatelia_id }}">{{ $serie->nombre }}</option>
                                    @endforeach
                                </select>
                                @error("pedidos.$index.serie")
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sello --}}
                            <div class="col-span-2">
                                <label class="block md:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sello</label>
                                <select class="w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white disabled:opacity-50"
                                        wire:model.live="pedidos.{{ $index }}.sello"
                                        {{ empty($pedido['serie']) ? 'disabled' : '' }}>
                                    <option value="">Seleccione sello</option>
                                    @foreach($sellos->where('serie_filatelia_id', $pedido['serie'] ?? null) as $sello)
                                        <option value="{{ $sello->sello_id }}">{{ $sello->nombre }}</option>
                                    @endforeach
                                </select>
                                @error("pedidos.$index.sello")
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cantidad --}}
                            <div>
                                <label class="block md:hidden text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Cantidad</label>
                                <input type="number" min="1" placeholder="0"
                                    class="w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 text-center transition bg-white"
                                    wire:model.live="pedidos.{{ $index }}.cantidad">
                                @error("pedidos.$index.cantidad")
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Eliminar --}}
                            <div class="flex justify-end md:justify-center">
                                <button type="button" title="Eliminar línea"
                                    class="p-2 text-red-300 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-600 transition"
                                    wire:click="eliminar_pedido({{ $index }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 2. DATOS DEL CLIENTE --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                    <div class="w-1 h-5 bg-teal-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Datos del Cliente</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo Documento <span class="text-red-500">*</span></label>
                        <select wire:model="tipo_documento"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('tipo_documento') border-red-400 bg-red-50 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach ($tipos_documentos as $doc)
                                <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nro Documento <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="documento"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('documento') border-red-400 bg-red-50 @enderror">
                        @error('documento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nombre"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('nombre') border-red-400 bg-red-50 @enderror">
                        @error('nombre') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="apellido"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('apellido') border-red-400 bg-red-50 @enderror">
                        @error('apellido') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="telefono"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('telefono') border-red-400 bg-red-50 @enderror">
                        @error('telefono') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Correo <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="correo"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('correo') border-red-400 bg-red-50 @enderror">
                        @error('correo') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 3. PAGO --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                    <div class="w-1 h-5 bg-amber-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Pago</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        {{-- Caja de pago --}}
                        <div class="lg:col-span-7 border-r border-gray-200 pr-8">
                            <livewire:caja-de-pago.caja-de-pago/>
                        </div>

                        {{-- Resumen --}}
                        <div class="lg:col-span-5 flex flex-col justify-center space-y-4">
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 space-y-3">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Resumen</p>

                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Subtotal</span>
                                    <span class="text-lg font-black text-gray-700">{{ number_format($pago_fil, 2, ',', '.') }} Bs</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">IVA (16%)</span>
                                    <span class="text-lg font-black text-gray-700">{{ number_format($iva, 2, ',', '.') }} Bs</span>
                                </div>

                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Total</span>
                                    <span class="text-xl font-black text-primary">{{ number_format($pago_total, 2, ',', '.') }} Bs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- BOTÓN GENERAR --}}
            {{-- ============================================================ --}}
            <div class="flex justify-center pb-6">
                <button type="button" wire:click="verificar_pago"
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary hover:bg-red-800 text-white font-black rounded-xl shadow-lg transform transition active:scale-95 uppercase tracking-widest text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Generar Registro
                </button>
            </div>

        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })

        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })

        Livewire.on('recargarPagina', () => {
            window.location.reload();
        });
    </script>
    @endscript
@endpush
</div>
