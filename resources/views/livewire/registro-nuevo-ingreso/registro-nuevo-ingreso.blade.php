<div>
    @section('titulo')
        Registro de Empleados
    @endsection

    {{-- Encabezado --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl font-black text-primary uppercase tracking-widest">Registro de Empleados</h1>
        <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Gestión de nuevos ingresos</p>
    </div>

    {{-- Contenedor principal --}}
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- ============================================================ --}}
            {{-- 1. DATOS PERSONALES --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                {{-- Header sección --}}
                <div class="px-6 py-4 border-b border-gray-300 flex items-center gap-3">
                    <div class="w-1 h-5 bg-primary rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Datos Personales</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombres <span class="text-red-500">*</span></label>
                        <input wire:model.live="nombre" type="text"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('nombre') border-red-400 bg-red-50 @enderror">
                        @error('nombre') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Apellidos <span class="text-red-500">*</span></label>
                        <input wire:model.live="apellido" type="text"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('apellido') border-red-400 bg-red-50 @enderror">
                        @error('apellido') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo Documento <span class="text-red-500">*</span></label>
                        <select wire:model.live="tipo_documento"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('tipo_documento') border-red-400 bg-red-50 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach ($tipo_documentos as $td)
                                <option value="{{ $td->tipo }}">{{ $td->tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Cédula / Documento <span class="text-red-500">*</span></label>
                        <input wire:model.live="documento" type="text"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('documento') border-red-400 bg-red-50 @enderror">
                        @error('documento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Correo Electrónico <span class="text-red-500">*</span></label>
                        <input wire:model.live="correo" type="email"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('correo') border-red-400 bg-red-50 @enderror">
                        @error('correo') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono Personal <span class="text-red-500">*</span></label>
                        <input wire:model.live="telefono" type="text" placeholder="04121234567"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('telefono') border-red-400 bg-red-50 @enderror">
                        @error('telefono') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono Secundario</label>
                        <input wire:model.live="telefono_2" type="text"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('telefono_2') border-red-400 bg-red-50 @enderror">
                        @error('telefono_2') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono de Emergencia</label>
                        <input wire:model.live="telefono_emergencia" type="text"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('telefono_emergencia') border-red-400 bg-red-50 @enderror">
                        @error('telefono_emergencia') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                        <input wire:model.live="fecha_nacimiento" type="date"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('fecha_nacimiento') border-red-400 bg-red-50 @enderror">
                        @error('fecha_nacimiento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Género <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-5 mt-2.5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="genero" type="radio" name="genero" value="0" class="text-primary focus:ring-primary border-gray-300">
                                <span class="text-sm text-gray-600">Femenino</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="genero" type="radio" name="genero" value="1" class="text-primary focus:ring-primary border-gray-300">
                                <span class="text-sm text-gray-600">Masculino</span>
                            </label>
                        </div>
                        @error('genero') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nacionalidad <span class="text-red-500">*</span></label>
                        <div wire:ignore x-data x-init="
                            new TomSelect($refs.selectNacionalidad, {
                                dropdownParent: 'body',
                                onChange(value) {
                                    @this.set('nacionalidad', value)
                                }
                            })
                        ">
                            <select x-ref="selectNacionalidad"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('nacionalidad') border-red-400 bg-red-50 @enderror">
                                <option value="">Seleccione...</option>
                                @foreach ($nacionalidades as $nac)
                                    <option value="{{ $nac->nacionalidad_id }}" @selected($nacionalidad == $nac->nacionalidad_id)>{{ $nac->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('nacionalidad') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado Civil <span class="text-red-500">*</span></label>
                        <select wire:model.live="estado_civil"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('estado_civil') border-red-400 bg-red-50 @enderror">
                            <option value="">Seleccione...</option>
                            @foreach ($estados_civiles as $ec)
                                <option value="{{ $ec->estado_civil_id }}">{{ $ec->nombre }}</option>
                            @endforeach
                        </select>
                        @error('estado_civil') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Fecha de Ingreso <span class="text-red-500">*</span></label>
                        <input wire:model.live="fecha_ingreso" type="date"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('fecha_ingreso') border-red-400 bg-red-50 @enderror">
                        @error('fecha_ingreso') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- -------------------------------------------------------- --}}
                {{-- Subsección: Direcciones --}}
                {{-- -------------------------------------------------------- --}}
                <div class="border-t border-gray-300 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-teal-500 rounded-full"></div>
                        <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest">Dirección(es)</h3>
                    </div>
                    <button type="button" wire:click.prevent="añadirBloqueDireccion"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-teal-700 bg-teal-50 border border-teal-200 rounded-lg hover:bg-teal-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Añadir Dirección
                    </button>
                </div>

                <div class="px-6 pb-6 space-y-3">
                    @foreach ($bloques_direccion as $index => $bloque)
                        <div class="relative bg-gray-50 border border-gray-300 rounded-xl p-4">
                            @if ($index === 0)
                                <span class="absolute top-3 left-4 text-[9px] font-black text-teal-600 uppercase tracking-widest bg-teal-50 border border-teal-200 rounded px-1.5 py-0.5">Principal</span>
                            @else
                                <span class="absolute top-3 left-4 text-[9px] font-black text-gray-400 uppercase tracking-widest bg-white border border-gray-200 rounded px-1.5 py-0.5">Secundaria</span>
                            @endif

                            @if ($index > 0)
                                <button type="button" wire:click="eliminarBloqueDireccion({{ $index }})"
                                    class="absolute top-3 right-3 p-1.5 rounded-lg text-red-300 bg-red-50 hover:bg-red-100 hover:text-red-600 transition" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado <span class="text-red-500">*</span></label>
                                    <select wire:model.live="bloques_direccion.{{ $index }}.estado_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_direccion.'.$index.'.estado_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($estados as $est)
                                            <option value="{{ $est->estado_id }}">{{ $est->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_direccion.'.$index.'.estado_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Municipio <span class="text-red-500">*</span></label>
                                    <select wire:model.live="bloques_direccion.{{ $index }}.municipio_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_direccion.'.$index.'.municipio_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($bloque['municipios'] as $mun)
                                            <option value="{{ $mun['municipio_id'] }}">{{ $mun['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_direccion.'.$index.'.municipio_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Parroquia <span class="text-red-500">*</span></label>
                                    <select wire:model.live="bloques_direccion.{{ $index }}.parroquia_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_direccion.'.$index.'.parroquia_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($bloque['parroquias'] as $parr)
                                            <option value="{{ $parr['parroquia_id'] }}">{{ $parr['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_direccion.'.$index.'.parroquia_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sector <span class="text-red-500">*</span></label>
                                    <select wire:model.live="bloques_direccion.{{ $index }}.sector_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_direccion.'.$index.'.sector_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($bloque['sectores'] as $sec)
                                            <option value="{{ $sec['sector_id'] }}">{{ $sec['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_direccion.'.$index.'.sector_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="lg:col-span-4">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Dirección Específica <span class="text-red-500">*</span></label>
                                    <textarea wire:model="bloques_direccion.{{ $index }}.direccion" rows="2"
                                        placeholder="Calle, Avenida, Urb., Casa/Apto..."
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white resize-none @error('bloques_direccion.'.$index.'.direccion') border-red-400 bg-red-50 @enderror"></textarea>
                                    @error('bloques_direccion.'.$index.'.direccion') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 2. FORMACIÓN ACADÉMICA --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-primary rounded-full"></div>
                        <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Formación Académica</h2>
                    </div>
                    <button type="button" wire:click.prevent="añadirBloqueAcademico"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-primary border border-primary/30 bg-primary/5 rounded-lg hover:bg-primary/10 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar Formación
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($bloques_academico as $index => $bloque)
                        <div class="relative bg-gray-50 border border-gray-300 rounded-xl p-4" x-data="{ otraCarrera: false, otraInstitucion: false }">

                            @if($index > 0)
                                <button type="button" wire:click.prevent="eliminarBloqueAcademico({{ $index }})"
                                    class="absolute top-3 right-3 p-1.5 rounded-lg text-red-300 bg-red-50 hover:bg-red-100 hover:text-red-600 transition" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 {{ $index > 0 ? 'pr-6' : '' }}">

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nivel Educativo <span class="text-red-500">*</span></label>
                                    <select wire:model="bloques_academico.{{ $index }}.nivel_educativo_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.nivel_educativo_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($niveles_educativos as $nivel_educativo)
                                            <option value="{{ $nivel_educativo->nivel_educativo_id }}">{{ $nivel_educativo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_academico.'.$index.'.nivel_educativo_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Carrera</label>
                                        <label class="inline-flex items-center gap-1 cursor-pointer">
                                            <input x-model="otraCarrera" type="checkbox"
                                                @change="otraCarrera ? $wire.set('bloques_academico.{{ $index }}.carrera_id', '') : $wire.set('bloques_academico.{{ $index }}.carrera_otro', '')"
                                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                            <span class="text-[10px] text-gray-400 uppercase tracking-widest font-black">¿Otra?</span>
                                        </label>
                                    </div>
                                    <div x-show="!otraCarrera" x-transition>
                                        <div wire:ignore x-data x-init="
                                            new TomSelect($refs.selectCarrera{{ $index }}, {
                                                dropdownParent: 'body',
                                                onChange(value) {
                                                    @this.set('bloques_academico.{{ $index }}.carrera_id', value)
                                                }
                                            })
                                        ">
                                            <select x-ref="selectCarrera{{ $index }}"
                                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.carrera_id') border-red-400 bg-red-50 @enderror">
                                                <option value="">Seleccione...</option>
                                                @foreach ($carreras as $carrera)
                                                    <option value="{{ $carrera->carrera_estudio_id }}" @selected($bloque['carrera_id'] == $carrera->carrera_estudio_id)>{{ $carrera->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div x-show="otraCarrera" x-transition style="display: none;">
                                        <input wire:model="bloques_academico.{{ $index }}.carrera_otro" type="text"
                                            placeholder="Especifique la carrera..."
                                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.carrera_otro') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @if($errors->has('bloques_academico.'.$index.'.carrera_id') || $errors->has('bloques_academico.'.$index.'.carrera_otro'))
                                        <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">Debe seleccionar una carrera o especificarla manualmente.</p>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Institución</label>
                                        <label class="inline-flex items-center gap-1 cursor-pointer">
                                            <input x-model="otraInstitucion" type="checkbox"
                                                @change="otraInstitucion ? $wire.set('bloques_academico.{{ $index }}.institucion_id', '') : $wire.set('bloques_academico.{{ $index }}.institucion_otro', '')"
                                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                            <span class="text-[10px] text-gray-400 uppercase tracking-widest font-black">¿Otra?</span>
                                        </label>
                                    </div>
                                    <div x-show="!otraInstitucion" x-transition>
                                        <div wire:ignore x-data x-init="
                                            new TomSelect($refs.selectInstitucion{{ $index }}, {
                                                dropdownParent: 'body',
                                                onChange(value) {
                                                    @this.set('bloques_academico.{{ $index }}.institucion_id', value)
                                                }
                                            })
                                        ">
                                            <select x-ref="selectInstitucion{{ $index }}"
                                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.institucion_id') border-red-400 bg-red-50 @enderror">
                                                <option value="">Seleccione...</option>
                                                @foreach ($instituciones as $institucion)
                                                    <option value="{{ $institucion->institucion_id }}" @selected($bloque['institucion_id'] == $institucion->institucion_id)>{{ $institucion->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div x-show="otraInstitucion" x-transition style="display: none;">
                                        <input wire:model="bloques_academico.{{ $index }}.institucion_otro" type="text"
                                            placeholder="Nombre de la institución..."
                                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.institucion_otro') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @if($errors->has('bloques_academico.'.$index.'.institucion_id') || $errors->has('bloques_academico.'.$index.'.institucion_otro'))
                                        <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">Debe seleccionar una institución o especificarla manualmente.</p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">País de Graduación</label>
                                    <div wire:ignore x-data x-init="
                                        new TomSelect($refs.selectPais{{ $index }}, {
                                            dropdownParent: 'body',
                                            onChange(value) {
                                                @this.set('bloques_academico.{{ $index }}.pais', value)
                                            }
                                        })
                                    ">
                                        <select x-ref="selectPais{{ $index }}"
                                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.pais') border-red-400 bg-red-50 @enderror">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($paises as $p)
                                                <option value="{{ $p->pais_id }}" @selected($bloque['pais'] == $p->pais_id)>{{ $p->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('bloques_academico.'.$index.'.pais') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Año de Graduación</label>
                                    <input wire:model="bloques_academico.{{ $index }}.año_graduacion" type="number"
                                        min="1950" max="{{ date('Y') }}" placeholder="Ej: 2018"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_academico.'.$index.'.año_graduacion') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_academico.'.$index.'.año_graduacion') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Título Obtenido (PDF)</label>
                                    <input wire:model="bloques_academico.{{ $index }}.titulo_obtenido" type="file" accept=".pdf"
                                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:bg-primary file:text-white hover:file:bg-primary/90 file:transition cursor-pointer border border-gray-200 rounded-xl bg-white p-1.5">
                                    @error('bloques_academico.'.$index.'.titulo_obtenido') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 3. DISCAPACIDADES DEL EMPLEADO --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-amber-500 rounded-full"></div>
                        <div>
                            <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Discapacidades del Empleado</h2>
                            <p class="text-[10px] text-gray-400 mt-0.5">Ingrese si el empleado padece alguna condición o discapacidad.</p>
                        </div>
                    </div>
                    <button type="button" wire:click.prevent="añadirBloqueDiscapacidad"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-amber-700 border border-amber-200 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Añadir
                    </button>
                </div>

                <div class="p-6 space-y-3">
                    @foreach($bloques_discapacidad as $index => $bloque)
                        <div class="relative bg-gray-50 border border-gray-300 rounded-xl p-4">
                            @if($index > 0)
                                <button type="button" wire:click.prevent="eliminarBloqueDiscapacidad({{ $index }})"
                                    class="absolute top-3 right-3 p-1.5 rounded-lg text-red-300 bg-red-50 hover:bg-red-100 hover:text-red-600 transition" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 {{ $index > 0 ? 'pr-6' : '' }}">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo de Discapacidad</label>
                                    <select wire:model="bloques_discapacidad.{{ $index }}.tipo_discapacidad_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_discapacidad.'.$index.'.tipo_discapacidad_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($discapacidades as $discapacidad)
                                            <option value="{{ $discapacidad->tipo_discapacidad_id }}">{{ $discapacidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloques_discapacidad.'.$index.'.tipo_discapacidad_id') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Descripción / Detalles</label>
                                    <input wire:model="bloques_discapacidad.{{ $index }}.descripcion" type="text"
                                        placeholder="Describa brevemente..."
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_discapacidad.'.$index.'.descripcion') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_discapacidad.'.$index.'.descripcion') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- 4. GRUPO FAMILIAR --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-5 bg-blue-500 rounded-full"></div>
                        <div>
                            <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Grupo Familiar</h2>
                            <p class="text-[10px] text-gray-400 mt-0.5">Registre padres, hijos, cónyuge, etc.</p>
                        </div>
                    </div>
                    <button type="button" wire:click.prevent="añadirBloqueGrupoFamiliar"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-700 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Añadir Familiar
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($bloques_grupo_familiar as $index => $bloque)
                        <div x-data="{ tieneDiscaFam: $wire.entangle('bloques_grupo_familiar.{{ $index }}.discapacidad'), parentesco: $wire.entangle('bloques_grupo_familiar.{{ $index }}.parentesco') }"
                            class="relative bg-gray-50 border border-gray-300 rounded-xl p-4">

                            @if($index > 0)
                                <button type="button" wire:click="eliminarBloqueGrupoFamiliar({{ $index }})"
                                    class="absolute top-3 right-3 p-1.5 rounded-lg text-red-300 bg-red-50 hover:bg-red-100 hover:text-red-600 transition" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 {{ $index > 0 ? 'pr-6' : '' }}">

                                <div class="lg:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre(s)</label>
                                    <input wire:model="bloques_grupo_familiar.{{ $index }}.nombres" type="text"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_grupo_familiar.'.$index.'.nombres') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_grupo_familiar.'.$index.'.nombres') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Apellido(s)</label>
                                    <input wire:model="bloques_grupo_familiar.{{ $index }}.apellidos" type="text"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_grupo_familiar.'.$index.'.apellidos') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_grupo_familiar.'.$index.'.apellidos') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Parentesco</label>
                                    <select wire:model="bloques_grupo_familiar.{{ $index }}.parentesco"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_grupo_familiar.'.$index.'.parentesco') border-red-400 bg-red-50 @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach ($parentescos as $parentesco)
                                            @php
                                                $estaAlLimite = in_array((string)$parentesco->parentesco_id, $parentescosAlLimite)
                                                    && (string)($bloque['parentesco'] ?? '') !== (string)$parentesco->parentesco_id;
                                            @endphp
                                            <option value="{{ $parentesco->parentesco_id }}" @disabled($estaAlLimite)>
                                                {{ $parentesco->nombre }}{{ $estaAlLimite ? ' (límite alcanzado)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bloques_grupo_familiar.'.$index.'.parentesco') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nivel Educativo</label>
                                    <select wire:model="bloques_grupo_familiar.{{ $index }}.nivel_educativo_id"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                        <option value="">Seleccione...</option>
                                        @foreach ($niveles_educativos as $nivel_educativo)
                                            <option value="{{ $nivel_educativo->nivel_educativo_id }}">{{ $nivel_educativo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo Documento</label>
                                    <select wire:model="bloques_grupo_familiar.{{ $index }}.tipo_documento"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                        <option value="">Seleccionar...</option>
                                        @foreach ($tipo_documentos as $tipo_documento)
                                            <option value="{{ $tipo_documento->tipo }}">{{ $tipo_documento->tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Documento / Cédula</label>
                                    <input wire:model="bloques_grupo_familiar.{{ $index }}.documento" type="text"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                                    <input wire:model="bloques_grupo_familiar.{{ $index }}.nacimiento" type="date"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_grupo_familiar.'.$index.'.nacimiento') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_grupo_familiar.'.$index.'.nacimiento') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                                    <input wire:model="bloques_grupo_familiar.{{ $index }}.telefono" type="text"
                                        class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-2.5 transition bg-white @error('bloques_grupo_familiar.'.$index.'.telefono') border-red-400 bg-red-50 @enderror">
                                    @error('bloques_grupo_familiar.'.$index.'.telefono') <p class="text-red-500 text-[10px] font-black uppercase tracking-tight mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Género --}}
                                <div class="flex flex-col justify-center bg-white border border-gray-200 rounded-xl p-3">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Género</label>
                                    <div class="flex items-center gap-4">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model="bloques_grupo_familiar.{{ $index }}.genero" name="genFam{{ $index }}" value="0" class="text-primary focus:ring-primary border-gray-300">
                                            <span class="text-sm text-gray-600">Femenino</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model="bloques_grupo_familiar.{{ $index }}.genero" name="genFam{{ $index }}" value="1" class="text-primary focus:ring-primary border-gray-300">
                                            <span class="text-sm text-gray-600">Masculino</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Checkboxes --}}
                                <div class="lg:col-span-3 bg-white border border-gray-200 rounded-xl p-3 flex flex-wrap items-center gap-5">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest w-full mb-0.5 -mt-0.5">Situación</label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input wire:model="bloques_grupo_familiar.{{ $index }}.trabaja" type="checkbox"
                                            class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-xs text-gray-600 uppercase tracking-widest font-semibold">Trabaja</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input wire:model="bloques_grupo_familiar.{{ $index }}.estudia" type="checkbox"
                                            class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-xs text-gray-600 uppercase tracking-widest font-semibold">Estudia</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input wire:model="bloques_grupo_familiar.{{ $index }}.vive_con_empleado" type="checkbox"
                                            class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-xs text-gray-600 uppercase tracking-widest font-semibold">Vive con el Empleado</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input wire:model="bloques_grupo_familiar.{{ $index }}.discapacidad" type="checkbox"
                                            @change="if (!$event.target.checked) {
                                                $wire.set('bloques_grupo_familiar.{{ $index }}.tipo_discapacidad', '');
                                                $wire.set('bloques_grupo_familiar.{{ $index }}.descripcion', '');
                                            }"
                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="text-xs text-red-600 uppercase tracking-widest font-black">Discapacidad</span>
                                    </label>
                                </div>

                            </div>

                            {{-- Discapacidad del familiar --}}
                            <div x-show="tieneDiscaFam" x-transition style="display: none;"
                                class="mt-4 pt-4 border-t border-red-100">
                                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-3">Detalle de Discapacidad</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo de Discapacidad</label>
                                        <select wire:model="bloques_grupo_familiar.{{ $index }}.tipo_discapacidad"
                                            class="block w-full border border-red-200 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2.5 transition bg-white">
                                            <option value="">Seleccione...</option>
                                            @foreach ($discapacidades as $discapacidad)
                                                <option value="{{ $discapacidad->tipo_discapacidad_id }}">{{ $discapacidad->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Detalles</label>
                                        <input wire:model="bloques_grupo_familiar.{{ $index }}.descripcion" type="text"
                                            placeholder="Describa brevemente..."
                                            class="block w-full border border-red-200 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm p-2.5 transition bg-white">
                                    </div>
                                </div>
                            </div>

                            {{-- Partida de nacimiento (solo hijos: parentesco == 5) --}}
                            <div x-show="parentesco == 5" x-transition style="display: none;"
                                class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Documento Requerido</p>
                                <div class="max-w-sm">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Partida de Nacimiento (PDF)</label>
                                    <input wire:model.live="bloques_grupo_familiar.{{ $index }}.partida_nacimiento" type="file" accept=".pdf"
                                        x-effect="if (parentesco != 5) { $el.value = ''; }"
                                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:bg-primary file:text-white hover:file:bg-primary/90 file:transition cursor-pointer border border-gray-200 rounded-xl bg-white p-1.5">
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- BOTONES DE ACCIÓN --}}
            {{-- ============================================================ --}}
            <div class="flex items-center justify-between pt-2 pb-6">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest"><span class="text-red-500">*</span> Campos obligatorios</p>
                <div class="flex items-center gap-3">
                    <button type="button"
                        class="inline-flex items-center px-5 py-2.5 border border-gray-200 rounded-xl text-xs font-semibold uppercase tracking-widest text-gray-600 bg-white hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-primary/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Guardar Registro
                    </button>
                </div>
            </div>

        </form>
    </div>

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
                timer: 1000
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
