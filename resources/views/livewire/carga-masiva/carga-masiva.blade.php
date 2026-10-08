<div>
    @section('titulo')
        Carga Masiva
    @endsection
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-[#6b1820] font-bold uppercase tracking-tight">Carga Masiva de Envíos</h1>
        <p class="mt-1 text-sm text-gray-600 font-medium">Importación de envíos mediante archivo Excel para procesamiento por lote.</p>
    </div>

    {{-- ALERTAS DE ÉXITO O ERROR GENERAL --}}
    @if($mensaje)
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-xl shadow-sm border-l-4 {{ $fallidas > 0 ? 'bg-red-100 border-red-500 text-red-700' : 'bg-green-100 border-green-500 text-green-700' }}">
                <p class="font-bold">{{ $fallidas > 0 ? 'Algo no salió como esperábamos' : '¡Excelente!' }}</p>
                <p>{{ $mensaje }}.</p>
                @if($fallidas > 0 && !empty($errores))
                    <p class="text-sm mt-1 font-medium">Hemos encontrado {{ $fallidas }} filas con detalles que necesitan tu atención. Puedes ver los detalles al final de esta página.</p>
                @endif
            </div>
        </div>
    @endif

    <br>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 p-8" 
            x-data="{ isUploading: false, progress: 0 }" 
            x-on:livewire-upload-start="isUploading = true" 
            x-on:livewire-upload-finish="isUploading = false" 
            x-on:livewire-upload-error="isUploading = false" 
            x-on:livewire-upload-progress="progress = $event.detail.progress">
        
            <div class="w-full mx-auto space-y-6">
                <label class="block text-[11px] font-black text-gray-500 uppercase tracking-[0.2em]">
                    Seleccionar Archivo Excel (.xlsx, .xls)
                </label>
                <div class="relative group w-full">
                    <input type="file" wire:model="archivo" id="archivo" accept=".xlsx, .xls"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    
                    <div :class="isUploading ? 'border-[#6b1820] bg-white' : 'border-gray-300 bg-gray-50'" 
                        class="border-2 border-dashed rounded-2xl p-16 flex flex-col items-center justify-center group-hover:border-[#6b1820] transition-all duration-150 shadow-sm">
                        
                        <div :class="isUploading ? 'text-[#6b1820] animate-bounce' : 'text-gray-400'" class="mb-4 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        
                        <p class="text-xl text-gray-600 font-medium text-center">
                            <span x-show="!isUploading">Arrastre el archivo aquí o <span class="text-[#6b1820] font-bold underline">Búsquelo</span></span>
                            <span x-show="isUploading" class="text-[#6b1820] font-bold tracking-tight">Cargando archivo al sistema...</span>
                        </p>
                        <p class="mt-2 text-xs text-gray-400 uppercase tracking-widest font-semibold">Procesamiento por lote inmediato</p>
                    </div>
                </div>

                {{-- MENSAJE DE ERROR DE VALIDACIÓN --}}
                @error('archivo') 
                    <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider italic mt-2">
                        Por favor, asegúrese de seleccionar un archivo válido en formato Excel (.xlsx o .xls).
                    </p> 
                @enderror

                <div x-show="isUploading" x-transition class="w-full mt-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-[10px] font-black text-[#6b1820] uppercase tracking-widest">Estado de subida</span>
                        <span class="text-[10px] font-black text-[#6b1820]" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden shadow-inner">
                        <div class="bg-[#6b1820] h-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                {{-- ARCHIVO SELECCIONADO --}}
                @if($archivo && !$errores)
                    <div class="mt-6 flex items-center bg-green-50 border border-green-100 p-4 rounded-lg shadow-sm" x-show="!isUploading">
                        <div class="bg-green-600 text-white px-2 py-1 rounded text-[10px] font-black mr-4">LISTO</div>
                        <div class="flex-1">
                            <span class="text-xs font-bold text-green-800 truncate block">{{ $archivo->getClientOriginalName() }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('archivo').value = ''" wire:click="$set('archivo', null)" class="text-green-800 hover:text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- BOTÓN DE PROCESAR --}}
                <div class="mt-8 flex justify-center">
                    <button wire:click="importar"
                            wire:loading.attr="disabled"
                            :disabled="isUploading"
                            class="min-w-[250px] bg-[#6b1820] hover:bg-[#7b1f27] active:bg-[#521218] text-white font-bold py-3.5 px-8 rounded-lg shadow-lg inline-flex items-center justify-center uppercase text-sm tracking-widest transition ease-in-out duration-150 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-[#6b1820] focus:ring-offset-2">
                        
                        <span wire:loading.remove wire:target="importar">Procesar Carga</span>
                        
                        <span wire:loading wire:target="importar">
                            Importando datos...
                        </span>
                    </button>
                </div>
            </div>

            {{-- REPORTE DE INCONSISTENCIAS --}}
            @if(!empty($errores))
                <div class="mt-12 border-t border-gray-100 pt-8" x-show="!isUploading">
                    <h3 class="text-xs font-black text-red-700 uppercase tracking-[0.2em] mb-4 flex items-center">
                        <span class="bg-red-700 text-white p-1 rounded mr-2">!</span>
                        Detalles detectados en el archivo que requieren su revisión
                    </h3>
                    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest w-24">Nro. Fila</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Sugerencia de corrección</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($errores as $fila => $mensajes)
                                    <tr class="hover:bg-red-50/50 transition duration-75">
                                        <td class="px-6 py-4 text-xs font-bold text-center text-red-700 bg-red-50/30">{{ $fila }}</td>
                                        <td class="px-6 py-4 text-xs text-gray-600 font-medium italic">
                                            {{ implode(', ', $mensajes) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>