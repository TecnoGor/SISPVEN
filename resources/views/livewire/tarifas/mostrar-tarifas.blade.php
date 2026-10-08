@section('titulo')
    Servicios
@endsection

<div>
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="mb-6 text-left px-4 sm:px-6 lg:px-8 max-w-[95%] mx-auto">
        <h1 class="text-2xl md:text-3xl text-primary font-bold">Servicios</h1>
        <p class="mt-1 text-sm text-gray-600">Gestión de servicios y tarifas.</p>
    </div>

    <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
        {{-- TABS DE NAVEGACIÓN --}}
        <div class="flex items-center">
            <x-tab-link :href="route('tarifas')" :active="request()->routeIs('tarifas')" wire:navigate.hover class="px-5 py-2">
                {{ __('Servicios Nacionales') }}
            </x-tab-link>

            <x-tab-link :href="route('tarifas-internacionales')" :active="request()->routeIs('tarifas-internacionales')" wire:navigate.hover class="px-5 py-2">
                {{ __('Servicios Internacionales') }}
            </x-tab-link>
        </div>

        <div class="bg-white shadow-xl rounded-xl rounded-tl-none overflow-hidden border border-gray-200 p-6">
            {{-- BARRA DE BÚSQUEDA Y ACCIONES --}}
            <div class="mt-2 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center w-full md:w-2/3">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] block w-full pl-10 p-2.5 transition duration-150"
                                placeholder="Buscar servicio...">
                        </div>
                    </div>

                    <div class="flex items-center justify-start md:justify-end w-full md:w-auto">
                        @can('Crear roles')
                            <x-primary-button wire:click="create" class="px-5 py-2.5 text-sm rounded-md shadow-sm whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Crear Servicio
                            </x-primary-button>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 p-0">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            @include('livewire.includes.sort-table', ['column' => 'nombre', 'displayName' => 'Servicio'])
                            @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Fecha de creación'])
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cobro Cos. Administrativos</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cobro de Excedente</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cobro de Almacenaje</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Cobro A. Llegada</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($servicios as $servicio)
                            <tr wire:key="{{ $servicio->servicio_id }}" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $servicio->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $servicio->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" wire:click="toggleCobroEntrega({{ $servicio->servicio_id }})" title="Cambiar cobro en entrega" class="inline-flex items-center">
                                        <div class="relative">
                                            <input type="checkbox" class="sr-only peer" {{ $servicio->cobra_entrega ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-300 rounded-full
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                                after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                            </div>
                                        </div>
                                        <span class="ml-2 text-xs font-semibold {{ $servicio->cobra_entrega ? 'text-[#6b1820]' : 'text-gray-400' }}">
                                            {{ $servicio->cobra_entrega ? 'SÍ' : 'NO' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" wire:click="toggleCobroExcedente({{ $servicio->servicio_id }})" title="Cambiar cobro de excedente" class="inline-flex items-center">
                                        <div class="relative">
                                            <input type="checkbox" class="sr-only peer" {{ $servicio->cobra_excedente ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-300 rounded-full
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                                after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                            </div>
                                        </div>
                                        <span class="ml-2 text-xs font-semibold {{ $servicio->cobra_excedente ? 'text-[#6b1820]' : 'text-gray-400' }}">
                                            {{ $servicio->cobra_excedente ? 'SÍ' : 'NO' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" wire:click="toggleCobroAlmacenaje({{ $servicio->servicio_id }})" title="Cambiar cobro de almacenaje" class="inline-flex items-center">
                                        <div class="relative">
                                            <input type="checkbox" class="sr-only peer" {{ $servicio->cobra_almacenaje ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-300 rounded-full
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                                after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                            </div>
                                        </div>
                                        <span class="ml-2 text-xs font-semibold {{ $servicio->cobra_almacenaje ? 'text-[#6b1820]' : 'text-gray-400' }}">
                                            {{ $servicio->cobra_almacenaje ? 'SÍ' : 'NO' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" wire:click="toggleCobroAvisosLlegada({{ $servicio->servicio_id }})" title="Cambiar cobro de avisos de llegada" class="inline-flex items-center">
                                        <div class="relative">
                                            <input type="checkbox" class="sr-only peer" {{ $servicio->cobra_avisos_llegada ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-300 rounded-full
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                                after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                            </div>
                                        </div>
                                        <span class="ml-2 text-xs font-semibold {{ $servicio->cobra_avisos_llegada ? 'text-[#6b1820]' : 'text-gray-400' }}">
                                            {{ $servicio->cobra_avisos_llegada ? 'SÍ' : 'NO' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <a href="{{ route('ver-tarifas', $servicio->servicio_id) }}" title="Ver Tarifas" wire:navigate.hover class="p-2 rounded-lg hover:bg-green-50 transition inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600 group-hover:scale-110 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="7" class="py-10 text-gray-500 italic bg-gray-50">No hay Servicios disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 pt-4">
            <div class="flex items-center gap-4">
                <label class="block text-sm font-medium text-gray-700">Registros/listado:</label>
                <select wire:model.live="perPage" class="block w-24 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#6b1820] focus:border-[#6b1820] text-sm py-2 px-2">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="w-full md:w-auto">
                {{ $servicios->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL: Crear Rol --}}
    <form wire:submit="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                Crear Rol
            </x-slot>

            <x-slot name="content">
                <div class="mb-4">
                    <x-input-label for="createForm.name" :value="__('Nombre del Rol')" />
                    <x-text-input id="createForm.name" class="block mt-1 w-full" type="text" wire:model.blur="createForm.name" :value="old('createForm.name')" placeholder="Admin, Super Usuario, Gerente..."/>
                    <x-input-error :messages="$errors->get('createForm.name')" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('createForm.open', false)" type="button">
                        Cancelar
                    </x-danger-button>

                    <x-primary-button wire:loading.attr="disabled" wire:target="store">
                        Crear
                        <x-loading-button wire:target="store"/>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- MODAL: Actualizar Rol --}}
    <form wire:submit="update">
        <x-dialog-modal wire:model="editForm.open">
            <x-slot name="title">
                Actualizar Rol
            </x-slot>

            <x-slot name="content">
                <div class="mb-4">
                    <x-input-label for="editForm.name" :value="__('Nombre del Rol')" />
                    <x-text-input id="editForm.name" class="block mt-1 w-full" type="text" wire:model.blur="editForm.name" :value="old('editForm.name')" placeholder="Admin, Super Usuario, Gerente..."/>
                    <x-input-error :messages="$errors->get('editForm.name')" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-danger-button class="mr-2" wire:click="$set('editForm.open', false)" type="button">
                        Cancelar
                    </x-danger-button>

                    <x-primary-button wire:loading.attr="disabled" wire:target="update">
                        Guardar
                        <x-loading-button wire:target="update"/>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- MODAL: Crear Servicio --}}
    @if( (is_array($CreateForm) && ($CreateForm['open'] ?? false)) || (is_object($CreateForm) && ($CreateForm->open ?? false)) )
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 bg-gray-900 bg-opacity-60 backdrop-blur-sm sm:p-0">
                <div class="inline-block w-full max-w-2xl overflow-hidden align-middle transition-all transform bg-white rounded-xl shadow-2xl">
                    <div class="bg-gray-100 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#6b1820] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-800">Crear Servicio</h3>
                        </div>

                        <button type="button" wire:click="$set('CreateForm.open', false)" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-200 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="p-6 space-y-4">
                            @if (session()->has('error'))
                                <div class="bg-red-500 text-white p-4 rounded mb-4">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div>
                                <x-input-label for="CreateForm.nombre" :value="__('Nombre Del Servicio')" />
                                <x-text-input id="CreateForm.nombre" class="block mt-1 w-full" type="text" wire:model.blur="CreateForm.nombre" :value="old('CreateForm.nombre')" placeholder="Nombre De Servicio"/>
                                <x-input-error :messages="$errors->get('CreateForm.nombre')" class="mt-2" />
                            </div>

                            <div class="flex flex-col">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio:</label>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                        <span class="text-sm {{ !$CreateForm->internacional ? 'text-blue-600 font-bold' : 'text-gray-400' }}">
                                            Internacional
                                        </span>
                                        <div class="relative">
                                            <input type="checkbox" class="sr-only peer" wire:model.live="CreateForm.internacional">                                      
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#6b1820]
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                                after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                                peer-checked:bg-[#6b1820] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                            </div>
                                        </div>
                                        <span class="text-sm {{ $CreateForm->internacional ? 'text-[#6b1820] font-bold' : 'text-gray-400' }}">
                                            Nacional
                                        </span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Determina si el servicio operará dentro o fuera del país.</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                            <x-button type="button" wire:click="store" class="px-8 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-bold text-sm shadow-md uppercase">Crear</x-button>
                            <x-button type="button" wire:click="$set('CreateForm.open', false)" class="px-6 py-2.5 bg-[#6b1820] text-white hover:bg-[#7b1f27] rounded-lg font-medium text-sm">Cancelar</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({
                position: "center",
                icon: "success",
                title: message.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        Livewire.on('tarifaUpdated', () => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
    </script>
@endpush