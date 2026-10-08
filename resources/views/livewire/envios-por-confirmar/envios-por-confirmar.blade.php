<div>
    @section('titulo')
        Envios Por Confirmar
    @endsection
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">ENVIOS POR CONFIRMAR</h1>
            </div>

            {{-- <x-button class="mb-6 sm:mb-0 mt-10" wire:click="reporte_excel">
                REPORTE EXCEL
            </x-button> --}}
        </div>
        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            {{-- Barra de búsqueda --}}
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between p-4 border-b border-gray-100">
                <div class="relative w-full sm:max-w-xs">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-400"
                            fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary block w-full pl-10 p-2.5 transition"
                        placeholder="Buscar por documento...">
                </div>

                <div wire:loading.delay wire:target="search" class="text-xs text-gray-400 font-medium">
                    Buscando…
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-wide text-white bg-primary">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Servicio</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Usuario</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Fecha de creación</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Código de Envío</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Remitente</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Documento</th>
                            <th scope="col" class="px-4 py-3 font-semibold text-center">Térmica</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($envios as $envio)
                            <tr wire:key="{{ $envio->envio_id }}" class="odd:bg-white even:bg-gray-50/50 hover:bg-primary/5 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-semibold text-primary">
                                        {{ $envio->servicio?->nombre ?? 'No asignado' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $envio->users?->name ?? 'No encontrado' }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $envio->created_at?->toFormattedDateString() ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-ms font-semibold text-gray-900">{{ $envio->codigo_envio ?? 'No encontrado' }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ trim($envio->nombre_rem.' '.$envio->apellido_rem) ?: 'No encontrado' }}</td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $envio->tipo_documento_rem.'-'.$envio->documento_rem }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($envio->tipo_envio === 'nacional')
                                        <button wire:click="generarTermica({{ $envio->envio_id }})"
                                            title="Generar térmica"
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500/40 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                        <span class="text-base font-medium text-gray-500">No hay envíos por confirmar</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pie: selector de registros (izquierda) + paginación (derecha) --}}
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-between p-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm text-gray-600 order-2 sm:order-1">
                    <span>Mostrar</span>
                    <select wire:model.live="por_pagina"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary py-1.5 pl-2.5 pr-8">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>registros</span>
                </div>

                <div class="order-1 sm:order-2 w-full sm:w-auto">
                    {{ $envios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess2', message => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: message.message,
                showConfirmButton: false,
                timer: 2500
            });
        })
    </script>
    @endscript
@endpush