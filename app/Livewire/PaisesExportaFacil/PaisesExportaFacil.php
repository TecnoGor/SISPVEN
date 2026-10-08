<?php

namespace App\Livewire\PaisesExportaFacil;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Pais;
use App\Models\Continente;
use App\Models\TarifaExportaFacil;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;


#[Layout('layouts.app')]
class PaisesExportaFacil extends Component
{
    use WithPagination;
    public $pais_id;
    public $buscar = '';
    public $input = 5;
    public $continentes = [];
    public $continente;
    public $update = false;
    public $modal_tarifa = false;
    public $info = [];
    public $tarifa_nueva = 0.00;
    public $tarifa_convertida = 0;

    public function mount()
    {
        $this->continentes = Continente::all();
    }

    public function updatedTarifaNueva()
    {
        $this->tarifa_convertida = floatval(str_replace(',', '.', str_replace('.', '', $this->tarifa_nueva)));
    }

    public function editar($pais_id)
    {
        $pais = Pais::find($pais_id);
        if ($pais) {
            $pais->exporta_facil = !$pais->exporta_facil;
            $pais->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se " . ($pais->exporta_facil ? 'activó' : 'desactivó') . " Exporta Fácil para el país ({$pais->pais_id})",
            ]);

            $this->resetPage(); // Refresca la paginación y la vista
            $this->dispatch('alertSuccess', message: 'Estado de Exporta Fácil actualizado exitosamente!');
        }
    }

    public function actualizar_tarifa($pais)
    {
        $this->modal_tarifa = true;
        $this->info = Pais::where('pais_id', $pais)->first();

        $this->tarifa_nueva = $this->info->tarifa?->monto ?? 0.00;
    }

    public function cambiar_tarifa()
    {
        $nueva_tarifa = TarifaExportaFacil::where('pais_id', $this->info['pais_id'])->first();

        if($nueva_tarifa){
            $nueva_tarifa->monto = $this->tarifa_convertida;
            $nueva_tarifa->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó la tarifa Exporta Fácil del país ({$this->info['pais_id']}) a {$this->tarifa_convertida}",
            ]);
        }else{
            TarifaExportaFacil::create([
                'pais_id' => $this->info['pais_id'],
                'monto' => $this->tarifa_convertida,
                'activo' => true,
                'medida_id' => 1,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó la tarifa Exporta Fácil del país ({$this->info['pais_id']}) con monto {$this->tarifa_convertida}",
            ]);
        }
        $this->dispatch('alertSuccess', message: 'Tarifa actualizada correctamente!');
        $this->cerrar_tarifa();
    }

    public function cerrar_tarifa()
    {
        $this->modal_tarifa = false;
        $this->info = [];
        $this->tarifa_nueva = 0;
        $this->tarifa_convertida = 0;
    }

    public function render()
    {
        if ($this->continente == null) {
            $pais = Pais::orderBy('pais_id', 'asc')
                ->where('nombre', 'like', '%' . $this->buscar . '%')->paginate($this->input);
        } else {
            $pais = Pais::orderBy('pais_id', 'asc')
                ->where('nombre', 'like', '%' . $this->buscar . '%')->where('continente_id', $this->continente)->paginate($this->input);
        }
        return view('livewire.paises-exporta-facil.paises-exporta-facil', ['pais' => $pais]);
    }
}
