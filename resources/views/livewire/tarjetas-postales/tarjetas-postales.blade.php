<div>
    @section('titulo')
        Venta de Tarjetas Postales
    @endsection

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-10 space-y-8 pb-10">
        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tarjetas Postales</h1>
                <p class="mt-2 text-sm text-gray-500 font-medium">Gestión de ventas e inventario de material postal institucional.</p>
            </div>
            
            @if ($producto)
                <div class="flex items-center gap-4">
                    <button type="button"
                        wire:click="editar({{ $producto->producto_id }})"
                        class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-bold py-2.5 px-5 rounded-xl border-2 border-gray-100 shadow-sm transition active:scale-95 text-sm">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Añadir Stock
                    </button>
                    <div class="bg-primary/5 p-3 rounded-2xl shadow-sm border border-primary/10">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            @endif
        </div>

        {{-- MAIN FORM CARD --}}
        <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
            <form wire:submit.prevent="submit" class="divide-y divide-gray-100">
                
                {{-- SECTION 1: DETALLES DE LA VENTA --}}
                <div class="p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-indigo-100 text-indigo-700 p-2 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Detalles de Venta</h2>
                    </div>

                    <div class="max-w-xs space-y-1">
                        <label for="tarjetas_postales" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Cantidad de Tarjetas</label>
                        <div class="relative group">
                            <input type="number" step="1" min="1" max="99" id="number"
                                wire:model.live="tarjetas_postales"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-lg font-black p-4 transition bg-gray-50/50 group-hover:bg-white text-primary uppercase pr-12">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">UDS</div>
                        </div>
                        @error('tarjetas_postales') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- SECTION 2: DATOS DEL CLIENTE --}}
                <div class="p-8 space-y-6 bg-gray-50/20">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-blue-100 text-blue-700 p-2 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Información del Cliente</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label for="tipo_documento" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Tipo Documento</label>
                            <select id="tipo_documento" wire:model.lazy="tipo_documento"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                                <option value="">Seleccione...</option>
                                <option value="V">Venezolano (V)</option>
                                <option value="E">Extranjero (E)</option>
                                <option value="J">Jurídico (J)</option>
                                <option value="G">Gubernamental (G)</option>
                            </select>
                            @error('tipo_documento') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="documento" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Número Documento</label>
                            <input type="text" id="documento" wire:model.live="documento" placeholder="00.000.000"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                            @error('documento') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="nombre" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Nombre</label>
                            <input type="text" id="nombre" wire:model.lazy="nombre" placeholder="Ej. Juan"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                            @error('nombre') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="apellido" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Apellido</label>
                            <input type="text" id="apellido" wire:model.lazy="apellido" placeholder="Ej. Pérez"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                            @error('apellido') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="nro_telefono" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Teléfono Contacto</label>
                            <input type="text" id="nro_telefono" wire:model.lazy="telefono" placeholder="04121234567"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                            @error('telefono') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Correo Electrónico</label>
                            <input type="email" id="email" wire:model.lazy="correo" placeholder="cliente@correo.com"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-white">
                            @error('correo') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: PAGO Y RESUMEN --}}
                <div class="p-8 bg-gray-50/80">
                    <div class="flex flex-col lg:flex-row gap-10">
                        <div class="lg:flex-1 space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-green-100 text-green-700 p-2 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Caja y Facturación</h2>
                            </div>
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                                <livewire:caja-de-pago.caja-de-pago/>
                            </div>
                        </div>

                        {{-- TICKET SUMMARY --}}
                        <div class="w-full lg:w-[380px]">
                            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden flex flex-col relative">
                                <div class="h-2 bg-primary w-full"></div>
                                <div class="p-6 space-y-6">
                                    <div class="text-center">
                                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Resumen de Venta</h3>
                                        <div class="mt-4 flex flex-col items-center">
                                            <span class="text-4xl font-black text-gray-900">{{ number_format($total, 2, ',', '.') }}</span>
                                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Bolívares</span>
                                        </div>
                                    </div>

                                    <div class="space-y-3 pt-6 border-t border-dashed border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-bold text-gray-500">Monto Subtotal</span>
                                            <span class="text-sm font-black text-gray-800">{{ number_format($monto_servicio, 2, ',', '.') }} Bs</span>
                                        </div>
                                        <div class="flex justify-between items-center text-blue-600">
                                            <span class="text-sm font-bold opacity-80">I.V.A (16%)</span>
                                            <span class="text-sm font-black">{{ number_format($iva, 2, ',', '.') }} Bs</span>
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 mt-2 flex justify-between items-center">
                                            <span class="text-lg font-black text-gray-900">Total Venta</span>
                                            <span class="text-lg font-black text-green-600 underline decoration-green-200 decoration-4 underline-offset-4">{{ number_format($total, 2, ',', '.') }} Bs</span>
                                        </div>
                                    </div>
                                    
                                    <button type="button" wire:click="Habilitar" 
                                        class="w-full bg-primary hover:bg-red-800 text-white font-black py-4 rounded-xl shadow-lg shadow-red-200 transform transition active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2 group">
                                        <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Completar Venta
                                    </button>

                                    @if($mensaje_pago)
                                        <p class="text-center text-xs font-bold text-red-600 animate-bounce">{{ $mensaje_pago }}</p>
                                    @endif
                                </div>
                                <div class="flex justify-between px-2 pb-1">
                                    @for($i=0; $i<15; $i++)
                                        <div class="w-3 h-3 rounded-full bg-gray-100 -mb-2"></div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- STOCK MODAL --}}
    @if ($update)
        <div class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900/60 backdrop-blur-md">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden transition-all transform border border-white/20">
                    <div class="bg-gray-50/50 px-8 py-6 flex items-center justify-between border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 p-2 rounded-xl text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 tracking-tight">Stock de Tarjetas</h3>
                        </div>
                        <button type="button" wire:click="cerrar_editar" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-200/50 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-black text-primary uppercase tracking-widest">Inventario Actual</p>
                                <p class="text-4xl font-black text-gray-900 mt-1">{{ $productos_oficina->cantidad ?? 0 }}</p>
                            </div>
                            <div class="bg-white p-3 rounded-xl shadow-sm">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="cantidad_a_agregar" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Cantidad a Añadir</label>
                            <input type="number" id="cantidad_a_agregar" wire:model.defer="cantidad_a_agregar"
                                placeholder="Ej. 100"
                                class="block w-full border-2 border-gray-100 rounded-2xl shadow-sm py-4 px-5 focus:ring-primary focus:border-primary transition font-black text-lg text-gray-800">
                            @error('cantidad_a_agregar') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50/50 px-8 py-6 flex flex-row-reverse gap-4 border-t border-gray-100">
                        <button type="button" wire:click="actualizar" wire:loading.attr="disabled"
                            class="bg-primary hover:bg-red-800 text-white font-black py-4 px-8 rounded-2xl shadow-lg shadow-red-100 flex-1 transition active:scale-95 uppercase tracking-widest text-xs">
                            <span wire:loading.remove wire:target="actualizar">Confirmar Ingreso</span>
                            <span wire:loading wire:target="actualizar">Procesando...</span>
                        </button>
                        <button type="button" wire:click="cerrar_editar" 
                            class="bg-white border-2 border-gray-200 text-gray-500 font-bold py-4 px-6 rounded-2xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@script
<script>
    Livewire.on('alertSuccess', message => {
        Swal.fire({
            position: "center",
            icon: "success",
            title: message.message,
            showConfirmButton: false,
            timer: 1500,
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

    Livewire.on('servicioAceptado', () => {
        setTimeout(() => { location.reload(); }, 1500);
    });
</script>

@endscript
@endpush
