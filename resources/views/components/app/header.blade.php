{{--
    Header fijo (sticky) con efecto glass + compactado al hacer scroll.
    - Se mantiene pegado arriba del area de contenido para que el hamburguesa y el
      boton de usuario esten siempre a mano (el usuario no tiene que subir).
    - Al hacer scroll: fondo semitransparente con desenfoque (glass), sombra suave
      y un ligero compactado. Arriba del todo se ve limpio y sin sombra.
    El scroll se detecta en el contenedor de contenido (overflow-y-auto), no en el body.
--}}
<div
    x-data="{
        scrolled: false,
        scroller: null,
        onScroll() { this.scrolled = (this.scroller?.scrollTop ?? 0) > 8; },
        init() {
            this.scroller = this.$el.closest('.overflow-y-auto');
            if (this.scroller) {
                this.onScroll();
                this.scroller.addEventListener('scroll', () => this.onScroll(), { passive: true });
            }
        }
    }"
    class="sticky top-0 z-30 transition-all duration-300 ease-out"
    :class="scrolled
        ? 'bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm border-b border-gray-200/60 dark:border-gray-700/40'
        : 'bg-transparent border-b border-transparent'"
>
    <div
        class="flex items-center justify-end space-x-3 w-full px-4 transition-all duration-300 ease-out"
        :class="scrolled ? 'py-1.5' : 'py-2 mt-2'"
    >
        <!-- User button -->
        @livewire('centro-notificaciones')

        <!-- Divider -->
        <hr class="w-px h-6 bg-gray-200 dark:bg-gray-700/60 border-none" />

        <!-- Header: Right side -->
        <div class="flex">
            <!-- Hamburger button -->
            <button
                class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 lg:hidden"
                @click.stop="sidebarOpen = !sidebarOpen"
                aria-controls="sidebar"
                :aria-expanded="sidebarOpen"
            >
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="5" width="16" height="2" />
                    <rect x="4" y="11" width="16" height="2" />
                    <rect x="4" y="17" width="16" height="2" />
                </svg>
            </button>
        </div>
    </div>
</div>
