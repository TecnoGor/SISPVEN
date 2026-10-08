<?php

namespace App\Livewire\Forms\Almacen;

use App\Http\Controllers\Api\encaminamientoEnvioController;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\RegistroEntrega;
use App\Models\TarifaNacionalConcepto;
use App\Models\TipoPago;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateForm extends Form
{
    public $open = false;
    public $envio_id = '';
    public $envio_almacen;
    public $codigo;
    public $cedula;
    public $autorizado = false;
    public $aviso = false;
    public $aviso_coste;
    public $costo_dias_almacenado;
    public $dias_almacenado;
    public $costoTotal;
    public $nombre;
    public $monto;
    public $monto_pagado;
    public $tipo_pago_id;
    public $remitente;
    public $nombre_remitente;
    public $apellido_remitente;
    public $telefono_remitente;
    public $nombre_destinatario;
    public $apellido_destinatario;
    public $telefono_destinatario;
    public $destinatario;
    public $peso;
    public $oficina;
    public $correo_destinatario;
    public $correo_remitente;
    public $pagos = [];

    public function create(Envio $envio)
    {
        $this->open = true;
        $this->envio_id = $envio->envio_id;
        $this->codigo = $envio->codigo_envio;
        $this->remitente = $envio->documento_rem;
        $this->nombre_remitente = $envio->nombre_rem;
        $this->apellido_remitente = $envio->apellido_rem;
        $this->telefono_remitente = $envio->telefono_rem;
        $this->correo_remitente = $envio->correo_rem;

        $this->nombre_destinatario = $envio->nombre_dest;
        $this->apellido_destinatario = $envio->apellido_dest;
        $this->telefono_destinatario = $envio->tlf_dest;
        $this->destinatario = $envio->documento_dest;
        $this->correo_destinatario = $envio->correo_dest;
        $this->peso = $envio->peso;
        $this->oficina = $envio->oficinas->nombre;
        $this->envio_almacen = $envio->envio_id;
        $this->aviso();
        $this->calcular_precio();
    }


    public function aviso()
    {
        if($this->aviso == true){
            $this->aviso_coste = TarifaNacionalConcepto::where('tarifa_conceptos_id', 17)->pluck('monto')->first();
        }else{
            $this->aviso_coste = 0;
        }
        $this->calcular_precio();
    }

    public function calcular_precio()
    {
        $envio = EnvioAlmacen::where('envio_id', $this->envio_almacen)->first();

        if($envio->estatus) {
            $this->dias_almacenado = \Carbon\Carbon::parse($envio->Entrada)->diffInDays(now());
            
            if($this->dias_almacenado >= 3){
                $this->costo_dias_almacenado = TarifaNacionalConcepto::where('tarifa_conceptos_id', 16)->pluck('monto')->first();
            }else{
                $this->costo_dias_almacenado = 0;
            }
            
            if($this->dias_almacenado >= 3 || $this->aviso == true){
                $this->costoTotal = ceil(($this->dias_almacenado * $this->costo_dias_almacenado)*100)/100;
                $this->costoTotal = ceil(($this->costoTotal + $this->aviso_coste)*100)/100;
            }else{
                $this->costoTotal = 0;
            }
        }else{
            return;
        }
    }

    public function store()
    {
        if ($this->monto < $this->costoTotal) {
            $this->addError('monto', 'El monto ingresado no puede ser menor que el total a pagar.');
            return;
        }

        $usuario = auth()->user();
        $Entrega = RegistroEntrega::create([
            'usuario_id' => $usuario->id,
            'envio_id' => $this->envio_id,
            'codigo_envio' => $this->codigo,
            'cedula_remitente' => $this->cedula,
            'nombre_remitente' => $this->nombre,
            'costo_total' => $this->costoTotal,
            'monto_pagado' => $this->monto,
            'tipo_pago_id' => $this->tipo_pago_id,
            'autorizado' => $this->autorizado,
        ]);
        
        $Entrega = EnvioEncaminamiento::create([
            'usuario_id' => $usuario->id,
            'envio_id' => $this->envio_id,
            'oficina_id' => $usuario->oficina_id,
            'estatus_id' => 17,
            'devolucion' => false,
        ]);
        EnvioAlmacen::where('envio_id', $this->envio_id)
        ->where('codigo', $this->codigo)
        ->where('oficina_id', $usuario->oficina_id)
        ->update([
            'estatus' => false,
            'Salida' =>now()->toDateTimeString(),
        ]);
    

        $this->reset();
        $this->open = false;
    }
}
