<div>
    @section('titulo')
        Apartados Postales
    @endsection

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-10 space-y-8 pb-10">
        {{-- HEADER SECTION --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Apartados Postales</h1>
                <p class="mt-2 text-sm text-gray-500 font-medium">Gestión y asignación estratégica de apartados postales institucionales.</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-red-50 p-3 rounded-2xl shadow-sm border border-red-100">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- MAIN FORM CARD --}}
        <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
            <form wire:submit.prevent="submit" class="divide-y divide-gray-100">
                {{-- SECTION 1: DATOS DEL CLIENTE --}}
                <div class="p-8 space-y-6">
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
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50">
                                <option value="">Seleccione...</option>
                                @foreach ($documentos as $doc)
                                    <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="documento" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Número Documento</label>
                            <input type="text" id="documento" wire:model.live.debounce.300ms="documento" placeholder="00.000.000"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50">
                            @error('documento') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="nombre" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Nombre / Razón Social</label>
                            <input type="text" id="nombre" wire:model.lazy="nombre" placeholder="Ej. Juan Pérez"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50">
                            @error('nombre') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="apellido" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Apellido / Rep. Legal</label>
                            <input type="text" id="apellido" wire:model.lazy="apellido" placeholder="Ej. González"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50">
                            @error('apellido') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="nro_telefono" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">WhatsApp / Teléfono</label>
                            <input type="text" id="nro_telefono" wire:model.lazy="telefono"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50"
                                placeholder="04141234567">
                            @error('telefono') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Correo Electrónico</label>
                            <input type="email" id="email" wire:model.lazy="correo" placeholder="cliente@ejemplo.com"
                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 transition bg-gray-50/50">
                            @error('correo') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: ASIGNACIÓN DE APARTADO --}}
                <div class="p-8 space-y-6 bg-gray-50/30">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-amber-100 text-amber-700 p-2 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Espacio de Apartado</h2>
                    </div>

                    <div class="max-w-md space-y-1">
                        <label for="apartado_postal" class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Taquilla Postal Disponible</label>
                        <select id="apartado_postales" wire:model.lazy="apartado_postal"
                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3 font-bold text-gray-700 transition bg-white border-2">
                            <option value="">Seleccione número de taquilla...</option>
                            @foreach ($apartados_postales as $ap)
                                <option value="{{$ap->codigo_apartado_id}}">#{{$ap->apartado}} — Disponible</option>
                            @endforeach
                        </select>
                        @error('apartado_postal') <span class="text-red-500 text-xs font-bold pl-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- SECTION 3: PAGO Y RESUMEN --}}
                <div class="p-8 bg-gray-50/80 border-t border-gray-200">
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
                                {{-- Ticket head decoration --}}
                                <div class="h-2 bg-primary w-full"></div>
                                <div class="p-6 space-y-6">
                                    <div class="text-center">
                                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Resumen de Cargo</h3>
                                        <div class="mt-4 flex flex-col items-center">
                                            <span class="text-4xl font-black text-gray-900">{{ $total }}</span>
                                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Bolívares</span>
                                        </div>
                                    </div>

                                    <div class="space-y-3 pt-6 border-t border-dashed border-gray-200">
                                        <div class="flex justify-between items-center group">
                                            <span class="text-sm font-bold text-gray-500">Monto Base</span>
                                            <span class="text-sm font-black text-gray-800">{{ $monto_servicio }} Bs</span>
                                        </div>
                                        <div class="flex justify-between items-center group text-blue-600">
                                            <span class="text-sm font-bold opacity-80">I.V.A (16%)</span>
                                            <span class="text-sm font-black">{{ $iva }} Bs</span>
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 mt-2 flex justify-between items-center">
                                            <span class="text-lg font-black text-gray-900">Total</span>
                                            <span class="text-lg font-black text-green-600 underline decoration-green-200 decoration-4 underline-offset-4">{{ $total }} Bs</span>
                                        </div>
                                    </div>
                                    
                                    <button type="button" wire:click="Habilitar" 
                                        class="w-full bg-primary hover:bg-red-800 text-white font-black py-4 rounded-xl shadow-lg shadow-red-200 transform transition active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2 group">
                                        <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Asignar Apartado
                                    </button>
                                </div>
                                {{-- Ticket foot decoration --}}
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
                    timer: 1500,
                    background: '#ffffff',
                    customClass: {
                        title: 'text-gray-800 font-bold'
                    }
                });
            })

            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: true,
                    confirmButtonColor: '#6b1820',
                });
            })

            Livewire.on('servicioAceptado', () => {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            });
        </script>
        @endscript

    @endpush
</div>