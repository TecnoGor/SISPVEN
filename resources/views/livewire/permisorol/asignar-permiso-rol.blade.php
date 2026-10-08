<div>
    <h3 class="text-xl font-bold mb-3">Asignar <span class="font-medium text-sm">({{ $permisos->count() }} @choice('Permiso Asignable|Permisos Asignables', $permisos->count()))</span></h3>
    <x-text-input wire:model.live="search" class="block my-3 w-full" type="text" placeholder="Crear Usuarios, Editar Roles, Asignar Permisos..." />
    <div class="max-h-96 overflow-y-scroll scroll-smooth">
        @forelse ($permisos as $permiso)
            <div class="p-3 border-b border-gray-200 flex flex-col md:flex-row md:justify-between gap-2">
                <p>{{ $permiso->name }}</p>
                <x-primary-button wire:loading.attr="disabled" class="flex gap-2" wire:click="$dispatch('mostrarAsignar', { 'role_id': {{ $role->id }}, 'permiso_id': {{ $permiso->id }} } )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Añadir

                    <x-loading-button/>
                </x-primary-button>
            </div>
        @empty
            <p class="text-center text-xl font-bold">No existen permisos</p>
        @endforelse
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @script
    <script>
        Livewire.on('mostrarAsignar', data => {
            let role_id = data.role_id;
            let permiso_id = data.permiso_id;

            Swal.fire({
            title: "Asignar Permiso?",
            text: "Está seguro de realizar esta acción?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4f46e5",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, asignar",
            cancelButtonText: "Cancelar"
            })
            .then((result) => {
                if (result.isConfirmed) {
                    // eliminar el permiso al rol
                    Livewire.dispatch('asignarPermisoRol', {role_id: role_id, permiso_id: permiso_id});
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