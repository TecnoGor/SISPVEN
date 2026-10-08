<?php

namespace App\Livewire\ParametrosValijas;

use App\Models\Saca;
use App\Models\TipoSaca;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ParametrosValijas extends Component
{
    // Listado
    public $search = '';
    public $mostrarInactivos = false;

    // Modal
    public bool $mostrarModal = false;
    public ?int $tipoSacaIdEditando = null;

    // Form
    public string $nombre = '';
    public string $nombreReferencial = '';
    public bool $certificado = false;
    public bool $cargarPorPeso = false;
    public array $serviciosSeleccionados = [];

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'certificado' => 'boolean',
            'cargarPorPeso' => 'boolean',
            'serviciosSeleccionados' => 'array|min:1',
            'serviciosSeleccionados.*' => 'integer|exists:servicios,servicio_id',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre del tipo de saca es obligatorio.',
        'serviciosSeleccionados.min' => 'Debe seleccionar al menos un servicio.',
        'serviciosSeleccionados.*.exists' => 'Uno de los servicios seleccionados no es válido.',
    ];

    public function abrirModalCrear()
    {
        $this->resetForm();
        $this->mostrarModal = true;
    }

    public function abrirModalEditar($tipoSacaId)
    {
        $tipo = TipoSaca::with('servicios')->find($tipoSacaId);
        if (!$tipo) {
            $this->dispatch('alertError', message: 'Tipo de saca no encontrado.');
            return;
        }

        $this->tipoSacaIdEditando = $tipo->tipo_saca_id;
        $this->nombre = $tipo->nombre ?? '';
        $this->nombreReferencial = $tipo->nombre_referencial ?? '';
        $this->certificado = (bool) $tipo->certificado;
        $this->cargarPorPeso = (bool) $tipo->cargar_por_peso;
        $this->serviciosSeleccionados = $tipo->servicios->pluck('servicio_id')->map(fn($id) => (int) $id)->toArray();
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->tipoSacaIdEditando = null;
        $this->nombre = '';
        $this->nombreReferencial = '';
        $this->certificado = false;
        $this->cargarPorPeso = false;
        $this->serviciosSeleccionados = [];
        $this->resetValidation();
    }

    public function guardar()
    {
        $this->validate();

        // Marcar un tipo como carga por peso deja en un estado incoherente a las
        // valijas de ese tipo que ya tienen envios dentro: el sistema pasaria a
        // asumir que no deberian tenerlos. Se bloquea el cambio en ese caso.
        if ($this->tipoSacaIdEditando && $this->cargarPorPeso) {
            $conEnvios = Saca::where('tipo_saca_id', $this->tipoSacaIdEditando)
                ->whereHas('envios')
                ->exists();

            if ($conEnvios) {
                $this->dispatch('alertError', message: 'No se puede marcar como carga por peso: existen valijas de este tipo con envíos cargados.');
                return;
            }
        }

        $datos = [
            'nombre' => $this->nombre,
            'nombre_referencial' => $this->nombreReferencial ?: $this->nombre,
            'certificado' => $this->certificado,
            'cargar_por_peso' => $this->cargarPorPeso,
            'activo' => true,
        ];

        if ($this->tipoSacaIdEditando) {
            $tipo = TipoSaca::find($this->tipoSacaIdEditando);
            $tipo->update($datos);

            $tipo->servicios()->sync($this->serviciosSeleccionados);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Editó tipo de saca '{$tipo->nombre}' (id={$tipo->tipo_saca_id}). Servicios: [" . implode(',', $this->serviciosSeleccionados) . "]",
            ]);

            $this->dispatch('alertSuccess', message: 'Tipo de saca actualizado.');
        } else {
            $tipo = TipoSaca::create($datos);
            $tipo->servicios()->sync($this->serviciosSeleccionados);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Creó tipo de saca '{$tipo->nombre}' (id={$tipo->tipo_saca_id}). Servicios: [" . implode(',', $this->serviciosSeleccionados) . "]",
            ]);

            $this->dispatch('alertSuccess', message: 'Tipo de saca creado.');
        }

        $this->cerrarModal();
    }

    public function toggleActivo($tipoSacaId)
    {
        $tipo = TipoSaca::find($tipoSacaId);
        if (!$tipo) return;

        $tipo->activo = !$tipo->activo;
        $tipo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => ($tipo->activo ? 'Activó' : 'Desactivó') . " tipo de saca '{$tipo->nombre}' (id={$tipo->tipo_saca_id})",
        ]);

        $this->dispatch('alertSuccess', message: $tipo->activo ? 'Tipo de saca activado.' : 'Tipo de saca desactivado.');
    }

    public function render()
    {
        $query = TipoSaca::with('servicios')->orderBy('tipo_saca_id');

        if (!$this->mostrarInactivos) {
            $query->where('activo', true);
        }

        if (strlen(trim($this->search)) > 0) {
            $query->where('nombre', 'ilike', '%' . trim($this->search) . '%');
        }

        $tiposSaca = $query->get();
        $servicios = Servicio::where('activo', true)->where('es_envio', true)->orderBy('nombre')->get();

        return view('livewire.parametros-valijas.parametros-valijas', [
            'tiposSaca' => $tiposSaca,
            'servicios' => $servicios,
        ]);
    }
}
