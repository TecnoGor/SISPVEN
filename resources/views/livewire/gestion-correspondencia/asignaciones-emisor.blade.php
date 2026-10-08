<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    {{-- Encabezado --}}
    @php
        $user = auth()->user();
        $backRoute = match(true) {
            $user->hasRole('Presidente Correspondencia') => route('correspondencia.presidente'),
            $user->hasRole('Director Correspondencia')   => route('correspondencia.director'),
            $user->hasRole('Gerente Correspondencia')    => route('correspondencia.gerente'),
            default                                       => route('correspondencia'),
        };
    @endphp
    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Instrucciones Emitidas</h2>
            <p class="text-slate-500 text-sm mt-1">
                Rol Activo: <span class="font-bold text-red-600">{{ $user->getRoleNames()->first() }}</span>
            </p>
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button wire:click="abrir_modal"
                class="px-4 py-2 bg-primary text-white font-bold rounded-lg shadow hover:bg-blue-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Emitir Instrucción
            </button>
            <div class="min-w-[140px]">
                <select wire:model.live="filtro_estatus"
                    class="block w-full border-gray-300 rounded-md shadow-sm font-semibold text-slate-700 bg-gray-50 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="Todas">Todas</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Completado">Completado</option>
                </select>
            </div>
            <a href="{{ $backRoute }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-red-600 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Alerta éxito --}}
    @if(session()->has('asignacion_emisor_ok'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('asignacion_emisor_ok') }}
        </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-xs border-b border-gray-200">
                <tr>
                    <th class="px-5 py-4 w-36">Código</th>
                    <th class="px-5 py-4 w-44">Analista</th>
                    <th class="px-5 py-4">Asunto</th>
                    <th class="px-5 py-4 w-32 whitespace-nowrap">Fecha Límite</th>
                    <th class="px-5 py-4 w-36 text-center">Estatus</th>
                    <th class="px-5 py-4 w-44">Comunicado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($asignaciones as $a)
                    <tr wire:key="asig-{{ $a->id }}" class="bg-white hover:bg-red-50 transition group">

                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-gray-700">{{ $a->codigo }}</span>
                        </td>

                        <td class="px-5 py-4 text-gray-800 max-w-[176px]">
                            <div class="truncate">{{ $a->analista?->name ?? '—' }}</div>
                        </td>

                        <td class="px-5 py-4 max-w-0">
                            <div class="truncate font-bold text-gray-900 group-hover:text-red-700 transition" title="{{ $a->asunto_instruccion }}">
                                {{ $a->asunto_instruccion }}
                            </div>
                            @if($a->tipo_documento_esperado)
                                <span class="inline-flex mt-0.5 px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                                    {{ $a->tipo_documento_esperado }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($a->fecha_limite)
                                <span class="text-sm {{ $a->estatus !== 'Completado' && $a->fecha_limite->isPast() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $a->fecha_limite->format('d/m/Y') }}
                                    @if($a->estatus !== 'Completado' && $a->fecha_limite->isPast())
                                        <span class="block text-[9px] font-black uppercase text-red-500">Vencido</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center">
                            @if($a->estatus === 'Completado')
                                <span class="px-3 py-1 text-[11px] font-bold rounded-full border bg-green-100 text-green-800 border-green-200 uppercase tracking-wider whitespace-nowrap">
                                    Completado
                                </span>
                            @else
                                <span class="px-3 py-1 text-[11px] font-bold rounded-full border bg-yellow-100 text-yellow-800 border-yellow-200 uppercase tracking-wider whitespace-nowrap">
                                    Pendiente
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if($a->comunicadoGenerado)
                                <span class="font-mono text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-1 rounded">
                                    {{ $a->comunicadoGenerado->codigo }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs italic">Sin comunicado</span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400 font-medium">
                            No hay instrucciones emitidas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Emitir Instrucción --}}
    @if($modal_abierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" wire:key="modal-emitir-instruccion">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-slate-800">Emitir Instrucción a Analista</h3>
                    <button wire:click="cerrar_modal" class="text-gray-400 hover:text-red-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit.prevent="emitir_instruccion" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Analista <span class="text-red-500">*</span></label>
                        <select wire:model="analista_id" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm">
                            <option value="">— Seleccione —</option>
                            @foreach($analistas as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('analista_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Asunto <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="asunto_instruccion" maxlength="255"
                            class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm"
                            placeholder="Ej: Redactar oficio de respuesta a MINEC">
                        @error('asunto_instruccion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Detalle de la instrucción <span class="text-red-500">*</span></label>
                        <textarea wire:model="detalle_instruccion" rows="4"
                            class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm resize-none"
                            placeholder="Describa qué documento debe elaborarse y cualquier observación relevante."></textarea>
                        @error('detalle_instruccion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Tipo de documento esperado</label>
                            <select wire:model="tipo_documento_esperado" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm">
                                <option value="">— Opcional —</option>
                                <option value="Agenda al Decisor">Agenda al Decisor</option>
                                <option value="Circular">Circular</option>
                                <option value="MEMORANDO">Memorándum</option>
                                <option value="Minuta Horizontal">Minuta Horizontal</option>
                                <option value="Oficio - Tipo Carta">Oficio - Tipo Carta</option>
                                <option value="Oficio - Tipo Oficio">Oficio - Tipo Oficio</option>
                                <option value="Punto de Cuenta - Directorio">Punto de Cuenta - Directorio</option>
                                <option value="Punto de Cuenta - Presidencia IPOSTEL">Punto de Cuenta - Presidencia IPOSTEL</option>
                                <option value="Punto de Información - Presidencia IPOSTEL">Punto de Información - Presidencia IPOSTEL</option>
                                <option value="Punto-de-Cuenta-MPPT">Punto de Cuenta - MPPT</option>
                                <option value="Punto-de-Informacion-MPPT">Punto de Información - MPPT</option>
                                <option value="OTRO">Indicaciones (Redactar Texto)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Fecha límite</label>
                            <input type="date" wire:model="fecha_limite" min="{{ now()->format('Y-m-d') }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm">
                            @error('fecha_limite') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="cerrar_modal"
                            class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg shadow-sm hover:bg-blue-800 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            Emitir Instrucción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
