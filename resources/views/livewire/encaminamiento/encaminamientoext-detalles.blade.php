@section('titulo')
    Detalles de Rastreo y Seguimiento
@endsection
<div >
    
    <div class="flex justify-center items-center mb-4 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('encaminamiento.encaminamientoext')" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-primary font-bold">Detalles del Rastreo y Seguimiento</h1>
    </div>

    <div class="max-w-7xl mx-auto my-5 sm:px-6 lg:px-8">
    
    <div class="mb-4 sm:mb-0">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Código de Envío: {{ $envio->codigo_envio }}</h1>
    </div>

    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg border border-gray-200 rounded-lg overflow-scroll md:overflow-hidden mt-4 p-16">
        <h2 class="text-2xl font-bold my-5">Información del Envío: </h2>
        <table class="min-w-full text-lg">
            <tbody>
                <tr>
                    <td class="px-4 py-2"><font class="font-extrabold text-primary">Origen:</font> {{ $envio->oficinas->nombre }}</td>
                    <td class="px-4 py-2"><font class="font-extrabold text-primary">Remitente:</font> {{ $envio->nombre_rem }} {{ $envio->apellido_rem }}</td>
                </tr>
                <tr>
                    <td class="px-4 py-2"><font class="font-extrabold text-primary">Dirección:</font> {{ $envio->direccion_dest }}</td>
                </tr>
                
            </tbody>
        </table>
    
    <div class="overflow-scroll md:overflow-hidden mt-10">
        <h2 class="text-2xl font-bold mb-5">Rastreo y Seguimiento del Envío: </h2>
        <div class="grid grid-cols-3 justify-items-center items-center">

        @php
            $transitoaux = false;
        @endphp

        @foreach ($resultados as $tr)
                
            <div class="justify-self-end">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-9 text-green-800">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="px-4 py-1 font-semibold text-lg col-span-2 justify-self-start ml-3.5">
                @if ($tr->envios_estatus_id >= 4 && $tr->envios_estatus_id <= 11) 
                    @if (!$transitoaux)  
                        <font class="text-green-900">EN TRÁNSITO A SU DESTINO</font>
                        <div>{{ $tr->created_at }}</div>
                        @php
                            $enTransitoMostrado = true; // Cambia a true después de mostrar
                        @endphp
                    @endif
                    @elseif ($tr->envios_estatus_id == 3)
                        <font class="text-green-900">EN TRÁNSITO A SU DESTINO</font>
                        <div>{{ $tr->created_at }}</div>
                    @else
                        {{ $tr->nombre_oficina }} - <font class="text-green-900">{{ $tr->estatus }}</font>
                        <div>{{ $tr->created_at }}</div>
                @endif
            </div>
            
            @if ($tr === $resultados->last()) @else
                <div class="border-4 border-green-800 rounded-lg h-10 w-min justify-self-end mr-3.5"></div>
                <div class="col-span-2"></div>
            @endif
            
            

        @endforeach
        </div>
    </div>
</div>
</div>

</div>