<?php

namespace App\Livewire\GestionAlianzas;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\EnteAliadoRecaudacion;
use App\Models\TipoAlianza;
use App\Models\CatalogoServicioAlianza;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionAlianzas extends Component
{
    // 'entes' | 'tipos' | 'catalogo'
    public string $tipo = 'entes';

    // Colección de registros actuales (según tipo)
    public $registros = [];

    // Modal / formulario (crear / editar)
    public bool $modal_open = false;
    public $editing_id = null;
    public array $form_registro = [
        'nombre' => '',
        'activo' => true,
    ];

    // Mapeo dinámico tipo => Model
    protected array $map = [
        'entes'   => EnteAliadoRecaudacion::class,
        'tipos'   => TipoAlianza::class,
        'catalogo'=> CatalogoServicioAlianza::class,
    ];

    protected $listeners = [
        'refreshList' => 'cargar',
    ];

    public function mount(): void
    {
        $this->cargar();
    }

    /*Cambia el tipo visible y recarga la lista.*/
    public function cambiarTipo(string $tipo): void
    {
        if (! array_key_exists($tipo, $this->map)) {
            return;
        }

        $this->tipo = $tipo;
        $this->editing_id = null;
        $this->resetForm();
        $this->cargar();
    }

    /*Carga registros desde la tabla asociada al tipo actual.*/
    protected function cargar(): void
    {
        $modelo = $this->map[$this->tipo];
        $this->registros = $modelo::orderBy('nombre')->get();
    }

    /*Alterna activo/inactivo para un registro.*/
    public function toggleActivo(int $id): void
    {
        $modelo = $this->map[$this->tipo];
        $registro = $modelo::findOrFail($id);

        $registro->activo = ! $registro->activo;
        $registro->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus del registro ({$id}) de '{$this->tipo}' a " . ($registro->activo ? 'activo' : 'inactivo'),
        ]);

        $this->cargar();

        $this->dispatch('alertSuccess', message: 'Estado actualizado correctamente');
    }

    /*Abre modal para crear.*/
    public function create(): void
    {
        $this->editing_id = null;
        $this->resetForm();
        $this->modal_open = true;
    }

    /*Abre modal para editar un registro existente.*/
    public function edit(int $id): void
    {
        $modelo = $this->map[$this->tipo];
        $registro = $modelo::findOrFail($id);

        $this->editing_id = $id;
        $this->form_registro['nombre'] = $registro->nombre;
        $this->form_registro['activo'] = (bool) $registro->activo;
        $this->modal_open = true;
    }

    /* Resetea el formulario a valores por defecto.*/
    protected function resetForm(): void
    {
        $this->form_registro = [
            'nombre' => '',
            'activo' => true,
        ];
    }

    protected function rules()
    {
        $modelo = $this->map[$this->tipo];
        $instance = new $modelo;
        $table = $instance->getTable();
        $keyName = $instance->getKeyName();
        // Normaliza el nombre para la cláusula where de la regla unique
        $normalized = '';
        if (isset($this->form_registro['nombre'])) {
            $normalized = mb_strtolower(preg_replace('/\s+/', ' ', trim($this->form_registro['nombre'])));
        }

        return [
            'form_registro.nombre' => [
                'required',
                'string',
                'max:200',
                Rule::unique($table, 'nombre')
                    ->ignore($this->editing_id, $keyName)
                    ->where(function ($query) use ($normalized) {
                        if ($normalized === '') {
                            return;
                        }
                        // Comparación insensible a mayúsculas y espacios
                        $query->whereRaw('LOWER(TRIM(nombre)) = ?', [$normalized]);
                    }),
            ],
            'form_registro.activo' => ['required', 'boolean'],
        ];
    }

    /*Crear o actualizar registro en la tabla activa.*/
    public function save(): void
    {
        if (isset($this->form_registro['nombre'])) {
            $this->form_registro['nombre'] = preg_replace('/\s+/', ' ', trim($this->form_registro['nombre']));
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errorsForName = $e->validator->errors()->get('form_registro.nombre');
            if (! empty($errorsForName)) {
                $this->dispatch('alertSuccess2', message: $errorsForName[0]);
                return;
            }

            $all = $e->validator->errors()->all();
            $msg = !empty($all) ? $all[0] : 'Error de validación';
            $this->dispatch('alertSuccess2', message: $msg);
            return;
        }

        $modelo = $this->map[$this->tipo];

        if ($this->editing_id) {
            $registro = $modelo::findOrFail($this->editing_id);
            $registro->update([
                'nombre' => $this->form_registro['nombre'],
                'activo' => $this->form_registro['activo'],
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó el registro ({$this->editing_id}) de '{$this->tipo}'",
            ]);

            $this->dispatch('alertSuccess', message: 'Registro actualizado correctamente');
        } else {
            $nuevo_registro = $modelo::create([
                'nombre' => $this->form_registro['nombre'],
                'activo' => $this->form_registro['activo'],
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó un registro de '{$this->tipo}' con nombre '{$this->form_registro['nombre']}'",
            ]);

            $this->dispatch('alertSuccess', message: 'Registro creado correctamente');
        }

        $this->modal_open = false;
        $this->resetForm();
        $this->cargar();
    }

    public function render()
    {
        return view('livewire.gestion-alianzas.gestion-alianzas');
    }
}