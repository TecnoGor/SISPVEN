@section('titulo')
    Semaforo Postal
@endsection

{{-- Contenedor Maestro --}}
<div x-data="{ vista: 'principal', regionSeleccionada: null, estadoSeleccionado: null }">
    
    <div x-show="vista === 'principal'" x-transition:enter.duration.300ms>
        <div class="max-w-[95%] mx-auto font-sans text-primary font-bold">

            <div class="max-w-[95%] mx-auto flex justify-between items-end mb-8 border-b-2 border-gray-300 pb-4">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-black italic tracking-tighter leading-none">Semaforo Postal</h1>
            </div>
            {{------------------------------------------------------}}
            {{------------ VISTA DETALLE DE REGIONES ---------------}}
            {{------------------------------------------------------}}
            <div class="max-w-[95%] mx-auto space-y-4">

                @php
                    $coleccion = collect($grupos ?? []);
                    $total_operativas_global = $coleccion->sum('operativas');
                    $total_inoperativas_global = $coleccion->sum('inoperativas');
                    $total_oficinas_global = $coleccion->sum('total_oficinas');
                    $total_propias_global = $coleccion->sum('propias');
                    $total_arrendadas_global = $coleccion->sum('arrendadas');
                    $total_comodato_global = $coleccion->sum('comodato');
                    $total_sin_espacio_global = $coleccion->sum(fn($g) => data_get($g, 'sin_espacio', 0));
                    $flota_activa = $coleccion->sum('vehiculos_activo');
                    $flota_inoperativa = $coleccion->sum('vehiculos_inoperativos');
                    $flota_desincorporada = 0;
                    $flota_total = $flota_activa + $flota_inoperativa + $flota_desincorporada;
                    $grupos_ordenados = $coleccion->sortBy(fn ($g) => strtolower(data_get($g, 'region', '')))->values();
                    $gran_total = $total_oficinas_global + $total_sin_espacio_global;
                @endphp

                @forelse($grupos_ordenados as $grupo)
                    @php
                        $region_nombre = data_get($grupo, 'region', '—');
                        $operativas = intval(data_get($grupo, 'operativas', 0));
                        $inoperativas = intval(data_get($grupo, 'inoperativas', 0));
                        $propias = intval(data_get($grupo, 'propias', 0));
                        $arrendadas = intval(data_get($grupo, 'arrendadas', 0));
                        $comodato = intval(data_get($grupo, 'comodato', 0));
                        $sin_espacio = intval(data_get($grupo, 'sin_espacio', 0));
                        $total_oficinas = intval(data_get($grupo, 'total_oficinas', 0));
                        $opt = $operativas + $inoperativas + $sin_espacio;
                    @endphp

                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-3 flex flex-wrap xl:flex-nowrap items-center gap-3 hover:shadow-lg transition-shadow">
                        <button type="button"
                                @click="vista = 'detalle'; regionSeleccionada = '{{ $region_nombre }}'"
                                class="flex items-center gap-3 w-full xl:w-[220px] shrink-0 group transition-all duration-200 hover:scale-105 focus:outline-none">
                            <div class="w-12 h-12 bg-white rounded-full border-2 border-cyan-500 flex items-center justify-center shadow-sm group-hover:border-cyan-400">
                                <span class="text-xl">🌐</span>
                            </div>
                            <div class="bg-[#ccff00] px-4 py-2 skew-x-[-15deg] border-l-[6px] border-cyan-500 shadow-sm flex-1 text-left">
                                <span class="block skew-x-[15deg] font-black italic text-[13px] leading-none uppercase text-[#0e3b43]">
                                    Región<br>{{ $region_nombre }}
                                </span>
                            </div>
                        </button>

                        <div class="flex flex-wrap lg:flex-nowrap flex-1 items-center justify-around gap-3 px-2">
                            <div class="bg-gradient-to-b from-[#1a234e] to-[#0d1333] rounded-xl flex divide-x divide-white/10 shadow-lg border-b-[4px] border-cyan-500 shrink-0">
                                <div class="px-4 md:px-6 py-2 text-center">
                                    <div class="text-[#4ade80] text-3xl md:text-4xl font-black leading-none">{{ str_pad($operativas, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-white text-[10px] font-bold uppercase tracking-wider">Operativas</div>
                                </div>
                                <div class="px-4 md:px-6 py-2 text-center">
                                    <div class="text-[#f15a24] text-3xl md:text-4xl font-black leading-none">{{ str_pad($inoperativas, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-white text-[10px] font-bold uppercase tracking-wider">Inoperativas</div>
                                </div>
                            </div>

                            <div class="text-[11px] font-bold text-gray-500 uppercase leading-snug w-[120px] md:w-[140px]">
                                <div class="flex justify-between border-b border-gray-100 pb-1"><span>Propias</span> <span class="text-cyan-600 font-black">{{ str_pad($propias, 2, '0', STR_PAD_LEFT) }}</span></div>
                                <div class="flex justify-between border-b border-gray-100 py-1"><span>Arrendadas</span> <span class="text-cyan-600 font-black">{{ str_pad($arrendadas, 2, '0', STR_PAD_LEFT) }}</span></div>
                                <div class="flex justify-between pt-1"><span>Comodatos</span> <span class="text-cyan-600 font-black">{{ str_pad($comodato, 2, '0', STR_PAD_LEFT) }}</span></div>
                            </div>

                            <div class="flex items-center gap-6 md:gap-10">
                                <div class="text-center">
                                    <div class="text-[#1a234e] text-3xl md:text-4xl font-black leading-none tracking-tighter">{{ $total_oficinas }}</div>
                                    <div class="text-[10px] font-black uppercase text-gray-400 leading-tight">Operativas e<br>Inoperativas</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[#f15a24] text-3xl md:text-4xl font-black leading-none">{{ $sin_espacio }}</div>
                                    <div class="text-[10px] font-black uppercase text-[#f15a24] leading-tight">Sin Espacio<br>Físico</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pl-4 border-l-2 border-gray-100 shrink-0 w-full xl:w-auto justify-end xl:justify-start">
                            <div class="bg-[#3d3d3d] p-3 rounded-full text-white shadow-md">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="flex items-center gap-3 text-right">
                                <span class="text-[#00b29a] text-5xl md:text-[64px] font-black leading-none italic tracking-tighter">{{ $opt }}</span>
                                <span class="text-[11px] font-black text-[#1a3a4a] leading-tight uppercase">Oficinas Postales<br>Telegráficas</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 text-center">
                        <p class="text-gray-600 font-bold">No hay regiones registradas.</p>
                    </div>
                @endforelse
            </div>
            {{------------ FOOTER DETALLE DE REGIONES----------------}}
            <div class="max-w-[95%] mx-auto mt-12 space-y-6">
                <div class="relative mt-16">
                    <div class="absolute -top-4 left-10 bg-slate-800 text-white text-[10px] font-black uppercase tracking-[0.3em] px-6 py-1 rounded-full shadow-lg z-10 border border-slate-600">
                        Consolidado Nacional de Infraestructura
                    </div>

                    <div class="bg-gradient-to-r from-white via-gray-50 to-gray-100 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border-2 border-white p-4 md:p-6 flex items-center justify-between gap-3 overflow-visible">

                        {{-- Etiqueta "Total Nivel Nacional" --}}
                        <div class="flex items-center gap-6 shrink-0">
                            <div class="bg-[#ccff00] px-4 py-3 skew-x-[-12deg] border-l-[10px] border-slate-900 shadow-sm">
                                <h2 class="block skew-x-[12deg] font-black italic text-sm leading-none uppercase text-slate-900 whitespace-nowrap">
                                    Total Nivel<br><span class="text-xl">Nacional</span>
                                </h2>
                            </div>
                        </div>

                        {{-- Panel operativas / inoperativas --}}
                        <div class="bg-slate-900 rounded-[2rem] p-2 flex shadow-2xl shrink-0">
                            <div class="px-5 lg:px-10 py-3 text-center relative group cursor-help">
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-48 bg-slate-800 border border-slate-600 rounded-2xl p-4 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                    <div class="text-[10px] text-[#00ffcc] font-black uppercase tracking-widest mb-2 border-b border-white/10 pb-1">Desglose Operativas</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>PROPIAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>ARRENDADAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>COMODATOS:</span><span class="text-white">00</span></div>
                                    </div>
                                    <div class="absolute bottom-[-6px] left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 border-r border-b border-slate-600 rotate-45"></div>
                                </div>
                                <div class="text-[#00ffcc] text-4xl lg:text-6xl font-black leading-none tracking-tighter">{{ str_pad($total_operativas_global, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-white/50 text-[10px] font-bold uppercase tracking-widest mt-1">Operativas</div>
                            </div>

                            <div class="w-px bg-white/10 my-4"></div>

                            <div class="px-5 lg:px-10 py-3 text-center relative group cursor-help">
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-48 bg-slate-800 border border-slate-600 rounded-2xl p-4 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                    <div class="text-[10px] text-[#ff5500] font-black uppercase tracking-widest mb-2 border-b border-white/10 pb-1">Desglose Inoperativas</div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>PROPIAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>ARRENDADAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>COMODATOS:</span><span class="text-white">00</span></div>
                                    </div>
                                    <div class="absolute bottom-[-6px] left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 border-r border-b border-slate-600 rotate-45"></div>
                                </div>
                                <div class="text-[#ff5500] text-4xl lg:text-6xl font-black leading-none tracking-tighter">{{ str_pad($total_inoperativas_global, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-white/50 text-[10px] font-bold uppercase tracking-widest mt-1">Inoperativas</div>
                            </div>
                        </div>

                        {{-- Propias / Arrendadas / Comodatos --}}
                        <div class="space-y-1 shrink-0 min-w-[120px]">
                            <div class="flex justify-between items-center text-xs font-black uppercase text-slate-400">
                                <span>Propias</span>
                                <span class="text-slate-800 text-base font-black ml-3">{{ str_pad($total_propias_global, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-black uppercase text-slate-400 border-y border-gray-200 py-1">
                                <span>Arrendadas</span>
                                <span class="text-slate-800 text-base font-black ml-3">{{ str_pad($total_arrendadas_global, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-black uppercase text-slate-400">
                                <span>Comodatos</span>
                                <span class="text-slate-800 text-base font-black ml-3">{{ str_pad($total_comodato_global, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        {{-- Operativas e Inoperativas + Sin Espacio --}}
                        <div class="flex items-center gap-5 border-l-2 border-gray-200 pl-4 shrink-0">
                            <div class="text-center">
                                <div class="text-slate-800 text-3xl lg:text-5xl font-black leading-none tracking-tighter">{{ $total_oficinas_global }}</div>
                                <div class="text-[10px] font-black uppercase text-slate-400 mt-1 leading-tight">Operativas e<br>Inoperativas</div>
                            </div>

                            <div class="text-center relative group cursor-help">
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-48 bg-slate-800 border border-slate-600 rounded-2xl p-4 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                    <div class="text-[10px] text-[#f15a24] font-black uppercase tracking-widest mb-2 border-b border-white/10 pb-1">Desglose Sin Espacio</div>
                                    <div class="space-y-1 text-left">
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>PROPIAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>ARRENDADAS:</span><span class="text-white">00</span></div>
                                        <div class="flex justify-between text-white/70 text-[10px] font-bold"><span>COMODATOS:</span><span class="text-white">00</span></div>
                                    </div>
                                    <div class="absolute bottom-[-6px] left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-800 border-r border-b border-slate-600 rotate-45"></div>
                                </div>
                                <div class="text-[#f15a24] text-3xl lg:text-5xl font-black leading-none tracking-tighter">{{ $total_sin_espacio_global }}</div>
                                <div class="text-[10px] font-black uppercase text-[#f15a24] mt-1 leading-tight">Sin Espacio<br>Físico</div>
                            </div>
                        </div>

                        {{-- Gran total --}}
                        <div class="flex items-center gap-3 pl-4 border-l-2 border-gray-200 shrink-0">
                            <span class="text-slate-900 text-[60px] lg:text-[80px] font-black italic leading-none tracking-tighter">{{ $gran_total }}</span>
                            <div class="flex flex-col">
                                <div class="bg-cyan-600 p-2 rounded-xl text-white shadow-lg mb-1 self-start">
                                    <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                </div>
                                <div class="text-xs font-black text-slate-500 uppercase leading-none">Total<br>Oficinas</div>
                            </div>
                        </div>

                    </div>
                </div>
                {{------------ FLOTA DE VEHICULOS ----------------}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 flex flex-wrap xl:flex-nowrap items-center justify-between gap-3 group hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4 w-full xl:w-[220px] shrink-0">
                        <div class="w-12 h-12 bg-white rounded-full border-2 border-slate-700 flex items-center justify-center shadow-sm">
                            <span class="text-xl">🚚</span>
                        </div>
                        <div class="bg-[#ccff00] px-4 py-2 skew-x-[-15deg] border-l-[6px] border-slate-700 shadow-sm flex-1">
                            <span class="block skew-x-[15deg] font-black italic text-[13px] leading-none uppercase text-[#0e3b43]">
                                Flota<br>Vehicular
                            </span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-b from-[#1a234e] to-[#0d1333] rounded-xl flex divide-x divide-white/10 shadow-lg border-b-[4px] border-slate-500 flex-1 mx-0 md:mx-6 lg:mx-10">
                        <div class="flex-1 py-4 text-center">
                            <div class="text-[#4ade80] text-3xl md:text-5xl font-black leading-none">
                                {{ $flota_activa ?? 0 }}
                            </div>
                            <div class="text-white text-xs font-bold uppercase tracking-wider mt-1">Activos</div>
                        </div>
                        <div class="flex-1 py-4 text-center">
                            <div class="text-[#f9a825] text-3xl md:text-5xl font-black leading-none">
                                {{ $flota_inoperativa ?? 0 }}
                            </div>
                            <div class="text-white text-xs font-bold uppercase tracking-wider mt-1">Inoperativos</div>
                        </div>
                        <div class="flex-1 py-4 text-center">
                            <div class="text-[#f15a24] text-3xl md:text-5xl font-black leading-none">
                                {{ $flota_desincorporada ?? 0 }}
                            </div>
                            <div class="text-white text-xs font-bold uppercase tracking-wider mt-1">Por Desinc.</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pl-4 md:pl-6 border-l-2 border-gray-100 shrink-0 w-full xl:w-auto justify-end xl:justify-start">
                        <div class="bg-[#3d3d3d] p-3 rounded-full text-white shadow-md">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-800 text-[56px] md:text-[80px] font-black leading-none italic tracking-tighter">
                                {{ $flota_total ?? 0 }}
                            </span>
                            <span class="text-xs font-black text-[#1a3a4a] leading-tight uppercase text-right">
                                Total Flota<br>Vehicular Global
                            </span>
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
    {{------------------------------------------------------}}
    {{------------ VISTA DETALLE DE ESTADOS ----------------}}
    {{------------------------------------------------------}}
    <div x-show="vista === 'detalle'" x-cloak x-transition:enter.duration.300ms>
    
        <div class="max-w-[95%] mx-auto flex flex-wrap justify-between items-end gap-3 mb-8 border-b-2 border-gray-300 pb-4 mt-8">

            <h1 class="text-3xl md:text-5xl lg:text-6xl font-black italic tracking-tighter leading-none uppercase text-primary font-bold">
                Región <span x-text="regionSeleccionada"></span>
            </h1>

            <button @click="vista = 'principal'"
                    class="bg-[#1a234e] text-white px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all flex items-center gap-2 mb-1 shadow-lg">
                <span>←</span> Regresar
            </button>
        </div>

        <div class="max-w-[95%] mx-auto font-sans text-[#1a234e]">
            @foreach($detalles as $region_nombre => $info)
                <div x-show="regionSeleccionada === '{{ $region_nombre }}'" class="max-w-[98%] mx-auto bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100 mb-10">
                    <div class="p-6 md:p-10 space-y-8">
                        @foreach($info['estados'] as $estado_nombre => $datos_estado)
                        <button type="button"
                                    wire:key="btn-estado-{{ $datos_estado['estado_id'] }}"
                                    wire:click="seleccionar_estado({{ $datos_estado['estado_id'] }})"
                                    @click="vista = 'oficinas'; estadoSeleccionado = '{{ $estado_nombre }}'"
                                    class="group w-full flex flex-wrap lg:flex-nowrap items-center text-left p-4 md:p-6 rounded-2xl border border-gray-100 transition-all hover:bg-gray-50 hover:shadow-md active:scale-[0.99] cursor-pointer gap-3">

                            <div class="w-full lg:w-56 shrink-0 transform transition-transform duration-300 group-hover:translate-x-2">
                                <h2 class="text-[#f9a825] text-2xl md:text-4xl font-black uppercase leading-none italic">{{ $estado_nombre }}</h2>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-4xl md:text-6xl font-black italic">{{ $datos_estado['total_operativas'] + $datos_estado['total_inoperativas'] }}</span>
                                    <span class="text-xl md:text-2xl font-black italic">OPT</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap sm:flex-nowrap flex-1 justify-around items-center gap-3">
                                <div class="flex gap-6 md:gap-10 lg:gap-16 italic">
                                    <div class="text-center"><div class="text-2xl md:text-4xl font-black text-[#1a234e]">{{ str_pad($datos_estado['propias']['operativas'] + $datos_estado['propias']['inoperativas'], 2, '0', STR_PAD_LEFT) }}</div><div class="text-xs font-bold uppercase">Propias</div></div>
                                    <div class="text-center"><div class="text-2xl md:text-4xl font-black text-[#1a234e]">{{ str_pad($datos_estado['arrendadas']['operativas'] + $datos_estado['arrendadas']['inoperativas'], 2, '0', STR_PAD_LEFT) }}</div><div class="text-xs font-bold uppercase">Arrendadas</div></div>
                                    <div class="text-center"><div class="text-2xl md:text-4xl font-black text-[#1a234e]">{{ str_pad($datos_estado['comodato']['operativas'] + $datos_estado['comodato']['inoperativas'], 2, '0', STR_PAD_LEFT) }}</div><div class="text-xs font-bold uppercase">Comodatos</div></div>
                                </div>
                                <div class="hidden sm:block h-16 md:h-20 w-[4px] bg-[#1a234e] rounded-full mx-4 md:mx-8"></div>
                                <div class="flex gap-8 md:gap-14 lg:gap-20">
                                    <div class="text-center text-[#00b29a]"><div class="text-4xl md:text-6xl font-black">{{ str_pad($datos_estado['total_operativas'], 2, '0', STR_PAD_LEFT) }}</div><div class="text-xs font-bold uppercase">Operativas</div></div>
                                    <div class="text-center text-[#f15a24]"><div class="text-4xl md:text-6xl font-black">{{ str_pad($datos_estado['total_inoperativas'], 2, '0', STR_PAD_LEFT) }}</div><div class="text-xs font-bold uppercase">Inoperativas</div></div>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    {{------------ FOOTER DETALLE DE ESTADOS----------------}}
                    @php
                        $reg_op = collect($info['estados'])->sum('total_operativas');
                        $reg_inop = collect($info['estados'])->sum('total_inoperativas');
                        $reg_pro = collect($info['estados'])->sum(fn($e) => $e['propias']['operativas'] + $e['propias']['inoperativas']);
                        $reg_arr = collect($info['estados'])->sum(fn($e) => $e['arrendadas']['operativas'] + $e['arrendadas']['inoperativas']);
                        $reg_com = collect($info['estados'])->sum(fn($e) => $e['comodato']['operativas'] + $e['comodato']['inoperativas']);
                        $reg_veh_activos = collect($info['estados'])->sum('vehiculos_activo');
                        $reg_veh_inoperativos = collect($info['estados'])->sum('vehiculos_inoperativos');
                    @endphp
                    <div class="bg-[#1a234e] p-4 md:p-6 flex flex-wrap md:flex-nowrap justify-between items-center px-4 md:px-8 gap-4 shadow-2xl">
                        <div class="flex items-center gap-4">
                            <div class="text-right border-r border-white/20 pr-4">
                                <div class="text-base text-white font-black uppercase italic leading-none tracking-tighter">Estatus</div>
                                <div class="text-base text-white font-black uppercase italic tracking-tighter">Operativo</div>
                            </div>
                            <div class="flex gap-6">
                                <div class="text-center">
                                    <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Operativas</div>
                                    <div class="text-5xl font-black text-[#00e676] leading-none">{{ $reg_op }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Inoperativas</div>
                                    <div class="text-5xl font-black text-[#f15a24] leading-none">{{ $reg_inop }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Sin Espacio</div>
                                    <div class="text-5xl font-black text-[#f15a24] leading-none">00</div>
                                </div>
                            </div>
                        </div>

                        <div class="h-12 w-[1px] bg-white/20"></div>

                        <div class="flex items-center gap-4">
                            <div class="text-right border-r border-white/20 pr-4">
                                <div class="text-base text-white font-black uppercase italic leading-none tracking-tighter">Estatus</div>
                                <div class="text-base text-white font-black uppercase italic tracking-tighter">Legal</div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Propias</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        {{ $reg_pro }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Arrendadas</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        {{ $reg_arr }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Comodatos</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        {{ $reg_com }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="h-12 w-[1px] bg-white/20"></div>

                        <div class="flex items-center gap-4">
                            <div class="text-right border-r border-white/20 pr-4">
                                <div class="text-base text-white font-black uppercase italic leading-none tracking-tighter">Vehículos</div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Operativos</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        {{ $reg_veh_activos }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Inoperativos</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        {{ $reg_veh_inoperativos }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Por Desinc.</span>
                                    <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center text-xl font-black text-white">
                                        00
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {{------------------------------------------------------}} 
    {{--------------VISTA DETALLE DE OFICINAS---------------}} 
    {{------------------------------------------------------}}
    <div x-show="vista === 'oficinas'" x-cloak x-transition:enter.duration.300ms>
        <div class="max-w-[95%] mx-auto font-sans text-[#1a234e]">
            
            <div class="max-w-[95%] mx-auto flex flex-wrap justify-between items-end gap-3 mb-8 border-b-2 border-gray-300 pb-4 mt-8">
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-black italic tracking-tighter leading-none uppercase text-primary">
                        <span x-text="estadoSeleccionado"></span>
                        <span class="text-[#f9a825] text-xl md:text-3xl ml-2 md:ml-4 font-bold">{{ count($oficinas_det) }} OPT</span>
                    </h1>
                </div>

                <button @click="vista = 'detalle'"
                        class="bg-[#1a234e] text-white px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all flex items-center gap-2 mb-1 shadow-lg">
                    <span>←</span> Regresar
                </button>
            </div>

            <div class="max-w-[98%] mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-200 mb-10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1a234e] text-white text-[10px] uppercase tracking-wider italic">
                                <th class="px-4 py-5 border-r border-white/10 text-center">Oficinas Postales Telegráficas</th>
                                <th class="px-4 py-5 border-r border-white/10 text-center">Infraestructura</th>
                                <th class="px-4 py-5 border-r border-white/10 text-center">Recursos Humanos</th>
                                <th class="px-4 py-5 border-r border-white/10 text-center">Estatus Legal</th>
                                <th class="px-4 py-5 border-r border-white/10 text-center">Conectividad</th>
                                <th class="px-4 py-5 text-center">Vehículos</th>
                            </tr>
                        </thead>
                        <tbody class="text-[11px] font-bold uppercase italic">
                            @forelse($oficinas_det as $oficina)
                                <tr wire:key="ofi-{{ $oficina['oficina_id'] }}" class="border-b border-gray-100 hover:bg-blue-50/50 transition-colors">
                                    <td class="px-4 py-3 border-r border-gray-100 bg-gray-50/50">
                                        <div class="flex items-center gap-2 {{ $oficina['operativa'] ? 'text-[#00b29a]' : 'text-[#f15a24]' }}">
                                            <span class="w-2 h-2 {{ $oficina['operativa'] ? 'bg-[#00b29a]' : 'bg-[#f15a24]' }} rounded-full"></span>
                                            {{ $oficina['oficina'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-r border-gray-100 text-center text-gray-400">No Disponible</td>
                                    <td class="px-4 py-3 border-r border-gray-100 text-center text-gray-400">No Disponible</td>
                                    <td class="px-4 py-3 border-r border-gray-100 text-center">
                                        @if($oficina['estatus_legal'] === 'Propia Ipostel')
                                            <span class="text-[#00b29a]">{{ $oficina['estatus_legal'] }}</span>
                                        @else
                                            <span class="text-[#f15a24]">{{ $oficina['estatus_legal'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-r border-gray-100 text-center text-gray-400">No Disponible</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($oficina['tiene_vehiculos'])
                                            <span class="text-[#00b29a]">TIENE VEHÍCULO</span>
                                        @else
                                            <span class="text-[#f15a24]">SIN VEHÍCULOS</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-gray-400 italic">
                                        No se encontraron oficinas para este estado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{------------ FOOTER DETALLE DE OFICINAS----------------}}
                
                <div class="bg-[#1a234e] p-4 md:p-6 flex flex-wrap md:flex-nowrap justify-between items-center px-4 md:px-6 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="text-right border-r border-white/20 pr-4">
                            <div class="text-base text-white font-black uppercase italic leading-none">Estatus</div>
                            <div class="text-base text-white font-black uppercase italic">Operativo</div>
                        </div>
                        <div class="flex gap-6">
                            <div class="text-center">
                                <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Operativas</div>
                                <div class="text-4xl font-black text-[#00e676]">
                                    {{ str_pad($oficinas_det->where('operativa', true)->count(), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Inoperativas</div>
                                <div class="text-4xl font-black text-[#f15a24]">
                                    {{ str_pad($oficinas_det->where('operativa', false)->count(), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-[9px] text-gray-400 font-bold uppercase mb-1 tracking-wider">Sin Espacio</div>
                                <div class="text-4xl font-black text-[#f15a24]">00</div>
                            </div>
                        </div>
                    </div>

                    <div class="h-12 w-[1px] bg-white/20"></div>

                    <div class="flex items-center gap-4">
                        <div class="text-right border-r border-white/20 pr-4">
                            <div class="text-base text-white font-black uppercase italic leading-none">Estatus</div>
                            <div class="text-base text-white font-black uppercase italic">Legal</div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Propias</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    {{ str_pad($oficinas_det->where('estatus_legal', 'Propia Ipostel')->count(), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Comodatos</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    {{ str_pad($oficinas_det->where('estatus_legal', 'En Comodato')->count(), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Arrendadas</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    {{ str_pad($oficinas_det->where('estatus_legal', 'Arrendada')->count(), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="h-12 w-[1px] bg-white/20"></div>

                    <div class="flex items-center gap-4">
                        <div class="text-right border-r border-white/20 pr-4">
                            <div class="text-base text-white font-black uppercase italic leading-none">Vehículos</div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Operativos</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    {{ str_pad($oficinas_det->sum(fn($o) => $o['vehiculos']['operativos']), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Inoperativos</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    {{ str_pad($oficinas_det->sum(fn($o) => $o['vehiculos']['inoperativos']), 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[9px] text-gray-400 font-bold uppercase mb-1">Por Desinc.</span>
                                <div class="w-11 h-11 rounded-full border-2 border-white flex items-center justify-center text-lg font-black text-white">
                                    00
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    </script>
    @endscript
@endpush