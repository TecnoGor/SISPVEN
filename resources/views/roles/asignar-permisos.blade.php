@section('titulo')
    Editar Permisos
@endsection

<x-app-layout>
   
        <h2 class="font-semibold text-xl text-primary leading-tight flex gap-3 ml-10">
            <x-return-link :href="route('roles-mostrar')" wire:navigate.hover/>
            Asignar permisos al rol
        </h2>
    

    <div class="py-12 max-w-[90%] mx-auto"> 
        <p class="text-2xl mb-3 font-bold text-primary">Rol: <span class="font-medium text-primary">{{ $role->name }}</span></p>

        <div class="flex flex-col md:flex-row gap-4 justify-between ml-4">
           
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg w-full">
                    <livewire:permisorol.asignar-permiso-rol :role="$role" />
                </div>
        

           
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg w-full mr-4">
                    <livewire:permisorol.quitar-permiso-rol :role="$role" />
                </div>
        
        </div>
    </div>
</x-app-layout>