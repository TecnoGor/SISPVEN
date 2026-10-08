<div>
    @section('titulo')
       Registro Entrada
    @endsection
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl md:text-3xl text-primary font-bold">Registro de Entrada</h1>
            <x-primary-button 
                wire:click="create()">
                Registrar entrada
            </x-primary-button>    
        </div>
        <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
            <div class="flex flex-col md:flex-row gap-2 items-center justify-center p-4">
                <!-- Barra de búsqueda para código de envío con botón -->
                <div class="flex flex-col w-full items-center gap-2">
                    <label for="codigoEnvioBusqueda" class="text-sm font-medium text-gray-700">
                        Buscar código de envío:
                    </label>
                    <div class="flex gap-2 w-4/5 md:w-3/4 xl:w-2/3">
                        <input type="text" id="codigoEnvioBusqueda" wire:model.live="codigoEnvioBusqueda"  
                            placeholder="Colocar código de envio"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-md 
                                   focus:ring-default focus:border-default flex-1 p-2">
                        
                        <button wire:click="agregarEnvios"
                                class="bg-primary hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md"
                                @disabled(!$codigoValido)>
                                Agregar
                        </button>
                    </div>

                    <p class="text-sm font-semibold 
                    @if($mensajeBusqueda == 'Envío conseguido') text-green-600 
                    @else text-red-600 @endif">
                    {{ $mensajeBusqueda }}
                </p>
                    <!-- Mensaje de resultado de la búsqueda -->
                        <table class="w-full border-collapse border border-gray-300 mt-2">
                            <thead class="bg-gray-200">
                                <tr>
                                    @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código'])
                                    @include('livewire.includes.sort-table', ['column' => 'contenido', 'displayName' => 'Contenido'])
                                    @include('livewire.includes.sort-table', ['column' => 'peso', 'displayName' => 'Peso'])
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Acción</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @forelse($envios as $envio)
                                <tr class="bg-white border border-gray-300">
                                    <td class="px-4 py-3 text-black">{{ $envio->codigo_envio }}</td>
                                    <td class="px-4 py-3 text-black">{{ $envio->contenido ?? '—' }}</td>
                                    <td class="px-4 py-3 text-black">{{ $envio->peso }} kg</td>
                                    <td class="px-4 py-3 text-black">
                                        <button wire:click="eliminarEnvio({{ $envio->envio_id }})" class="text-red-600 hover:text-red-800 font-semibold text-sm">Eliminar</button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">No hay envíos agregados.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            
                        </table>
                </div>
            </div>
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
                timer: 1500
            });
        })

        
        Livewire.on('tarifaUpdated', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
    @endscript
@endpush
