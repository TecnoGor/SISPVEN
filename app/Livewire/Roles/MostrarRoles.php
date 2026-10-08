<?php

namespace App\Livewire\Roles;
use Livewire\Component;

use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\Roles\EditForm;
use App\Livewire\Forms\Roles\CreateForm;
use App\Livewire\Forms\Roles\DeleteForm;
use App\Models\TipoOficina;

#[Layout('layouts.app')]
class MostrarRoles extends Component
{

    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $sortBy = 'id';
    public $sortDir = 'ASC';
    public CreateForm $createForm;
    public EditForm $editForm;
    public DeleteForm $deleteForm;

    public function edit(Role $role)
    {
        $this->resetValidation();
        $this->editForm->edit($role);
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Rol creado exitosamente!');
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Rol editado exitosamente!');
    }

    #[On('delete')]
    public function delete(Role $role)
    {
        $this->deleteForm->destroy($role);
        $this->resetPage();
        $this->dispatch('alertSuccess', message: 'Rol eliminado exitosamente!');
    }


    public function render()
    {
        $roles = Role::where('name', 'NOT LIKE', '%Correspondencia%')
        ->when($this->search, function($query) {
            $query->where('name', 'LIKE', '%'. $this->search.'%');
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);

        $oficinas = TipoOficina::whereNotIn('tipo_oficina_id', [6,7])->get();

        return view('livewire.roles.mostrar-roles', compact('roles', 'oficinas'));
    }
}
