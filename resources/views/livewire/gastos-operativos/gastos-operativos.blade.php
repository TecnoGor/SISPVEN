@section('titulo')
    Gastos Operativos
@endsection
<div class="max-w-[95%] mx-auto">
    {{-- ENCABEZADO --}}
    <div class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
                    Gastos Operativos {{$oficina->nombre}}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Gestión y detalle de los gastos operativos de la oficina.</p>
            </div>
            {{-- Botón de Reporte Excel --}}
            <x-button class="w-fit px-5 py-2 text-sm font-bold rounded-md shadow-sm" 
                wire:click="exportarGastosOperativos" wire:loading.attr='disabled' wire:target='exportarGastosOperativos'>
                REPORTE EXCEL
            </x-button>
        </div>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-2">
            
            {{-- BARRA DE BÚSQUEDA Y BOTONES DE ACCIÓN --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-1/2">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                    placeholder="Buscar gasto...">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end">
                        <x-primary-button wire:click="crear" class="px-5 py-2 text-sm font-bold rounded-md shadow-sm">
                            Crear Tipo de Gasto
                        </x-primary-button>

                        <x-primary-button class="px-4 py-2 text-sm rounded-md shadow-sm" wire:click="ingresar">
                            Ingresar Gasto
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- FILTROS DE FECHA --}}
            <div class="flex flex-col md:flex-row gap-4 mb-6 items-end justify-start">
                <div class="w-full md:w-1/4">
                    <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Gasto Operativo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Monto</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($gastos as $index => $gasto)
                            <tr wire:key="{{ $gasto->gasto_operativo_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $gasto->tipo_gasto->tipo_gasto_operativo}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center font-semibold">{{ number_format($gasto->monto, 2, ',', '.')}} Bs</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $gasto->created_at->format('d/m/Y')}}</td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="3" class="py-10 text-gray-500 text-lg italic bg-gray-50">No hay registros de gastos operativos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN LINKS --}}
            <div class="py-4 px-3 flex items-center justify-end">
                {{$gastos->links()}}
            </div>
        </div>
    </div>

    {{-- REGISTROS POR PÁGINA --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4"> 
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="page" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    {{-- MODAL: CREAR TIPO DE GASTO --}}
    @if($crear_gasto)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9-4h18c1.104 0 2 .896 2 2v8c0 1.104-.896 2-2 2H3c-1.104 0-2-.896-2-2v-8c0-1.104.896-2 2-2z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Crear Tipo de Gasto</h3>
                        </div>
                        <button type="button" wire:click="cerrar" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <label for="nuevo_gasto" class="block text-sm font-medium text-gray-700 mb-1">Introducir Nuevo Tipo de Gasto<span class="text-red-500">*</span></label>
                        <input type="text" id="nuevo_gasto" wire:model="nuevo_gasto" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                        @error('nuevo_gasto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button wire:click="crear_nuevo_gasto" wire:loading.attr='disabled' wire:target='crear_nuevo_gasto'
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Crear
                        </x-button>
                        <x-button wire:click="cerrar" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Cerrar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: INGRESAR GASTO --}}
    @if($ingresar_gasto)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Ingresar Nuevo Gasto Operativo</h3>
                        </div>
                        <button type="button" wire:click="cerrar" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label for="gasto_selec" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Gasto<span class="text-red-500">*</span></label>
                            <select id="gasto_selec" wire:model.live="gasto_selec" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                <option value="" selected>Seleccionar tipo de gasto</option>
                                @foreach ($tipos_gastos as $gasto)
                                    <option value="{{$gasto->tipo_gasto_operativo_id}}">{{$gasto->tipo_gasto_operativo}}</option>
                                @endforeach
                            </select>
                            @error('gasto_selec') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Introducir Monto del Gasto<span class="text-red-500">*</span></label>
                            <input type="text" id="monto" wire:model="monto" placeholder="monto bs..."
                                class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                oninput="formatCurrency(this)">
                            @error('monto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button wire:click="ingresar_nuevo_gasto" wire:loading.attr='disabled' wire:target='ingresar_nuevo_gasto'
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Ingresar
                        </x-button>
                        <x-button wire:click="cerrar" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase">
                            Cerrar
                        </x-button>
                    </div>
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

    <script>
        function formatCurrency(input) {
                    // Eliminar caracteres no numéricos
                    let value = input.value.replace(/[^0-9]/g, '');

                    // Convertir a número y formatear
                    if (value.length === 0) {
                        input.value = '0,00';
                        return;
                    }

                    // Convertir a centimos
                    let cents = parseInt(value, 10);

                    // Formatear a bs y centimos
                    let bs = Math.floor(cents / 100);
                    let formattedCents = (cents % 100).toString().padStart(2, '0');

                    // // Agregar separador de miles
                    let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    // Actualizar el valor del input
                    input.value = `${formattedbs},${formattedCents}`;
                    console.log('entro');

                }
    </script>

@endpush

