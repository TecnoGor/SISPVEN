<?php

namespace App\Livewire\EnviosInternacionales;

use App\Models\Ciudad;
use App\Models\Documento;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioInternacional;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Oficina;
use App\Models\Pais;
use App\Models\Parroquia;
use App\Models\Sector;
use App\Models\Servicio;
use App\Models\TipoSaca;
use App\Models\UsuarioSeguimiento;
use App\Rules\CodigosTelefono;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EnviosInternacionales extends Component
{

    public  $peso, $pais, $estado, $municipio, $parroquia, $ciudad, $contenido, $clase_correo, $codigo_rastreo, $documento, $documento_dest,
        $nombre, $nombre_dest, $estado_dest, $municipio_dest, $ciudad_dest, $parroquia_dest, $codigo_postal, $codigo_postal_dest, $direccion,
        $direccion_dest, $telefono, $telefono_dest, $correo, $correo_dest, $tipo_documento, $tipo_documento_dest, $apellido, $apellido_dest, $oficina_dest;

    public $lista_correo = false;
    public $certificado = false;
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $ciudades = [];
    public $codigos_postales = [];
    public $documentos = [];
    public $usuario = [];
    public $oficinas = [];
    public $cliente_existe = false;
    public $tipos_envios = [];
    public $tipo_envio = '';
    public $paises = [];

    public function rules()
    {
        return [
            'codigo_rastreo' => [
                $this->tipo_envio == 14 ? 'nullable' : 'required',
                'regex:/^[A-Z]{2}\d{9}[A-Z]{2}$/',
                // El índice de la BD es UNIQUE sobre UPPER(codigo_envio), así que la
                // comprobación se hace con el mismo criterio: comparar literal dejaría
                // pasar códigos que el índice sí rechaza (ab1... vs AB1...).
                Rule::unique('envios', 'codigo_envio')
                    ->where(fn($query) => $query->whereRaw('upper(codigo_envio) = ?', [
                        strtoupper(trim($this->codigo_rastreo ?? '')),
                    ])),
            ],
            'tipo_envio' => 'required',
            'peso' => 'required|integer|min:1|max:100000',
            'contenido' => 'nullable|max:300',
            'pais' => 'nullable|integer|exists:paises,pais_id',
            'nombre' => 'nullable|max:40',
            'tipo_documento' => 'nullable',
            'documento' => 'nullable|digits_between:8,12',
            'telefono' => ['nullable', new CodigosTelefono],
            'correo' => 'nullable|email',
            'estado' => 'nullable',
            'municipio' => 'nullable',
            'parroquia' => 'nullable',
            'ciudad' => 'nullable',
            'codigo_postal' => 'nullable',
            'direccion' => 'nullable|max:200',
            'oficina_dest' => 'required_if:lista_correo,true',
            'correo_dest' => 'nullable|email|max:50',
            'estado_dest' => 'nullable|max:30',
            'tipo_documento_dest' => 'nullable',
            'documento_dest' => 'nullable',
            'municipio_dest' => 'nullable',
            'parroquia_dest' => 'nullable|max:30',
            'ciudad_dest' => 'nullable|max:30',
            'codigo_postal_dest' => 'required|digits:4',
            'telefono_dest' => ['nullable', new CodigosTelefono],
            'direccion_dest' => 'required|max:200',
            'nombre_dest' => 'required|max:40',
            'apellido_dest' => 'required|max:40'
        ];
    }



    public function messages()
    {
        return [
            'codigo_rastreo.unique' => 'Este código de rastreo ya está registrado en el sistema.',
        ];
    }

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->documentos = Documento::all();
        $this->tipos_envios = Servicio::where('servicio_id', '!=', 17)
            ->where('nacional', false)->where('activo', true)
            ->get();
        $this->paises = Pais::whereNot('pais_id', 90)->get();
    }

    public function info_rastreo()
    {
        $this->dispatch('alertSuccess3', message: 'El codigo debe llevar el siguiente formato S10: AB123456789CD');
    }


    public function updatedEstadoDest()
    {
        // Al cambiar el estado se reinician sus campos dependientes del
        // destinatario: municipio, ciudad, parroquia y codigo postal (con sus
        // respectivas listas), para no arrastrar valores del estado anterior.
        $this->municipio_dest = '';
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->ciudades = [];
        $this->parroquias = [];
        $this->codigos_postales = [];

        if ($this->estado_dest == '') {
            $this->municipios = [];
        } else {
            $this->municipios = Municipio::where('estado_id', $this->estado_dest)->get();
        }

        if ($this->lista_correo == true) {
            $this->updatedListaCorreo();
        } else {
            return;
        }
    }

    public function updatedMunicipioDest()
    {
        // Al cambiar el municipio se reinician ciudad, parroquia y codigo postal.
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->codigos_postales = [];

        if ($this->municipio_dest == '') {
            $this->parroquias = [];
            $this->ciudades = [];
        } else {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio_dest)->get();
            $this->ciudades = Ciudad::where('municipio_id', $this->municipio_dest)->get();
        }
    }

    public function updatedParroquiaDest()
    {
        // Al cambiar la parroquia se reinicia el codigo postal.
        $this->codigo_postal_dest = '';

        if ($this->parroquia_dest == '') {
            $this->codigos_postales = [];
        } else {
            $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia_dest)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    public function updatedListaCorreo()
    {
        if ($this->lista_correo == true) {
            if ($this->estado_dest !== null) {
                $this->oficinas = Oficina::where('estado_id', $this->estado_dest)->whereIn('tipo_oficina_id', [1, 2, 3])->get();
            } else {
                $this->oficinas = [];
            }
        } else {
            $this->oficinas = [];
        }
    }

    public function updatedOficinaDest()
    {
        $oficina = Oficina::where('oficina_id', $this->oficina_dest)->first();

        if ($this->oficina_dest !== null) {
            $this->municipio_dest = $oficina->municipio_id;
            $this->parroquia_dest = $oficina->parroquia_id;
            $this->direccion_dest = $oficina->direccion;
        }

        $this->updatedMunicipioDest();
        $this->updatedParroquiaDest();
    }

    public function updatedTipoEnvio()
    {
        if (!\in_array($this->tipo_envio, [14, 18])) {
            $this->certificado = false;
        }
    }


    public function submit()
    {
        $this->codigo_rastreo = strtoupper(trim($this->codigo_rastreo ?? ''));
        $this->validate();

        // Asignación del tipo de saca según el servicio y el flag certificado:
        // - Si el servicio tiene una sola variante activa, se usa esa (sin importar el flag).
        // - Si tiene múltiples variantes activas, se usa la que coincida con $certificado.
        // - Si no tiene ninguna variante activa, queda en null.
        $servicioId = (int) $this->tipo_envio;
        $certificadoElegido = (bool) $this->certificado;

        $tiposSacaDelServicio = TipoSaca::where('servicio_id', $servicioId)
            ->where('activo', true)
            ->get();

        $tipo_saca_id = null;

        if ($tiposSacaDelServicio->count() === 1) {
            $tipo_saca_id = $tiposSacaDelServicio->first()->tipo_saca_id;
        } elseif ($tiposSacaDelServicio->count() > 1) {
            $tipo_saca_id = $tiposSacaDelServicio
                ->firstWhere('certificado', $certificadoElegido)
                ?->tipo_saca_id;
        }

        $estatus_envio = 37;

        DB::beginTransaction();

        try {

            $envio = Envio::create([
                'servicio_id' => $this->tipo_envio,
                'tipo_envio' => 'internacional',
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'nombre_rem' => $this->nombre,
                'apellido_rem' => $this->apellido,
                'tipo_documento_rem' => $this->tipo_documento,
                'documento_rem' => $this->documento,
                'codigo_postal_rem' => $this->codigo_postal,
                'estado_rem' => $this->estado,
                'municipio_rem' => $this->municipio,
                'parroquia_rem' => $this->parroquia,
                'ciudad_rem' => $this->ciudad,
                'direccion_rem' => $this->direccion,
                'correo_rem' => $this->correo,
                'telefono_rem' => $this->telefono,
                'nombre_dest' => $this->nombre_dest ?? 'N/A',
                'apellido_dest' => $this->apellido_dest ?? 'N/A',
                'tipo_documento_dest' => $this->tipo_documento_dest,
                'documento_dest' => $this->documento_dest,
                'codigo_postal_dest' => $this->codigo_postal_dest,
                'continente_dest' => null,
                'pais_dest' => null,
                'estado_dest' => null,
                'municipio_dest' => null,
                'parroquia_dest' => null,
                'ciudad_dest' => null,
                'direccion_dest' => $this->direccion_dest,
                'tlf_dest' => $this->telefono_dest,
                'correo_dest' => $this->correo_dest,
                'servicio_expreso' => null,
                'peso' => $this->peso,
                'coste' => null,
                'contenido' => $this->contenido ?? 'N/A',
                'apartado_postal' => null,
                'codigo_envio' => $this->codigo_rastreo,
                'devolucion' => false,
                'descubierto' => false,
                'tipo_saca_id' => $tipo_saca_id,
            ]);

            EnvioEncaminamiento::create([
                'envio_id' => $envio->envio_id,
                'oficina_id' => $envio->oficina_id,
                'usuario_id' => $this->usuario['id'],
                'estatus_id'  => $estatus_envio,
                'devolucion' => false,

            ]);

            EnvioInternacional::create([
                'envio_id' => $envio->envio_id,
                'pais_id' => $this->pais ?: null,
                'tipo_envio' => $envio->tipo_envio,
                'lista_correo' => $this->lista_correo,
            ]);

            EnvioAlmacen::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'envio_id' => $envio->envio_id,
                'codigo' => $envio->codigo_envio,
                'saca_id' => null,
                'estatus' => true,
                'Entrada' => now()->toDateTimeString(),
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el envío internacional ({$envio->envio_id}) con código ({$envio->codigo_envio})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Envio Registrado exitosamente!');
            $this->dispatch('envio_registrado');
        } catch (UniqueConstraintViolationException $e) {
            // Red de seguridad para la carrera entre dos operadores que registran el
            // mismo código a la vez: la regla unique de rules() no la cubre porque
            // consulta antes del INSERT. Aquí el rebote lo da el índice
            // envios_codigo_envio_unique (sobre UPPER(codigo_envio)).
            DB::rollback();
            $this->addError('codigo_rastreo', 'Este código de rastreo ya está registrado en el sistema.');
            $this->dispatch('alertSuccess2', message: 'El código de rastreo ya está registrado en el sistema.');
        } catch (\Exception $e) {
            //  dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del envio, verifique los datos e intente de nuevo');
            // Si ocurre un error, revertimos todos los cambios
        }
    }


    public function render()
    {
        return view('livewire.envios-internacionales.envios-internacionales');
    }
}
