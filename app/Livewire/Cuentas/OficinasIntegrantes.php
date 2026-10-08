<?php

namespace App\Livewire\Cuentas;

use App\Models\User;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\Cuentas\EditForm;

#[Layout('layouts.app')] 
class OficinasIntegrantes extends Component
{
    public $search = ''; // Campo para la búsqueda en tiempo real
    public EditForm $editForm;
    public $roles = [];

    public function edit(User $usuario)
    {
        $this->resetValidation();
        $this->editForm->edit($usuario);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Usuario editado exitosamente!');
    }

    public function updatedEditFormOficina($oficinaId)
    {
        // Obtener los roles filtrados según la oficina seleccionada
        $this->roles = OficinaPersonal::where('oficina_id', $oficinaId)
            ->pluck('rol_id')
            ->map(function ($rolId) {
                return Role::find($rolId);
            })
            ->filter(); // Eliminar posibles valores nulos
    }

    public function render()
    {
        // Consulta para buscar usuarios en tiempo real
        $usuarios = User::whereNotNull('oficina_id') // Solo usuarios con oficina_id asignada
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->orWhere('name', 'LIKE', '%'. $this->search. '%')// Filtra por nombre
                      ->orWhere('cedula', 'LIKE', '%' . $this->search . '%'); // Filtra por cédula
                });
            })
            ->get();

            $oficinas = Oficina::orderBy('nombre', 'asc')->get();
        return view('livewire.cuentas.oficinas-integrantes', [
            'usuarios' => $usuarios, 
            'oficinas' => $oficinas, 
        ]);
    }
}
