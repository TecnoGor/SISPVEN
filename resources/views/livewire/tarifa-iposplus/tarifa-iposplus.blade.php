<div>
    @section('titulo') Tarifas Iposplus @endsection
     {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Tarifas Iposplus</h1>
        <p class="mt-1 text-sm text-gray-600">Configuración de rangos de peso, costos y estados operativos.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">
            {{-- Buscador y Botón Nuevo --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="relative w-full md:w-1/3">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition"
                            placeholder="Buscar por kilo o precio...">
                    </div>

                    <button wire:click="create" class="min-w-[160px] px-4 py-2 bg-[#6b1820] text-white text-sm font-bold rounded-md shadow-sm hover:bg-[#7b1f27] transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        AGREGAR TARIFA
                    </button>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600">
                                <button wire:click="setSortBy('kilo_min')" class="flex items-center gap-1 mx-auto hover:text-[#6b1820] transition">
                                    Kilo Mínimo
                                    @if($sortBy === 'kilo_min')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'ASC' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600">
                                <button wire:click="setSortBy('kilo_max')" class="flex items-center gap-1 mx-auto hover:text-[#6b1820] transition">
                                    Kilo Máximo
                                    @if($sortBy === 'kilo_max')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'ASC' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600">
                                <button wire:click="setSortBy('precio')" class="flex items-center gap-1 mx-auto hover:text-[#6b1820] transition">
                                    Precio
                                    @if($sortBy === 'precio')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'ASC' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600">
                                <button wire:click="setSortBy('activo')" class="flex items-center gap-1 mx-auto hover:text-[#6b1820] transition">
                                    Estatus
                                    @if($sortBy === 'activo')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $sortDir === 'ASC' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"/></svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold uppercase text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($lista_tarifas as $item)
                            <tr wire:key="t-{{ $item->tarifa_iposplus_id }}" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->kilo_min }} kg</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->kilo_max }} kg</td>
                                <td class="px-6 py-4 font-bold text-[#6b1820]">${{ number_format($item->precio, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-center items-center">
                                        @if ($item->activo)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                Inactivo
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-center gap-3">
                                        {{-- BOTÓN EDICIÓN --}}
                                        <button wire:click="edit({{ $item->tarifa_iposplus_id }})" 
                                            class="text-blue-500 hover:text-blue-700 transition" 
                                            title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        {{-- BOTÓN ACTIVAR / DESACTIVAR --}}
                                        @if ($item->activo)
                                            <button wire:click="toggleActivo({{ $item->tarifa_iposplus_id }})" 
                                                class="text-red-600 hover:text-orange-700 transition" title="Desactivar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button wire:click="toggleActivo({{ $item->tarifa_iposplus_id }})" 
                                                class="text-green-500 hover:text-green-700 transition" title="Activar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay tarifas disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 pt-4">
            <div class="flex items-center gap-4">
                <label class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                <select wire:model.live="perPage" class="block w-24 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-2">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="w-full md:w-auto">
                {{ $lista_tarifas->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    @if($modal_open)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="inline-block w-full max-w-2xl overflow-hidden transition-all transform bg-white rounded-xl shadow-2xl">
                    {{-- Encabezado Modal --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $editing_id ? 'Editar Tarifa' : 'Registro de Nueva Tarifa' }}</h3>
                        </div>
                        <button wire:click="$set('modal_open', false)" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    {{-- Cuerpo y campos del Modal --}}
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilo Mínimo <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" wire:model="kilo_min" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820] transition">
                                    @error('kilo_min') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilo Máximo <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" wire:model="kilo_max" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820] transition">
                                    @error('kilo_max') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                        <input type="text" wire:model="precio" placeholder="0,00"
                                            class="block w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820]"
                                            onkeydown="return event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Enter' || event.key === 'Tab' || !isNaN(event.key)"
                                            oninput="formatCurrency(this)">
                                    </div>
                                    @error('precio') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="pt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Estatus de Tarifa:</label>
                                    <div class="flex items-center">
                                        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                            <span class="text-sm {{ !$esta_activo ? 'text-red-600 font-bold' : 'text-gray-400' }}">Inactivo</span>
                                            <input type="checkbox" class="sr-only peer" wire:model.live="esta_activo">
                                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-[#6b1820] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                            <span class="text-sm {{ $esta_activo ? 'text-[#6b1820] font-bold' : 'text-gray-400' }}">Activo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                        <x-button 
                            wire:click="save" 
                            type="button" 
                            wire:loading.attr="disabled" 
                            wire:target="save"
                            class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $editing_id ? 'Actualizar Registro' : 'Guardar Registro' }}
                            </span>
                            <span wire:loading wire:target="save">Procesando...</span>
                        </x-button>

                        <x-button 
                            type="button" 
                            wire:click="$set('modal_open', false)" 
                            class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                        >
                            Cancelar
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        Livewire.on('alertSuccess', (message) => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('alertSuccess2', (message) => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: true,
                confirmButtonColor: '#6b1820'
            });
        });
        
        Livewire.on('alertSuccess3', (message) => {
            Swal.fire({
                position: "center",
                icon: "info",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endscript

    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, '');

            if (value.length === 0) {
                input.value = '0,00';
                return;
            }

            let cents = parseInt(value, 10);
            let bs = Math.floor(cents / 100);
            let formattedCents = (cents % 100).toString().padStart(2, '0');
            let formattedbs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            input.value = `${formattedbs},${formattedCents}`;
        }
    </script>
@endpush