<div class="p-6">
    @php
        $user = auth()->user();
        $backRoute = match(true) {
            $user->hasRole('Presidente Correspondencia') => route('correspondencia.presidente'),
            $user->hasRole('Director Correspondencia')   => route('correspondencia.director'),
            $user->hasRole('Gerente Correspondencia')    => route('correspondencia.gerente'),
            default                                       => route('correspondencia'),
        };
    @endphp
    <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reporte de Comunicados</h1>
            <p class="text-sm text-gray-500">Listado consolidado de todos los comunicados, su estado actual y responsable.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="exportarPdf" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span wire:loading.remove wire:target="exportarPdf">Exportar PDF</span>
                <span wire:loading wire:target="exportarPdf">Generando...</span>
            </button>
            <a href="{{ $backRoute }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-red-600 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                <input type="date" wire:model.live="fecha_inicio"
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                <input type="date" wire:model.live="fecha_fin"
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
                <select wire:model.live="tipo"
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Estatus</label>
                <select wire:model.live="estatus_id"
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    @foreach($estatus as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar código o asunto..."
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="mt-3 flex justify-end">
            <button wire:click="limpiarFiltros" class="text-xs text-gray-500 hover:text-gray-700 underline">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="flex items-center gap-4 text-xs text-gray-600 mb-3 flex-wrap">
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded bg-red-100 border border-red-300"></span> Vencido</span>
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded bg-yellow-100 border border-yellow-300"></span> Vence en menos de 24h</span>
        <span class="ml-auto text-gray-500">Total: <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span></span>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-gray-50 text-[11px] font-semibold text-gray-600 uppercase">
                    <tr>
                        <th class="px-3 py-3 border-b">Código</th>
                        <th class="px-3 py-3 border-b">Tipo</th>
                        <th class="px-3 py-3 border-b">Asunto</th>
                        <th class="px-3 py-3 border-b">Prioridad</th>
                        <th class="px-3 py-3 border-b">Creado por</th>
                        <th class="px-3 py-3 border-b">Destinatario final</th>
                        <th class="px-3 py-3 border-b">Quien lo tiene ahora</th>
                        <th class="px-3 py-3 border-b">Estatus</th>
                        <th class="px-3 py-3 border-b">F. Creación</th>
                        <th class="px-3 py-3 border-b">F. Límite</th>
                        <th class="px-3 py-3 border-b text-center">Días</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $r)
                        <tr class="{{ $r->vencido ? 'bg-red-50 hover:bg-red-100' : ($r->proximo_vencer ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-gray-50') }} transition-colors">
                            <td class="px-3 py-3 font-mono text-xs font-semibold text-gray-800 whitespace-nowrap">{{ $r->codigo }}</td>
                            <td class="px-3 py-3 text-gray-700 whitespace-nowrap">{{ $r->tipo }}</td>
                            <td class="px-3 py-3 text-gray-700 max-w-xs truncate" title="{{ $r->asunto }}">{{ $r->asunto }}</td>
                            <td class="px-3 py-3">
                                @php
                                    $pColor = match(strtolower($r->prioridad ?? '')) {
                                        'alta' => 'bg-red-100 text-red-700',
                                        'media' => 'bg-yellow-100 text-yellow-700',
                                        'baja' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-[10px] font-bold rounded uppercase {{ $pColor }}">
                                    {{ $r->prioridad ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-gray-700">{{ $r->creado_por }}</td>
                            <td class="px-3 py-3 text-gray-700">{{ $r->destinatario_final }}</td>
                            <td class="px-3 py-3 font-medium text-gray-900">{{ $r->tiene_ahora }}</td>
                            <td class="px-3 py-3">
                                <span class="px-2 py-1 text-[10px] font-semibold rounded border {{ $r->estatus_color }}">
                                    {{ $r->estatus }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $r->fecha_creacion }}</td>
                            <td class="px-3 py-3 whitespace-nowrap {{ $r->vencido ? 'text-red-700 font-bold' : ($r->proximo_vencer ? 'text-yellow-700 font-semibold' : 'text-gray-600') }}">
                                {{ $r->fecha_limite }}
                            </td>
                            <td class="px-3 py-3 text-center text-gray-700 font-semibold">{{ $r->dias_transcurridos }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-3 py-8 text-center text-gray-500">
                                No se encontraron comunicados con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-200">
            {{ $paginator->links() }}
        </div>
    </div>
</div>
