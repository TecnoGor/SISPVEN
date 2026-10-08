<div>
    @section('titulo')
        Recepción de Envios Internacionales
    @endsection

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-black tracking-widest uppercase">Recepción de Envíos Internacionales</h1>
        <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">Gestión de recepción de paquetes internacionales.</p>
    </div>

    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- ============================================================ --}}
            {{-- SECCIÓN: DATOS DEL ENVÍO --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-1 h-5 bg-primary rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Datos del Envío</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Código de rastreo --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Código de Rastreo @if($tipo_envio != 14)<span class="text-red-500">*</span>@endif
                        </label>
                        <input type="text" wire:model="codigo_rastreo"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white uppercase tracking-widest"
                            placeholder="Ej: AB123456789CD"/>
                        @error('codigo_rastreo') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipo de envío --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Tipo de Envío <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="tipo_envio"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                            <option value="">— Seleccione —</option>
                            @foreach ($tipos_envios as $servicio)
                                <option value="{{ $servicio->servicio_id }}">{{ $servicio->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_envio') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Peso --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Peso Total (gr) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" inputmode="numeric" wire:model="peso"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white font-bold text-primary"
                            placeholder="0"/>
                        @error('peso') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Contenido --}}
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                            Contenido del Envío
                        </label>
                        <textarea wire:model="contenido" maxlength="300" rows="2" style="resize:none"
                            class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                            placeholder="Descripción del contenido..."></textarea>
                        @error('contenido') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lista de Correos + Certificado --}}
                    <div class="flex flex-col gap-3">
                        <label class="inline-flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 w-full hover:bg-gray-100 transition">
                            <input type="checkbox" wire:model="lista_correo"
                                class="form-checkbox h-5 w-5 text-primary border-gray-300 rounded focus:ring-primary checked:bg-primary checked:border-primary"/>
                            <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Lista de Correos</span>
                        </label>

                        {{-- Certificado (SERVICIO POSTAL UNIVERSAL INTERNACIONAL y SACAS M) --}}
                        @if($tipo_envio == 14 || $tipo_envio == 18)
                        <label class="inline-flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 w-full hover:bg-gray-100 transition">
                            <input type="checkbox" wire:model="certificado"
                                class="form-checkbox h-5 w-5 text-primary border-gray-300 rounded focus:ring-primary checked:bg-primary checked:border-primary"/>
                            <span class="text-xs font-black text-gray-700 uppercase tracking-widest">Certificado</span>
                        </label>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- SECCIÓN: REMITENTE / DESTINATARIO --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- REMITENTE --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-1 h-5 bg-gray-400 rounded-full"></div>
                        <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Remitente</h2>
                    </div>
                    <div class="p-6 space-y-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo Documento</label>
                                <select wire:model="tipo_documento"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($documentos as $doc)
                                        <option value="{{ $doc->tipo }}">{{ $doc->descripcion }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Documento</label>
                                <input type="number" wire:model="documento" maxlength="20"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Nro. documento"/>
                                @error('documento') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre</label>
                                <input type="text" wire:model="nombre" maxlength="40"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Nombre"/>
                                @error('nombre') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Apellido</label>
                                <input type="text" wire:model="apellido" maxlength="40"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Apellido"/>
                                @error('apellido') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">País</label>
                                <div wire:ignore x-data x-init="
                                    new TomSelect($refs.selectPais, {
                                        dropdownParent: 'body',
                                        onChange(value) {
                                            @this.set('pais', value)
                                        }
                                    })
                                ">
                                    <select x-ref="selectPais" class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                        <option value="">— Seleccione —</option>
                                        @foreach ($paises as $paisOpcion)
                                            <option value="{{ $paisOpcion->pais_id }}" @selected($pais == $paisOpcion->pais_id)>{{ $paisOpcion->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('pais') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado/Provincia</label>
                                <input type="text" wire:model="estado"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Estado o provincia"/>
                                @error('estado') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Ciudad</label>
                                <input type="text" wire:model="ciudad" maxlength="30"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Ciudad"/>
                                @error('ciudad') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Parroquia</label>
                                <input type="text" wire:model="parroquia" maxlength="30"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Parroquia"/>
                                @error('parroquia') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Código Postal</label>
                            <input type="text" wire:model="codigo_postal"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Código postal"/>
                            @error('codigo_postal') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Dirección</label>
                            <textarea wire:model="direccion" maxlength="150" rows="2" style="resize:none"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Dirección de habitación"></textarea>
                            @error('direccion') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                                <input type="tel" wire:model="telefono" maxlength="11"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Ej: 04121234567"/>
                                @error('telefono') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">E-mail</label>
                                <input type="email" wire:model="correo"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="correo@ejemplo.com"/>
                                @error('correo') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- DESTINATARIO --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-1 h-5 bg-primary rounded-full"></div>
                        <h2 class="text-xs font-black text-gray-700 uppercase tracking-widest">Destinatario</h2>
                    </div>
                    <div class="p-6 space-y-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipo Documento</label>
                                <select wire:model.live="tipo_documento_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($documentos as $doc)
                                        <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Documento</label>
                                <input type="text" wire:model.live="documento_dest" maxlength="20"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Nro. documento"/>
                                @error('documento_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nombre_dest" maxlength="40"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Nombre"/>
                                @error('nombre_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Apellido <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="apellido_dest" maxlength="40"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Apellido"/>
                                @error('apellido_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Estado</label>
                                <select wire:model.live="estado_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('estado_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($lista_correo == false)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Municipio</label>
                                <select wire:model.live="municipio_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($municipios as $municipio)
                                        <option value="{{ $municipio->municipio_id }}">{{ $municipio->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('municipio_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($lista_correo == true)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Oficina <span class="text-red-500">*</span></label>
                                <select wire:model.live="oficina_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($oficinas as $oficina)
                                        <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('oficina_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Ciudad</label>
                                <select wire:model.live="ciudad_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($ciudades as $ciudad)
                                        <option value="{{ $ciudad->ciudad_id }}">{{ $ciudad->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('ciudad_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($lista_correo == false)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Parroquia</label>
                                <select wire:model.live="parroquia_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                                    <option value="">— Seleccione —</option>
                                    @foreach ($parroquias as $parroquia)
                                        <option value="{{ $parroquia->parroquia_id }}">{{ $parroquia->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('parroquia_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>

                        <div class="w-1/2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Código Postal <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="codigo_postal_dest" maxlength="4" pattern="\d{4}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="0000"/>
                            @error('codigo_postal_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($lista_correo == false)
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Dirección <span class="text-red-500">*</span></label>
                            <textarea wire:model="direccion_dest" maxlength="200" rows="2" style="resize:none"
                                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                placeholder="Dirección de habitación"></textarea>
                            @error('direccion_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                                <input type="tel" wire:model.live="telefono_dest" maxlength="11"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="Ej: 04121234567"/>
                                @error('telefono_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">E-mail</label>
                                <input type="email" wire:model="correo_dest"
                                    class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white"
                                    placeholder="correo@ejemplo.com"/>
                                @error('correo_dest') <p class="text-[10px] font-black text-red-500 uppercase mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- BOTÓN REGISTRAR --}}
            {{-- ============================================================ --}}
            <div class="flex justify-end pb-4">
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="inline-flex items-center gap-2 px-8 py-3 text-sm font-black uppercase tracking-widest text-white bg-primary hover:bg-red-800 rounded-xl shadow-lg shadow-primary/20 transition active:scale-95 disabled:opacity-60">
                    <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Registrar Envío
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({ position: "center", icon: "success", title: message.message, showConfirmButton: false, timer: 1000 });
        });
        Livewire.on('alertSuccess2', message => {
            Swal.fire({ position: "center", icon: "error", title: message.message, showConfirmButton: false, timer: 3000 });
        });
        Livewire.on('alertSuccess3', message => {
            Swal.fire({ position: "center", icon: "info", title: message.message, showConfirmButton: false, timer: 10000 });
        });
        Livewire.on('envio_registrado', () => {
            setTimeout(() => { location.reload(); }, 1000);
        });
    </script>
    @endscript
@endpush
