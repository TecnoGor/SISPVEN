<div>
    @section('titulo')
        Valija
    @endsection

    {{-- Valija de carga por peso: no admite envíos, se cierra vacía indicando
         solo el peso. Se configura en Parámetros de Valijas. --}}
    @php($esCargaPorPeso = (bool) ($saca->tipoSaca->cargar_por_peso ?? false))

    <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
        <x-return-link :href="route('mostrar-sacas')" wire:navigate.hover />
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6 mt-8">
            <div>
                {{-- Estado de la valija: siempre visible, con texto + forma, no solo color. --}}
                @if($saca->cerrado)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                        </svg>
                        Cerrada
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path d="M18 1.5c2.9 0 5.25 2.35 5.25 5.25v.75a.75.75 0 0 1-1.5 0V6.75a3.75 3.75 0 1 0-7.5 0V9h.75a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h7.75V6.75C12.75 3.85 15.1 1.5 18 1.5Z" />
                        </svg>
                        Abierta
                    </span>
                @endif

                <h1 class="text-2xl md:text-3xl text-primary font-bold mt-2 leading-tight">
                    Valija {{ $saca->codigo_saca }}
                </h1>
                <p class="text-base text-gray-700 mt-1">
                    <span class="text-gray-500">Oficina destino:</span>
                    <span class="font-semibold text-primary-dark">{{ $saca->oficinaDestino->nombre ?? 'Sin oficina' }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if(!$saca->cerrado && ($envios->count() > 0 || $esCargaPorPeso))
                    @if($totalPeso <= 30000)
                        <x-primary-button wire:click="closeSaca">
                            Cerrar Valija
                        </x-primary-button>
                    @else
                        <p class="text-red-600 text-sm">No se puede cerrar la valija, peso mayor a 30 kilos.</p>
                    @endif
                @endif

                @if(!$saca->cerrado && !$esCargaPorPeso)
                    <x-primary-button wire:click="create()">
                        Insertar Despachos
                    </x-primary-button>
                @endif
                @if(!$saca->cerrado && !$sacaYaSalio)
                    <x-primary-button wire:click="abrirEdicionValija">
                        Editar Valija
                    </x-primary-button>
                @endif
                @if($saca->cerrado)
                    <x-primary-button wire:click="imprimirRelacion">
                        Imprimir Relación de Envío Certificado
                    </x-primary-button>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-md sm:rounded-lg overflow-hidden border border-gray-200">
            <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
                <div class="flex w-full md:w-auto">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                                fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2 "
                            placeholder="Buscar...">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="overflow-y-auto max-h-80">
                    <table class="table-auto w-full text-sm">
                        <thead class="text-xs text-gray-600 uppercase tracking-wide bg-gray-100 sticky top-0">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left font-semibold">Código</th>
                                <th scope="col" class="px-3 py-3 text-left font-semibold">Contenido</th>
                                <th scope="col" class="px-3 py-3 text-right font-semibold">Peso</th>
                                <th scope="col" class="px-3 py-3 text-right font-semibold"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($envios as $envio)
                            <tr wire:key="{{ $envio->envio_id }}" class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-3 py-2 font-semibold text-gray-900 tabular-nums">{{ $envio->codigo_envio }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $envio->contenido }}</td>
                                <td class="px-3 py-2 text-gray-900 text-right tabular-nums whitespace-nowrap">{{ $envio->peso }} gr</td>
                                <td class="px-3 py-2 text-right">
                                @if(!$saca->cerrado)
                                    <button wire:click="eliminarEnvio({{ $envio->envio_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="eliminarEnvio({{ $envio->envio_id }})"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-primary hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                                        title="Eliminar envío de la valija">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 text-base">No hay envíos en esta valija.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Cantidad de envíos --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Envíos en la valija</p>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-bold text-primary tabular-nums">{{ $totalEnvios }}</span>
                    <span class="text-lg text-gray-700">{{ $totalEnvios == 1 ? 'envío' : 'envíos' }}</span>
                </div>
                @if ($search !== '' && $envios->count() !== $totalEnvios)
                    {{-- $envios viene filtrado por el buscador; se aclara para que el
                         operador no lea el filtrado como el total real de la valija. --}}
                    <p class="text-sm text-gray-600 mt-1.5">
                        Mostrando <span class="font-medium text-gray-800 tabular-nums">{{ $envios->count() }}</span> por el filtro actual
                    </p>
                @endif
            </div>

            {{-- Peso --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Peso final</p>
                <div class="flex items-center gap-2 mt-1.5">
                    @if($saca->cerrado)
                        <span class="text-2xl font-bold text-primary tabular-nums">{{ $saca->peso ?? 'N/A' }}</span>
                        <span class="text-lg text-gray-700">gr</span>
                    @else
                        <input
                            type="number"
                            min="0"
                            step="1"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            wire:model.defer="pesoFinal"
                            class="border border-gray-300 rounded-md px-2 py-1 w-28 text-lg text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary tabular-nums"
                            placeholder="gramos"
                        >
                        <span class="text-lg text-gray-700">gr</span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-1.5">Sugerido (aprox.): <span class="font-medium text-gray-800 tabular-nums">{{ $totalPeso }} gr</span></p>
                @error('pesoFinal') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Precinto --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">N° de precinto</p>
                <div class="flex items-center gap-2 mt-1.5">
                    @if($saca->cerrado)
                        <span class="text-2xl font-bold text-primary tabular-nums">{{ $saca->numero_precinto ?? 'N/A' }}</span>
                    @else
                        @if($editandoPrecinto)
                            <input type="text" wire:model="nuevoPrecinto" class="border border-gray-300 rounded-md px-2 py-1 text-gray-900 text-lg focus:border-primary focus:ring-1 focus:ring-primary">
                            <div class="flex flex-col gap-2 shrink-0">
                                <x-primary-button wire:click="guardarPrecinto">Guardar</x-primary-button>
                                <x-danger-button wire:click="$set('editandoPrecinto', false)">Cancelar</x-danger-button>
                            </div>
                        @else
                            <span class="text-2xl font-bold text-primary tabular-nums">{{ $saca->numero_precinto ?? 'N/A' }}</span>
                            <button wire:click="$set('editandoPrecinto', true)"
                                class="inline-flex items-center justify-center rounded-md p-1.5 text-primary hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-primary/40 transition-colors"
                                title="Editar precinto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 2.487a2.25 2.25 0 013.182 3.183l-11.38 11.38a4.5 4.5 0 01-1.897 1.128l-4.124 1.177a.75.75 0 01-.917-.918l1.177-4.124a4.5 4.5 0 011.128-1.897l11.38-11.38z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5l-6-6" />
                                </svg>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

@if(!$saca->cerrado && !$esCargaPorPeso)
<div class="mt-8">
    <h2 class="text-base font-semibold text-primary mb-2">
        Envíos sugeridos para esta valija
    </h2>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            @if(is_null($enviosSugeridos))
                {{-- Aún no cargados: wire:init dispara la carga en una petición aparte,
                     así el detalle de la valija se muestra sin esperar estas consultas. --}}
                <div wire:init="cargarSugeridos" class="flex items-center justify-center gap-2 py-8 text-gray-600">
                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Cargando envíos sugeridos...
                </div>
            @else
                @forelse($enviosSugeridos as $envio)
                    <button
                        wire:click="agregarEnvioSugerido({{ $envio->envio_id }})"
                        wire:loading.attr="disabled"
                        wire:target="agregarEnvioSugerido({{ $envio->envio_id }})"
                        class="group w-full flex items-center gap-4 px-3 py-2 text-left hover:bg-primary-light focus:outline-none focus:bg-primary-light disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                        title="Añadir a la valija"
                    >
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline gap-2">
                                <span class="font-semibold text-primary tabular-nums">{{ $envio->codigo_envio }}</span>
                                <span class="text-sm text-gray-700 truncate">{{ $envio->contenido }}</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-0.5">
                                <span class="tabular-nums">{{ $envio->peso }} gr</span>
                                <span class="mx-1.5 text-gray-300">·</span>
                                {{ $envio->servicio->nombre ?? '-' }}
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1 rounded-md bg-primary px-2.5 py-1 text-xs font-semibold text-white group-hover:bg-primary-dark transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                            </svg>
                            Añadir
                        </span>
                    </button>
                @empty
                    <div class="py-8 text-center text-gray-500">
                        No hay envíos sugeridos para este tipo de valija.
                    </div>
                @endforelse
            @endif
        </div>
    </div>
</div>
@endif


    <form wire:submit.prevent="store">
        <x-dialog-modal wire:model="createForm.open">
            <x-slot name="title">
                Insertar Envíos a Valija
            </x-slot>

            <x-slot name="content">
                {{-- Ingresar múltiples códigos de envío --}}
                <div>
                    <x-input-label for="createForm.codigosEnvios" :value="__('Códigos de envío')" />
                    <textarea
                        id="createForm.codigosEnvios"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary font-mono text-sm tabular-nums"
                        rows="6"
                        wire:model.defer="codigosEnvios"
                        placeholder="Un código por línea. Ejemplo:&#10;ABC123&#10;DEF456&#10;GHI789"
                    ></textarea>
                    <p class="text-sm text-gray-600 mt-1">Ingrese un código por línea para procesar varios envíos a la vez.</p>
                    <x-input-error :messages="$errors->get('createForm.codigosEnvios')" class="mt-2" />
                </div>

                {{-- Resultados de la búsqueda --}}
                <div class="mt-4 space-y-2" wire:loading.remove wire:target="buscarEnvios">
                    @if (count($enviosEncontrados) > 0)
                        <div class="rounded-md border border-green-200 bg-green-50 p-3">
                            <h4 class="flex items-center gap-1.5 font-semibold text-green-800 text-sm mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                Válidos para agregar ({{ count($enviosEncontrados) }})
                            </h4>
                            <ul class="space-y-1 max-h-32 overflow-y-auto text-sm text-green-900">
                                @foreach($enviosEncontrados as $envio)
                                    <li><span class="font-semibold tabular-nums">{{ $envio->codigo_envio }}</span> — {{ $envio->contenido }} · {{ $envio->peso }} gr</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (count($enviosYaEnSaca) > 0)
                        <div class="rounded-md border border-amber-200 bg-amber-50 p-3">
                            <h4 class="flex items-center gap-1.5 font-semibold text-amber-800 text-sm mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                                Ya están en una valija ({{ count($enviosYaEnSaca) }})
                            </h4>
                            <ul class="space-y-1 text-sm text-amber-900">
                                @foreach($enviosYaEnSaca as $envio)
                                    <li><span class="font-semibold tabular-nums">{{ $envio->codigo_envio }}</span> — {{ $envio->contenido }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (count($enviosNoEncontrados) > 0)
                        <div class="rounded-md border border-red-200 bg-red-50 p-3">
                            <h4 class="flex items-center gap-1.5 font-semibold text-red-800 text-sm mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" /></svg>
                                No encontrados ({{ count($enviosNoEncontrados) }})
                            </h4>
                            <ul class="space-y-1 text-sm text-red-900 tabular-nums">
                                @foreach($enviosNoEncontrados as $codigo)
                                    <li>{{ $codigo }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-between">
                    <x-secondary-button wire:click="buscarEnvios" wire:loading.attr="disabled" wire:target="buscarEnvios">
                        <span wire:loading.remove wire:target="buscarEnvios">Buscar Envíos</span>
                        <span wire:loading wire:target="buscarEnvios">Buscando...</span>
                    </x-secondary-button>
                    
                    <div class="flex gap-2">
                        <x-danger-button wire:click="closeModal" type="button">
                            Cancelar
                        </x-danger-button>

                        @if (count($enviosEncontrados) > 0)
                            <x-primary-button wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">Agregar {{ count($enviosEncontrados) }} Envíos</span>
                                <span wire:loading wire:target="store">Agregando...</span>
                            </x-primary-button>
                        @else
                            <x-primary-button class="opacity-50 cursor-not-allowed" disabled>
                                Agregar Envíos
                            </x-primary-button>
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-dialog-modal>
    </form>

    {{-- MODAL: Editar valija (tipo y oficina de destino) --}}
    <x-dialog-modal wire:model="editandoValija">
        <x-slot name="title">
            Editar Valija
        </x-slot>

        <x-slot name="content">
            <div class="space-y-5">
                {{-- Tipo de valija --}}
                <div>
                    <x-input-label for="editTipoSaca" :value="__('Tipo de valija')" />
                    <select id="editTipoSaca" wire:model="editTipoSaca"
                        @if($envios->count() > 0) disabled @endif
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary @if($envios->count() > 0) bg-gray-100 text-gray-500 cursor-not-allowed @endif">
                        <option value="">Seleccione un tipo</option>
                        @foreach($tiposEdit as $tipo)
                            <option value="{{ $tipo->tipo_saca_id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @if($envios->count() > 0)
                        <p class="mt-1.5 flex items-start gap-1 text-xs text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 mt-px shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                            No se puede cambiar el tipo: la valija ya tiene envíos.
                        </p>
                    @endif
                    <x-input-error :messages="$errors->get('editTipoSaca')" class="mt-2" />
                </div>

                {{-- Destino: estado + oficina, agrupados --}}
                <fieldset class="border-t border-gray-200 pt-4">
                    <legend class="text-xs font-semibold uppercase tracking-wide text-gray-600 mb-2">Destino</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="editEstadoDest" :value="__('Estado')" />
                            <select id="editEstadoDest" wire:model.live="editEstadoDest"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">Seleccione un estado</option>
                                @foreach($estadosEdit as $estado)
                                    <option value="{{ $estado->estado_id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="editOficinaDest" :value="__('Oficina')" />
                            <select id="editOficinaDest" wire:model="editOficinaDest"
                                @if(empty($oficinasEdit) || count($oficinasEdit) === 0) disabled @endif
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                <option value="">{{ empty($editEstadoDest) ? 'Seleccione un estado primero' : 'Seleccione una oficina' }}</option>
                                @foreach($oficinasEdit as $oficina)
                                    <option value="{{ $oficina->oficina_id }}">{{ $oficina->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('editOficinaDest')" class="mt-2" />
                        </div>
                    </div>
                </fieldset>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end">
                <x-danger-button class="mr-2" wire:click="cerrarEdicionValija" type="button">
                    Cancelar
                </x-danger-button>
                <x-primary-button wire:click="actualizarValija" wire:loading.attr="disabled" wire:target="actualizarValija">
                    Guardar
                </x-primary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('mostrarAlerta', role_id => {
            Swal.fire({
                title: "Eliminar Rol?",
                text: "Un rol eliminado no se puede recuperar!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // eliminar el rol
                    Livewire.dispatch('delete', {role: role_id});
                }
            });
        });

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