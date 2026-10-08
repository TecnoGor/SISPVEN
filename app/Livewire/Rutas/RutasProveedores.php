<?php

namespace App\Livewire\Rutas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProveedorRuta;
use Livewire\Attributes\Layout;
use App\Models\Rutas as ModelsRutas;
use App\Livewire\Forms\RutasProveedor\CreateForm;
use App\Models\RutasModelo;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')] 
class RutasProveedores extends Component
{
    use WithPagination;

    public $proveedor_id; // Proveedor ID recibido
    public $search; // Campo de búsqueda
    public $filter = 'all'; // Filtro por defecto
    public $perPage = 5; // Registros por página
    public $sortBy = 'ruta_id'; // Columna por la que se ordena
    public $sortDir = 'ASC'; // Dirección de ordenamiento
    public CreateForm $createForm;

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->createForm->store($this->proveedor_id);
        $this->dispatch('alertSuccess', message: 'Ruta Asignada exitosamente!');
    }

    public function getUnassignedRutas()
    {
        // Obtener los IDs de rutas que están asignadas al proveedor
        $rutas_asignadas = ProveedorRuta::where('proveedor_id', $this->proveedor_id)
            ->pluck('ruta_id');

            return RutasModelo::whereHas('oficinaDestino', function ($query) {
                $query->where('tipo_oficina_id', 4);
            })->get();
    }

    public function getRutas()
    {
        // Obtener los IDs de rutas asociados al proveedor utilizando Eloquent
        $rutas_id = ProveedorRuta::where('proveedor_id', $this->proveedor_id)
            ->pluck('ruta_id');

        // Obtener las rutas usando los IDs obtenidos y aplicar paginación
        return RutasModelo::whereIn('ruta_id', $rutas_id)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }
    public function desactivar($ruta_id)
    {
        // Elimina la ruta asignada al proveedor
        ProveedorRuta::where('proveedor_id', $this->proveedor_id)
            ->where('ruta_id', $ruta_id)
            ->delete();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'delete',
            'descripcion' => "Se eliminó la ruta ({$ruta_id}) asignada al proveedor ({$this->proveedor_id})",
        ]);

        // Notifica que la ruta fue eliminada
        $this->dispatch('alertSuccess', message: 'Ruta eliminada exitosamente!');

        // Opcional: Actualiza la lista de rutas si es necesario
        $this->getRutas();
    }
    public function render()
    {
        // Obtener todas las rutas que no están asignadas al proveedor
        $allrutas = $this->getUnassignedRutas();

        // Obtener las rutas asignadas al proveedor
        $rutas = $this->getRutas();

        return view('livewire.rutas.rutas-proveedores', [
            'rutas' => $rutas,
            'allrutas' => $allrutas,
        ]);
    }
}
