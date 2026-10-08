<div x-data="{
    confirmarLimpiarPromotor(insumoUsuarioId, descripcion) {
        Swal.fire({
            title: '¿Limpiar existencias?',
            html: 'Se establecerá en <b>0</b> la cantidad de <b>' + descripcion + '</b> asignada a este promotor. Esta acción no se puede deshacer y las existencias no regresarán al inventario de la oficina.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6b1820',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.limpiarInsumoPromotor(insumoUsuarioId);
            }
        });
    }
}">
    @section('titulo')
        Inventario de Promotores
    @endsection

    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Inventario de Promotores {{ $usuario->oficina->nombre }}</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y reasignación de insumos asignados a promotores.</p>
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

            @can('Ingresar Insumos')
                {{-- Barra de acciones (botón) --}}
                <div class="mt-2 mb-4 flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-end gap-2">
                    @if(in_array($oficina['tipo_oficina_id'], [1,2,3]))
                        <button type="button" wire:click="openModal"
                            class="whitespace-nowrap shadow-sm inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Devolver Insumos
                        </button>
                    @endif
                </div>

                {{-- Filtro selector de promotor --}}
                <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                    <div class="w-full md:w-1/4">
                        <label for="promotor" class="block text-sm font-medium text-gray-700 mb-1">Promotor</label>
                        <select id="promotor" wire:model.live="promotor"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                            <option value="">Selección de promotor</option>
                            @foreach ($promotores as $pro)
                                <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                            @endforeach
                        </select>
                        @error('promotor')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endcan

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Insumo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cantidad Disponible</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Última Actualización</th>
                            @can('Ingresar Insumos')
                                @if (count($inventarios) > 0)
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                                @endif
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
                                @can('Ingresar Insumos')
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <button type="button"
                                            x-on:click="confirmarLimpiarPromotor({{ $inventario->insumo_usuario_id }}, @js($inventario->insumo->descripcion ?? 'este insumo'))"
                                            wire:loading.attr="disabled"
                                            wire:target="limpiarInsumoPromotor({{ $inventario->insumo_usuario_id }})"
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
                                <td colspan="4" class="py-7 text-gray-500 text-lg italic bg-gray-50">
                                    @if($promotor)
                                        Este promotor no tiene insumos asignados
                                    @else
                                        Selecciona un promotor para ver sus insumos
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    @if ($modalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden transition-all transform">
                    {{-- Cabecera --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center text-[#6b1820]">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-800">Devolver Insumos</h3>
                        </div>
                        <button wire:click="close" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label for="promotor_reasignar" class="block text-sm font-bold text-gray-700 mb-1">Promotor <span class="text-red-500">*</span></label>
                            <select id="promotor_reasignar" wire:model.live="promotor_reasignar"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-white">
                                <option value="">Seleccione un promotor</option>
                                @foreach ($promotores as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                @endforeach
                            </select>
                            @error('promotor_reasignar')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Insumos del promotor</label>
                            <div class="border border-gray-200 rounded-lg bg-gray-50 max-h-52 overflow-y-auto shadow-inner">
                                @forelse ($insumos as $insumo)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-0 hover:bg-white transition" wire:key="insumo-{{ $insumo->insumo_usuario_id }}">
                                        <label for="insumo-{{ $insumo->insumo_usuario_id }}" class="flex items-center cursor-pointer flex-1">
                                            <input id="insumo-{{ $insumo->insumo_usuario_id }}"
                                                type="checkbox"
                                                value="{{ $insumo->insumo_usuario_id }}"
                                                wire:model="insumo_selec"
                                                class="w-5 h-5 text-[#6b1820] border-gray-300 rounded focus:ring-[#6b1820]">
                                            <span class="ml-3 text-sm text-gray-700">
                                                <span class="font-medium">{{ $insumo->insumo->descripcion }}</span>
                                                <span class="block text-xs text-gray-500">Disponible: {{ $insumo->cantidad }}</span>
                                            </span>
                                        </label>
                                        <input type="number"
                                            wire:model="cantidad_por_insumo.{{ $insumo->insumo_usuario_id }}"
                                            id="cantidad-{{ $insumo->insumo_usuario_id }}"
                                            class="w-20 h-9 border border-gray-300 rounded-md text-center text-sm focus:ring-1 focus:ring-[#6b1820] focus:border-[#6b1820]"
                                            min="1" step="1" pattern="\d*" inputmode="numeric"
                                            max="{{ $insumo->cantidad }}"
                                            placeholder="Cant.">
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm text-center py-4">Este promotor no tiene insumos asignados.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-200">
                        <button type="button" wire:click="asignar" wire:loading.attr='disabled' wire:target='asignar'
                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[#6b1820] hover:bg-[#7b1f27] shadow-sm transition">
                            <span wire:loading.remove wire:target="asignar">Devolver</span>
                            <span wire:loading wire:target="asignar">Procesando...</span>
                        </button>
                        <button type="button" wire:click="close"
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