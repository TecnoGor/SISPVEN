<?php

namespace App\Livewire\Envios;

use App\Models\Envio;
use App\Models\User;
use App\Models\Oficina;
use App\Models\Servicio;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\FacturacionTarifaEnvio;
use App\Models\FacturacionDetalle;
use App\Models\FacturacionPago;
use App\Models\FacturacionEnvio;
use App\Models\Facturacion;
use App\Models\TipoPago;
use App\Models\TarifaNacionalConcepto;
use App\Models\TarifaInternacionalConcepto;

#[Layout('layouts.app')]
class CierreCaja extends Component
{

    public $servicio_id, $cantidad, $servicio, $usuario_id, $oficina_id, $desde, $hasta, $sobrante, $envio_base;
    public $metodos_pago = [];
    public $usuarios = [];
    public $total_pagar = [];
    public $total_pagado = [];
    public $iva = [];
    public $subservicios = [];
    public $registros = [];


    public function updatedOficinaId($value)
    {

        if ($value) {
            $this->usuarios = User::where('oficina_id', $value)->get();
            $this->usuario_id = null;
        }
        else {
            $this->usuarios = [];
            $this->usuario_id = null;
        }

    }

    public function mount($servicio_id)
    {

        $this->servicio_id = $servicio_id;

    }

    public function render()
    {
        
        $this->usuarios = [];
        $this->total_pagar = [];
        $this->total_pagado = [];
        $this->iva = [];
        $this->subservicios = [];
        $this->registros = [];
        $this->metodos_pago = [];

        $oficinas = Oficina::where('oficina_relacionada_id', auth()->user()->oficina_id)->get();   

        $usuario_id = $this->usuario_id;
        $oficina_id = $this->oficina_id;

        if (auth()->user()->hasRole('Jefe de OPT')) {
            if(!$this->usuarios)
            {
                $this->usuarios = User::where('oficina_id', auth()->user()->oficina_id)->get();
            }
            if(!$this->oficina_id)
            {
                $this->oficina_id = Oficina::find(auth()->user()->oficina_id)->oficina_id;
            }
        }

        $fechaInicio = $this->desde;
        $fechaFin = $this->hasta;

        if ((!$fechaInicio)||(!$fechaFin)) {
            $fechaInicio = now()->startOfDay();
            $fechaFin = now()->endOfDay();

            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        }
        else {
            $fechaInicio = $fechaInicio."  00:00:00";
            $fechaFin = $fechaFin."  23:59:59";
        }

        $registros = FacturacionEnvio::where('servicio_id', $this->servicio_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($oficina_id) {

            $registros = $registros->where('oficina_id', $oficina_id);

            if ($usuario_id) {

                $registros = $registros->where('usuario_id', $usuario_id);

            }

        }
        else {

            $registros = $registros->where('oficina_id', auth()->user()->oficina_id)
            ->where('usuario_id', auth()->user()->id);

        }
                
        $registros = $registros->get();

        $cantidad = FacturacionEnvio::where('servicio_id', $this->servicio_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($oficina_id) {

            $cantidad = $cantidad->where('oficina_id', $oficina_id);

            if ($usuario_id) {

                $cantidad = $cantidad->where('usuario_id', $usuario_id);

            }

        }
        else {

            $cantidad = $cantidad->where('oficina_id', auth()->user()->oficina_id)
            ->where('usuario_id', auth()->user()->id);

        }

        $cantidad = $cantidad->count();

        $this->registros = $registros;
        $this->cantidad = $cantidad;

        foreach($this->registros as $registro)
        {

            $subservicios = FacturacionTarifaEnvio::where('envio_id', $registro->envio_id)->get();

            if(Servicio::find($this->servicio_id)->nacional)
            {
                foreach ($subservicios as $subservicio) {

                    $monto_subservicio = TarifaNacionalConcepto::find($subservicio->tarifa_id)->monto;

                    if (isset($this->subservicios[TarifaNacionalConcepto::find($subservicio->tarifa_id)->nombre])) {
                        $this->subservicios[TarifaNacionalConcepto::find($subservicio->tarifa_id)->nombre] += $monto_subservicio;
                    }
                    else {
                        $this->subservicios[TarifaNacionalConcepto::find($subservicio->tarifa_id)->nombre] = $monto_subservicio;
                    }
                }
            }
            else
            {
                foreach ($subservicios as $subservicio) {

                    $monto_subservicio = TarifaInternacionalConcepto::find($subservicio->tarifa_id)->monto;

                    if (isset($this->subservicios[TarifaInternacionalConcepto::find($subservicio->tarifa_id)->nombre])) {
                        $this->subservicios[TarifaInternacionalConcepto::find($subservicio->tarifa_id)->nombre] += $monto_subservicio;
                    }
                    else {
                        $this->subservicios[TarifaInternacionalConcepto::find($subservicio->tarifa_id)->nombre] = $monto_subservicio;
                    }

                }
            }

            $facturacion = Facturacion::find($registro->facturacion_id);
            $facturacion_detalles = FacturacionDetalle::where('facturacion_id', $registro->facturacion_id)->first();

            $facturacion_pago = FacturacionPago::where('facturacion_id', $registro->facturacion_id)->get();
            foreach ($facturacion_pago as $fact_pago) {

                $tipo_pago = TipoPago::find($fact_pago->tipo_pago_id);

                if (isset($this->metodos_pago[$tipo_pago->nombre])) {

                    $this->metodos_pago[$tipo_pago->nombre] += $fact_pago->monto;

                }
                elseif (!isset($this->metodos_pago[$tipo_pago->nombre])) {

                    $this->metodos_pago[$tipo_pago->nombre] = $fact_pago->monto;

                }
            }
            
            $this->total_pagar[$registro->facturacion_id] = $facturacion_detalles->monto;
            $this->total_pagado[$registro->facturacion_id] = $facturacion->monto_total;
            $this->iva[$registro->facturacion_id] = $facturacion->iva;

        }

        if ($registros->isNotEmpty()) {
            
            $this->envio_base = array_sum($this->total_pagar) - array_sum($this->iva) - array_sum($this->subservicios) ?? 0;
            $this->sobrante = array_sum($this->total_pagado) - array_sum($this->total_pagar);
            $this->iva = array_sum($this->iva);
            $this->total_pagar = array_sum($this->total_pagar);
            $this->total_pagado = array_sum($this->total_pagado);

        }

        $this->servicio = Servicio::find($this->servicio_id);

        return view('livewire.envios.cierre-caja', compact('oficinas'));
        
    }
}
