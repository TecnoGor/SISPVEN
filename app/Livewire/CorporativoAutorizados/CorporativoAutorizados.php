<?php

namespace App\Livewire\CorporativoAutorizados;

use Livewire\Component;

use App\Models\Documento;
use Livewire\WithPagination;
use App\Rules\CodigosTelefono;
use Livewire\Attributes\Layout;
use App\Models\ClienteCorporativo;
use Illuminate\Support\Facades\DB;
use App\Models\ClienteCorporativoAutorizado;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class CorporativoAutorizados extends Component
{
    use WithPagination;

    public $cliente, $nombre, $tipo_documento, $documento, $correo, $telefono, $cargo, $cliente_sel;

    public $search = '';
    public $perPage = 5;
    public $clientes = [];
    public $documentos = [];
    public $modal_open = false;
    public $estatus_aut = false;

    public function rules()
    {
        return [
            'nombre' => 'required|regex:/^[A-Za-z ]+$/',
            'cargo' => 'required|min:3|max:25',
            'tipo_documento' => 'required',
            'documento' => 'required|digits_between:7,10',
            'telefono' => ['required', new CodigosTelefono],
            'correo' => 'required_if:mostrar_contrato, false|email',
            'cliente_sel' => 'required',
        ];
    }

    protected $messages = [
        'tipo_documento.required' => 'El campo es requerido',
        'documento.required' => 'El campo es requrido',
        'documento.digits_between' => 'El valor debe ser de tipo numerico y tener entre 7 y 10 digitos',
        'nombre.regex' => 'Solo caracteres alfabeticos',
        'correo.required' => 'El campo es requerido',
        'correo.email' => 'Debe estar en un formato valido para correo',
        'telefono.required' => 'El campo es obligatorio',
    ];

    public function mount()
    {
        $this->documentos = Documento::whereIn('tipo', ['V', 'E'])->get();
        $this->clientes = ClienteCorporativo::all();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCliente()
    {
        $this->resetPage();
    }

    public function updatedEstatusAut()
    {
        $this->resetPage();
    }

    public function modalOpen()
    {
        $this->modal_open = true;
    }

    public function modalClose()
    {
        $this->modal_open = false;
    }

    public function inoperativo($aut_id)
    {
        $persona = ClienteCorporativoAutorizado::find($aut_id);

        if ($persona) {
            $persona->activo = false;
            $persona->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se desactivó al autorizado corporativo ({$aut_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Autorizado desactivado');
        }
    }

    public function operativo($aut_id)
    {
        $persona = ClienteCorporativoAutorizado::find($aut_id);

        if ($persona) {
            $persona->activo = true;
            $persona->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se activó al autorizado corporativo ({$aut_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Autorizado activado');
        } else {
            return;
        }
    }

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $nuevo_autorizado = ClienteCorporativoAutorizado::create([
                'cliente_corporativo_id' => $this->cliente_sel,
                'activo' => true,
                'nombre' => $this->nombre,
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'cargo' => $this->cargo,
                'correo' => $this->correo,
                'telefono' => $this->telefono,
                'created_at' => now(),
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró autorizado corporativo ({$nuevo_autorizado->getKey()}) para el cliente ({$this->cliente_sel})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Registro Creado exitosamente!');
            $this->dispatch('recargar');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del registro, verifique los datos e intente de nuevo');
            return;
            // Si ocurre un error, revertimos todos los cambios
        }
    }

    public function render()
    {
        $autorizados = [];

        if ($this->cliente) {
            $autorizados = ClienteCorporativoAutorizado::where('cliente_corporativo_id', $this->cliente)
                ->where('activo', $this->estatus_aut)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('correo', 'like', '%' . $this->search . '%')
                            ->orWhere('documento', 'like', '%' . $this->search . '%')
                            ->orWhere('cargo', 'like', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->perPage);
        }

        return view('livewire.corporativo-autorizados.corporativo-autorizados', ['autorizados' => $autorizados]);
    }
}
