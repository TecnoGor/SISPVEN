<div>
    @section('titulo')
        Asignar Presidente
    @endsection

    {{-- Cabecera --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">ASIGNAR PRESIDENTE</h1>
        <p class="mt-1 text-sm text-gray-600">Designación y administración del Presidente del sistema.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Card del Presidente actual --}}
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Presidente actual</h2>

            @if($presidenteActual)
                <div class="bg-gradient-to-r from-[#6b1820]/5 to-transparent rounded-xl border border-[#6b1820]/20 p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nombre</p>
                            <p class="text-base font-bold text-gray-800">{{ $presidenteActual->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Cédula</p>
                            <p class="text-base font-bold text-gray-800">{{ $presidenteActual->cedula }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Correo</p>
                            <p class="text-base text-gray-700">{{ $presidenteActual->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Oficina</p>
                            <p class="text-base text-gray-700">{{ $presidenteActual->oficina?->nombre ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3 justify-end">
                        <button type="button" wire:click="abrirCambiarClave"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm transition shadow-sm">
                            Cambiar contraseña
                        </button>
                        <button type="button" onclick="confirmarRemover('{{ addslashes($presidenteActual->name) }}')"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-sm transition shadow-sm">
                            Remover Presidente
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-gray-500 italic">No hay un Presidente asignado actualmente.</p>
                </div>
            @endif
        </div>

        {{-- Buscar y asignar nuevo Presidente --}}
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                {{ $presidenteActual ? 'Reemplazar Presidente' : 'Asignar Presidente' }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="documento_busqueda" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar por documento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="documento_busqueda" wire:model="documento_busqueda"
                        wire:keydown.enter="buscarPorDocumento"
                        class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3"
                        placeholder="Ej: 12345678">
                    @error('documento_busqueda') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <button type="button" wire:click="buscarPorDocumento"
                        wire:loading.attr="disabled" wire:target="buscarPorDocumento"
                        class="w-full px-5 py-2.5 bg-[#6b1820] hover:bg-[#7b1f27] text-white rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-50">
                        <span wire:loading.remove wire:target="buscarPorDocumento">Buscar</span>
                        <span wire:loading wire:target="buscarPorDocumento">Buscando...</span>
                    </button>
                </div>
            </div>

            {{-- Resultado de búsqueda --}}
            @if($busqueda_realizada)
                @if($usuario_encontrado)
                    <div class="mt-6 bg-green-50 rounded-xl border border-green-200 p-5">
                        <p class="text-[10px] font-black text-green-700 uppercase tracking-widest mb-2">
                            Usuario encontrado
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Nombre</p>
                                <p class="text-sm font-bold text-gray-800">{{ $usuario_encontrado->name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Cédula</p>
                                <p class="text-sm font-bold text-gray-800">{{ $usuario_encontrado->cedula }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Correo</p>
                                <p class="text-sm text-gray-700">{{ $usuario_encontrado->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Rol actual</p>
                                <p class="text-sm text-gray-700">{{ $usuario_encontrado->roles->first()?->name ?? 'Sin rol' }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" wire:click="asignar"
                                wire:loading.attr="disabled" wire:target="asignar"
                                class="px-8 py-2.5 bg-[#6b1820] hover:bg-[#7b1f27] text-white rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-50">
                                <span wire:loading.remove wire:target="asignar">Asignar como Presidente</span>
                                <span wire:loading wire:target="asignar">Procesando...</span>
                            </button>
                        </div>
                    </div>
                @elseif($empleado_encontrado)
                    <div class="mt-6 bg-amber-50 rounded-xl border border-amber-200 p-5">
                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-2">
                            Empleado sin cuenta — se creará una nueva
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Nombre</p>
                                <p class="text-sm font-bold text-gray-800">{{ $empleado_encontrado->nombre }} {{ $empleado_encontrado->apellido }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Documento</p>
                                <p class="text-sm font-bold text-gray-800">{{ $empleado_encontrado->tipo_documento }}{{ $empleado_encontrado->documento }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Correo</p>
                                <p class="text-sm text-gray-700">{{ $empleado_encontrado->correo ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Teléfono</p>
                                <p class="text-sm text-gray-700">{{ $empleado_encontrado->telefono ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="clave" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña inicial <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="clave" wire:model="clave"
                                class="block w-full md:w-1/2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3"
                                placeholder="Mínimo 8 caracteres">
                            @error('clave') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="button" wire:click="asignar"
                                wire:loading.attr="disabled" wire:target="asignar"
                                class="px-8 py-2.5 bg-[#6b1820] hover:bg-[#7b1f27] text-white rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-50">
                                <span wire:loading.remove wire:target="asignar">Crear cuenta y asignar como Presidente</span>
                                <span wire:loading wire:target="asignar">Procesando...</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-gray-50 rounded-xl border border-gray-200 p-5 text-center">
                        <p class="text-gray-500 italic">No se encontró ningún usuario o empleado con ese documento.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Modal cambiar contraseña --}}
    @if($modal_cambiar_clave)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-[#6b1820] to-[#7b1f27]">
                        <h2 class="text-white text-lg font-semibold uppercase tracking-wide text-center">
                            Cambiar Contraseña del Presidente
                        </h2>
                    </div>
                    <div class="p-6">
                        <label for="nueva_clave" class="block text-sm font-medium text-gray-700 mb-1">
                            Nueva contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="nueva_clave" wire:model="nueva_clave"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3"
                            placeholder="Mínimo 8 caracteres">
                        @error('nueva_clave') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200">
                        <x-button type="button" wire:click="cambiarClave"
                            class="px-6 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm uppercase">
                            Guardar
                        </x-button>
                        <x-button type="button" wire:click="cerrarCambiarClave"
                            class="px-6 py-2.5 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg font-medium text-sm">
                            Cancelar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
        @script
        <script>
            Livewire.on('alertSuccess', message => {
                Swal.fire({ position: 'center', icon: 'success', title: message.message, showConfirmButton: false, timer: 1800 });
            });
            Livewire.on('alertError', message => {
                Swal.fire({ position: 'center', icon: 'error', title: message.message, showConfirmButton: true, confirmButtonColor: '#6b1820' });
            });
        </script>
        @endscript

        <script>
            function confirmarRemover(nombre) {
                Swal.fire({
                    title: '¿Remover Presidente?',
                    text: '¿Seguro que deseas quitarle el rol Presidente a ' + nombre + '? Quedará sin rol asignado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6b1820',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Sí, remover',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.Livewire.dispatch('removerPresidente-confirmado');
                    }
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('removerPresidente-confirmado', () => {
                    @this.removerPresidente();
                });
            });
        </script>
    @endpush
</div>