@section('titulo')
    Detalles del Envio
@endsection
<div >
    <div class="flex justify-center items-center mb-4 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('envios.consulta-envios', ['servicio_id' => $servicio_id])" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-primary font-bold">Detalles del Envío</h1>
    </div>

    <div class="max-w-7xl mx-auto my-5 sm:px-6 lg:px-8">
    
    <div class="mb-4 sm:mb-0">
        @if ($envio->codigo_envio)
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Código de Envío: <font class="text-primary">{{ $envio->codigo_envio }}</font></h1>
        @endif
    </div>
    
    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg border border-gray-200 rounded-lg overflow-scroll md:overflow-hidden mt-4 p-16">
        <div class="min-w-full text-lg grid grid-cols-2">
                                        
                    @if ($envio->tipo_envio)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Tipo de Envío:</font> {{ strtoupper($envio->tipo_envio); }} </div>
                    @endif
                    @if ($envio->contenido)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Contenido del Envío:</font> {{ $envio->contenido }} </div>
                    @endif

                    @if ($envio->peso)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Peso:</font> @if ($envio->peso) {{$envio->peso}} g @endif </div>
                    @endif
                    @if ($envio->coste)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Monto:</font> @if ($envio->coste) {{ $envio->coste }} Bs. @endif</div>
                    @endif

                    @if ($envio->created_at)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Fecha de Creación:</font> {{ $envio->created_at }}</div>
                    @endif
                    
                    @if ($envio->servicio_expreso)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Servicio Expreso:</font> {{ $envio->servicio_expreso }}</div>
                    @endif

                    @if ($envio->servicio_id)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Servicio:</font> {{ $servicio->nombre }}</div>
                    @endif

                    @if ($envio->usuario_id)
                    <div class="px-4 pt-8 col-span-2"><font class="font-extrabold text-primary">Usuario:</font> {{ $usuario->name }} </div>
                    @endif

                    @if ($envio->oficina_id)
                    <div class="px-4 py-8 col-span-2"><font class="font-extrabold text-primary">Oficina Origen:</font> {{ $envio->oficinas->nombre }}</div>
                    @endif

                    <div class="py-2">
                        
                        @if ($envio->nombre_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Remitente:</font> {{ $envio->nombre_rem }}</div>
                        @endif

                        @if ($envio->codigo_postal_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Código Postal Remitente:</font><br> {{ $envio->codigo_postal_rem }}</div>
                        @endif
                        @if ($envio->correo_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Correo del Remitente:</font><br> {{ $envio->correo_rem }}</div>
                        @endif
                        @if ($envio->telefono_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Teléfono del Remitente:</font><br> {{ $envio->telefono_rem }}</div>
                        @endif
                        @if ($envio->estado_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Estado del Remitente:</font><br> {{ $estador->nombre }}</div>
                        @endif
                        @if ($envio->municipio_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Municipio del Remitente:</font><br> {{ $municipior->nombre }}</div>
                        @endif
                        @if ($envio->parroquia_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Parroquia del Remitente:</font><br> {{ $parroquiar->nombre }}</div>
                        @endif
                        @if ($envio->direccion_rem)
                        <div class="px-4 py-2"><font class="font-extrabold text-primary">Dirección del Remitente:</font><br> {{ $envio->direccion_rem }}</div>
                        @endif
                    </div>
                    
                    <div class="px-4 py-2">
                    @if ($envio->nombre_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Destinatario:</font> {{ $envio->nombre_dest }}</div>
                    @endif

                    @if ($envio->codigo_postal_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Código Postal Destinatario:</font><br> {{ $envio->codigo_postal_dest }}</div>
                    @endif
                    @if ($envio->correo_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Correo del Destinatario:</font><br> {{ $envio->correo_dest }}</div>
                    @endif
                    @if ($envio->tlf_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Teléfono del Destinatario:</font><br> {{ $envio->tlf_dest }}</div>
                    @endif
                    @if ($envio->continente_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Continente del Destinatario:</font><br> {{ $continente->nombre }}</div>
                    @endif
                    @if ($envio->pais_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">País del Destinatario:</font><br> {{ $pais->nombre }}</div>
                    @endif
                    @if ($envio->estado_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Estado del Destinatario:</font><br> {{ $estadod->nombre }}</div>
                    @endif
                    @if ($envio->municipio_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Municipio del Destinatario:</font><br> {{ $municipiod->nombre }}</div>
                    @endif
                    @if ($envio->parroquia_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Parroquia del Destinatario:</font><br> {{ $parroquiad->nombre }}</div>
                    @endif
                    @if ($envio->direccion_dest)
                    <div class="px-4 py-2"><font class="font-extrabold text-primary">Dirección Destino:</font><br> {{ $envio->direccion_dest }}</div>
                    @endif
                    </div>
                
            </tbody>
        </table>
    </div>
</div>
</div>

