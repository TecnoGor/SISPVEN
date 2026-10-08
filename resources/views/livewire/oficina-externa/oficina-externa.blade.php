<div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @section('titulo') Oficinas Aliadas @endsection
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="mb-6 text-left">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">
            Oficinas Aliadas
        </h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y registro de sedes externas.</p>
    </div>

    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 mt-6">
        
        {{-- BARRA DE HERRAMIENTAS --}}
        <div class="flex flex-col md:flex-row items-center justify-between p-6 gap-4 border-b border-gray-100">
            {{-- Buscador --}}
            <div class="flex w-full md:w-1/3">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                        placeholder="Buscar oficina...">
                </div>
            </div>

            {{-- Botón Crear --}}
            <div class="w-full md:w-auto">
                <x-primary-button class="w-full md:w-auto px-4 py-2.5 text-sm rounded-lg shadow-sm justify-center" wire:click="crear">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear oficina
                </x-primary-button>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase bg-gray-100 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-center font-bold tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-center font-bold tracking-wider">Fecha de creación</th>
                        <th class="px-6 py-4 text-center font-bold tracking-wider">Acciones</th> {{-- NUEVA COLUMNA --}}
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($externas as $index => $externa)
                        <tr wire:key="{{ $externa->oficina_id }}" class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 text-gray-900 text-center font-medium">{{ $externa->nombre }}</td>
                            <td class="px-6 py-4 text-gray-600 text-center">{{ $externa->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="editar({{ $externa->oficina_id }})"
                                    class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-500 italic text-lg bg-gray-50">
                                No hay oficinas Aliadas registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-auto">
                {{ $externas->links() }}
            </div>
        </div>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="paginacion" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="paginacion" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    {{-- MODAL (CREAR) --}}
    @if ($modal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
            <div class="inline-block w-full max-w-2xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                <form wire:submit.prevent="crear_oficina">
                    {{-- ENCABEZADO --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Crear Oficina Aliada</h3>
                        </div>
                        <button type="button" wire:click="cerrarModalCrear" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- CUERPO --}}
                    <div class="p-6 text-left max-h-[70vh] overflow-y-auto">

                        {{-- Seccion: Datos de la oficina --}}
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos de la Oficina</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" wire:model="nombre" placeholder="Ej: Oficina MRW Caracas" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select wire:model.live="estado" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    <option value="">Seleccione un estado</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                                <select wire:model.live="municipio" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    <option value="">Seleccione un municipio</option>
                                    @foreach ($municipios as $municipio)
                                        <option value="{{ $municipio->municipio_id }}">{{ $municipio->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('municipio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Parroquia</label>
                                <select wire:model.live="parroquia" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    <option value="">Seleccione una parroquia</option>
                                    @foreach ($parroquias as $parroquia)
                                        <option value="{{ $parroquia->parroquia_id }}">{{ $parroquia->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('parroquia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Codigo Postal</label>
                                <select wire:model="codigo_postal" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    <option value="">Seleccione un Codigo</option>
                                    @foreach ($codigos as $codigo)
                                        <option value="{{ $codigo->codigo_postal }}">{{ $codigo->codigo_postal }}</option>
                                    @endforeach
                                </select>
                                @error('codigo_postal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Seccion: Informacion del aliado --}}
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 border-t border-gray-200 pt-4">Informacion del Aliado</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RIF</label>
                                <input type="text" wire:model="RIF" placeholder="Ej: J123456789" maxlength="10" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('RIF') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numero de Contrato</label>
                                <input type="text" wire:model="nro_contrato" placeholder="Ej: CONT-2026-001" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('nro_contrato') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Contratacion</label>
                                <input type="date" wire:model="fecha_contratacion" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('fecha_contratacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa Aplicada</label>
                                <input type="number" step="0.01" wire:model="tarifa_aplicada" placeholder="Ej: 15.50" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('tarifa_aplicada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Facturacion</label>
                                <input type="text" wire:model="tipo_facturacion" placeholder="Ej: Mensual" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('tipo_facturacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Entrega (Zona)</label>
                                <input type="text" wire:model="tiempo_entrega_zona" placeholder="Ej: 2-3 dias habiles" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('tiempo_entrega_zona') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Entrega (Estado)</label>
                                <input type="text" wire:model="tiempo_entrega_estado" placeholder="Ej: 5-7 dias habiles" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                @error('tiempo_entrega_estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- PIE DE MODAL --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button type="submit" class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm">
                            Guardar
                        </x-button>
                        <x-button type="button" wire:click="cerrarModalCrear" class="px-4 py-2 bg-gray-200 text-white-700 hover:bg-gray-300 rounded-lg font-medium text-sm transition">
                            Cancelar
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL (EDITAR OFICINA) --}}
    @if ($modal_editar)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-2xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <form wire:submit.prevent="actualizar_oficina">
                        {{-- ENCABEZADO --}}
                        <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-800">Editar Oficina Aliada</h3>
                            </div>
                            <button type="button" wire:click="cerrarModalEditar" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- CUERPO --}}
                        <div class="p-6 text-left max-h-[70vh] overflow-y-auto">

                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Datos de la Oficina</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" wire:model="editar_nombre" placeholder="Ej: Oficina MRW Caracas" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 border-t border-gray-200 pt-4">Informacion del Aliado</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">RIF</label>
                                    <input type="text" wire:model="editar_RIF" placeholder="Ej: J123456789" maxlength="10" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_RIF') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numero de Contrato</label>
                                    <input type="text" wire:model="editar_nro_contrato" placeholder="Ej: CONT-2026-001" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_nro_contrato') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Contratacion</label>
                                    <input type="date" wire:model="editar_fecha_contratacion" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_fecha_contratacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa Aplicada</label>
                                    <input type="number" step="0.01" wire:model="editar_tarifa_aplicada" placeholder="Ej: 15.50" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_tarifa_aplicada') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Facturacion</label>
                                    <input type="text" wire:model="editar_tipo_facturacion" placeholder="Ej: Mensual" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_tipo_facturacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Entrega (Zona)</label>
                                    <input type="text" wire:model="editar_tiempo_entrega_zona" placeholder="Ej: 2-3 dias habiles" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_tiempo_entrega_zona') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Entrega (Estado)</label>
                                    <input type="text" wire:model="editar_tiempo_entrega_estado" placeholder="Ej: 5-7 dias habiles" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm">
                                    @error('editar_tiempo_entrega_estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- PIE DE MODAL --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button type="submit" class="px-4 py-2 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm">
                                Guardar
                            </x-button>
                            <x-button type="button" wire:click="cerrarModalEditar" class="px-4 py-2 bg-gray-200 text-white-700 hover:bg-gray-300 rounded-lg font-medium text-sm transition">
                                Cancelar
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        // success alert Livewire
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertSuccess2', message => {
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

