<?php

namespace App\Livewire\Devoluciones;
use App\Models\Envio;
use Livewire\Component;
use App\Models\Incidencia;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEstatus;
use App\Models\EnvioIncidencia;
use Livewire\Attributes\Layout;
use App\Models\IncidenciaDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\EnvioEncaminamiento;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class Devoluciones extends Component
{
    public $estatus;
    public $usuario;
    public $comentario;
    public $caracteres_restantes;
    public $codigo_envio;
    public $envio = [];
    public $encaminamiento;
    public $motivo;
    public $incidencias;
    public $incidencias_sel = [];


    protected $rules = [
        'codigo_envio' => 'required',
        'motivo' => 'required',
        'incidencias_sel' => 'required_if:motivo,1',
        'comentario' => 'required|max:350',
    ];

    protected $messages = [
        'codigo_envio.required' => 'El codigo de envio es requrido',
        'motivo.required' => 'el motivo es requerido',
        'comentario.required' => 'El comentario es obligatorio',
        'comentario.max' => 'El maximo de caracteres es de 350',
        'incidencias_sel.required_if' => 'Debe seleccionarse alguna incidencia',
    ];


    public function mount()
    {
        $this->usuario = auth()->user();
    }


    public function updatedCodigoEnvio($value)
    {
        if(empty($value)){
            $this->envio = [];
            return;
        }else{
            $this->envio = Envio::where('codigo_envio', $value)->first();
            if(!$this->envio){
                $this->envio = [];
                return;
            }else{


            $this->estatus = EnvioEncaminamiento::where('envio_id', $this->envio['envio_id'])->latest()->pluck('estatus_id')->first();
            $this->encaminamiento = EnvioEstatus::where('envios_estatus_id', $this->estatus)->pluck('estatus')->first();
            }
        }
    }


    public function updatedMotivo()
    {
        if($this->motivo == 1){
            $this->incidencias = Incidencia::all();
        }else{
            $this->incidencias_sel = [];
        }
    }


    public function updatedComentario($value)
    {
        $max = 400;

        $cantidad_caracteres = strlen($value);

        $this->caracteres_restantes = max(0, $max - $cantidad_caracteres);
    }


    public function submit()
    {
        $this->validate();

        DB::beginTransaction();

        try {
        if($this->motivo == 1 || $this->motivo == 2){
            $envio_incidencia = EnvioIncidencia::Create([
                'envio_id' => $this->envio['envio_id'],
                'detalle' => $this->comentario,
                'usuario_id' => $this->usuario['id'],
                'oficina_id' => $this->usuario['oficina_id'],
            ]);

            if($this->motivo == 1){
                foreach($this->incidencias_sel as $incidencias)
                IncidenciaDetalle::create([
                    'envio_incidencia_id' => $envio_incidencia->envio_incidencia_id,
                    'incidencia_id' => $incidencias,
                ]);

            }elseif($this->motivo == 2){
                $envio = Envio::find($this->envio['envio_id']);

                if($envio && $envio->devolucion == false){
                    $envio->update(['devolucion' => true]);

                    // Estatus de devolución: se resuelve por nombre para no depender de un ID fijo.
                    $estatusDevolucion = EnvioEstatus::firstOrCreate(
                        ['estatus' => 'ENVÍO EN DEVOLUCIÓN'],
                        ['created_at' => now()]
                    );

                    EnvioEncaminamiento::create([
                        'envio_id' => $this->envio['envio_id'],
                        'usuario_id' => $this->usuario['id'],
                        'oficina_id' => $this->usuario['oficina_id'],
                        'estatus_id' => $estatusDevolucion->envios_estatus_id,
                        'devolucion' => true,
                    ]);
                }else{
                    $this->dispatch('alertSuccess2', message: 'Este envio ya se encuentra en devolucion');
                    return;
                }
            }
        }
        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => $this->motivo == 2 ? 'update' : 'create',
            'descripcion' => ($this->motivo == 2 ? "Se registró devolución" : "Se registró incidencia") . " del envío ({$this->envio['envio_id']})",
        ]);

        DB::commit();
        $this->dispatch('alertSuccess', message: 'Aviso de Incidencia/Devolucion generado correctamente');
        $this->dispatch('recargar');

    } catch (\Exception $e) {
        // dd($e);
        DB::rollback();
        $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion de la solicitud, verifique e intente de nuevo');
         // Si ocurre un error, revertimos todos los cambios
    }
    }

    public function render()
    {
        return view('livewire.devoluciones.devoluciones');
    }
}
