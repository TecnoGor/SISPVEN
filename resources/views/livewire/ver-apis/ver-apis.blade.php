
<div>
    @section('titulo')
        APIs
    @endsection

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Apis</h1>
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
                            <tr>
                                <th class="px-4 py-3 text-left">Api</th>
                                <th class="px-4 py-3 text-left">Rol</th>
                                <th class="px-4 py-3 text-left">Estatus</th>
                                <th class="px-4 py-3 text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr wire:key="" class="border-b text-left">
                                    <th class="px-4 py-3 font-medium text-black">Mobible API </th>
                                    <th class="px-4 py-3 font-medium text-black">2 </th>
                                    <th class="px-4 py-3 font-medium text-black">3 </th>
                                    <th class="px-4 py-3 font-medium text-green-600">  <button wire:click="" title="Activar" class="text-white p-2 rounded">
                                                    <!-- Icono de Activar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </button>
                               
                                    </th>

                                </tr>
                        </tbody>
                    </table>
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