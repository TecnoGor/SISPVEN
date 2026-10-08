<?php

namespace App\Livewire\ArrendamientoOficina;

use Carbon\Carbon;
use App\Models\Oficina;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\GastoArrendamiento;
use Illuminate\Support\Facades\DB;
use App\Models\OficinaSemaforoPostal;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class ArrendamientoOficina extends Component
{
    public $fecha_registro, $monto, $monto_final, $desde, $hasta;

    public $usuario = [];
    public $oficina = [];
    public $estatus = false;
    public $registro = false;
    public $tipo_registro = "1";
    public $perPage = 10;

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
    }


    public function registrar()
    {
        $this->registro = true;
    }

    public function cerrar()
    {
        $this->registro = false;
        $this->fecha_registro = ''; 
        $this->monto = ''; 
        $this->monto_final = ''; 
    }

    public function updatedMonto()
    {
        $this->monto_final = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));
    }

    public function ingresar()
    {
        $this->validate([
            'tipo_registro' => 'required',
            'fecha_registro' => 'required',
            'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
        ]);

        DB::beginTransaction();

        try {

            $registro_existente = GastoArrendamiento::where('oficina_id', $this->oficina['oficina_id'])
            ->whereRaw("TO_CHAR(fecha, 'YYYY-MM') = ?", [
                Carbon::parse($this->fecha_registro)->format('Y-m')
            ])->first();

            if($registro_existente){
                if ($registro_existente->estatus != $this->tipo_registro) {
                    // Se permite actualizar el estatus (de deuda a pagado o viceversa)
                    $registro_existente->update([
                        'monto' => $this->monto_final,
                        'estatus' => $this->tipo_registro,
                        'usuario_id' => $this->usuario['id'],
                    ]);
                } else {
                    $this->dispatch('alertSuccess2', message: 'Ya existe un registro de pago para la oficina este mes!');
                    return;
                }
            }else{
                GastoArrendamiento::create([
                    'oficina_id' => $this->oficina['oficina_id'],
                    'usuario_id' => $this->usuario['id'],
                    'fecha' => $this->fecha_registro,
                    'monto' => $this->monto_final,
                    'estatus' => $this->tipo_registro
                ]);

                $this->fecha_registro = '';
                $this->monto = '';
                $this->monto_final = '';
            }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => isset($registro_existente) && $registro_existente ? 'update' : 'create',
            'descripcion' => "Se " . (isset($registro_existente) && $registro_existente ? 'actualizó' : 'registró') . " el gasto de arrendamiento de la oficina ({$this->oficina['oficina_id']}) por Bs {$this->monto_final}",
        ]);

        DB::commit();
            $this->dispatch('alertSuccess', message: 'Registro Creado exitosamente!');

        } catch (\Exception $e) {
        //    dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del registro, verifique los datos e intente de nuevo');
        }
    }


    public function render()
    {
        $pagos = GastoArrendamiento::where('oficina_id', $this->oficina['oficina_id'])->where('estatus', $this->estatus)
        ->when($this->desde && $this->hasta, function ($query){
            $query->whereRaw("TO_CHAR(fecha, 'YYYY-MM') BETWEEN ? AND ?", [
                $this->desde,
                $this->hasta
            ]);
        })
        ->orderBy('fecha', 'desc')->paginate();

        

        $condiciones = OficinaSemaforoPostal::getCondiciones();
        return view('livewire.arrendamiento-oficina.arrendamiento-oficina', ['pagos' => $pagos]);
    }
}
