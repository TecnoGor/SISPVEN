<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                @if($envio_seleccionado === 'nacional')
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
                <input type="radio" wire:model.live="envio_seleccionado" value="nacional" checked class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-primary">Nacional</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="radio" wire:model.live="envio_seleccionado" value="internacional" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-primary">Internacional</span>
            </label>

            {{-- <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" wire:model.live="corporativo" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                <span class="text-primary">Cliente Corporativo</span>
            </label> --}}

            <section class="md:col-start-6">
                <a href="{{ route('envios-lotes') }}">
                    <x-button>Envíos Por Lotes</x-button>
                </a>
            </section>
        </div>

        <div style="background-color: white;border-radius: 8px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 3px;/">

            <form wire:submit.prevent="submit" class="space-y-4">

                <div class="mb-2 sm:mb-0">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b-2 border-gray-300 pb-2">
                        Datos del Envío
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Tipos de Envios -->
                    <div class="">
                        <label for="servicio_id" class="block text-sm font-medium text-gray-700">Tipos de Envios</label>
                        <select id="servicio_id" wire:model.live="servicioss" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Seleccione un envío</option>
                            @foreach ($servicios_filtrados as $servicio)
                            <option value="{{$servicio['servicio_id']}}">
                                {{$servicio['nombre']}}
                            </option>
                            @endforeach
                        </select>
                        @error('servicio_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    @foreach ($this->ObtenerInputs() as $input)
                        @if ($input === 'tarifa_encon')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tarifas Nacionales</label>
                                <div class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($tarifas as $index => $tarifa)
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox"
                                                value="{{ $tarifa->tarifa_conceptos_id }}"
                                                wire:model.live="tarifa_encon"
                                                wire:change="actualizarTarifa({{ $tarifa->tarifa_conceptos_id }})"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700"
                                                {{ in_array($tarifa->exclusion, $tarifa_encon)}}>
                                            <span class="ml-2 text-gray-800">{{ $tarifa->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tarifa_encon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @elseif ($input === 'apartado')
                            <div>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model.live="apartado" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <span class="text-primary">Apartado Postal</span>
                                </label>
                            </div>

                            @elseif ($input === 'subservicios')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad de Servicio:</label>
                                <div class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($subservicios as $index => $subservicio)
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox"
                                                value="{{ $subservicio->tarifa_conceptos_id }}"
                                                wire:model.live="subservicio_encon"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700"
                                                {{ in_array($subservicio->exclusion, $subservicio_encon) ? 'disabled' : '' }}
                                                @if($subservicio->tarifa_conceptos_id == 20 && $peso <= 2000) disabled @endif>
                                            <span class="ml-2 text-gray-800">{{ $subservicio->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('subservicio_encon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @elseif ($input === 'insumos')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Insumos</label>
                                <div class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($insumos as $index => $insumo)
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox"
                                                value="{{ $insumo->insumo_id }}"
                                                wire:model.live="insumo_sel"
                                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                                            <span class="ml-2 text-gray-800">{{ $insumo->descripcion}}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('subservicio_encon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @elseif ($input === 'subservicios_inter')
                            <!-- Tarifas -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad de Servicio:</label>
                                <div class="mb-4 p-4 border border-gray-300 rounded-lg shadow-sm bg-white max-h-52 w-70 overflow-auto">
                                    @foreach ($subservicios_inter as $index => $subservicio)
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
                                @error('subservicio_inter_encon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            @elseif ($input === 'codigo')
                                @if($envio_seleccionado == 'internacional')
                                <div class="">
                                    <label for="codigo_rastreo" class="block text-sm font-medium text-gray-700">
                                    Codigo de Rastreo
                                    <span class="text-red-500">*</span></label>
                                    <input type="text" id="codigo_rastreo" wire:model.live='codigo'
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm
                                    [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [appearance:textfield]"/>
                                    @error('codigo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @endif

                            @elseif ($input === 'recoleccion')
                            <div class="">
                                <label for="recoleccion" class="block text-sm font-medium text-gray-700 mb-2">Recolección a Domicilio</label>
                                <input type="checkbox" name="recoleccion" id="recoleccion"
                                wire:model.live="recoleccion"
                                class="form-checkbox h-5 w-5 text-red-700 border-gray-300 rounded focus:ring-red-700 checked:bg-red-700 checked:border-red-700">
                                <br>
                                <span class="text-red-500 text-sm">{{ $recol_mensj }}</span>
                            </div>

                        @elseif($input === 'peso')
                            <!-- Peso -->
                            <div class="md:w-1/2">
                                <label for="peso" class="block text-sm font-medium text-gray-700">
                                    Peso ({{ $servicioss == 8 ? 'kilogramos' : 'gramos' }})
                                <span class="text-red-500">*</span></label>
                                <input type="number" id="peso" wire:model.live.debounce.500ms='peso' oninput="this.value = this.value < 0.01 ? '' : this.value" placeholder="0.00"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm
                                [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [appearance:textfield]"/>
                                @error('peso') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                        @elseif($input === 'contenido')
                            <!-- Contenido del Envio -->
                            <div class="">
                                <label for="contenido" class="block text-sm font-medium text-gray-700">Contenido del Envio<span class="text-red-500">*</span></label>
                                <textarea type="text" style="resize: none;" wire:model="contenido" maxlength="300" id="contenido"  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"> </textarea>
                                @error('contenido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    @endforeach
                </div>

                    <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                @if(!empty($servicioss))

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
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre/Razon Soc.<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre" wire:model="nombre_rem" @if($corporativo) disabled @endif maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            <!-- Apellido -->
                            @elseif ($input === 'apellido_rem')
                                <div class="">
                                    <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                                    <input type="text" id="apellido" wire:model="apellido_rem" @if($corporativo) disabled @endif maxlength="20" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            <!-- Tipo de Documento -->
                            @elseif ($input === 'tipo_documento_rem')
                                <div class="">
                                    <label for="tipo_documento_id" class="block text-sm font-medium text-gray-700">Documento<span class="text-red-500">*</span></label>
                                    <select id="tipo_documento_id" wire:model.live="tipo_documento_rem" @if($corporativo) disabled @endif class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="">Seleccionar</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                @error('tipo_documento_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            <!-- Numero de Documento -->
                            @elseif ($input === 'documento_rem')
                                <div class="">
                                    <label for="documento" class="block text-sm font-medium text-gray-700">Nro. Documento<span class="text-red-500">*</span></label>
                                    <input type="text" id="documento" wire:model.live.debounce.300ms="documento_rem" @if($corporativo) disabled @endif class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                @error('documento_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            <!-- Cliente Corporativo -->
                            @elseif ($input === 'cliente_corporativo')
                            <div class="">
                                <label for="cliente_corp" class="block text-sm font-medium text-gray-700">Cliente Corporativo</label>
                                <select id="cliente_corp" wire:model.live="cliente_corp" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" selected>Seleccione un Cliente</option>
                                    @foreach ($clientes_corp as $cliente)
                                        <option value="{{$cliente->cliente_corporativo_id}}">{{$cliente->razon_social}}</option>
                                    @endforeach
                                </select>
                                @error('cliente_corp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Estados -->
                            @elseif ($input === 'estadoss')
                                <div class="">
                                    <label for="estado_id" class="block text-sm font-medium text-gray-700">Estado<span class="text-red-500">*</span></label>
                                    <select id="estado_id" wire:model.live="estadoss" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected >Seleccione un estado</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('estadoss') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            <!-- Municipio -->
                            @elseif ($input === 'municipioss')
                                <div class="">
                                    <label for="municipio_id" class="block text-sm font-medium text-gray-700">Municipio<span class="text-red-500">*</span></label>
                                    <select id="municipio_id" wire:model.live="municipioss" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un municipio</option>
                                        @foreach ($municipios as $municipio)
                                            <option value="{{$municipio->municipio_id}}" @if($municipio->municipio_id == $municipioss) selected @endif>{{$municipio->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('municipioss') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            @elseif ($input === 'parroquiass')
                                <!-- Parroquias -->
                                <div class="">
                                    <label for="parroquia_id" class="block text-sm font-medium text-gray-700">Parroquia<span class="text-red-500">*</span></label>
                                    <select id="parroquia_id" wire:model.live="parroquiass" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Parroquia</option>
                                        @foreach ($parroquias as $parroquia)
                                            <option value="{{$parroquia->parroquia_id}}" @if($parroquia->parroquia_id == $parroquiass) selected @endif>{{$parroquia->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquiass') <span class="text-red-500 text-sm">{{$message}}</span> @enderror
                                </div>

                            @elseif ($input === 'ciudades_rem')
                                <!-- Municipio -->
                                <div class="">
                                    <label for="ciudades_rem" class="block text-sm font-medium text-gray-700">Ciudad<span class="text-red-500">*</span></label>
                                    <select id="ciudades_rem" wire:model.live="ciudades_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Ciudad</option>
                                        @foreach ($ciudades as $ciudad)
                                        <option value="{{$ciudad->ciudad_id}}">{{$ciudad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudades_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            @elseif ($input === 'codigo_postal_rem')
                                <!-- Codigo Postal -->
                                <div class="">
                                    <label for="codigo_postal_rem" class="block text-sm font-medium text-gray-700">Codigo Postal<span class="text-red-500">*</span></label>
                                    <select id="codigo_postal_rem" wire:model.live="codigo_postal_rem" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="">Seleccionar Código Postal</option>
                                        @foreach ($codigos_postales_rem as $codigo_postal)
                                            <option value="{{ $codigo_postal }}" @if($codigo_postal == $codigo_postal_rem) selected @endif>{{ $codigo_postal }}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>


                            @elseif ($input === 'direccion_rem')
                                <!-- Direccion -->
                                <div class=" md:col-span-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección<span class="text-red-500">*</span></label>
                                    <textarea wire:model="direccion_rem" id="direccion" style="resize: none;" maxlength="150"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                    @error('direccion_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>


                            @elseif ($input === 'tlf_rem')
                                <!-- Telefono -->
                                <div class="">
                                    <label for="tlf" class="block text-sm font-medium text-gray-700">Nro. Teléfono<span class="text-red-500">*</span></label>
                                    <input type="tel" id="tlf" wire:model.live="tlf_rem" @if($corporativo) disabled @endif maxlength="11" placeholder="ejem: 04245555555" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('tlf_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>


                            @elseif ($input === 'correo_rem')
                                <!-- Correo Electronico -->
                                <div class="">
                                    <label for="correo" class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                                    <input type="email" id="correo" wire:model="correo_rem" @if($corporativo) disabled @endif placeholder="ejem: correo@gmail.com" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('correo_rem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                                    <label for="nombre2" class="block text-sm font-medium text-gray-700">Nombre/Razon Soc.<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre2" wire:model="nombre_dest"  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('nombre_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'apellido_dest')
                                <!-- Apellido -->
                                <div class="">
                                    <label for="apellido2" class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                                    <input type="text" id="apellido2" wire:model="apellido_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('apellido_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'tipo_documento_dest')
                                <!-- Tipo de Documento -->
                                <div class="">
                                    <label for="tipo_documento_id2" class="block text-sm font-medium text-gray-700">Documento<span class="text-red-500">*</span></label>
                                    <select id="tipo_documento_id2" wire:model="tipo_documento_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="">Seleccionar:</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'documento_dest')
                                <!-- Documento -->
                                <div class="">
                                    <label for="documento2" class="block text-sm font-medium text-gray-700">Nro. Documento<span class="text-red-500">*</span></label>
                                    <input type="text" id="documento2" wire:model.live.debounce.300ms="documento_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('documento_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'continentess')
                                    @if($envio_seleccionado == 'internacional')
                                        <!-- Continente -->
                                        <div class="">
                                                <label for="continente_id" class="block text-sm font-medium text-gray-700">Continente<span class="text-red-500">*</span></label>
                                                <select id="continente_id" wire:model.live="continentess" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                    <option value="" selected>Seleccione un continente</option>
                                                    @foreach ($continentes as $continente)
                                                    <option value="{{$continente->continente_id}}">
                                                        {{$continente->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('continentess') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                @elseif ($input === 'paiss')
                                    @if($envio_seleccionado === 'internacional')
                                        <!-- Pais -->
                                        <div class="">
                                            <label for="pais_id" class="block text-sm font-medium text-gray-700">País<span class="text-red-500">*</span></label>
                                            <select id="pais_id" wire:model.live="paiss" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                <option value="" selected>Seleccione un pais</option>
                                                @foreach ($paises as $pais)
                                                <option value="{{$pais->pais_id}}">{{$pais->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('paiss') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                @elseif ($input === 'estadoss_dest_inter')
                                    @if($envio_seleccionado === 'internacional')
                                        <!-- Estado -->
                                        <div class="mb-2">
                                            <label for="estado_id3" class="block text-sm font-medium text-gray-700">Estado/Provincia<span class="text-red-500">*</span></label>
                                            <select id="estado_id3" wire:model.live="estados_dest_inter" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                <option value="" selected>Seleccione un estado</option>
                                                @foreach ($estadoss_dest_inter as $estado)
                                                <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('estadoss_dest_inter') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                @elseif ($input === 'estadoss_dest')
                                <!-- Estado -->
                                <div class="" wire:key="dest-estado">
                                    <label for="estado_id2" class="block text-sm font-medium text-gray-700">Estado<span class="text-red-500">*</span></label>
                                    <select id="estado_id2" wire:model.live="estadoss_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un estado</option>
                                        @foreach ($estados as $estado)
                                        <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('estadoss_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'municipioss_dest')
                                <!-- Municipio -->
                                <div class="" wire:key="dest-municipio">
                                    <label for="municipio_id2" class="block text-sm font-medium text-gray-700">Municipio<span class="text-red-500">*</span></label>
                                    <select id="municipio_id2" wire:model.live="municipioss_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccione un municipio</option>
                                        @foreach ($municipios_dest as $municipio)
                                        <option value="{{$municipio->municipio_id}}">{{$municipio->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('municipioss_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'parroquiass_dest')
                                <!-- Parroquia -->
                                <div class="" wire:key="dest-parroquia">
                                    <label for="parroquia_id2" class="block text-sm font-medium text-gray-700">Parroquia<span class="text-red-500">*</span></label>
                                    <select id="parroquia_id2" wire:model.live="parroquiass_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar parroquia</option>
                                        @foreach ($parroquias_dest as $parroquia)
                                        <option value="{{$parroquia->parroquia_id}}">{{$parroquia->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('parroquiass_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'ciudadess_dest')
                                <!-- ciudad -->
                                <div class="" wire:key="dest-ciudad">
                                    <label for="ciudades_dest" class="block text-sm font-medium text-gray-700">Ciudad<span class="text-red-500">*</span></label>
                                    <select id="ciudades_dest" wire:model.live="ciudadess_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Ciudad</option>
                                        @foreach ($ciudades_dest as $ciudad)
                                        <option value="{{$ciudad->ciudad_id}}">{{$ciudad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudadess_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'codigo_postal_dest')
                                <!-- Codigo Postal -->
                                <div class="" wire:key="dest-codigo-postal">
                                    <label for="cod_pos_dest" class="block text-sm font-medium text-gray-700">Codigo Postal<span class="text-red-500">*</span></label>
                                    <select id="cod_pos_dest" wire:model.live="codigo_postal_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar Codigo Postal</option>
                                        @foreach ($codigos_postales_dest as $codigo_postal)
                                            <option value="{{$codigo_postal}}">{{$codigo_postal}}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_postal_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'opt')
                                <!-- Parroquia -->
                                <div class="">
                                    <label for="opt" class="block text-sm font-medium text-gray-700">Oficinas<span class="text-red-500">*</span></label>
                                    <select id="opt" wire:model.live="opt_dest" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar oficina</option>
                                        @foreach ($opt as $op)
                                        <option value="{{$op->oficina_id}}">{{$op->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('opt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'taquilla_postal')
                                <!-- Parroquia -->
                                <div class="">
                                    <label for="codigo_apartado" class="block text-sm font-medium text-gray-700">Nro Apartado<span class="text-red-500">*</span></label>
                                    <select id="codigo_apartado" wire:model.live="codigo_apartado_postal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                        <option value="" selected>Seleccionar apartado</option>
                                        @foreach ($apartados as $ap)
                                        <option value="{{$ap->codigo_apartado_id}}">{{$ap->apartado}}</option>
                                        @endforeach
                                    </select>
                                    @error('codigo_apartado_postal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'direccion_dest')
                                @foreach ($parametro as $key => $direccion_dest)
                                    <div class="">
                                        <label for="{{$key}}" class="block text-sm font-medium text-gray-700">{{$key}}<span class="text-red-500">*</span></label>
                                        <input type="text" id="{{$key}}" wire:model.live.debounce.500ms="parametro.{{$key}}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                        @error('parametro.{{$key}}') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach

                                <!-- Direccion -->
                                <div class=" md:col-span-2">
                                    <label for="direccion2" class="block text-sm font-medium text-gray-700">Direccion<span class="text-red-500">*</span></label>
                                    <textarea type="text" wire:model="direccion_dest" style="resize: none;" id="direccion2"  class="mt-1 block w-full border border-gray-300
                                    rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs"></textarea>
                                    @error('direccion_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>


                                @elseif ($input === 'tlf_dest')
                                <!-- Numero de Telefono -->
                                <div class="">
                                    <label for="tlf2" class="block text-sm font-medium text-gray-700">Nro. Teléfono<span class="text-red-500">*</span></label>
                                    <input type="tel" id="tlf2" wire:model.live="tlf_dest" maxlength="11" placeholder="ejem: 04245555555" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" />
                                    @error('tlf_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @elseif ($input === 'correo_dest')
                                <!-- Correo -->
                                <div class="">
                                    <label for="email2" class="block text-sm font-medium text-gray-700">E-mail<span class="text-red-500">*</span></label>
                                    <input type="email" id="email2" wire:model="correo_dest" placeholder="ejem: correo@gmail.com" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-xs" />
                                    @error('correo_dest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @endif
                            @endforeach
                        </div>

                    <hr class="border-0 h-0.5 bg-red-600 shadow-md" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 space-x-3 gap-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">

                            <!-- Metodos de Pago -->
                            <div class="mb-2">
                                <label for="metodos_pago" class="block text-sm text-center font-medium text-gray-700">Métodos de Pago</label>
                                @error('metodo_pago_seleccionado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <select id="metodos_pago" wire:model="metodo_pago_seleccionado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="">Seleccione un método:</option>
                                    @foreach ($metodos_pago as $tipo_pago)
                                    <option value="{{$tipo_pago->tipo_pago_id}}">{{$tipo_pago->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('metodos_pago') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                <div class="mb-2">
                                    <input type="text" wire:model="monto" placeholder="monto bs..."
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                    onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                    oninput="formatCurrency(this)">
                                    @error('monto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="">
                                    <x-button type="button" wire:click="agregar_pago" class="bg-primary">Guardar</x-button>
                                </div>
                            </div>


                            <div class="mb-2">
                                <label for="" class="block text-sm text-center">Pagos Realizados:</label>
                                <div class="flex justify-center">
                                    <input type="text"
                                        disabled
                                        value="{{ $monto_pagado }} Bs"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm
                                        focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                </div>

                                <div class="mt-2">
                                    <ul class="list-none p-0">
                                        @foreach ($pagos as $pago)
                                            <li class="flex items-center justify-between p-2 mb-2 border-b border-gray-300 rounded-md bg-gray-50">
                                                <span class="text-sm font-medium text-gray-700">{{ $pago['nombre'] }} - {{ $pago['monto'] }} bs</span>
                                                
                                                <!-- Botón de basurero rojo para eliminar el pago -->
                                                <button 
                                                    type="button" wire:click="eliminar_pago({{ $pago['tipo_pago_id'] }})" 
                                                    class="text-red-500 hover:text-red-700 focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>  
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:border-l-2  p-2 sm:border-t-0 border-t-2 sm:grid-cols-2 gap-4">
                            <!-- Montos y precio -->
                                @if($servicioss == 16)
                                    <ul class="list-disc pl-5 mt-2">
                                        @if(empty($tarifa_selec))
                                            <span></span>
                                        @else
                                            @foreach ($tarifa_selec as $tarifas_total)
                                                    <li class="flex justify-between py-1 border-b border-gray-200">
                                                        <span class="font-medium text-[12px]">{{ $tarifas_total->nombre }}:</span>
                                                        <span class="text-gray-600 ml-2">{{ number_format($tarifas_total->monto, 2)}} Bs</span>
                                                    </li>
                                            @endforeach
                                            <br>
                                            <span class="font-medium text-[13px]">Coste: {{number_format($precio_total, 2)}} Bs</span>
                                            <br>
                                            <span class="font-medium text-[13px]">I.V.A: {{number_format($iva, 2)}} Bs</span>
                                            <br>
                                            <div class="font-semibold text-red-700">
                                                <span class="font-medium text-[13px]">Total a pagar: {{number_format($total_pagar, 2)}} Bs</span>
                                            </div>

                                        @endif
                                    </ul>

                                @elseif($servicioss == 9)
                                    <div class="md:flex md:gap-3">
                                        <li class="text-[14px] mb-4">
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-700">Estado de Origen:</span>
                                                <span>{{$estado_or->nombre ?? 'N/A'}}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-700">Ciudad de Origen:</span>
                                                <span>{{$ciudad_or->nombre ?? 'N/A'}}</span>
                                            </div>
                                                <br>
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-700">Estado de Destino:</span>
                                                <span>{{$estado_dest->nombre ?? 'N/A'}}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-700">Ciudad de Destino:</span>
                                                <span>{{$ciudad_dest->nombre ?? 'N/A'}}</span>
                                            </div>
                                                <br>
                                            <div class="mb-2">
                                                <span class="font-semibold text-red-700">Tipo de Envío:</span>
                                                <span>{{$tipo_envio_expreso}}</span>
                                            </div>
                                                <br>
                                            <div class="font-medium text-[14px]">
                                                <span>Coste:</span>
                                                <span class="ml-1">{{number_format($precio_total, 2)}} Bs</span>
                                            </div>
                                            <div class="font-medium text-[14px]">
                                                <span>I.V.A:</span>
                                                <span class="ml-1">{{number_format($iva, 2)}} Bs</span>
                                            </div>
                                            <br>
                                            <div class=" text-red-700 font-medium text-[14px]">
                                                <span>Total a pagar:</span>
                                                <span class="ml-1">{{number_format($total_pagar, 2)}} Bs</span>
                                            </div>
                                            <br>
                                            <div class="font-medium text-[14px]">
                                                <span>Tipo de Envio</span>
                                                <select wire:model.live="envio_expreso_b" id="envio_expreso" class="mt-1 block w-2/3 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                    <option value="">Seleccionar</option>
                                                    <option value="Urbano">Urbano</option>
                                                    <option value="Intraestatal">Interestatal</option>
                                                    <option value="Nacional">Nacional</option>
                                                </select>
                                            </div>
                                        </li>
                                    </div>

                                @else
                                    @if($servicioss != 2 || $peso >= 500)
                                    <div class="flex flex-col gap-3">
                                        <div class="flex gap-3">
                                            <span class="font-medium text-[16px]">Coste Base:</span>
                                            <span class="font-medium text-[16px] text-red-500">{{ number_format($precio_total, 2)}} Bs</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <span class="font-medium text-[16px]">I.V.A:</span>
                                            <span class="font-medium text-[16px] text-red-500">{{ number_format($iva, 2)}} Bs</span>
                                        </div>
                                        <br>
                                        <div class="flex gap-3">
                                            <span class="font-medium text-[16px]">Total a Pagar:</span>
                                            <span class="font-medium text-[16px] text-green-700">{{ number_format($total_pagar, 2)}} Bs</span>
                                        </div>
                                    </div>
                                    @endif
                                @endif


                            @if($mostrar_subservicio == true && $envio_seleccionado === 'nacional')
                                <div class="grid-cols-2 sm:border-l-2  p-2 sm:border-t-0 border-t-2 border-red-500 md:grid-cols-2 gap-4">
                                    @if(empty($subser))
                                        <span></span>
                                    @else
                                        @foreach($subser as $subserv)
                                        <li>
                                            <span class="font-medium text-[12px]">{{ $subserv['nombre']}}</span>
                                            <span class="font-medium text-[12px] text-red-500">{{ $subserv['monto']}}</span>
                                        </li>
                                        @endforeach
                                        <br>
                                    @endif

                                    @if ($recoleccion)
                                        <span class="font-medium text-[12px]">Recolección a Domicilio:</span>
                                        <span class="font-medium text-[12px] text-red-500">{{$recoleccion_coste}} Bs</span>
                                    @endif
                                    @if($this->insumo_sel)
                                        @foreach($insumo_selec as $insumo)
                                        <li>
                                            <span class="font-medium text-[10px]">{{ $insumo['descripcion']}}</span>
                                            <span class="font-medium text-[12px] text-red-500">{{ $insumo['costo']}} Bs</span>
                                        </li>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                            @if($mostrar_subservicio_inter == true && $envio_seleccionado === 'internacional')
                                <div class="grid-cols-2 sm:border-l-2  p-2 sm:border-t-0 border-t-2 border-red-500 md:grid-cols-2 lg:grid-cols-2 gap-4">
                                    @if(empty($subser_inter))
                                        <span></span>
                                    @else
                                        @foreach($subser_inter as $subserv)
                                        <li>
                                            <span class="font-medium text-[12px]">{{ $subserv['nombre']}}</span>
                                            <span class="font-medium text-[12px] text-red-500">{{$subserv['monto']}}</span>
                                        </li>
                                        @endforeach
                                        <span class="font-medium text-[12px]">Total de Subservicios:</span>
                                        <span class="font-medium text-[12px] text-green-700">{{$total_subser_inter}}</span>
                                    @endif
                                    <br>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="border-0 h-0.5 bg-red-600 shadow-md sm:col-span-2" />

                    <div class="md:flex md:gap-3 justify-center sm:col-span-2 ">
                        <div class="">
                            <x-button type="button" class="bg primary"
                                wire:click="habilitar" wire:loading.attr='disabled' wire:target='habilitar, submit'>
                                CREAR ENVÍO
                            </x-button>
                        </div>
                        <div class="">
                            <x-button type="button" class="bg primary"
                                wire:click="borrar">
                                LIMPIAR
                            </x-button>
                        </div>
                    </div>
                @endif
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
                timer: 1000
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

        Livewire.on('envio_registrado', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
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







































