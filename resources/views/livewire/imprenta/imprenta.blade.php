<div>
    @section('titulo')
        Imprenta
    @endsection

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Imprenta</h1>
        <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Gestión y registro de pedidos de imprenta</p>
    </div>

    {{-- Contenedor Principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- ============================================================ --}}
            {{-- 1. DESCRIPCIÓN DEL PEDIDO --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                    <div class="w-1 h-5 bg-primary rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Descripción del Pedido</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alto (cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" min="0" wire:model="alto" placeholder="Ej: 21.0"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('alto') border-red-400 bg-red-50 @enderror">
                        @error('alto') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Ancho (cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" min="0" wire:model="ancho" placeholder="Ej: 29.7"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('ancho') border-red-400 bg-red-50 @enderror">
                        @error('ancho') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Fecha Requerida <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="fecha_entrega"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('fecha_entrega') border-red-400 bg-red-50 @enderror">
                        @error('fecha_entrega') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Imagen (PNG/JPG) <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="imagen" accept="image/png,image/jpeg"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-xl file:border-0
                            file:text-xs file:font-black file:uppercase file:tracking-widest
                            file:bg-gray-100 file:text-gray-700
                            hover:file:bg-primary hover:file:text-white
                            file:cursor-pointer file:transition
                            border border-gray-300 rounded-xl shadow-sm">
                        @error('imagen') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>
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
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">E-mail <span class="text-red-500">*</span></label>
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
                        {{-- Caja de pago (componente reutilizable) --}}
                        <div class="lg:col-span-7 border-r border-gray-200 pr-8">
                            <livewire:caja-de-pago.caja-de-pago/>
                        </div>

                        {{-- Total a pagar --}}
                        <div class="lg:col-span-5 flex flex-col justify-center space-y-4">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Total a Pagar <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="pago_total" placeholder="0,00"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-lg p-3 transition bg-white font-black text-primary"
                                    onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                    oninput="formatCurrency(this)">
                                @error('pago_total') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($pago_total)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total</span>
                                        <span class="text-lg font-black text-primary">{{ $pago_total }} Bs</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Abonado</span>
                                        <span class="text-lg font-black text-green-600">{{ number_format($monto_pagado, 2, ',', '.') }} Bs</span>
                                    </div>
                                </div>
                            @endif
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
                timer: 2000
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
</div>
