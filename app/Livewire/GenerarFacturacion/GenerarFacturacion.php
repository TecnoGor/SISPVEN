<?php

namespace App\Livewire\GenerarFacturacion;
use App\Models\Envio;
use App\Models\Facturacion;
use App\Models\FacturacionDetalle;
use App\Models\FacturacionEnvio;
use App\Models\FacturacionPago;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GenerarFacturacion extends Component
{
    public $monto_pagado;
    public $pagos = [];
    public $envios_sel = [];
    public $cancelar_pago = false;
    public $id_envios = [];
    public $total_lote;
    public $totales_por_servicio = [];


    public function agregar($id)
    {
        $envio = Envio::find($id);

        if (!$envio) return;

        if (collect($this->id_envios)->contains('envio_id', $envio->envio_id)) {
            return;
        }

        $this->envios_sel[] = $envio;

        $this->id_envios[] = $envio->only([
            'envio_id',
            'servicio_id',
            'coste_sin_iva',
            'nombre_rem',
            'apellido_rem',
            'tipo_documento_rem',
            'documento_rem',
            'direccion_rem'
        ]) + [
            'total_pagar' => $envio->coste
        ];
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function pagar()
    {
        $this->cancelar_pago = true;

        // Reiniciar totales
        $this->total_lote = 0;
        $this->totales_por_servicio = [];

        foreach ($this->id_envios as $envio) {

            $this->total_lote += $envio['total_pagar'];

            if (!isset($this->totales_por_servicio[$envio['servicio_id']])) {
                $this->totales_por_servicio[$envio['servicio_id']] = [
                    'cant' => 0,
                    'total' => 0,
                ];
            }

            $this->totales_por_servicio[$envio['servicio_id']]['cant']++;
            $this->totales_por_servicio[$envio['servicio_id']]['total'] += $envio['total_pagar'];
        }
    }

    public function generar_facturacion()
    {
        $id_facturacion = [];
        $count_envios = count($this->id_envios);
        $count_pagos = count($this->pagos);
        $porcentajes_pagos = [];

        DB::beginTransaction();

        try {

            foreach ($this->pagos as $pago) {
                if($this->total_lote == 0){
                    $porcentaje = 0;
                }else{
                    $porcentaje = $pago['monto'] / $this->total_lote;
                }         
                $porcentajes_pagos[$pago['tipo_pago_id']] = $porcentaje;
            }

            foreach($this->id_envios as $envio){
                $facturacion = Facturacion::create([
                    'oficina_id' => $this->usuario['oficina_id'],
                    'nombre' => $envio['nombre_rem'],
                    'apellido' => $envio['apellido_rem'],
                    'tipo_documento' => $envio['tipo_documento_rem'],
                    'documento' => $envio['documento_rem'],
                    'direccion' => $envio['direccion_rem'],
                    'monto_total' => $envio['total_pagar'],
                    'iva' => $envio['iva'],
                ]);
                $id_facturacion[] = [
                    'facturacion_id' => $facturacion->facturacion_id,
                    'envio_id' => $envio['envio_id'],
                    'servicio_id' => $envio['servicio_id'],
                    'total_pagar' => $envio['total_pagar'],
                ];
            }

            foreach($id_facturacion as $factura){
                FacturacionDetalle::create([
                    'servicio_id' => $factura['servicio_id'],
                    'facturacion_id' => $factura['facturacion_id'],
                    'monto' => $factura['total_pagar'],
                ]);
            }
                

            foreach($id_facturacion as $factura){
                $factura_id = $factura['facturacion_id'];
                $total_envio = $factura['total_pagar'];

                $monto_pago = bcdiv($total_envio / $count_pagos, 1, 2);

                foreach($this->pagos as $pago){
                    $tipo_pago_id = $pago['tipo_pago_id'];
                    $porcentaje_pago = $porcentajes_pagos[$tipo_pago_id];

                    $monto_proporcion = bcdiv($total_envio * $porcentaje_pago, 1, 2);

                    FacturacionPago::create([
                        'facturacion_id' => $factura_id,
                        'tipo_pago_id' => $tipo_pago_id,
                        'monto' => $monto_proporcion,
                    ]);
                }
            }

            foreach($id_facturacion as $factura){
                FacturacionEnvio::create([
                    'facturacion_id' => $factura['facturacion_id'],
                    'envio_id' => $factura['envio_id'],
                    'oficina_id' => $this->usuario['oficina_id'],
                    'usuario_id' => $this->usuario['id'],
                    'servicio_id' => $factura['servicio_id'],
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se generó facturación de " . count($id_facturacion) . " envío(s) con total Bs " . ($this->total_lote ?? 0),
            ]);

            DB::commit();
        }catch(\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique que todos los campos esten llenados correctamente');
            return;
        }

        session()->forget('id_envios');
        $this->id_envios = [];

        $this->dispatch('alertSuccess', message: 'Facturacion registrada correctamente');
        $this->dispatch('envio_registrado');
    }

    public function cerrar_pago()
    {
        $this->cancelar_pago = false;
        $this->total_lote = 0;
        $this->totales_por_servicio = [];
    }



    public function render()
    {
        $envios = Envio::whereDoesntHave('factura_envio')->where('servicio_id', '!=', 3)->get();

        return view('livewire.generar-facturacion.generar-facturacion', ['envio' => $envios]);
    }
}
