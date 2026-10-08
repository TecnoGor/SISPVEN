<div>
    @section('titulo') Gastos de Arrendamiento @endsection
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
            Gastos de Arrendamiento {{ $oficina->nombre }}
        </h1>
        <p class="mt-1 text-sm text-gray-600">Visualización y registro de pagos y deudas mensuales.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BUSCADOR Y BOTÓN --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm" wire:click="registrar" wire:loading.attr='disabled' wire:target='registrar'>
                            Nuevo Registro
                        </x-primary-button>
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                <div class="flex flex-col md:flex-row gap-4 w-full items-start md:items-center">
                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                        <input type="month" wire:model.live="desde"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3">
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                        <input type="month" wire:model.live="hasta"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3">
                    </div>

                    <div class="flex flex-col h-full pt-4 md:pt-0"> 
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estatus:</label>
                        <div class="flex items-center">
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <span class="text-sm text-default">Pendientes</span>
                                <input type="checkbox" class="sr-only peer" wire:model.live="estatus">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-2 focus:ring-[#6b1820] after:content-[''] 
                                    after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                </div>
                                <span class="text-sm text-default">Pagadas</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
                                {{ $estatus ? 'Monto Pagado' : 'Monto Pendiente' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pagos as $index => $pago)
                            <tr wire:key="{{ $pago->pago_servicio_publico_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    {{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center font-bold">
                                    {{ number_format($pago->monto, 2, ',', '.') }} Bs
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="2" class="py-7 text-gray-500 text-lg italic bg-gray-50">
                                    {{ $estatus ? 'No hay registros de pagos de arrendamiento' : 'No hay registros de deudas de arrendamiento' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
                {{ $pagos->links() }}
            </div>
        </div> 
    </div>

    {{-- REGISTROS/LISTADO --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4"> 
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
    </div>

    {{-- MODAL --}}
    @if($registro)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">

                    {{-- ENCABEZADO MODAL --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                Ingresar Nuevo Pago/Deuda de Arrendamiento
                            </h3>
                        </div>
                        <button type="button" wire:click="cerrar" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- FORMULARIO MODAL --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            
                            {{-- Tipo de Registro --}}
                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Registro<span class="text-red-500">*</span></label>
                                <select id="tipo_registro" wire:model="tipo_registro" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    <option value="1">Pago</option>
                                    <option value="0">Deuda</option>
                                </select>
                                @error('tipo_registro') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Fecha Registro --}}
                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del Registro</label>
                                <input id="fecha_registro" type="date" wire:model.live="fecha_registro" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                @error('fecha_registro') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Monto --}}
                            <div class="col-span-1 sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Introducir Monto<span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="monto" placeholder="monto bs..."
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                    onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                    oninput="formatCurrency(this)">
                                @error('monto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>

                    {{-- PIE DE MODAL (ACCIONES) --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button wire:click="ingresar" wire:loading.attr='disabled' wire:target='ingresar'
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide">
                            Ingresar
                        </x-button>

                        <x-button type="button" wire:click="cerrar" wire:loading.attr='disabled' wire:target='cerrar'
                            class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm">
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
                timer: 2500
            });
        });

        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 2500
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