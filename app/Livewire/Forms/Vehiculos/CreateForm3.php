<?php

namespace App\Livewire\Forms\Vehiculos;

use Carbon\Carbon;
use Livewire\Form;
use App\Models\UsuarioSeguimiento;
use App\Models\MantenimientoDetalle;
use App\Models\Mantenimiento; // Modelo para Mantenimiento

class CreateForm3 extends Form
{
    public $open = false;        // Controla la apertura del modal
    public $vehiculo_id = '';    // ID del vehículo actual
    public $servicios = [        // Array de servicios dinámicos
        ['servicio_id' => '', 'fecha' => ''],
    ];
    public $descripcion = '';    // Campo de descripción
    public $kilometraje = '';    // Campo de kilometraje
    public $costo_total = '';    // Campo de costo total

    // Método para abrir el modal
    public function create($vehiculo)
    {
        $this->open = true;
        $this->vehiculo_id = $vehiculo->vehiculo_id;
        $this->servicios = [['servicio_id' => '', 'fecha' => '']];  // Inicializa un solo servicio
        $this->descripcion = '';
        $this->kilometraje = '';
        $this->costo_total = '';
    }

    // Método para guardar los datos
    public function store()
    {
        // Valida cada conjunto de campos de servicios

        // Valida la descripción general y los campos fijos
        $this->validate([
            'descripcion' => 'required|string|max:500',
            'kilometraje' => 'required|integer|min:0',
            'costo_total' => 'required|numeric|min:0',
        ]);
    
        // Crear el registro principal de mantenimiento
        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $this->vehiculo_id,
            'fecha' => Carbon::today()->toDateString(), // Fecha actual
            'descripcion' => $this->descripcion,  
            'kilometraje' => $this->kilometraje,
            'costo' => $this->costo_total,
            'created_at' => now(), // Fecha y hora actuales  // Descripción del mantenimiento
        ]);

        // Crear cada servicio relacionado usando el mismo mantenimiento_id
        foreach ($this->servicios as $servicioData) {
            MantenimientoDetalle::create([
                'mantenimiento_id' => $mantenimiento->mantenimiento_id, // Usar el ID del mantenimiento creado
                'servicios_flota_id' => $servicioData['servicio_id'],
                'fecha' => $servicioData['fecha'],
            ]);
        }
    
        // Guardar el seguimiento del usuario
        $usuario = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} registró un mantenimiento para el vehículo {$this->vehiculo_id}.",
        ]);
    
        // Reiniciar el formulario y cerrar el modal
        $this->reset();
        $this->open = false;
    }

    // Opcional: método para agregar más servicios dinámicos en el frontend
    public function addServicio()
    {
        $this->servicios[] = ['servicio_id' => '', 'fecha' => ''];
    }

    // Opcional: método para eliminar servicios dinámicos
    public function removeServicio($index)
    {
        unset($this->servicios[$index]);
        $this->servicios = array_values($this->servicios); // Reindexa el array
    }
}
