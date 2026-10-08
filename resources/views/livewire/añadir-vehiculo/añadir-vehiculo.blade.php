<div class="p-6 max-h-[80vh] overflow-y-auto">

    {{-- Encabezado --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Vehículo Externo</p>
            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">Vincular vehículo de otra oficina</p>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-4">

        {{-- Estado --}}
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                Estado <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="estado"
                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                <option value="">— Seleccione un estado —</option>
                @foreach($estados as $est)
                    <option value="{{ $est->estado_id }}">{{ $est->nombre }}</option>
                @endforeach
            </select>
        </div>

        {{-- Oficina --}}
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                Oficina <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="oficina"
                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                <option value="">— Seleccione una oficina —</option>
                @foreach($oficinas as $of)
                    <option value="{{ $of->oficina_id }}">{{ $of->nombre }}</option>
                @endforeach
            </select>
        </div>

        {{-- Vehículo --}}
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                Vehículo <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="vehiculo"
                class="block w-full border border-gray-300 rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm px-3 py-2.5 bg-white">
                <option value="">— Seleccione un vehículo —</option>
                @foreach($vehiculos as $veh)
                    <option value="{{ $veh->vehiculo_id }}">{{ $veh->marca . ' — ' . $veh->placa }}</option>
                @endforeach
            </select>
            @error('vehiculo')
                <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 pt-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="guardar"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition active:scale-95 disabled:opacity-60">
                <svg wire:loading wire:target="guardar" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Registrar Vehículo
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