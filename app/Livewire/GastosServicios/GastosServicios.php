<?php

namespace App\Livewire\GastosServicios;
use Carbon\Carbon;
use App\Models\Oficina;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServicioPublico;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\PagoServicioPublico;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class GastosServicios extends Component
{
    public $mes, $fecha_registro, $monto, $monto_final, $servicio, $desde, $hasta;

    public $usuario = [];
    public $oficina = [];
    public $servicios = [];
    public $estatus = false;
    public $registro = false;
    public $tipo_registro = true;
    public $perPage = 10;


    public function mount()
    {
        $this->mes = now()->format('Y-m');
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->servicios = ServicioPublico::all();
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
        $this->servicio = '';
    }

    public function updatedMonto()
    {
        $this->monto_final = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));
    }

    public function ingresar_nuevo_gasto()
    {
        $this->validate([
            'tipo_registro' => 'required',
            'servicio' => 'required',
            'fecha_registro' => 'required',
            'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
        ]);

        DB::beginTransaction();

        try {

            $registro_existente = PagoServicioPublico::where('servicio_publico_id', $this->servicio)
            ->where('oficina_id', $this->oficina['oficina_id'])
            ->whereRaw("TO_CHAR(mensualidad, 'YYYY-MM') = ?", [
                Carbon::parse($this->fecha_registro)->format('Y-m')
            ])
            ->first();

            if($registro_existente){
                if ($registro_existente->estatus != $this->tipo_registro) {
                    // Se permite actualizar el estatus (de deuda a pagado o viceversa)
                    $registro_existente->update([
                        'monto' => $this->monto_final,
                        'estatus' => $this->tipo_registro,
                        'usuario_id' => $this->usuario['id'],
                    ]);
                } else {
                    $this->dispatch('alertSuccess2', message: 'Ya existe un registro para este servicio en este mes!');
                    return;
                }
            }else{
                // No existe registro, se puede crear
                PagoServicioPublico::create([
                    'oficina_id' => $this->oficina['oficina_id'],
                    'usuario_id' => $this->usuario['id'],
                    'servicio_publico_id' => $this->servicio,
                    'mensualidad' => $this->fecha_registro,
                    'monto' => $this->monto_final,
                    'estatus' => $this->tipo_registro
                ]);
            }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => isset($registro_existente) && $registro_existente ? 'update' : 'create',
            'descripcion' => "Se " . (isset($registro_existente) && $registro_existente ? 'actualizó' : 'registró') . " el pago de servicio público ({$this->servicio}) por Bs {$this->monto_final}",
        ]);

        DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Registro Creado exitosamente!');

        } catch (\Exception $e) {
        //    dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del registro, verifique los datos e intente de nuevo');
             // Si ocurre un error, revertimos todos los cambios
        }
    }

    public function render()
    {
        $pagos = PagoServicioPublico::where('estatus', $this->estatus)->where('oficina_id', $this->oficina['oficina_id'])
        ->when($this->desde && $this->hasta, function ($query) {
            $query->whereRaw("TO_CHAR(mensualidad, 'YYYY-MM') BETWEEN ? AND ?", [
                $this->desde,
                $this->hasta
            ]);
        })
        ->orderBy('mensualidad', 'desc')->paginate($this->perPage);

        return view('livewire.gastos-servicios.gastos-servicios', compact('pagos'));
    }
}
