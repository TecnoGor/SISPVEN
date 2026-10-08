<div>
    @section('título')
        Valijas
    @endsection

    @php
        $oficinaActual = \App\Models\Oficina::find(auth()->user()->oficina_id);
    @endphp

    <div class="max-w-8xl mx-auto px-4 sm:px-5 lg:px-6">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-5">
            <div>
                <h1 class="text-2xl md:text-3xl text-primary font-bold tracking-tight text-balance">
                    Valijas
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $oficinaActual->nombre ?? 'Oficina no asignada' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($sacas->total() > 0)
                    <x-secondary-button wire:click="exportarPDF" wire:loading.attr="disabled" wire:target="exportarPDF"
                        class="gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.72 13.829q-.577.106-1.147.24m1.147-.24 1.902 4.756m-1.902-4.757a48 48 0 0 1 10.56 0m-10.56 0 3.6 8.999m6.96-8.759q.577.106 1.147.24m-1.147-.24-3.6 8.999m0 0-.34.85m3.94-9.849a49 49 0 0 0-10.56 0" />
                        </svg>
                        Imprimir PDF
                    </x-secondary-button>

                    <x-secondary-button wire:click="exportarExcel" wire:loading.attr="disabled" wire:target="exportarExcel"
                        class="gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Exportar Excel
                    </x-secondary-button>
                @endif

                <x-primary-button wire:click="create()" class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Crear Valija
                </x-primary-button>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200">

            {{-- BARRA DE FILTROS --}}
            <div class="flex flex-col gap-4 p-4 border-b border-gray-200 lg:flex-row lg:items-end lg:justify-between">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    {{-- Búsqueda --}}
                    <div>
                        <label for="buscarValija" class="block text-xs font-medium text-gray-700 mb-1">
                            Buscar por código
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="search" id="buscarValija" wire:model.live.debounce.300ms="search"
                                placeholder="Código de valija"
                                class="bg-white border border-gray-300 text-gray-900 placeholder:text-gray-500 text-sm rounded-md focus:ring-2 focus:ring-primary focus:border-primary block w-full sm:w-56 pl-9 pr-8 py-2">

                            @if ($search)
                                <button type="button" wire:click="$set('search', '')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-500 hover:text-gray-800 focus:outline-none focus:text-primary"
                                    title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label for="fechaBusqueda" class="block text-xs font-medium text-gray-700 mb-1">
                            Fecha de creación
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" wire:model.live="fechaBusqueda" id="fechaBusqueda"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-primary focus:border-primary block py-2 px-3">

                            @if ($fechaBusqueda)
                                <button type="button" wire:click="$set('fechaBusqueda', null)"
                                    class="p-2 text-gray-500 rounded-md hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary"
                                    title="Limpiar fecha" aria-label="Limpiar fecha">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Estado: control segmentado. El estado es explícito, no se deduce
                     del texto de un botón de acción. --}}
                <div>
                    <span class="block text-xs font-medium text-gray-700 mb-1">Estado</span>
                    <div class="inline-flex p-0.5 bg-gray-100 rounded-md border border-gray-200" role="group"
                        aria-label="Filtrar valijas por estado">
                        <button type="button" wire:click="mostrarAbiertas"
                            aria-pressed="{{ $pestana === 'abiertas' ? 'true' : 'false' }}"
                            class="px-3 py-1.5 text-sm font-medium rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1
                                {{ $pestana === 'abiertas' ? 'bg-white text-primary shadow-sm' : 'text-gray-700 hover:text-gray-900' }}">
                            Abiertas
                        </button>
                        <button type="button" wire:click="mostrarCerradasValijas"
                            aria-pressed="{{ $pestana === 'cerradas' ? 'true' : 'false' }}"
                            class="px-3 py-1.5 text-sm font-medium rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1
                                {{ $pestana === 'cerradas' ? 'bg-white text-primary shadow-sm' : 'text-gray-700 hover:text-gray-900' }}">
                            Cerradas
                        </button>
                        {{-- Listado histórico: valijas creadas en esta oficina, estén donde
                             estén ahora. Es donde se rastrea una valija ya despachada, que
                             deja de aparecer en Abiertas/Cerradas al salir. --}}
                        <button type="button" wire:click="mostrarCreadas"
                            aria-pressed="{{ $pestana === 'creadas' ? 'true' : 'false' }}"
                            class="px-3 py-1.5 text-sm font-medium rounded transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1
                                {{ $pestana === 'creadas' ? 'bg-white text-primary shadow-sm' : 'text-gray-700 hover:text-gray-900' }}">
                            Creadas aquí
                        </button>
                    </div>
                </div>
            </div>

            {{-- Resumen de resultados --}}
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                <p class="text-xs text-gray-700">
                    @if ($sacas->total() > 0)
                        <span class="font-semibold text-gray-900">{{ $sacas->total() }}</span>
                        {{ $sacas->total() == 1 ? 'valija' : 'valijas' }}
                        @if ($pestana === 'creadas')
                            creadas en esta oficina
                        @else
                            {{ $pestana === 'cerradas' ? 'cerradas' : 'abiertas' }} en esta oficina
                        @endif
                        @if ($search || $fechaBusqueda)
                            <span class="text-gray-600">· filtrado</span>
                        @endif
                    @endif
                </p>

                <div wire:loading.flex wire:target="search, fechaBusqueda, perPage, mostrarAbiertas, mostrarCerradasValijas, mostrarCreadas"
                    class="items-center gap-1.5 text-xs text-gray-600">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Actualizando
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <caption class="sr-only">
                        Listado de valijas {{ $pestana === 'creadas' ? 'creadas en' : ($pestana === 'cerradas' ? 'cerradas de' : 'abiertas de') }} la oficina
                    </caption>
                    <thead class="text-xs uppercase bg-gray-100 text-gray-700 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Código</th>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Tipo</th>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Destino</th>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Despacho</th>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Creada</th>
                            <th scope="col" class="px-4 py-2.5 text-left font-semibold">Usuario</th>
                            <th scope="col" class="px-4 py-2.5 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sacas as $saca)
                            <tr wire:key="saca-{{ $saca->saca_id }}" class="hover:bg-gray-50 transition-colors duration-150">
                                {{-- Código: identificador de la fila --}}
                                <th scope="row" class="px-4 py-2.5 text-left font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $saca->codigo_saca }}
                                </th>

                                <td class="px-4 py-2.5 text-gray-900">
                                    {{ $saca->tipoSaca->nombre_referencial ?? '—' }}
                                </td>

                                <td class="px-4 py-2.5 text-gray-900">
                                    @if ($saca->oficinaDestino)
                                        {{ $saca->oficinaDestino->nombre }}
                                    @else
                                        <span class="text-gray-500">Sin destino</span>
                                    @endif
                                </td>

                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    @if ($saca->numeroDespacho)
                                        <span class="font-medium text-gray-900 tabular-nums">
                                            N° {{ $saca->numeroDespacho->numero_despacho }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">Sin despacho</span>
                                    @endif
                                </td>

                                <td class="px-4 py-2.5 text-gray-900 whitespace-nowrap tabular-nums">
                                    @if ($saca->created_at)
                                        {{ $saca->created_at->format('d/m/Y') }}
                                        <span class="text-gray-600">{{ $saca->created_at->format('h:i A') }}</span>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-2.5 text-gray-900">
                                    {{ $saca->usuario->name ?? '—' }}
                                </td>

                                <td class="px-4 py-2.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('sacas-detalles', $saca->saca_id) }}"
                                            class="inline-flex items-center justify-center p-1.5 rounded-md text-gray-600 hover:text-primary hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-150"
                                            title="Ver detalles de la valija {{ $saca->codigo_saca }}"
                                            aria-label="Ver detalles de la valija {{ $saca->codigo_saca }}">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                                            </svg>
                                        </a>

                                        {{-- Solo en Cerradas: reabrir una valija que está aquí y
                                             cerrada. En Creadas no se pinta porque la valija puede
                                             estar en otra oficina. --}}
                                        @if ($pestana === 'cerradas')
                                            <button type="button" wire:click="reabrirValija({{ $saca->saca_id }})"
                                                wire:loading.attr="disabled" wire:target="reabrirValija({{ $saca->saca_id }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-md text-gray-600 hover:text-primary hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-150 disabled:opacity-50"
                                                title="Reabrir la valija {{ $saca->codigo_saca }}"
                                                aria-label="Reabrir la valija {{ $saca->codigo_saca }}">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M22 12C22 17.5228 17.5229 22 12 22C6.4772 22 2 17.5228 2 12C2 6.47715 6.4772 2 12 2V4C7.5817 4 4 7.58172 4 12C4 16.4183 7.5817 20 12 20C16.4183 20 20 16.4183 20 12C20 9.25022 18.6127 6.82447 16.4998 5.38451L16.5 8H14.5V2L20.5 2V4L18.0008 3.99989C20.4293 5.82434 22 8.72873 22 12Z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Estado vacío: distingue "sin resultados de búsqueda" de
                                 "no hay valijas todavía" y ofrece la salida adecuada. --}}
                            <tr>
                                <td colspan="7" class="px-4 py-12">
                                    <div class="flex flex-col items-center text-center max-w-sm mx-auto">
                                        <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>

                                        @if ($search || $fechaBusqueda)
                                            <p class="text-base font-semibold text-gray-900">Sin coincidencias</p>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Ninguna valija coincide con los filtros aplicados.
                                            </p>
                                            <button type="button" wire:click="limpiarFiltros"
                                                class="mt-3 text-sm font-semibold text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary rounded px-1">
                                                Limpiar filtros
                                            </button>
                                        @elseif ($pestana === 'creadas')
                                            <p class="text-base font-semibold text-gray-900">No hay valijas creadas aquí</p>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Aquí aparecen todas las valijas creadas en esta oficina, incluidas las
                                                que ya fueron despachadas a otro destino.
                                            </p>
                                        @elseif ($pestana === 'cerradas')
                                            <p class="text-base font-semibold text-gray-900">No hay valijas cerradas</p>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Las valijas aparecen aquí una vez que se cierran para su despacho, y
                                                dejan de aparecer al salir hacia su destino.
                                            </p>
                                        @else
                                            <p class="text-base font-semibold text-gray-900">No hay valijas abiertas</p>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Crea una valija para empezar a agrupar envíos hacia una oficina de destino.
                                            </p>
                                            <x-primary-button wire:click="create()" class="mt-4">
                                                Crear Valija
                                            </x-primary-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if ($sacas->total() > 0)
                <div class="flex flex-col gap-3 px-4 py-3 border-t border-gray-200 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <label for="perPage" class="text-xs font-medium text-gray-700">Por página</label>
                        <select id="perPage" wire:model.live="perPage"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-primary focus:border-primary py-1.5 pl-2.5 pr-8">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div>
                        {{ $sacas->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: CREAR VALIJA --}}
    <form wire:submit.prevent="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                Crear valija
            </x-slot>

            <x-slot name="content">
                <p class="-mt-2 mb-5 text-sm text-gray-600">
                    La valija se crea en <span class="font-medium text-gray-900">{{ $oficinaActual->nombre ?? 'tu oficina' }}</span>.
                    Indica su tipo y hacia dónde va.
                </p>

                <div class="space-y-4">
                    {{-- Tipo --}}
                    <div>
                        <x-input-label for="tipoSeleccionado" :value="__('Tipo de valija')" />
                        <select id="tipoSeleccionado" wire:model="createForm.tipoSeleccionado"
                            @error('createForm.tipoSeleccionado') aria-invalid="true" @enderror
                            class="block mt-1 w-full rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary
                                @error('createForm.tipoSeleccionado') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Seleccione un tipo</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->tipo_saca_id }}">{{ $tipo->nombre_referencial }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('createForm.tipoSeleccionado')" class="mt-1.5 text-sm text-red-600" />
                    </div>

                    {{-- Destino: estado y oficina son un solo paso, el estado filtra la oficina --}}
                    <fieldset class="pt-4 border-t border-gray-200">
                        <legend class="text-xs font-semibold uppercase tracking-wide text-gray-700 mb-3">
                            Destino
                        </legend>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="estadoSeleccionado" :value="__('Estado')" />
                                <select id="estadoSeleccionado" wire:model.live="estadoSeleccionado"
                                    @error('estadoSeleccionado') aria-invalid="true" @enderror
                                    class="block mt-1 w-full rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary
                                        @error('estadoSeleccionado') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Seleccione un estado</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('estadoSeleccionado')" class="mt-1.5 text-sm text-red-600" />
                            </div>

                            <div>
                                <x-input-label for="oficinaSeleccionada" :value="__('Oficina de destino')" />

                                <select id="oficinaSeleccionada" wire:model="createForm.oficinaSeleccionada"
                                    @disabled(!$estadoSeleccionado)
                                    @error('createForm.oficinaSeleccionada') aria-invalid="true" @enderror
                                    class="block mt-1 w-full rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary
                                        disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed
                                        @error('createForm.oficinaSeleccionada') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">
                                        {{ $estadoSeleccionado ? 'Seleccione una oficina' : 'Seleccione primero un estado' }}
                                    </option>
                                    @foreach ($filteredOficinas as $oficinaDestino)
                                        <option value="{{ $oficinaDestino->oficina_id }}">{{ $oficinaDestino->nombre }}</option>
                                    @endforeach
                                </select>

                                {{-- Estado sin oficinas disponibles: se avisa en vez de dejar un select vacío --}}
                                @if ($estadoSeleccionado && count($filteredOficinas) === 0)
                                    <p class="mt-1.5 text-sm text-gray-600">
                                        Este estado no tiene oficinas activas disponibles.
                                    </p>
                                @endif

                                <x-input-error :messages="$errors->get('createForm.oficinaSeleccionada')" class="mt-1.5 text-sm text-red-600" />
                            </div>
                        </div>
                    </fieldset>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-danger-button wire:click="$set('createForm.open', false)" type="button">
                        Cancelar
                    </x-danger-button>

                    <x-primary-button wire:loading.attr="disabled" wire:target="store">
                        Crear valija
                        <x-loading-button wire:target="store" />
                    </x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
        <script>
            Livewire.on('alertSuccess', (event) => {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: event.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            });

            Livewire.on('alertError', (event) => {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: event.message,
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        </script>
    @endscript
@endpush