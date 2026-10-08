<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Instrucciones Recibidas
        </h3>
        <select wire:model.live="filtro_estatus" class="text-xs border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <option value="Pendiente">Pendientes</option>
            <option value="Completado">Completadas</option>
            <option value="Todas">Todas</option>
        </select>
    </div>

    @if(session()->has('asignacion_analista_error'))
        <div class="mx-5 mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            {{ session('asignacion_analista_error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        @if($asignaciones->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">
                No tienes instrucciones {{ strtolower($filtro_estatus === 'Todas' ? '' : $filtro_estatus) }}.
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($asignaciones as $a)
                    <div class="px-5 py-4 flex items-start justify-between gap-4 hover:bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs text-gray-500">{{ $a->codigo }}</span>
                                @if($a->estatus === 'Completado')
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-700">Completado</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700">Pendiente</span>
                                @endif
                                @if($a->tipo_documento_esperado)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $a->tipo_documento_esperado }}
                                    </span>
                                @endif
                            </div>
                            <p class="font-semibold text-slate-800 truncate">{{ $a->asunto_instruccion }}</p>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $a->detalle_instruccion }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                <span>De: <span class="font-semibold text-gray-700">{{ $a->emisor?->name ?? '—' }}</span></span>
                                @if($a->fecha_limite)
                                    <span>•</span>
                                    <span>Límite: <span class="font-semibold text-gray-700">{{ $a->fecha_limite->format('d/m/Y') }}</span></span>
                                @endif
                                @if($a->comunicadoGenerado)
                                    <span>•</span>
                                    <span>Comunicado: <span class="font-mono font-semibold text-green-700">{{ $a->comunicadoGenerado->codigo }}</span></span>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($a->estatus === 'Pendiente')
                                <button wire:click="atender({{ $a->id }})"
                                        class="px-3 py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition">
                                    Atender y Generar Comunicado
                                </button>
                            @else
                                <span class="text-xs text-gray-400">Entregado</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
