<div>
    @section('titulo')
        Tasas
    @endsection

    {{-- Encabezado --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Tasas de Divisas</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y configuración de tasas del sistema.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- Barra superior: búsqueda + botón crear --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="relative w-full md:w-80">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 pl-10 pr-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition bg-gray-50"
                            placeholder="Buscar divisa...">
                    </div>

                    <button type="button" wire:click="abrirCrear"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#6b1820] hover:bg-[#7b1f27] text-white text-sm font-bold uppercase tracking-wide rounded-lg shadow-md transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Nueva Divisa
                    </button>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Valor</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Ultima Actualización</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tasas as $tasa)
                            <tr wire:key="{{ $tasa->parametro_id }}" class="border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tasa->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($tasa->valor, 2, ',', '.') }} Bs</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if ($tasa->activo)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 min-w-[70px]">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 min-w-[70px]">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $tasa->updated_at?->format('d/m/Y | H:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Editar --}}
                                        <button wire:click="edit({{ $tasa->parametro_id }})"
                                            class="text-blue-600 hover:text-blue-900 p-1.5 rounded-md hover:bg-blue-50 transition"
                                            title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Ver historial --}}
                                        <button wire:click="verHistorial({{ $tasa->parametro_id }})"
                                            class="text-amber-600 hover:text-amber-900 p-1.5 rounded-md hover:bg-amber-50 transition"
                                            title="Ver historial">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>

                                        {{-- Toggle Activar/Desactivar --}}
                                        @if($tasa->activo)
                                            <button wire:click="toggleActivo({{ $tasa->parametro_id }})"
                                                class="text-red-600 hover:text-red-900 p-1.5 rounded-md hover:bg-red-50 transition"
                                                title="Desactivar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button wire:click="toggleActivo({{ $tasa->parametro_id }})"
                                                class="text-green-600 hover:text-green-900 p-1.5 rounded-md hover:bg-green-50 transition"
                                                title="Activar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="5" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay tasas disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="py-4 px-3">
                {{ $tasas->links() }}
            </div>

            {{-- Selector de registros por página --}}
            <div class="py-1 px-3 flex items-center justify-start gap-4">
                <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                <select wire:model.live="perPage" id="perPage_bottom"
                    class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDITAR TASA --}}
    {{-- ============================================================ --}}
    <form wire:submit="update">
        <x-dialog-modal wire:model.blur="editForm.open">
            <x-slot name="title">
                <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 -mx-6 -mt-4 rounded-t-xl">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">Actualizar Tasa</h3>
                    </div>
                    <button type="button" wire:click="$set('editForm.open', false)" class="text-gray-400 hover:text-gray-600 transition duration-150">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-5 pt-4">
                    <div>
                        <x-input-label for="editForm.nombre" class="text-sm font-semibold text-gray-700 mb-1">
                            {{ __('Nombre de la Tasa') }} <span class="text-red-500">*</span>
                        </x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <x-text-input id="editForm.nombre"
                                class="block w-full pl-10 border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                wire:model="editForm.nombre" required maxlength="14" placeholder="Ej: USD, EUR..." />
                        </div>
                        <x-input-error :messages="$errors->get('editForm.nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="editForm.valor" class="text-sm font-semibold text-gray-700 mb-1">
                            {{ __('Valor (Bs)') }} <span class="text-red-500">*</span>
                        </x-input-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium sm:text-sm">Bs.</span>
                            </div>
                            <x-text-input
                                id="editForm.valor"
                                class="block w-full pl-10 border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                wire:model.blur="editForm.valor"
                                required
                                type="text"
                                maxlength="20"
                                placeholder="Ej: 1.234,56"
                                oninput="formatCurrency(this)"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('editForm.valor')" class="mt-2" />
                    </div>
                    <br><br>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-3 -m-6 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl w-[calc(100%+3rem)]">
                    <x-button
                        wire:loading.attr="disabled"
                        wire:target="update"
                        class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none"
                    >
                        <span wire:loading.remove wire:target="update">Guardar Cambios</span>
                        <span wire:loading wire:target="update">Procesando...</span>
                    </x-button>
                    <x-button
                        wire:click="$set('editForm.open', false)"
                        type="button"
                        class="px-5 py-2.5 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide border-none"
                    >
                        Cancelar
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- ============================================================ --}}
    {{-- MODAL: CREAR DIVISA --}}
    {{-- ============================================================ --}}
    @if($modal_crear)
        <div wire:click.self="cerrarCrear" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                {{-- Header --}}
                <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">Nueva Divisa</h3>
                    </div>
                    <button type="button" wire:click="cerrarCrear" class="text-gray-400 hover:text-gray-600 transition duration-150">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Cuerpo --}}
                <form wire:submit="crearDivisa" class="px-6 py-6 space-y-5">
                    <div>
                        <label for="nombre_nueva" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <input type="text" id="nombre_nueva" wire:model.blur="nombre_nueva" maxlength="14"
                                class="block w-full pl-10 border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                placeholder="Ej: USD, EUR, PEN..."/>
                        </div>
                        @error('nombre_nueva') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="valor_nueva" class="block text-sm font-semibold text-gray-700 mb-1">
                            Valor (Bs) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium sm:text-sm">Bs.</span>
                            </div>
                            <input type="text" id="valor_nueva" wire:model.blur="valor_nueva" maxlength="20"
                                class="block w-full pl-10 border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition"
                                placeholder="Ej: 1.234,56"
                                oninput="formatCurrency(this)"/>
                        </div>
                        @error('valor_nueva') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="cerrarCrear"
                            class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="crearDivisa"
                            class="px-8 py-2.5 bg-[#6b1820] hover:bg-[#7b1f27] text-white rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide disabled:opacity-60">
                            <span wire:loading.remove wire:target="crearDivisa">Crear</span>
                            <span wire:loading wire:target="crearDivisa">Creando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: HISTORIAL DE CAMBIOS --}}
    {{-- ============================================================ --}}
    @if($modal_historial)
        <div wire:click.self="cerrarHistorial" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
            <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden border border-gray-100 max-h-[90vh] flex flex-col">
                {{-- Header --}}
                <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200 shrink-0">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">Historial de Cambios — {{ $historial_nombre }}</h3>
                    </div>
                    <button type="button" wire:click="cerrarHistorial" class="text-gray-400 hover:text-gray-600 transition duration-150">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Filtros por fecha + selector por página --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 shrink-0">
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                        <div class="flex-1 w-full sm:w-auto">
                            <label for="filtro_desde" class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Desde</label>
                            <input type="date" id="filtro_desde" wire:model.live="filtro_desde"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto">
                            <label for="filtro_hasta" class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Hasta</label>
                            <input type="date" id="filtro_hasta" wire:model.live="filtro_hasta"
                                class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition" />
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="historialPerPage" class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Mostrar</label>
                            <select id="historialPerPage" wire:model.live="historialPerPage"
                                class="block w-full sm:w-20 border border-gray-300 rounded-lg shadow-sm py-2 px-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        @if($filtro_desde || $filtro_hasta)
                            <button type="button" wire:click="limpiarFiltros"
                                class="inline-flex items-center gap-1 px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg transition uppercase tracking-wide">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Tabla de historial --}}
                <div class="overflow-y-auto flex-1 px-6 py-4">
                    @if($historial && $historial->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Usuario</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Valor Anterior</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Valor Nuevo</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($historial as $registro)
                                        @php
                                            $diferenciaBs = $registro->valor_anterior !== null
                                                ? $registro->valor_nuevo - $registro->valor_anterior
                                                : null;
                                            $diferenciaPct = ($registro->valor_anterior && $registro->valor_anterior != 0)
                                                ? (($diferenciaBs / $registro->valor_anterior) * 100)
                                                : null;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition duration-100">
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {{ $registro->fecha_cambio->format('d/m/Y') }}
                                                <span class="text-gray-400 text-xs ml-1">{{ $registro->fecha_cambio->format('H:i') }}</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {{ $registro->usuario?->name ?? 'Sistema' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-gray-600">
                                                @if($registro->valor_anterior !== null)
                                                    {{ number_format($registro->valor_anterior, 2, ',', '.') }} Bs
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Creación</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-gray-900">
                                                {{ number_format($registro->valor_nuevo, 2, ',', '.') }} Bs
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                @if($diferenciaBs !== null)
                                                    <span class="font-medium {{ $diferenciaBs >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $diferenciaBs >= 0 ? '+' : '' }}{{ number_format($diferenciaBs, 2, ',', '.') }} Bs
                                                    </span>
                                                    @if($diferenciaPct !== null)
                                                        <span class="text-xs ml-1 {{ $diferenciaPct >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                                            ({{ $diferenciaPct >= 0 ? '+' : '' }}{{ number_format($diferenciaPct, 2, ',', '.') }}%)
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación + Resumen --}}
                        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <span class="text-xs text-gray-500">{{ $historial->total() }} registro(s) encontrado(s)</span>
                            <div class="w-full sm:w-auto">
                                {{ $historial->links() }}
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <p class="text-gray-500 text-lg italic">No hay registros de cambios en este rango.</p>
                            @if($filtro_desde || $filtro_hasta)
                                <p class="text-gray-400 text-sm mt-1">Prueba ajustando los filtros de fecha o presiona "Limpiar" para ver todo.</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 shrink-0 flex justify-end">
                    <button type="button" wire:click="cerrarHistorial"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide">
                        Cerrar
                    </button>
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

        // error alert
        Livewire.on('alertError', message => {
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