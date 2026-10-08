<div>
    @section('titulo')
        Gestión Exporta Fácil
    @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Gestión de países - Exporta Fácil</h1>
        <p class="mt-1 text-sm text-gray-600">Administración de áreas geográficas y tarifas de exportación.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BUSCADOR Y FILTRO --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="buscar"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar país...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <div class="w-full md:w-64">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Continente</label>
                            <select wire:model.live="continente"
                                class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                                <option value="">Seleccionar Continente</option>
                                @foreach ($continentes as $continente)
                                    <option value="{{ $continente->continente_id }}">{{ $continente->nombre }}</option>  
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Continente</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Exporta Fácil</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Tarifas</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pais as $index => $p)
                            <tr wire:key="{{ $p->pais_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $p->continentes->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $p->nombre }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if ($p->exporta_facil)
                                        <button wire:click="editar({{ $p->pais_id }})" title="Inoperativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="editar({{ $p->pais_id }})" title="Operativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    <button wire:click="actualizar_tarifa({{$p->pais_id}})" type="button"
                                        class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-xs transition shadow-md uppercase tracking-wide">
                                        {{$p->tarifa->monto ?? 'Sin Tarifa'}}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="4" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <label class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="input" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
        </div>
        <div>
            {{ $pais->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    @if($modal_tarifa)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <div class="inline-block w-full max-w-lg overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Tarifa de {{$info->nombre}}</h3>
                        </div>
                        <button type="button" wire:click="cerrar_tarifa" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                    </div>

                    <form>
                        <div class="p-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monto Tarifa</label>
                            <input type="text" wire:model.live='tarifa_nueva' 
                                class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || !isNaN(event.key)"
                                oninput="formatCurrency(this)">
                            @error('tarifa_nueva') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button type="button" wire:click="cambiar_tarifa" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm shadow-md uppercase">Actualizar</x-button>
                            <x-button type="button" wire:click="cerrar_tarifa" class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm">Cancelar</x-button>
                        </div>
                    </form>
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
