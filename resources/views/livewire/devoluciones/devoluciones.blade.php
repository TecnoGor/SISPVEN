<div>
    @section('titulo')
        Incidencias/Devoluciones
    @endsection
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-7">
        {{-- ENCABEZADO (sin icono) --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl text-primary font-bold">Incidencias/Devoluciones</h1>
            <p class="mt-1 text-sm text-gray-600">Gestión de incidencias y solicitudes de devolución por código de envío.</p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

            <form wire:submit.prevent="submit">
                {{-- ───────────── PASO 1: DATOS DEL ENVÍO ───────────── --}}
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary text-white text-xs font-bold leading-none shrink-0">1</span>
                        <h2 class="text-lg font-bold text-gray-800">Datos del Envío</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        {{-- Código de envío --}}
                        <div>
                            <label for="codigo_envio" class="block text-sm font-medium text-gray-700 mb-1.5">Código de Envío</label>
                            <input type="text" id="codigo_envio" wire:model.live="codigo_envio" placeholder="Ingrese el código y espere..."
                                class="block w-full h-11 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm px-3 transition duration-150" />
                            @error('codigo_envio') <span class="text-red-500 text-xs mt-1.5 inline-block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Motivo --}}
                        @if($envio)
                            <div>
                                <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1.5">Motivo</label>
                                <select id="motivo" wire:model.live="motivo"
                                    class="block w-full h-11 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm px-3 transition duration-150">
                                    <option value="">Seleccione un motivo</option>
                                    <option value="1">Incidencia</option>
                                    <option value="2">Devolucion</option>
                                </select>
                                @error('motivo') <span class="text-red-500 text-xs mt-1.5 inline-block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ───────────── SELECCIÓN DE INCIDENCIAS (motivo = 1) ───────────── --}}
                @if($envio && $motivo == 1)
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-2.5 mb-5">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary text-white text-xs font-bold leading-none shrink-0">2</span>
                            <h3 class="text-lg font-bold text-gray-800">Selección de Incidencias</h3>
                        </div>

                        <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 max-h-80 overflow-y-auto">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2">
                                @foreach ($incidencias as $index => $incidente)
                                    <label class="flex items-center text-sm p-2 rounded-lg hover:bg-white cursor-pointer transition duration-150">
                                        <input type="checkbox"
                                            value="{{ $incidente->incidencia_id }}"
                                            wire:model="incidencias_sel"
                                            class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary checked:bg-primary checked:border-primary shrink-0">
                                        <span class="ml-2.5 text-gray-800 font-medium">{{ $incidente->incidencia }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('incidencias_sel') <span class="text-red-500 text-xs mt-1.5 inline-block">{{ $message }}</span> @enderror
                    </div>
                @endif

                {{-- ───────────── DETALLES DEL PAQUETE ───────────── --}}
                @if($envio)
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-5">Detalles del Paquete</h3>

                        {{-- Resumen: 3 tarjetas con icono --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 transition duration-150 hover:border-primary/40 hover:shadow-sm">
                                <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-primary/10 text-primary shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Tipo de Envío</span>
                                    <span class="text-gray-800 font-medium capitalize truncate block">{{ $envio->tipo_envio ?: '—' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 transition duration-150 hover:border-primary/40 hover:shadow-sm">
                                <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-primary/10 text-primary shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Peso</span>
                                    <span class="text-gray-800 font-medium block">{{ $envio->peso }} GR</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 transition duration-150 hover:border-primary/40 hover:shadow-sm">
                                <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-primary/10 text-primary shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Origen</span>
                                    <span class="text-gray-800 font-medium truncate block">{{ $envio->oficinas->nombre ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 mb-4">
                            <span class="font-semibold text-gray-700 block mb-1">Contenido del Envío:</span>
                            <span class="text-gray-600 break-words text-sm">{{ $envio->contenido ?: '—' }}</span>
                        </div>

                        <div class="border-t border-gray-200 pt-4 mb-5">
                            <span class="font-semibold text-gray-700 block mb-1.5">Estatus Actual:</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                {{ $encaminamiento ?? '—' }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 border-t border-gray-200 pt-5 mb-4">Datos de Contacto</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            {{-- Remitente --}}
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 transition duration-200 hover:shadow-md hover:border-primary/40">
                                <div class="flex items-center gap-2.5 mb-3 pb-3 border-b border-gray-200">
                                    <span class="flex items-center justify-center h-9 w-9 rounded-full bg-primary/10 text-primary shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                    </span>
                                    <span class="font-bold text-primary">Remitente</span>
                                </div>
                                <div class="space-y-2">
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        {{ trim($envio->nombre_rem . ' ' . $envio->apellido_rem) ?: '—' }}
                                    </p>
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </svg>
                                        {{ $envio->documento_rem ?: '—' }}
                                    </p>
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        <span class="break-all">{{ $envio->correo_rem ?: '—' }}</span>
                                    </p>
                                </div>
                            </div>
                            {{-- Destinatario --}}
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 transition duration-200 hover:shadow-md hover:border-primary/40">
                                <div class="flex items-center gap-2.5 mb-3 pb-3 border-b border-gray-200">
                                    <span class="flex items-center justify-center h-9 w-9 rounded-full bg-primary/10 text-primary shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </span>
                                    <span class="font-bold text-primary">Destinatario</span>
                                </div>
                                <div class="space-y-2">
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        {{ trim($envio->nombre_dest . ' ' . $envio->apellido_dest) ?: '—' }}
                                    </p>
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                        </svg>
                                        {{ $envio->documento_dest ?: '—' }}
                                    </p>
                                    <p class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        <span class="break-all">{{ $envio->correo_dest ?: '—' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ───────────── COMENTARIOS ADICIONALES ───────────── --}}
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Comentarios Adicionales</h2>
                    <div>
                        <label for="comentario" class="block text-sm font-medium text-gray-700 mb-1.5">Descripción de la Incidencia / Motivo de devolucion</label>
                        <textarea id="comentario" wire:model.live="comentario" style="resize: none;"
                            class="h-36 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-sm p-3 transition duration-150"></textarea>

                        <div class="flex justify-between items-center mt-1.5">
                            <span class="text-xs text-gray-500">Cantidad de caracteres restantes: {{ $caracteres_restantes }}</span>
                            @error('comentario') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <x-button type="button" wire:click="submit" class="px-4 py-2 bg-primary text-white hover:bg-[#7b1f27]">
                            Iniciar Solicitud
                        </x-button>
                    </div>
                </div>
            </form>
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
                timer: 2000
            });
        })

        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 2000
            });
        })

        Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
    @endscript
@endpush
