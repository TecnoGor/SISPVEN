<div>
    @section('titulo')
        Asignar Gerente de Estado
    @endsection

    {{-- Cabecera --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">ASIGNAR GERENTE DE ESTADO</h1>
        <p class="mt-1 text-sm text-gray-600">Designación y administración de Gerentes de Estado.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Selector de estado --}}
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6">
            <label for="estado_seleccionado" class="block text-sm font-medium text-gray-700 mb-2">
                Estado <span class="text-red-500">*</span>
            </label>
            <select id="estado_seleccionado" wire:model.live="estado_seleccionado"
                class="block w-full md:w-1/2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                <option value="">Seleccione un estado...</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                @endforeach
            </select>
            @error('estado_seleccionado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            <p class="text-xs text-gray-500 mt-2">
                Distrito Capital también administra La Guaira y Miranda. Monagas también administra Delta Amacuro.
            </p>
        </div>

        @if($estado_seleccionado)
            {{-- Tabla de gerentes actuales --}}
            <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Gerentes actuales</h2>

                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cédula</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Oficina</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($gerentes as $gerente)
                                <tr wire:key="gerente-{{ $gerente->id }}" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-center text-gray-900 font-medium">{{ $gerente->name }}</td>
                                    <td class="px-6 py-3 text-center text-gray-700">{{ $gerente->cedula }}</td>
                                    <td class="px-6 py-3 text-center text-gray-700">{{ $gerente->oficina?->nombre ?? '—' }}</td>
                                    <td class="px-6 py-3 text-center text-gray-700">{{ $gerente->oficina?->estado?->nombre ?? '—' }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <button type="button" wire:click="abrirCambiarClave({{ $gerente->id }})"
                                            title="Cambiar contraseña"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                            onclick="confirmarRemover({{ $gerente->id }}, '{{ addslashes($gerente->name) }}')"
                                            title="Remover rol"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition ml-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-7 text-center text-gray-500 italic bg-gray-50">
                                        Este estado no tiene Gerentes asignados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Formulario de asignación --}}
            <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Asignar nuevo Gerente</h2>

                {{-- Selector de modo --}}
                <div class="flex gap-2 mb-6">
                    <button type="button" wire:click="$set('modo', 'existente')"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition
                        {{ $modo === 'existente' ? 'bg-[#6b1820] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Usuario existente
                    </button>
                    <button type="button" wire:click="$set('modo', 'nuevo')"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition
                        {{ $modo === 'nuevo' ? 'bg-[#6b1820] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Crear desde empleado
                    </button>
                </div>

                @if($modo === 'existente')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="usuario_seleccionado" class="block text-sm font-medium text-gray-700 mb-1">
                                Usuario <span class="text-red-500">*</span>
                            </label>
                            <select id="usuario_seleccionado" wire:model="usuario_seleccionado"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                                <option value="">Seleccione un usuario...</option>
                                @foreach($usuariosDisponibles as $usuario)
                                    <option value="{{ $usuario->id }}">
                                        {{ $usuario->name }} — {{ $usuario->cedula }}
                                    </option>
                                @endforeach
                            </select>
                            @error('usuario_seleccionado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            @if($usuariosDisponibles->isEmpty())
                                <p class="text-xs text-gray-500 mt-2">No hay usuarios disponibles para asignar en los estados cubiertos.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="empleado_seleccionado" class="block text-sm font-medium text-gray-700 mb-1">
                                Empleado <span class="text-red-500">*</span>
                            </label>
                            <select id="empleado_seleccionado" wire:model="empleado_seleccionado"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                                <option value="">Seleccione un empleado...</option>
                                @foreach($empleadosDisponibles as $empleado)
                                    <option value="{{ $empleado->empleado_id }}">
                                        {{ $empleado->nombre }} {{ $empleado->apellido }} — {{ $empleado->tipo_documento }}{{ $empleado->documento }}
                                    </option>
                                @endforeach
                            </select>
                            @error('empleado_seleccionado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            @if($empleadosDisponibles->isEmpty())
                                <p class="text-xs text-gray-500 mt-2">No hay empleados sin cuenta en los estados cubiertos.</p>
                            @endif
                        </div>

                        <div>
                            <label for="clave" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña inicial <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="clave" wire:model="clave"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3"
                                placeholder="Mínimo 8 caracteres">
                            @error('clave') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button type="button" wire:click="asignar"
                        wire:loading.attr="disabled" wire:target="asignar"
                        class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="asignar">Asignar Gerente</span>
                        <span wire:loading wire:target="asignar">Procesando...</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal cambiar contraseña --}}
    @if($modal_cambiar_clave)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-[#6b1820] to-[#7b1f27]">
                        <h2 class="text-white text-lg font-semibold uppercase tracking-wide text-center">
                            Cambiar Contraseña
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
            function confirmarRemover(userId, nombre) {
                Swal.fire({
                    title: '¿Remover rol?',
                    text: '¿Seguro que deseas quitarle el rol Gerente de Estado a ' + nombre + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6b1820',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Sí, remover',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.Livewire.dispatch('removerRol-confirmado', { userId: userId });
                    }
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('removerRol-confirmado', (event) => {
                    @this.removerRol(event.userId);
                });
            });
        </script>
    @endpush
</div>