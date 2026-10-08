<?php

namespace App\Livewire\CargaMasiva;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Envio;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioInternacional;
use App\Models\EnvioAlmacen;
use App\Models\Servicio;
use App\Models\Oficina;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Ciudad;
use App\Models\Continente;
use App\Models\Pais;
use App\Models\TipoSaca;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use PhpOffice\PhpSpreadsheet\Shared\Date;

#[Layout('layouts.app')]
class CargaMasiva extends Component
{
    use WithFileUploads;

    public $archivo;
    public $mensaje = '';
    public $errores = [];
    public $fallidas = 0;

    public function updatedArchivo()
    {
        $this->mensaje = '';
        $this->errores = [];
        $this->fallidas = 0;
    }

    public function ping()
    {
        $this->mensaje = 'Livewire OK';
    }

    private function normalizar($cadena)
    {
        if (!$cadena) return '';
        $cadena = Str::ascii($cadena);
        $cadena = strtolower(trim($cadena));
        $cadena = preg_replace('/[^a-z0-9 ]/', '', $cadena);
        return str_replace(' ', '', $cadena);
    }

    /**
     * Convierte un índice numérico de columna (0, 1, 2...) 
     * a su equivalente en letra de Excel (A, B, C... AA, AB...).
     */
    private function getExcelLetter($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(($index % 26) + 65) . $letter;
            $index = floor($index / 26) - 1;
        }
        return $letter;
    }

    public function importar()
    {
        $this->validate([
            'archivo' => 'required|file|mimes:xlsx,xls',
        ]);

        // --- CARGA DE MAESTROS CON IDS ---

        $servicios = Servicio::all()->pluck('servicio_id', 'nombre')->toArray();
        $servicios_normalizados = [];
        foreach ($servicios as $nombre => $id) {
            $servicios_normalizados[$this->normalizar($nombre)] = $id;
        }

        $oficinas = Oficina::all()->pluck('oficina_id', 'nombre')->toArray();
        $oficinas_normalizadas = [];
        foreach ($oficinas as $nombre => $id) {
            $oficinas_normalizadas[$this->normalizar($nombre)] = $id;
        }

        $estados_db = Estado::all();
        $estados_normalizados = [];
        foreach ($estados_db as $e) {
            $estados_normalizados[$this->normalizar($e->nombre)] = $e->estado_id;
        }

        $municipios_db = Municipio::all();
        $municipios_normalizados = [];
        foreach ($municipios_db as $m) {
            $municipios_normalizados[$this->normalizar($m->nombre) . '|' . $m->estado_id] = $m->municipio_id;
        }

        $parroquias_db = Parroquia::all();
        $parroquias_normalizados = [];
        foreach ($parroquias_db as $p) {
            $parroquias_normalizados[$this->normalizar($p->nombre) . '|' . $p->municipio_id] = $p->parroquia_id;
        }

        $ciudades_db = Ciudad::all();
        $ciudades_normalizados = [];
        foreach ($ciudades_db as $c) {
            $ciudades_normalizados[$this->normalizar($c->nombre) . '|' . $c->estado_id] = $c->ciudad_id;
        }

        $continentes_db = class_exists('\App\Models\Continente') ? Continente::all() : [];
        $continentes_normalizados = [];
        foreach ($continentes_db as $c) {
            $continentes_normalizados[$this->normalizar($c->nombre)] = $c->continente_id;
        }

        $paises_db = class_exists('\App\Models\Pais') ? Pais::all() : [];
        $paises_normalizados = [];
        foreach ($paises_db as $p) {
            $paises_normalizados[$this->normalizar($p->nombre)] = $p->pais_id;
        }

        $tipos_saca = class_exists('\App\Models\TipoSaca') ? TipoSaca::all()->pluck('id', 'nombre')->toArray() : [];
        $tipos_saca_normalizados = [];
        foreach ($tipos_saca as $nombre => $id) {
            $tipos_saca_normalizados[$this->normalizar($nombre)] = $id;
        }

        try {
            $data = Excel::toArray([], $this->archivo);
            $registros_brutos = $data[0] ?? [];

            // 3. Ignorar Fila de Instrucciones (Fila 1 y Fila 2 de Excel)
            if (isset($registros_brutos[0])) unset($registros_brutos[0]); // Cabeceras
            if (isset($registros_brutos[1])) unset($registros_brutos[1]); // Instrucciones

            $registros = [];
            foreach ($registros_brutos as $index => $fila) {
                // 4. Filtro de Filas Vacías (Ghost Rows)
                $fila_vacia = true;
                foreach ($fila as $celda) {
                    if (trim((string)$celda) !== '') {
                        $fila_vacia = false;
                        break;
                    }
                }
                if ($fila_vacia) continue;

                // 1. Normalización del Campo de Servicio (Columna 29 / AD)
                if (isset($fila[29])) {
                    $val29 = strtolower(trim((string)$fila[29]));
                    if (in_array($val29, ['nacional', 'urbano', 'interestatal', 'no'])) {
                        $fila[29] = $val29;
                    } else {
                        $fila[29] = trim((string)$fila[29]);
                    }
                }

                // 2. Flexibilidad en Columnas AK (36) y AL (37) (Si/No)
                if (isset($fila[36])) {
                    $val36 = strtolower(trim((string)$fila[36]));
                    if (in_array($val36, ['si', 'no'])) {
                        $fila[36] = strtoupper($val36);
                    } else {
                        $fila[36] = trim((string)$fila[36]);
                    }
                }

                if (isset($fila[37])) {
                    $val37 = strtolower(trim((string)$fila[37]));
                    if (in_array($val37, ['si', 'no'])) {
                        $fila[37] = strtoupper($val37);
                    } else {
                        $fila[37] = trim((string)$fila[37]);
                    }
                }

                $registros[$index] = $fila;
            }

            $messages = [
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser un texto válido.',
                'max'      => 'El campo :attribute no debe exceder los :max caracteres.',
                'size'     => 'El campo :attribute debe tener exactamente :size caracteres.',
                'alpha'    => 'El campo :attribute solo debe contener letras.',
                'numeric'  => 'El campo :attribute solo acepta números. Por favor, asegúrese de haber ingresado solo dígitos (sin letras ni espacios) en esta columna del archivo Excel.',
                'min'      => 'El campo :attribute debe ser al menos :min.',
                'regex'    => 'El formato del campo :attribute no es válido.',
                'in'       => 'El valor seleccionado para :attribute no es válido.',
            ];

            $columnNamesRaw = [
                0 => 'Servicio',
                1 => 'Tipo de Envío',
                2 => 'Oficina',
                3 => 'Nombre del Remitente',
                4 => 'Apellido del Remitente',
                5 => 'Tipo Documento Remitente',
                6 => 'Documento Remitente',
                7 => 'Código Postal Remitente',
                8 => 'Estado Remitente',
                9 => 'Municipio Remitente',
                10 => 'Parroquia Remitente',
                11 => 'Dirección Remitente',
                12 => 'Correo Remitente',
                13 => 'Teléfono Remitente',
                14 => 'Nombre del Destinatario',
                15 => 'Apellido del Destinatario',
                16 => 'Tipo Documento Destinatario',
                17 => 'Documento Destinatario',
                18 => 'Código Postal Destinatario',
                19 => 'Continente Destino',
                20 => 'País Destino',
                21 => 'Estado Destino',
                22 => 'Oficina Destino',
                23 => 'Municipio Destino',
                24 => 'Parroquia Destino',
                25 => 'Ciudad Destino',
                26 => 'Dirección Destinatario',
                27 => 'Teléfono Destinatario',
                28 => 'Correo Destinatario',
                29 => 'Servicio Expreso',
                30 => 'Peso',
                31 => 'Coste',
                32 => 'Contenido',
                33 => 'Apartado Postal',
                34 => 'Código de Envío',
                35 => 'Tipo de Saca',
                36 => 'Devolución',
                37 => 'Descubierto',
            ];

            $columnNames = [];
            foreach ($columnNamesRaw as $idx => $name) {
                $columnNames["*.{$idx}"] = $name;
            }

            $rules = [
                '*.0'  => ['required', 'string', 'max:200'],
                '*.1'  => ['required', 'regex:/^internacional$|^nacional$/i'],
                '*.2'  => ['required', 'string', 'max:100'],
                // REMITENTE: ahora opcional
                '*.3'  => ['nullable', 'string', 'max:200'],
                '*.4'  => ['nullable', 'string', 'max:50'],
                '*.5'  => ['nullable', 'string', 'size:1', 'alpha'],
                '*.6'  => ['nullable', 'numeric'],
                '*.7'  => ['nullable', 'numeric'],
                '*.8'  => ['nullable', 'string', 'max:100'],
                '*.9'  => ['nullable', 'string', 'max:100'],
                '*.10' => ['nullable', 'string', 'max:100'],
                '*.11' => ['nullable', 'string', 'max:100'],
                '*.12' => ['nullable', 'string', 'max:100'],
                '*.13' => ['nullable', 'numeric'],
                '*.14' => ['required', 'string', 'max:200'],
                '*.15' => ['required', 'string', 'max:50'],
                // DESTINATARIO: tipo de documento y documento ahora opcionales
                '*.16' => ['nullable', 'string', 'size:1', 'alpha'],
                '*.17' => ['nullable', 'numeric'],
                '*.18' => ['required', 'numeric'],
                '*.19' => ['required', 'string', 'max:100'],
                '*.20' => ['required', 'string', 'max:100'],
                '*.21' => ['required', 'string', 'max:100'],
                '*.22' => ['nullable', 'string', 'max:200'],
                '*.23' => ['nullable', 'string', 'max:100'],
                '*.24' => ['nullable', 'regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑüÜ\-\.]{1,100}$/u'],
                '*.25' => ['nullable', 'string', 'max:100'],
                '*.26' => ['nullable', 'string', 'max:255'],
                '*.27' => ['nullable', 'numeric'],
                '*.28' => ['nullable', 'string', 'max:100'],
                '*.29' => ['nullable', 'in:nacional,urbano,interestatal,no,""'],
                // COSTE Y PESO: ahora opcionales
                '*.30' => ['nullable', 'numeric', 'min:0.01'],
                '*.31' => ['nullable', 'regex:/^\d+(\.\d{1,2})?$/'],
                '*.32' => ['nullable', 'string', 'max:255'],
                '*.33' => ['nullable', 'numeric'],
                '*.34' => ['nullable', 'string', 'max:30'],
                '*.35' => ['nullable', 'string', 'max:100'],
                '*.36' => ['nullable', 'in:SI,NO,""'],
                '*.37' => ['nullable', 'in:SI,NO,""'],
            ];

            $validator = Validator::make($registros, $rules, $messages, $columnNames);

            if ($validator->fails()) {
                $raw = $validator->errors()->getMessages();
                $filaErrores = [];
                foreach ($raw as $key => $mensajes) {
                    if (strpos($key, '.') === false) {
                        $rowIndex = $key;
                        $colIndex = null;
                    } else {
                        list($rowIndex, $colIndex) = explode('.', $key, 2);
                    }
                    if (!is_numeric($rowIndex)) continue;

                    $excelRow = intval($rowIndex) + 1;
                    $letter = !is_null($colIndex) ? $this->getExcelLetter(intval($colIndex)) : '';
                    $colName = !is_null($colIndex) ? ($columnNamesRaw[intval($colIndex)] ?? "Columna {$letter}") : 'fila';

                    foreach ($mensajes as $m) {
                        // Inyectamos el detalle de Fila y Columna en el mensaje
                        $replacement = "{$colName} (Fila {$excelRow} / Columna {$letter})";
                        $friendlyMessage = str_replace($colName, $replacement, $m);

                        $filaErrores[$excelRow][] = "En {$colName}: {$friendlyMessage}";
                    }
                }
                $this->errores = $filaErrores;
                $this->fallidas = count($filaErrores);
                $this->mensaje = 'Se han detectado algunos detalles que deben corregirse en el archivo antes de procesarlo.';
                return;
            }

            // Validar que el servicio de cada fila exista en la BD
            $erroresServicio = [];
            foreach ($registros as $index => $fila) {
                $servicioNorm = $this->normalizar($fila[0] ?? '');
                if (!isset($servicios_normalizados[$servicioNorm])) {
                    $excelRow = intval($index) + 1;
                    $erroresServicio[$excelRow][] = "El servicio \"{$fila[0]}\" (Fila {$excelRow} / Columna A) no existe en el sistema.";
                }
            }

            if (!empty($erroresServicio)) {
                $this->errores = $erroresServicio;
                $this->fallidas = count($erroresServicio);
                $this->mensaje = 'Se han detectado servicios no registrados en el sistema. Verifique los nombres en la columna A.';
                return;
            }

            DB::beginTransaction();
            foreach ($registros as &$fila) {
                foreach ([3, 4, 6, 7, 13, 14, 15, 17, 18, 26] as $i) {
                    if (isset($fila[$i])) $fila[$i] = (string)$fila[$i];
                }

                $devolucion = isset($fila[36]) && strtolower(trim($fila[36])) === 'si' ? true : false;
                $descubierto = isset($fila[37]) && strtolower(trim($fila[37])) === 'si' ? true : false;

                // --- MAPEO JERÁRQUICO REMITENTE ---
                $servicio_id = $servicios_normalizados[$this->normalizar($fila[0] ?? '')] ?? null;
                $oficina_id = $oficinas_normalizadas[$this->normalizar($fila[2] ?? '')] ?? null;
                $estado_rem_id = $estados_normalizados[$this->normalizar($fila[8] ?? '')] ?? null;

                $municipio_rem_id = null;
                if ($estado_rem_id) {
                    $municipio_rem_id = $municipios_normalizados[$this->normalizar($fila[9] ?? '') . '|' . $estado_rem_id] ?? null;
                }

                $parroquia_rem_id = null;
                if ($municipio_rem_id) {
                    $parroquia_rem_id = $parroquias_normalizados[$this->normalizar($fila[10] ?? '') . '|' . $municipio_rem_id] ?? null;
                }

                $ciudad_rem_id = null;
                if ($estado_rem_id) {
                    $ciudad_rem_id = $ciudades_normalizados[$this->normalizar($fila[11] ?? '') . '|' . $estado_rem_id] ?? null;
                }

                // --- MAPEO JERÁRQUICO DESTINATARIO ---
                $oficina_dest_id = $oficinas_normalizadas[$this->normalizar($fila[22] ?? '')] ?? null;
                $estado_dest_id = $estados_normalizados[$this->normalizar($fila[21] ?? '')] ?? null;

                $municipio_dest_id = null;
                if ($estado_dest_id) {
                    $municipio_dest_id = $municipios_normalizados[$this->normalizar($fila[23] ?? '') . '|' . $estado_dest_id] ?? null;
                }

                $parroquia_dest_id = null;
                if ($municipio_dest_id) {
                    $parroquia_dest_id = $parroquias_normalizados[$this->normalizar($fila[24] ?? '') . '|' . $municipio_dest_id] ?? null;
                }

                $ciudad_dest_id = null;
                if ($estado_dest_id) {
                    $ciudad_dest_id = $ciudades_normalizados[$this->normalizar($fila[25] ?? '') . '|' . $estado_dest_id] ?? null;
                }

                // --- MAPEO INTERNACIONAL ---
                $continente_id = $continentes_normalizados[$this->normalizar($fila[19] ?? '')] ?? null;
                $pais_id = $paises_normalizados[$this->normalizar($fila[20] ?? '')] ?? null;
                $tipo_saca_id = $tipos_saca_normalizados[$this->normalizar($fila[35] ?? '')] ?? null;

                $envio = Envio::create([
                    'servicio_id'           => $servicio_id,
                    'tipo_envio'            => $fila[1] ?? null,
                    'oficina_id'            => $oficina_id,
                    'usuario_id'            => auth()->id(),
                    'nombre_rem'            => strtoupper(trim($fila[3] ?? '')),
                    'apellido_rem'          => strtoupper(trim($fila[4] ?? '')),
                    'tipo_documento_rem'    => $fila[5] ?? null,
                    'documento_rem'         => $fila[6] ?? null,
                    'codigo_postal_rem'     => $fila[7] ?? null,
                    'estado_rem'            => $estado_rem_id,
                    'municipio_rem'         => $municipio_rem_id,
                    'parroquia_rem'         => $parroquia_rem_id,
                    'ciudad_rem'            => $ciudad_rem_id,
                    'direccion_rem'         => $fila[11] ?? null,
                    'correo_rem'            => $fila[12] ?? null,
                    'telefono_rem'          => $fila[13] ?? null,
                    'nombre_dest'           => strtoupper(trim($fila[14] ?? '')),
                    'apellido_dest'         => strtoupper(trim($fila[15] ?? '')),
                    'tipo_documento_dest'   => $fila[16] ?? null,
                    'documento_dest'        => $fila[17] ?? null,
                    'codigo_postal_dest'    => $fila[18] ?? null,
                    'continente_dest'       => $continente_id,
                    'pais_dest'             => $pais_id,
                    'estado_dest'           => $estado_dest_id,
                    'oficina_dest_id'       => $oficina_dest_id,
                    'municipio_dest'        => $municipio_dest_id,
                    'parroquia_dest'        => $parroquia_dest_id,
                    'ciudad_dest'           => $ciudad_dest_id,
                    'direccion_dest'        => $fila[26] ?? null,
                    'tlf_dest'              => $fila[27] ?? null,
                    'correo_dest'           => $fila[28] ?? null,
                    'servicio_expreso'      => $fila[29] ?? null,
                    'peso'                  => $fila[30] ?? null,
                    'coste'                 => $fila[31] ?? null,
                    'contenido'             => $fila[32] ?? null,
                    'apartado_postal'       => is_numeric(trim($fila[33] ?? '')) ? trim($fila[33]) : null,
                    'codigo_envio'          => $fila[34] ?? null,
                    'tipo_saca_id'          => $tipo_saca_id,
                    'devolucion'            => $devolucion,
                    'descubierto'           => $descubierto,
                    'carga_masiva'          => true,
                ]);

                // --- 1. TABLA: envios_encaminamiento (Registro 1 con estatus_id = 1) ---
                EnvioEncaminamiento::create([
                    'envio_id'           => $envio->envio_id,
                    'oficina_id'         => $envio->oficina_id,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $envio->usuario_id,
                    'viaje_id'           => null,
                    'estatus_id'         => 1,
                    'devolucion'         => $envio->devolucion ?? false,
                ]);

                // --- 1. TABLA: envios_encaminamiento (Registro 2 con estatus_id = 2) ---
                EnvioEncaminamiento::create([
                    'envio_id'           => $envio->envio_id,
                    'oficina_id'         => $envio->oficina_id,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $envio->usuario_id,
                    'viaje_id'           => null,
                    'estatus_id'         => 2,
                    'devolucion'         => $envio->devolucion ?? false,
                ]);

                EnvioInternacional::create([
                    'envio_id'     => $envio->envio_id,
                    'pais'         => $envio->pais_dest ?? 'N/A',
                    'tipo_envio'   => $envio->tipo_envio,
                    'lista_correo' => false,
                ]);

                // --- 2. TABLA: envios_almacen (Único registro) ---
                EnvioAlmacen::create([
                    'oficina_id'   => $envio->oficina_id,
                    'envio_id'     => $envio->envio_id,
                    'codigo'       => $envio->codigo_envio, // Asumiendo que viene del Excel y se guardó en envios
                    'saca_id'      => null,
                    'estatus'      => true, // DB requiere boolean, no acepta null
                    'Entrada' => now()->toDateString(), // O ->toDateTimeString() si requiere hora
                    'Salida'  => null,
                ]);
            }
            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se realizó una carga masiva de envíos desde archivo Excel",
            ]);

            DB::commit();

            $this->mensaje = '¡Éxito! Todos los envíos han sido cargados correctamente en el sistema.';
            $this->errores = [];
            $this->fallidas = 0;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CargaMasiva Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            $this->mensaje = 'Lo sentimos, ocurrió un problema inesperado al procesar la carga. Por favor, intente nuevamente o contacte al administrador si el error persiste.';
            $this->fallidas = 1; // Marcamos como error para que la interfaz muestre la alerta roja
        }
    }

    public function render()
    {
        return view('livewire.carga-masiva.carga-masiva');
    }
}
