<div class="p-8 bg-white rounded-xl shadow-xl max-w-4xl max-h-[90vh] overflow-y-auto mx-auto border border-gray-100">
    <div class="mb-8 border-b pb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Información de Oficina</h2>
        <p class="text-sm text-gray-500 mt-1">Complete los campos detallados a continuación</p>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div class="space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Nombre</label>
                <input type="text" wire:model.defer="nombre" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150 outline-none">
                @error('nombre')
                    <span class="text-xs font-semibold text-red-500 mt-1 flex items-center italic">● {{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Correo Electrónico</label>
                <input type="email" wire:model.defer="correo" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150 outline-none">
                @error('correo')
                    <span class="text-xs font-semibold text-red-500 mt-1 flex items-center italic">● {{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Teléfono</label>
                <input type="text" wire:model.defer="telefono" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150 outline-none">
                @error('telefono')
                    <span class="text-xs font-semibold text-red-500 mt-1 flex items-center italic">● {{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Parroquia</label>
                <select wire:model.defer="parroquia" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150 outline-none appearance-none">
                    <option value="">Seleccionar parroquia...</option>
                    @foreach($parroquias as $parroquia)
                        <option value="{{ $parroquia->parroquia_id }}">{{ $parroquia->nombre }}</option>
                    @endforeach
                </select>
                @error('parroquia')
                    <span class="text-xs font-semibold text-red-500 mt-1 flex items-center italic">● {{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Código Postal</label>
                <select wire:model.defer="codigo_postal" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150">
                    <option value="">Seleccionar</option>
                    @foreach($codigos_postales as $codigo)
                        <option value="{{ $codigo }}">{{ $codigo }}</option>
                    @endforeach
                </select>
                @error('codigo_postal')
                    <span class="text-xs text-red-500 mt-1 block italic">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Latitud</label>
                <input type="text" wire:model.defer="latitud" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150" placeholder="0.000000">
                @error('latitud')
                    <span class="text-xs text-red-500 mt-1 block italic">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Longitud</label>
                <input type="text" wire:model.defer="longitud" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150" placeholder="0.000000">
                @error('longitud')
                    <span class="text-xs text-red-500 mt-1 block italic">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div class="space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Condición del Inmueble</label>
                <select wire:model.live="condicion" wire:change="obtener_condicion" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150">
                    <option value="">Seleccionar...</option>
                    @foreach($condiciones as $condicion)
                        <option value="{{ $condicion }}">{{ $condicion }}</option>
                    @endforeach
                </select>
                @error('condicion')
                    <span class="text-xs font-semibold text-red-500 mt-1 italic">● {{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center mt-6">
                <input type="checkbox" wire:model.defer="zona_economica" id="zona_economica" 
                    class="h-4 w-4 text-[#6b1820] border-gray-300 rounded focus:ring-[#6b1820]">
                <label for="zona_economica" class="ml-2 text-sm text-gray-700">Zona Económica Especial</label>
                @error('zona_economica')
                    <span class="text-xs text-red-600 mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            @if($oficina['tipo_oficina_id'] == 4)
                <div class="flex items-center mt-6">
                    <input type="checkbox" wire:model.defer="centralizadora" id="centralizadora" 
                        class="h-4 w-4 text-[#6b1820] border-gray-300 rounded focus:ring-[#6b1820]">
                    <label for="centralizadora" class="ml-2 text-sm text-gray-700">Centralizadora?</label>
                    @error('centralizadora')
                        <span class="text-xs text-red-600 mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

        @if($modal)
            <div class="md:col-span-2 w-full mt-4 border border-gray-200 rounded-lg p-4 bg-gray-50 shadow-inner">
                <h3 class="text-sm font-bold text-[#6b1820] mb-4 uppercase tracking-tight">Lapso de Tiempo del Contrato</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicial</label>
                        <input type="date" wire:model.live="fecha_inicial" 
                            class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150">
                        @error('fecha_inicial')
                            <span class="text-xs text-red-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Final</label>
                        <input type="date" wire:model.live="fecha_final" 
                            class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150">
                        @error('fecha_final')
                            <span class="text-xs text-red-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="md:col-span-2 space-y-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">Dirección Exacta</label>
            <textarea wire:model.defer="direccion" rows="3" class="w-full px-4 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full p-2.5 transition duration-150 outline-none resize-none" placeholder="Calle, edificio, número..."></textarea>
            @error('direccion')
                <span class="text-xs font-semibold text-red-500 mt-1 italic">● {{ $message }}</span>
            @enderror
        </div>

        <div class="md:col-span-2 text-center pt-6">
            <button type="submit" 
                class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm transition shadow-md uppercase tracking-wide">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        // success alert
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1000
            });
        })

         // success alert
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 3000
            });
        })
    </script>
    @endscript   
@endpush