<div>
    @section('titulo')
        Recolectas
    @endsection
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">
            Recolectas Solicitadas
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Solicitudes de recolecta a domicilio creadas desde la app de clientes para esta oficina.
        </p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            {{-- BARRA DE BÚSQUEDA Y FILTROS --}}
            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-1/3">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar por código, cédula o remitente...">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <select wire:model.live="filtro_estatus"
                            class="border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                            <option value="">Todos los estados</option>
                            @foreach ($estatus_disponibles as $estatus)
                                <option value="{{ $estatus->slug }}">{{ $estatus->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- FILTROS DE FECHA --}}
            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start border-t border-gray-100 pt-4">
                <div class="w-full md:w-1/4">
                    <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input id="desde" type="date" wire:model.live="desde"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input id="hasta" type="date" wire:model.live="hasta"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50">
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Código</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Remitente</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Dirección de Recolecta</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Peso (kg)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Total Bs</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Pago</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">GIT</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($recolectas as $recolecta)
                            <tr wire:key="{{ $recolecta->recolecta_id }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-primary">{{ $recolecta->codigo }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">
                                    {{ $recolecta->nombre_rem }} {{ $recolecta->apellido_rem }}
                                    <div class="text-xs text-gray-400">{{ $recolecta->tipo_documento_rem }}-{{ $recolecta->documento_rem }} · {{ $recolecta->telefono_rem }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 max-w-xs">
                                    <div class="truncate" title="{{ $recolecta->direccion }}">{{ $recolecta->direccion }}</div>
                                    @if ($recolecta->latitud !== null && $recolecta->longitud !== null)
                                        <a href="https://www.openstreetmap.org/?mlat={{ $recolecta->latitud }}&mlon={{ $recolecta->longitud }}#map=17/{{ $recolecta->latitud }}/{{ $recolecta->longitud }}"
                                            target="_blank" class="text-xs text-blue-600 hover:underline">
                                            Ver en mapa ({{ $recolecta->gps_manual ? 'pin manual' : 'GPS' }})
                                        </a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">{{ $recolecta->peso }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600">{{ number_format((float) $recolecta->total, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @forelse ($recolecta->pagos as $pago)
                                        <div class="text-xs text-gray-600">
                                            {{ $pago->tipoPago?->nombre }}
                                            @if ($pago->numero_referencia)
                                                · Ref: {{ $pago->numero_referencia }}
                                            @endif
                                            <span class="px-1.5 inline-flex text-[10px] leading-4 font-semibold rounded-full
                                                {{ $pago->estatus === 'confirmado' ? 'bg-green-100 text-green-800' : ($pago->estatus === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($pago->estatus) }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-gray-400">Sin pago</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @php
                                        $slug = $recolecta->estatus?->slug;
                                        $colores = [
                                            'solicitada' => 'bg-blue-100 text-blue-800',
                                            'pago_reportado' => 'bg-yellow-100 text-yellow-800',
                                            'pago_confirmado' => 'bg-indigo-100 text-indigo-800',
                                            'recolectada' => 'bg-green-100 text-green-800',
                                            'cancelada' => 'bg-gray-100 text-gray-800',
                                            'rechazada' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colores[$slug] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $recolecta->estatus?->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-primary">
                                    {{ $recolecta->envio?->codigo_envio ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($slug === 'pago_reportado')
                                            <button wire:click="confirmarPago({{ $recolecta->recolecta_id }})"
                                                wire:confirm="¿Confirmar que el pago reportado fue verificado?"
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-800 transition shadow-sm">
                                                Confirmar Pago
                                            </button>
                                        @endif
                                        @if ($slug === 'pago_confirmado')
                                            <button wire:click="procesar({{ $recolecta->recolecta_id }})"
                                                wire:confirm="¿Registrar el paquete como recolectado y crear el envío?"
                                                class="inline-flex items-center px-3 py-1.5 bg-[#6b1820] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition shadow-sm">
                                                Procesar
                                            </button>
                                        @endif
                                        @if (in_array($slug, ['solicitada', 'pago_reportado', 'pago_confirmado'], true))
                                            <button wire:click="abrirRechazo({{ $recolecta->recolecta_id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition shadow-sm">
                                                Rechazar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    No hay recolectas para esta oficina.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $recolectas->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL RECHAZO --}}
    @if ($modal_rechazo)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                <h2 class="text-lg font-bold text-primary mb-2">Rechazar Recolecta</h2>
                <p class="text-sm text-gray-600 mb-4">Indique el motivo del rechazo. El cliente lo verá en la app.</p>

                <textarea wire:model="motivo_rechazo" rows="3" maxlength="255"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-3 bg-gray-50"
                    placeholder="Motivo del rechazo..."></textarea>
                @error('motivo_rechazo')
                    <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="cerrarRechazo"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button wire:click="rechazar"
                        class="inline-flex items-center px-4 py-2 bg-red-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        Rechazar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
