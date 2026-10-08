<div>
    @section('titulo')
        Registro Entrada
    @endsection
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl md:text-3xl text-primary font-bold">Registro de Entrada</h1>
            <x-primary-button wire:click="create()" wire:loading.attr="disabled" wire:target="create">
                <span wire:loading.remove wire:target="create">Registrar entrada</span>
                <span wire:loading wire:target="create">Registrando...</span>
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
                            placeholder="Colocar código de despacho o de valija"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-md 
                                focus:ring-default focus:border-default flex-1 p-2">
                        
                        <!-- Botón Agregar -->
                        <button wire:click="agregarEnvios"
                            class="bg-primary hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md"
                            @if(empty($enviosIds) && empty($sacasIds)) disabled class="opacity-50 cursor-not-allowed" @endif>
                            Agregar
                        </button>
                    </div>

                    <!-- Mensaje de resultado de la búsqueda -->
                    <p class="text-sm font-semibold 
                        @if($mensajeBusqueda == 'Envío conseguido' || $mensajeBusqueda == 'Valija conseguida') text-green-600 
                        @else text-red-600 @endif">
                        {{ $mensajeBusqueda }}
                    </p>

                    @if (count($enviosDetalles))
                        <table class="w-full border-collapse border border-gray-300 mt-2">
                            <thead class="bg-gray-200">
                                <tr>
                                    @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'codigo'])
                                    @include('livewire.includes.sort-table', ['column' => 'email', 'displayName' => 'Contenido'])
                                    @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Peso'])
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enviosDetalles as $envio)
                                    <tr class="bg-white border border-gray-300">
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $envio->codigo_envio }}</th>
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $envio->contenido }}</th>
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $envio->peso }}gr</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if (count($sacasDetalles))
                        <table class="w-full border-collapse border border-gray-300 mt-4">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-black text-left">Valija</th>
                                    <th class="px-4 py-3 font-semibold text-black text-left">Tipo</th>
                                    <th class="px-4 py-3 font-semibold text-black text-left">Peso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sacasDetalles as $saca)
                                    <tr class="bg-white border border-gray-300">
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $saca->codigo_saca }}</th>
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $saca->tipoSaca->nombre ?? '-' }}</th>
                                        <th class="px-4 py-3 font-medium text-black text-left">{{ $saca->peso ?? 0 }}gr</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div> 
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
@script
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recarga automática la primera vez que se entra al módulo
        if (!sessionStorage.getItem('reloadDone')) {
            sessionStorage.setItem('reloadDone', 'true');
            location.reload();
            return; // Previene ejecución duplicada
        }

        // Listener para reemplazar comillas simples por guiones
        const inputBusqueda = document.getElementById('codigoEnvioBusqueda');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', function() {
                const originalValue = this.value;
                const newValue = originalValue.replace(/'/g, '-');
                if (newValue !== originalValue) {
                    this.value = newValue;
                    this.dispatchEvent(new Event('input'));
                }
            });
        }
    });

    // Mensaje de éxito
    Livewire.on('alertSuccess', message => {
        Swal.fire({
            position: "center",
            icon: "success",
            title: message.message,
            showConfirmButton: false,
            timer: 1500
        });
    });

    // Mensaje de error
    Livewire.on('alertError', message => {
        Swal.fire({
            position: "center",
            icon: "error",
            title: message.message,
            showConfirmButton: true,
        });
    });

    // Recargar si se actualiza tarifa
    Livewire.on('tarifaUpdated', () => {
        setTimeout(() => {
            location.reload();
        }, 1000);
    });
</script>
@endscript
@endpush
