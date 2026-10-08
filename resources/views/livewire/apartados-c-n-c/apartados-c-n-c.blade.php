@section('titulo')
    Apartados
@endsection

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Apartados Postales CNC</h1>

            </div>
        </div>

        <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
            <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
                <div class="flex w-full md:w-auto">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="currentColor"
                                viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2"
                            placeholder="Buscar...">

                    </div>
                </div>
                <x-button class="mb-6 sm:mb-0" wire:click="exportarApartadosCNC" wire:loading.attr='disabled'
                    wire:target='exportarApartadosCNC'>
                    REPORTE EXCEL
                </x-button>
                <x-button class="mb-6 sm:mb-0" wire:click="exportarPdf" wire:loading.attr='disabled'
                    wire:target='exportarPdf'>
                    DESCARGAR PDF
                </x-button>
            </div>
            <div class="flex flex-col md:flex-row gap-2 mb-2 ml-2">
                <div>
                    <label for="desde" class="block text-sm font-medium text-gray-700 mt-4">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
                </div>
                <div>
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mt-4">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2">
                </div>
                <div>
                    <label for="oficina_selec" class="block text-sm font-medium text-gray-700 mt-4">OPT</label>
                    <select id="oficina_selec" wire:model.live="oficina_selec"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="" selected>Seleccione una OPT:</option>
                        @foreach ($oficinas_encon as $oficina)
                            <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-default uppercase bg-gray-100">
                        <tr>
                            @include('livewire.includes.sort-table', [
                                'column' => 'apartado',
                                'displayName' => 'Codigo',
                            ])
                            @include('livewire.includes.sort-table', [
                                'column' => 'operativo',
                                'displayName' => 'Condicion',
                            ])
                            @include('livewire.includes.sort-table', [
                                'column' => 'nombre',
                                'displayName' => 'Nro Documento',
                            ])
                            @include('livewire.includes.sort-table', [
                                'column' => 'documento',
                                'displayName' => 'Cliente',
                            ])
                            @include('livewire.includes.sort-table', [
                                'column' => 'dias',
                                'displayName' => 'Dias Restantes',
                            ])
                            @include('livewire.includes.sort-table', [
                                'column' => 'estatus',
                                'displayName' => 'Estatus',
                            ])
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apartadosp as $apartado)
                            <tr wire:key="{{ $apartado->codigo_apartado_id }}" class="border-b text-left">
                                <th class="px-4 py-2 font-medium text-black text-center">
                                    {{ $apartado->apartado ?: 'no disponible' }}</th>
                                <th
                                    class="px-4 py-3 font-medium {{ $apartado->operativo ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $apartado->operativo ? 'Operativo' : 'Inoperativo' }}
                                </th>

                                <!-- Mostrar tipo de documento y número si existe un registro activo -->
                                <th class="px-4 py-2 font-medium text-black text-left">
                                    @php
                                        $registro = $apartado
                                            ->registro_apartado()
                                            ->where('activo', true)
                                            ->latest()
                                            ->first();
                                    @endphp
                                    @if ($registro)
                                        {{ $registro->tipo_documento . '-' . $registro->documento }}
                                    @else
                                        No existe cliente actual
                                    @endif
                                </th>

                                <!-- Mostrar nombre y apellido si existe un registro activo -->
                                <th class="px-4 py-2 font-medium text-black text-left">
                                    @php
                                        $registro = $apartado
                                            ->registro_apartado()
                                            ->where('activo', true)
                                            ->latest()
                                            ->first();
                                    @endphp
                                    @if ($registro)
                                        {{ $registro->nombre . ' ' . $registro->apellido }}
                                    @else
                                        No existe cliente actual
                                    @endif
                                </th>

                                <th class="px-4 py-2 font-medium text-black text-center">
                                    {{ isset($dias_faltantes[$apartado->codigo_apartado_id]) ? $dias_faltantes[$apartado->codigo_apartado_id] : '--' }}
                                </th>

                                <th
                                    class="px-4 py-3 font-medium {{ $apartado['activo'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $apartado->activo ? 'Activo' : 'Inactivo' }}
                                </th>

                            </tr>
                        @empty
                            <tr class="border-b text-right">
                                <th colspan="8" class="py-7 text-default text-2xl text-center">No hay apartados
                                    postales registrados</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3">
                <select wire:model.live="perPage" id="paginacion"
                    class="mt-1 block w-1/4 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="75">75</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="py-4 px-3">
                {{ $apartadosp->links() }}
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
        </script>
    @endscript
@endpush
