<div class="w-full bg-white p-6 rounded-lg shadow-sm">

    <div class="flex flex-col md:flex-row justify-between items-end border-b border-gray-100 pb-6 mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                @if($current_view == 'dashboard') Mi Dashboard de Correspondencia
                @elseif($current_view == 'inbox') Bandeja de Entrada
                @elseif($current_view == 'detail') Revisión de Comunicado
                @elseif($current_view == 'detail_sent') Comunicados Enviados
                @endif
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                Rol Activo: <span class="font-bold text-red-600">{{ $current_role }}</span>
                <span class="text-xs text-gray-400 ml-2">(Solo lectura y confirmación de recepción)</span>
            </p>
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button wire:click="open_inbox" class="relative p-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm group">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                @if($notifications_count > 0)
                    <div class="absolute -top-2 -right-2 bg-primary text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                        {{ $notifications_count }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <div class="min-h-[400px]">

        @if($current_view === 'dashboard')
            {{-- Filtros de Fecha --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-col md:flex-row items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="fecha_inicio" class="text-sm font-semibold text-slate-700">Desde:</label>
                    <input type="date" id="fecha_inicio" wire:model.live="fecha_inicio" class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <label for="fecha_fin" class="text-sm font-semibold text-slate-700">Hasta:</label>
                    <input type="date" id="fecha_fin" wire:model.live="fecha_fin" class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm">
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-10 text-center border border-gray-200">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-200 mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Bienvenido al Módulo de Correspondencia</h3>
                <p class="text-gray-500 mb-2">Como usuario general, puede consultar su bandeja de entrada y confirmar la recepción de comunicados.</p>
                <p class="text-xs text-gray-400 mb-6 italic">Este rol no tiene permisos para crear ni responder comunicaciones.</p>
                <button wire:click="open_inbox" class="px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-blue-800 shadow transition">
                    Ir a mi Bandeja &rarr;
                </button>
            </div>

        @elseif($current_view === 'inbox')
            @include('livewire.gestion-correspondencia.partials.inbox')

        @elseif($current_view === 'detail' && $active_doc)
            @include('livewire.gestion-correspondencia.partials.detail')

        @elseif($current_view === 'detail_sent' && $active_doc)
            @include('livewire.gestion-correspondencia.partials.detail-sent')
        @endif

    </div>
</div>
