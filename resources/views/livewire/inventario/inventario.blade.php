<div x-data="{
    confirmarLimpiar(inventarioId, descripcion) {
        Swal.fire({
            title: '¿Limpiar existencias?',
            html: 'Se establecerá en <b>0</b> la cantidad disponible y lo asignado a promotores de <b>' + descripcion + '</b>. Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6b1820',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.limpiarExistencias(inventarioId);
            }
        });
    }
}">
    @section('titulo')
        Inventario de Insumos
    @endsection
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Inventario de Insumos {{ $usuario->oficina->nombre }}</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión, registro y transferencia de insumos disponibles en la oficina.</p>
    </div>

    @if (session()->has('message'))
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                <p class="font-bold">Éxito</p>
                <p>{{ session('message') }}</p>
            </div>
        </div>
    @endif

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- Barra de acciones (botones) --}}
            <div class="mt-2 mb-4 flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-end gap-2">
                <button type="button" wire:click="exportarInventario" wire:loading.attr='disabled' wire:target='exportarInventario'
                    class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Reporte Excel
                </button>
                <button type="button" wire:click="exportarInventarioPdf" wire:loading.attr='disabled' wire:target='exportarInventarioPdf'
                    class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reporte PDF
                </button>

                @can('Ingresar Insumos')
                    <button type="button" wire:click="openModal"
                        class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ingresar Existencias
                    </button>

                    @if(in_array($oficina['tipo_oficina_id'], [1,2,3]))
                        <button type="button" wire:click="openModalUser"
                            class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Asignar Insumos
                        </button>
                    @endif
                @endcan

                @can('Enviar Insumos')
                    @if ($oficina['tipo_oficina_id'] == 5)
                        <button type="button" wire:click="openModal2"
                            class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Insumos a COP
                        </button>
                    @endif
                    <button type="button" wire:click="openModal3"
                        class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Insumos a OPT
                    </button>
                @endcan
            </div>

            {{-- Buscador --}}
            <div class="mt-2 mb-4">
                <div class="flex items-center w-full md:w-2/3">
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                            placeholder="Buscar insumo...">
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Insumo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cantidad Disponible</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Última Actualización</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Total en Oficina</th>
                            @can('Ingresar Insumos')
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($inventarios as $inventario)
                            <tr wire:key="{{ $inventario->insumo_inventario_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    {{ $inventario->insumo->descripcion ?: 'No disponible' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $inventario->cantidad ?: 'Sin existencias' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $inventario->updated_at->format('d/m/Y') ?: 'No disponible' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $inventario->total_oficina ?: 'Sin existencias' }}
                                </td>
                                @can('Ingresar Insumos')
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <button type="button"
                                            x-on:click="confirmarLimpiar({{ $inventario->insumo_inventario_id }}, @js($inventario->insumo->descripcion ?? 'este insumo'))"
                                            wire:loading.attr="disabled"
                                            wire:target="limpiarExistencias({{ $inventario->insumo_inventario_id }})"
                                            title="Limpiar existencias"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay insumos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Comentarios de paginación --}}
            {{-- <div class="py-4 px-3">
                <select wire:model.live="perPage" id="paginacion" class="mt-1 block w-1/4 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="10">15</option>
                    <option value="15">20</option>
                    <option value="20">25</option>
                    <option value="40">30</option>
                </select>
            </div>
            <div class="py-4 px-3">
                {{$apartadosp->links()}}
            </div> --}}

        </div>
    </div>

    {{-- 1. MODAL: AGREGAR EXISTENCIAS --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden transition-all transform">
                    {{-- Cabecera --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center text-[#6b1820]">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <h3 class="text-xl font-bold text-gray-800">Agregar Existencias</h3>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>

                    <div class="p-6">
                        <label class="block text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wider">Insumos Disponibles</label>
                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50 max-h-64 overflow-y-auto shadow-inner">
                            @foreach ($insumos as $insumo)
                                <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-0 hover:bg-white transition" wire:key="insumo-add-{{ $insumo->insumo_id }}">
                                    <label class="flex items-center cursor-pointer flex-1">
                                        <input type="checkbox" value="{{ $insumo->insumo_id }}" wire:model="insumo_selec" class="w-5 h-5 text-[#6b1820] border-gray-300 rounded focus:ring-[#6b1820]">
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $insumo->descripcion }}</span>
                                    </label>
                                    <input type="number" wire:model="cantidad_por_insumo.{{ $insumo->insumo_id }}" min="0" 
                                        class="w-20 h-9 border border-gray-300 rounded-md text-center text-sm focus:ring-1 focus:ring-[#6b1820] focus:border-[#6b1820]" placeholder="0">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="ingresar_insumos" wire:loading.attr='disabled' wire:target='ingresar_insumos'
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] shadow-sm transition">
                            <span wire:loading.remove wire:target="ingresar_insumos">Ingresar</span>
                            <span wire:loading wire:target="ingresar_insumos">Procesando...</span>
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 2. MODAL: TRANSFERIR A COP --}}
    @if ($modalOpen2)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden transition-all transform">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center text-[#6b1820]">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            <h3 class="text-xl font-bold text-gray-800">Transferir Insumos a COP</h3>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <label for="cops" class="block text-sm font-bold text-gray-700 mb-1">COP Destino <span class="text-red-500">*</span></label>
                            <select id="cops" wire:model.live="cop" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820] py-2">
                                <option value="">Seleccione una COP</option>
                                @foreach ($cops as $cop) <option value="{{ $cop->oficina_id }}">{{ $cop->nombre }}</option> @endforeach
                            </select>
                            @error('cop') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Insumos a transferir</label>
                            <div class="border border-gray-200 rounded-lg bg-gray-50 max-h-52 overflow-y-auto">
                                @foreach ($insumos as $insumo)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-0 hover:bg-white transition" wire:key="insumo-cop-{{ $insumo->insumo_id }}">
                                        <label class="flex items-center cursor-pointer flex-1">
                                            <input type="checkbox" value="{{ $insumo->insumo_id }}" wire:model="insumo_selec" class="w-5 h-5 text-[#6b1820] border-gray-300 rounded focus:ring-[#6b1820]">
                                            <span class="ml-3 text-sm text-gray-700">{{ $insumo->descripcion }}</span>
                                        </label>
                                        <input type="number" wire:model="cantidad_por_insumo.{{ $insumo->insumo_id }}" class="w-20 h-9 border border-gray-300 rounded-md text-center text-sm focus:ring-[#6b1820]">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="transferir_insumos" wire:loading.attr='disabled' wire:target='transferir_insumos'
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] shadow-sm transition">
                            <span wire:loading.remove wire:target="transferir_insumos">Transferir</span>
                            <span wire:loading wire:target="transferir_insumos">Procesando...</span>
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. MODAL: TRANSFERIR A OPT --}}
    @if ($modalOpen3)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden transition-all transform">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center text-[#6b1820]">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-800">
                                Transferir Insumos a OPT
                            </h3>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200 transition duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        @if ($oficina['tipo_oficina_id'] == 5)
                            <div>
                                <label class="block text-sm font-bold text-gray-700">COP Origen <span class="text-red-500">*</span></label>
                                <select wire:model.live="cop" class="w-full mt-1 border border-gray-300 rounded-lg focus:ring-[#6b1820] py-2">
                                    <option value="">Seleccione una COP</option>
                                    @foreach ($cops as $cop) <option value="{{ $cop->oficina_id }}">{{ $cop->nombre }}</option> @endforeach
                                </select>
                                @error('cop') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-bold text-gray-700">OPT Destino <span class="text-red-500">*</span></label>
                            <select wire:model.live="opt" class="w-full mt-1 border border-gray-300 rounded-lg focus:ring-[#6b1820] py-2">
                                <option value="">Seleccione una OPT</option>
                                @foreach ($opts as $opt) <option value="{{ $opt->oficina_id }}">{{ $opt->nombre }}</option> @endforeach
                            </select>
                            @error('opt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Selección de Insumos</label>
                            <div class="border border-gray-200 rounded-lg bg-gray-50 max-h-52 overflow-y-auto shadow-inner p-1">
                                @foreach ($insumos as $insumo)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-100 last:border-0 hover:bg-white" wire:key="insumo-opt-{{ $insumo->insumo_id }}">
                                        <label class="flex items-center cursor-pointer flex-1">
                                            <input type="checkbox" value="{{ $insumo->insumo_id }}" wire:model="insumo_selec" class="w-5 h-5 text-[#6b1820] border-gray-300 rounded">
                                            <span class="ml-3 text-sm text-gray-700">{{ $insumo->descripcion }}</span>
                                        </label>
                                        <input type="number" wire:model="cantidad_por_insumo.{{ $insumo->insumo_id }}" class="w-20 h-9 border border-gray-300 rounded-md text-center text-sm">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="transferir_insumos_opt" wire:loading.attr='disabled' wire:target='transferir_insumos_opt'
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] shadow-sm transition">
                            <span wire:loading.remove wire:target="transferir_insumos_opt">Procesar</span>
                            <span wire:loading wire:target="transferir_insumos_opt">Procesando...</span>
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 4. MODAL: ASIGNAR INSUMOS A PROMOTOR --}}
    @if ($modalOpenUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 text-[#6b1820]">
                        <h3 class="text-xl font-bold text-gray-800">Asignar Insumos a Promotor</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Promotores <span class="text-red-500">*</span></label>
                            <select wire:model.live="promotor" class="w-full border border-gray-300 rounded-lg focus:ring-[#6b1820] py-2">
                                <option value="">Seleccione un Usuario:</option>
                                @foreach ($promotores as $pro) <option value="{{ $pro->id }}">{{ $pro->name }}</option> @endforeach
                            </select>
                            @error('promotor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <div class="border border-gray-200 rounded-lg bg-gray-50 max-h-64 overflow-y-auto">
                                @foreach ($insumos as $insumo)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-0 hover:bg-white" wire:key="insumo-user-{{ $insumo->insumo_id }}">
                                        <label class="flex items-center cursor-pointer flex-1">
                                            <input type="checkbox" value="{{ $insumo->insumo_id }}" wire:model="insumo_selec" class="w-5 h-5 text-[#6b1820] border-gray-300 rounded">
                                            <span class="ml-3 text-sm text-gray-700 font-medium">{{ $insumo->descripcion }}</span>
                                        </label>
                                        <input type="number" wire:model="cantidad_por_insumo.{{ $insumo->insumo_id }}" class="w-20 h-9 border border-gray-300 rounded-md text-center">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="asignar" wire:loading.attr='disabled' wire:target='asignar'
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] shadow-sm transition">
                            <span wire:loading.remove wire:target="asignar">Asignar Insumos</span>
                            <span wire:loading wire:target="asignar">Procesando...</span>
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @script
            <script>
                // success alert
                Livewire.on('alertSuccess', message => {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: message.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                })

                Livewire.on('alertSuccess2', message => {
                    Swal.fire({
                        position: "center",
                        icon: "error",
                        title: message.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                })
            </script>
        @endscript
    @endpush
</div>