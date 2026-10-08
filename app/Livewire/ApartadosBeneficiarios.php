<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Documento;
use App\Models\RegistroApartado;
use App\Models\ApartadoPostalBeneficiario;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ApartadosBeneficiarios extends Component
{
    use WithPagination;

    // --- Búsqueda / selección del apartado (titular) ---
    public $search;
    public $registro_selec = null;   // RegistroApartado seleccionado (con beneficiarios cargados)

    // --- Formulario de beneficiario ---
    public $mostrar_form = false;
    public $beneficiario_id = null;  // null = creando, con valor = editando
    public $nombre;
    public $apellido;
    public $tipo_documento;
    public $documento;
    public $correo;
    public $telefono;

    public $documentos = [];

    protected function rules()
    {
        return [
            'nombre'         => 'required|max:25|regex:/^[A-Za-z ]+$/',
            'apellido'       => 'required|max:25|regex:/^[A-Za-z ]+$/',
            'tipo_documento' => 'required',
            'documento'      => 'required|digits_between:6,8',
            'correo'         => 'nullable|email',
            'telefono'       => 'nullable|digits:11',
        ];
    }

    protected $messages = [
        'nombre.required'         => 'El nombre es obligatorio',
        'nombre.max'             => 'Máximo 25 caracteres',
        'nombre.regex'           => 'Solo se permiten letras',
        'apellido.required'       => 'El apellido es obligatorio',
        'apellido.max'           => 'Máximo 25 caracteres',
        'apellido.regex'         => 'Solo se permiten letras',
        'tipo_documento.required' => 'El tipo de documento es obligatorio',
        'documento.required'      => 'El documento es obligatorio',
        'documento.digits_between' => 'El documento debe tener entre 6 y 8 dígitos',
        'correo.email'            => 'Debe ser un correo válido',
        'telefono.digits'         => 'El teléfono debe tener 11 dígitos',
    ];

    public function mount()
    {
        $this->documentos = Documento::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Autocompletar datos si el documento ya existe como cliente.
    public function updatedDocumento()
    {
        $this->documento = preg_replace('/[^\d]/', '', $this->documento);

        if (empty($this->documento)) {
            return;
        }

        $cliente = Cliente::where('numero_documento', $this->documento)->first();
        if ($cliente) {
            $this->nombre = $cliente->nombre;
            $this->apellido = $cliente->apellido;
            $this->tipo_documento = $cliente->tipo_documento;
            $this->telefono = $cliente->telefono;
            $this->correo = $cliente->correo;
        }
    }

    public function seleccionar($registroId)
    {
        $this->registro_selec = RegistroApartado::with(['beneficiarios', 'apartado'])
            ->find($registroId);
        $this->resetForm();
    }

    public function volver()
    {
        $this->registro_selec = null;
        $this->resetForm();
    }

    public function nuevoBeneficiario()
    {
        $this->resetForm();
        $this->mostrar_form = true;
    }

    public function editar($id)
    {
        $b = ApartadoPostalBeneficiario::findOrFail($id);

        // Solo beneficiarios del apartado actualmente seleccionado.
        if (! $this->registro_selec || $b->registro_apartado_id != $this->registro_selec->registro_apartado_id) {
            return;
        }

        $this->beneficiario_id = $b->apartado_postal_beneficiario_id;
        $this->nombre = $b->nombre;
        $this->apellido = $b->apellido;
        $this->tipo_documento = $b->tipo_documento;
        $this->documento = $b->documento;
        $this->correo = $b->correo;
        $this->telefono = $b->telefono;
        $this->mostrar_form = true;
    }

    public function guardar()
    {
        if (! $this->registro_selec) {
            return;
        }

        $this->validate();

        ApartadoPostalBeneficiario::updateOrCreate(
            ['apartado_postal_beneficiario_id' => $this->beneficiario_id],
            [
                'registro_apartado_id' => $this->registro_selec->registro_apartado_id,
                'nombre'         => $this->nombre,
                'apellido'       => $this->apellido,
                'tipo_documento' => $this->tipo_documento,
                'documento'      => $this->documento,
                'correo'         => $this->correo,
                'telefono'       => $this->telefono,
                'activo'         => true,
            ]
        );

        $this->registro_selec->load('beneficiarios');
        $this->resetForm();
        $this->dispatch('alertSuccess', message: 'Beneficiario guardado exitosamente');
    }

    public function toggleActivo($id)
    {
        $b = ApartadoPostalBeneficiario::findOrFail($id);

        if (! $this->registro_selec || $b->registro_apartado_id != $this->registro_selec->registro_apartado_id) {
            return;
        }

        $b->update(['activo' => ! $b->activo]);
        $this->registro_selec->load('beneficiarios');
    }

    private function resetForm()
    {
        $this->reset([
            'beneficiario_id', 'nombre', 'apellido', 'tipo_documento',
            'documento', 'correo', 'telefono', 'mostrar_form',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $registros = null;

        if (! $this->registro_selec) {
            $registros = RegistroApartado::where('activo', true)
                ->where('oficina_id', auth()->user()->oficina_id)
                ->when($this->search, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('documento', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->with('apartado')
                ->withCount('beneficiarios')
                ->orderByDesc('registro_apartado_id')
                ->paginate(10);
        }

        return view('livewire.apartados-beneficiarios', [
            'registros' => $registros,
        ]);
    }
}