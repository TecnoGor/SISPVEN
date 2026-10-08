<div>
    @section('titulo')
        Beneficiarios de Apartados
    @endsection

    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-4">
            <h1 class="text-2xl md:text-3xl text-primary font-bold mt-10">Beneficiarios de Apartados Postales</h1>
        </div>

        {{-- ══════════════ MODO 1: BUSCAR APARTADO ══════════════ --}}
        @if (! $registro_selec)
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between p-4 border-b border-gray-100">
                    <div class="relative w-full sm:max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary block w-full pl-10 p-2.5 transition"
                            placeholder="Buscar titular por documento, nombre o apellido...">
                    </div>
                    <div wire:loading.delay wire:target="search" class="text-xs text-gray-400 font-medium">Buscando…</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-wide text-white bg-primary">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Titular</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Documento</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Casillero</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-center">Beneficiarios</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registros as $registro)
                                <tr wire:key="reg-{{ $registro->registro_apartado_id }}" class="odd:bg-white even:bg-gray-50/50 hover:bg-primary/5 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ trim($registro->nombre.' '.$registro->apellido) }}</td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $registro->tipo_documento.'-'.$registro->documento }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs font-semibold text-gray-900">{{ $registro->apartado?->apartado ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-semibold text-primary">
                                            {{ $registro->beneficiarios_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="seleccionar({{ $registro->registro_apartado_id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                                            Gestionar
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                            </svg>
                                            <span class="text-base font-medium text-gray-500">
                                                {{ $search ? 'Sin resultados para la búsqueda' : 'Busca un apartado por el documento del titular' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($registros && $registros->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $registros->links() }}
                    </div>
                @endif
            </div>

        {{-- ══════════════ MODO 2: GESTIONAR BENEFICIARIOS ══════════════ --}}
        @else
            {{-- Cabecera del titular --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5 mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <button wire:click="volver" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-primary transition mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                            Volver a la búsqueda
                        </button>
                        <h2 class="text-lg font-bold text-gray-900">{{ trim($registro_selec->nombre.' '.$registro_selec->apellido) }}</h2>
                        <p class="text-sm text-gray-500">
                            {{ $registro_selec->tipo_documento }}-{{ $registro_selec->documento }}
                            <span class="mx-2 text-gray-300">·</span>
                            Casillero <span class="font-mono font-semibold text-gray-700">{{ $registro_selec->apartado?->apartado ?? '—' }}</span>
                        </p>
                    </div>
                    <button wire:click="nuevoBeneficiario"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40 transition self-start sm:self-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Agregar beneficiario
                    </button>
                </div>
            </div>

            {{-- Formulario (crear / editar) --}}
            @if ($mostrar_form)
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-5 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500 mb-4">
                        {{ $beneficiario_id ? 'Editar beneficiario' : 'Nuevo beneficiario' }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo Documento<span class="text-red-500">*</span></label>
                            <select wire:model="tipo_documento" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                                <option value="">Seleccionar</option>
                                @foreach ($documentos as $doc)
                                    <option value="{{ $doc->tipo }}">{{ $doc->tipo }}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nro Documento<span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live.debounce.500ms="documento" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            @error('documento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre<span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nombre" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido<span class="text-red-500">*</span></label>
                            <input type="text" wire:model="apellido" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            @error('apellido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" wire:model="telefono" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail</label>
                            <input type="email" wire:model="correo" class="mt-1 block w-full border border-gray-300 rounded-lg text-sm p-2 focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            @error('correo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-5">
                        <button type="button" wire:click="$set('mostrar_form', false)"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button wire:click="guardar" wire:loading.attr="disabled" wire:target="guardar"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                            {{ $beneficiario_id ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </div>
            @endif

            {{-- Tabla de beneficiarios --}}
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-wide text-white bg-primary">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Nombre</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Documento</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Teléfono</th>
                                <th scope="col" class="px-4 py-3 font-semibold">E-mail</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-center">Estado</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registro_selec->beneficiarios as $b)
                                <tr wire:key="ben-{{ $b->apartado_postal_beneficiario_id }}" class="odd:bg-white even:bg-gray-50/50 hover:bg-primary/5 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ trim($b->nombre.' '.$b->apellido) }}</td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $b->tipo_documento.'-'.$b->documento }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $b->telefono ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $b->correo ?: '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($b->activo)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Activo</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="editar({{ $b->apartado_postal_beneficiario_id }})" title="Editar"
                                                class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                </svg>
                                            </button>
                                            <button wire:click="toggleActivo({{ $b->apartado_postal_beneficiario_id }})" title="{{ $b->activo ? 'Desactivar' : 'Activar' }}"
                                                class="inline-flex items-center justify-center rounded-lg p-1.5 {{ $b->activo ? 'text-red-500 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} focus:outline-none focus:ring-2 focus:ring-primary/40 transition">
                                                @if ($b->activo)
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                            </svg>
                                            <span class="text-base font-medium text-gray-500">Este apartado no tiene beneficiarios aún</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
    @script
    <script>
        Livewire.on('alertSuccess', message => {
            Swal.fire({ position: "center", icon: "success", title: message.message, showConfirmButton: false, timer: 1200 });
        });
        Livewire.on('alertSuccess2', message => {
            Swal.fire({ position: "center", icon: "error", title: message.message, showConfirmButton: false, timer: 2500 });
        });
    </script>
    @endscript
@endpush