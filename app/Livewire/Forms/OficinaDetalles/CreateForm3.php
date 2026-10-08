<?php

namespace App\Livewire\Forms\OficinaDetalles;

use Livewire\Form;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Validate;

class CreateForm3 extends Form
{

    public $id_oficina;  
    public $open = false;
    public $roles = [];
    public $maxUsages = [];

    public function create($oficina_id)
    {
        $this->open = true;
        $this->id_oficina = $oficina_id;
    }

    public function store()
    {
        // Filtrar roles seleccionados
        $selectedRoles = array_filter($this->roles, function ($role) {
            return isset($role['selected']) && $role['selected'] === true;
        });
    
        // Verificar si no hay roles seleccionados
        if (empty($selectedRoles)) {
            $this->addError('roles', 'Debe seleccionar al menos un rol.');
            return;
        }
    
        // Validación de los roles seleccionados
        $this->validate([
            'roles.*.maxUso' => 'required|integer|min:1|max:99',
        ], [
            'roles.*.maxUso.required' => 'Debe especificar la cantidad máxima de uso para el rol seleccionado.',
            'roles.*.maxUso.integer' => 'La cantidad máxima debe ser un número entero.',
            'roles.*.maxUso.min' => 'La cantidad máxima debe ser al menos 1.',
            'roles.*.maxUso.max' => 'La cantidad máxima no puede exceder 99.',
        ]);
    
        // Guardar roles seleccionados
        foreach ($selectedRoles as $roleId => $roleData) {
            OficinaPersonal::create([
                'oficina_id' => $this->id_oficina,
                'rol_id' => $roleId,
                'cantidad_max' => $roleData['maxUso'],
            ]);
        }
}

    
}
