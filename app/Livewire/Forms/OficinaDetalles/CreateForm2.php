<?php

namespace App\Livewire\Forms\OficinaDetalles;

use Livewire\Form;
use Livewire\WithFileUploads;  // Importa el trait para manejar archivos
use App\Models\Vehiculo;
use App\Models\OficinaVehiculo;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\Storage;  // Necesario para la gestión de archivos

class CreateForm2 extends Form
{
    use WithFileUploads;  // Necesario para la carga de archivos

    public $id_oficina;  
    public $open = false;
    public $placa;
    public $color;
    public $año;
    public $poliza;
    public $fecha_vecimiento;
    public $capacidad;
    public $marca;
    public $modelo;
    public $imagen;  // Variable para la imagen
    public $tipo; 
    public $kilometraje_actual;

    public function create($oficina_id)
    {
        $this->open = true;
        $this->id_oficina = $oficina_id;
        $this->kilometraje_actual = null;
    }

    protected $messages = [
        'placa.required' => 'La placa es obligatoria.',
        'placa.unique' => 'La placa ya está registrada en el sistema.',
    ];

    public function store()
    {
        $this->validate([
            'placa' => [
                'required',
                'string',
                'unique:vehiculos,placa', // Asegura que la placa sea única
            ],
            'color' => 'required|string',
            'año' => [
                'required',
                'numeric',
                'min:1900', // Año mínimo válido
                'not_in:0', // No permite el valor 0
            ],
            'fecha_vecimiento' => [
                'nullable',
                'date',
                'after_or_equal:' . now()->format('Y-m-d'),
            ],


            'capacidad' => [
                'required',
                'numeric',
                'min:1', // Capacidad mínima
                'not_in:0', // No permite el valor 0
            ],
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'poliza' => [
                'nullable',
                'string',
                'unique:vehiculos,num_poliza', // Asegura que la póliza sea única
            ],


            'imagen' => 'nullable|image|max:2048',
            'kilometraje_actual' => 'nullable|integer|min:0',
            
            'tipo' => [
                'required'
            ],
        ], [
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.integer' => 'El kilometraje debe ser un número entero.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
        ]);
    
        // Verifica si se subió una imagen y guárdala
        $imagenPath = null; // Inicializa la variable para la ruta de la imagen
        if ($this->imagen) {
            $imagenPath = $this->imagen->store('vehiculos', 'public'); // Guarda la imagen en la carpeta 'vehiculos' del disco 'public'
        }
        // Crear el registro en la base de datos
        $vehiculo = Vehiculo::create([
            'oficina_id' => $this->id_oficina,
            'tipo_vehiculo_id' => $this->tipo,
            'placa' => $this->placa,
            'color' => $this->color,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'año' => $this->año,
            'num_poliza' => $this->poliza,
            'fecha_vencimiento' => $this->fecha_vecimiento,
            'capacidad_carga' => $this->capacidad,
            'kilometraje_actual' => $this->kilometraje_actual,
            'activo' => true,
            'imagen' => $imagenPath, // Guarda la ruta de la imagen en la base de datos
        ]);
    
        // Crear registro de seguimiento
        $usuario = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} creó un vehículo para la oficina: {$usuario->oficina_id}.",
        ]);

        OficinaVehiculo::create([
            'oficina_id' => $this->id_oficina,
            'vehiculo_id' => $vehiculo->vehiculo_id,
            'activo' => true,
        ]);
    
        // Resetea el formulario y cierra el modal
        $this->reset();
        $this->open = false;
    }
    
}
