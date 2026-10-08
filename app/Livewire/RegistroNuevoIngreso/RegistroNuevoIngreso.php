<?php

namespace App\Livewire\RegistroNuevoIngreso;

use App\Rules\CodigosTelefono;
use App\Models\CarreraEstudio;
use App\Models\Direccion;
use App\Models\DiscapacidadPersona;
use App\Models\Documento;
use App\Models\Empleado;
use App\Models\Estado;
use App\Models\EstadoCivil;
use App\Models\FormacionAcademica;
use App\Models\GrupoFamiliar;
use App\Models\Institucion;
use App\Models\Municipio;
use App\Models\Nacionalidad;
use App\Models\NivelEducativo;
use App\Models\Pais;
use App\Models\Parentesco;
use App\Models\Parroquia;
use App\Models\Sector;
use App\Models\TipoDiscapacidad;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistroNuevoIngreso extends Component
{
    use WithFileUploads;

    public $nombre, $apellido, $tipo_documento, $documento, $correo, $telefono, $telefono_2, $telefono_emergencia,
        $fecha_nacimiento, $genero, $nacionalidad, $estado_civil, $fecha_ingreso;


    public $tipo_documentos = [];
    public $discapacidades = [];
    public $nacionalidades = [];
    public $estados_civiles = [];
    public $niveles_educativos = [];
    public $carreras = [];
    public $instituciones = [];
    public $parentescos = [];
    public $bloques_academico = [];
    public $bloques_discapacidad = [];
    public $bloques_grupo_familiar = [];
    public $bloques_direccion = [];
    public $estados = [];
    public $paises = [];




    public function mount()
    {
        $this->tipo_documentos = Documento::all();
        $this->nacionalidades = Nacionalidad::all();
        $this->estados_civiles = EstadoCivil::all();
        $this->niveles_educativos = NivelEducativo::all();
        $this->carreras = CarreraEstudio::all();
        $this->instituciones = Institucion::all();
        $this->discapacidades = TipoDiscapacidad::all();
        $this->parentescos = Parentesco::all();
        $this->estados = Estado::all();
        $this->paises = Pais::all();

        $this->añadirBloqueDiscapacidad();
        $this->añadirBloqueAcademico();
        $this->añadirBloqueGrupoFamiliar();
        $this->añadirBloqueDireccion();
    }


    // Bloque Direccion
    public function añadirBloqueDireccion()
    {
        $this->bloques_direccion[] = [
            'estado_id'    => '',
            'municipio_id' => '',
            'parroquia_id' => '',
            'sector_id'    => '',
            'direccion'    => '',
            'municipios'   => [],
            'parroquias'   => [],
            'sectores'     => [],
        ];
    }

    public function eliminarBloqueDireccion($index)
    {
        unset($this->bloques_direccion[$index]);
        $this->bloques_direccion = array_values($this->bloques_direccion);
    }

    public function updatedBloquesDireccion($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $campo = $parts[1] ?? '';

        if ($campo === 'estado_id') {
            $this->bloques_direccion[$index]['municipio_id'] = '';
            $this->bloques_direccion[$index]['parroquia_id'] = '';
            $this->bloques_direccion[$index]['sector_id']    = '';
            $this->bloques_direccion[$index]['municipios']   = $value ? Municipio::where('estado_id', $value)->get()->toArray() : [];
            $this->bloques_direccion[$index]['parroquias']   = [];
            $this->bloques_direccion[$index]['sectores']     = [];
        }

        if ($campo === 'municipio_id') {
            $this->bloques_direccion[$index]['parroquia_id'] = '';
            $this->bloques_direccion[$index]['sector_id']    = '';
            $this->bloques_direccion[$index]['parroquias']   = $value ? Parroquia::where('municipio_id', $value)->get()->toArray() : [];
            $this->bloques_direccion[$index]['sectores']     = [];
        }

        if ($campo === 'parroquia_id') {
            $this->bloques_direccion[$index]['sector_id'] = '';
            $this->bloques_direccion[$index]['sectores']  = $value ? Sector::where('parroquia_id', $value)->get()->toArray() : [];
        }
    }


    //Bloque Academico
    public function añadirBloqueAcademico()
    {
        $this->bloques_academico[] = [
            'nivel_educativo_id' => '',
            'carrera_id' => '',
            'carrera_otro' => '',
            'institucion_id' => '',
            'institucion_otro' => '',
            'pais' => '',
            'año_graduacion' => '',
            'titulo_obtenido' => ''
        ];
    }

    public function eliminarBloqueAcademico($index)
    {
        unset($this->bloques_academico[$index]);
        $this->bloques_academico = array_values($this->bloques_academico);
    }

    public function updatedBloquesAcademico($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $campo = $parts[1] ?? '';

        if ($campo === 'carrera_id') {
            $this->bloques_academico[$index]['carrera_otro'] = '';
        }
        if ($campo === 'carrera_otro') {
            $this->bloques_academico[$index]['carrera_id'] = '';
        }
        if ($campo === 'institucion_id') {
            $this->bloques_academico[$index]['institucion_otro'] = '';
        }
        if ($campo === 'institucion_otro') {
            $this->bloques_academico[$index]['institucion_id'] = '';
        }
    }


    //Bloque Discapacidad
    public function añadirBloqueDiscapacidad()
    {
        $this->bloques_discapacidad[] = ['tipo_discapacidad_id' => '', 'descripcion' => ''];
    }

    public function eliminarBloqueDiscapacidad($index)
    {
        unset($this->bloques_discapacidad[$index]);
        $this->bloques_discapacidad = array_values($this->bloques_discapacidad);
    }


    //Bloque Grupo Familiar
    public function añadirBloqueGrupoFamiliar()
    {
        $this->bloques_grupo_familiar[] = [
            'nombres' => '',
            'apellidos' => '',
            'parentesco' => '',
            'nivel_educativo_id' => '',
            'tipo_documento' => '',
            'documento' => '',
            'nacimiento' => '',
            'telefono' => '',
            'genero' => '',
            'trabaja' => false,
            'estudia' => false,
            'vive_con_empleado' => false,
            'discapacidad' => false,
            'tipo_discapacidad' => '',
            'descripcion' => '',
            'partida_nacimiento' => null,
        ];
    }

    public function eliminarBloqueGrupoFamiliar($index)
    {
        unset($this->bloques_grupo_familiar[$index]);
        $this->bloques_grupo_familiar = array_values($this->bloques_grupo_familiar);
    }

    public function parentescosAlLimite(): array
    {
        $limites = ['1' => 4, '2' => 1, '3' => 1, '7' => 1];
        $bloqueados = [];

        foreach ($limites as $id => $max) {
            $cantidad = collect($this->bloques_grupo_familiar)
                ->filter(fn($b) => (string)($b['parentesco'] ?? '') === (string)$id)
                ->count();

            if ($cantidad >= $max) {
                $bloqueados[] = (string)$id;
            }
        }

        return $bloqueados;
    }

    public function updatedBloquesGrupoFamiliar($value, $key)
    {
        if (!str_ends_with($key, '.parentesco')) {
            return;
        }

        $index = explode('.', $key)[0];

        // Si deja de ser hijo/a (5), limpiar la partida de nacimiento
        if ((string)$value !== '5') {
            $this->bloques_grupo_familiar[$index]['partida_nacimiento'] = null;
        }

        $limites = [
            '1' => ['max' => 4, 'nombre' => 'Abuelo/a'],
            '2' => ['max' => 1, 'nombre' => 'Padre'],
            '3' => ['max' => 1, 'nombre' => 'Madre'],
            '7' => ['max' => 1, 'nombre' => 'Cónyuge'],
        ];

        if (!isset($limites[$value])) {
            return;
        }

        $limite = $limites[$value];

        // Contar cuántos bloques tienen ese parentesco, excluyendo el actual
        $cantidad = collect($this->bloques_grupo_familiar)
            ->filter(fn($b, $i) => (string)($b['parentesco'] ?? '') === (string)$value && $i != $index)
            ->count();

        if ($cantidad >= $limite['max']) {
            $this->bloques_grupo_familiar[$index]['parentesco'] = '';
            $this->dispatch('alertError', message: "Solo se permite un máximo de {$limite['max']} {$limite['nombre']}(s) por empleado.");
        }
    }






    protected function reglasBloquesAcademico(): array
    {
        $reglas = [];

        foreach ($this->bloques_academico as $i => $bloque) {
            $nivelId = (int) ($bloque['nivel_educativo_id'] ?? 0);
            $superior = $nivelId > 6;

            // Carrera: obligatorio uno de los dos si nivel > 1
            $reglas["bloques_academico.{$i}.carrera_id"] = array_filter([
                $superior ? 'required_without:bloques_academico.' . $i . '.carrera_otro' : 'nullable',
                'nullable',
                'integer',
                'exists:carreras_estudio,carrera_estudio_id',
            ]);
            $reglas["bloques_academico.{$i}.carrera_otro"] = array_filter([
                $superior ? 'required_without:bloques_academico.' . $i . '.carrera_id' : 'nullable',
                'nullable',
                'string',
                'max:255',
            ]);

            // Institución: obligatorio uno de los dos si nivel > 1
            $reglas["bloques_academico.{$i}.institucion_id"] = array_filter([
                $superior ? 'required_without:bloques_academico.' . $i . '.institucion_otro' : 'nullable',
                'nullable',
                'integer',
                'exists:instituciones,institucion_id',
            ]);
            $reglas["bloques_academico.{$i}.institucion_otro"] = array_filter([
                $superior ? 'required_without:bloques_academico.' . $i . '.institucion_id' : 'nullable',
                'nullable',
                'string',
                'max:255',
            ]);

            // País y año: obligatorios si nivel > 1
            $reglas["bloques_academico.{$i}.pais"] = $superior
                ? 'required|integer|exists:paises,pais_id'
                : 'nullable|integer|exists:paises,pais_id';

            $reglas["bloques_academico.{$i}.año_graduacion"] = $superior
                ? 'required|integer|min:1950|max:' . date('Y')
                : 'nullable|integer|min:1950|max:' . date('Y');

            $reglas["bloques_academico.{$i}.titulo_obtenido"] = 'nullable|file|mimes:pdf|max:20480';
        }

        return $reglas;
    }

    public function rules(): array
    {
        return [
            // Datos Personales
            'nombre'              => 'required|string|max:255',
            'apellido'            => 'required|string|max:255',
            'tipo_documento'      => 'required|string',
            'documento'           => 'required|string|max:20',
            'correo'              => 'required|email|max:255',
            'telefono'            => ['required', new CodigosTelefono],
            'telefono_2'          => ['nullable', new CodigosTelefono],
            'telefono_emergencia' => ['nullable', new CodigosTelefono],
            'fecha_nacimiento'    => 'required|date|before:today',
            'genero'              => 'required|boolean',
            'nacionalidad'        => 'required|integer|exists:nacionalidad,nacionalidad_id',
            'estado_civil'        => 'required|integer|exists:estados_civiles,estado_civil_id',
            'fecha_ingreso'       => 'required|date',

            // Formación Académica
            'bloques_academico'                        => 'required|array|min:1',
            'bloques_academico.*.nivel_educativo_id'   => 'required|integer|exists:niveles_educativos,nivel_educativo_id',
            ...$this->reglasBloquesAcademico(),

            // Discapacidades del Empleado
            'bloques_discapacidad.*.tipo_discapacidad_id' => 'nullable|integer|exists:tipos_discapacidades,tipo_discapacidad_id',
            'bloques_discapacidad.*.descripcion'          => 'nullable|string|max:500',

            // Direcciones
            'bloques_direccion'                => 'required|array|min:1',
            'bloques_direccion.*.estado_id'    => 'required|integer|exists:estados,estado_id',
            'bloques_direccion.*.municipio_id' => 'required|integer|exists:municipios,municipio_id',
            'bloques_direccion.*.parroquia_id' => 'required|integer|exists:parroquias,parroquia_id',
            'bloques_direccion.*.sector_id'    => 'required|integer|exists:sectores,sector_id',
            'bloques_direccion.*.direccion'    => 'required|string|max:500',

            // Grupo Familiar
            'bloques_grupo_familiar.*.nombres'            => 'nullable|string|max:255',
            'bloques_grupo_familiar.*.apellidos'          => 'nullable|string|max:255',
            'bloques_grupo_familiar.*.parentesco'         => 'nullable|integer|exists:parentescos,parentesco_id',
            'bloques_grupo_familiar.*.genero'             => 'nullable|boolean',
            'bloques_grupo_familiar.*.nacimiento'         => 'nullable|date',
            'bloques_grupo_familiar.*.tipo_documento'     => 'nullable|string',
            'bloques_grupo_familiar.*.documento'          => 'nullable|string|max:20',
            'bloques_grupo_familiar.*.telefono'           => ['nullable', new CodigosTelefono],
            'bloques_grupo_familiar.*.nivel_educativo_id' => 'nullable|integer|exists:niveles_educativos,nivel_educativo_id',
            'bloques_grupo_familiar.*.tipo_discapacidad'  => 'nullable|integer|exists:tipos_discapacidades,tipo_discapacidad_id',
            'bloques_grupo_familiar.*.descripcion'        => 'nullable|string|max:500',
            'bloques_grupo_familiar.*.partida_nacimiento' => 'nullable|file|mimes:pdf|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            // Datos Personales
            'nombre.required'              => 'El nombre es obligatorio.',
            'apellido.required'            => 'El apellido es obligatorio.',
            'tipo_documento.required'      => 'El tipo de documento es obligatorio.',
            'documento.required'           => 'La cédula/documento es obligatoria.',
            'correo.required'              => 'El correo electrónico es obligatorio.',
            'correo.email'                 => 'El correo electrónico no es válido.',
            'telefono.required'            => 'El teléfono personal es obligatorio.',
            'fecha_nacimiento.required'    => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'      => 'La fecha de nacimiento debe ser anterior a hoy.',
            'genero.required'              => 'El género es obligatorio.',
            'nacionalidad.required'        => 'La nacionalidad es obligatoria.',
            'estado_civil.required'        => 'El estado civil es obligatorio.',
            'fecha_ingreso.required'       => 'La fecha de ingreso es obligatoria.',

            // Formación Académica
            'bloques_academico.min'                                   => 'Debe registrar al menos una formación académica.',
            'bloques_academico.*.nivel_educativo_id.required'         => 'El nivel educativo es obligatorio en cada formación.',
            'bloques_academico.*.carrera_id.required_without'         => 'Debe seleccionar una carrera o especificarla manualmente.',
            'bloques_academico.*.carrera_otro.required_without'       => 'Debe seleccionar una carrera o especificarla manualmente.',
            'bloques_academico.*.institucion_id.required_without'     => 'Debe seleccionar una institución o especificarla manualmente.',
            'bloques_academico.*.institucion_otro.required_without'   => 'Debe seleccionar una institución o especificarla manualmente.',
            'bloques_academico.*.pais.required'                       => 'El país de graduación es obligatorio.',
            'bloques_academico.*.año_graduacion.required'             => 'El año de graduación es obligatorio.',
            'bloques_academico.*.año_graduacion.min'                  => 'El año de graduación no puede ser anterior a 1950.',
            'bloques_academico.*.año_graduacion.max'                  => 'El año de graduación no puede ser futuro.',
            'bloques_academico.*.titulo_obtenido.mimes'               => 'El título debe ser un archivo PDF.',
            'bloques_academico.*.titulo_obtenido.max'                 => 'El título no debe superar los 20MB.',

            // Direcciones
            'bloques_direccion.min'                    => 'Debe registrar al menos una dirección.',
            'bloques_direccion.*.estado_id.required'   => 'El estado es obligatorio en cada dirección.',
            'bloques_direccion.*.municipio_id.required' => 'El municipio es obligatorio en cada dirección.',
            'bloques_direccion.*.parroquia_id.required' => 'La parroquia es obligatoria en cada dirección.',
            'bloques_direccion.*.sector_id.required'   => 'El sector es obligatorio en cada dirección.',
            'bloques_direccion.*.direccion.required'   => 'La dirección específica es obligatoria.',

            // Archivos grupo familiar
            'bloques_grupo_familiar.*.partida_nacimiento.mimes' => 'La partida de nacimiento debe ser un archivo PDF.',
            'bloques_grupo_familiar.*.partida_nacimiento.max'   => 'La partida de nacimiento no debe superar los 20MB.',
        ];
    }

    public function submit()
    {
        $this->validate();

        $usuario = auth()->user();

        DB::beginTransaction();
        try {
            // 1. Guardar Empleado
            $empleado = Empleado::create([
                'usuario_id'          => $usuario->id,
                'oficina_id'          => $usuario->oficina_id,
                'nombre'              => $this->nombre,
                'apellido'            => $this->apellido,
                'tipo_documento'      => $this->tipo_documento,
                'documento'           => $this->documento,
                'correo'              => $this->correo,
                'telefono'            => $this->telefono,
                'telefono_secundario' => $this->telefono_2,
                'telefono_emergencia' => $this->telefono_emergencia,
                'fecha_nacimiento'    => $this->fecha_nacimiento,
                'genero'              => $this->genero,
                'nacionalidad'        => $this->nacionalidad,
                'estado_civil'        => $this->estado_civil,
                'fecha_ingreso'       => $this->fecha_ingreso,
            ]);

            // 2. Guardar Formación Académica
            foreach ($this->bloques_academico as $bloque) {
                $path = null;
                if ($bloque['titulo_obtenido']) {
                    $path = $bloque['titulo_obtenido']->store('titulos', 'public');
                }

                FormacionAcademica::create([
                    'empleado_id'       => $empleado->empleado_id,
                    'nivel_educativo_id' => $bloque['nivel_educativo_id'] ?: null,
                    'carrera_id'        => $bloque['carrera_id'] ?: null,
                    'carrera_otro'      => $bloque['carrera_otro'] ?: null,
                    'institucion_id'    => $bloque['institucion_id'] ?: null,
                    'institucion_otro'  => $bloque['institucion_otro'] ?: null,
                    'pais_id'           => $bloque['pais'] ?: 90,
                    'año_graduacion'    => $bloque['año_graduacion'] ?: null,
                    'titulo_obtenido'   => $path,
                ]);
            }

            // 3. Guardar Discapacidades del Empleado
            foreach ($this->bloques_discapacidad as $bloque) {
                if (!$bloque['tipo_discapacidad_id']) continue;

                DiscapacidadPersona::create([
                    'empleado_id'         => $empleado->empleado_id,
                    'grupo_familiar_id'   => null,
                    'tipo_discapacidad_id' => $bloque['tipo_discapacidad_id'],
                    'discapacidad_detalle' => $bloque['descripcion'],
                ]);
            }

            // 4. Guardar Direcciones
            foreach ($this->bloques_direccion as $index => $bloque) {
                if (!$bloque['estado_id']) continue;

                Direccion::create([
                    'empleado_id'        => $empleado->empleado_id,
                    'estado_id'          => $bloque['estado_id'],
                    'municipio_id'       => $bloque['municipio_id'] ?: null,
                    'parroquia_id'       => $bloque['parroquia_id'] ?: null,
                    'sector_id'          => $bloque['sector_id'] ?: null,
                    'direccion_especifica' => $bloque['direccion'],
                    'principal'          => $index === 0,
                ]);
            }

            // 5. Guardar Grupo Familiar
            foreach ($this->bloques_grupo_familiar as $bloque) {
                if (!$bloque['nombres']) continue;

                $partida = null;
                if ($bloque['partida_nacimiento']) {
                    $partida = $bloque['partida_nacimiento']->store('partidas', 'public');
                }

                $familiar = GrupoFamiliar::create([
                    'usuario_id'        => $usuario->id,
                    'oficina_id'        => $usuario->oficina_id,
                    'empleado_id'       => $empleado->empleado_id,
                    'parentesco_id'     => $bloque['parentesco'],
                    'genero'            => $bloque['genero'],
                    'nombre'            => $bloque['nombres'],
                    'apellido'          => $bloque['apellidos'],
                    'fecha_nacimiento'  => $bloque['nacimiento'] ?: null,
                    'tipo_documento'    => $bloque['tipo_documento'] ?: null,
                    'documento'         => $bloque['documento'] ?: null,
                    'telefono'          => $bloque['telefono'] ?: null,
                    'trabaja'           => $bloque['trabaja'],
                    'estudia'           => $bloque['estudia'],
                    'nivel_educativo_id' => $bloque['nivel_educativo_id'] ?: null,
                    'vive_con_empleado' => $bloque['vive_con_empleado'],
                    'tiene_discapacidad' => $bloque['discapacidad'],
                    'partida_nacimiento' => $partida,
                ]);

                // Guardar discapacidad del familiar si aplica
                if ($bloque['discapacidad'] && $bloque['tipo_discapacidad']) {
                    DiscapacidadPersona::create([
                        'empleado_id'          => null,
                        'grupo_familiar_id'    => $familiar->grupo_familiar_id,
                        'tipo_discapacidad_id' => $bloque['tipo_discapacidad'],
                        'discapacidad_detalle' => $bloque['descripcion'],
                    ]);
                }
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el nuevo empleado ({$empleado->empleado_id}) - {$this->nombre} {$this->apellido}",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Empleado registrado exitosamente.');
            $this->js('setTimeout(() => window.location.reload(), 2000)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alertError', message: 'Ocurrió un error al registrar el empleado. Intente de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.registro-nuevo-ingreso.registro-nuevo-ingreso', [
            'parentescosAlLimite' => $this->parentescosAlLimite(),
        ]);
    }
}
