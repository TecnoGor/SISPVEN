<?php

namespace App\Livewire\Forms\Viajes;

use Carbon\Carbon;
use Livewire\Form;
use App\Models\Viaje;
use Illuminate\Support\Str;
use App\Models\UsuarioSeguimiento;

class CreateForm3 extends Form
{
    public $open = false;
    public $ruta;
    public $fecha;
    public $vehiculo_id;
    public $trips = []; // Soporta múltiples viajes

    protected $rules = [
        'ruta' => 'required|exists:rutas,ruta_id',
        'fecha' => 'required|date',
        'trips.*.dia_semana' => 'required|integer|between:1,7',
        'trips.*.vehiculo_id' => 'required|exists:vehiculos,vehiculo_id',
    ];

    public function mount($trips = [])
    {
        $this->trips = $trips; // Sincronizamos trips cuando se monte el componente
    }

    public function create()
    {
        $this->resetValidation();
        // Si deseas que el formulario se inicialice con los viajes existentes:
        $this->trips = $this->trips ?: []; // Usamos trips si tiene datos, o un array vacío si no
        $this->open = true;
    }

    // Método para almacenar la información en la base de datos
    public function store()
{
   // Verifica qué datos tiene la propiedad trips antes de pasarla a CreateForm
    $this->validate(); // Realiza la validación definida en $rules
    $usuario = auth()->user();

    // Procesamos cada viaje
    foreach ($this->trips as $trip) {
        // Generamos un código único para cada viaje
        $codigo = Str::upper(Str::random(6));
        // Creamos el registro de Viaje
        Viaje::create([
            'ruta_id' => $this->ruta,
            'codigo' => $codigo,
            'dia_semana_id' => $trip['dia_semana'],
            'fecha_salida' => Carbon::parse($this->fecha)->startOfWeek(), // Convierte la semana en lunes
            'vehiculo_id' => $trip['vehiculo_id'],
            'activo' => true,
        ]);

        // Creamos el registro de seguimiento de usuario
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} creó un viaje con código {$codigo}.",
        ]);
    }

    // Cerramos el modal y reseteamos el formulario
    $this->open = false;
    $this->resetForm();

}

    // Método para agregar un nuevo viaje al array de trips
    public function addAnotherTrip()
    {
        $this->trips[] = [
            'dia_semana' => null,
            'proveedor_id' => null,
            'vehiculo_id' => null,
        ];
    }

    // Método para eliminar un viaje específico del array de trips
    public function removeTrip($index)
    {
        unset($this->trips[$index]);
        $this->trips = array_values($this->trips); // Reindexa el array para evitar huecos
    }

    // Método privado para resetear los datos del formulario
    private function resetForm()
    {
        $this->ruta = null;
        $this->fecha = null;
        $this->trips = []; // Limpiamos los viajes
    }
}
