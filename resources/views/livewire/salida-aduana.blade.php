<div class="px-6 md:px-24 lg:px-32 xl:px-48"> <!-- Más margen lateral -->

    @section('titulo')
        Aduana Salida
    @endsection

    @php
        $usuario = auth()->user();
        $oficina = \App\Models\Oficina::find($usuario->oficina_id);
    @endphp

    <h1 class="text-2xl font-bold mb-8 text-left text-primary">Registro de Salida Hacia {{ $oficina->nombre }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        {{-- Envíos Disponibles --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <h2 class="text-xl font-semibold mb-6 text-gray-700">Envíos Disponibles</h2>

            <div class="mb-6">
                <input
                    type="text"
                   wire:model.live.debounce.300ms="busqueda"
                    placeholder="Buscar por código de envío..."
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                >
            </div>

            @if ($this->enviosDisponibles->isEmpty())
                <p class="text-center text-gray-500">No hay envíos disponibles.</p>
            @else
                <div class="space-y-4">
                    @foreach ($this->enviosDisponibles as $envio)
                        <button
                            wire:click="seleccionarEnvio({{ $envio->envio_id }})"
                            wire:key="disponible-{{ $envio->envio_id }}"
                            class="w-full text-left p-5 bg-gray-50 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition"
                        >
                            <div class="text-lg font-semibold text-gray-800">Código de Envío: {{ $envio->codigo_envio }}</div>
                            <div class="text-sm text-gray-600 mt-1">Contenido: {{ $envio->contenido }}</div>
                            <div class="text-sm text-gray-500">Peso: {{ $envio->peso }} gr</div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Envíos Seleccionados --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <h2 class="text-xl font-semibold mb-6 text-gray-700">Envíos para Salida</h2>

            @if ($enviosSeleccionados->isEmpty())
                <p class="text-center text-gray-500">No hay envíos seleccionados.</p>
            @else
                <div class="space-y-4">
                    @foreach ($enviosSeleccionados as $envio)
                        <button
                            wire:click="removerEnvio({{ $envio->envio_id }})"
                            wire:key="seleccionado-{{ $envio->envio_id }}"
                            class="w-full text-left p-5 bg-red-50 border border-red-300 rounded-lg shadow-sm hover:bg-red-100 transition"
                        >
                            <div class="text-lg font-semibold text-red-700">Código de Envío: {{ $envio->codigo_envio }}</div>
                            <div class="text-sm text-gray-600 mt-1">Contenido: {{ $envio->contenido }}</div>
                            <div class="text-sm text-gray-500">Peso: {{ $envio->peso }} gr</div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Botón para procesar salida --}}
    <div class="mt-12 text-center">
        <x-primary-button
            wire:click="procesarSalida"
            :disabled="$enviosSeleccionados->isEmpty()"
            class="px-8 py-3 text-lg"
        >
            Registrar Salida
        </x-primary-button>
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
