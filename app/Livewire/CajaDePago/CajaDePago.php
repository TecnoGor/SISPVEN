<?php

namespace App\Livewire\CajaDePago;

use Livewire\Component;
use App\Models\TipoPago;
use App\Models\EntidadBancaria;
use App\Models\UsuarioSeguimiento;
use App\Services\ConsultaPagoMovilBDV;

class CajaDePago extends Component
{
    public $monto, $pm_telefono, $pm_cedula, $pm_referencia, $pm_telefono_dest, $pm_banco, $pm_fecha;
    public $montoPagado;
    public $numero_referencia;
    public $metodo_pago_seleccionado = [];
    public $pagos = [];
    public $metodos_pago = [];
    public $entidades_bancarias = [];
    public $pago_movil_verificado = false;
    public $showModalPagoMovil = false;

    public function mount()
    {
        $this->metodos_pago = TipoPago::whereNotIn('tipo_pago_id', [6])->get();
        $this->entidades_bancarias = EntidadBancaria::orderBy('codigo')->get();
    }

    public function updatedMetodoPagoSeleccionado()
    {
        $this->numero_referencia = "";
    }

    public function agregar_pago()
    {
        // Validar los campos
        $this->validate([
            'metodo_pago_seleccionado' => 'required',
            'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
            'numero_referencia' => 'required_if:metodo_pago_seleccionado,3,4|regex:/^[0-9]+$/',

        ], [
            'numero_referencia.required_if' => 'El número de referencia es obligatorio para este método de pago.',
        ]);

        // Convertir el monto a formato numérico (quitar formato de texto)
        $monto_numerico = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));

        if ($monto_numerico > 10000000) {
            $this->dispatch('alertSuccess2', message: 'El monto agregado es demasiado alto');
            return;
        }

        // if ($this->metodo_pago_seleccionado == 4 && !$this->pago_movil_verificado) {
        //     $this->showModalPagoMovil = true;
        //     return;
        // }

        $tipo_pago = TipoPago::find($this->metodo_pago_seleccionado);
        if ($tipo_pago) {
            // Busca si el método de pago ya está en la matriz
            $pagoExistenteKey = array_search($tipo_pago->tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));
            if ($pagoExistenteKey !== false) {
                // Si existe, suma el nuevo monto al existente
                $this->pagos[$pagoExistenteKey]['monto'] += $monto_numerico;
            } else {
                // Si no existe, agrega un nuevo pago
                $this->pagos[] = [
                    'tipo_pago_id' => $tipo_pago->tipo_pago_id,
                    'nombre' => $tipo_pago->nombre,
                    'monto' => $monto_numerico,
                    'numero_referencia' => $this->numero_referencia,
                ];
            }
            $this->montoPagado += $monto_numerico;
            $this->dispatch('montoPagadoActualizado', $this->montoPagado, $this->pagos);
        }

        // Resetear los campos
        $this->metodo_pago_seleccionado = null;
        $this->monto = "";
        $this->numero_referencia = "";
        // Reseteo de verificacion de pago movil
        $this->pago_movil_verificado = false;
    }


    public function eliminar_pago($tipo_pago_id)
    {
        // Verifica que haya un pago seleccionado
        if ($tipo_pago_id) {
            // Encuentra el índice del pago seleccionado
            $pagoKey = array_search($tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));

            // Si se encuentra el pago, elimínalo
            if ($pagoKey !== false) {
                $this->montoPagado -= $this->pagos[$pagoKey]['monto'];
                unset($this->pagos[$pagoKey]);
                // Reindexa el array para evitar huecos
                $this->pagos = array_values($this->pagos);
            }

            $this->montoPagado = bcadd(
                (string) array_sum(array_column($this->pagos, 'monto')),
                "0",
                2
            );
        }
        $this->dispatch('montoPagadoActualizado', $this->montoPagado, $this->pagos);
    }

    public function verificarPagoMovil(ConsultaPagoMovilBDV $bdv)
    {
        $this->validate([
            'pm_telefono'      => 'required|digits:11',
            'pm_telefono_dest' => 'required|digits:11',
            'pm_cedula'        => 'required|string|min:6',
            'pm_referencia'    => 'required|string|min:4',
            'pm_fecha'         => 'required|date',
            'pm_banco'         => 'required|string',
            'monto'            => 'required',
        ], [
            'pm_telefono.required'      => 'El teléfono del pagador es obligatorio.',
            'pm_telefono.digits'        => 'El teléfono debe tener 11 dígitos.',
            'pm_telefono_dest.required' => 'El teléfono destino es obligatorio.',
            'pm_telefono_dest.digits'   => 'El teléfono destino debe tener 11 dígitos.',
            'pm_cedula.required'        => 'La cédula del pagador es obligatoria.',
            'pm_cedula.min'             => 'La cédula debe tener al menos 6 caracteres.',
            'pm_referencia.required'    => 'La referencia es obligatoria.',
            'pm_referencia.min'         => 'La referencia debe tener al menos 4 caracteres.',
            'pm_fecha.required'         => 'La fecha del pago es obligatoria.',
            'pm_fecha.date'             => 'La fecha no es válida.',
            'pm_banco.required'         => 'Debe seleccionar un banco.',
            'monto.required'            => 'El monto es obligatorio.',
        ]);

        // Asegurar prefijo de nacionalidad en la cédula (V o E)
        $cedula = trim($this->pm_cedula);
        if (!preg_match('/^[VvEe]/', $cedula)) {
            $cedula = 'V' . $cedula;
        }
        $cedula = strtoupper($cedula);

        $data = [
            "telefonoPagador" => $this->pm_telefono,
            "telefonoDestino" => $this->pm_telefono_dest,
            "cedulaPagador" => $cedula,
            "referencia" => $this->pm_referencia,
            "fechaPago" => $this->pm_fecha,
            "bancoOrigen" => $this->pm_banco,
            "importe" => number_format(floatval(str_replace(',', '.', str_replace('.', '', $this->monto))), 2, '.', ''),
            "reqCed" => $this->pm_banco === '0102',
        ];

        $response = $bdv->consultarPagoMovil($data);

        if (isset($response['error'])) {
            $errorMsg = $response['message'] ?? 'Error consultando la API';
            $this->dispatch('alertSuccess2', message: $errorMsg);
            return;
        }

        if ($response['status'] == 'APROBADO') {
            $this->pago_movil_verificado = true;
            $this->numero_referencia = $this->pm_referencia;

            $this->showModalPagoMovil = false;

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se verificó pago móvil BDV con referencia ({$this->pm_referencia}) — APROBADO",
            ]);

            $this->dispatch('alertSuccess', message: $response['message'] ?? 'Pago verificado exitosamente');

            $this->agregar_pago();
        } else {
            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Intento de verificación de pago móvil BDV con referencia ({$this->pm_referencia}) — NO APROBADO",
            ]);

            $errorMsg = $response['message'] ?? 'El pago no fue confirmado';
            $this->dispatch('alertSuccess2', message: $errorMsg);
        }
    }

    public function render()
    {
        return view('livewire.caja-de-pago.caja-de-pago');
    }
}
