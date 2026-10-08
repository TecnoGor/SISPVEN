<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\InventarioInsumo;
use App\Models\InsumoTransferencia;
use App\Models\Insumo;
use App\Models\Oficina;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioGeneralExport;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class InventarioGeneral extends Component
{
    public $search;
    public $perPage = 15;
    public $oficina;
    public $modalOpen = false;
    public $modalOpen2 = false;
    public $modalOpen3 = false;
    public $modalOpen4 = false;
    public $insumos = [];
    public $usuario = [];
    public $insumo_selec = [];
    public $cantidad_por_insumo = [];
    public $cops = [];
    public $cop;
    public $opts = [];
    public $opt;
    public $centrales = [];
    public $central;
    public $registro_transferencia = [];
    public $costos = [];

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->insumos = Insumo::all();
        $this->centrales = Oficina::where('tipo_oficina_id', 5)->get();
        $this->costos = Insumo::select('insumo_id', 'costo')->pluck('costo', 'insumo_id')->toArray();
        $this->cops = Oficina::where('tipo_oficina_id', 4)->where('externa', false)->orderBy('oficina_id')->get();
        
    }
    public function exportarInventarioGeneral()
    {
        $inventarios = InventarioInsumo::orderBy('insumo_inventario_id')->get();
        return Excel::download(new InventarioGeneralExport($inventarios), 'Inventario_General_Insumos.xlsx');
    }

    public function exportarInventarioGeneralPdf()
    {
        $inventarios = \App\Models\InventarioInsumo::orderBy('insumo_inventario_id')->get();
        $pdf = Pdf::loadView('pdf.inventario-general', compact('inventarios'));
        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'Inventario_General_Insumos.pdf'
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
        $this->modalOpen3 = true;
    }

    public function openModal4()
    {
        $this->modalOpen4 = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->modalOpen2 = false;
        $this->modalOpen3 = false;
        $this->modalOpen4 = false;
        $this->cop = '';
        $this->opt = '';
        $this->central = '';
        $this->opts = [];
        $this->insumo_selec = [];
        $this->cantidad_por_insumo = [];
    }

    public function updatedCop()
    {
        if(!$this->cop){
            $this->opts = [];
        }else{
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
                $inventario = InventarioInsumo::where('oficina_id', null)
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if ($inventario) {
                    // Si el insumo ya está registrado, se suma la cantidad
                    $inventario->cantidad += $cantidad;
                    $inventario->save();
                } else {
                    // Si el insumo no está registrado, se crea un nuevo registro
                    InventarioInsumo::create([
                        'oficina_id' => null,
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
        }
        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se ingresaron insumos (" . count($this->insumo_selec) . " tipo(s)) al inventario general",
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

            $inventario = InventarioInsumo::where('oficina_id', null)
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
                $inventario_actual = InventarioInsumo::where('oficina_id', null)
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
                'oficina_origen' => null,
                'oficina_destino' => $this->cop,
                'coste' => $this->costos[$insumo_id],
            ];

            $this->registros();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se transfirieron insumos (" . count($this->insumo_selec) . " tipo(s)) del inventario general a la oficina ({$this->cop})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos transferidos correctamente!');
    }

    public function transferir_insumos_opt()
    {
        if (empty($this->cop) || empty($this->opt)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar una COP y una OPT!');
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

            $inventario = InventarioInsumo::where('oficina_id', null)
                ->where('insumo_id', $insumo_id)
                ->first();

            if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_id]) {
                $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente en el inventario para transferir el insumo seleccionado.');
                return;
            }
        }

        foreach ($this->insumo_selec as $insumo_id) {
            $cantidad = $this->cantidad_por_insumo[$insumo_id];

            if ($cantidad > 0) {
                $inventario_actual = InventarioInsumo::where('oficina_id', null)
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
                'oficina_origen' => null,
                'oficina_destino' => $this->opt,
                'coste' => $this->costos[$insumo_id],
            ];

            $this->registros();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se transfirieron insumos (" . count($this->insumo_selec) . " tipo(s)) del inventario general a la OPT ({$this->opt})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos transferidos correctamente!');
    }

    public function transferir_insumos_centralizadora()
    {
        if (empty($this->central)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar una centralizadora de destino!');
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

            $inventario = InventarioInsumo::where('oficina_id', null)
                ->where('insumo_id', $insumo_id)
                ->first();

            if (!$inventario || $inventario->cantidad < $this->cantidad_por_insumo[$insumo_id]) {
                $this->dispatch('alertSuccess2', message: 'Cantidad insuficiente en el inventario para transferir el insumo seleccionado.');
                return;
            }
        }

        foreach ($this->insumo_selec as $insumo_id) {
            $cantidad = $this->cantidad_por_insumo[$insumo_id];

            if ($cantidad > 0) {
                // Descontar la cantidad del inventario de la oficina actual
                $inventario_actual = InventarioInsumo::where('oficina_id', null)
                    ->where('insumo_id', $insumo_id)
                    ->first();

                $inventario_actual->cantidad -= $cantidad;
                $inventario_actual->save();

                // Agregar la cantidad al inventario de la oficina de destino
                $inventario_destino = InventarioInsumo::where('oficina_id', $this->central)
                    ->where('insumo_id', $insumo_id)
                    ->first();

                if ($inventario_destino) {
                    $inventario_destino->cantidad += $cantidad;
                    $inventario_destino->save();
                } else {
                    InventarioInsumo::create([
                        'oficina_id' => $this->central,
                        'insumo_id' => $insumo_id,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
            $this->registro_transferencia[] = [
                'insumo' => $insumo_id,
                'cantidad' => $cantidad,
                'oficina_origen' => null,
                'oficina_destino' => $this->central,
                'coste' => $this->costos[$insumo_id],
            ];

            $this->registros();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se transfirieron insumos (" . count($this->insumo_selec) . " tipo(s)) del inventario general a la centralizadora ({$this->central})",
        ]);

        $this->closeModal();
        $this->dispatch('alertSuccess', message: 'Insumos transferidos correctamente!');
    }

    public function limpiarExistencias($inventario_id)
    {
        DB::beginTransaction();

        try {
            $inventario = InventarioInsumo::where('insumo_inventario_id', $inventario_id)
                ->whereNull('oficina_id')
                ->first();

            if (!$inventario) {
                $this->dispatch('alertSuccess2', message: 'No se encontró el registro de inventario.');
                return;
            }

            $insumo_id = $inventario->insumo_id;

            $inventario->cantidad = 0;
            $inventario->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se limpiaron existencias del insumo ({$insumo_id}) en el inventario general",
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
        foreach($this->registro_transferencia as $registro){
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
        $inventarios = InventarioInsumo::with('insumo')->where('oficina_id', null)->orderBy('insumo_id')->get();

        return view('livewire.inventario.inventario-general', ['inventarios'=>$inventarios]);
    }
}
