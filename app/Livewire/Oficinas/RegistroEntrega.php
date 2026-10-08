<?php

namespace App\Livewire\Oficinas;

use App\Models\TipoPago;
use App\Models\AlmacenAduana;
use App\Models\Envio; // Asegúrate de importar el modelo Envio
use App\Models\EnvioAlmacen;
use App\Models\EnvioInternacional;
use App\Models\EnvioEncaminamiento;
use App\Models\AlmacenAviso;
use App\Models\RegistroEntrega as ModelsRegistroEntrega;
use App\Models\TarifaNacionalConcepto;
use App\Models\TarifaInternacionalConcepto;
use App\Models\FacturacionDestinatario;
use App\Models\Documento;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class RegistroEntrega extends Component
{
    /**
     * Este componente maneja la entrega de UNO o VARIOS envíos con un solo pago.
     *
     * - Entrega individual: ruta entrega/{envio_id} → un solo envío.
     * - Entrega múltiple: ruta con ?ids=1,2,3 → varios envíos del mismo cliente.
     *
     * Cada envío se calcula de forma independiente (peso, avisos, días de
     * almacenaje, servicio y sus flags). El cobro se suma en un "gran total" y
     * el pago recibido se prorratea entre los envíos para cuadrar la caja.
     */

    // IDs de los envíos que se están entregando (uno o varios).
    public array $envio_ids = [];

    // Líneas de cobro: una por envío, cada una con su desglose de conceptos.
    // Estructura de cada línea:
    //  envio_id, codigo_envio, nombre_dest, documento_dest, tipo_documento_dest,
    //  modo_envio, carga_masiva, dias, total_avisos,
    //  monto_almacenaje, monto_aviso, monto_tipo_envio, tipo_cobro_extra,
    //  monto_lista_correo, monto_costos_admin, monto_aduana, paso_por_aduana,
    //  lista_correo, cobra_entrega,
    //  subtotal, iva, total, nombre_servicio
    public array $lineas = [];

    // Almacenaje editable a mano por envío (solo carga masiva), indexado por envio_id.
    public array $almacenaje_manual = [];

    // Datos del destinatario / persona que retira (compartidos en la entrega).
    public $autorizado = false;
    public $nombre;
    public $tipo_documento;
    public $cedula;

    // Totales globales de la entrega.
    public $gran_subtotal = 0;
    public $gran_iva = 0;
    public $gran_total = 0;

    // Pago recibido desde el componente hijo CajaDePago.
    public $monto_pagado = 0;
    public $pagos = [];

    // Catálogos y flags de presentación.
    public $documentos = [];
    public $tarifa_almacenaje;
    public $porcentaje_iva = 0.16;
    public bool $oficina_cobra_iva = true;
    public bool $destinatarios_distintos = false;

    /**
     * @param int|null $envio_id  ID recibido por la ruta individual entrega/{envio_id}.
     */
    public function mount($envio_id = null)
    {
        // Determinar los IDs a entregar: los de ?ids=... o el de la ruta individual.
        $ids = collect(explode(',', (string) request('ids')))
            ->map(fn($v) => (int) trim($v))
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty() && $envio_id) {
            $ids = collect([(int) $envio_id]);
        }

        if ($ids->isEmpty()) {
            abort(404, 'No se especificaron envíos para entregar.');
        }

        $usuario = auth()->user();

        // Solo envíos que existan y estén en el almacén ACTIVO de la oficina del usuario.
        // Esto evita el crash por envío inexistente y que se entregue algo que ya salió.
        $envios = Envio::with('servicio')
            ->whereIn('envio_id', $ids)
            ->whereHas('almacen', function ($q) use ($usuario) {
                $q->where('oficina_id', $usuario->oficina_id)->where('estatus', true);
            })
            ->get();

        if ($envios->isEmpty()) {
            abort(404, 'Los envíos indicados no están disponibles en el almacén de esta oficina.');
        }

        $this->envio_ids = $envios->pluck('envio_id')->all();
        $this->documentos = Documento::all();
        $this->tarifa_almacenaje = TarifaNacionalConcepto::where('tarifa_conceptos_id', 16)->value('monto');

        // La oficina define si aplica IVA (zona económica especial = sin IVA).
        $oficina = $usuario->oficina;
        $this->oficina_cobra_iva = !($oficina && $oficina->zona_economica_especial == true);

        // Datos de la persona que retira: se toman del primer envío por defecto.
        $primero = $envios->first();
        $this->nombre = trim(($primero->nombre_dest ?? '') . ' ' . ($primero->apellido_dest ?? ''));
        $this->tipo_documento = $primero->tipo_documento_dest ?? null;
        $this->cedula = $primero->documento_dest ?? null;

        // Inicializar el almacenaje editable de los envíos de carga masiva.
        foreach ($envios as $envio) {
            if ($envio->carga_masiva) {
                $this->almacenaje_manual[$envio->envio_id] = 0;
            }
        }

        // Advertir (no bloquear) si los destinatarios difieren.
        $this->destinatarios_distintos = $envios
            ->map(fn($e) => trim((string) $e->documento_dest))
            ->filter()
            ->unique()
            ->count() > 1;

        $this->recalcular();
    }

    /**
     * Recalcula todas las líneas y los totales globales.
     * Se llama en mount y cada vez que cambia un toggle (aduana / almacenaje manual).
     */
    public function recalcular()
    {
        $envios = Envio::with('servicio')
            ->whereIn('envio_id', $this->envio_ids)
            ->get()
            ->keyBy('envio_id');

        $lineas = [];
        foreach ($this->envio_ids as $id) {
            $envio = $envios->get($id);
            if (!$envio) {
                continue;
            }
            $lineas[] = $this->calcularLinea($envio);
        }
        $this->lineas = $lineas;

        // Totales globales: suma de subtotales e IVA de cada línea (escala 2 con bc*).
        $subtotal = '0';
        $iva = '0';
        foreach ($this->lineas as $linea) {
            $subtotal = bcadd($subtotal, (string) $linea['subtotal'], 2);
            $iva = bcadd($iva, (string) $linea['iva'], 2);
        }
        $this->gran_subtotal = $subtotal;
        $this->gran_iva = $iva;
        $this->gran_total = bcadd($subtotal, $iva, 2);
    }

    /**
     * Calcula el desglose de conceptos de un envío individual.
     * Réplica de la lógica de cobro original, aplicada por línea.
     */
    protected function calcularLinea(Envio $envio): array
    {
        $id = $envio->envio_id;
        $modo_envio = $envio->tipo_envio == 'nacional';

        // Flags del servicio (default true si no existen).
        $cobra_entrega        = $envio->servicio?->cobra_entrega ?? true;
        $cobra_excedente      = $envio->servicio?->cobra_excedente ?? true;
        $cobra_almacenaje     = $envio->servicio?->cobra_almacenaje ?? true;
        $cobra_avisos_llegada = $envio->servicio?->cobra_avisos_llegada ?? true;

        // --- Costos administrativos (concepto 9): solo si el servicio los cobra ---
        $monto_costos_admin = $cobra_entrega
            ? (TarifaNacionalConcepto::where('tarifa_conceptos_id', 9)->value('monto') ?? 0)
            : 0;

        // --- Conceptos exclusivos de internacional ---
        $lista_correo = false;
        $monto_lista_correo = 0;
        $monto_aduana_base = 0;
        $monto_aduana = 0;
        $paso_por_aduana = false;
        if (!$modo_envio) {
            $lista_correo = (bool) EnvioInternacional::where('envio_id', $id)->value('lista_correo');
            if ($lista_correo) {
                $monto_lista_correo = TarifaInternacionalConcepto::where('tarifa_conceptos_internacional_id', 2)->value('monto') ?? 0;
            }

            // La presentación en aduana se cobra si el envío realmente pasó por
            // Aduana, no a criterio del operador. La marca es la fila en
            // almacen_aduana: la crean tanto el módulo Aduana como la pantalla
            // EntradaAduana, y sobrevive a la salida (queda con estatus = false),
            // así que sirve igual para un envío que ya salió de aduana.
            $paso_por_aduana = AlmacenAduana::where('envio_id', $id)->exists();

            if ($paso_por_aduana) {
                $monto_aduana_base = TarifaInternacionalConcepto::where('tarifa_conceptos_internacional_id', 7)->value('monto') ?? 0;
                $monto_aduana = $monto_aduana_base;
            }
        }

        // --- Excedente de peso (concepto 20 nac. / 8 int.): solo si el servicio lo cobra ---
        $monto_tipo_envio = 0;
        $tipo_cobro_extra = '';
        if ($cobra_excedente) {
            if ($modo_envio && $envio->peso >= 2000) {
                $monto_tipo_envio = TarifaNacionalConcepto::where('tarifa_conceptos_id', 20)->value('monto') ?? 0;
                $tipo_cobro_extra = 'nacional 2kg';
            } elseif (!$modo_envio && $envio->peso >= 500) {
                $monto_tipo_envio = TarifaInternacionalConcepto::where('tarifa_conceptos_internacional_id', 8)->value('monto') ?? 0;
                $tipo_cobro_extra = 'internacional 500gr';
            }
        }

        // --- Avisos de llegada (concepto 17): solo si el servicio los cobra ---
        $total_avisos = AlmacenAviso::where('envio_id', $id)->count();
        $monto_aviso = 0;
        if ($cobra_avisos_llegada && $total_avisos >= 1) {
            $tarifa_aviso = TarifaNacionalConcepto::where('tarifa_conceptos_id', 17)->value('monto') ?? 0;
            $monto_aviso = bcmul((string) $total_avisos, (string) $tarifa_aviso, 2);
        }

        // --- Almacenaje (concepto 16): días desde el primer aviso × tarifa ---
        $primer_aviso = AlmacenAviso::where('envio_id', $id)
            ->orderBy('created_at', 'asc')
            ->value('created_at');
        $dias = 0;
        if ($primer_aviso) {
            $dias = (int) Carbon::parse($primer_aviso)->diffInDays(Carbon::now());
        }

        if ($envio->carga_masiva) {
            // Carga masiva: el operador puede editar el monto a mano (formato 1.500,50).
            $raw = (string) ($this->almacenaje_manual[$id] ?? 0);
            $monto_almacenaje = number_format(
                floatval(str_replace(',', '.', str_replace('.', '', $raw))),
                2,
                '.',
                ''
            );
        } elseif ($cobra_almacenaje) {
            $monto_almacenaje = bcmul((string) $dias, (string) $this->tarifa_almacenaje, 2);
        } else {
            $monto_almacenaje = '0.00';
        }

        // --- Subtotal, IVA y total de la línea (todo con bc*, escala 2) ---
        $subtotal = '0';
        foreach ([$monto_almacenaje, $monto_aviso, $monto_tipo_envio, $monto_lista_correo, $monto_costos_admin, $monto_aduana] as $concepto) {
            $subtotal = bcadd($subtotal, (string) $concepto, 2);
        }

        $iva = $this->oficina_cobra_iva
            ? bcmul($subtotal, (string) $this->porcentaje_iva, 2)
            : '0.00';
        $total = bcadd($subtotal, $iva, 2);

        // "Cobrable" = el envío genera algún cobro. Se usa para decidir si mostrar
        // el desglose.
        $cobrable = bccomp($subtotal, '0', 2) > 0;

        return [
            'envio_id'            => $id,
            'codigo_envio'        => $envio->codigo_envio ?? 'N/A',
            'nombre_dest'         => trim(($envio->nombre_dest ?? '') . ' ' . ($envio->apellido_dest ?? '')),
            'documento_dest'      => $envio->documento_dest,
            'tipo_documento_dest' => $envio->tipo_documento_dest,
            'servicio_id'         => $envio->servicio_id,
            'nombre_servicio'     => $envio->servicio?->nombre ?? 'No disponible',
            'modo_envio'          => $modo_envio,
            'carga_masiva'        => (bool) $envio->carga_masiva,
            'peso'                => $envio->peso,
            'dias'                => $dias,
            'total_avisos'        => $total_avisos,
            'lista_correo'        => $lista_correo,
            'cobra_entrega'       => $cobra_entrega,
            'monto_almacenaje'    => $monto_almacenaje,
            'monto_aviso'         => $monto_aviso,
            'monto_tipo_envio'    => $monto_tipo_envio,
            'tipo_cobro_extra'    => $tipo_cobro_extra,
            'monto_lista_correo'  => $monto_lista_correo,
            'monto_costos_admin'  => $monto_costos_admin,
            'monto_aduana'        => $monto_aduana,
            'paso_por_aduana'     => $paso_por_aduana,
            'subtotal'            => $subtotal,
            'iva'                 => $iva,
            'total'              => $total,
            'cobrable'            => $cobrable,
        ];
    }

    // Recalcular cuando el operador edita el almacenaje manual (carga masiva).
    public function updatedAlmacenajeManual()
    {
        $this->recalcular();
    }

    // Evento desde el componente hijo CajaDePago.
    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    /**
     * Reparte un pago total entre las líneas de forma proporcional al total de
     * cada línea, usando aritmética bc* a escala 2. El residuo de redondeo se
     * asigna a la última línea con cobro para garantizar que la suma de las
     * partes sea exactamente igual al monto repartido (caja cuadrada).
     *
     * @param  string $monto        Monto total a repartir (ej. de un método de pago).
     * @param  array  $totalesLinea [envio_id => total] solo de líneas con total > 0.
     * @return array  [envio_id => monto_asignado]
     */
    protected function prorratear(string $monto, array $totalesLinea): array
    {
        $granTotal = '0';
        foreach ($totalesLinea as $t) {
            $granTotal = bcadd($granTotal, (string) $t, 2);
        }

        // Si no hay base para repartir, todo al primero (o vacío).
        if (bccomp($granTotal, '0', 2) <= 0) {
            return [];
        }

        $asignado = [];
        $acumulado = '0';
        $ids = array_keys($totalesLinea);
        $ultimo = end($ids);

        foreach ($totalesLinea as $id => $total) {
            if ($id === $ultimo) {
                // La última línea absorbe el residuo: monto - lo ya repartido.
                $parte = bcsub($monto, $acumulado, 2);
            } else {
                // parte = monto * (total_linea / granTotal), a escala 2.
                $parte = bcdiv(bcmul($monto, (string) $total, 4), $granTotal, 2);
                $acumulado = bcadd($acumulado, $parte, 2);
            }
            $asignado[$id] = $parte;
        }

        return $asignado;
    }

    public function store()
    {
        // Validar pago si hay un total a cobrar.
        if (bccomp((string) $this->gran_total, '0', 2) > 0
            && bccomp((string) $this->monto_pagado, (string) $this->gran_total, 2) < 0) {
            $this->dispatch('alertSuccess2', message: 'La cantidad pagada no puede ser menor que el costo total.');
            return;
        }

        $usuario = auth()->user();

        DB::beginTransaction();

        try {
            // Revalidar concurrencia: bloquear los EnvioAlmacen activos de esta oficina.
            $almacenes = EnvioAlmacen::whereIn('envio_id', $this->envio_ids)
                ->where('oficina_id', $usuario->oficina_id)
                ->where('estatus', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('envio_id');

            // Solo se entregan los envíos que siguen activos en el almacén.
            $idsValidos = array_values(array_filter(
                $this->envio_ids,
                fn($id) => $almacenes->has($id)
            ));

            if (empty($idsValidos)) {
                DB::rollBack();
                $this->dispatch('alertSuccess2', message: 'Los envíos seleccionados ya no están disponibles en el almacén.');
                return;
            }

            // Líneas con cobro (total > 0) para el prorrateo, respetando el orden.
            $totalesLinea = [];
            foreach ($this->lineas as $linea) {
                if (in_array($linea['envio_id'], $idsValidos, true)
                    && bccomp((string) $linea['total'], '0', 2) > 0) {
                    $totalesLinea[$linea['envio_id']] = (string) $linea['total'];
                }
            }

            // Prorratear cada método de pago entre las líneas con cobro.
            // Resultado: [envio_id => [ ['tipo_pago_id'=>.., 'monto'=>..], ... ]]
            $facturacionPorEnvio = [];
            if (!empty($totalesLinea)) {
                foreach ($this->pagos as $pago) {
                    $reparto = $this->prorratear((string) $pago['monto'], $totalesLinea);
                    foreach ($reparto as $envioId => $montoAsignado) {
                        if (bccomp((string) $montoAsignado, '0', 2) <= 0) {
                            continue;
                        }
                        $facturacionPorEnvio[$envioId][] = [
                            'tipo_pago_id' => $pago['tipo_pago_id'],
                            'monto'        => $montoAsignado,
                        ];
                    }
                }
            }

            // IVA prorrateado por envío (para la fila de facturación).
            $ivaLinea = [];
            foreach ($this->lineas as $linea) {
                $ivaLinea[$linea['envio_id']] = (string) $linea['iva'];
            }

            $lineasPorId = collect($this->lineas)->keyBy('envio_id');

            foreach ($idsValidos as $envioId) {
                $linea = $lineasPorId->get($envioId);

                // Datos de quien recibe:
                //  - Retiro por tercero: una sola persona autorizada retira todo → datos del formulario.
                //  - Retiro normal: cada envío guarda su propio destinatario (pueden ser distintos).
                if ($this->autorizado) {
                    $nombreReceptor    = $this->nombre;
                    $documentoReceptor = $this->cedula;
                    $tipoDocReceptor   = $this->tipo_documento;
                } else {
                    $nombreReceptor    = $linea['nombre_dest'];
                    $documentoReceptor = $linea['documento_dest'];
                    $tipoDocReceptor   = $linea['tipo_documento_dest'];
                }

                $registro = ModelsRegistroEntrega::create([
                    'usuario_id'                => $usuario->id,
                    'oficina_id'                => $usuario->oficina_id,
                    'envio_id'                  => $envioId,
                    'servicio_id'               => $linea['servicio_id'],
                    'nacional?'                 => $linea['modo_envio'],
                    'codigo_envio'              => $linea['codigo_envio'],
                    'cedula_remitente'          => $documentoReceptor,
                    'nombre_remitente'          => $nombreReceptor,
                    'costo_total'               => $linea['total'],
                    'coste_aviso'               => $linea['monto_aviso'],
                    'coste_almacenaje'          => $linea['monto_almacenaje'],
                    'autorizado'                => $this->autorizado,
                    'tipo_cobro_extra'          => $linea['tipo_cobro_extra'] ?: null,
                    'monto_cobro_extra'         => $linea['monto_tipo_envio'] ?? 0,
                    'lista_correo'              => $linea['monto_lista_correo'],
                    'dias_almacenaje'           => $linea['dias'],
                    'coste_administrativo'      => $linea['monto_costos_admin'],
                    'coste_presentacion_aduana' => $linea['monto_aduana'],
                ]);

                // Filas de facturación (una por método de pago prorrateado a este envío).
                foreach (($facturacionPorEnvio[$envioId] ?? []) as $fila) {
                    FacturacionDestinatario::create([
                        'usuario_id'          => $usuario->id,
                        'envio_id'            => $envioId,
                        'registro_entrega_id' => $registro->registro_entrega_id,
                        'nombre'              => $nombreReceptor,
                        'tipo_documento'      => $tipoDocReceptor,
                        'documento'           => $documentoReceptor,
                        'tipo_pago_id'        => $fila['tipo_pago_id'],
                        'monto'               => $fila['monto'],
                        'iva'                 => $ivaLinea[$envioId] ?? 0,
                    ]);
                }

                // Encaminamiento: estatus 17 = entregado.
                EnvioEncaminamiento::create([
                    'usuario_id' => $usuario->id,
                    'envio_id'   => $envioId,
                    'oficina_id' => $usuario->oficina_id,
                    'estatus_id' => 17,
                    'devolucion' => false,
                ]);

                // Marcar salido del almacén.
                $almacenes->get($envioId)->update([
                    'estatus' => false,
                    'Salida'  => now()->toDateTimeString(),
                ]);
            }

            // Auditoría: un solo registro por la acción real de entrega (no por carga de pantalla).
            $cantidad = count($idsValidos);
            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => "Registró la entrega de {$cantidad} envío(s): " . implode(', ', $idsValidos),
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Se registró la entrega exitosamente!');
            $this->redirect(route('ver-almacen'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alertSuccess2', message: 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tipopagos = TipoPago::where('activo', true)->get();

        return view('livewire.oficinas.registro-entrega', [
            'tipopagos' => $tipopagos,
        ]);
    }
}