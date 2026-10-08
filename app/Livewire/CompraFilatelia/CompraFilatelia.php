<?php

namespace App\Livewire\CompraFilatelia;

use App\Models\Sello;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Documento;
use App\Models\SerieFilatelia;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CompraFilatelia extends Component
{
    public $series, $pago_fil = 0, $iva = 0, $pago_total = 0;
    public $nombre, $apellido, $tipo_documento, $documento, $correo, $telefono;

    public $tipos_documentos = [];
    public $monto_pagado = 0;
    public $pagos = [];
    public $sellos;
    public $pedidos = [];
    public $usuario = [];
    public $oficina = [];

    public function mount()
    {
        $this->series = SerieFilatelia::where('activo', true)->orderBy('serie_filatelia_id', 'asc')->get();
        $this->sellos = Sello::where('activo', true)->orderBy('sello_id', 'asc')->get();
        $this->pedidos = [['serie' => null, 'sello' => null, 'cantidad' => 1]];
        $this->tipos_documentos = Documento::all();
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
    }

    public function agregar_pedido()
    {
        $this->pedidos[] = ['serie' => null, 'sello' => null, 'cantidad' => 1];
    }

    public function eliminar_pedido($index)
    {
        unset($this->pedidos[$index]);
        $this->pedidos = array_values($this->pedidos);
        $this->calcularTotal();
    }

    public function updatedPedidos()
    {
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->pago_fil = 0;

        foreach ($this->pedidos as $pedido) {
            if (!empty($pedido['sello']) && !empty($pedido['cantidad'])) {
                $sello = $this->sellos->firstWhere('sello_id', $pedido['sello']);
                if ($sello) {
                    $this->pago_fil += $sello->coste * $pedido['cantidad'];
                }
            }
        }
        $this->iva = bcdiv($this->pago_fil * 0.16, 1, 2);
        $this->pago_total = bcdiv($this->pago_fil + $this->iva, 1, 2);
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function verificar_pago()
    {
        $this->validate([
            'pedidos.*.serie'    => 'required',
            'pedidos.*.sello'    => 'required',
            'pedidos.*.cantidad' => 'required|integer|min:1',
            'tipo_documento'     => 'required|string',
            'documento'          => 'required|string|min:6|max:20',
            'nombre'             => 'required|string|max:255',
            'apellido'           => 'required|string|max:255',
            'telefono'           => 'required|string|max:20',
            'correo'             => 'required|email|max:255',
        ], [
            'pedidos.*.serie.required'    => 'Debe seleccionar una serie.',
            'pedidos.*.sello.required'    => 'Debe seleccionar un sello.',
            'pedidos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'pedidos.*.cantidad.min'      => 'La cantidad debe ser al menos 1.',
            'tipo_documento.required'     => 'El tipo de documento es obligatorio.',
            'documento.required'          => 'El documento es obligatorio.',
            'nombre.required'             => 'El nombre es obligatorio.',
            'apellido.required'           => 'El apellido es obligatorio.',
            'telefono.required'           => 'El teléfono es obligatorio.',
            'correo.required'             => 'El correo es obligatorio.',
            'correo.email'                => 'El correo no es válido.',
        ]);

        if ($this->pago_total <= 0) {
            $this->dispatch('alertSuccess2', message: 'El total a pagar debe ser mayor a cero.');
            return;
        }

        if ($this->monto_pagado >= $this->pago_total) {
            $this->crear_compra();
        } else {
            $this->dispatch('alertSuccess2', message: 'El monto pagado debe ser igual o mayor al total a pagar.');
        }
    }

    private function crear_compra()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Registro de facturación del servicio
            $facturacion = \App\Models\FacturacionServicio::create([
                'servicio_id'    => 13,
                'referencia_id'  => null,
                'tipo_documento' => $this->tipo_documento,
                'documento'      => $this->documento,
                'monto_subtotal' => $this->pago_fil,
                'monto_iva'      => $this->iva,
                'monto_total'    => $this->pago_total,
                'oficina_id'     => $this->usuario['oficina_id'],
                'usuario_id'     => $this->usuario['id'],
            ]);

            foreach ($this->pagos as $pago) {
                \App\Models\FacturacionServicioPago::create([
                    'facturacion_servicio_id' => $facturacion->facturacion_servicio_id,
                    'tipo_pago_id'            => $pago['tipo_pago_id'] ?? $pago['tipo_pago'],
                    'monto'                   => $pago['monto'],
                    'referencia_bancaria'     => $pago['numero_referencia'] ?? null,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró compra de filatelia ({$facturacion->facturacion_servicio_id}) por Bs {$this->pago_total}",
            ]);

            \Illuminate\Support\Facades\DB::commit();
            $this->dispatch('alertSuccess', message: 'Compra realizada correctamente!');
            $this->dispatch('recargarPagina');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al registrar la compra, intente de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.compra-filatelia.compra-filatelia');
    }
}
