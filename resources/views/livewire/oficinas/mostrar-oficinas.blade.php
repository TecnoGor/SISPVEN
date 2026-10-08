@section('titulo')
    Oficinas
@endsection

<div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="w-full">
            <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Oficinas</h1>
                </div>

                <x-button class="mb-6 sm:mb-0 mt-10" wire:click="exportarOficinas">
                REPORTE
                </x-button>

                @can('Crear oficinas')
                <x-primary-button class="mb-6 sm:mb-0 mt-10" wire:click="openModal">
                        Crear Oficina
                </x-primary-button>
                @endcan
            </div>

                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 flex flex-col">
                    <!-- Filters Header -->
                    <div class="bg-gray-50 border-b border-gray-200 p-4">
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                            <div class="w-full relative md:w-1/3">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm transition duration-150 ease-in-out shadow-sm"
                                    placeholder="Buscar oficina...">
                            </div>

                            <div class="w-full md:w-2/3 flex flex-col sm:flex-row gap-3">
                                <select wire:model.live="oficina_filtro" class="w-full block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm rounded-lg shadow-sm transition duration-150 ease-in-out cursor-pointer">
                                    <option value="">Todas las oficinas</option>
                                    @foreach ($tipos_oficina as $oficina)
                                        <option value="{{$oficina->tipo_oficina_id}}">{{$oficina->nombre}}</option>
                                    @endforeach
                                </select>

                                <select wire:model.live="estado_filtro" class="w-full block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm rounded-lg shadow-sm transition duration-150 ease-in-out cursor-pointer">
                                    <option value="">Todos los Estados</option>
                                    @foreach ($estados_fil as $estado)
                                        <option value="{{$estado->estado_id}}">{{$estado->nombre}}</option>
                                    @endforeach
                                </select>

                                <select wire:model.live="municipio_filtro" class="w-full block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm rounded-lg shadow-sm transition duration-150 ease-in-out cursor-pointer">
                                    <option value="">Todos los Municipios</option>
                                    @foreach ($municipio_fil as $municipio)
                                        <option value="{{$municipio->municipio_id}}">{{$municipio->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="overflow-x-auto overflow-y-auto max-h-[55vh] lg:max-h-[60vh]">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nombre</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Código</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tipo de Oficina</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider min-w-[250px]">Dirección</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Estado</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Municipio</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Parroquia</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Estatus Físico</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($AllOficinas as $index => $oficina)
                                    <tr wire:key="{{ $oficina->oficina_id }}" class="hover:bg-[#fcf5f5] transition-colors duration-150 ease-in-out {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">{{ $oficina->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                            <span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs border border-gray-200">{{ $oficina->codigo }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $oficina->tipo_oficina ? $oficina->tipo_oficina->nombre : 'No disponible' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 text-left truncate max-w-[250px]" title="{{ $oficina->direccion }}">{{ $oficina->direccion }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $oficina->estado ? $oficina->estado->nombre : '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $oficina->municipio ? $oficina->municipio->nombre : '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $oficina->parroquia ? $oficina->parroquia->nombre : '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <!-- Select para cambiar el estatus -->
                                            <select wire:change="actualizarEstatus({{ $oficina->oficina_id }}, $event.target.value)"
                                                    class="text-xs font-semibold rounded-lg border focus:ring-red-500 focus:border-red-500 shadow-sm py-1.5 pl-3 pr-8 cursor-pointer outline-none transition-colors 
                                                    {{ $oficina->estatus_id == 1 ? 'text-green-700 bg-green-50 border-green-200 hover:bg-green-100' : ($oficina->estatus_id == 2 ? 'text-yellow-700 bg-yellow-50 border-yellow-200 hover:bg-yellow-100' : 'text-red-700 bg-red-50 border-red-200 hover:bg-red-100') }}">
                                                <option value="1" {{ $oficina->estatus_id == 1 ? 'selected' : '' }}>Activa</option>
                                                <option value="2" {{ $oficina->estatus_id == 2 ? 'selected' : '' }}>Inoperativa</option>
                                                <option value="3" {{ $oficina->estatus_id == 3 ? 'selected' : '' }}>Inactiva</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                @if ($oficina->tipo_oficina && $oficina->tipo_oficina->nombre != 'EXTERNA')
                                                <a href="{{ route('oficina-detalles',$oficina->oficina_id ) }}" title="Ver Detalles" wire:navigate.hover class="text-green-600 hover:text-green-800 transition-colors transform hover:scale-110">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </a>
                                                @endif
                                                <button wire:click="cambiar_operacion({{ $oficina->oficina_id }})"
                                                    title="{{ $oficina->operaciones ? 'Cerrar operaciones' : 'Abrir operaciones' }}"
                                                    class="transition-colors transform hover:scale-110 {{ $oficina->operaciones ? 'text-green-600 hover:text-green-800' : 'text-red-400 hover:text-red-600' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                                                    </svg>
                                                </button>
                                                <button wire:click="modificar({{$oficina->oficina_id}})" title="Modificar Información" class="text-blue-600 hover:text-blue-800 transition-colors transform hover:scale-110">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                                </svg>
                                                <span class="text-lg font-medium text-gray-700">No se encontraron oficinas</span>
                                                <p class="text-gray-400 mt-1">Intenta ajustando los filtros o el término de búsqueda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination & Footer -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center w-full md:w-auto">
                            <label for="paginacion" class="mr-3 text-sm font-medium text-gray-700 focus:outline-none mb-0">Mostrar:</label>
                            <select wire:model.live="perPage" id="paginacion" class="border border-gray-300 rounded-lg shadow-sm focus:ring-[#6b1820] focus:border-[#6b1820] sm:text-sm py-1.5 cursor-pointer outline-none">
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="150">150</option>
                                <option value="200">200</option>
                            </select>
                            <span class="ml-3 text-sm text-gray-500">registros</span>
                        </div>
                        <div class="w-full md:w-auto overflow-x-auto">
                            {{ $AllOficinas->links() }}
                        </div>
                    </div>
                </div>
        </div>


    <!-- Modal -->
    @if($modalOpen)
        @livewire('oficinas.crear-oficinas')
    @endif

    @if($npc)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all">
                
                {{-- HEADER DEL MODAL --}}
                <div class="bg-gray-50 px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 p-2.5 rounded-xl text-blue-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 tracking-tight">Punto de Cuenta</h3>
                    </div>
                    <button wire:click="cerrar_npc" type="button" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg p-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- CUERPO DEL MODAL --}}
                <div class="p-6 md:p-8 space-y-6">
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Para inhabilitar la oficina, es obligatorio adjuntar y detallar el documento de <span class="font-bold text-gray-700">Punto de Cuenta</span> autorizatorio.
                    </p>

                    {{-- CARGA DE PDF --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Adjuntar Documento <span class="text-red-500">*</span></label>
                        <label for="pdf_npc" class="group flex flex-col items-center justify-center w-full h-32 border-2 border-dashed {{ $pdf_npc ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-500 hover:bg-gray-50' }} rounded-xl cursor-pointer transition-all duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                @if($pdf_npc)
                                    <svg class="w-10 h-10 text-blue-500 mb-3 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-sm font-bold text-blue-700 px-4 text-center break-all">{{ $pdf_npc->getClientOriginalName() }}</p>
                                    <p class="text-xs text-blue-500 mt-1 font-medium">Haz clic para reemplazar archivo</p>
                                @else
                                    <svg class="w-10 h-10 text-gray-400 group-hover:text-blue-500 mb-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-bold text-blue-600">Haz clic para explorar</span> o arrastra el PDF</p>
                                    <p class="text-xs text-gray-400 font-semibold">Solo formato PDF</p>
                                @endif
                            </div>
                            <input type="file" id="pdf_npc" wire:model.live="pdf_npc" accept="application/pdf" class="hidden" />
                        </label>
                        @error('pdf_npc') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- NUMERO DE PUNTO --}}
                    <div>
                        <label for="numero_punto_cuenta" class="block text-sm font-bold text-gray-700 mb-2">Número Autorizatorio <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="numero_punto_cuenta" id="numero_punto_cuenta" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-4 py-2.5 outline-none transition-all placeholder-gray-400" placeholder="Ej. PC-2026-001">
                        @error('numero_punto_cuenta') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- MOTIVO --}}
                    <div>
                        <label for="motivo_npc" class="block text-sm font-bold text-gray-700 mb-2">Motivo / Justificación <span class="text-red-500">*</span></label>
                        <textarea wire:model="motivo_npc" id="motivo_npc" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-4 py-3 min-h-[100px] resize-none outline-none transition-all placeholder-gray-400" placeholder="Describe brevemente el por qué de la inhabilitación..."></textarea>
                        @error('motivo_npc') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- FOOTER CON ACCIONES --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-200">
                    <button wire:click="cerrar_npc" type="button" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all font-bold text-sm w-full sm:w-auto text-center shadow-sm" wire:loading.attr="disabled">
                        Cancelar
                    </button>
                    <button wire:click="ingresar_npc" type="button" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all font-bold text-sm flex items-center justify-center w-full sm:w-auto shadow-md" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="ingresar_npc">Confirmar e Inhabilitar</span>
                        <span wire:loading.flex wire:target="ingresar_npc" class="items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    @if($mod)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-1/2">
                <button wire:click="$set('mod', false)" class="float-right text-red-500">✖</button>

                @livewire('modificar-informacion-oficina.modificar-informacion-oficina', ['id' => $this->modificar_oficina_id])
            </div>
        </div>
    @endif

        <style>
            tr {
            page-break-inside: avoid;
            }
        </style>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('mostrarAlerta', oficina_id => {
                Swal.fire({
                    title: "Eliminar Oficina?",
                    text: `Una Oficina eliminada no se puede recuperar oficina_id: ${oficina_id}!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4f46e5",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, eliminar!",
                    cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // eliminar el rol
                    Livewire.dispatch('eliminar', {oficina: oficina_id});
                }
            });
        });
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


