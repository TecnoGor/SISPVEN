<?php

namespace App\Livewire\GenerarTermicaInternacional;

use App\Models\Pais;
use App\Models\Envio;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Servicio;
use App\Models\Municipio;
use App\Models\Parroquia;
use Barryvdh\DomPDF\Facade\PDF;
use Livewire\Attributes\Layout;
use App\Models\ServicioAdicional;
use App\Models\EnvioEncaminamiento;
use App\Models\UsuarioSeguimiento;
use Picqer\Barcode\BarcodeGeneratorPNG;

#[Layout('layouts.app')]
class GenerarTermicaInternacional extends Component
{
    public $servicio_id;
    public $servicio = [];
    public $envio;
    public $fecha;
    public $oficina_or = [];
    public $oficina_dest = [];
    public $estado_of_or = [];
    public $estado_of_dest = [];
    public $ciudad = [];
    public $municipio_dest = [];
    public $parroquia_dest;
    public $codigo_barras;
    public $mostrar_modal = false;
    public $pais;



    public function mount($envio)
    {
        $comprobar = [];

        $this->envio = Envio::where('envio_id', $envio)->first();

        $comprobar = EnvioEncaminamiento::where('envio_id',$this->envio['envio_id'])->latest()->first();
        if($comprobar && $comprobar->estatus_id > 1){
            return redirect()->route('envios.listado-ventas');
        }else{
            $this->fecha = date('Y-m-d - H:i:s');

            $this->servicio_id = $this->envio['servicio_id'];
            $this->servicio = Servicio::where('servicio_id', $this->servicio_id)->first();
            $this->oficina_or = Oficina::where('oficina_id', $this->envio['oficina_id'])->first();
            $this->oficina_dest = Oficina::where('estado_id', $this->envio['estado_dest'])->where('tipo_oficina_id', 4)->first();
            $this->estado_of_or = Estado::where('estado_id', $this->oficina_or['estado_id'])->first();
            $this->pais = Pais::where('pais_id', $this->envio['pais_dest'])->first();
            // dd($this->pais);

            // $this->estado_of_dest = Estado::where('estado_id', $this->envio['estado_dest'])->first();
            // $this->municipio_dest = Municipio::where('municipio_id', $this->envio['municipio_dest'])->first();
            // $this->parroquia_dest = Parroquia::where('parroquia_id', $this->envio['parroquia_dest'])->first();
            // $this->ciudad = Ciudad::where('ciudad_id', $this->envio['ciudad_dest'])->first();


        $this->generateBarcode();
        }
    }

    public function generateBarcode()
    {
        $generator = new BarcodeGeneratorPNG();
        $width = 2;
        $height = 40;
        $this->codigo_barras = base64_encode($generator->getBarcode($this->envio['codigo_envio'], $generator::TYPE_CODE_128, $width, $height));
    }

    public function confirmarTermica()
    {
        $this->mostrar_modal = true;
    }

    public function actualizarEstatus($respuesta)
    {
        if ($respuesta === 'no') {
            $this->mostrar_modal = false;
            return;
        }
        $envio_preparado = EnvioEncaminamiento::where('envio_id',$this->envio['envio_id'])->first();

        if ($envio_preparado) {
            $nuevo_envio_encaminamiento = new EnvioEncaminamiento();

            $nuevo_envio_encaminamiento->envio_id = $envio_preparado->envio_id;
            $nuevo_envio_encaminamiento->saca_id = $envio_preparado->saca_id;
            $nuevo_envio_encaminamiento->usuario_id = $envio_preparado->usuario_id;
            $nuevo_envio_encaminamiento->estatus_id = 2;
            $nuevo_envio_encaminamiento->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó el estatus del envío internacional ({$this->envio['envio_id']}) a 'Preparado' tras generar la térmica",
            ]);

            return redirect()->route('envios.listado-ventas');
        }
    }
    public function render()
    {
        return view('livewire.generar-termica-internacional.generar-termica-internacional');
    }
}
