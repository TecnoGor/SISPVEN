@php
    // Filtrar por búsqueda si hay texto
    $reciente_filtrados = $comunicaciones_recientes;
    $busqueda = $busqueda_actividad ?? '';
    if ($busqueda !== '') {
        $q = strtolower($busqueda);
        $reciente_filtrados = array_values(array_filter($comunicaciones_recientes, function ($doc) use ($q) {
            return str_contains(strtolower($doc['id'] ?? ''), $q)
                || str_contains(strtolower($doc['subject'] ?? ''), $q)
                || str_contains(strtolower($doc['sender'] ?? ''), $q)
                || str_contains(strtolower($doc['destinatario'] ?? ''), $q)
                || str_contains(strtolower($doc['type'] ?? ''), $q);
        }));
    }

    $reciente_total = count($reciente_filtrados);
    $reciente_total_paginas = max(1, (int) ceil($reciente_total / $por_pagina_reciente));
    $reciente_offset = ($pagina_reciente - 1) * $por_pagina_reciente;
    $reciente_pagina_docs = array_slice($reciente_filtrados, $reciente_offset, $por_pagina_reciente);
    $reciente_desde = $reciente_total > 0 ? $reciente_offset + 1 : 0;
    $reciente_hasta = min($reciente_offset + $por_pagina_reciente, $reciente_total);
@endphp

{{-- Actividad Reciente --}}
<div class="mt-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Actividad Reciente
            @if($busqueda !== '')
                <span class="text-xs font-semibold text-gray-400">({{ $reciente_total }} resultado{{ $reciente_total !== 1 ? 's' : '' }})</span>
            @endif
        </h3>
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="busqueda_actividad" type="text"
                class="w-full pl-9 pr-8 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm text-sm"
                placeholder="Buscar por código, asunto, remitente...">
            @if($busqueda !== '')
                <button wire:click="$set('busqueda_actividad', '')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
    </div>
    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-xs border-b border-gray-200">
                <tr>
                    <th class="px-5 py-4 w-40">Remitente</th>
                    <th class="px-5 py-4">Asunto</th>
                    <th class="px-5 py-4 w-40">Destinatario</th>
                    <th class="px-5 py-4 w-28 whitespace-nowrap">Fecha</th>
                    <th class="px-5 py-4 w-24 text-center">Prioridad</th>
                    <th class="px-5 py-4 w-36 text-center">Estatus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reciente_pagina_docs as $doc)
                    <tr wire:click="open_detail_sent('{{ $doc['id'] }}')"
                        class="bg-white hover:bg-red-50 cursor-pointer transition group
                            {{ $doc['status'] === 'Pendiente' ? 'font-bold' : 'font-normal' }}">

                        <td class="px-5 py-4 text-gray-800 max-w-[160px]"><div class="truncate">{{ $doc['sender'] }}</div></td>

                        <td class="px-5 py-4 max-w-0">
                            <div class="truncate font-bold text-gray-900" title="{{ $doc['subject'] }}">
                                {{ $doc['subject'] }}
                            </div>
                            <div class="flex items-center gap-1 mt-0.5 truncate">
                                @if(($doc['motivo_id'] ?? null) == 5)
                                    <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-purple-100 text-purple-700 border border-purple-200">REMITIDO</span>
                                @endif
                                @if(($doc['motivo_id'] ?? null) == 7)
                                    <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-yellow-100 text-yellow-700 border border-yellow-200">DEVUELTO</span>
                                @endif
                                @if(($doc['correcciones'] ?? 0) > 0)
                                    <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-100 text-amber-700 border border-amber-200">CORREC. {{ $doc['correcciones'] }}</span>
                                @endif
                                @if(!empty($doc['reference']))
                                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider font-mono" title="{{ $doc['reference'] }}">Ref: {{ $doc['reference'] }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4 text-gray-800 max-w-[160px]"><div class="truncate">{{ $doc['destinatario'] ?? 'N/A' }}</div></td>

                        <td class="px-5 py-4 text-gray-600 font-medium whitespace-nowrap">{{ $doc['date'] }}</td>

                        <td class="px-5 py-4 text-center">
                            @if($doc['priority'] == 'Urgente')
                                <span class="inline-flex px-2 py-1 text-[10px] font-black rounded uppercase tracking-wider bg-red-100 text-red-800">
                                    {{ $doc['priority'] }}
                                </span>
                            @elseif($doc['priority'] == 'Alta')
                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded uppercase tracking-wider bg-orange-100 text-orange-800">
                                    {{ $doc['priority'] }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded uppercase tracking-wider bg-gray-100 text-gray-800">
                                    {{ $doc['priority'] }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full border {{ $doc['status_color'] ?? 'bg-gray-100 text-gray-800' }} uppercase tracking-wider">
                                {{ $doc['status'] }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 font-medium">No hay actividad reciente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($reciente_total > 0)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Mostrando <span class="font-bold text-gray-800">{{ $reciente_desde }}</span> a <span class="font-bold text-gray-800">{{ $reciente_hasta }}</span> de <span class="font-bold text-gray-800">{{ $reciente_total }}</span></span>
                <select wire:model.live="por_pagina_reciente" class="ml-2 border border-gray-300 rounded-md text-xs font-semibold text-slate-700 bg-white py-1 px-2 focus:border-red-400 focus:ring-red-400">
                    <option value="10">10 / pág</option>
                    <option value="25">25 / pág</option>
                    <option value="50">50 / pág</option>
                </select>
            </div>
            <div class="flex items-center gap-1">
                <button wire:click="reciente_pagina(1)" @disabled($pagina_reciente <= 1)
                    class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_reciente <= 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                    «
                </button>
                <button wire:click="reciente_pagina({{ $pagina_reciente - 1 }})" @disabled($pagina_reciente <= 1)
                    class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_reciente <= 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                    ‹
                </button>
                @for($p = max(1, $pagina_reciente - 2); $p <= min($reciente_total_paginas, $pagina_reciente + 2); $p++)
                    <button wire:click="reciente_pagina({{ $p }})"
                        class="px-3 py-1 rounded text-xs font-bold border {{ $p === $pagina_reciente ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                        {{ $p }}
                    </button>
                @endfor
                <button wire:click="reciente_pagina({{ $pagina_reciente + 1 }})" @disabled($pagina_reciente >= $reciente_total_paginas)
                    class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_reciente >= $reciente_total_paginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                    ›
                </button>
                <button wire:click="reciente_pagina({{ $reciente_total_paginas }})" @disabled($pagina_reciente >= $reciente_total_paginas)
                    class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_reciente >= $reciente_total_paginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                    »
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
