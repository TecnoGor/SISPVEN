<div>
    @section('titulo')
        Asignar Jefe de OPT
    @endsection

    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Asignar Jefe de OPT</h1>
        <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Asignación de jefes a las OPTs gestionadas desde esta COP</p>
    </div>

    {{-- Contenedor Principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">

            {{-- ============================================================ --}}
            {{-- 1. SELECCIÓN DE OPT --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                    <div class="w-1 h-5 bg-primary rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Seleccionar OPT</h2>
                </div>

                <div class="p-6">
                    <div class="max-w-xl">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">OPT <span class="text-red-500">*</span></label>
                        <select wire:model.live="opt_seleccionada"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                            <option value="">Seleccione una OPT...</option>
                            @foreach($opts as $opt)
                                <option value="{{ $opt->oficina_id }}">
                                    {{ $opt->nombre }}
                                    @if($opt->jefe_nombre) — (Jefe: {{ $opt->jefe_nombre }}) @else — (Sin jefe) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('opt_seleccionada') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Alerta si la OPT ya tiene jefe --}}
                    @if($jefe_actual)
                        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-amber-800">Esta OPT ya tiene jefe asignado</p>
                                    <p class="text-xs text-amber-700 mt-1">Jefe actual: <strong>{{ $jefe_actual->name }}</strong> ({{ $jefe_actual->cedula }})</p>
                                    <p class="text-xs text-amber-600 mt-1">Al continuar con la asignación, se le removerá el rol de Jefe de OPT.</p>
                                </div>
                            </div>
                            <button type="button" wire:click="abrirCambiarClave"
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Cambiar Clave
                            </button>
                        </div>

                        {{-- Modal cambiar contraseña --}}
                        @if($modal_cambiar_clave)
                            <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen p-4 bg-gray-950/70 backdrop-blur-md">
                                    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/20">
                                        {{-- Header --}}
                                        <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Cambiar Contraseña</h3>
                                            <button type="button" wire:click="cerrarCambiarClave" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/50 transition">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        {{-- Body --}}
                                        <div class="p-6 space-y-4">
                                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jefe actual</p>
                                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $jefe_actual->name }} — {{ $jefe_actual->cedula }}</p>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nueva Contraseña</label>
                                                <input type="password" wire:model="nueva_clave" placeholder="Dejar vacío para mantener la actual"
                                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                                @error('nueva_clave') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                                <p class="text-[10px] text-gray-400 mt-1">Mínimo 8 caracteres. Si no escribe nada se mantiene la actual.</p>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                                            <button type="button" wire:click="cambiarClave"
                                                class="bg-primary hover:bg-red-800 text-white font-black py-2.5 px-6 rounded-xl shadow-lg transition active:scale-95 uppercase tracking-widest text-xs">
                                                Guardar
                                            </button>
                                            <button type="button" wire:click="cerrarCambiarClave"
                                                class="bg-white border border-gray-200 text-gray-500 font-bold py-2.5 px-4 rounded-xl hover:bg-gray-50 transition text-xs uppercase tracking-widest">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 2. ASIGNACIÓN (solo si hay OPT seleccionada) --}}
            {{-- ============================================================ --}}
            @if($opt_seleccionada)
                <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                        <div class="w-1 h-5 bg-teal-500 rounded-full"></div>
                        <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Asignar Nuevo Jefe</h2>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Selector de modo --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tipo de Asignación</label>
                            <div class="flex gap-4">
                                <label class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-300 hover:border-primary hover:bg-red-50/50 transition cursor-pointer">
                                    <input type="radio" wire:model.live="modo" value="existente" class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                                    <span class="ml-2 text-sm font-bold text-gray-700">Usuario existente de la OPT</span>
                                </label>
                                <label class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-300 hover:border-primary hover:bg-red-50/50 transition cursor-pointer">
                                    <input type="radio" wire:model.live="modo" value="nuevo" class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                                    <span class="ml-2 text-sm font-bold text-gray-700">Crear usuario desde empleado</span>
                                </label>
                            </div>
                        </div>

                        {{-- Modo: Usuario existente --}}
                        @if($modo === 'existente')
                            <div class="max-w-xl">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Usuario <span class="text-red-500">*</span></label>
                                @if($usuariosDisponibles->isEmpty())
                                    <p class="text-sm text-gray-500 italic">No hay usuarios disponibles en esta OPT.</p>
                                @else
                                    <select wire:model.live="usuario_seleccionado"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                        <option value="">Seleccione un usuario...</option>
                                        @foreach($usuariosDisponibles as $usr)
                                            <option value="{{ $usr->id }}">{{ $usr->name }} — {{ $usr->cedula }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('usuario_seleccionado') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        {{-- Modo: Crear desde empleado --}}
                        @if($modo === 'nuevo')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Empleado <span class="text-red-500">*</span></label>
                                    @if($empleadosDisponibles->isEmpty())
                                        <p class="text-sm text-gray-500 italic">No hay empleados sin cuenta en esta OPT.</p>
                                    @else
                                        <select wire:model.live="empleado_seleccionado"
                                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                            <option value="">Seleccione un empleado...</option>
                                            @foreach($empleadosDisponibles as $emp)
                                                <option value="{{ $emp->empleado_id }}">{{ $emp->nombre }} {{ $emp->apellido }} — {{ $emp->tipo_documento }}{{ $emp->documento }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('empleado_seleccionado') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Contraseña <span class="text-red-500">*</span></label>
                                    <input type="password" wire:model="clave" placeholder="Mínimo 8 caracteres"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                    @error('clave') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- BOTÓN ASIGNAR --}}
                {{-- ============================================================ --}}
                <div class="flex justify-center pb-6">
                    <button type="button" wire:click="asignar" wire:loading.attr="disabled" wire:target="asignar"
                        class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary hover:bg-red-800 text-white font-black rounded-xl shadow-lg transform transition active:scale-95 uppercase tracking-widest text-xs">
                        <span wire:loading.remove wire:target="asignar" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Asignar Jefe
                        </span>
                        <span wire:loading wire:target="asignar" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            @endif

        </div>
    </div>
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
                timer: 2500
            });
        })

        Livewire.on('alertError', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })
    </script>
    @endscript
@endpush
