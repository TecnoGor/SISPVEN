<?php

namespace App\Livewire\Aduana;

use App\Models\AlmacenAduana;
use App\Models\Envio;
use App\Models\EnvioEncaminamiento;
use App\Models\Oficina;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Aduana extends Component
{
    use WithPagination;

    // Estatus involucrados en el flujo de Aduana.
    //
    // Almacén marca la ENTRADA (7) al despachar hacia Aduana; el envío aparece en
    // "Por aceptar" y al aceptarlo solo se crea su fila en almacen_aduana, sin otro
    // encaminamiento. Al despacharlo a la oficina destino se marca la SALIDA (10).
    //
    // Antes se usaban 27/28, que en envios_estatus son "SALIDA/ENTRADA EN ALMACEN
    // BULTO POSTAL" — otro flujo distinto que no corresponde a Aduana.
    const ESTATUS_ENTRADA = 7;  // "Entrada a Aduana"
    const ESTATUS_SALIDA  = 10; // "Salida de Aduana"

    /**
     * Oficinas a las que puede despachar la Aduana de cada oficina.
     * Clave = oficina_id de la Aduana; valor = array de oficina_id destino.
     *
     * La Aduana de CPC (10) despacha a OPT San Martín (37). Cuando otra oficina
     * tenga Aduana, se agrega aquí su listado de destinos.
     */
    const DESTINOS_POR_OFICINA = [
        10 => [37], // CPC → OPT San Martín
    ];

    public $search = '';
    public $servicio_id = '';
    public $desde = '';
    public $hasta = '';
    public $perPage = 15;

    // 'por_aceptar' | 'en_aduana' | 'historico'
    public string $vista = 'por_aceptar';

    // Selección múltiple para aceptación en lote
    public array $seleccionados_aceptar = []; // envio_id

    // Despacho a otra oficina (solo en la vista EN ADUANA).
    public array $seleccionados_despacho = []; // almacen_aduana_id
    public bool $mostrar_modal_despacho = false;
    public $oficina_destino = null;

    public function updatedSearch()     { $this->resetPage(); }
    public function updatedServicioId() { $this->resetPage(); }
    public function updatedDesde()      { $this->resetPage(); }
    public function updatedHasta()      { $this->resetPage(); }
    public function updatedPerPage()    { $this->resetPage(); }

    public function setVista(string $vista)
    {
        if (!in_array($vista, ['por_aceptar', 'en_aduana', 'historico'])) {
            return;
        }
        $this->vista = $vista;
        $this->seleccionados_aceptar = [];
        $this->seleccionados_despacho = [];
        $this->resetPage();
    }

    /**
     * Oficinas a las que puede despachar la Aduana de la oficina del usuario.
     * Vacío = esta oficina no tiene destinos configurados.
     */
    public function getDestinosPermitidosProperty()
    {
        $ids = self::DESTINOS_POR_OFICINA[auth()->user()->oficina_id] ?? [];

        if (empty($ids)) {
            return collect();
        }

        return Oficina::whereIn('oficina_id', $ids)->orderBy('nombre')->get();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'servicio_id', 'desde', 'hasta']);
        $this->resetPage();
    }

    // ─────────── ACEPTACIÓN (POR ACEPTAR → EN ADUANA) ───────────

    public function aceptarSeleccionados()
    {
        if (empty($this->seleccionados_aceptar)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Releer desde BD respetando todos los filtros de POR ACEPTAR
        // (incluye el filtro de tipo_envio = internacional como doble validación).
        // Se traen envio_id + codigo_envio: 'codigo' se copia a almacen_aduana igual
        // que hace la pantalla de EntradaAduana, si no la fila queda con codigo NULL.
        $enviosValidos = $this->queryPorAceptar($oficinaId)
            ->whereIn('envios.envio_id', $this->seleccionados_aceptar)
            ->get(['envios.envio_id', 'envios.codigo_envio']);

        if ($enviosValidos->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_aceptar = [];
            return;
        }

        DB::transaction(function () use ($enviosValidos, $oficinaId, $usuario) {
            foreach ($enviosValidos as $envio) {
                // Sin encaminamiento aquí: la "Entrada a Aduana" (7) ya la registró
                // Almacén al despachar. Aceptar solo materializa la fila del almacén
                // de Aduana; duplicar el 7 ensuciaría el historial del envío.
                AlmacenAduana::create([
                    'oficina_id'         => $oficinaId,
                    'envio_id'           => $envio->envio_id,
                    'codigo'             => $envio->codigo_envio,
                    'estatus'            => true,
                    'Entrada'            => now()->toDateString(),
                    'usuario_ingreso_id' => $usuario->id,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => 'Aceptó ' . $enviosValidos->count() . ' envío(s) en Aduana',
            ]);
        });

        $cantidad = $enviosValidos->count();
        $this->seleccionados_aceptar = [];

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en Aduana.");
    }

    // ─────────── DESPACHO (EN ADUANA → OTRA OFICINA) ───────────

    public function abrirModalDespacho()
    {
        if (empty($this->seleccionados_despacho)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $destinos = $this->destinosPermitidos;

        if ($destinos->isEmpty()) {
            $this->dispatch('alertError', message: 'Esta oficina no tiene destinos configurados para despachar desde Aduana.');
            return;
        }

        // Con un único destino posible (caso CPC → San Martín) se preselecciona,
        // para que el operador solo tenga que confirmar.
        $this->oficina_destino = $destinos->count() === 1
            ? $destinos->first()->oficina_id
            : null;

        $this->mostrar_modal_despacho = true;
    }

    public function cerrarModalDespacho()
    {
        $this->mostrar_modal_despacho = false;
        $this->oficina_destino = null;
    }

    /**
     * Despacha los envíos seleccionados hacia la oficina destino.
     *
     * El envío queda EN TRÁNSITO: sale de almacen_aduana y se registra el
     * encaminamiento de salida con oficina_externa_id = destino. No entra al
     * almacén de esta oficina; es la oficina destino la que lo recibe con
     * Registrar Entrada.
     */
    public function despacharSeleccionados()
    {
        if (empty($this->seleccionados_despacho)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $destinos = $this->destinosPermitidos;
        $destino = $destinos->firstWhere('oficina_id', (int) $this->oficina_destino);

        if (!$destino) {
            $this->dispatch('alertError', message: 'Debes seleccionar una oficina de destino válida.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Revalidar contra BD: solo filas activas de esta oficina.
        $registros = AlmacenAduana::whereIn('almacen_aduana_id', $this->seleccionados_despacho)
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->get();

        if ($registros->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_despacho = [];
            $this->mostrar_modal_despacho = false;
            return;
        }


        DB::transaction(function () use ($registros, $usuario, $oficinaId, $destino) {
            foreach ($registros as $registro) {
                EnvioEncaminamiento::create([
                    'envio_id'           => $registro->envio_id,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => $destino->oficina_id,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => self::ESTATUS_SALIDA,
                    'devolucion'         => false,
                ]);

                // Sin 'observaciones': el modelo lo declara fillable y el Histórico lo
                // muestra, pero la columna no existe en almacen_aduana (desajuste
                // preexistente). Escribirlo aquí revienta el update.
                $registro->update([
                    'estatus'           => false,
                    'Salida'            => now()->toDateString(),
                    'usuario_salida_id' => $usuario->id,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'update',
                'descripcion' => 'Despachó ' . $registros->count() . ' envío(s) desde Aduana hacia ' . $destino->nombre,
            ]);
        });

        $cantidad = $registros->count();
        $this->seleccionados_despacho = [];
        $this->oficina_destino = null;
        $this->mostrar_modal_despacho = false;

        $this->dispatch('alertSuccess', message: "Se despacharon {$cantidad} envío(s) hacia {$destino->nombre}.");
    }

    /**
     * Marca o desmarca de golpe los envíos de la página actual.
     *
     * Solo la página visible: seleccionar todas las páginas dejaría aceptar envíos que
     * el operador nunca vio. Los ya seleccionados en otras páginas se conservan.
     */
    public function toggleSeleccionPagina()
    {
        // pluck sin prefijo de tabla: sobre la colección del paginador, "envios.envio_id"
        // se interpreta como acceso anidado y devuelve null.
        $idsPagina = $this->registrosPorAceptar()
            ->pluck('envio_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $seleccionados = array_map('strval', $this->seleccionados_aceptar);

        // Si ya están todos los de esta página, el clic los quita; si no, los añade.
        if (!array_diff($idsPagina, $seleccionados)) {
            $this->seleccionados_aceptar = array_values(array_diff($seleccionados, $idsPagina));
            return;
        }

        $this->seleccionados_aceptar = array_values(array_unique(array_merge($seleccionados, $idsPagina)));
    }

    // ─────────── QUERIES ───────────

    /**
     * Página actual de "Por aceptar" con los filtros de pantalla ya aplicados.
     * La usan render() y toggleSeleccionPagina(), así el checkbox de la cabecera
     * marca exactamente lo que el operador está viendo.
     */
    protected function registrosPorAceptar()
    {
        return $this->queryPorAceptar(auth()->user()->oficina_id)
            ->with(['servicio', 'oficinas'])
            ->when($this->servicio_id, fn($q) => $q->where('servicio_id', $this->servicio_id))
            ->when($this->desde, fn($q) => $q->whereDate('envios.created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('envios.created_at', '<=', $this->hasta))
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                       ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                       ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('envios.created_at', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Envíos cuyo último encaminamiento es "Entrada a Aduana" en mi oficina,
     * que son internacionales, y que aún no tienen una fila activa en almacen_aduana.
     *
     * El 7 lo marca Almacén al despachar; aquí aparecen hasta que se aceptan y se
     * les crea su fila.
     */
    protected function queryPorAceptar(int $oficinaId)
    {
        return Envio::query()
            ->where('envios.tipo_envio', 'internacional')
            ->whereIn('envios.envio_id', function ($sub) use ($oficinaId) {
                $sub->select('e1.envio_id')
                    ->from('envios_encaminamiento as e1')
                    ->whereRaw('e1.envios_encaminamiento_id = (
                        SELECT MAX(e2.envios_encaminamiento_id)
                        FROM envios_encaminamiento e2
                        WHERE e2.envio_id = e1.envio_id
                    )')
                    ->where('e1.estatus_id', self::ESTATUS_ENTRADA)
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('aduana_actual');
    }

    public function render()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        if ($this->vista === 'por_aceptar') {
            $registros = $this->registrosPorAceptar();
        } elseif ($this->vista === 'en_aduana') {
            $registros = AlmacenAduana::with([
                    'envio.servicio',
                    'envio.oficinas',
                    'usuarioIngreso',
                ])
                ->where('estatus', true)
                ->where('oficina_id', $oficinaId)
                ->when($this->servicio_id, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_id));
                })
                ->when($this->desde, fn($q) => $q->whereDate('Entrada', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('Entrada', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                           ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                           ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('Entrada', 'desc')
                ->paginate($this->perPage);
        } else { // historico
            $registros = AlmacenAduana::with([
                    'envio.servicio',
                    'envio.oficinas',
                    'usuarioIngreso',
                    'usuarioSalida',
                ])
                ->where('estatus', false)
                ->where('oficina_id', $oficinaId)
                ->when($this->servicio_id, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_id));
                })
                ->when($this->desde, fn($q) => $q->whereDate('Salida', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('Salida', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                           ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                           ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('Salida', 'desc')
                ->paginate($this->perPage);
        }

        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.aduana.aduana', [
            'registros' => $registros,
            'servicios' => $servicios,
        ]);
    }
}