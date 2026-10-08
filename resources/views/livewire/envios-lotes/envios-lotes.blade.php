<div>
    @section('titulo')
        Crear Envíos
    @endsection
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                @if ($envio_seleccionado === 'nacional')
                    <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight">Envíos Nacionales</h1>
                @else
                    <h1 class="text-3xl md:text-4xl text-primary font-bold tracking-tight">Envíos Internacionales</h1>
                @endif
            </div>
        </div>
        <br>

        <!-- Botones tipo envio -->
        <div class="px-6 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="radio" wire:model.live="envio_seleccionado" value="nacional" checked
                    class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-primary">Nacional</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="radio" wire:model.live="envio_seleccionado" value="internacional"
                    class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-primary">Internacional</span>
            </label>
        </div>


        <div
            style="background-color: white;border-radius: 8px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 3px;/">

            <form wire:submit.prevent="submit" class="space-y-4">

                <div class="mb-2 sm:mb-0">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                        Datos del Envio
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Tipos de Envios -->
                    <div class="">
                        <label for="servicio_id" class="block text-sm font-medium text-gray-700">Tipos de Envíos</label>
                        <select id="servicio_id" wire:model.live="servicioss"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Seleccione un envio</option>
                            @foreach ($servicios_filtrados as $servicio)
                                <option value="{{ $servicio['servicio_id'] }}">
                                    {{ $servicio['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('servicio_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @foreach ($this->ObtenerInputs() as $input)
                        @if ($input === 'apartado')
                            <div>
                                <div class="">
                                    <label for="recoleccion"
                                        class="block text-sm font-medium text-gray-700 mb-2">Apartado Postal</label>
                                    <input type="checkbox" name="apartado" id="apartado" wire:model.live="apartado"
                                        class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                                </div>
                            </div>
                        @elseif ($input === 'subservicios')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad de
                                    Servicio:</label>
                                <div
                                    class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($subservicios as $index => $subservicio)
                                        {{-- Los servicios nacionales no ofrecen "Certificado" (concepto 14). --}}
                                        @continue($subservicio->tarifa_conceptos_id == 14)
                                        <label class="flex items-center mb-2"
                                            wire:key="subservicio-{{ $subservicio->tarifa_conceptos_id }}">
                                            <input type="checkbox" value="{{ $subservicio->tarifa_conceptos_id }}"
                                                wire:model.live="subservicio_encon"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700"
                                                {{ in_array($subservicio->exclusion, $subservicio_encon) ? 'disabled' : '' }}
                                                @if ($subservicio->tarifa_conceptos_id == 20 && $peso <= 2000) disabled @endif>
                                            <span class="ml-2 text-gray-800">{{ $subservicio->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('subservicio_encon')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                        @elseif ($input === 'insumos')
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Insumos</label>
                                <div
                                    class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($inventarios as $inventario)
                                        <label class="flex items-center mb-2"
                                            wire:key="insumo-{{ $inventario->insumo_id }}">
                                            {{-- El disponible descuenta lo ya comprometido por los envíos del lote --}}
                                            <input type="checkbox" value="{{ $inventario->insumo_id }}"
                                                wire:model.live="insumo_sel"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled($inventario->disponible == 0 && !in_array($inventario->insumo_id, $insumo_sel))>

                                            <span class="ml-2 text-gray-800">
                                                {{ $inventario->insumo->descripcion }}
                                                @if ($inventario->disponible == 0)
                                                    <span class="text-red-500">(Sin existencias)</span>
                                                @else
                                                    <span class="text-green-700">({{ $inventario->disponible }}
                                                        disponibles)</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('insumo_sel')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @elseif ($input === 'subservicios_inter')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad de
                                    Servicio:</label>
                                <div
                                    class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($subservicios_inter as $index => $subservicio)
                                        {{-- El "Certificado" (concepto 1) se agrega automáticamente
                                            según el servicio; no se ofrece como opción manual. --}}
                                        @continue($subservicio->tarifa_conceptos_internacional_id == 1)
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox"
                                                value="{{ $subservicio->tarifa_conceptos_internacional_id }}"
                                                wire:model.live="subservicio_inter_encon"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700"
                                                {{ in_array($subservicio->exclusion, $subservicio_encon) ? 'disabled' : '' }}>
                                            <span class="ml-2 text-gray-800">{{ $subservicio->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('subservicio_inter_encon')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @elseif ($input === 'codigo')
                            @if ($envio_seleccionado == 'internacional')
                                <div class="">
                                    <label for="codigo_rastreo" class="block text-sm font-medium text-gray-700"> Código
                                        de Rastreo <span class="text-red-500">*</span></label>
                                    <input type="text" id="codigo_rastreo" wire:model.live='codigo_envio_creado'
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm
                                    [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [appearance:textfield]" />
                                    @error('codigo')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @elseif ($input === 'recoleccion')
                            <div class="">
                                <label for="recoleccion"
                                    class="block text-sm font-medium text-gray-700 mb-2">Recoleccion a Domicilio</label>
                                <input type="checkbox" name="recoleccion" id="recoleccion" wire:model.live="recoleccion"
                                    class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                            </div>
                        @elseif ($input === 'cliente')
                            <div class="">
                                <label for="recoleccion" class="block text-sm font-medium text-gray-700 mb-2">Cliente
                                    Corporativo</label>
                                <input type="checkbox" name="corporativo" id="corporativo" wire:model.live="corporativo"
                                    class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                            </div>
                        @elseif($input === 'peso')
                            <!-- Peso -->
                            <div class="md:w-1/2">
                                <label for="peso" class="block text-sm font-medium text-gray-700">
                                    Peso ({{ $servicioss == 8 ? 'kilogramos' : 'gramos' }})
                                    <span class="text-red-500">*</span></label>
                                <input type="number" id="peso" wire:model.live.debounce.500ms='peso'
                                    oninput="this.value = this.value < 0.01 ? '' : this.value" placeholder="0.00"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm
                                [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [appearance:textfield]" />
                                @error('peso')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @elseif($input === 'contenido')
                            <!-- Contenido del Envio -->
                            <div class="">
                                <label for="contenido" class="block text-sm font-medium text-gray-700">Contenido del
                                    Envio<span class="text-red-500">*</span></label>
                                <textarea type="text" style="resize: none;" wire:model="contenido" maxlength="300" id="contenido"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"> </textarea>
                                @error('contenido')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    @endforeach
                </div>

                <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                @if (!empty($servicioss))

                    <div class="mb-2 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                            Datos del Remitente
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                        @foreach ($this->ObtenerInputs() as $input)
                            <!-- Nombre -->
                            @if ($input === 'nombre_rem')
                                <div class="">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre/Razón
                                        Soc.<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre" wire:model="nombre_rem"
                                        @if ($corporativo) disabled @endif maxlength="40"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Apellido -->
                            @elseif ($input === 'apellido_rem')
                                <div class="">
                                    <label for="apellido"
                                        class="block text-sm font-medium text-gray-700">Apellido<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="apellido" wire:model="apellido_rem"
                                        @if ($corporativo) disabled @endif maxlength="25"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('apellido_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Tipo de Documento -->
                            @elseif ($input === 'tipo_documento_rem')
                                <div class="">
                                    <label for="tipo_documento_id"
                                        class="block text-sm font-medium text-gray-700">Documento<span
                                            class="text-red-500">*</span></label>
                                    <select id="tipo_documento_id" wire:model.live="tipo_documento_rem"
                                        @if ($corporativo) disabled @endif
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="">Seleccionar</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Numero de Documento -->
                            @elseif ($input === 'documento_rem')
                                <div class="">
                                    <label for="documento" class="block text-sm font-medium text-gray-700">Nro.
                                        Documento<span class="text-red-500">*</span></label>
                                    <input type="text" id="documento"
                                        wire:model.live.debounce.300ms="documento_rem"
                                        @if ($corporativo) disabled @endif
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('documento_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                            @elseif ($input === 'doc_autorizado')
                                <div class="">
                                    <label for="doc_autorizado" class="block text-sm font-medium text-gray-700">Doc.
                                        Autorizado<span class="text-red-500">*</span></label>
                                    <input type="text" id="doc_autorizado"
                                        wire:model.live.debounce.500ms="doc_autorizado"
                                        @if (!$cliente_corp) disabled @endif
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('doc_autorizado')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'nombre_autorizado')
                                <div class="">
                                    <label for="nombre_autorizado"
                                        class="block text-sm font-medium text-gray-700">Autorizado<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="nombre_autorizado"
                                        wire:model.live.debounce.300ms="nombre_autorizado"
                                        @if ($corporativo) disabled @endif
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_autorizado')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Estados -->
                            @elseif ($input === 'estadoss')
                                <div class="">
                                    <label for="estado_id" class="block text-sm font-medium text-gray-700">Estado<span
                                            class="text-red-500">*</span></label>
                                    <select id="estado_id" wire:model.live="estadoss"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un estado</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estadoss')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Municipio -->
                            @elseif ($input === 'municipioss')
                                <div class="">
                                    <label for="municipio_id"
                                        class="block text-sm font-medium text-gray-700">Municipio<span
                                            class="text-red-500">*</span></label>
                                    <select id="municipio_id" wire:model.live="municipioss"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un municipio</option>
                                        @foreach ($municipios as $municipio)
                                            <option value="{{ $municipio->municipio_id }}"
                                                @if ($municipio->municipio_id == $municipioss) selected @endif>
                                                {{ $municipio->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipioss')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'parroquiass')
                                <!-- Parroquias -->
                                <div class="">
                                    <label for="parroquia_id"
                                        class="block text-sm font-medium text-gray-700">Parroquia<span
                                            class="text-red-500">*</span></label>
                                    <select id="parroquia_id" wire:model.live="parroquiass"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Parroquia</option>
                                        @foreach ($parroquias as $parroquia)
                                            <option value="{{ $parroquia->parroquia_id }}"
                                                @if ($parroquia->parroquia_id == $parroquiass) selected @endif>
                                                {{ $parroquia->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquiass')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'ciudades_rem')
                                <!-- Municipio -->
                                <div class="">
                                    <label for="ciudades_rem"
                                        class="block text-sm font-medium text-gray-700">Ciudad<span
                                            class="text-red-500">*</span></label>
                                    <select id="ciudades_rem" wire:model.live="ciudades_rem"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Ciudad</option>
                                        @foreach ($ciudades as $ciudad)
                                            <option value="{{ $ciudad->ciudad_id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudades_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'codigo_postal_rem')
                                <!-- Codigo Postal -->
                                <div class="">
                                    <label for="codigo_postal_rem"
                                        class="block text-sm font-medium text-gray-700">Código Postal<span
                                            class="text-red-500">*</span></label>
                                    <select id="codigo_postal_rem" wire:model.live="codigo_postal_rem"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="">Seleccionar Código Postal</option>
                                        @foreach ($codigos_postales_rem as $codigo_postal)
                                            <option value="{{ $codigo_postal }}"
                                                @if ($codigo_postal == $codigo_postal_rem) selected @endif>
                                                {{ $codigo_postal }}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'direccion_rem')
                                <!-- Direccion -->
                                <div class=" md:col-span-2">
                                    <label for="direccion"
                                        class="block text-sm font-medium text-gray-700">Dirección<span
                                            class="text-red-500">*</span></label>
                                    <textarea wire:model="direccion_rem" id="direccion" style="resize: none;" maxlength="150"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                    @error('direccion_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'tlf_rem')
                                <!-- Telefono -->
                                <div class="">
                                    <label for="tlf" class="block text-sm font-medium text-gray-700">Nro.
                                        Teléfono<span class="text-red-500">*</span></label>
                                    <input type="tel" id="tlf" wire:model.live="tlf_rem"
                                        @if ($corporativo) disabled @endif maxlength="11"
                                        placeholder="ejem: 04245555555"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('tlf_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'correo_rem')
                                <!-- Correo Electronico -->
                                <div class="">
                                    <label for="correo" class="block text-sm font-medium text-gray-700">E-mail<span
                                            class="text-red-500">*</span></label>
                                    <input type="email" id="correo" wire:model="correo_rem"
                                        @if ($corporativo) disabled @endif
                                        placeholder="ejem: correo@gmail.com"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('correo_rem')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @endforeach

                    </div>

                    <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                    <div class="mb-2 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                            Datos del Destinatario
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                        @foreach ($this->ObtenerInputs() as $input)
                            @if ($input === 'nombre_dest')
                                <!-- Nombre -->
                                <div class="">
                                    <label for="nombre2" class="block text-sm font-medium text-gray-700">Nombre/Razón
                                        Soc.<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre2" wire:model="nombre_dest" maxlength="20"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'apellido_dest')
                                <!-- Apellido -->
                                <div class="">
                                    <label for="apellido2"
                                        class="block text-sm font-medium text-gray-700">Apellido<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="apellido2" wire:model="apellido_dest" maxlength="20"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('apellido_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'tipo_documento_dest')
                                <!-- Tipo de Documento -->
                                <div class="">
                                    <label for="tipo_documento_id2"
                                        class="block text-sm font-medium text-gray-700">Documento<span
                                            class="text-red-500">*</span></label>
                                    <select id="tipo_documento_id2" wire:model="tipo_documento_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="">Seleccionar:</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'documento_dest')
                                <!-- Documento -->
                                <div class="">
                                    <label for="documento2" class="block text-sm font-medium text-gray-700">Nro.
                                        Documento<span class="text-red-500">*</span></label>
                                    <input type="text" id="documento2"
                                        wire:model.live.debounce.300ms="documento_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('documento_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'continentess')
                                @if ($envio_seleccionado == 'internacional')
                                    <!-- Continente -->
                                    <div class="">
                                        <label for="continente_id"
                                            class="block text-sm font-medium text-gray-700">Continente<span
                                                class="text-red-500">*</span></label>
                                        <select id="continente_id" wire:model.live="continentess"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione un continente</option>
                                            @foreach ($continentes as $continente)
                                                <option value="{{ $continente->continente_id }}">
                                                    {{ $continente->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('continentess')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @elseif ($input === 'paiss')
                                @if ($envio_seleccionado === 'internacional')
                                    <!-- Pais -->
                                    <div class="">
                                        <label for="pais_id"
                                            class="block text-sm font-medium text-gray-700">País<span
                                                class="text-red-500">*</span></label>
                                        <select id="pais_id" wire:model.live="paiss"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione un país</option>
                                            @foreach ($paises as $pais)
                                                <option value="{{ $pais->pais_id }}">{{ $pais->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('paiss')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @elseif ($input === 'estadoss_dest_inter')
                                @if ($envio_seleccionado === 'internacional')
                                    <!-- Estado -->
                                    <div class="mb-2">
                                        <label for="estado_id3"
                                            class="block text-sm font-medium text-gray-700">Estado/Provincia<span
                                                class="text-red-500">*</span></label>
                                        <select id="estado_id3" wire:model.live="estados_dest_inter"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                            <option value="" selected>Seleccione un estado</option>
                                            @foreach ($estadoss_dest_inter as $estado)
                                                <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('estadoss_dest_inter')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @elseif ($input === 'estadoss_dest')
                                <!-- Estado -->
                                <div class="" wire:key="dest-estado">
                                    <label for="estado_id2"
                                        class="block text-sm font-medium text-gray-700">Estado<span
                                            class="text-red-500">*</span></label>
                                    <select id="estado_id2" wire:model.live="estadoss_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un estado</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('estadoss_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'municipioss_dest')
                                <!-- Municipio -->
                                <div class="" wire:key="dest-municipio">
                                    <label for="municipio_id2"
                                        class="block text-sm font-medium text-gray-700">Municipio<span
                                            class="text-red-500">*</span></label>
                                    <select id="municipio_id2" wire:model.live="municipioss_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un municipio</option>
                                        @foreach ($municipios_dest as $municipio)
                                            <option value="{{ $municipio->municipio_id }}">{{ $municipio->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('municipioss_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'parroquiass_dest')
                                <!-- Parroquia -->
                                <div class="" wire:key="dest-parroquia">
                                    <label for="parroquia_id2"
                                        class="block text-sm font-medium text-gray-700">Parroquia<span
                                            class="text-red-500">*</span></label>
                                    <select id="parroquia_id2" wire:model.live="parroquiass_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar parroquia</option>
                                        @foreach ($parroquias_dest as $parroquia)
                                            <option value="{{ $parroquia->parroquia_id }}">{{ $parroquia->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parroquiass_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'ciudadess_dest')
                                <!-- ciudad -->
                                <div class="" wire:key="dest-ciudad">
                                    <label for="ciudades_dest"
                                        class="block text-sm font-medium text-gray-700">Ciudad<span
                                            class="text-red-500">*</span></label>
                                    <select id="ciudades_dest" wire:model.live="ciudadess_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Ciudad</option>
                                        @foreach ($ciudades_dest as $ciudad)
                                            <option value="{{ $ciudad->ciudad_id }}">{{ $ciudad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudadess_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'codigo_postal_dest')
                                <!-- Codigo Postal -->
                                <div class="" wire:key="dest-codigo-postal">
                                    <label for="cod_pos_dest" class="block text-sm font-medium text-gray-700">Código
                                        Postal<span class="text-red-500">*</span></label>
                                    <select id="cod_pos_dest" wire:model.live="codigo_postal_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Código Postal</option>
                                        @foreach ($codigos_postales_dest as $codigo_postal)
                                            <option value="{{ $codigo_postal }}">{{ $codigo_postal }}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'opt')
                                <!-- Parroquia -->
                                <div class="">
                                    <label for="opt"
                                        class="block text-sm font-medium text-gray-700">Oficinas<span
                                            class="text-red-500">*</span></label>
                                    <select id="opt" wire:model.live="opt_dest"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar oficina</option>
                                        @foreach ($opt as $op)
                                            <option value="{{ $op->oficina_id }}">{{ $op->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('opt')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'taquilla_postal')
                                <!-- Parroquia -->
                                <div class="">
                                    <label for="codigo_apartado" class="block text-sm font-medium text-gray-700">Nro
                                        Apartado<span class="text-red-500">*</span></label>
                                    <select id="codigo_apartado" wire:model.live="codigo_apartado_postal"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar apartado</option>
                                        @foreach ($apartados as $ap)
                                            <option value="{{ $ap->codigo_apartado_id }}">{{ $ap->apartado }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('codigo_apartado_postal')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'direccion_dest')
                                @foreach ($parametro as $key => $direccion_dest)
                                    <div class="">
                                        <label for="{{ $key }}"
                                            class="block text-sm font-medium text-gray-700">{{ $key }}<span
                                                class="text-red-500">*</span></label>
                                        <input type="text" id="{{ $key }}"
                                            wire:model.live.debounce.500ms="parametro.{{ $key }}"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                        @error('parametro.{{ $key }}')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach

                                <!-- Direccion -->
                                <div class=" md:col-span-2">
                                    <label for="direccion2"
                                        class="block text-sm font-medium text-gray-700">Dirección<span
                                            class="text-red-500">*</span></label>
                                    <textarea type="text" wire:model="direccion_dest" style="resize: none;" id="direccion2"
                                        class="mt-1 block w-full border border-gray-300
                                    rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                    @error('direccion_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'tlf_dest')
                                <!-- Numero de Telefono -->
                                <div class="">
                                    <label for="tlf2" class="block text-sm font-medium text-gray-700">Nro.
                                        Teléfono<span class="text-red-500">*</span></label>
                                    <input type="tel" id="tlf2" wire:model.live="tlf_dest" maxlength="11"
                                        placeholder="ejem: 04245555555"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('tlf_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($input === 'correo_dest')
                                <!-- Correo -->
                                <div class="">
                                    <label for="email2" class="block text-sm font-medium text-gray-700">E-mail<span
                                            class="text-red-500">*</span></label>
                                    <input type="email" id="email2" wire:model="correo_dest"
                                        placeholder="ejem: correo@gmail.com"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs" />
                                    @error('correo_dest')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                    @if ($cancelar_pago)
                        <div
                            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm overflow-y-auto p-4">
                            <div class="relative bg-white shadow-2xl rounded-xl max-w-4xl w-full overflow-hidden">

                                <div class="bg-gray-50 border-b px-6 py-4 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-800">Finalizar Pago</h3>
                                    <button type="button" wire:click="cerrar_pago"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row gap-8">
                                        <div class="flex-1 min-w-0">
                                            <livewire:caja-de-pago.caja-de-pago />
                                        </div>

                                        
                                        <div class="w-full md:w-80 md:flex-shrink-0 bg-gray-50 rounded-xl p-5 border border-gray-200">
                                            <h4
                                                class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider border-b pb-2">
                                                Detalle de la Orden</h4>
                                            <div class="space-y-3">
                                                @foreach ($totales_por_servicio as $servicio_id => $totales)
                                                    @php $servicio = \App\Models\Servicio::find($servicio_id); @endphp
                                                    <div class="flex justify-between items-start text-sm">
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="font-medium text-gray-800">{{ $servicio->nombre }}</span>
                                                            <span class="text-xs text-gray-500">Cantidad:
                                                                {{ $totales['cant'] }}</span>
                                                        </div>
                                                        <span
                                                            class="font-semibold text-gray-700">{{ number_format($totales['total'], 2) }}
                                                            Bs</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-300">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-base font-bold text-gray-800">Total a
                                                        Pagar:</span>
                                                    <span class="text-xl font-black text-red-600">{{ $total_lote }}
                                                        Bs</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3">
                                    <x-button type="button" wire:click="cerrar_pago"
                                        class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</x-button>
                                    <x-button type="button" wire:click="aprobar_pago" wire:target="aprobar_pago, generar_facturacion" wire:loading.attr='disabled'
                                        class="bg-green-600 hover:bg-green-700 text-white px-8">Aprobar Pago</x-button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="max-w-7xl mx-auto p-4">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">

                                <div class="p-5">
                                    @if ($servicioss == 9)
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                                                    <p class="text-[10px] uppercase font-bold text-red-700">Origen</p>
                                                    <p class="text-gray-800 font-medium">
                                                        {{ $estado_or->nombre ?? 'N/A' }}</p>
                                                    <p class="text-gray-600 text-xs">
                                                        {{ $ciudad_or->nombre ?? 'N/A' }}</p>
                                                </div>
                                                <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                                                    <p class="text-[10px] uppercase font-bold text-gray-500">Destino
                                                    </p>
                                                    <p class="text-gray-800 font-medium">
                                                        {{ $estado_dest->nombre ?? 'N/A' }}</p>
                                                    <p class="text-gray-600 text-xs">
                                                        {{ $ciudad_dest->nombre ?? 'N/A' }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">Tipo de
                                                    Envío</label>
                                                <select wire:model.live="tipo_envio_expreso"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                    <option value="">Seleccionar</option>
                                                    <option value="Urbano">Urbano</option>
                                                    <option value="Intraestatal">Interestatal</option>
                                                    <option value="Nacional">Nacional</option>
                                                </select>
                                            </div>

                                            <div class="pt-4 border-t border-gray-100 space-y-1">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Coste del envío:</span>
                                                    <span
                                                        class="font-medium text-gray-800">{{ number_format($precio_total, 2) }}
                                                        Bs</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">I.V.A:</span>
                                                    <span
                                                        class="font-medium text-gray-800">{{ number_format($iva, 2) }}
                                                        Bs</span>
                                                </div>
                                                <div class="flex justify-between pt-2">
                                                    <span class="font-bold text-red-700">Total a pagar:</span>
                                                    <span
                                                        class="font-black text-red-700 text-lg">{{ number_format($total_pagar, 2) }}
                                                        Bs</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if ($servicioss != 2 || $peso >= 500)
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Coste Base:</span>
                                                    <span
                                                        class="font-bold text-gray-700">{{ number_format($precio_total, 2) }}
                                                        Bs</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">I.V.A:</span>
                                                    <span
                                                        class="font-bold text-gray-700">{{ number_format($iva, 2) }}
                                                        Bs</span>
                                                </div>
                                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                                    <span class="font-bold text-gray-800">Total a Pagar:</span>
                                                    <span
                                                        class="font-black text-green-700 text-lg">{{ number_format($total_pagar, 2) }}
                                                        Bs</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <div class="p-5 bg-gray-50/50">
                                    @if ($mostrar_subservicio == true && $envio_seleccionado === 'nacional')
                                        <h5 class="text-[10px] font-bold text-red-600 uppercase mb-3 tracking-widest">
                                            Servicios Adicionales (Nac)</h5>
                                        <ul class="space-y-2">
                                            @if (!empty($subser))
                                                @foreach ($subser as $subserv)
                                                    <li
                                                        class="flex justify-between items-center text-xs bg-white p-2 rounded border border-gray-100 shadow-sm">
                                                        <span class="text-gray-700">
                                                            {{ $subserv['nombre'] }}
                                                            @if (strcasecmp($subserv['nombre'] ?? '', 'Certificado') === 0)
                                                                <span class="text-[10px] text-red-500 italic">(incluido)</span>
                                                            @endif
                                                        </span>
                                                        <span class="font-bold text-red-500">+{{ $subserv['monto'] }}
                                                            Bs</span>
                                                    </li>
                                                @endforeach
                                            @endif

                                            @if ($recoleccion)
                                                <li
                                                    class="flex justify-between items-center text-xs bg-red-50 p-2 rounded border border-red-100">
                                                    <span class="text-red-800 font-medium">Recolección a
                                                        Domicilio</span>
                                                    <span class="font-bold text-red-600">{{ $recoleccion_coste }}
                                                        Bs</span>
                                                </li>
                                            @endif

                                            @if ($this->insumo_sel)
                                                @foreach ($insumo_selec as $insumo)
                                                    <li
                                                        class="flex justify-between items-center text-xs bg-white p-2 rounded border border-gray-100 shadow-sm">
                                                        <span
                                                            class="text-gray-700 italic">{{ $insumo['descripcion'] }}</span>
                                                        <span class="font-bold text-red-500">{{ $insumo['costo'] }}
                                                            Bs</span>
                                                    </li>
                                                @endforeach

                                                <li
                                                    class="mt-4 flex justify-between items-center p-2 bg-red-50 rounded">
                                                    <span class="text-xs font-bold text-red-800 uppercase">Subtotal
                                                        Insumos:</span>
                                                    <span
                                                        class="font-black text-red-800">{{ $insumo_pretotal }}</span>
                                                </li>
                                            @endif

                                            @if (!empty($subser))
                                                <li
                                                    class="mt-4 flex justify-between items-center p-2 bg-red-50 rounded">
                                                    <span class="text-xs font-bold text-red-800 uppercase">Subtotal
                                                        Extra:</span>
                                                    <span
                                                        class="font-black text-red-800">{{ $subser_pretotal }}</span>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif

                                    @if ($mostrar_subservicio_inter == true && $envio_seleccionado === 'internacional')
                                        <h5 class="text-[10px] font-bold text-blue-600 uppercase mb-3 tracking-widest">
                                            Servicios Adicionales (Inter)</h5>
                                        <div class="space-y-2">
                                            @if (!empty($subser_inter))
                                                @foreach ($subser_inter as $subserv)
                                                    <div
                                                        class="flex justify-between text-xs p-2 bg-white rounded border border-gray-100">
                                                        <span class="text-gray-600">
                                                            {{ $subserv['nombre'] }}
                                                            @if (strcasecmp($subserv['nombre'] ?? '', 'Certificado') === 0)
                                                                <span class="text-[10px] text-blue-500 italic">(incluido)</span>
                                                            @endif
                                                        </span>
                                                        <span
                                                            class="font-bold text-red-500">{{ $subserv['monto'] }}</span>
                                                    </div>
                                                @endforeach
                                                <div
                                                    class="mt-4 flex justify-between items-center p-2 bg-blue-50 rounded">
                                                    <span class="text-xs font-bold text-blue-800 uppercase">Subtotal
                                                        Extra:</span>
                                                    <span
                                                        class="font-black text-blue-800">{{ $total_subser_inter }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="md:flex md:gap-3 border-t-2 p-2 border-red-700 justify-center sm:col-span-2 ">
                        <div class="">
                            <x-button type="button" class="bg primary" wire:click="agregar_envio"
                                wire:loading.attr='disabled' wire:target='agregar_envio'>
                                AGREGAR ENVIO
                            </x-button>
                        </div>
                        <div class="">
                            @if ($id_envios)
                                <x-button type="button" class="bg primary" wire:click="realizar_pago"
                                    wire:loading.attr='disabled' wire:target='agregar_envio'>
                                    REALIZAR PAGO
                                </x-button>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
    @if ($aviso_envio)
        <div
            class="fixed inset-0 bg-gray-900 bg-opacity-60 flex justify-center items-center z-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300">

                {{-- Header --}}
                <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex-shrink-0 bg-red-100 p-2.5 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Lote en Proceso</h2>
                        <p class="text-sm text-gray-500">¿Qué desea hacer a continuación?</p>
                    </div>
                </div>

                {{-- Opciones --}}
                <div class="p-6 space-y-3">

                    {{-- Opción 1: Agregar otro envío --}}
                    <button wire:click="decision('registrar')"
                        class="w-full flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all text-left group">
                        <div class="flex-shrink-0 bg-green-100 group-hover:bg-green-200 p-2.5 rounded-lg transition">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 group-hover:text-green-700 transition">Agregar otro envío
                                al lote</p>
                            <p class="text-xs text-gray-500 mt-0.5">Continúe registrando más envíos antes de pagar</p>
                        </div>
                    </button>

                    {{-- Opción 2: Proceder al pago --}}
                    <button wire:click="decision('pagar')"
                        class="w-full flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-red-500 hover:bg-red-50 transition-all text-left group">
                        <div class="flex-shrink-0 bg-red-100 group-hover:bg-red-200 p-2.5 rounded-lg transition">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 group-hover:text-red-700 transition">Cerrar lote y
                                realizar pago</p>
                            <p class="text-xs text-gray-500 mt-0.5">No se agregarán más envíos, proceda al cobro</p>
                        </div>
                    </button>

                </div>
            </div>
        </div>
    @endif

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
                    timer: 2500
                });
            })

            // success alert
            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            })

            // success alert
            Livewire.on('alertSuccess3', message => {
                Swal.fire({
                    position: "center",
                    icon: "info",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            })

            Livewire.on('envio_registrado', () => {
                setTimeout(() => {
                    location.reload();
                }, 2000);
            });
        </script>
    @endscript

    <script>
        function formatCurrency(input) {
            // Eliminar caracteres no numéricos
            let value = input.value.replace(/[^0-9]/g, '');

            // Convertir a número y formatear
            if (value.length === 0) {
                input.value = '0,00';
                return;
            }

            // Convertir a centimos
            let cents = parseInt(value, 10);

            // Formatear a bs y centimos
            let bs = Math.floor(cents / 100);
            let formattedCents = (cents % 100).toString().padStart(2, '0');

            // // Agregar separador de miles
            let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Actualizar el valor del input
            input.value = `${formattedbs},${formattedCents}`;
            console.log('entro');

        }
    </script>
@endpush
