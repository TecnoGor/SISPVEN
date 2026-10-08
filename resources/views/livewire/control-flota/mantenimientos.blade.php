
@section('titulo')
    Control de Flota
@endsection

<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <div class="mb-4 sm:mb-0">
                    <h2 class="text-xl  leading-tight flex gap-3 text-primary font-bold mt-10">
                        <x-return-link :href="route('flota')" wire:navigate.hover />
                        {{ __('Regresar') }}
                    </h2> 
                </div>
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Mantenimientos: {{ $vehiculo->marca }} {{ $vehiculo->placa }}</h1>
            </div>
        </div>
            <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
                <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
                    <div class="flex w-full md:w-auto">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input  type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2 "
                                placeholder="Buscar...">
                        </div>
                    </div>
                    
                </div>
        
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-default uppercase bg-gray-100">
                            <tr class="">
                                @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Id'])
                                @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'descripcion'])
                                @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Fecha'])
                                <th class="px-4 py-3">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mantenimientos  as $index => $mantenimiento)
                                <tr wire:key="mantenimiento-{{ $mantenimiento->mantenimiento_id }}" class="border-b text-left">
                                    <th class="px-4 py-3 font-medium text-black">{{ $index + 1 }}</th> 
                                    <th class="px-4 py-3 font-medium text-black">{{ $mantenimiento->descripcion }}</th>
                                    <th class="px-4 py-3 font-medium text-black">
                                        {{ \Carbon\Carbon::parse($mantenimiento->created_at)->format('d/m/Y h:i A') }}

                                    </th>
                                    
                                    <th class="px-4 py-3 text-center">
                                        <button wire:click="toggleDetails({{ $mantenimiento->mantenimiento_id }})" class="focus:outline-none">
                                            @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                                                <!-- Flecha hacia arriba (SVG) -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <!-- Flecha hacia abajo (SVG) -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    
                                </tr>
                                @if ($expandedMantenimientoId === $mantenimiento->mantenimiento_id)
                                    @foreach ($mantenimiento->detalles as $index => $detalle) <!-- Agregado $index para el contador -->
                                        <tr class="border-b bg-gray-80">
                                            <td colspan="5" class="px-4 py-3 text-black text-left"> <!-- Cambié text-right por text-center -->
                                                {{ $index + 1 }}. {{ $detalle->servicioFlota->nombre }} - {{ \Carbon\Carbon::parse($detalle->fecha)->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr class="border-b text-center">
                                    <td colspan="8" class="py-7 text-gray-500 text-lg">No hay Mantenimientos realizados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="py-4 px-3">
                    {{ $mantenimientos->links() }}
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