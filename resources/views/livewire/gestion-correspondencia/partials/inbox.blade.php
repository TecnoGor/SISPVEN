{{-- Barra superior con botón de volver y Filtros --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
    <button wire:click="open_dashboard"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-red-600 transition shadow-sm w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Volver al Panel
    </button>

    {{-- Panel de Filtros --}}
    <div
        class="flex flex-wrap items-end gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm w-fit ml-auto">
        {{-- Filtro de Prioridad --}}
        <div class="min-w-[140px]">
            <label for="filtro_prioridad" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
            <select id="filtro_prioridad" wire:model.live="filtro_prioridad"
                class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Todas</option>
                <option value="Normal">Normal</option>
                <option value="Alta">Alta</option>
                <option value="Urgente">Urgente</option>
            </select>
        </div>

        {{-- Filtro de Estatus --}}
        <div class="min-w-[140px]">
            <label for="filtro_estatus" class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
            <select id="filtro_estatus" wire:model.live="filtro_estatus"
                class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Todos</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Leído">Leído</option>
                <option value="Confirmación de Recibido">Recibido</option>
                <option value="Respondido">Respondido</option>
                <option value="Remitido">Remitido</option>
                <option value="Aprobado / Firmado">Aprobado / Firmado</option>
            </select>
        </div>
 
        {{-- Filtro de Fechas --}}
        <div class="flex gap-4 min-w-[280px]">
            <div class="flex-1">
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" id="fecha_inicio" wire:model.live="fecha_inicio" 
                    class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50 text-sm focus:border-red-500 focus:ring-red-500" 
                    lang="es">
            </div>
            <div class="flex-1">
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" id="fecha_fin" wire:model.live="fecha_fin" 
                    class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50 text-sm focus:border-red-500 focus:ring-red-500" 
                    lang="es">
            </div>
        </div>

        <button wire:click="limpiar_filtros"
            class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-1 h-[38px] mb-[1px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Limpiar
        </button>
    </div>
</div>

@php
    $inbox_total = count($comunicaciones);
    $inbox_total_paginas = max(1, (int) ceil($inbox_total / $por_pagina_inbox));
    $inbox_offset = ($pagina_inbox - 1) * $por_pagina_inbox;
    $inbox_pagina_docs = array_slice($comunicaciones, $inbox_offset, $por_pagina_inbox);
    $inbox_desde = $inbox_total > 0 ? $inbox_offset + 1 : 0;
    $inbox_hasta = min($inbox_offset + $por_pagina_inbox, $inbox_total);
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-xs border-b border-gray-200">
            <tr>
                <th class="px-5 py-4 w-40">Remitente</th>
                <th class="px-5 py-4">Asunto</th>
                <th class="px-5 py-4 w-40">Destinatario</th>
                <th class="px-5 py-4 w-28 whitespace-nowrap">Fecha</th>
                <th class="px-5 py-4 w-24">Prioridad</th>
                <th class="px-5 py-4 w-36 text-center">Estatus</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($inbox_pagina_docs as $doc)
                <tr wire:click="open_detail('{{ $doc['id'] }}')"
                    class="cursor-pointer transition group
                        {{ $doc['status'] === 'Pendiente' && !($doc['ya_actuo_como_emisor'] ?? false)
                            ? 'bg-orange-50 hover:bg-orange-100 font-bold'
                            : 'bg-white hover:bg-red-50 font-normal' }}">
                    <td class="px-5 py-4 text-gray-800 group-hover:text-red-700 transition max-w-[160px]"><div class="truncate">{{ $doc['sender'] }}</div></td>
                    <td class="px-5 py-4 max-w-0">
                        <div class="truncate font-bold text-gray-900 group-hover:text-red-700 transition" title="{{ $doc['subject'] }}">
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
                            @if ($doc['referencia'])
                                <span class="text-xs text-gray-400 font-mono" title="{{ $doc['referencia'] }}">Ref: {{ $doc['referencia'] }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-700 max-w-[160px]"><div class="truncate">{{ $doc['destinatario'] ?? 'Yo (' . $current_role . ')' }}</div></td>
                    <td class="px-5 py-4 text-gray-500 whitespace-nowrap">{{ $doc['date'] }}</td>
                    <td class="px-5 py-4">
                        <span
                            class="px-2 py-1 text-xs font-bold rounded-md {{ $doc['priority'] == 'Urgente' ? 'bg-red-100 text-red-700' : ($doc['priority'] == 'Alta' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $doc['priority'] }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span
                            class="px-3 py-1 text-[11px] font-bold rounded-full border whitespace-nowrap {{ $doc['status_color'] ?? 'bg-gray-100 text-gray-800 border-gray-200' }} uppercase tracking-wider">
                            {{ $doc['status'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400 font-medium">No hay comunicaciones en la bandeja de entrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginación --}}
    @if($inbox_total > 0)
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3 border-t border-gray-100 bg-gray-50">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Mostrando <span class="font-bold text-gray-800">{{ $inbox_desde }}</span> a <span class="font-bold text-gray-800">{{ $inbox_hasta }}</span> de <span class="font-bold text-gray-800">{{ $inbox_total }}</span></span>
            <select wire:model.live="por_pagina_inbox" class="ml-2 border border-gray-300 rounded-md text-xs font-semibold text-slate-700 bg-white py-1 px-2 focus:border-red-400 focus:ring-red-400">
                <option value="10">10 / pág</option>
                <option value="25">25 / pág</option>
                <option value="50">50 / pág</option>
            </select>
        </div>
        <div class="flex items-center gap-1">
            <button wire:click="inbox_pagina(1)" @disabled($pagina_inbox <= 1)
                class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_inbox <= 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                «
            </button>
            <button wire:click="inbox_pagina({{ $pagina_inbox - 1 }})" @disabled($pagina_inbox <= 1)
                class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_inbox <= 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                ‹
            </button>
            @for($p = max(1, $pagina_inbox - 2); $p <= min($inbox_total_paginas, $pagina_inbox + 2); $p++)
                <button wire:click="inbox_pagina({{ $p }})"
                    class="px-3 py-1 rounded text-xs font-bold border {{ $p === $pagina_inbox ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    {{ $p }}
                </button>
            @endfor
            <button wire:click="inbox_pagina({{ $pagina_inbox + 1 }})" @disabled($pagina_inbox >= $inbox_total_paginas)
                class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_inbox >= $inbox_total_paginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                ›
            </button>
            <button wire:click="inbox_pagina({{ $inbox_total_paginas }})" @disabled($pagina_inbox >= $inbox_total_paginas)
                class="px-2 py-1 rounded text-xs font-bold border {{ $pagina_inbox >= $inbox_total_paginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                »
            </button>
        </div>
    </div>
    @endif
</div>
