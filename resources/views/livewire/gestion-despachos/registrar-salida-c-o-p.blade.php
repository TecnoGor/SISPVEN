<div>
    @section('titulo')
        Registro Salida
    @endsection

    {{-- 96rem (no max-w-7xl): con el sidebar recogido quedaba un hueco grande a los
         lados. El limite se mantiene para que las tablas no se estiren de mas en
         monitores muy anchos. --}}
    <div class="max-w-[96rem] mx-auto sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl md:text-3xl text-primary font-bold">Registro de Salida</h1>
        </div>

        <!-- Buscador rápido por código -->
        <div class="bg-white shadow-md sm:rounded-lg border border-gray-200 mb-6">
            <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Ubicar por código</label>
                    <input
                        type="text"
                        wire:model.defer="busquedaCodigo"
                        wire:keydown.enter="agregarPorCodigo"
                        class="border border-gray-300 rounded-md px-3 py-2 w-full focus:ring-primary focus:border-primary"
                        placeholder="Escanea una valija o envío para ubicar su despacho"
                    >
                </div>
                <div class="sm:pt-5">
                    <x-primary-button wire:click="agregarPorCodigo" class="w-full sm:w-auto justify-center">Buscar</x-primary-button>
                </div>
            </div>

            {{-- Chip del filtro activo: indica por qué la lista está recortada y
                 permite volver a verla completa de un clic. --}}
            @if($filtroEtiqueta)
                <div class="px-4 pb-4 -mt-1">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-semibold">
                        Filtrando por {{ $filtroEtiqueta }}
                        <button wire:click="limpiarFiltro" title="Quitar filtro"
                            class="hover:text-red-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                </div>
            @endif
        </div>

        <!-- Layout de 2 columnas: disponibles (izq) + carrito/destino (der) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- ═══════════ COLUMNA IZQUIERDA: DISPONIBLES ═══════════ -->
            {{-- El buscador por codigo cambia de pestaña solo: al escanear una valija
                 abre Despachos, al escanear un envio abre Envios al Descubierto. --}}
            <div class="lg:col-span-2 space-y-6"
                x-data="{ tab: @js($filtroTab ?: 'despachos') }"
                @filtro-aplicado.window="tab = $event.detail.tab">

                <!-- Pestañas Despachos / Sueltos -->
                <div class="flex items-center gap-2 border-b border-gray-200">
                    <button type="button" @click="tab = 'despachos'"
                        :class="tab === 'despachos' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-primary'"
                        class="px-4 py-2 -mb-px border-b-2 font-semibold text-sm transition-colors">
                        Despachos disponibles
                    </button>
                    <button type="button" @click="tab = 'sueltos'"
                        :class="tab === 'sueltos' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-primary'"
                        class="px-4 py-2 -mb-px border-b-2 font-semibold text-sm transition-colors">
                        Envíos al Descubierto
                    </button>
                </div>

                <!-- Panel: Despachos -->
                <div x-show="tab === 'despachos'" x-cloak class="bg-white shadow-md sm:rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg text-primary font-bold">Despachos disponibles (almacén)</h2>
                        <p class="text-sm text-gray-500">Cada despacho agrupa todas las valijas con el mismo número. Al agregarlo, entran todas de una vez.</p>
                    </div>
                    <div class="overflow-x-auto overflow-y-auto max-h-96">
                        <table class="min-w-full text-sm border-separate border-spacing-0">
                            <thead>
                                <tr>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">N° Despacho</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Destino</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Valijas</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Peso total (Gr)</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-center font-semibold text-primary">Guía de Despacho</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-center font-semibold text-primary">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($despachosPagina as $despacho)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-2 font-semibold text-primary">
                                            {{ ($despacho['despacho_num'] ?? null) ? 'N° ' . $despacho['despacho_num'] : 'Sin despacho' }}
                                        </td>
                                        <td class="px-4 py-2">{{ $despacho['destino'] }}</td>
                                        <td class="px-4 py-2">{{ $despacho['cantidad'] }} valija(s)</td>
                                        <td class="px-4 py-2">{{ $despacho['peso_total'] }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($despacho['despacho_id'])
                                                <a href="{{ route('manifiestos_paquetes', $despacho['despacho_id']) }}"
                                                    target="_blank"
                                                    title="Ver guía de despacho"
                                                    class="inline-flex items-center justify-center p-1.5 rounded-md border border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($despacho['despacho_id'])
                                                <button wire:click="agregarDespacho('{{ $despacho['despacho_id'] }}')"
                                                    wire:target="agregarDespacho('{{ $despacho['despacho_id'] }}')"
                                                    wire:loading.attr="disabled"
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-md bg-primary-light text-primary hover:bg-primary hover:text-white font-semibold text-xs transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <span wire:loading.remove wire:target="agregarDespacho('{{ $despacho['despacho_id'] }}')">+ Agregar</span>
                                                    <span wire:loading wire:target="agregarDespacho('{{ $despacho['despacho_id'] }}')" class="inline-flex items-center gap-1">
                                                        <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                        </svg>
                                                        Agregando…
                                                    </span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-400 italic">No hay despachos disponibles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($despachosTotalPaginas > 1)
                        <div class="flex flex-wrap items-center justify-center gap-1 p-4 border-t border-gray-100 text-sm">
                            <button wire:click="irPaginaDespachos({{ $despachosPaginaActual - 1 }})"
                                @disabled($despachosPaginaActual <= 1)
                                class="px-3 py-1 rounded border border-gray-300 text-primary font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50">
                                Anterior
                            </button>

                            @foreach($despachosNumeros as $numero)
                                @if($numero === '...')
                                    <span class="px-2 text-gray-400">…</span>
                                @else
                                    <button wire:click="irPaginaDespachos({{ $numero }})"
                                        @class([
                                            'min-w-[2rem] px-2 py-1 rounded border font-semibold transition-colors',
                                            'bg-primary text-white border-primary' => $numero == $despachosPaginaActual,
                                            'border-gray-300 text-primary hover:bg-gray-50' => $numero != $despachosPaginaActual,
                                        ])>
                                        {{ $numero }}
                                    </button>
                                @endif
                            @endforeach

                            <button wire:click="irPaginaDespachos({{ $despachosPaginaActual + 1 }})"
                                @disabled($despachosPaginaActual >= $despachosTotalPaginas)
                                class="px-3 py-1 rounded border border-gray-300 text-primary font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50">
                                Siguiente
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Panel: Envíos sueltos -->
                <div x-show="tab === 'sueltos'" x-cloak class="bg-white shadow-md sm:rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg text-primary font-bold">Envíos al Descubierto (sin valija)</h2>
                        <p class="text-sm text-gray-500">Envíos disponibles que no están dentro de ninguna valija. Agrégalos con el buscador de código.</p>
                    </div>
                    <div class="overflow-x-auto overflow-y-auto max-h-96">
                        <table class="min-w-full text-sm border-separate border-spacing-0">
                            <thead>
                                <tr>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Código</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Servicio</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Peso (Gr)</th>
                                    <th class="sticky top-0 z-10 bg-primary-light px-4 py-2 text-left font-semibold text-primary">Despacho</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($sueltosPagina as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-2 font-medium">{{ $item['codigo'] ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $item['servicio'] ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $item['peso'] ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            {{-- Si el suelto ya esta asociado a un despacho abierto, se muestra
                                                 marcado con un boton para quitarlo. Si no, un selector para asociarlo.
                                                 Asociar/quitar persiste de inmediato (aparece en la guia al instante). --}}
                                            @if(!empty($item['despacho_num']))
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-primary-light text-primary text-xs font-semibold">
                                                        N° {{ $item['despacho_num'] }}
                                                        @if(!empty($item['despacho_destino'])) → {{ $item['despacho_destino'] }} @endif
                                                    </span>
                                                    <button wire:click="desasociarEnvioDeDespacho('{{ $item['envio_id'] }}')"
                                                        class="text-red-600 hover:text-red-800 text-xs font-semibold transition">
                                                        Quitar
                                                    </button>
                                                </div>
                                            @else
                                                <select
                                                    wire:change="asociarEnvioADespacho('{{ $item['envio_id'] }}', $event.target.value)"
                                                    class="block w-full text-xs border border-gray-300 rounded px-2 py-1 focus:ring-primary focus:border-primary">
                                                    <option value="">Agregar a despacho…</option>
                                                    @foreach($despachosPagina as $despacho)
                                                        <option value="{{ $despacho['despacho_id'] }}">
                                                            N° {{ $despacho['despacho_num'] }} → {{ $despacho['destino'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-gray-400 italic">No hay envíos al descubierto</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($sueltosTotalPaginas > 1)
                        <div class="flex flex-wrap items-center justify-center gap-1 p-4 border-t border-gray-100 text-sm">
                            <button wire:click="irPaginaSueltos({{ $sueltosPaginaActual - 1 }})"
                                @disabled($sueltosPaginaActual <= 1)
                                class="px-3 py-1 rounded border border-gray-300 text-primary font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50">
                                Anterior
                            </button>

                            @foreach($sueltosNumeros as $numero)
                                @if($numero === '...')
                                    <span class="px-2 text-gray-400">…</span>
                                @else
                                    <button wire:click="irPaginaSueltos({{ $numero }})"
                                        @class([
                                            'min-w-[2rem] px-2 py-1 rounded border font-semibold transition-colors',
                                            'bg-primary text-white border-primary' => $numero == $sueltosPaginaActual,
                                            'border-gray-300 text-primary hover:bg-gray-50' => $numero != $sueltosPaginaActual,
                                        ])>
                                        {{ $numero }}
                                    </button>
                                @endif
                            @endforeach

                            <button wire:click="irPaginaSueltos({{ $sueltosPaginaActual + 1 }})"
                                @disabled($sueltosPaginaActual >= $sueltosTotalPaginas)
                                class="px-3 py-1 rounded border border-gray-300 text-primary font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50">
                                Siguiente
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Selección de destino (viaje / transferencia) -->
                <div class="bg-white shadow-md sm:rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg text-primary font-bold">Destino de la salida</h2>
                        <p class="text-sm text-gray-500">Elige cómo saldrá el despacho: por un viaje programado o por transferencia directa.</p>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap items-center gap-6 mb-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="modoDestino" value="viaje" class="mr-2 text-primary focus:ring-primary">
                                <span class="font-semibold text-gray-700">Por viaje</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="modoDestino" value="transferencia" class="mr-2 text-primary focus:ring-primary">
                                <span class="font-semibold text-gray-700">Transferencia directa</span>
                            </label>
                        </div>

                        <!-- Viajes -->
                        @if($modoDestino === 'viaje')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($viajesConPuntosEntrega as $viaje)
                                    <div wire:click="seleccionarViaje('{{ $viaje->viaje_id }}')"
                                        class="block bg-white shadow rounded-lg p-4 border-2 cursor-pointer transition transform hover:scale-[1.02]
                                            {{ $selectedViajeId == $viaje->viaje_id ? 'border-primary ring-1 ring-primary' : 'border-gray-200 hover:shadow-lg' }}">
                                        <div class="flex items-start gap-2">
                                            <span class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 {{ $selectedViajeId == $viaje->viaje_id ? 'border-primary' : 'border-gray-300' }}">
                                                @if($selectedViajeId == $viaje->viaje_id)
                                                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                                                @endif
                                            </span>
                                            <div>
                                                <h3 class="text-base font-bold text-primary">Viaje {{ $viaje->codigo }}</h3>
                                                <p class="text-gray-600 mt-1 text-sm">Origen: {{ $viaje->ruta->oficinaOrigen->nombre }}</p>
                                                <p class="text-gray-600 text-sm">Ruta: {{ $viaje->ruta->ruta }}</p>
                                                <p class="text-gray-600 text-sm">Destino: {{ $viaje->ruta->oficinaDestino->nombre }}</p>
                                                <p class="text-gray-600 text-sm">Día de salida: {{ $viaje->diaSemana->dia_semana }}</p>
                                                <p class="text-gray-600 text-sm">Fecha de salida: {{ \Carbon\Carbon::parse($viaje->fecha_salida)->format('d/m/Y') }}</p>

                                                <div class="mt-3">
                                                    <h4 class="font-semibold text-primary text-sm">Puntos de entrega:</h4>
                                                    @if (!empty($viaje->puntos_entrega) && $viaje->puntos_entrega->isNotEmpty())
                                                        <ul class="list-disc pl-5 mt-1 text-gray-600 text-sm">
                                                            @foreach($viaje->puntos_entrega as $punto)
                                                                <li>{{ $punto->oficina->nombre ?? 'Nombre no disponible' }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-gray-500 text-sm mt-1">No hay puntos de entrega asignados.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center col-span-full py-6">No hay viajes disponibles esta semana.</p>
                                @endforelse
                            </div>
                        @endif

                        <!-- Transferencia directa -->
                        @if($modoDestino === 'transferencia')
                            {{-- Buscador + lista compacta con scroll propio: la COP cubre varios
                                 estados y la lista puede pasar de 50 oficinas. Con tarjetas grandes
                                 la pagina se alargaba muchisimo para elegir un solo destino. --}}
                            <div class="mb-3">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                        </svg>
                                    </span>
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="busquedaOficina"
                                        class="border border-gray-300 rounded-md pl-9 pr-9 py-2 w-full text-sm focus:ring-primary focus:border-primary"
                                        placeholder="Buscar oficina por nombre o código…"
                                    >
                                    @if($busquedaOficina !== '')
                                        <button type="button" wire:click="$set('busquedaOficina', '')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                            title="Limpiar búsqueda">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Si la oficina elegida quedo fuera del filtro, se muestra igual aqui
                                 arriba para que el operador nunca pierda de vista su destino. --}}
                            @if($oficinaTransferenciaSel && !$oficinasEstados->contains('oficina_id', $oficinaTransferenciaSel->oficina_id))
                                <div class="mb-3 flex items-center justify-between gap-2 rounded-md border-2 border-primary bg-primary-light px-3 py-2">
                                    <div class="text-sm min-w-0">
                                        <span class="font-semibold text-primary">Destino seleccionado:</span>
                                        <span class="text-gray-700">
                                            {{ $oficinaTransferenciaSel->externa ? 'Aliado' : 'Oficina ' . $oficinaTransferenciaSel->codigo }}
                                            · {{ $oficinaTransferenciaSel->nombre }}
                                        </span>
                                        <span class="block text-xs text-gray-500">No coincide con la búsqueda actual.</span>
                                    </div>
                                    <button wire:click="seleccionarTransferencia('{{ $oficinaTransferenciaSel->oficina_id }}')"
                                        class="shrink-0 text-red-600 hover:text-red-800 text-xs font-semibold transition">
                                        Quitar
                                    </button>
                                </div>
                            @endif

                            <div class="border border-gray-200 rounded-md overflow-y-auto max-h-80 divide-y divide-gray-100">
                                @forelse($oficinasEstados as $oficina)
                                    <div wire:click="seleccionarTransferencia('{{ $oficina->oficina_id }}')"
                                        class="flex items-start gap-2 px-3 py-2 cursor-pointer transition-colors
                                            {{ $transferenciaOficina == $oficina->oficina_id ? 'bg-primary-light' : 'hover:bg-gray-50' }}">
                                        <span class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 {{ $transferenciaOficina == $oficina->oficina_id ? 'border-primary' : 'border-gray-300' }}">
                                            @if($transferenciaOficina == $oficina->oficina_id)
                                                <span class="h-2 w-2 rounded-full bg-primary"></span>
                                            @endif
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold {{ $transferenciaOficina == $oficina->oficina_id ? 'text-primary' : 'text-gray-800' }}">
                                                @if($oficina->externa)
                                                    <span class="inline-block mr-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-bold align-middle">ALIADO</span>
                                                @else
                                                    <span class="text-gray-400 font-normal">{{ $oficina->codigo }}</span> ·
                                                @endif
                                                {{ $oficina->nombre }}
                                            </div>
                                            @if(!$oficina->externa)
                                                {{-- La direccion promedio ronda los 96 caracteres y muchas traen dos
                                                     ubicaciones separadas por "/", asi que una sola linea cortaba casi
                                                     todas. Se muestran 2 lineas y la fila seleccionada la expande
                                                     completa: es cuando el operador quiere verificar el destino. --}}
                                                <div class="text-xs text-gray-500 whitespace-pre-line {{ $transferenciaOficina == $oficina->oficina_id ? '' : 'line-clamp-2' }}">
                                                    {{ $oficina->direccion ?: 'Sin dirección' }}
                                                </div>
                                                @if($oficina->telefono)
                                                    <div class="text-xs text-gray-400 mt-0.5">Tel: {{ $oficina->telefono }}</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-3 py-8 text-center text-gray-400 text-sm italic">
                                        No hay oficinas que coincidan con «{{ $busquedaOficina }}».
                                    </div>
                                @endforelse
                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                @if($busquedaOficina !== '')
                                    Mostrando {{ $oficinasEstados->count() }} de {{ $oficinasTotal }} oficina(s).
                                @else
                                    {{ $oficinasTotal }} oficina(s) disponible(s). Escribe para filtrar.
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ═══════════ COLUMNA DERECHA: CARRITO STICKY ═══════════ -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-md sm:rounded-lg border border-gray-200 overflow-hidden lg:sticky lg:top-6">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg text-primary font-bold">Tu despacho</h2>
                        @php
                            $totalItems = collect($carritoPorDespacho)->flatten(1)->count();
                        @endphp
                        <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 rounded-full bg-primary text-white text-xs font-bold">
                            {{ $totalItems }}
                        </span>
                    </div>

                    <div class="max-h-[28rem] overflow-y-auto divide-y divide-gray-100">
                        @forelse($carritoPorDespacho as $despachoNum => $itemsDespacho)
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-semibold text-primary">
                                        @if($despachoNum === '__sin__')
                                            Sin despacho
                                        @else
                                            Despacho N° {{ $despachoNum }}
                                        @endif
                                        @php
                                            $numValijas = collect($itemsDespacho)->where('etiqueta', 'Valija')->count();
                                            $numSueltos = collect($itemsDespacho)->where('etiqueta', 'Envío')->count();
                                        @endphp
                                        <span class="text-gray-400 font-normal">
                                            · {{ $numValijas }} valija(s)@if($numSueltos > 0), {{ $numSueltos }} suelto(s)@endif
                                        </span>
                                    </div>
                                    @if($despachoNum !== '__sin__')
                                        <button wire:click="quitarDespacho('{{ $despachoNum }}')"
                                            class="text-red-600 hover:text-red-800 text-xs font-semibold transition">
                                            Quitar
                                        </button>
                                    @endif
                                </div>
                                <ul class="space-y-1">
                                    @foreach($itemsDespacho as $fila)
                                        <li class="flex items-center justify-between text-sm bg-gray-50 rounded px-2 py-1.5">
                                            <div class="min-w-0">
                                                <span class="font-medium text-gray-800">{{ $fila['codigo'] }}</span>
                                                <span class="inline-block ml-1 px-1.5 py-0.5 rounded bg-primary-light text-primary text-[10px] font-semibold align-middle">{{ $fila['etiqueta'] }}</span>
                                                <div class="text-xs text-gray-500 truncate">{{ $fila['servicio'] }} · {{ $fila['peso'] }} Gr</div>
                                            </div>
                                            {{-- Quitar por item solo para los sin-despacho. Los items de un
                                                 despacho (valijas y sueltos asociados) se quitan en bloque con
                                                 el boton "Quitar" del despacho; el suelto se desasocia desde la
                                                 lista de "Envios al Descubierto". --}}
                                            @if($despachoNum === '__sin__')
                                                <button wire:click="quitarDeCarrito({{ $fila['idx'] }})"
                                                    class="ml-2 shrink-0 text-red-600 hover:text-red-800 text-xs font-semibold transition">
                                                    Quitar
                                                </button>
                                            @elseif($fila['etiqueta'] === 'Valija')
                                                {{-- Una valija que no se va a despachar se ELIMINA: sus envios
                                                     vuelven al almacen y el resto del despacho puede salir. --}}
                                                <button type="button"
                                                    wire:click="$set('valijaAEliminar', {{ $fila['saca_id'] }})"
                                                    class="ml-2 shrink-0 text-red-600 hover:text-red-800 text-xs font-semibold transition">
                                                    Eliminar
                                                </button>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <p class="italic">Aún no has agregado envíos.</p>
                                <p class="text-xs mt-1">Usa el buscador o los despachos disponibles.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pie: resumen + acción -->
                    <div class="p-4 border-t border-gray-100 bg-gray-50 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total de items</span>
                            <span class="font-bold text-primary">{{ $totalItems }}</span>
                        </div>
                        <x-primary-button
                            wire:click="create()"
                            wire:loading.attr="disabled"
                            wire:target="create"
                            :disabled="!($selectedViajeId || $transferenciaOficina)"
                            class="w-full justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="create">Realizar salida</span>
                            <span wire:loading wire:target="create">Procesando...</span>
                        </x-primary-button>
                        @if(!($selectedViajeId || $transferenciaOficina))
                            <p class="text-xs text-center text-gray-400">Selecciona un destino para habilitar la salida.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Aviso previo a eliminar una valija del despacho. Es irreversible y libera
         sus envios, asi que se indica cuantos son antes de confirmar. --}}
    @if($valijaAEliminarInfo)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
             role="dialog" aria-modal="true" aria-labelledby="tituloAvisoEliminarValija">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <h3 id="tituloAvisoEliminarValija" class="text-lg font-bold text-gray-900">
                            ¿Eliminar la valija {{ $valijaAEliminarInfo['codigo'] }}?
                        </h3>
                        <p class="mt-2 text-sm text-gray-700">
                            La valija se eliminará para poder despachar el resto del despacho.
                            @if($valijaAEliminarInfo['envios'] > 0)
                                Sus <strong>{{ $valijaAEliminarInfo['envios'] }} envío(s)</strong>
                                volverán al almacén y quedarán disponibles para cargarse en una
                                valija nueva.
                            @else
                                No tiene envíos dentro.
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-gray-700">
                            Deberá crearse de nuevo cuando vaya a despacharse, con otro número
                            de despacho. Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('valijaAEliminar', null)"
                            class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" wire:click="eliminarValijaDelDespacho({{ $valijaAEliminar }})"
                            wire:loading.attr="disabled" wire:target="eliminarValijaDelDespacho"
                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 disabled:cursor-wait">
                        <span wire:loading.remove wire:target="eliminarValijaDelDespacho">Sí, eliminar</span>
                        <span wire:loading wire:target="eliminarValijaDelDespacho">Eliminando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('tarifaUpdated', () => {
            setTimeout(() => {
                location.reload();
            }, 1000);
        });

        Livewire.on('alertError', ({ message }) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@endpush