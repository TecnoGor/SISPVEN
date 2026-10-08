<?php

namespace App\Livewire\Forms\Servicios;

use Livewire\Form;
use App\Models\User;
use App\Models\Servicio;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;
use App\Models\TarifaNacionalRango;
use Illuminate\Support\Facades\Hash;
use App\Models\TarifaInternacionalRango;
use PHPUnit\Framework\Constraint\IsTrue;

class CreateForm extends Form
{
    public $open = false;
    public $nombre;
    public $internacional = false;
    public $CreateForm;

    public function mount()
    {
        $this->CreateForm->open = false;
        $this->CreateForm->nombre = '';
        $this->CreateForm->internacional = '';
    }
    public function create()
    {
        $this->open = true;
    }

    public function store()
    {

        
        // Validar los campos antes de crear el usuario
        $this->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Crear el usuario con los datos ingresados
        $servicio = Servicio::create([
            'nombre' => $this->nombre,
            'activo' => true,
            'nacional' => $this->internacional,
        ]);
        $servicioId = $servicio->servicio_id;
        $nacional = $this->internacional;

        if($nacional == true)
        {
            TarifaNacionalRango::create(['desde' => 1, 'hasta' => 10, 'monto' => 1, 'activo' => true, 'medida_id' => 1, 'servicios_id' => $servicioId]);
        }
        else
        {
            TarifaInternacionalRango::create(['desde' => 1, 'hasta' => 1, 'grupo' => 'A', 'monto' => 1, 'activo' => true, 'medida_id' => 1, 'servicios_id' => $servicioId]);
        }

        $usuario = auth()->user();
        // Registrar seguimiento
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar el ID del usuario recién creado
            'accion' => 'create',
            'descripcion' => "Se ha creado un Serviocio Nacional",
        ]);

        // Resetear los campos
        $this->reset(['name', 'correo', 'clave']);
        $this->open = false;
    }
}

