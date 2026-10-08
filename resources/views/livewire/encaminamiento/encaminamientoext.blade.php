@section('titulo')
    Consulta de Rastreo y Seguimiento
@endsection

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex gap-3">
            <x-return-link :href="route('dashboard')" wire:navigate.hover />
            {{ __('Consultar Rastreo y Seguimiento') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-[90%] mx-auto">
        @if (session()->has('mensaje'))
            <x-alert class="bg-green-100 border-green-600 text-green-600 mb-4">
                {{ session('mensaje') }}
            </x-alert>
        @endif
    </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
                <h1 class="text-center text-3xl font-bold mt-10 text-primary">Consultar Rastreo y Seguimiento</h1>
                <div class="px-10 mx-auto">
                        <label for="buscar" class="">Buscar por:</label><br>
                    </div>
                <div class="px-10 pb-3 mx-auto mb-5 flex">

                    <select wire:model.blur="buscar" id="buscar" class="py-2 rounded-l-lg w-3/12">
                        <option selected>--Seleccione--</option>
                        <option value="1">Código de Envío</option>
                        <option value="2">C.I. Remitente</option>
                    </select><input
                        type="{{ $isNumericInput ? 'number' : 'text' }}"
                        wire:model.debounce.500ms="search"
                        id="search"
                        class=" py-2 w-8/12"
                        maxlength="20"
                        placeholder="@if($buscar == 1)  Inserte Número de Envío @endif @if($buscar == 2) Inserte Cédula de Identidad del Remitente @endif"
                        @if($isNumericInput) oninput="this.value = this.value.replace(/[^0-9]/g, '')" @endif
                        wire:keydown.enter="getPathing"
                    ><x-button
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                        class="py-2 rounded-r-lg w-1/12 bg-primary font-bold text-white border border-primary"
                        wire:loading.attr="disabled"
                        wire:target="getPathing"
                        wire:click="getPathing"
                    >

                            <svg wire:loading.attr="class" wire:loading.attr.class="hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>

                        <x-loading-button wire:target="getPathing"  />

                    </x-button>
                </div>

                @if($envio)
                    <div class="bg-white shadow-md sm:rounded-r-lg sm:rounded-b-lg overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs text-default uppercase bg-gray-100">
                                    <tr class="">
                                        @include('livewire.includes.sort-table', ['column' => 'created_at', 'displayName' => 'Creado'])
                                        @include('livewire.includes.sort-table', ['column' => 'codigo_envio', 'displayName' => 'Código de Envío'])
                                        @include('livewire.includes.sort-table', ['column' => 'nombre_rem', 'displayName' => 'Remitente'])
                                        <th class="px-4 py-3 text-center text-black">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($envio as $envi)
                                        <tr wire:key="{{ $envi->envio_id }}" class="border-b text-left">
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->created_at }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->codigo_envio }}</th>
                                            <th class="px-4 py-3 font-medium text-black">{{ $envi->nombre_rem }}</th>
                                            <th class="px-4 py-3 font-medium flex items-center justify-center gap-4">
                                                <button href="{{ route('encaminamiento.encaminamientoext-detalles', $envi->envio_id) }}" title="Detalles" wire:navigate.hover>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                    </svg>
                                                </button>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
