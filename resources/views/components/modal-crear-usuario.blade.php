@props(['empleadosDisponibles', 'rolescantidad', 'rolesCount', 'rolesNombres', 'tiposDocumento'])

<form wire:submit.prevent="store">
    <x-dialog-modal wire:model="CreateForm.open">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Crear Usuario</p>
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Nuevo integrante de oficina</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            @if (session()->has('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if (session()->has('alert'))
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl mb-4 text-sm font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('alert') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Selector de modo --}}
            <div class="flex rounded-xl border border-gray-200 overflow-hidden mb-5">
                <button type="button" wire:click="$set('CreateForm.modo', 'empleado')"
                    class="flex-1 px-4 py-2.5 text-xs font-black uppercase tracking-widest transition
                        {{ $this->CreateForm->modo === 'empleado'
                            ? 'bg-primary text-white shadow-inner'
                            : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Desde Empleado
                    </span>
                </button>
                <button type="button" wire:click="$set('CreateForm.modo', 'manual')"
                    class="flex-1 px-4 py-2.5 text-xs font-black uppercase tracking-widest transition
                        {{ $this->CreateForm->modo === 'manual'
                            ? 'bg-primary text-white shadow-inner'
                            : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Manual
                    </span>
                </button>
            </div>

            <div class="space-y-5">
                @if($this->CreateForm->modo === 'empleado')
                    {{-- ====== MODO EMPLEADO ====== --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Empleado <span class="text-red-500">*</span>
                        </label>
                        <div wire:ignore x-data="{ ts: null }"
                            x-init="
                                $watch('$wire.CreateForm.open', value => {
                                    if (value && $wire.CreateForm.modo === 'empleado') {
                                        $nextTick(() => {
                                            if ($refs.selectEmpleado) {
                                                ts = new TomSelect($refs.selectEmpleado, {
                                                    onChange(value) {
                                                        @this.set('CreateForm.empleado_id', value)
                                                    }
                                                });
                                                setTimeout(() => { ts.close(); ts.blur(); }, 50);
                                            }
                                        });
                                    } else if (ts) {
                                        ts.destroy();
                                        ts = null;
                                    }
                                })
                            ">
                            <select x-ref="selectEmpleado"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-3 py-2.5 bg-white">
                                <option value="">Seleccione un empleado...</option>
                                @foreach ($empleadosDisponibles as $empleado)
                                    <option value="{{ $empleado->empleado_id }}">
                                        {{ $empleado->nombre }} {{ $empleado->apellido }} — {{ $empleado->tipo_documento }}{{ $empleado->documento }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($empleadosDisponibles->isEmpty())
                            <p class="flex items-center gap-1.5 text-[10px] text-amber-600 font-semibold mt-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                No hay empleados sin cuenta de usuario en esta oficina.
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('CreateForm.empleado_id')" class="mt-1.5" />
                    </div>
                @else
                    {{-- ====== MODO MANUAL ====== --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="CreateForm.nombre"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Nombre y Apellido"/>
                        <x-input-error :messages="$errors->get('CreateForm.nombre')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model.blur="CreateForm.email"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="correo@ejemplo.com"/>
                        <x-input-error :messages="$errors->get('CreateForm.email')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Cédula <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select wire:model.blur="CreateForm.tipo_documento"
                                class="w-20 border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-2 py-2.5 bg-white">
                                @foreach($tiposDocumento as $doc)
                                    <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model.blur="CreateForm.numero_documento" maxlength="15"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="flex-1 block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="12345678"/>
                        </div>
                        <x-input-error :messages="$errors->get('CreateForm.tipo_documento')" class="mt-1.5" />
                        <x-input-error :messages="$errors->get('CreateForm.numero_documento')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="CreateForm.telefono" maxlength="20"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Ej: 04121234567"/>
                        <x-input-error :messages="$errors->get('CreateForm.telefono')" class="mt-1.5" />
                    </div>
                @endif

                {{-- Clave (común a ambos modos) --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            id="CreateForm.clave"
                            :type="show ? 'text' : 'password'"
                            wire:model.blur="CreateForm.clave"
                            placeholder="Ingrese una contraseña"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 pr-10 bg-white"
                        />
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('CreateForm.clave')" class="mt-1.5" />
                </div>

                {{-- Rol (común a ambos modos) --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                        Rol <span class="text-red-500">*</span>
                    </label>
                    <select id="CreateForm.role_id"
                        wire:model.blur="CreateForm.role_id"
                        wire:change="validarRolSeleccionado"
                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                        <option value="">— Seleccione un rol —</option>
                        @foreach($rolescantidad as $rol)
                            @php
                                $cantidadActual = $rolesCount[$rol->rol_id] ?? 0;
                                $estaLleno = $cantidadActual >= $rol->cantidad_max;
                            @endphp
                            <option value="{{ $rol->rol_id }}" {{ $estaLleno ? 'disabled' : '' }}>
                                {{ $rolesNombres[$rol->rol_id] ?? $rol->rol_id }}
                                @if($estaLleno)
                                    — LÍMITE ALCANZADO ({{ $cantidadActual }}/{{ $rol->cantidad_max }})
                                @else
                                    ({{ $cantidadActual }}/{{ $rol->cantidad_max }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('CreateForm.role_id')" class="mt-1.5" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('CreateForm.open', false)"
                    class="px-4 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95">
                    Cancelar
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="store"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-md shadow-primary/20 transition active:scale-95 disabled:opacity-60">
                    <svg wire:loading wire:target="store" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Crear Usuario
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</form>