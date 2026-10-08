<?php

namespace App\Livewire\GastosOperativos;

use App\Models\Oficina;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GastoOperativo;
use Livewire\Attributes\Layout;
use App\Models\TipoGastoOperativo;
use App\Models\UsuarioSeguimiento;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GastoOperativoExport;

#[Layout('layouts.app')]
class GastosOperativos extends Component
{
    use WithPagination;
    public $desde, $hasta, $search, $nuevo_gasto, $monto, $gasto_selec;

    public $Page = 10;
    public $usuario = [];
    public $oficina = [];
    public $tipos_gastos = [];
    public $crear_gasto = false;
    public $ingresar_gasto = false;

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->tipos_gastos = TipoGastoOperativo::all();
    }
    public function exportarGastosOperativos()
    {
        $gastos = GastoOperativo::where('oficina_id', $this->oficina['oficina_id'])
            ->when($this->desde, function ($q) {
                $q->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->hasta);
            })
            // Si quieres filtrar por búsqueda, agrega aquí el filtro
            ->orderBy('created_at', 'desc')
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new GastoOperativoExport($gastos),
            'Gastos_Operativos.xlsx'
        );
    }

    public function cerrar()
    {
        $this->crear_gasto = false;
        $this->ingresar_gasto = false;

        $this->gasto_selec = [];
        $this->monto = '';
    }

    public function crear()
    {
        $this->crear_gasto = true;
    }

    public function ingresar()
    {
        $this->ingresar_gasto = true;
    }

    public function crear_nuevo_gasto()
    {
        $this->validate([
            'nuevo_gasto' => 'required|min:5|max:30',
        ], [
            'nuevo_gasto.required' => 'El campo es obligatorio.',
            'nuevo_gasto.min' => 'Debe tener al menos 5 caracteres.',
            'nuevo_gasto.max' => 'No puede superar los 30 caracteres.',
        ]);

        $nuevo_tipo = TipoGastoOperativo::create([
            'tipo_gasto_operativo' => $this->nuevo_gasto,
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se creó el tipo de gasto operativo ({$nuevo_tipo->getKey()}) '{$this->nuevo_gasto}'",
        ]);

        $this->dispatch('alertSuccess', message: 'Tipo de Gasto Creado Correctamente!');
        $this->nuevo_gasto = '';
    }

    public function ingresar_nuevo_gasto()
    {
        $this->validate([
            'gasto_selec' => 'required',
            'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/'
        ], [
            'gasto_selec.required' => 'El campo es obligatorio.',
            'monto.required' => 'El campo es obligatorio.',
            'monto.regex' => 'Solo cantidades numericas.',
        ]);

        $this->monto = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));

        $nuevo_gasto = GastoOperativo::create([
            'oficina_id' => $this->usuario['oficina_id'],
            'usuario_id' => $this->usuario['id'],
            'tipo_gasto_operativo_id' => $this->gasto_selec,
            'monto' => $this->monto
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró el gasto operativo ({$nuevo_gasto->getKey()}) por Bs {$this->monto}",
        ]);

        $this->dispatch('alertSuccess', message: 'Gasto Operativo Ingresado con Exito');
    }


    public function render()
    {
        $gastos = GastoOperativo::Where('oficina_id', $this->oficina['oficina_id'])
            ->when($this->desde, function ($q) {
                $q->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->hasta);
            })
            // ->when($this->search, function ($q) {
            //     $q->where(function ($subquery) {
            //         $subquery->where('gasto', 'like', '%' . $this->search . '%')
            //                 ->orWhere('documento_dest', 'like', '%' . $this->search . '%')
            //                 ->orWhere('codigo_envio', 'like', '%' . $this->search . '%');
            //     });
            // })
            ->orderBy('created_at', 'desc')
            ->paginate($this->Page);

        return view('livewire.gastos-operativos.gastos-operativos',  compact('gastos'));
    }
}
