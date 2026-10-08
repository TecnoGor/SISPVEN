@props([
    'align' => 'right'
])

@php
    $telegramasCount = Auth::user()->telegramasNuevosCount();
@endphp

<div class="relative inline-flex" x-data="{ open: false }">
    <button
        class="inline-flex justify-center items-center group"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"                        
    >
        <div class="relative">
            <img class="w-8 h-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" width="32" height="32" alt="{{ Auth::user()->name }}" />
            @if($telegramasCount > 0)
                <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-gray-800 bg-red-500"></span>
            @endif
        </div>
        <div class="flex items-center truncate">
            <span class="truncate ml-2 text-sm font-medium text-primary group-hover:text-white">{{ Auth::user()->name }}</span>
            @if($telegramasCount > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">
                    {{ $telegramasCount }}
                </span>
            @endif
            <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-800" viewBox="0 0 12 12">
                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
            </svg>
        </div>
    </button>
    <div
        class="origin-top-right z-10 absolute top-full min-w-44 bg-gray-800 border border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{$align === 'right' ? 'right-0' : 'left-0'}}"                
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak                    
    >
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-gray-700/60">
            <div class="font-medium text-white">{{ Auth::user()->name }}</div>
            @php
            $oficina = Auth::user()->Oficina; // Asegúrate de tener la relación definida en el modelo User
        @endphp
        
        @if ($oficina)
            <div class="text-xs text-gray-400 italic">{{ $oficina->nombre }}</div>
        @endif
        </div>
        <ul>
            @if($telegramasCount > 0)
                <li class="border-b border-gray-700/60 pb-1.5 mb-1.5">
                    <a href="{{ route('confirmacion-telegrama') }}" class="font-medium text-xs text-red-400 hover:text-red-300 flex items-center py-1 px-3 transition-colors">
                        <svg class="w-4 h-4 mr-2 fill-current text-red-400" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm4 0H9v2h2V8zm4 0h-2v2h2V8z" />
                        </svg>
                        {{ $telegramasCount }} telegramas nuevos
                    </a>
                </li>
            @endif
            <li>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <a class="font-medium text-sm text-white hover:text-white dark:hover:text-white flex items-center py-1 px-3"
                        href="{{ route('logout') }}"
                        @click.prevent="$root.submit();"
                        @focus="open = true"
                        @focusout="open = false"
                    >
                        {{ __('Cerrar Sesion') }}
                    </a>
                </form>                                
            </li>
        </ul>                
    </div>
</div>