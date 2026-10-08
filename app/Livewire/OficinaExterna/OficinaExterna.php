<?php

namespace App\Livewire\OficinaExterna;

use App\Models\Estado;
use App\Models\InformacionAliado;
use App\Models\Municipio;
use App\Models\Oficina;
use App\Models\Parroquia;
use App\Models\Sector;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class OficinaExterna extends Component
{
    public $search, $nombre, $estado, $municipio, $parroquia, $codigo_postal, $RIF, $nro_contrato, 
    $fecha_contratacion, $tarifa_aplicada, $tipo_facturacion, $tiempo_entrega_zona, $tiempo_entrega_estado;

    public $modal = false;
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $codigos = [];
    public $modal_editar = false;
    public $editar_oficina_id;
    public $editar_nombre;
    public $editar_RIF;
    public $editar_nro_contrato;
    public $editar_fecha_contratacion;
    public $editar_tarifa_aplicada;
    public $editar_tipo_facturacion;
    public $editar_tiempo_entrega_zona;
    public $editar_tiempo_entrega_estado;

    public function mount()
    {
        $this->estados = Estado::all();
    }

    public function updatedEstado()
    {
        if($this->estado){
            $this->municipios = Municipio::where('estado_id', $this->estado)->get();
        }else{
            $this->municipios = [];
        }
    }

    public function updatedMunicipio()
    {
        if($this->municipio){
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio)->get();
        }else{
            $this->parroquias = [];
        }
    }

    public function updatedParroquia()
    {
        if($this->parroquia){
            $this->codigos = Sector::where('parroquia_id', $this->parroquia)->select('codigo_postal')->distinct()->get();
        }else{
            $this->codigos = [];
        }
    }

    public function crear()
    {
        $this->limpiarFormularioCrear();
        $this->modal = true;
    }

    public function cerrarModalCrear()
    {
        $this->limpiarFormularioCrear();
        $this->modal = false;
    }

    private function limpiarFormularioCrear()
    {
        $this->reset(['nombre', 'estado', 'municipio', 'parroquia', 'codigo_postal',
            'RIF', 'nro_contrato', 'fecha_contratacion', 'tarifa_aplicada',
            'tipo_facturacion', 'tiempo_entrega_zona', 'tiempo_entrega_estado']);
        $this->municipios = [];
        $this->parroquias = [];
        $this->codigos = [];
        $this->resetValidation();
    }

    public function cerrarModalEditar()
    {
        $this->reset(['editar_oficina_id', 'editar_nombre', 'editar_RIF', 'editar_nro_contrato',
            'editar_fecha_contratacion', 'editar_tarifa_aplicada', 'editar_tipo_facturacion',
            'editar_tiempo_entrega_zona', 'editar_tiempo_entrega_estado']);
        $this->resetValidation();
        $this->modal_editar = false;
    }


    public function crear_oficina()
    {
        $this->validate([
            'nombre' => 'required|min:3|max:50',
            'estado' => 'required|integer',
            'municipio' => 'required|integer',
            'parroquia' => 'required|integer',
            'codigo_postal' => 'required',
            'RIF' => 'required|regex:/^[A-Za-z]\d{9}$/',
            'nro_contrato' => 'required|max:50',
            'fecha_contratacion' => 'required|date',
            'tarifa_aplicada' => 'required|numeric|min:0',
            'tipo_facturacion' => 'required|max:100',
            'tiempo_entrega_zona' => 'required|max:100',
            'tiempo_entrega_estado' => 'required|max:100',
        ]);

        DB::beginTransaction();

        try{

        $data = Oficina::create([
                'nombre' => $this->nombre,
                'estado_id' => $this->estado,
                'municipio_id' => $this->municipio,
                'parroquia_id' => $this->parroquia,
                'codigo' => 'N/A',
                'codigo_ubicacion' => $this->codigo_postal,
                'tipo_oficina_id' => 4,
                'externa' => true,
                'estatus_id' =>  1,
                'zona_economica_especial' => false,
                'operaciones' => false,
            ]);

            InformacionAliado::create([
                'oficina_id' => $data->oficina_id,
                'RIF' => $this->RIF,
                'nro_contrato' => $this->nro_contrato,
                'fecha_contratacion' => $this->fecha_contratacion,
                'tarifa_aplicada' => $this->tarifa_aplicada,
                'tipo_facturacion' => $this->tipo_facturacion,
                'tiempo_entrega_zona' => $this->tiempo_entrega_zona,
                'tiempo_entrega_estado' => $this->tiempo_entrega_estado,
            ]);


            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó la oficina aliada externa ({$data->oficina_id}) '{$this->nombre}'",
            ]);

            DB::commit();
            $this->limpiarFormularioCrear();
            $this->modal = false;
            $this->dispatch('alertSuccess', message: 'Oficina Aliada Creada!');

        }catch (\Exception $e){
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Error al crear la oficina, compruebe los datos e intente de nuevo!');
        }
    }

    // --- MÉTODO PARA ABRIR MODAL DE EDICIÓN ---
    public function editar($oficina_id)
    {
        $oficina = Oficina::find($oficina_id);
        if (!$oficina) {
            $this->dispatch('alertSuccess2', message: 'Oficina no encontrada.');
            return;
        }

        $aliado = InformacionAliado::where('oficina_id', $oficina_id)->first();

        $this->editar_oficina_id = $oficina->oficina_id;
        $this->editar_nombre = $oficina->nombre;
        $this->editar_RIF = $aliado->RIF ?? '';
        $this->editar_nro_contrato = $aliado->nro_contrato ?? '';
        $this->editar_fecha_contratacion = $aliado->fecha_contratacion ?? '';
        $this->editar_tarifa_aplicada = $aliado->tarifa_aplicada ?? '';
        $this->editar_tipo_facturacion = $aliado->tipo_facturacion ?? '';
        $this->editar_tiempo_entrega_zona = $aliado->tiempo_entrega_zona ?? '';
        $this->editar_tiempo_entrega_estado = $aliado->tiempo_entrega_estado ?? '';
        $this->modal_editar = true;
    }

    // --- MÉTODO PARA GUARDAR LOS CAMBIOS ---
    public function actualizar_oficina()
    {
        $this->validate([
            'editar_nombre' => 'required|min:3|max:50',
            'editar_RIF' => 'required|regex:/^[A-Za-z]\d{9}$/',
            'editar_nro_contrato' => 'required|max:50',
            'editar_fecha_contratacion' => 'required|date',
            'editar_tarifa_aplicada' => 'required|numeric|min:0',
            'editar_tipo_facturacion' => 'required|max:100',
            'editar_tiempo_entrega_zona' => 'required|max:100',
            'editar_tiempo_entrega_estado' => 'required|max:100',
        ]);

        DB::beginTransaction();
        try {
            $oficina = Oficina::find($this->editar_oficina_id);
            if (!$oficina) {
                throw new \Exception('Oficina no encontrada');
            }

            $oficina->update([
                'nombre' => $this->editar_nombre,
            ]);

            InformacionAliado::updateOrCreate(
                ['oficina_id' => $this->editar_oficina_id],
                [
                    'RIF' => $this->editar_RIF,
                    'nro_contrato' => $this->editar_nro_contrato,
                    'fecha_contratacion' => $this->editar_fecha_contratacion,
                    'tarifa_aplicada' => $this->editar_tarifa_aplicada,
                    'tipo_facturacion' => $this->editar_tipo_facturacion,
                    'tiempo_entrega_zona' => $this->editar_tiempo_entrega_zona,
                    'tiempo_entrega_estado' => $this->editar_tiempo_entrega_estado,
                ]
            );

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó la oficina aliada externa ({$this->editar_oficina_id})",
            ]);

            DB::commit();
            $this->modal_editar = false;
            $this->dispatch('alertSuccess', message: 'Oficina aliada actualizada!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Error al actualizar la oficina, compruebe los datos e intente de nuevo!');
        }
    }

    public function render()
    {
        $externas = Oficina::where('tipo_oficina_id', 4)
        ->where('externa', true)
        ->when($this->search, function ($query) {
            $search = strtolower(trim($this->search));
            $query->whereRaw('LOWER(TRIM(nombre)) LIKE ?', ['%' . $search . '%']);
        })->paginate();

        return view('livewire.oficina-externa.oficina-externa', ['externas' => $externas]);
    }
}
