@section('titulo')
    Parámetros de Valijas
@endsection
<div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
        <div>
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
                Parámetros de Valijas
            </h1>
            <p class="text-sm text-gray-500">Administra los tipos de valija y los servicios que cada uno acepta.</p>
        </div>
    </div>

    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
        <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
            <div class="flex w-full md:w-auto gap-2">
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full md:w-72 p-2"
                    placeholder="Buscar por nombre...">

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="mostrarInactivos" class="rounded">
                    Mostrar inactivos
                </label>
            </div>

            <button wire:click="abrirModalCrear"
                class="bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition text-sm font-semibold">
                + Nuevo Tipo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Nombre Referencial</th>
                        <th class="px-4 py-2">Certificado</th>
                        <th class="px-4 py-2">Carga por peso</th>
                        <th class="px-4 py-2">Servicios</th>
                        <th class="px-4 py-2">Activo</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiposSaca as $tipo)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $tipo->nombre }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $tipo->nombre_referencial }}</td>
                            <td class="px-4 py-2">
                                @if ($tipo->certificado)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Sí</span>
                                @else
                                    <span class="text-gray-400 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if ($tipo->cargar_por_peso)
                                    <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded">Sí</span>
                                @else
                                    <span class="text-gray-400 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if ($tipo->servicios->isEmpty())
                                    <span class="text-gray-400 italic text-xs">Sin servicios</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($tipo->servicios as $s)
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded" title="{{ $s->nombre }}">
                                                {{ $s->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <button wire:click="toggleActivo({{ $tipo->tipo_saca_id }})"
                                    class="relative inline-flex items-center h-6 rounded-full w-11 transition {{ $tipo->activo ? 'bg-green-500' : 'bg-gray-300' }}">
                                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition {{ $tipo->activo ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </td>
                            <td class="px-4 py-2">
                                <button wire:click="abrirModalEditar({{ $tipo->tipo_saca_id }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500 italic">
                                No hay tipos de saca para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de creación / edición --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-primary">
                        {{ $tipoSacaIdEditando ? 'Editar Tipo de Valija' : 'Nuevo Tipo de Valija' }}
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nombre"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-default focus:border-default"
                            placeholder="Ej: Cartas/Pequeños Paquetes">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Referencial</label>
                        <input type="text" wire:model="nombreReferencial"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-default focus:border-default"
                            placeholder="Si se deja vacío, se usa el nombre">
                        @error('nombreReferencial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="certificado" id="certificado" class="rounded">
                        <label for="certificado" class="text-sm font-medium text-gray-700">Certificado</label>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="cargarPorPeso" id="cargarPorPeso" class="rounded">
                            <label for="cargarPorPeso" class="text-sm font-medium text-gray-700">
                                Carga por peso (no admite envíos)
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 ml-6">
                            La valija se despacha indicando solo su peso. No se le pueden agregar
                            envíos y se cierra vacía para darle salida y entrada.
                        </p>
                        @error('cargarPorPeso') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Servicios permitidos</label>
                        <div class="border border-gray-200 rounded-lg p-3 max-h-60 overflow-y-auto bg-gray-50">
                            @forelse ($servicios as $servicio)
                                <label class="flex items-center gap-2 py-1 text-sm hover:bg-white px-2 rounded cursor-pointer">
                                    <input type="checkbox"
                                        wire:model="serviciosSeleccionados"
                                        value="{{ $servicio->servicio_id }}"
                                        class="rounded">
                                    <span>{{ $servicio->nombre }}</span>
                                </label>
                            @empty
                                <p class="text-gray-500 italic text-sm">No hay servicios activos ó envíos disponibles.</p>
                            @endforelse
                        </div>
                        @error('serviciosSeleccionados') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @error('serviciosSeleccionados.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-6 border-t flex justify-end gap-2 bg-gray-50">
                    <button wire:click="cerrarModal"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                        Cancelar
                    </button>
                    <button wire:click="guardar"
                        class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:opacity-90">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
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

        Livewire.on('alertError', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
@endpush
