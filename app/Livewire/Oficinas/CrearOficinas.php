<?php

namespace App\Livewire\Oficinas;

use App\Models\CodigoPostal;
use App\Models\Estado;
use App\Models\EstatusOficina;
use App\Models\Municipio;
use App\Models\Oficina;
use App\Models\OficinaPersonal;
use App\Models\OficinaSemaforoPostal;
use App\Models\Parroquia;
use App\Models\RoleTipoOficina;
use App\Models\Sector;
use App\Models\ServicioOperativo;
use App\Models\TipoOficina;
use App\Models\TipoPago;
use App\Rules\CodigosTelefono;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;


#[Layout('layouts.app')]
class CrearOficinas extends Component
{
    public $tipo_oficina, $condicion_sel, $servicio_recibir, $codigos_postales_disponibles, $oficina_relacionada, $estadoss,
        $municipioss, $parroquiass, $cubicacion, $codigo, $tlf, $correo, $direccion, $latitud, $longitud, $nombre, $estatus_seleccionado, $fecha_inicio, $fecha_fin;
        
    public $zona_economica_especial = false;

    public $tipo_pago = [];
    public $rol_asignado = [];
    public $cantidad_por_rol = [];

    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $codigos_postales = [];
    public $condiciones = [];
    public $estatus = [];
    public $tipos_oficinas = [];
    public $tipos_pagos = [];
    public $servicios = [];
    public $servicios_operativos = [];
    public $codigos_postales_seleccionados = [];
    public $roles = [];
    public $serviciorecibir = 2;


    public function mount()
    {
        $this->servicios_operativos = ServicioOperativo::all();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->codigos_postales_disponibles = CodigoPostal::all();
        $this->tipos_pagos = TipoPago::all();
        $this->estatus = EstatusOficina::all();
        $this->tipos_oficinas = TipoOficina::whereNotIn('tipo_oficina_id', [5, 6, 7])->get();
        $this->condiciones = OficinaSemaforoPostal::getCondiciones();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:50',
            'correo' => 'nullable|email|max:40',
            'tipo_oficina' => 'required',
            'direccion' => 'nullable|string|max:255',
            'tlf' => ['nullable', new CodigosTelefono],
            'estadoss' => 'required|exists:estados,estado_id',
            'municipioss' => 'required|exists:municipios,municipio_id',
            'parroquiass' => 'required|exists:parroquias,parroquia_id',
            'zona_economica_especial' => 'required|boolean',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'estatus_seleccionado' => 'required',
            'condicion_sel' => 'required_if:tipo_oficina,1,2,3,4,5,6',
            'cubicacion' => 'required',
            'servicios' => 'required_if:tipo_oficina,1,2,3',
            'tipo_pago' => 'required_if:tipo_oficina,1,2,3',
            'rol_asignado' => 'required_if:tipo_oficina,1,2,3,4,5',
        ];
    }

    // Mensajes personalizados
    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.string' => 'El nombre solo debe contener texto.',
        'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',

        'correo.email' => 'El correo debe ser una dirección de correo válida.',
        'correo.max' => 'El correo no puede tener más de 40 caracteres.',

        'tipo_oficina.required' => 'El tipo de oficina es obligatorio.',

        'direccion.string' => 'La dirección debe ser una cadena de texto.',
        'direccion.max' => 'La dirección no puede tener más de 255 caracteres.',

        'estadoss.required' => 'El estado es obligatorio.',
        'estadoss.exists' => 'El estado seleccionado no es válido.',

        'municipioss.required' => 'El municipio es obligatorio.',
        'municipioss.exists' => 'El municipio seleccionado no es válido.',

        'parroquiass.required' => 'La parroquia es obligatoria.',
        'parroquiass.exists' => 'La parroquia seleccionada no es válida.',

        'zona_economica_especial.required' => 'La zona económica especial es obligatoria.',
        'zona_economica_especial.boolean' => 'La zona económica especial debe ser verdadero o falso.',

        'latitud.numeric' => 'La latitud debe ser un valor numérico.',
        'latitud.between' => 'La latitud debe estar entre -90 y 90.',

        'longitud.numeric' => 'La longitud debe ser un valor numérico.',
        'longitud.between' => 'La longitud debe estar entre -180 y 180.',

        'estatus_seleccionado.required' => 'El estatus de la oficina es obligatorio.',
        'condicion_sel.required' => 'La condición de la oficina es obligatoria.',
        'cubicacion.required' => 'El codigo postal es obligatorio.',

        'servicios.required_if' => 'El campo Servicios Operativos es obligatorio cuando el tipo de oficina es OPT.',
        'tipo_pago.required_if' => 'El campo tipo de pago es obligatorio cuando el tipo de oficina es OPT',

        'rol_asignado.required' => 'Debe seleccionar al menos un rol para la oficina y agregar una cantidad a ese rol.',

        'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio',
        'fecha_fin.required' => 'El campo fecha de finalizacion es obligatorio',
        'fecha_fin.after' => 'El campo debe contener una fecha posterior a la del inicio del contrato'
    ];

    protected $tipo_oficina_letras = [
        1 => 'OP',
        2 => 'OP',
        3 => 'OP',
        4 => 'CP',
        5 => 'CO',
        6 => 'CI',
        7 => 'EX',
    ];

    public function generarCodigo($tipo)
    {
        // Obtener las letras representativas del tipo de oficina
        $tipo_oficina_letras = isset($this->tipo_oficina_letras[$tipo]) ? $this->tipo_oficina_letras[$tipo] : 'XX';

        // Si el tipo es 1, 2 o 3, tratamos como un solo tipo global OP
        if (in_array($tipo, [1, 2, 3])) {
            $tipo_oficina_letras = 'OP';
        }

        // Obtener el último código de oficina de ese tipo
        // Si el tipo es OP, no importa si es 1, 2 o 3, todos deben compartir el mismo secuencial
        $last_office = Oficina::whereRaw("LEFT(codigo, 2) = ?", [$tipo_oficina_letras])
            ->orderBy('oficina_id', 'desc')
            ->first();

        // Extraer el número del último código, o empezar en 0 si no existe
        $last_code = $last_office ? $last_office->codigo : $tipo_oficina_letras . '000';
        $number = intval(substr($last_code, 2)); // Extrae los 3 dígitos del código

        // Incrementar el número y formatearlo
        $new_number = str_pad($number + 1, 3, '0', STR_PAD_LEFT);

        return $tipo_oficina_letras . $new_number;
    }

    public function updatedTipoOficina()
    {
        if ($this->tipo_oficina == []) {
            $filtrar_roles = [];
            $this->roles = [];
        } else {
            if ($this->tipo_oficina) {
                $filtrar_roles = RoleTipoOficina::where('tipo_oficina_id', $this->tipo_oficina)->pluck('rol_id');
                $this->roles = Role::whereIn('id', $filtrar_roles)->get();
            } else {
                return;
            }
        }
    }

    public function actualizarCodigosPostales()
    {

        if (in_array($this->serviciorecibir, $this->servicios)) {
            // Filtra los códigos postales que no están asociados a otras oficinas que ofrecen "Recibir"
            $this->codigos_postales_disponibles = CodigoPostal::whereDoesntHave('oficinas', function ($query) {
                $query->whereHas('servicios_operativos', function ($subQuery) {
                    $subQuery->where('servicios_operativos.servicio_operativo_id', $this->serviciorecibir);
                });
            })->get();
        } else {
            $this->codigos_postales_seleccionados = [];
            $this->codigos_postales_disponibles = CodigoPostal::all();
        }

        if ($this->cubicacion) {
            $primer_digito = substr($this->cubicacion, 0, 1);
            $this->codigos_postales_disponibles = $this->codigos_postales_disponibles->filter(function ($codigo) use ($primer_digito) {
                return strpos($codigo->codigo_postal, $primer_digito) === 0;
            });
        }
    }

    public function updatedEstadoss($value)
    {
        $this->municipioss = '';
        if (empty($value)) {
            $this->municipios = [];
        } else {
            $this->municipios = Municipio::where('estado_id', $value)->get();
        }
    }

    public function updatedMunicipioss($value)
    {
        $this->parroquiass = '';
        if (empty($value)) {
            $this->parroquias = [];
        } else {
            $this->parroquias = Parroquia::where('municipio_id', $value)->get();
        }
    }

    public function updatedParroquiass($value)
    {
        $this->cubicacion = '';
        if (empty($value)) {
            $this->codigos_postales = [];
        } else {
            $this->codigos_postales = Sector::where('parroquia_id', $value)->distinct()->pluck('codigo_postal')->sort();
        }
    }

    public function obtenerOficinaRelacionada()
    {
        if (in_array($this->tipo_oficina, [1, 2, 3])) {
            if ($this->estadoss == 2 || $this->estadoss == 24) {
                // Miranda y La Guaira comparten la COP de Distrito Capital
                $this->oficina_relacionada = Oficina::where('tipo_oficina_id', 4)->where('externa', false)->where('estado_id', 1)->pluck('oficina_id')->first();
            } elseif ($this->estadoss == 20) {
                // Delta Amacuro usa la COP de Monagas
                $this->oficina_relacionada = Oficina::where('tipo_oficina_id', 4)->where('externa', false)->where('estado_id', 21)->pluck('oficina_id')->first();
            } else {
                $this->oficina_relacionada = Oficina::where('tipo_oficina_id', 4)
                    ->where('externa', false)
                    ->where('estado_id', $this->estadoss)->pluck('oficina_id')->first();
            }
        } else {
            $this->oficina_relacionada = 1;
        }
    }

    public function save()
    {
        $rules = $this->rules();

        if (in_array(2, $this->servicios)) {
            $rules['codigos_postales_seleccionados'] = 'required';
        }

        if ($this->condicion_sel === 'Arrendada' || $this->condicion_sel === 'En Comodato') {
            $rules['fecha_inicio'] = 'required';
            $rules['fecha_fin'] = 'required|after:fecha_inicio';
        }

        $this->codigo = $this->generarCodigo($this->tipo_oficina);

        $this->validate($rules);

        if (in_array($this->tipo_oficina, [1, 2, 3, 4, 5])) {
            if (empty($this->rol_asignado)) {
                $this->addError('rol_asignado', 'Debe seleccionar al menos un rol para la oficina.');
                return;
            }

            foreach ($this->rol_asignado as $rolId) {
                $cantidad = $this->cantidad_por_rol[$rolId] ?? null;
                if ($cantidad === null || $cantidad === '' || $cantidad < 1 || !is_numeric($cantidad) || intval($cantidad) != $cantidad) {
                    $this->addError('error_roles', 'Todos los roles seleccionados deben tener una cantidad válida (número entero mayor a 0, sin decimales).');
                    return;
                }
            }
        }

        $this->obtenerOficinaRelacionada();

        if ($this->tipo_oficina == [1, 2, 3]) {
            $operaciones = false;
        } else {
            $operaciones = true;
        }

        $oficina = Oficina::create([
            'codigo' => $this->codigo,
            'oficina_relacionada_id' => $this->oficina_relacionada,
            'nombre' => $this->nombre,
            'tipo_oficina_id' => $this->tipo_oficina,
            'correo' => $this->correo ?: 'N/A',
            'direccion' => $this->direccion ?: 'N/A',
            'telefono' => $this->tlf ?: '04245555555',
            'estado_id' => $this->estadoss,
            'municipio_id' => $this->municipioss,
            'parroquia_id' => $this->parroquiass,
            'zona_economica_especial' => $this->zona_economica_especial,
            'latitud' => $this->latitud ?: 'N/A',
            'longitud' => $this->longitud ?: 'N/A',
            'estatus_id' => $this->estatus_seleccionado,
            'operaciones' => $operaciones,
            'codigo_ubicacion' => $this->cubicacion,
            'externa' => false,
        ]);

        if ($this->tipo_oficina != 7) {
            $data = [
                'oficina_id' => $oficina->oficina_id,
                'condicion' => $this->condicion_sel,
            ];
            if ($this->condicion_sel === 'Arrendada' || $this->condicion_sel === 'En Comodato') {
                $data['fecha_inicio'] = $this->fecha_inicio;
                $data['fecha_fin'] = $this->fecha_fin;
            }

            OficinaSemaforoPostal::create($data);
        }


        if (!empty($this->servicios)) {
            $oficina->servicios_operativos()->attach($this->servicios, ['created_at' => now()]);
        }

        if (in_array($this->serviciorecibir, $this->servicios)) {
            foreach ($this->codigos_postales_seleccionados as $codigoPostalId) {
                $oficina->codigos_postales()->attach($codigoPostalId, ['created_at' => now()]);
            }
        }

        if ($this->tipo_pago == 1) {
            if (!empty($this->tipo_pago)) {
                foreach ($this->tipo_pago as $tpago) {
                    $oficina->tipos_pagos()->attach($tpago, ['created_at' => now()]);
                }
            }
        }

        foreach ($this->rol_asignado as $rolId) {

            OficinaPersonal::create([
                'oficina_id' => $oficina->oficina_id,
                'rol_id' => $rolId,
                'cantidad_max' => $this->cantidad_por_rol[$rolId] ?? 0
            ]);
        }

        $this->dispatch('alertSuccess', message: 'Oficina Creada exitosamente!');
        $this->dispatch('cerrar-modal-crear');
    }


    public function render()
    {
        return view('livewire.oficinas.crear-oficinas');
    }
}
