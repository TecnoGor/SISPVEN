@section('titulo')
    Apartados
@endsection

<di>
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
            Apartados Postales
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Administración y control de apartados postales, estados operativos y asignación de clientes.
        </p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y BOTONES DE ACCIÓN --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-1/3">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar por código, cliente o documento...">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                    {{-- @can('Generar Apartados')
                            <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="openModal">
                                GENERAR CÓDIGOS
                            </x-primary-button>
                        @endcan --}}

                        <x-primary-button class="px-3 py-2 text-sm rounded-md shadow-sm" wire:click="reporte_excel">
                            REPORTE EXCEL
                        </x-primary-button>

                        <x-primary-button wire:click="exportarPdf" wire:loading.attr='disabled' wire:target='exportarPdf'
                            class="px-3 py-2 text-sm rounded-md shadow-sm">
                            DESCARGAR PDF
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- FILTROS DE FECHA --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start border-t border-gray-100 pt-4">
                <div class="w-full md:w-1/4">
                    <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            @include('livewire.includes.sort-table', ['column' => 'apartado', 'displayName' => 'Codigo'])
                            @include('livewire.includes.sort-table', ['column' => 'operativo', 'displayName' => 'Condicion'])
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cambio Condición</th>
                            @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Nro Documento'])
                            @include('livewire.includes.sort-table', ['column' => 'documento', 'displayName' => 'Cliente'])
                            @include('livewire.includes.sort-table', ['column' => 'dias', 'displayName' => 'Dias Restantes'])
                            @include('livewire.includes.sort-table', ['column' => 'estatus', 'displayName' => 'Estatus'])
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acción Estatus</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($apartadosp as $apartado)
                            <tr wire:key="{{ $apartado->codigo_apartado_id }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-gray-900">
                                    {{ $apartado->apartado ?: 'no disponible' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $apartado->operativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $apartado->operativo ? 'Operativo' : 'Inoperativo' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if ($apartado->operativo)
                                        <button wire:click="inoperativo({{ $apartado->codigo_apartado_id }})" title="Marcar Inoperativo" class="text-green-600 hover:text-green-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="operativo({{ $apartado->codigo_apartado_id }})" title="Marcar Operativo" class="text-red-600 hover:text-red-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-left text-gray-600">
                                    @php
                                        $registro = $apartado->registro_apartado()->where('activo', true)->latest()->first();
                                    @endphp
                                    {{ $registro ? $registro->tipo_documento . '-' . $registro->documento : 'No existe cliente actual' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-left text-gray-600">
                                    {{ $registro ? $registro->nombre . ' ' . $registro->apellido : 'No existe cliente actual' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center font-bold {{ (isset($dias_faltantes[$apartado->codigo_apartado_id]) && $dias_faltantes[$apartado->codigo_apartado_id] < 5) ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ isset($dias_faltantes[$apartado->codigo_apartado_id]) ? $dias_faltantes[$apartado->codigo_apartado_id] : '--' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $apartado->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $apartado->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if ($apartado->activo)
                                        <button wire:click="desactivar({{ $apartado->codigo_apartado_id }})" title="Desactivar" class="text-green-600 hover:text-green-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="activar({{ $apartado->codigo_apartado_id }})" title="Activar" class="text-red-600 hover:text-red-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-gray-500 text-lg italic bg-gray-50 text-center">
                                    No hay apartados postales registrados en este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="py-4 px-3">
                {{ $apartadosp->links() }}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4 mb-10">
        <div class="py-1 px-3 flex items-center justify-start gap-4">
            <label for="perPage" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage"
                class="block w-24 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-2 bg-white">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    @if ($modalOpen)
        <div class="bg-gray-800 bg-opacity-25 fixed inset-0 flex items-center justify-center">
            <div class="max-w-lg w-full sm:px-6 lg:px-12 mt-14">
                <div class="bg-white shadow rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!-- Estado -->
                        <div class="mb-5 col-span-3">
                            <label for="apartado" class="block text-sm font-medium text-gray-700">Cantidad de
                                Apartados<span class="text-red-500">*</span></label>
                            <input type="text" wire:model="cantidad"
                                class="mt-1 block w-1/2 border border-gray-300 rounded-md shadow-sm focus:ring-red-700 focus:border-red-700 sm:text-sm">
                            @error('cantidad')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-between gap-4">
                        <div class="flex justify-center w-full">
                            <x-button class="w-1/2" wire:click="generarApartados" wire:loading.attr='disabled'
                                wire:target='generarApartados, submit'>
                                Generar
                            </x-button>
                        </div>

                        <div class="flex justify-center w-full">
                            <x-button class="w-1/2 bg-red-700 text-white hover:bg-red-600" wire:click="closeModal"
                                type="button">
                                Cancelar
                            </x-button>
                        </div>
                    </div>
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
                    timer: 1500
                });
            })
        </script>
    @endscript
@endpush
