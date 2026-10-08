@section('titulo')
    Detalle de Proveedor
@endsection
        
<style>
    .input-group {
        margin-bottom: 1rem;
    }
    .input-style {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        background-color: #f9fafb;
        color: #4a5568;
        font-size: 1rem;
        max-width: 600px; /* Ancho máximo para los inputs */
    }
    .input-style:readonly {
        background-color: #f9fafb;
        cursor: not-allowed;
    }
    .max-w-4xl {
        max-width: 100%; /* Ajusta este valor según el ancho deseado */
    }
    .table-container {
        max-width: 100%;
    }
</style>

<div>
    <div class="flex justify-center items-center mb-4 space-x-4">
        <div class="bg-white rounded-lg shadow-md p-1">
            <x-return-link :href="route('ver-proveedores')" wire:navigate.hover />
        </div>
        <h1 class="text-3xl text-gray-100 font-bold">Detalles del Proveedor</h1>
    </div>
    <div class="flex flex-col items-center justify-center py-8 px-4">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden max-w-4xl w-full mx-auto transform transition duration-300 hover:scale-105">
            <!-- Encabezado con Gradiente y Icono -->
            <div class="bg-gradient-to-r from-primary to-secondary p-6 text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white rounded-full p-3 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                          </svg>
                          
                    </div>
                </div>
                <h2 class="text-4xl font-extrabold text-white">Información del Proveedor</h2>
            </div>
            
            <!-- Información Detallada en Tarjetas -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 bg-gray-50">
                <!-- Información del Representante -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-2xl font-semibold text-primary mb-4">Información del Representante</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Representante:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->representante_legal }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Cédula:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->cedula }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Teléfono:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->telefono }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Correo:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->correo }}</span>
                        </li>
                    </ul>
                </div>
            
                <!-- Información del Proveedor -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-2xl font-semibold text-primary mb-4">Datos del Proveedor</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">RIF:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->rif }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Razón Social:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->razon_social }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Dirección Fiscal:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->direccion_fiscal }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Retención:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->retencion ?? 'N/A' }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Contribuyente:</span>
                            <span class="text-gray-900 font-medium">{{ $proveedor->contribuyente_especial ?? 'N/A' }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-gray-500 font-semibold w-36">Estatus:</span>
                            <span class="text-lg font-semibold {{ $proveedor->activo ? 'text-green-600' : 'text-red-600' }}">
                                {{ $proveedor->activo ? 'ACTIVA' : 'INACTIVA' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
    
            <!-- Mensaje de Proveedor No Encontrado -->
            @if(!$proveedor)
                <p class="text-red-500 mt-6 text-center font-bold">No se encontró el Proveedor.</p>
            @endif
        </div>
    </div>
    

    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
        <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6 table-container max-w-4xl mx-auto">
            
            <livewire:chofer.chofer-mostrar :proveedor_id="$proveedor->proveedor_id" />
            
            <!-- Línea de separación -->
            <hr class="my-4 border-2 border-gray-300" />
            <hr class="my-2 border-gray-300" />
            
            <livewire:vehiculos.crear-vehiculos :proveedor_id="$proveedor->proveedor_id" />
            
            <!-- Línea de separación -->
            <hr class="my-4 border-2 border-gray-300" />
            <hr class="my-2 border-gray-300" />
            
            <livewire:rutas.rutas-proveedores :proveedor_id="$proveedor->proveedor_id" />
    
        </div>
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('tarifaUpdated', () => {
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
    </script>
@endpush
