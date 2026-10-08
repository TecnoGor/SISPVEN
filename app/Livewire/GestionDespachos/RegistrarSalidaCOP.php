<?php

namespace App\Livewire\GestionDespachos;

use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioDescubiertoDespacho;
use App\Models\EnvioDistribucionPaqueteMuestra;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioExpedicion;
use App\Models\EnvioSaca;
use App\Models\Estado;
use App\Models\Manifiesto;
use App\Models\ManifiestoPaquete;
use App\Models\NumeroDespachoOficina;
use App\Models\Oficina;
use App\Models\RutaPuntoEntrega;
use App\Models\Saca;
use App\Models\SacaEncaminamiento;
use App\Models\UsuarioSeguimiento;
use App\Models\Viaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RegistrarSalidaCOP extends Component
{
    use WithPagination;

    // En CPC los envíos disponibles para despachar vienen de Expedición + Distribución Paquetes Muestras,
    // no del almacén general.
    const OFICINA_CON_AREAS_INTERNAS = 10;
    const SERVICIO_BULTO = 23;
    const SERVICIO_EMS   = 21;

    // Las valijas de carga por peso (ej. "Valija de Peso") por diseño NO llevan envios
    // dentro. Son las unicas que pueden despacharse vacias; el resto requiere al menos
    // un envio. La condicion se configura en `tipos_sacas.cargar_por_peso` desde
    // Parametros de Valijas (antes era un id de tipo fijo en el codigo).

    // Selectores y datos de oficina/viaje/transferencia
    public $search = '';
    public $perPage = 30;
    public $sortBy = 'saca_id';
    public $sortDir = 'ASC';
    public $expandedRutas = [];
    public $selectedViajeId = null;
    public $transferenciaOficina = null;
    public $oficinaRelacionadaId = null;
    public $filteredOficinas = [];
    public $oficinaSeleccionada = null;
    public $estadoSeleccionado = null;
    public $seleccionadosParaSalida = [];

    // Filtro del buscador de oficinas para transferencia directa. Una COP cubre varios
    // estados, asi que la lista puede pasar de 50 oficinas: sin filtro el operador tiene
    // que hacer scroll por toda la pagina para elegir una sola.
    public $busquedaOficina = '';

    // Filtro activo tras escanear un codigo. El buscador NO agrega al carrito: localiza
    // el despacho (o el envio al descubierto) y deja la tabla filtrada para que el
    // operador pulse "Agregar". Todo sale por despacho, nunca valija suelta.
    public $filtroDespachoId = null;   // despacho a mostrar en la tarjeta de despachos
    public $filtroEnvioId = null;      // envio a mostrar en la tarjeta de sueltos
    public $filtroEtiqueta = '';       // texto del chip ("valija CP014-…")
    public $filtroTab = '';            // 'despachos' | 'sueltos': pestaña a abrir

    /** Limpia el filtro del buscador y devuelve las listas completas. */
    public function limpiarFiltro()
    {
        $this->filtroDespachoId = null;
        $this->filtroEnvioId = null;
        $this->filtroEtiqueta = '';
        $this->filtroTab = '';
        $this->pageDespachos = 1;
        $this->pageSueltos = 1;
    }

    // NUEVAS propiedades para lógica de almacén y buscador
    public $busquedaCodigo = '';

    /**
     * saca_id de la valija que el operador quiere eliminar del despacho. Mientras
     * no sea null la vista muestra el aviso de confirmacion: borrar la valija es
     * irreversible y hay que decir cuantos envios se liberan.
     */
    public $valijaAEliminar = null;

    // El carrito SI viaja entre peticiones: es la seleccion del operador y no se puede
    // reconstruir desde la BD. Es pequeño (solo lo agregado).
    public $enviosConYSinSaca = [];

    // Estas tres listas son DERIVADAS del almacen: se recalculan enteras en cada
    // peticion desde hydrate(). Son 'protected' a proposito — como propiedades publicas
    // Livewire las serializaba al HTML y el navegador las devolvia integras en cada clic
    // (en la CPC son miles de envios), y ademas firmaba el payload en el hilo principal:
    // de ahi los segundos de espera y el spinner congelado al agregar/quitar.
    protected $almacenDisponibles = [];
    protected $despachosDisponibles = []; // Despachos (valijas agrupadas por numero_despacho_id) listos para salida.
    protected $enviosSueltos = [];        // Envios sin valija, se listan/despachan aparte.

    /**
     * Livewire no conserva las propiedades protected entre peticiones, asi que las
     * listas derivadas se reconstruyen aqui, antes de ejecutar la accion del usuario.
     */
    public function hydrate()
    {
        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();
    }

    public $sacas = [];
    public $enviosDisponibles = [];

    // Métodos para selectores múltiples (puedes mantenerlos si los usas)
    public $selectedSacas = [];
    public $selectAllSacas = false;
    public $selectedEnvios = [];
    public $selectAllEnvios = false;

    // Paginación independiente de cada tarjeta (despachos / envíos sueltos).
    public $pageDespachos = 1;
    public $pageSueltos = 1;
    const POR_PAGINA_TARJETA = 15;

    public function irPaginaDespachos($pagina)
    {
        $this->pageDespachos = max(1, (int) $pagina);
    }

    public function irPaginaSueltos($pagina)
    {
        $this->pageSueltos = max(1, (int) $pagina);
    }

    // Modo de destino actual: 'viaje' | 'transferencia' | ''. Controla qué grid se
    // muestra y garantiza que solo una fuente de destino esté seleccionada a la vez.
    public $modoDestino = '';

    public function updatedModoDestino()
    {
        // Al cambiar de modo, limpiar la selección del modo contrario para no
        // arrastrar un destino que ya no está visible.
        $this->selectedViajeId = null;
        $this->transferenciaOficina = null;
    }

    /**
     * Alterna la selección de un viaje: si se hace clic en el ya seleccionado, se
     * deselecciona; si no, se selecciona (y se limpia cualquier transferencia).
     */
    public function seleccionarViaje($viajeId)
    {
        $this->selectedViajeId = ($this->selectedViajeId == $viajeId) ? null : $viajeId;
        $this->transferenciaOficina = null;
    }

    /**
     * Alterna la selección de una oficina de transferencia (mismo comportamiento
     * de toggle que seleccionarViaje).
     */
    public function seleccionarTransferencia($oficinaId)
    {
        $this->transferenciaOficina = ($this->transferenciaOficina == $oficinaId) ? null : $oficinaId;
        $this->selectedViajeId = null;
    }

    public function mount()
    {
        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();
    }

    public function updatedEstadoSeleccionado($estadoId)
    {
        if (!$estadoId) {
            $this->filteredOficinas = [];
        } else {
            $this->filteredOficinas = Oficina::where(function ($query) use ($estadoId) {
                $query->where('estado_id', $estadoId)->where('externa', false);
            })->get();
            $this->oficinaSeleccionada = null;
        }
    }

    public function cargarDespachosDisponibles()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // 1. Buscar envíos disponibles en esta oficina
        // En CPC vienen de Expedición + Distribución; en el resto, del almacén general.
        if ($oficinaId === self::OFICINA_CON_AREAS_INTERNAS) {
            $enviosDisponibles = $this->cargarDesdeAreasInternas($oficinaId, $usuario);
        } else {
            $enviosDisponibles = $this->cargarDesdeAlmacenGeneral($oficinaId);
        }

        // Recoger IDs de esos envíos
        $enviosIds = $enviosDisponibles->pluck('envio_id');

        // 2. Buscar relaciones EnvioSaca solo de los envíos disponibles
        $enviosEnSacas = EnvioSaca::whereIn('envio_id', $enviosIds)->get();

        // Agrupar por saca_id
        $sacasAgrupadas = $enviosEnSacas->groupBy('saca_id');

        $enviosEnSacaActiva = collect();
        $sacasParaMostrar = collect();

        // 3. Revisar cada saca: solo mostrar la saca si TODOS sus envíos disponibles tienen activo=true
        foreach ($sacasAgrupadas as $sacaId => $relaciones) {
            // Solo tomar los envíos disponibles en almacén
            $enviosDeEstaSaca = $relaciones->whereIn('envio_id', $enviosIds);

            // Si todos los envíos disponibles en esta saca tienen activo=true, mostrar la saca
            if ($enviosDeEstaSaca->count() > 0 && $enviosDeEstaSaca->every(fn($rel) => $rel->activo)) {
                $saca = Saca::find($sacaId);
                if ($saca) {
                    $sacasParaMostrar->push($saca);
                    // Marcar estos envíos como "ya agrupados"
                    $enviosEnSacaActiva = $enviosEnSacaActiva->merge($enviosDeEstaSaca->pluck('envio_id'));
                }
            }
            // Si no, esos envíos se mostrarán como sueltos abajo
        }

        // 3.1 Incluir las VALIJAS DE PESO que estan en esta oficina. Por diseño
        // no llevan envios dentro, asi que el flujo de arriba (que descubre valijas por sus
        // envios) nunca las encontraria y partiria el despacho. SOLO las de peso se rescatan
        // vacias; el resto de valijas requiere al menos un envio para salir.
        $yaPresentes = $sacasParaMostrar->pluck('saca_id');

        $sacasPeso = Saca::whereHas('tipoSaca', fn($q) => $q->where('cargar_por_peso', true))
            ->where('cerrado', true)
            ->whereNotIn('saca_id', $yaPresentes)
            ->with('encaminamientoActual')
            ->get()
            ->filter(function ($saca) use ($oficinaId) {
                // La valija de peso debe estar fisicamente en ESTA oficina y no en transito:
                // sin encaminamiento (recien creada en su origen) o su ultimo movimiento la
                // deja en esta oficina sin haber salido.
                $ult = $saca->encaminamientoActual;
                if (!$ult) {
                    return $saca->oficina_id == $oficinaId;
                }
                return (int) $ult->saca_estatus_id !== Saca::ESTATUS_TRANSITO
                    && $ult->oficina_id == $oficinaId;
            });

        $sacasParaMostrar = $sacasParaMostrar->merge($sacasPeso);

        // 4. Envíos sueltos: los que no están en ninguna saca activa
        $enviosSinSaca = $enviosDisponibles->filter(function ($registro) use ($enviosEnSacaActiva) {
            return !$enviosEnSacaActiva->contains($registro->envio_id);
        });

        $this->almacenDisponibles = [];

        // Estas listas son propiedades públicas: Livewire las serializa y las manda al
        // navegador (y de vuelta) en CADA petición. Guardar modelos Eloquent completos
        // hacía que una oficina con miles de envíos moviera decenas de MB por clic, y de
        // ahí venía la lentitud al paginar. Por eso se guarda solo lo que la vista pinta.
        foreach ($enviosSinSaca as $registro) {
            $envio = $registro->envio;
            if (!$envio) {
                continue;
            }

            $this->almacenDisponibles[] = [
                'tipo'     => 'envio',
                'envio_id' => $envio->envio_id,
                'codigo'   => $envio->codigo_envio,
                'servicio' => $envio->Servicio->nombre ?? '-',
                'peso'     => $envio->peso,
            ];
        }

        // Añadir las sacas válidas, anexando el despacho al que pertenecen.
        // El despacho (numero_despacho_id) viaja con la valija: una COP intermedia
        // ve el numero original. Las sacas se agrupan por despacho en la vista.
        foreach ($sacasParaMostrar as $saca) {
            $this->almacenDisponibles[] = [
                'tipo'           => 'saca',
                'saca_id'        => $saca->saca_id,
                'codigo'         => $saca->codigo_saca,
                'servicio'       => $saca->tipoSaca->nombre_referencial ?? '-',
                'peso'           => $saca->peso,
                'despacho_id'    => $saca->numero_despacho_id,
                'despacho_num'   => $saca->numeroDespacho->numero_despacho ?? null,
            ];
        }

        // Construir la lista AGRUPADA por despacho para la vista: una fila por despacho
        // (numero, destino, conteo de valijas, peso total). Asi el operador da salida al
        // despacho completo de un clic, sin agregar valija por valija.
        $this->despachosDisponibles = $sacasParaMostrar
            ->groupBy('numero_despacho_id')
            ->map(function ($sacasDelDespacho, $despachoId) {
                $primera = $sacasDelDespacho->first();
                return [
                    'despacho_id'   => $despachoId ?: null,
                    'despacho_num'  => $primera->numeroDespacho->numero_despacho ?? null,
                    'destino'       => $primera->oficinaDestino->nombre ?? '-',
                    'cantidad'      => $sacasDelDespacho->count(),
                    'peso_total'    => $sacasDelDespacho->sum(fn($s) => (float) ($s->peso ?? 0)),
                    'saca_ids'      => $sacasDelDespacho->pluck('saca_id')->all(),
                ];
            })
            ->values()
            ->toArray();

        // Envios sueltos para la seccion aparte.
        $sueltos = collect($this->almacenDisponibles)
            ->where('tipo', 'envio')
            ->values();

        // Marcar a que despacho pertenece cada suelto, para mostrarlo en la lista
        // (numero + destino).
        //
        // NO se filtra por `activo`: un despacho puede estar cerrado (guia
        // confirmada) y su suelto seguir aqui sin despachar. Filtrando por activo
        // la lista lo mostraba como libre y permitia asociarlo a un segundo
        // despacho, duplicando la asociacion.
        //
        // Estos envios vienen de `almacenDisponibles`, o sea que estan presentes
        // en la oficina: si tienen asociacion, es la vigente.
        $envioIdsSueltos = $sueltos->pluck('envio_id')->filter()->all();

        $asociaciones = EnvioDescubiertoDespacho::with('numeroDespacho.oficinaDestino')
            ->whereIn('envio_id', $envioIdsSueltos)
            ->get()
            ->keyBy('envio_id');

        $this->enviosSueltos = $sueltos->map(function ($item) use ($asociaciones) {
            $asociacion = $asociaciones[$item['envio_id']] ?? null;

            $item['despacho_id']  = $asociacion?->numero_despacho_id;
            $item['despacho_num'] = $asociacion?->numeroDespacho?->numero_despacho;
            $item['despacho_destino'] = $asociacion?->numeroDespacho?->oficinaDestino?->nombre;

            return $item;
        })->toArray();
    }

    /**
     * Fuente de envíos para COPs normales: envios_almacen activos en la oficina.
     */
    protected function cargarDesdeAlmacenGeneral(int $oficinaId)
    {
        // El servicio se carga por adelantado: la lista lo pinta por cada envio y sin esto
        // se dispara una consulta por envio (N+1) en cada agregar/quitar del carrito.
        return EnvioAlmacen::with('envio.Servicio')
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->get();
    }

    /**
     * Fuente de envíos para CPC: une envios_expedicion + envios_distribucion_paquetes_muestras
     * activos en la oficina. Expedición se filtra por rol (EMS / Bulto) si aplica;
     * Distribución no aplica filtro por rol (cualquier usuario autorizado ve todos los SPU).
     */
    protected function cargarDesdeAreasInternas(int $oficinaId, $usuario)
    {
        $expedicion = EnvioExpedicion::with('envio.Servicio')
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->when($usuario->hasRole('Clasificador EMS'), function ($q) {
                $q->whereHas('envio', fn($sub) => $sub->where('servicio_id', self::SERVICIO_EMS));
            })
            ->when($usuario->hasRole('Clasificador Bultos'), function ($q) {
                $q->whereHas('envio', fn($sub) => $sub->where('servicio_id', self::SERVICIO_BULTO));
            })
            ->get();

        $distribucion = EnvioDistribucionPaqueteMuestra::with('envio.Servicio')
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->get();

        // Ambas colecciones devuelven objetos con ->envio_id y ->envio, así que
        // la lógica de agrupamiento en cargarDespachosDisponibles() los trata igual.
        return $expedicion->concat($distribucion)->values();
    }

    public function filtrarAlmacenDisponibles()
    {
        // Filtra los que ya están seleccionados arriba
        $seleccionados = collect($this->enviosConYSinSaca)->map(function ($item) {
            return [
                'tipo' => $item['tipo'],
                'id' => $item['tipo'] === 'saca' ? $item['saca_id'] : $item['envio_id'],
            ];
        });

        $this->almacenDisponibles = collect($this->almacenDisponibles)->filter(function ($item) use ($seleccionados) {
            $id = $item['tipo'] === 'saca' ? $item['saca_id'] : $item['envio_id'];
            return !$seleccionados->contains(function ($sel) use ($item, $id) {
                return $sel['tipo'] === $item['tipo'] && $sel['id'] == $id;
            });
        })->values()->toArray();

        // IDs de sacas ya en el carrito, para no reofertar despachos/valijas ya agregadas.
        $sacaIdsEnCarrito = $seleccionados->where('tipo', 'saca')->pluck('id');

        // Un despacho deja de ofrecerse cuando TODAS sus valijas ya estan en el carrito.
        $this->despachosDisponibles = collect($this->despachosDisponibles)
            ->map(function ($despacho) use ($sacaIdsEnCarrito) {
                $restantes = collect($despacho['saca_ids'])
                    ->reject(fn($id) => $sacaIdsEnCarrito->contains($id))
                    ->values();
                $despacho['saca_ids'] = $restantes->all();
                $despacho['cantidad'] = $restantes->count();
                return $despacho;
            })
            ->filter(fn($despacho) => $despacho['cantidad'] > 0)
            ->values()
            ->toArray();

        // Envios sueltos ya agregados tampoco se reofrecen.
        $envioIdsEnCarrito = $seleccionados->where('tipo', 'envio')->pluck('id');
        $this->enviosSueltos = collect($this->enviosSueltos)
            ->reject(fn($item) => $envioIdsEnCarrito->contains($item['envio_id'] ?? null))
            ->values()
            ->toArray();
    }

    /**
     * Item de carrito para una valija. Igual que las listas de disponibles, guarda solo
     * los campos que se pintan: el carrito también es propiedad pública y viaja en cada
     * petición de Livewire.
     */
    protected function itemCarritoSaca(Saca $saca): array
    {
        return [
            'tipo'         => 'saca',
            'saca_id'      => $saca->saca_id,
            'codigo'       => $saca->codigo_saca,
            'servicio'     => $saca->tipoSaca->nombre_referencial ?? '-',
            'peso'         => $saca->peso,
            'despacho_id'  => $saca->numero_despacho_id,
            'despacho_num' => $saca->numeroDespacho->numero_despacho ?? null,
        ];
    }

    /** Item de carrito para un envío suelto, opcionalmente asociado a un despacho. */
    protected function itemCarritoEnvio(Envio $envio, $despachoId = null, $despachoNum = null): array
    {
        return [
            'tipo'         => 'envio',
            'envio_id'     => $envio->envio_id,
            'codigo'       => $envio->codigo_envio,
            'servicio'     => $envio->Servicio->nombre ?? '-',
            'peso'         => $envio->peso,
            'despacho_id'  => $despachoId,
            'despacho_num' => $despachoNum,
        ];
    }

    /**
     * ¿El envio sigue presente en esta oficina, listo para despacharse?
     *
     * Es la señal de que su asociacion a un despacho es VIGENTE. No sirve mirar
     * manifiestos_paquetes: esa tabla es historica acumulativa, asi que un envio
     * que llego desde otra oficina ya figura en el manifiesto de aquel tramo aunque
     * este aqui y sin despachar.
     *
     * Se consulta la MISMA fuente de la que se cargan los envios: en CPC,
     * Expedicion + Distribucion; en el resto, el almacen general.
     */
    protected function envioDisponibleEnOficina($envioId): bool
    {
        $oficinaId = auth()->user()->oficina_id;

        if ($oficinaId === self::OFICINA_CON_AREAS_INTERNAS) {
            return EnvioExpedicion::where('envio_id', $envioId)
                    ->where('oficina_id', $oficinaId)
                    ->where('estatus', true)
                    ->exists()
                || EnvioDistribucionPaqueteMuestra::where('envio_id', $envioId)
                    ->where('oficina_id', $oficinaId)
                    ->where('estatus', true)
                    ->exists();
        }

        return EnvioAlmacen::where('envio_id', $envioId)
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->exists();
    }

    /**
     * Localiza el codigo escaneado y FILTRA la lista, en vez de agregar al carrito.
     * Todo sale por despacho: el operador escanea para encontrar el despacho y luego
     * pulsa "Agregar". Una valija suelta ya no entra al carrito por si sola.
     *
     * - Codigo de valija -> filtra la tarjeta de despachos al despacho que la contiene.
     * - Codigo de envio  -> filtra la tarjeta de envios al descubierto a ese envio.
     */
    public function agregarPorCodigo()
    {
        $this->busquedaCodigo = preg_replace("/'/", '-', $this->busquedaCodigo);

        $codigo = trim($this->busquedaCodigo);

        if (!$codigo) {
            $this->busquedaCodigo = '';
            return;
        }

        $oficinaId = auth()->user()->oficina_id;

        // 1. Buscar valija (saca) por codigo.
        $saca = Saca::where('codigo_saca', $codigo)->first();
        if ($saca) {
            $this->busquedaCodigo = '';
            $this->localizarValija($saca, $oficinaId);
            return;
        }

        // 2. Buscar envio por codigo.
        $envio = Envio::where('codigo_envio', $codigo)->first();
        if ($envio) {
            $this->busquedaCodigo = '';
            $this->localizarEnvio($envio, $oficinaId);
            return;
        }

        $this->busquedaCodigo = '';
        $this->dispatch('alertError', message: 'Código no encontrado.');
    }

    /**
     * Deja la tarjeta de despachos mostrando solo el despacho de esta valija.
     * Avisa con el motivo concreto cuando no se puede.
     */
    protected function localizarValija(Saca $saca, int $oficinaId): void
    {
        $usaAreasInternas = ($oficinaId === self::OFICINA_CON_AREAS_INTERNAS);

        // Las valijas de carga por peso no se validan contra envios (no tienen).
        // Basta con que esten cerradas y fisicamente en esta oficina, que es lo que
        // ya comprobo la consulta que las rescata en cargarDespachosDisponibles.
        $esPeso = (bool) ($saca->tipoSaca->cargar_por_peso ?? false);

        // La validez de la valija se chequea contra las fuentes correspondientes a la oficina.
        // En CPC: envios_expedicion o envios_distribucion_paquetes_muestras.
        // En el resto: envios_almacen.
        if ($esPeso) {
            $valijaDisponible = true;
        } elseif ($usaAreasInternas) {
            $valijaDisponible = EnvioSaca::where('saca_id', $saca->saca_id)
                ->where('activo', true)
                ->where(function ($q) use ($oficinaId) {
                    $q->whereIn('envio_id', function ($sub) use ($oficinaId) {
                        $sub->select('envio_id')
                            ->from('envios_expedicion')
                            ->where('oficina_id', $oficinaId)
                            ->where('estatus', true);
                    })->orWhereIn('envio_id', function ($sub) use ($oficinaId) {
                        $sub->select('envio_id')
                            ->from('envios_distribucion_paquetes_muestras')
                            ->where('oficina_id', $oficinaId)
                            ->where('estatus', true);
                    });
                })
                ->exists();
        } else {
            $valijaDisponible = EnvioSaca::where('saca_id', $saca->saca_id)
                ->where('activo', true)
                ->whereIn('envio_id', function ($q) use ($oficinaId) {
                    $q->select('envio_id')
                        ->from('envios_almacen')
                        ->where('oficina_id', $oficinaId)
                        ->where('estatus', true);
                })
                ->exists();
        }

        if (!$valijaDisponible) {
            $this->dispatch('alertError', message: 'Esta valija no está disponible en esta oficina. Sus envíos ya fueron procesados previamente o no se encuentran en este almacén.');
            return;
        }

        if (!$saca->numero_despacho_id) {
            $this->dispatch('alertError', message: "La valija {$saca->codigo_saca} no pertenece a ningún despacho. Solo se puede dar salida por despacho.");
            return;
        }

        // El despacho debe estar entre los disponibles (puede haberse agregado ya al carrito).
        $enLista = collect($this->despachosDisponibles)
            ->firstWhere('despacho_id', $saca->numero_despacho_id);

        if (!$enLista) {
            $this->dispatch('alertError', message: "El despacho de la valija {$saca->codigo_saca} no está disponible: puede que ya lo hayas agregado a tu despacho.");
            return;
        }

        $this->filtroDespachoId = $saca->numero_despacho_id;
        $this->filtroEnvioId = null;
        $this->filtroEtiqueta = "valija {$saca->codigo_saca}";
        $this->filtroTab = 'despachos';
        $this->pageDespachos = 1;

        $this->dispatch('filtro-aplicado', tab: 'despachos');
    }

    /**
     * Deja la tarjeta de envios al descubierto mostrando solo este envio, que es
     * donde se le asigna (o se le quita) un despacho.
     */
    protected function localizarEnvio(Envio $envio, int $oficinaId): void
    {
        $usaAreasInternas = ($oficinaId === self::OFICINA_CON_AREAS_INTERNAS);
        $usuario = auth()->user();

        // La disponibilidad se comprueba contra la MISMA fuente de la que se cargan
        // los envios: en CPC, Expedicion + Distribucion; en el resto, almacen general.
        if ($usaAreasInternas) {
            $disponible = EnvioExpedicion::where('envio_id', $envio->envio_id)
                ->where('oficina_id', $oficinaId)
                ->where('estatus', true)
                ->when($usuario->hasRole('Clasificador EMS'), function ($q) {
                    $q->whereHas('envio', fn($sub) => $sub->where('servicio_id', self::SERVICIO_EMS));
                })
                ->when($usuario->hasRole('Clasificador Bultos'), function ($q) {
                    $q->whereHas('envio', fn($sub) => $sub->where('servicio_id', self::SERVICIO_BULTO));
                })
                ->exists();

            if (!$disponible) {
                $disponible = EnvioDistribucionPaqueteMuestra::where('envio_id', $envio->envio_id)
                    ->where('oficina_id', $oficinaId)
                    ->where('estatus', true)
                    ->exists();
            }
        } else {
            $disponible = EnvioAlmacen::where('envio_id', $envio->envio_id)
                ->where('oficina_id', $oficinaId)
                ->where('estatus', true)
                ->exists();
        }

        if (!$disponible) {
            $this->dispatch('alertError', message: "El envío {$envio->codigo_envio} no se encuentra disponible en esta oficina.");
            return;
        }

        // Si va dentro de una valija no es un envio al descubierto: se despacha con ella.
        $sacaActiva = EnvioSaca::where('envio_id', $envio->envio_id)
            ->where('activo', true)
            ->value('saca_id');

        if ($sacaActiva) {
            $codigoSaca = Saca::where('saca_id', $sacaActiva)->value('codigo_saca');
            $this->dispatch('alertError', message: "El envío {$envio->codigo_envio} va dentro de la valija {$codigoSaca}. Busque esa valija para ubicar su despacho.");
            return;
        }

        $enLista = collect($this->enviosSueltos)
            ->firstWhere('envio_id', $envio->envio_id);

        if (!$enLista) {
            $this->dispatch('alertError', message: "El envío {$envio->codigo_envio} no está disponible: puede que ya lo hayas agregado a tu despacho.");
            return;
        }

        $this->filtroEnvioId = $envio->envio_id;
        $this->filtroDespachoId = null;
        $this->filtroEtiqueta = "envío {$envio->codigo_envio}";
        $this->filtroTab = 'sueltos';
        $this->pageSueltos = 1;

        $this->dispatch('filtro-aplicado', tab: 'sueltos');
    }

    public function quitarDeCarrito($index)
    {
        unset($this->enviosConYSinSaca[$index]);
        $this->enviosConYSinSaca = array_values($this->enviosConYSinSaca); // reindexar

        // Recargar los disponibles y filtrar para que el quitado vuelva a aparecer abajo
        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();
    }

    /**
     * Elimina una valija que NO va a despacharse, para poder dar salida al resto
     * del despacho.
     *
     * Caso de uso: los envios de una valija se quedan en espera y no se llega a
     * llenar, pero otra valija del mismo despacho ya esta lista. Sin esta opcion
     * habria que despachar el despacho completo o no despachar nada, porque las
     * valijas de un despacho entran y salen del carrito en bloque.
     *
     * La valija se borra; sus envios se liberan y vuelven al almacen para
     * cargarse manaña en una valija nueva (que tendra otro numero de despacho).
     *
     * Solo se permite si la valija NUNCA ha salido. Se comprueban las dos señales
     * porque no coinciden: las valijas historicas figuran en manifiestos pero no
     * tienen encaminamiento de salida (sacas_encaminamiento es posterior).
     */
    public function eliminarValijaDelDespacho($sacaId)
    {
        $usuario = auth()->user();
        $this->valijaAEliminar = null; // cierra el aviso pase lo que pase

        $saca = Saca::where('saca_id', $sacaId)->first();

        if (!$saca) {
            $this->dispatch('alertError', message: 'La valija no existe.');
            return;
        }

        if ((int) $saca->oficina_id !== (int) $usuario->oficina_id) {
            $this->dispatch('alertError', message: 'Solo puede eliminar valijas creadas en su oficina.');
            return;
        }

        if ($this->valijaYaSalio($sacaId)) {
            $this->dispatch('alertError', message: "La valija {$saca->codigo_saca} ya fue despachada: no puede eliminarse.");
            return;
        }

        $codigo = $saca->codigo_saca;

        DB::transaction(function () use ($sacaId, $usuario, $codigo) {
            // Los envios NO se borran: solo se rompe su vinculo con la valija, asi
            // que vuelven a estar disponibles en el almacen (mismo criterio que
            // DetallesSacas::eliminarEnvio).
            $liberados = EnvioSaca::where('saca_id', $sacaId)->count();
            EnvioSaca::where('saca_id', $sacaId)->delete();

            // Las FK hacia sacas son NO ACTION: hay que borrar primero lo que
            // apunta a la valija.
            SacaEncaminamiento::where('saca_id', $sacaId)->delete();
            Saca::where('saca_id', $sacaId)->delete();

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'delete',
                'descripcion' => "Se eliminó la valija {$codigo} ({$sacaId}) antes de la salida; se liberaron {$liberados} envío(s)",
            ]);
        });

        // La valija desaparece del carrito y de las listas de disponibles.
        $this->enviosConYSinSaca = collect($this->enviosConYSinSaca)
            ->reject(fn($item) => ($item['tipo'] ?? null) === 'saca'
                && ($item['saca_id'] ?? null) == $sacaId)
            ->values()
            ->toArray();

        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();

        $this->dispatch('alertSuccess', message: "Valija {$codigo} eliminada. Sus envíos volvieron al almacén.");
    }

    /**
     * Una valija ya salio si figura en algun manifiesto o si tiene un movimiento
     * de encaminamiento distinto de CREADA. Se comprueban las dos: las valijas
     * anteriores a sacas_encaminamiento solo dejan rastro en el manifiesto.
     */
    protected function valijaYaSalio($sacaId): bool
    {
        $enManifiesto = ManifiestoPaquete::where('saca_id', $sacaId)->exists();

        $conMovimiento = SacaEncaminamiento::where('saca_id', $sacaId)
            ->where('saca_estatus_id', '!=', Saca::ESTATUS_CREADA)
            ->exists();

        return $enManifiesto || $conMovimiento;
    }

    /**
     * Agrega al carrito TODAS las valijas de un despacho de un solo clic.
     * El despacho sale completo, no valija por valija.
     */
    public function agregarDespacho($despachoId)
    {
        $despacho = collect($this->despachosDisponibles)
            ->firstWhere('despacho_id', $despachoId);

        if (!$despacho) {
            return;
        }

        $sacas = Saca::with(['tipoSaca', 'numeroDespacho'])
            ->whereIn('saca_id', $despacho['saca_ids'])
            ->get();

        foreach ($sacas as $saca) {
            $yaExiste = collect($this->enviosConYSinSaca)->contains(fn($e) =>
                $e['tipo'] === 'saca' && ($e['saca_id'] ?? null) == $saca->saca_id);

            if (!$yaExiste) {
                $this->enviosConYSinSaca[] = $this->itemCarritoSaca($saca);
            }
        }

        // Incluir los envios al descubierto ya asociados a este despacho: viajan con el.
        $sueltosAsociados = EnvioDescubiertoDespacho::with('envio.Servicio')
            ->where('numero_despacho_id', $despachoId)
            ->get();

        foreach ($sueltosAsociados as $asociacion) {
            $envio = $asociacion->envio;
            if (!$envio) {
                continue;
            }

            $yaExiste = collect($this->enviosConYSinSaca)->contains(fn($e) =>
                $e['tipo'] === 'envio' && ($e['envio_id'] ?? null) == $envio->envio_id);

            if (!$yaExiste) {
                $this->enviosConYSinSaca[] = $this->itemCarritoEnvio(
                    $envio,
                    $despachoId,
                    $despacho['despacho_num'] ?? null
                );
            }
        }

        // hydrate() ya cargó las listas en esta petición; solo hay que re-filtrarlas
        // contra el carrito que esta acción acaba de modificar.
        $this->filtrarAlmacenDisponibles();
    }

    /**
     * Asocia un envio al descubierto (suelto, sin valija) a un despacho: crea la fila
     * en envio_descubierto_despacho DE INMEDIATO. A partir de ahi el envio pertenece
     * al despacho (aparece en su guia al instante y viaja con el como una valija mas).
     * NO lo mete al carrito: entra al carrito junto con el despacho al pulsar "Agregar".
     *
     * Sirve tambien para REASIGNAR: si ya estaba en otro despacho abierto, hay que
     * quitarlo primero (regla "un solo despacho abierto a la vez").
     */
    public function asociarEnvioADespacho($envioId, $despachoId)
    {
        $despacho = collect($this->despachosDisponibles)
            ->firstWhere('despacho_id', $despachoId);

        if (!$despacho) {
            $this->dispatch('alertError', message: 'El despacho seleccionado no está disponible.');
            return;
        }

        $envio = Envio::where('envio_id', $envioId)->first();

        if (!$envio) {
            $this->dispatch('alertError', message: 'El envío no existe.');
            return;
        }

        // Un despacho CONFIRMADO (activo=false) ya no admite contenido nuevo. La lista
        // de despachos disponibles se arma desde las valijas presentes en la oficina y
        // no mira `activo`, asi que sin este guard un despacho ya confirmado seguia
        // aceptando sueltos, y ademas quedaban atrapados: desasociarEnvioDeDespacho()
        // solo actua sobre despachos abiertos.
        $despachoActivo = NumeroDespachoOficina::where('numero_despacho_id', $despachoId)
            ->value('activo');

        if (!$despachoActivo) {
            $this->dispatch('alertError', message: "El despacho N° {$despacho['despacho_num']} ya fue confirmado: no admite más envíos al descubierto.");
            return;
        }

        // Regla "uno a la vez": el envio no puede estar ya asociado a OTRO despacho
        // mientras siga presente en esta oficina.
        //
        // La condicion NO es "el despacho esta abierto" (un despacho con la guia
        // confirmada sigue cerrado y su suelto sigue aqui) ni "figura en algun
        // manifiesto": manifiestos_paquetes es historico acumulativo, asi que un
        // envio que llego desde otra oficina ya tiene manifiesto de aquel tramo.
        //
        // Lo que decide es si el envio sigue DISPONIBLE aqui: si lo esta, su
        // asociacion es vigente; si no, ya viajo y una oficina posterior puede
        // reasignarlo al reexpedirlo.
        $otraAsociacion = EnvioDescubiertoDespacho::where('envio_id', $envioId)
            ->where('numero_despacho_id', '!=', $despachoId)
            ->exists();

        if ($otraAsociacion && $this->envioDisponibleEnOficina($envioId)) {
            $this->dispatch('alertError', message: 'El envío ya está asociado a otro despacho. Quítelo de ese despacho antes de reasignarlo.');
            return;
        }

        // Persistir la asociacion de inmediato. firstOrCreate respeta el unique y evita
        // duplicar si ya estaba asociado a este mismo despacho.
        EnvioDescubiertoDespacho::firstOrCreate([
            'envio_id'           => $envioId,
            'numero_despacho_id' => $despachoId,
        ]);

        // hydrate() ya cargó las listas en esta petición; solo hay que re-filtrarlas
        // contra el carrito que esta acción acaba de modificar.
        $this->filtrarAlmacenDisponibles();

        $this->dispatch('alertSuccess', message: "Envío asociado al despacho N° {$despacho['despacho_num']}.");
    }

    /**
     * Quita un envio al descubierto de su despacho: borra la fila de la pivote. Solo
     * permitido mientras el despacho siga ABIERTO (aun sin salida); una vez cerrado la
     * asociacion es historica e inmutable. El envio vuelve a estar disponible como suelto.
     */
    public function desasociarEnvioDeDespacho($envioId)
    {
        // Se puede quitar mientras el envio siga DISPONIBLE en esta oficina, este su
        // despacho abierto o con la guia ya confirmada. Si ya salio, la asociacion es
        // historica e inmutable: forma parte del manifiesto de esa salida.
        $asociacion = EnvioDescubiertoDespacho::where('envio_id', $envioId)->first();

        if (!$asociacion) {
            $this->dispatch('alertError', message: 'El envío no está asociado a ningún despacho.');
            return;
        }

        if (!$this->envioDisponibleEnOficina($envioId)) {
            $this->dispatch('alertError', message: 'El envío ya fue despachado: no puede quitarse del despacho.');
            return;
        }

        // Si el despacho estaba en el carrito, tambien sacar el suelto del carrito.
        $this->enviosConYSinSaca = collect($this->enviosConYSinSaca)
            ->reject(fn($item) => $item['tipo'] === 'envio'
                && ($item['envio_id'] ?? null) == $envioId)
            ->values()
            ->toArray();

        $asociacion->delete();

        // Al quitar del carrito hay que recargar: filtrar es destructivo y no puede
        // devolver a la lista lo que acaba de liberarse.
        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();

        $this->dispatch('alertSuccess', message: 'Envío quitado del despacho.');
    }

    /**
     * Quita del carrito TODO un despacho de un solo clic: sus valijas y tambien los
     * envios sueltos que se le hayan asociado. El suelto vuelve a estar disponible.
     */
    public function quitarDespacho($despachoNum)
    {
        $this->enviosConYSinSaca = collect($this->enviosConYSinSaca)
            ->reject(function ($item) use ($despachoNum) {
                return ($item['despacho_num'] ?? null) == $despachoNum;
            })
            ->values()
            ->toArray();

        // Al quitar del carrito hay que recargar: filtrar es destructivo y no puede
        // devolver a la lista lo que acaba de liberarse.
        $this->cargarDespachosDisponibles();
        $this->filtrarAlmacenDisponibles();
    }

    public function create()
    {
        try {
            $usuario = auth()->user();

            // Validaciones previas (fuera de la transacción)

            // El destino ya no se elige con un selector aparte: la salida se hace por
            // viaje (hacia la oficina relacionada) o por transferencia directa, igual que OPT.
            $oficinaRelacionadaId = Oficina::where('oficina_id', $usuario->oficina_id)
                ->value('oficina_relacionada_id');

            $oficinaDestino = $this->selectedViajeId
                ? $oficinaRelacionadaId
                : $this->transferenciaOficina;

            $oficinaDestinoinfo = Oficina::find($oficinaDestino);

            // Verificar si hay envíos
            if (empty($this->enviosConYSinSaca)) {
                $this->dispatch('alertError', message: 'Debe agregar al menos un despacho o envío antes de registrar la salida.');
                return;
            }

            // Verificar que se haya seleccionado un viaje o una oficina de transferencia
            if (!$this->selectedViajeId && !$this->transferenciaOficina) {
                $this->dispatch('alertError', message: 'Debe seleccionar un viaje o una oficina de transferencia directa.');
                return;
            }

            DB::beginTransaction();

            // Extraer todos los envio_ids (de valijas y sueltos). La asociacion de los
            // sueltos con su despacho ya esta persistida (se crea al asociar, no aqui).
            $envioIds = [];
            $sacaIdsSeleccionadas = []; // Sacas despachadas directamente (incluye las vacías).
            foreach ($this->enviosConYSinSaca as $item) {
                if ($item['tipo'] === 'saca') {
                    $sacaId = $item['saca_id'] ?? null;
                    if ($sacaId) {
                        $sacaIdsSeleccionadas[] = $sacaId;
                    }
                    $enviosEnSaca = EnvioSaca::where('saca_id', $sacaId)
                        ->pluck('envio_id')->toArray();
                    $envioIds = array_merge($envioIds, $enviosEnSaca);
                } elseif ($item['tipo'] === 'envio') {
                    $envioIds[] = $item['envio_id'] ?? null;
                }
            }
            $envioIds = array_unique(array_filter($envioIds));
            $sacaIdsSeleccionadas = array_unique(array_filter($sacaIdsSeleccionadas));

            // Antes de despachar: revisar que cada despacho involucrado no deje valijas
            // pendientes (no seleccionadas y aun abiertas) en el origen. Una valija
            // pendiente CON envios aborta la salida (el operador debe cerrarla); una
            // pendiente SIN envios (valija de peso vacia) se elimina para no dejarla
            // huerfana en un despacho que se va a cerrar.
            if (!$this->resolverValijasPendientes($sacaIdsSeleccionadas, $usuario->oficina_id)) {
                DB::rollBack();
                return; // resolverValijasPendientes ya emitio el aviso
            }

            // Guard de idempotencia: revalidar contra la BD que los envíos sigan disponibles,
            // consultando LA MISMA fuente de la que se cargaron (en CPC son Expedición y
            // Distribución; en el resto, el almacén general). Se bloquean las filas para que
            // una segunda petición concurrente (doble clic, otra pestaña) espere y las
            // encuentre ya cerradas, en vez de duplicar la salida.
            if ($usuario->oficina_id === self::OFICINA_CON_AREAS_INTERNAS) {
                $disponibles = EnvioExpedicion::whereIn('envio_id', $envioIds)
                    ->where('oficina_id', $usuario->oficina_id)
                    ->where('estatus', true)
                    ->lockForUpdate()
                    ->pluck('envio_id')
                    ->merge(
                        EnvioDistribucionPaqueteMuestra::whereIn('envio_id', $envioIds)
                            ->where('oficina_id', $usuario->oficina_id)
                            ->where('estatus', true)
                            ->lockForUpdate()
                            ->pluck('envio_id')
                    );
            } else {
                $disponibles = EnvioAlmacen::whereIn('envio_id', $envioIds)
                    ->where('oficina_id', $usuario->oficina_id)
                    ->where('estatus', true)
                    ->lockForUpdate()
                    ->pluck('envio_id');
            }

            $envioIds = $disponibles->unique()->values()->toArray();

            // Las valijas de carga por peso no tienen envios, por eso el guard solo
            // aborta si NO queda ningun envio NI ninguna valija seleccionada (mismo
            // criterio que RegistrarSalidaOPT).
            if (empty($envioIds) && empty($sacaIdsSeleccionadas)) {
                DB::rollBack();
                $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles: es posible que la salida ya se haya registrado.');
                return;
            }

            // Buscar o crear manifiesto
            $manifiesto = Manifiesto::where('oficina_id', $usuario->oficina_id)
                ->where('oficina_destino_id', $oficinaDestino)
                ->where('status', false)
                ->whereDate('created_at', now()->toDateString())
                ->first();

            if (!$manifiesto) {
                $manifiesto = Manifiesto::create([
                    'oficina_id'         => $usuario->oficina_id,
                    'oficina_destino_id' => $oficinaDestino,
                    'status'             => false,
                ]);
            }

            // Agrupar envíos por saca_id y registrar encaminamiento
            $enviosAgrupados = [];
            foreach ($envioIds as $envioId) {
                // Determinar el estatus_id basado en el tipo_oficina_id
                $estatusId = ($oficinaDestinoinfo->tipo_oficina_id == 4) ? 13 : 14;

                // Registrar encaminamiento según viaje o transferencia directa sin restringir duplicados históricos
                EnvioEncaminamiento::create([
                    'envio_id'           => $envioId,
                    'oficina_id'         => $usuario->oficina_id,
                    'oficina_externa_id' => $oficinaDestino,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => $this->selectedViajeId ?? null,
                    'estatus_id'         => $estatusId,
                    'devolucion'         => false
                ]);

                // Agrupar para manifiesto
                $sacaId = EnvioSaca::where('envio_id', $envioId)
                    ->where('activo', true)
                    ->value('saca_id');

                if (!isset($enviosAgrupados[$sacaId])) {
                    $enviosAgrupados[$sacaId] = [];
                }
                $enviosAgrupados[$sacaId][] = $envioId;
            }

            // Crear registros en ManifiestoPaquete
            foreach ($enviosAgrupados as $sacaId => $envios) {
                if ($sacaId) {
                    $saca = Saca::where('saca_id', $sacaId)->first();
                    $peso = $saca ? $saca->peso : 0;
                    // El servicio se toma de la pivote tipo_saca_servicio (primer servicio).
                    // Fallback a la columna vieja servicio_id y luego a 1.
                    $servicioId = $saca?->tipoSaca?->servicios->first()?->servicio_id
                        ?? $saca?->tipoSaca?->servicio_id
                        ?? 1;

                    ManifiestoPaquete::create([
                        'manifiesto_id' => $manifiesto->manifiesto_id,
                        'saca_id'       => $sacaId,
                        'envio_id'      => null,
                        'peso'          => $peso,
                        'servicio_id'   => $servicioId,
                    ]);
                } else {
                    foreach ($envios as $envioId) {
                        $envio = Envio::where('envio_id', $envioId)->first();
                        $peso = $envio ? $envio->peso : 0;
                        $servicioId = $envio ? $envio->servicio_id : 1;

                        ManifiestoPaquete::create([
                            'manifiesto_id' => $manifiesto->manifiesto_id,
                            'saca_id'       => null,
                            'envio_id'      => $envioId,
                            'peso'          => $peso,
                            'servicio_id'   => $servicioId,
                        ]);
                    }
                }
            }

            // Valijas sin envíos: no entran en $enviosAgrupados, pero igual deben
            // figurar en el manifiesto de despacho.
            $sacasConEnvios = array_filter(array_keys($enviosAgrupados));
            $sacasVacias = array_diff($sacaIdsSeleccionadas, $sacasConEnvios);
            foreach ($sacasVacias as $sacaId) {
                $saca = Saca::where('saca_id', $sacaId)->first();
                $peso = $saca ? $saca->peso : 0;
                // El servicio se toma de la pivote tipo_saca_servicio (primer servicio).
                // Fallback a la columna vieja servicio_id y luego a 1.
                $servicioId = $saca?->tipoSaca?->servicios->first()?->servicio_id
                    ?? $saca?->tipoSaca?->servicio_id
                    ?? 1;

                ManifiestoPaquete::create([
                    'manifiesto_id' => $manifiesto->manifiesto_id,
                    'saca_id'       => $sacaId,
                    'envio_id'      => null,
                    'peso'          => $peso,
                    'servicio_id'   => $servicioId,
                ]);
            }

            // Registrar encaminamiento de la valija (En Tránsito) por cada saca
            // despachada. Funciona también para valijas sin envíos.
            foreach ($sacaIdsSeleccionadas as $sacaId) {
                SacaEncaminamiento::create([
                    'saca_id'            => $sacaId,
                    'oficina_id'         => $usuario->oficina_id,   // donde se marca el estatus (oficina que despacha)
                    'oficina_externa_id' => $oficinaDestino,        // hacia dónde va (no se muestra en la vista)
                    'saca_estatus_id'    => Saca::ESTATUS_TRANSITO,
                    'usuario_id'         => $usuario->id,
                ]);
            }

            // Cerrar el despacho SOLO cuando la salida es desde el ORIGEN (la oficina
            // que creo la valija). Asi el correlativo del par (origen, destino) avanza
            // una sola vez por despacho. Una COP intermedia que reenvia NO cierra nada:
            // el numero de despacho viaja intacto con la valija hasta su destino final.
            $despachosACerrar = Saca::whereIn('saca_id', $sacaIdsSeleccionadas)
                ->where('oficina_id', $usuario->oficina_id)   // salida desde origen
                ->whereNotNull('numero_despacho_id')
                ->pluck('numero_despacho_id')
                ->unique();

            if ($despachosACerrar->isNotEmpty()) {
                NumeroDespachoOficina::whereIn('numero_despacho_id', $despachosACerrar)
                    ->where('activo', true)
                    ->update(['activo' => false]);
            }

            // Actualizar estatus en EnvioAlmacen (no-op para envíos que vinieron de Expedición — ya están cerrados).
            EnvioAlmacen::whereIn('envio_id', $envioIds)
                ->where('oficina_id', $usuario->oficina_id)
                ->update(['estatus' => false, 'Salida' => now()->toDateTimeString()]);

            // En CPC, además cerrar las filas activas en envios_expedicion y envios_distribucion_paquetes_muestras.
            // Cada update es no-op para los envíos que no estén en esa tabla, así que es seguro ejecutar ambos.
            if ($usuario->oficina_id === self::OFICINA_CON_AREAS_INTERNAS) {
                EnvioExpedicion::whereIn('envio_id', $envioIds)
                    ->where('oficina_id', $usuario->oficina_id)
                    ->where('estatus', true)
                    ->update([
                        'estatus'           => false,
                        'Salida'            => now()->toDateString(),
                        'usuario_salida_id' => $usuario->id,
                    ]);

                EnvioDistribucionPaqueteMuestra::whereIn('envio_id', $envioIds)
                    ->where('oficina_id', $usuario->oficina_id)
                    ->where('estatus', true)
                    ->update([
                        'estatus'           => false,
                        'Salida'            => now()->toDateString(),
                        'usuario_salida_id' => $usuario->id,
                    ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró salida desde COP de " . count($envioIds) . " envío(s)",
            ]);

            DB::commit();

            // Mensaje de éxito
            $this->dispatch('alertSuccess', message: 'Salida Registrada exitosamente!');
            $this->dispatch('tarifaUpdated');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[CREATE ERROR] ' . $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine());
            $this->dispatch('alertError', message: 'Ocurrió un error al registrar la salida. Intente nuevamente o contacte al administrador.');
        }
    }

    /**
     * Revisa, para los despachos de las sacas seleccionadas, si quedan valijas
     * PENDIENTES en el origen (mismo numero_despacho_id, no seleccionadas y aun
     * abiertas). Debe ejecutarse dentro de la transaccion de salida.
     *
     * - Pendiente CON envios: aborta la salida (devuelve false) con un aviso, porque
     *   el despacho no esta completo y la valija debe cerrarse antes.
     * - Pendiente SIN envios (valija de peso vacia): se elimina (su encaminamiento y
     *   la saca), para no dejarla huerfana apuntando a un despacho que se va a cerrar.
     *
     * @return bool true si se puede continuar con la salida; false si debe abortarse.
     */
    protected function resolverValijasPendientes(array $sacaIdsSeleccionadas, int $oficinaOrigenId): bool
    {
        // Despachos del lado origen tocados por esta salida.
        $despachoIds = Saca::whereIn('saca_id', $sacaIdsSeleccionadas)
            ->where('oficina_id', $oficinaOrigenId)
            ->whereNotNull('numero_despacho_id')
            ->pluck('numero_despacho_id')
            ->unique();

        if ($despachoIds->isEmpty()) {
            return true;
        }

        // Valijas del mismo despacho, en el origen, NO seleccionadas y aun abiertas.
        $pendientes = Saca::whereIn('numero_despacho_id', $despachoIds)
            ->where('oficina_id', $oficinaOrigenId)
            ->whereNotIn('saca_id', $sacaIdsSeleccionadas)
            ->where('cerrado', false)
            ->get();

        if ($pendientes->isEmpty()) {
            return true;
        }

        // ¿Alguna pendiente tiene envios? -> no se puede cerrar el despacho.
        $pendientesConEnvios = EnvioSaca::whereIn('saca_id', $pendientes->pluck('saca_id'))
            ->pluck('saca_id')
            ->unique();

        if ($pendientesConEnvios->isNotEmpty()) {
            $numeros = NumeroDespachoOficina::whereIn('numero_despacho_id', $despachoIds)
                ->pluck('numero_despacho')
                ->implode(', ');
            $this->dispatch('alertError', message: "El despacho N°{$numeros} tiene valijas sin despachar. Ciérrelas o elimínelas antes de dar salida.");
            return false;
        }

        // Todas las pendientes estan vacias (valija de peso sin envios): eliminarlas.
        // Se borra primero su encaminamiento (FK ON DELETE NO ACTION) y luego la saca.
        $idsVacias = $pendientes->pluck('saca_id');
        SacaEncaminamiento::whereIn('saca_id', $idsVacias)->delete();
        Saca::whereIn('saca_id', $idsVacias)->delete();

        return true;
    }

    /**
     * Prepara el carrito ya agrupado por despacho y con los campos de presentacion
     * resueltos, para que la vista solo itere (sin logica de negocio en el Blade).
     * Cada item conserva su indice original ('idx') para quitarDeCarrito().
     * Los items sin despacho quedan bajo la clave '__sin__'.
     */
    protected function carritoAgrupadoPorDespacho()
    {
        return collect($this->enviosConYSinSaca)
            ->map(function ($item, $idx) {
                $tipo = $item['tipo'] ?? null;

                return [
                    'idx'          => $idx,
                    'despacho_num' => $item['despacho_num'] ?? null,
                    'codigo'       => $item['codigo'] ?? '-',
                    'etiqueta'     => $tipo === 'saca' ? 'Valija' : ($tipo === 'envio' ? 'Envío' : '-'),
                    'servicio'     => $item['servicio'] ?? '-',
                    'peso'         => $item['peso'] ?? '-',
                    // Necesario para el boton "Eliminar" de las valijas del carrito.
                    'saca_id'      => $item['saca_id'] ?? null,
                ];
            })
            ->groupBy(fn($fila) => $fila['despacho_num'] ?? '__sin__');
    }

    public function render()
    {
        // Obtener el inicio y fin de la semana
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        // Obtener la oficina del usuario autenticado
        $oficinaId = auth()->user()->oficina_id;

        // Obtener los viajes de esta semana. Se cargan por adelantado las relaciones que
        // pinta la tarjeta de cada viaje (origen, destino y día); sin esto la vista
        // dispara una consulta por viaje y por relación (N+1) en cada render.
        $viajes = Viaje::with(['ruta.oficinaOrigen', 'ruta.oficinaDestino', 'diaSemana'])
            ->whereBetween('fecha_salida', [$inicioSemana, $finSemana])
            ->get();

        // Extraer los ruta_ids de los viajes
        $rutaIds = $viajes->pluck('ruta_id')->toArray();

        // Obtener los puntos de entrega relacionados con la oficina del usuario
        $puntosEntregaPorRuta = RutaPuntoEntrega::with('oficina')
            ->whereIn('ruta_id', $rutaIds)
            ->get()
            ->groupBy('ruta_id');

        // Filtrar los viajes que cumplan con alguna de las condiciones
        $viajesFiltrados = $viajes->map(function ($viaje) use ($puntosEntregaPorRuta, $oficinaId) {
            $ruta = $viaje->ruta;

            if ($ruta->oficina_id_origen == $oficinaId || $ruta->oficina_id_destino == $oficinaId) {
                $viaje->puntos_entrega = collect();
            }

            if ($puntosEntregaPorRuta->has($viaje->ruta_id)) {
                $viaje->puntos_entrega = $puntosEntregaPorRuta[$viaje->ruta_id];
            }

            return $viaje;
        });

        $oficinaRelacionadaId = Oficina::where('oficina_id', $oficinaId)->value('oficina_relacionada_id');
        $oficinaRelacionada = Oficina::find($oficinaRelacionadaId);

        $estadoIdUsuario = Oficina::where('oficina_id', auth()->user()->oficina_id)->value('estado_id');

        // Una COP despacha a las oficinas de todos los estados que administra: el suyo
        // más los agrupados (p. ej. la CPC de Distrito Capital cubre Miranda y La Guaira;
        // Monagas cubre Delta Amacuro).
        $estadosCubiertos = Estado::estadosCubiertos($estadoIdUsuario);

        $oficinasQuery = Oficina::where(function ($query) use ($estadosCubiertos) {
            $query->where(function ($q) use ($estadosCubiertos) {
                $q->whereIn('estado_id', $estadosCubiertos)
                    ->whereIn('tipo_oficina_id', [1, 2, 3]);
            })->orWhere(function ($q) {
                $q->where('tipo_oficina_id', 4)
                    ->where('externa', true);
            });
        });

        $oficinasTotal = (clone $oficinasQuery)->count();

        // El buscador filtra por nombre o codigo. Se envuelve en su propio grupo para no
        // romper el OR de arriba (sin el parentesis, un termino de busqueda dejaria pasar
        // todos los aliados).
        if (trim($this->busquedaOficina) !== '') {
            $termino = '%' . strtolower(trim($this->busquedaOficina)) . '%';
            $oficinasQuery->where(function ($query) use ($termino) {
                $query->whereRaw('lower(nombre) like ?', [$termino])
                    ->orWhereRaw('lower(codigo) like ?', [$termino]);
            });
        }

        $oficinas = $oficinasQuery
            ->orderByRaw("tipo_oficina_id = 4 ASC")
            ->orderBy('nombre', 'ASC')
            ->get();

        // El buscador por código filtra la tarjeta correspondiente. Si el item filtrado
        // ya no está (se agregó al carrito), la lista queda vacía en vez de mostrarlo todo:
        // el chip del filtro sigue visible para que el operador sepa por qué.
        $despachosFiltrados = $this->filtroDespachoId
            ? collect($this->despachosDisponibles)
                ->where('despacho_id', $this->filtroDespachoId)
                ->values()
                ->all()
            : $this->despachosDisponibles;

        $sueltosFiltrados = $this->filtroEnvioId
            ? collect($this->enviosSueltos)
                ->where('envio_id', $this->filtroEnvioId)
                ->values()
                ->all()
            : $this->enviosSueltos;

        // Cada tarjeta se pagina por separado, con su propia página actual.
        $despachosPag = $this->paginarTarjeta($despachosFiltrados, $this->pageDespachos);
        $sueltosPag   = $this->paginarTarjeta($sueltosFiltrados, $this->pageSueltos);

        return view('livewire.gestion-despachos.registrar-salida-c-o-p', [
            'carritoPorDespacho' => $this->carritoAgrupadoPorDespacho(),
            // Datos del aviso de eliminacion: el operador debe saber cuantos
            // envios se liberan antes de confirmar.
            'valijaAEliminarInfo' => $this->valijaAEliminar
                ? [
                    'codigo' => Saca::where('saca_id', $this->valijaAEliminar)->value('codigo_saca'),
                    'envios' => EnvioSaca::where('saca_id', $this->valijaAEliminar)->count(),
                ]
                : null,
            'despachosPagina' => $despachosPag['items'],
            'despachosPaginaActual' => $despachosPag['pagina'],
            'despachosTotalPaginas' => $despachosPag['totalPaginas'],
            'despachosNumeros' => $despachosPag['numeros'],
            'sueltosPagina' => $sueltosPag['items'],
            'sueltosPaginaActual' => $sueltosPag['pagina'],
            'sueltosTotalPaginas' => $sueltosPag['totalPaginas'],
            'sueltosNumeros' => $sueltosPag['numeros'],
            'viajesConPuntosEntrega' => $viajesFiltrados,
            'oficinaRelacionada' => $oficinaRelacionada,
            'oficinasEstados' => $oficinas,
            'oficinasTotal' => $oficinasTotal,
            // La oficina elegida puede quedar fuera del filtro actual. Se pasa aparte para
            // que la vista siga mostrando cual es el destino seleccionado.
            'oficinaTransferenciaSel' => $this->transferenciaOficina
                ? Oficina::find($this->transferenciaOficina)
                : null,
        ]);
    }

    /**
     * Pagina en memoria un array (despachos o envíos sueltos) para su tarjeta.
     * Devuelve el slice de la página y los metadatos de paginación. La página se
     * acota al rango válido por si los datos se redujeron tras agregar al carrito.
     */
    protected function paginarTarjeta(array $datos, int $paginaActual): array
    {
        $coleccion = collect($datos);
        $totalPaginas = max(1, (int) ceil($coleccion->count() / self::POR_PAGINA_TARJETA));
        $pagina = min(max(1, $paginaActual), $totalPaginas);

        return [
            'items'        => $coleccion->forPage($pagina, self::POR_PAGINA_TARJETA)->values()->all(),
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'numeros'      => $this->numerosPaginacion($pagina, $totalPaginas),
        ];
    }

    /**
     * Números de página a mostrar: siempre la primera y la última, más una ventana
     * alrededor de la actual. Los saltos se marcan con '...' para que la vista solo
     * itere. Con pocas páginas se devuelven todas.
     */
    protected function numerosPaginacion(int $actual, int $total): array
    {
        if ($total <= 7) {
            return range(1, $total);
        }

        $ventana = collect([1, $total, $actual, $actual - 1, $actual + 1])
            ->filter(fn($n) => $n >= 1 && $n <= $total)
            ->unique()
            ->sort()
            ->values();

        $numeros = [];
        $previo = 0;

        foreach ($ventana as $n) {
            if ($previo && $n - $previo > 1) {
                $numeros[] = '...';
            }
            $numeros[] = $n;
            $previo = $n;
        }

        return $numeros;
    }
}
