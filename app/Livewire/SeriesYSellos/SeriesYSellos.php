<?php

namespace App\Livewire\SeriesYSellos;
use App\Models\Sello;
use App\Models\SerieFilatelia;
use App\Models\UsuarioSeguimiento;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination; 

#[Layout('layouts.app')]
class SeriesYSellos extends Component
{
    use WithPagination;

    public $serie, $sello, $serie_sello, $nombre_serie, $nombre_sello, $coste;

    public $series = [];
    public $modal_open = false;
    public $modal_open2 = false;
    public $perPage = 10;
    public $sellos_modal = [['nombre' => '', 'precio' => '']];
    public $is_edit = false; 
    public $sello_id_actual;
    public $nombre_sello_edit;
    public $precio_sello_edit;

    // ----- NUEVAS PROPIEDADES PARA GESTIÓN DE SERIES -----
    public $modal_series_open = false;
    public $series_manage_list = [];
    public $selected_serie_id = null;
    public $selected_serie_name_edit = '';
    public $selected_serie_active_edit = false;
    // ----------------------------------------------------

    public function mount()
    {
        $this->series = SerieFilatelia::where('activo', true)->get();
    }

    public function agregar_sello()
    {
        $this->sellos_modal[] = ['nombre' => '', 'precio' => ''];
    }

    public function crear_serie()
    {
        $this->modal_open = true;
    }

    private function castPrecio($precio)
    {
        $precio = str_replace('.', '', $precio); 
        $precio = str_replace(',', '.', $precio);
        return is_numeric($precio) ? (float) $precio : null;
    }

    public function crear_sellos()
    {
         // Normalizar todos los precios
        foreach ($this->sellos_modal as $i => $sello) {
            $this->sellos_modal[$i]['precio'] = $this->castPrecio($sello['precio']);
        }

         $this->validate([
        'serie_sello' => 'required',
        'sellos_modal.*.nombre' => 'required|string|min:3|max:70',
        'sellos_modal.*.precio' => 'required|numeric|min:0',
        ], [
            'serie_sello.required' => 'Debe seleccionar una serie.',
            'sellos_modal.*.nombre.required' => 'El nombre del sello es obligatorio.',
            'sellos_modal.*.nombre.min' => 'El nombre del sello debe tener al menos 3 caracteres.',
            'sellos_modal.*.precio.required' => 'El precio del sello es obligatorio.',
            'sellos_modal.*.precio.numeric' => 'El precio debe ser un número válido.',
        ]);
        
            DB::beginTransaction();

        try {
           foreach($this->sellos_modal as $sello)
        {
            Sello::create([
                'nombre' => $sello['nombre'],
                'serie_filatelia_id' => $this->serie_sello,
                'coste' => $sello['precio'],
                'activo' => true,
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se generaron " . count($this->sellos_modal) . " sello(s) en la serie ({$this->serie_sello})",
        ]);

        DB::commit();
            $this->dispatch('alertSuccess', message: 'Sellos Generados exitosamente!');
            $this->dispatch('recargar');

        }catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique los datos!');
        }

    }


    public function ingresar_serie()
    {
        $this->validate([
            'nombre_serie' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $nueva_serie = SerieFilatelia::create([
                'nombre' => $this->nombre_serie,
                'activo' => true,

            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó la serie filatelia ({$nueva_serie->serie_filatelia_id}) con nombre '{$this->nombre_serie}'",
            ]);

        DB::commit();
            $this->dispatch('alertSuccess', message: 'Serie Generada exitosamente!');
            $this->dispatch('recargar');

        }catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique los datos!');
        }

    }

    public function estatus($id)
    {
        $sello = Sello::find($id);

        if($sello){
            $sello->activo = !$sello->activo;

            $sello->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se cambió el estatus del sello ({$sello->sello_id}) a " . ($sello->activo ? 'activo' : 'inactivo'),
            ]);
        }

        $this->dispatch('alertSuccess', message: 'Estatus del Sello cambiado correctamente!');
    }

    public function gestionar_series()
    {
        $this->series_manage_list = SerieFilatelia::orderBy('nombre')->get()->toArray();
        $this->series_manage_list = SerieFilatelia::orderBy('nombre')->get();
        $this->modal_series_open = true;
        $this->selected_serie_id = null;
        $this->selected_serie_name_edit = '';
        $this->selected_serie_active_edit = false;
    }

    public function modal_series_close()
    {
        $this->modal_series_open = false;
        $this->selected_serie_id = null;
        $this->selected_serie_name_edit = '';
        $this->selected_serie_active_edit = false;
    }

    public function cargar_serie_editar($id)
    {
        $serie = SerieFilatelia::find($id);
        if(!$serie) {
            $this->dispatch('alertSuccess2', message: 'Serie no encontrada.');
            return;
        }

        $this->selected_serie_id = $serie->serie_filatelia_id;
        $this->selected_serie_name_edit = $serie->nombre;
        $this->selected_serie_active_edit = (bool) $serie->activo;
        $this->modal_series_open = true;
    }

    public function toggle_serie($id)
    {
        $serie = SerieFilatelia::find($id);
        if(!$serie){
            $this->dispatch('alertSuccess2', message: 'Serie no encontrada.');
            return;
        }

        $serie->activo = !$serie->activo;
        $serie->save();
        $this->series_manage_list = SerieFilatelia::orderBy('nombre')->get();
        $this->series = SerieFilatelia::where('activo', true)->get();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus de la serie filatelia ({$serie->serie_filatelia_id}) a " . ($serie->activo ? 'activo' : 'inactivo'),
        ]);

        $this->dispatch('alertSuccess', message: 'Estatus de la serie actualizado correctamente!');
    }

    public function toggle_selected_serie_active()
    {
        $this->selected_serie_active_edit = !$this->selected_serie_active_edit;
    }

    public function actualizar_serie()
    {
        if(!$this->selected_serie_id){
            $this->dispatch('alertSuccess2', message: 'No hay serie seleccionada para actualizar.');
            return;
        }

        $this->validate([
            'selected_serie_name_edit' => 'required|string|min:2|max:100',
        ], [
            'selected_serie_name_edit.required' => 'El nombre de la serie es obligatorio.',
            'selected_serie_name_edit.min' => 'El nombre debe tener al menos 2 caracteres.',
        ]);

        DB::beginTransaction();
        try {
            $serie = SerieFilatelia::find($this->selected_serie_id);
            if(!$serie){
                DB::rollback();
                $this->dispatch('alertSuccess2', message: 'Serie no encontrada.');
                return;
            }

            $serie->update([
                'nombre' => $this->selected_serie_name_edit,
                'activo' => $this->selected_serie_active_edit ? true : false,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó la serie filatelia ({$serie->serie_filatelia_id})",
            ]);

            DB::commit();
            $this->series_manage_list = SerieFilatelia::orderBy('nombre')->get();
            $this->series = SerieFilatelia::where('activo', true)->get();
            $this->selected_serie_id = null;
            $this->selected_serie_name_edit = '';
            $this->selected_serie_active_edit = false;

            $this->dispatch('alertSuccess', message: 'Serie actualizada correctamente!');
            $this->dispatch('recargar');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al actualizar la serie.');
        }
    }

    // ----------------------------------------------------

    public function render()
    {
        if(!$this->serie){
            $sellos = Sello::orderBy('sello_id')->paginate($this->perPage);
        }else{
            $sellos = Sello::where('serie_filatelia_id', $this->serie)->orderBy('sello_id')->paginate($this->perPage);
        }
        

        return view('livewire.series-y-sellos.series-y-sellos', ['sellos' => $sellos]);
    }

    // CARGA DE DATOS PARA EDITAR
    public function edit_sello($id)
    {
        $sello_data = Sello::findOrFail($id);
        $this->sello_id_actual = $id;
        $this->serie_sello = $sello_data->serie_filatelia_id;
        $this->nombre_sello_edit = $sello_data->nombre;
        $this->precio_sello_edit = number_format($sello_data->coste, 2, ',', '.');
        
        $this->is_edit = true;
    }

    // ACCIÓN DE ACTUALIZAR 
    public function actualizar_sello()
    {
        // Castea el precio antes de validar
        $precio_cast = $this->castPrecio($this->precio_sello_edit);

        $this->validate([
            'serie_sello' => 'required',
            'nombre_sello_edit' => 'required|string|min:3|max:70',
            'precio_sello_edit' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $sello_update = Sello::find($this->sello_id_actual);
            $sello_update->update([
                'nombre' => $this->nombre_sello_edit,
                'serie_filatelia_id' => $this->serie_sello,
                'coste' => $precio_cast,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó el sello ({$sello_update->sello_id})",
            ]);

            DB::commit();
            $this->is_edit = false;
            $this->dispatch('alertSuccess', message: 'Sello actualizado exitosamente!');
            $this->dispatch('recargar');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al actualizar!');
        }
    }

    public function closeModal()
    {
        $this->is_edit = false;
    }

    public function modalClose()
    {
        $this->modal_open2 = false;
        $this->nombre_serie = '';
    }
}
