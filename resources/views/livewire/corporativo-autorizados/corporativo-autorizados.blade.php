<div>
    @section('titulo') Personal Autorizado @endsection
    {{-- ENCABEZADO --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Personal Autorizado</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión y detalle del personal autorizado.</p>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/number.css') }}">
    @endpush

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200 p-4 mt-6">

            <div class="mt-2 mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <input type="text" wire:model.live.debounce.300ms="search"
                                    class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                    placeholder="Buscar...">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 justify-start md:justify-end w-full md:w-auto">
                        <x-primary-button class="min-w-[160px] px-4 py-2 text-sm rounded-md shadow-sm" wire:click="modalOpen">
                            Agregar Autorizado
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-4 items-center justify-start">
                
                <div class="flex flex-col md:flex-row gap-4 w-full items-start md:items-center">
                    
                    <div class="w-full md:w-1/4">
                        <label for="clientes_filtro" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Cliente</label>
                        <select id="clientes_filtro" wire:model.live="cliente"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-3">
                            <option value="">Seleccionar Cliente</option>
                            @foreach ($clientes as $clie)
                                <option value="{{ $clie->cliente_corporativo_id }}">{{ $clie->razon_social }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col h-full pt-4 md:pt-0"> 
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estatus:</label>
                        
                        <div class="flex items-center">
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <span class="text-sm text-default">Inactivos</span>
                                <input type="checkbox" class="sr-only peer" wire:model.live="estatus_aut">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-2 focus:ring-[#6b1820] after:content-[''] 
                                    after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                </div>
                                <span class="text-sm text-default">Activos</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Nombre</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cédula</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cargo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Teléfono</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Correo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estatus</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($autorizados as $index => $aut)
                            <tr wire:key="{{ $aut->cliente_corporativo_autorizado_id }}" class="border-b hover:bg-gray-50 transition duration-150 {{ $index % 2 == 0 ? '' : '' }}">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $aut->nombre}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $aut->tipo_documento.'-'.$aut->documento}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $aut->cargo }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{$aut->telefono}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{$aut->correo}}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                    @if($aut->activo)
                                        <button wire:click="inoperativo({{ $aut->cliente_corporativo_autorizado_id }})"
                                            title="Inoperativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-700">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="operativo({{ $aut->cliente_corporativo_autorizado_id }})"
                                            title="Operativo" class="inline-flex items-center justify-center p-2 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="6" class="py-7 text-gray-500 text-lg italic bg-gray-50">No hay personas autorizadas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3 flex items-center justify-end gap-4">
                @if ($cliente)
                    {{ $autorizados->links() }}
                @endif
            </div>

        </div> 
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="py-1 px-3 flex items-center justify-start gap-4"> 
            <label for="perPage_bottom" class="block text-sm font-medium text-gray-700">Registros/listado:</label>
            <select wire:model.live="perPage" id="perPage_bottom" class="block w-28 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2.5 px-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>


    @if($modal_open)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Fondo oscuro con efecto blur --}}
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenido del Modal --}}
                <div class="inline-block w-full max-w-4xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">

                    {{-- ENCABEZADO --}}
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            {{-- Ícono de Usuario/Personal --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800" id="modal-title">
                                Registro de Personal Autorizado
                            </h3>
                        </div>
                        {{-- Botón de Cierre (X) --}}
                        <button type="button" wire:click="modalClose" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- FORMULARIO --}}
                    <form wire:submit="submit">
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                
                                {{-- Tipo de Documento --}}
                                <div class="col-span-1">
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo Doc.<span class="text-red-500">*</span></label>
                                    <select id="tipo_documento" wire:model.lazy="tipo_documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                        <option value="">Seleccionar</option>
                                        @foreach ($documentos as $doc)
                                            <option value="{{$doc->tipo}}">{{$doc->tipo}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Nro Documento --}}
                                <div class="col-span-1 sm:col-span-1">
                                    <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">Nro Documento<span class="text-red-500">*</span></label>
                                    <input type="number" id="documento" wire:model.live="documento" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('documento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Nombre --}}
                                <div class="col-span-1 sm:col-span-2">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo<span class="text-red-500">*</span></label>
                                    <input type="text" id="nombre" wire:model.lazy="nombre" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Cargo --}}
                                <div class="col-span-1 sm:col-span-2">
                                    <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo / Puesto<span class="text-red-500">*</span></label>
                                    <input type="text" id="cargo" wire:model.lazy="cargo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('cargo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Teléfono --}}
                                <div class="col-span-1 sm:col-span-1">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono<span class="text-red-500">*</span></label>
                                    <input type="text" id="telefono" wire:model="telefono" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('telefono') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Correo --}}
                                <div class="col-span-1 sm:col-span-1">
                                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                                    <input type="email" id="correo" wire:model="correo" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition">
                                    @error('correo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Cliente Corporativo --}}
                                <div class="col-span-1 sm:col-span-4">
                                    <label for="clientes_modal" class="block text-sm font-medium text-gray-700 mb-1">Asignar a Cliente Corporativo</label>
                                    <select id="clientes_modal" wire:model.live="cliente_sel" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm transition font-semibold text-[#6b1820]">
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($clientes as $clie)
                                            <option value="{{ $clie->cliente_corporativo_id }}">{{ $clie->razon_social }}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente_sel') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                        {{-- PIE DE MODAL (ACCIONES) --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button 
                                type="submit" 
                                class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide"
                            >
                                Registrar Personal
                            </x-button>

                            <x-button 
                                type="button" 
                                wire:click="modalClose" 
                                class="px-5 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm transition shadow-sm"
                            >
                                Cancelar
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @push('scripts')
        @script
        <script>
            Livewire.on('alertSuccess', message => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('alertSuccess2', message => {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('alertSuccess3', message => {
                Swal.fire({
                    position: "center",
                    icon: "info",
                    title: message.message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });

            Livewire.on('recargar', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        </script>
        @endscript
    @endpush
