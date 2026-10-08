# Separación de la notificación en un ícono de campana (PENDIENTE DE LUZ VERDE)

> **Estado:** documentado y **revertido**. Todo el sistema quedó EXACTAMENTE como estaba
> antes de esta tarea. Este documento sirve para **rehacer** el cambio cuando se autorice.
>
> **Objetivo del cambio:** sacar la notificación de telegrama del botón de usuario
> (foto + nombre) y ponerla en un **ícono de campana aparte**, a la izquierda del botón
> de usuario. El botón de usuario debe pasar a usar el `dropdown-profile` original (limpio).

---

## Contexto (estado ANTES del cambio)

- El header (`resources/views/components/app/header.blade.php`) usaba
  `@livewire('telegramas-notificacion')` como botón de usuario.
- Ese componente (`telegramas-notificacion`) NO era solo la notificación: era el botón
  de usuario completo (foto + nombre + menú con oficina, "telegramas nuevos" y
  "Cerrar Sesión"), con la notificación de telegrama mezclada dentro.
- Existía además `resources/views/components/dropdown-profile.blade.php`, un componente
  Blade anónimo casi idéntico pero **huérfano** (no se usaba en el header). También tenía
  código de telegrama dentro.
- El contador lo provee `Auth::user()->telegramasNuevosCount()` (método en `app/Models/User.php`),
  que consulta `TelegramaRecibido` (telegramas de la oficina del usuario aún no confirmados).
- Auto-refresco: `wire:poll.30s` + evento Livewire `#[On('telegrama-confirmado')]`.

---

## PASOS PARA REHACER EL CAMBIO

### 1. Crear el componente Livewire de la campana

**Archivo nuevo:** `app/Livewire/NotificacionesMenu.php`

```php
<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Campanita de notificaciones del header.
 * Muestra un icono aparte (a la izquierda del boton de usuario) con la
 * cuenta de notificaciones. Por ahora agrupa la notificacion de telegramas.
 * Preparado para sumar mas tipos de notificacion en el futuro.
 */
class NotificacionesMenu extends Component
{
    #[On('telegrama-confirmado')]
    public function actualizarContador()
    {
        // El evento fuerza el re-render; el contador se recalcula en render().
    }

    public function render()
    {
        $telegramasCount = Auth::user()->telegramasNuevosCount();

        return view('livewire.notificaciones-menu', [
            'telegramasCount' => $telegramasCount,
            'totalCount'      => $telegramasCount, // suma de todas las notificaciones
        ]);
    }
}
```

### 2. Crear la vista de la campana

**Archivo nuevo:** `resources/views/livewire/notificaciones-menu.blade.php`

```blade
<div wire:poll.30s class="relative inline-flex" x-data="{ open: false }">
    {{-- Boton campanita --}}
    <button
        class="relative inline-flex justify-center items-center w-9 h-9 rounded-full hover:bg-gray-200/60 dark:hover:bg-gray-700/60 transition-colors group"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
        title="Notificaciones"
    >
        <span class="sr-only">Notificaciones</span>
        {{-- Icono de campana --}}
        <svg class="w-5 h-5 fill-current text-gray-500 group-hover:text-gray-700 dark:text-gray-400" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-5-1.6-1.6V10a5.4 5.4 0 0 0-4.15-5.27V4a1.25 1.25 0 0 0-2.5 0v.73A5.4 5.4 0 0 0 6.6 10v5.4L5 17a.75.75 0 0 0 .53 1.28h12.94A.75.75 0 0 0 19 17z" />
        </svg>

        {{-- Badge con el numero de notificaciones --}}
        @if($totalCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 inline-flex items-center justify-center rounded-full text-[10px] font-bold bg-red-500 text-white ring-2 ring-white dark:ring-gray-900">
                {{ $totalCount > 99 ? '99+' : $totalCount }}
            </span>
        @endif
    </button>

    {{-- Menu desplegable de notificaciones --}}
    <div
        class="origin-top-right z-20 absolute top-full right-0 min-w-[260px] bg-gray-800 border border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-2"
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
        {{-- Encabezado --}}
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-gray-700/60">
            <div class="font-medium text-white text-sm">Notificaciones</div>
        </div>

        <ul>
            {{-- Notificacion: Telegramas nuevos --}}
            @if($telegramasCount > 0)
                <li class="border-b border-gray-700/60 last:border-0">
                    <a href="{{ route('confirmacion-telegrama') }}"
                       class="flex items-start gap-2 py-2 px-3 hover:bg-gray-700/50 transition-colors group">
                        <svg class="w-5 h-5 mt-0.5 fill-current text-red-400 shrink-0" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm4 0H9v2h2V8zm4 0h-2v2h2V8z" />
                        </svg>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white leading-tight">{{ $telegramasCount }} telegramas nuevos</p>
                            <p class="text-xs text-gray-400 mt-0.5">Toca para confirmar su recepción</p>
                        </div>
                    </a>
                </li>
            @endif

            {{-- Estado vacio --}}
            @if($totalCount === 0)
                <li class="py-6 px-3 text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 fill-current text-gray-600" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-5-1.6-1.6V10a5.4 5.4 0 0 0-4.15-5.27V4a1.25 1.25 0 0 0-2.5 0v.73A5.4 5.4 0 0 0 6.6 10v5.4L5 17a.75.75 0 0 0 .53 1.28h12.94A.75.75 0 0 0 19 17z" />
                    </svg>
                    <p class="text-xs text-gray-400">No tienes notificaciones nuevas</p>
                </li>
            @endif
        </ul>
    </div>
</div>
```

### 3. Modificar el header

**Archivo:** `resources/views/components/app/header.blade.php`

Reemplazar el bloque final:

```blade
    <!-- User button -->
    @livewire('telegramas-notificacion')
```

por:

```blade
    <!-- Notifications button -->
    @livewire('notificaciones-menu')

    <!-- User button -->
    <x-dropdown-profile />
```

### 4. Limpiar `dropdown-profile.blade.php` (quitar lo de telegrama)

**Archivo:** `resources/views/components/dropdown-profile.blade.php`

Quitar estas 3 cosas (todo el codigo de telegrama):

1. El bloque `@php` del contador, al inicio:
```blade
@php
    $telegramasCount = Auth::user()->telegramasNuevosCount();
@endphp
```

2. Los dos badges de telegrama (el `@if($telegramasCount > 0)` con el puntito sobre la
   foto, y el `@if($telegramasCount > 0)` con la burbuja del numero junto al nombre).

3. El `<li>` de "telegramas nuevos" dentro del `<ul>` del menu (todo el
   `@if($telegramasCount > 0) <li> ... </li> @endif` que enlaza a `confirmacion-telegrama`).

Debe quedar: foto + nombre + oficina + "Cerrar Sesion". Sin nada de telegrama.

### 5. Eliminar el componente viejo (ya no se usa)

```
rm resources/views/livewire/telegramas-notificacion.blade.php
rm app/Livewire/TelegramasNotificacion.php
```

### 6. Verificar

- Que NO queden referencias: `grep -rn "telegramas-notificacion\|TelegramasNotificacion" resources/ app/ routes/ config/`
- `php artisan view:clear`
- Probar: campana a la izquierda del nombre, con badge y menu; boton de usuario abre
  oficina + Cerrar Sesion, sin telegrama.

---

## Notas

- El metodo `telegramasNuevosCount()` en `app/Models/User.php` NO se toca: lo reutiliza la campana.
- Livewire resuelve `notificaciones-menu` por convencion (clase `App\Livewire\NotificacionesMenu`),
  igual que el resto; no requiere registro manual.
- `dropdown-profile` es un componente Blade anonimo: se invoca con `<x-dropdown-profile />`.
