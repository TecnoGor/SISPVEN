<?php

namespace App\Livewire\ModificarInformacionOficina;

use App\Models\Sector;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Parroquia;
use App\Rules\CodigosTelefono;
use Livewire\Attributes\Layout;
use App\Models\OficinaSemaforoPostal;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class ModificarInformacionOficina extends Component
{
    public $oficina_id, $nombre, $correo, $telefono, $parroquia, $codigo_postal, $latitud, $longitud, $zona_economica, $direccion, $condicion,
        $fecha_inicial, $fecha_final, $centralizadora;
    public $oficina = [];
    public $parroquias = [];
    public $condiciones = [];
    public $codigos_postales = [];
    public $modal = false;

    public function mount($id)
    {
        $oficina = Oficina::findOrFail($id);
        $this->oficina = $oficina;
        $this->oficina_id = $oficina->oficina_id;
        $this->nombre = $oficina->nombre;
        $this->correo = $oficina->correo;
        $this->telefono = $oficina->telefono;
        $this->parroquia = $oficina->parroquia_id;
        $this->codigo_postal = $oficina->codigo_ubicacion;
        $this->latitud = $oficina->latitud;
        $this->longitud = $oficina->longitud;
        $this->zona_economica = $oficina->zona_economica_especial;
        $this->direccion = $oficina->direccion;
        $this->centralizadora = $oficina->centralizadora;
        $this->parroquias = Parroquia::where('municipio_id', $oficina->municipio_id)->get();
        $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia)->distinct()->pluck('codigo_postal')->sort();
        $this->condiciones = OficinaSemaforoPostal::getCondiciones();

        $condicion_of = OficinaSemaforoPostal::where('oficina_id', $id)->first();

        if ($condicion_of) {
            $this->condicion = $condicion_of->condicion;
            $this->fecha_inicial = $condicion_of->fecha_inicio;
            $this->fecha_final = $condicion_of->fecha_fin;
        } else {
            $this->condicion = '';
            $this->fecha_inicial = '';
            $this->fecha_final = '';
        }

        $this->obtener_condicion();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:50',
            'correo' => 'nullable|email|max:40',
            'direccion' => 'nullable|string|max:255',
            'telefono' => ['nullable', new CodigosTelefono],
            'parroquia' => 'required|exists:parroquias,parroquia_id',
            'zona_economica' => 'required|boolean',
            'centralizadora' => 'required|boolean',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'codigo_postal' => 'required',
            // Validación para asegurar coherencia de fechas cuando la condición es 'Arrendada'
            'fecha_inicial' => $this->condicion === 'Arrendada'
                ? 'required|date|before_or_equal:fecha_final'
                : 'nullable|date',
            'fecha_final' => $this->condicion === 'Arrendada'
                ? 'required|date|after_or_equal:fecha_inicial'
                : 'nullable|date',
        ];
    }

    /**
     * Mensajes personalizados para validaciones de fecha (UX más clara).
     */
    public function messages()
    {
        return [
            'fecha_inicial.before_or_equal' => 'La fecha inicial no puede ser posterior a la fecha final.',
            'fecha_final.after_or_equal' => 'La fecha final no puede ser anterior a la fecha inicial.',
            'fecha_inicial.required' => 'La fecha inicial es requerida cuando la condición es Arrendada.',
            'fecha_final.required' => 'La fecha final es requerida cuando la condición es Arrendada.',
        ];
    }

    public function obtener_condicion()
    {
        if ($this->condicion === 'Arrendada') {
            $this->modal = true;
            // No limpiamos las fechas aquí porque se espera que el usuario las ingrese o ya existan.
        } else {
            $this->modal = false;
            // Limpiar las fechas para evitar valores huérfanos que puedan generar conflictos
            // con validaciones posteriores o con la lógica de negocio.
            $this->fecha_inicial = null;
            $this->fecha_final = null;
        }
    }

    public function updatedParroquia()
    {
        if ($this->parroquia == '') {
            $this->codigos_postales = [];
        } else {
            $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia)->distinct()->pluck('codigo_postal')->sort();
        }
    }

    public function guardar()
    {
        $this->validate();

        try {
            // Actualizar oficina
            $oficina = Oficina::findOrFail($this->oficina_id);

            if ($this->centralizadora == true && $oficina->tipo_oficina_id != 4) {
                $this->addError('centralizadora', 'Solo las oficinas de tipo COP pueden ser asignadas como centralizadoras.');
                return;
            }

            $oficina->update([
                'nombre' => $this->nombre,
                'correo' => $this->correo,
                'telefono' => $this->telefono,
                'parroquia_id' => $this->parroquia,
                'codigo_ubicacion' => $this->codigo_postal,
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
                'zona_economica_especial' => $this->zona_economica,
                'centralizadora' => $this->centralizadora,
                'direccion' => $this->direccion,
            ]);

            $fechas = in_array($this->condicion, [
                \App\Models\OficinaSemaforoPostal::CONDICION_ENACOMODATO,
                \App\Models\OficinaSemaforoPostal::CONDICION_PROPIA_IPOSTEL,
            ])
                ? [
                    'fecha_inicio' => null,
                    'fecha_fin' => null,
                ]
                : [
                    'fecha_inicio' => $this->fecha_inicial ?: null,
                    'fecha_fin' => $this->fecha_final ?: null,
                ];

            // Forzar actualización completa
            OficinaSemaforoPostal::updateOrCreate(
                ['oficina_id' => $this->oficina_id],
                [
                    'condicion' => $this->condicion,
                    ...$fechas,
                ]
            );

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se modificó la información de la oficina ({$this->oficina_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Cambios Realizados Correctamente!');
        } catch (\Exception $e) {
            // Si ocurre cualquier otro error (BD, etc.) se notifica con el dispatch existente
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, revise los campos e intente de nuevo!');
        }
    }

    public function render()
    {
        return view('livewire.modificar-informacion-oficina.modificar-informacion-oficina');
    }
}
