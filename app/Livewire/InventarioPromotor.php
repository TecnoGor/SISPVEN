<?php

namespace App\Livewire;

use App\Models\User;

use App\Models\Oficina;
use Livewire\Component;
use App\Models\InsumoUsuario;
use Livewire\Attributes\Layout;
use App\Models\InventarioInsumo;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class InventarioPromotor extends Component
{
    public $promotor, $promotor_reasignar;

    public $usuario = [];
    public $oficina = [];
    public $promotores = [];
    public $insumos = [];
    public $insumo_selec = [];
    public $cantidad_por_insumo = [];
    public $modalOpen = false;

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->promotores = User::where('oficina_id', $this->oficina['oficina_id'])->role(['Promotor Integral', 'Jefe de Oficina Unipersonal', 'Jefe de OPT'])->get();
    }

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function close()
    {
        $this->modalOpen = false;
        $this->promotor_reasignar = [];
        $this->insumo_selec = [];
        $this->cantidad_por_insumo = [];
    }

    public function updatedPromotorReasignar()
    {
        if ($this->promotor_reasignar) {
            $this->insumos = InsumoUsuario::with('insumo')->where('usuario_id', $this->promotor_reasignar)->orderBy('insumo_id', 'desc')->get();
        } else {
            $this->insumos = [];
        }
    }

    public function asignar()
    {
        if (empty($this->promotor_reasignar)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un promotor!');
            return;
        }

        if (empty($this->insumo_selec)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar al menos un insumo!');
            return;
        }

        foreach ($this->insumo_selec as $insumo_usuario_id) {
            // Verificar si la cantidad ingresada es válida
            if (empty($this->cantidad_por_insumo[$insumo_usuario_id]) || $this->cantidad_por_insumo[$insumo_usuario_id] <= 0) {
                $this->dispatch('alertSuccess2', message: 'Debe agregar una cantidad válida para cada insumo seleccionado.');
                return;
            }

            $inventario = InsumoUsuario::where('insumo_usuario_id', $insumo_usuario_id)
                ->where('usuario_id', $this->promotor_reasignar)
                ->first();
            if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_usuario_id]) {
                $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente para transferir el insumo seleccionado.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            foreach ($this->insumo_selec as $insumo_usuario_id) {
                $cantidad = $this->cantidad_por_insumo[$insumo_usuario_id];

                $inventario_actual = InsumoUsuario::where('insumo_usuario_id', $insumo_usuario_id)->first();

                $inventario_actual->cantidad -= $cantidad;
                $inventario_actual->save();

                $inventario_oficina = InventarioInsumo::where('oficina_id', $this->oficina['oficina_id'])
                    ->where('insumo_id', $inventario_actual->insumo_id)
                    ->first();

                if ($inventario_oficina) {
                    $inventario_oficina->cantidad += $cantidad;
                    $inventario_oficina->save();
                } else {
                    throw new \Exception('No se encontró un registro de este insumo en la oficina.');
                }
            }
            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se devolvieron " . count($this->insumo_selec) . " tipo(s) de insumos del promotor ({$this->promotor_reasignar}) al inventario de la oficina ({$this->oficina['oficina_id']})",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Insumos devueltos correctamente!');
            $this->close();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error, verifique los datos e intente de nuevo.');
        }
    }



    public function limpiarInsumoPromotor($insumo_usuario_id)
    {
        DB::beginTransaction();

        try {
            $registro = InsumoUsuario::where('insumo_usuario_id', $insumo_usuario_id)
                ->where('oficina_id', $this->oficina['oficina_id'])
                ->first();

            if (!$registro) {
                $this->dispatch('alertSuccess2', message: 'No se encontró el registro a limpiar.');
                return;
            }

            $insumo_id = $registro->insumo_id;
            $usuario_id = $registro->usuario_id;

            $registro->cantidad = 0;
            $registro->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se limpiaron existencias del insumo ({$insumo_id}) asignadas al promotor ({$usuario_id}) en la oficina ({$this->oficina['oficina_id']})",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Existencias del promotor limpiadas correctamente!');
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al limpiar las existencias.');
        }
    }

    public function render()
    {
        if ($this->promotor) {
            $inventarios = InsumoUsuario::where('usuario_id', $this->promotor)->orderBy('insumo_id', 'desc')->get();
        } else {
            $inventarios = [];
        }

        return view('livewire.inventario-promotor', ['inventarios' => $inventarios]);
    }
}
