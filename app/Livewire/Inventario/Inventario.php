<?php

namespace App\Livewire\Inventario;

use App\Models\User;
use App\Models\Insumo;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\InsumoUsuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use App\Models\InventarioInsumo;
use App\Exports\InventarioExport;
use Illuminate\Support\Facades\DB;
use App\Models\InsumoTransferencia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioGeneralExport;
use App\Models\InsumoUsuarioTransferencia;
use App\Models\UsuarioSeguimiento;


#[Layout('layouts.app')]
class Inventario extends Component
{
    public $search;
    public $perPage = 15;
    public $oficina;
    public $modalOpen = false;
    public $modalOpen2 = false;
    public $modalOpen3 = false;
    public $modalOpenUser = false;
    public $insumos = [];
    public $usuario = [];
    public $promotores = [];
    public $insumo_selec = [];
    public $cantidad_por_insumo = [];
    public $cops = [];
    public $cop;
    public $opts = [];
    public $opt;
    public $registro_transferencia = [];
    public $promotor;
    public $costos = [];

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->insumos = Insumo::all();
        $this->costos = Insumo::select('insumo_id', 'costo')->pluck('costo', 'insumo_id')->toArray();
        $this->cops = Oficina::where('tipo_oficina_id', 4)->where('externa', false)->where('estatus_id', 1)->get();
    }

    public function exportarInventario()
    {
        $inventarios = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])->orderBy('insumo_inventario_id')->get();
        return Excel::download(new InventarioExport($inventarios), 'Inventario_Insumos.xlsx');
    }

    public function exportarInventarioPdf()
    {
        $inventarios = \App\Models\InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
            ->orderBy('insumo_inventario_id')->get();
        $oficina = $this->oficina;
        $pdf = Pdf::loadView('pdf.inventario', compact('inventarios', 'oficina'));
        return response()->streamDownload(
            fn() => print($pdf->stream()),
            'Inventario_Insumos_' . ($oficina->nombre ?? 'oficina') . '.pdf'
        );
    }

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function openModal2()
    {
        $this->modalOpen2 = true;
    }

    public function openModal3()
    {
        if ($this->oficina['tipo_oficina_id'] == 4) {
            $this->opts = Oficina::where('oficina_relacionada_id', $this->oficina['oficina_id'])->get();
            $this->cop = $this->oficina['oficina_id'];
        } else {
            $this->opts = [];
        }

        $this->modalOpen3 = true;
    }

    public function openModalUser()
    {
        $this->promotores = User::where('oficina_id', $this->oficina['oficina_id'])->role(['Promotor Integral', 'Jefe de Oficina Unipersonal', 'Jefe de OPT'])->get();
        $this->modalOpenUser = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->modalOpen2 = false;
        $this->modalOpen3 = false;
        $this->modalOpenUser = false;
        $this->cop = '';
        $this->opt = '';
        $this->promotor = '';
        $this->opts = [];
        $this->insumo_selec = [];
        $this->cantidad_por_insumo = [];
    }

    public function updatedCop()
    {
        if (!$this->cop) {
            $this->opts = [];
        } else {
            $this->opts = Oficina::where('oficina_relacionada_id', $this->cop)->get();
        }
    }

    public function ingresar_insumos()
    {
        if (empty($this->insumo_selec)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar al menos un insumo!');
            return;
        }

        foreach ($this->insumo_selec as $insumo_id) {
            if (empty($this->cantidad_por_insumo[$insumo_id]) || $this->cantidad_por_insumo[$insumo_id] <= 0) {
                $this->dispatch('alertSuccess2', message: 'debe agregar una cantidad para cada insumo seleccionado, No se permiten cantidades negativas');
                return;
            }
        }

        foreach ($this->insumo_selec as $insumo_id) {
            $cantidad = $this->cantidad_por_insumo[$insumo_id];

            if ($cantidad > 0) {
                // Verificar si el insumo ya está registrado en la oficina
                $inventario = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if ($inventario) {
                    // Si el insumo ya está registrado, se suma la cantidad
                    $inventario->cantidad += $cantidad;
                    $inventario->save();
                } else {
                    // Si el insumo no está registrado, se crea un nuevo registro
                    InventarioInsumo::create([
                        'oficina_id' => $this->usuario['oficina_id'],
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
        }
        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se ingresaron insumos (" . count($this->insumo_selec) . " tipo(s)) al inventario de la oficina ({$this->usuario['oficina_id']})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos agregados correctamente!');
    }


    public function transferir_insumos()
    {
        if (empty($this->cop)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar una oficina de destino!');
            return;
        }

        if (empty($this->insumo_selec)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar al menos un insumo!');
            return;
        }

        foreach ($this->insumo_selec as $insumo_id) {
            // Verificar si la cantidad ingresada es válida
            if (empty($this->cantidad_por_insumo[$insumo_id]) || $this->cantidad_por_insumo[$insumo_id] <= 0) {
                $this->dispatch('alertSuccess2', message: 'Debe agregar una cantidad válida para cada insumo seleccionado.');
                return;
            }

            $inventario = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                ->where('insumo_id', $insumo_id)
                ->first();

            if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_id]) {
                $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente para transferir el insumo seleccionado.');
                return;
            }
        }

        foreach ($this->insumo_selec as $insumo_id) {
            $cantidad = $this->cantidad_por_insumo[$insumo_id];

            if ($cantidad > 0) {
                // Descontar la cantidad del inventario de la oficina actual
                $inventario_actual = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                    ->where('insumo_id', $insumo_id)
                    ->first();

                $inventario_actual->cantidad -= $cantidad;
                $inventario_actual->save();

                // Agregar la cantidad al inventario de la oficina de destino
                $inventario_destino = InventarioInsumo::where('oficina_id', $this->cop)
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if ($inventario_destino) {
                    $inventario_destino->cantidad += $cantidad;
                    $inventario_destino->save();
                } else {
                    InventarioInsumo::create([
                        'oficina_id' => $this->cop,
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
            $this->registro_transferencia[] = [
                'insumo' => $insumo_id,
                'cantidad' => $cantidad,
                'oficina_origen' => $this->usuario['oficina_id'],
                'oficina_destino' => $this->cop,
                'coste' => $this->costos[$insumo_id],
            ];

            $this->registros();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se transfirieron insumos (" . count($this->insumo_selec) . " tipo(s)) de la oficina ({$this->usuario['oficina_id']}) a la oficina ({$this->cop})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos transferidos correctamente!');
    }

    public function transferir_insumos_opt()
    {
        if (empty($this->cop) || empty($this->opt)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar una COP y/o una OPT!');
            return;
        }

        if (empty($this->insumo_selec)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar al menos un insumo!');
            return;
        }

        foreach ($this->insumo_selec as $insumo_id) {
            if (empty($this->cantidad_por_insumo[$insumo_id]) || $this->cantidad_por_insumo[$insumo_id] <= 0) {
                $this->dispatch('alertSuccess2', message: 'Debe agregar una cantidad válida para cada insumo seleccionado.');
                return;
            }

            $inventario = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                ->where('insumo_id', $insumo_id)
                ->first();

            if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_id]) {
                $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente para transferir el insumo seleccionado.');
                return;
            }
        }

        foreach ($this->insumo_selec as $insumo_id) {
            $cantidad = $this->cantidad_por_insumo[$insumo_id];

            if ($cantidad > 0) {
                $inventario_actual = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                    ->where('insumo_id', $insumo_id)
                    ->first();

                $inventario_actual->cantidad -= $cantidad;
                $inventario_actual->save();

                $inventario_destino = InventarioInsumo::where('oficina_id', $this->opt)
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if ($inventario_destino) {
                    $inventario_destino->cantidad += $cantidad;
                    $inventario_destino->save();
                } else {
                    InventarioInsumo::create([
                        'oficina_id' => $this->opt,
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
            $this->registro_transferencia[] = [
                'insumo' => $insumo_id,
                'cantidad' => $cantidad,
                'oficina_origen' => $this->usuario['oficina_id'],
                'oficina_destino' => $this->opt,
                'coste' => $this->costos[$insumo_id],
            ];

            $this->registros();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se transfirieron insumos COP→OPT (" . count($this->insumo_selec) . " tipo(s)) de la oficina ({$this->usuario['oficina_id']}) a la OPT ({$this->opt})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos transferidos correctamente!');
    }

    public function asignar()
    {
        DB::beginTransaction();

        try {
            if (empty($this->promotor)) {
                $this->dispatch('alertSuccess2', message: 'Debe seleccionar un Promotor Integral!');
                return;
            }

            if (empty($this->insumo_selec)) {
                $this->dispatch('alertSuccess2', message: 'Debe seleccionar al menos un insumo!');
                return;
            }

            foreach ($this->insumo_selec as $insumo_id) {
                if (empty($this->cantidad_por_insumo[$insumo_id]) || $this->cantidad_por_insumo[$insumo_id] <= 0) {
                    $this->dispatch('alertSuccess2', message: 'Debe agregar una cantidad válida para cada insumo seleccionado.');
                    return;
                }

                $inventario = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_id]) {
                    $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente para transferir el insumo seleccionado.');
                    return;
                }
            }

            foreach ($this->insumo_selec as $insumo_id) {
                $cantidad = $this->cantidad_por_insumo[$insumo_id];

                if ($cantidad > 0) {
                    $inventario_actual = InventarioInsumo::where('oficina_id', $this->usuario['oficina_id'])
                        ->where('insumo_id', $insumo_id)
                        ->first();

                    $inventario_actual->cantidad -= $cantidad;
                    $inventario_actual->save();

                    if ($cantidad > 0) {
                        $inventario_usuario = InsumoUsuario::where('oficina_id', $this->oficina['oficina_id'])
                            ->where('usuario_id', $this->promotor)->where('insumo_id', $insumo_id)
                            ->first();

                        if ($inventario_usuario) {
                            $inventario_usuario->cantidad += $cantidad;
                            $inventario_usuario->save();
                        } else {
                            InsumoUsuario::create([
                                'oficina_id' => $this->oficina['oficina_id'],
                                'usuario_id' => $this->promotor,
                                'insumo_id' => $insumo_id,
                                'cantidad' => $cantidad,
                            ]);
                        }
                    }
                    InsumoUsuarioTransferencia::create([
                        'usuario_id' => $this->promotor,
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                        'coste' => $this->costos[$insumo_id] ?? 0,
                    ]);
                }
            }
            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se asignaron insumos (" . count($this->insumo_selec) . " tipo(s)) al promotor ({$this->promotor}) desde la oficina ({$this->usuario['oficina_id']})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Insumos asignados correctamente!');
        } catch (\Exception $e) {
            //    dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error verifique los datos e intente de nuevo');
        }
        $this->closeModal();
    }

    public function limpiarExistencias($inventario_id)
    {
        DB::beginTransaction();

        try {
            $inventario = InventarioInsumo::where('insumo_inventario_id', $inventario_id)
                ->where('oficina_id', $this->usuario['oficina_id'])
                ->first();

            if (!$inventario) {
                $this->dispatch('alertSuccess2', message: 'No se encontró el registro de inventario.');
                return;
            }

            $insumo_id = $inventario->insumo_id;

            $inventario->cantidad = 0;
            $inventario->save();

            InsumoUsuario::where('oficina_id', $this->usuario['oficina_id'])
                ->where('insumo_id', $insumo_id)
                ->update(['cantidad' => 0]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se limpiaron existencias del insumo ({$insumo_id}) en la oficina ({$this->usuario['oficina_id']})",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Existencias limpiadas correctamente!');
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al limpiar las existencias.');
        }
    }

    public function registros()
    {
        foreach ($this->registro_transferencia as $registro) {
            InsumoTransferencia::create([
                'usuario_id' => $this->usuario['id'],
                'insumo_id' => $registro['insumo'],
                'cantidad' => $registro['cantidad'],
                'oficina_origen' => $registro['oficina_origen'],
                'oficina_destino' => $registro['oficina_destino'],
                'coste' => $registro['coste'],
            ]);
        }
        $this->registro_transferencia = [];
    }

    public function render()
    {
        $oficina_id = $this->usuario['oficina_id'];

        // Obtener los inventarios de la oficina
        $inventarios = InventarioInsumo::with('insumo')
            ->where('oficina_id', $oficina_id)
            ->orderBy('insumo_id')
            ->get();

        // Obtener la cantidad asignada a usuarios por insumo
        $asignados = InsumoUsuario::where('oficina_id', $oficina_id)
            ->select('insumo_id', DB::raw('SUM(cantidad) as total_asignado'))
            ->groupBy('insumo_id')
            ->pluck('total_asignado', 'insumo_id');

        // Agregar total a cada inventario
        foreach ($inventarios as $inventario) {
            $asignado = $asignados->get($inventario->insumo_id, 0);
            $inventario->total_oficina = $inventario->cantidad + $asignado;
        }

        return view('livewire.inventario.inventario', ['inventarios' => $inventarios]);
    }
}
