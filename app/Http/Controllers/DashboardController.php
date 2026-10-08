<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\Envio;
use App\Models\Oficina;
use App\Models\OficinaSemaforoPostal;
use App\Models\Servicio;
use App\Models\Estado;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\FacturacionDestinatario;
use App\Models\RegistroEntrega;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $oficinaId;

    public function __construct(Request $request)
    {
        $this->oficinaId = $request->get('oficina_id');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $accessStates = null;
        if (!$user->hasRole(['Presidente', 'SuperAdmin', 'CNC'])) {
            $estadoUsuario = $user->oficina->estado_id ?? null;
            $accessStates = [$estadoUsuario];
            if ($estadoUsuario == 1) { 
                $accessStates = array_merge($accessStates, [2, 24]); 
            } elseif ($estadoUsuario == 20) { 
                $accessStates = array_merge($accessStates, [21]); 
            }
            
            $estados = Estado::whereIn('estado_id', $accessStates)->get();
            $oficinas = Oficina::where('externa', false)
                ->whereIn('tipo_oficina_id', [1, 2, 3])
                ->whereIn('estado_id', $accessStates)
                ->get();
            
            $estadoId = $request->get('estado_id');
            if ($estadoId && !in_array($estadoId, $accessStates)) {
                $estadoId = null; // Reset to all allowed states if selection is invalid
            }
            $oficinaId = $request->get('oficina_id');
            if ($oficinaId && !$oficinas->contains('oficina_id', $oficinaId)) {
                $oficinaId = null;
            }
        } else {
            $estados = Estado::all();
            $oficinas = Oficina::where('externa', false)->whereIn('tipo_oficina_id', [1, 2, 3])->get();
            $estadoId = $request->get('estado_id');
            $oficinaId = $request->get('oficina_id');
        }
        $fechaInicioStr = $request->get('fecha_inicio');
        $fechaFinStr = $request->get('fecha_fin');

        $fecha = now();
        
        if ($fechaInicioStr && $fechaFinStr) {
            $start = \Carbon\Carbon::parse($fechaInicioStr)->startOfDay();
            $end = \Carbon\Carbon::parse($fechaFinStr)->endOfDay();
            if ($start->greaterThan($end)) {
                $temp = $start;
                $start = $end->copy()->startOfDay();
                $end = $temp->copy()->endOfDay();
            }
        } else {
            $start = $fecha->copy()->startOfMonth();
            $end = $fecha->copy()->endOfMonth();
        }

        $query = Envio::whereBetween('created_at', [$start, $end]);
        $queryEntregados = RegistroEntrega::whereBetween('created_at', [$start, $end]);
        $queryIngresosEntregados = FacturacionDestinatario::whereBetween('facturacion_destinatario.created_at', [$start, $end]);

        if ($oficinaId) {
            $query->where('oficina_id', $oficinaId);
            $queryEntregados->where('oficina_id', $oficinaId);
            // Los ingresos se atribuyen a la oficina donde se realizó la entrega
            $queryIngresosEntregados->whereHas('registro', function ($q) use ($oficinaId) {
                $q->where('oficina_id', $oficinaId);
            });
        } elseif ($estadoId) {
            // Obtener todas las oficinas de ese estado
            $oficinasDelEstado = Oficina::where('externa', false)->where('estado_id', $estadoId)->pluck('oficina_id');
            $query->whereIn('oficina_id', $oficinasDelEstado);
            $queryEntregados->whereIn('oficina_id', $oficinasDelEstado);
            $queryIngresosEntregados->whereHas('registro', function ($q) use ($oficinasDelEstado) {
                $q->whereIn('oficina_id', $oficinasDelEstado);
            });
        } elseif ($accessStates) {
            // Restringir a los estados permitidos para el gerente (si no es admin)
            $oficinasPermitidas = Oficina::where('externa', false)->whereIn('estado_id', $accessStates)->pluck('oficina_id');
            $query->whereIn('oficina_id', $oficinasPermitidas);
            $queryEntregados->whereIn('oficina_id', $oficinasPermitidas);
            $queryIngresosEntregados->whereHas('registro', function ($q) use ($oficinasPermitidas) {
                $q->whereIn('oficina_id', $oficinasPermitidas);
            });
        }

        $dataFeed[0] = (clone $query)->count();
        $dataFeed[1] = (clone $query)->where('tipo_envio', "nacional")->count();
        $dataFeed[2] = (clone $query)->where('tipo_envio', "internacional")->count();
        $dataFeed[3] = (clone $query)->sum('coste');
        $dataFeed[4] = (clone $query)->where('tipo_envio', "nacional")->sum('coste');
        $dataFeed[5] = (clone $query)->where('tipo_envio', "internacional")->sum('coste');
        $dataFeed[6] = Oficina::where('externa', false)->count();
        $dataFeed[7] = Oficina::where('externa', false)->where('estatus_id', 1)->count();
        $dataFeed[8] = Oficina::where('externa', false)->where('estatus_id', 2)->count();
        $dataFeed[9] = Oficina::where('externa', false)->where('estatus_id', 3)->count();
        $dataFeed[10] = (clone $query)->where('devolucion', true)->count();
        $semaforoQuery = OficinaSemaforoPostal::query();
        if ($oficinaId) {
            $semaforoQuery->where('oficina_id', $oficinaId);
        } elseif ($estadoId) {
            $semaforoQuery->whereHas('oficinas', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        } elseif ($accessStates) {
            // Si el usuario es CNC, no filtramos por estado en el semáforo postal
            if (!$user->hasRole('CNC')) {
                $semaforoQuery->whereHas('oficinas', function ($q) use ($accessStates) {
                    $q->whereIn('estado_id', $accessStates);
                });
            }
        }

        $dataFeed[11] = (clone $semaforoQuery)->where('condicion', 'Propia Ipostel')->count();
        $dataFeed[12] = (clone $semaforoQuery)->where('condicion', 'Arrendada')->count();
        $dataFeed[13] = (clone $semaforoQuery)->where('condicion', 'En Comodato')->count();

        $dataFeed[14] = (clone $query)->sum('coste');

        // Nuevos datos para envíos entregados (dashboard-card-14)
        $dataFeed[15] = (clone $queryEntregados)->count();
        $dataFeed[16] = (clone $queryEntregados)->where('nacional?', true)->count();
        $dataFeed[17] = (clone $queryEntregados)->where('nacional?', false)->count();

        // Ingresos de envíos entregados (dashboard-card-15)
        $dataFeed[18] = (clone $queryIngresosEntregados)->sum('monto');
        $dataFeed[19] = (clone $queryIngresosEntregados)->whereHas('envio', function ($q) {
            $q->where('tipo_envio', 'nacional');
        })->sum('monto');
        $dataFeed[20] = (clone $queryIngresosEntregados)->whereHas('envio', function ($q) {
            $q->where('tipo_envio', 'internacional');
        })->sum('monto');

        $chartEnvios = ['todos' => ['data' => [], 'labels' => []], 'nacional' => ['data' => [], 'labels' => []], 'internacional' => ['data' => [], 'labels' => []]];
        $chartEntregados = ['todos' => ['data' => [], 'labels' => []], 'nacional' => ['data' => [], 'labels' => []], 'internacional' => ['data' => [], 'labels' => []]];
        $chartIngresos = ['todos' => ['data' => [], 'labels' => []], 'nacional' => ['data' => [], 'labels' => []], 'internacional' => ['data' => [], 'labels' => []]];
        $chartMargen = ['data' => [], 'labels' => []];
        $chartDevoluciones = ['todos' => ['data' => [], 'labels' => []]];
        $chartIngresosEntregados = ['todos' => ['data' => [], 'labels' => []], 'nacional' => ['data' => [], 'labels' => []], 'internacional' => ['data' => [], 'labels' => []]];

        $rangos = [];
        $diffInDays = (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay());

        if ($diffInDays === 0) {
            // Un solo día (por hora)
            $horaLimite = $start->isToday() ? $fecha->hour : 23;
            for ($hora = 0; $hora <= $horaLimite; $hora++) {
                $inicio = $start->copy()->startOfDay()->addHours($hora);
                $fin = $inicio->copy()->endOfHour();
                $rangos[] = ['inicio' => $inicio, 'fin' => $fin, 'label' => $inicio->format('h A')];
            }
        } elseif ($diffInDays <= 31) {
            // Hasta 1 mes (por día)
            for ($i = 0; $i <= $diffInDays; $i++) {
                $inicio = $start->copy()->addDays($i)->startOfDay();
                $fin = $inicio->copy()->endOfDay();
                $rangos[] = ['inicio' => $inicio, 'fin' => $fin, 'label' => $inicio->format('d-m-Y')];
            }
        } elseif ($diffInDays <= 90) {
            // Hasta 3 meses (por semana)
            $startLoop = $start->copy()->startOfWeek();
            while ($startLoop->lessThanOrEqualTo($end)) {
                $inicio = $startLoop->copy();
                $fin = $startLoop->copy()->endOfWeek();
                if ($inicio->lessThan($start)) $inicio = $start->copy();
                if ($fin->greaterThan($end)) $fin = $end->copy();
                $rangos[] = ['inicio' => $inicio, 'fin' => $fin, 'label' => 'Sem '.$inicio->weekOfYear];
                $startLoop->addWeek();
            }
        } else {
            // Más de 3 meses (por mes)
            $startLoop = $start->copy()->startOfMonth();
            while ($startLoop->lessThanOrEqualTo($end)) {
                $inicio = $startLoop->copy();
                $fin = $startLoop->copy()->endOfMonth();
                if ($inicio->lessThan($start)) $inicio = $start->copy();
                if ($fin->greaterThan($end)) $fin = $end->copy();
                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                $label = $meses[$inicio->month - 1] . ' ' . $inicio->format('y');
                $rangos[] = ['inicio' => $inicio, 'fin' => $fin, 'label' => $label];
                $startLoop->addMonth();
            }
        }

        // Granularidad de agrupación SQL según el tamaño del período (misma lógica
        // que definió $rangos arriba): 1 día → hora, ≤1 mes → día, ≤3 meses → semana, más → mes.
        if ($diffInDays === 0) {
            $granularidad = 'hour';
        } elseif ($diffInDays <= 31) {
            $granularidad = 'day';
        } elseif ($diffInDays <= 90) {
            $granularidad = 'week';
        } else {
            $granularidad = 'month';
        }

        // Labels (comunes a todas las series)
        $labels = array_map(fn($r) => $r['label'], $rangos);

        // Cada serie se resuelve en UNA consulta con GROUP BY (antes: un query por rango).
        $chartEnvios['todos']['data']          = $this->serieAgrupada($query, $rangos, $granularidad);
        $chartEnvios['nacional']['data']       = $this->serieAgrupada((clone $query)->where('tipo_envio', 'nacional'), $rangos, $granularidad);
        $chartEnvios['internacional']['data']  = $this->serieAgrupada((clone $query)->where('tipo_envio', 'internacional'), $rangos, $granularidad);
        $chartEnvios['todos']['labels'] = $chartEnvios['nacional']['labels'] = $chartEnvios['internacional']['labels'] = $labels;

        $chartEntregados['todos']['data']         = $this->serieAgrupada($queryEntregados, $rangos, $granularidad);
        $chartEntregados['nacional']['data']      = $this->serieAgrupada((clone $queryEntregados)->where('nacional?', true), $rangos, $granularidad);
        $chartEntregados['internacional']['data'] = $this->serieAgrupada((clone $queryEntregados)->where('nacional?', false), $rangos, $granularidad);
        $chartEntregados['todos']['labels'] = $chartEntregados['nacional']['labels'] = $chartEntregados['internacional']['labels'] = $labels;

        $chartIngresos['todos']['data']         = $this->serieAgrupada($query, $rangos, $granularidad, 'created_at', 'sum', 'coste');
        $chartIngresos['nacional']['data']      = $this->serieAgrupada((clone $query)->where('tipo_envio', 'nacional'), $rangos, $granularidad, 'created_at', 'sum', 'coste');
        $chartIngresos['internacional']['data'] = $this->serieAgrupada((clone $query)->where('tipo_envio', 'internacional'), $rangos, $granularidad, 'created_at', 'sum', 'coste');
        $chartIngresos['todos']['labels'] = $chartIngresos['nacional']['labels'] = $chartIngresos['internacional']['labels'] = $labels;

        // Margen: sincronizado con el total de ingresos (misma serie).
        $chartMargen['data']   = $chartIngresos['todos']['data'];
        $chartMargen['labels'] = $labels;

        // Ingresos entregados: la fecha vive en facturacion_destinatario (calificada por el join).
        $chartIngresosEntregados['todos']['data']         = $this->serieAgrupada($queryIngresosEntregados, $rangos, $granularidad, 'facturacion_destinatario.created_at', 'sum', 'monto');
        $chartIngresosEntregados['nacional']['data']      = $this->serieAgrupada((clone $queryIngresosEntregados)->whereHas('envio', fn($q) => $q->where('tipo_envio', 'nacional')), $rangos, $granularidad, 'facturacion_destinatario.created_at', 'sum', 'monto');
        $chartIngresosEntregados['internacional']['data'] = $this->serieAgrupada((clone $queryIngresosEntregados)->whereHas('envio', fn($q) => $q->where('tipo_envio', 'internacional')), $rangos, $granularidad, 'facturacion_destinatario.created_at', 'sum', 'monto');
        $chartIngresosEntregados['todos']['labels'] = $chartIngresosEntregados['nacional']['labels'] = $chartIngresosEntregados['internacional']['labels'] = $labels;

        $chartDevoluciones['todos']['data']   = $this->serieAgrupada((clone $query)->where('devolucion', true), $rangos, $granularidad);
        $chartDevoluciones['todos']['labels'] = $labels;

        // Graph 3: Envíos por servicio (solo servicios relevantes de los formularios de envíos)
        $chartServiciosEnvios = ['data' => [], 'labels' => []];
        $serviciosEnvioIds = [1, 9, 10, 14, 15, 17, 18, 19, 21, 22];
        $servicios = Servicio::whereIn('servicio_id', $serviciosEnvioIds)->orderBy('servicio_id')->get();
        foreach ($servicios as $index => $servicio) {
            $chartServiciosEnvios['data'][$index] = (clone $query)->where('servicio_id', $servicio->servicio_id)->count();
            $chartServiciosEnvios['labels'][$index] = $servicio->nombre;
        }

        // Graph 4: Ingresos por servicio
        $chartServiciosIngresos = ['data' => [], 'labels' => []];
        foreach ($servicios as $index => $servicio) {
            $chartServiciosIngresos['data'][$index] = (clone $query)->where('servicio_id', $servicio->servicio_id)->sum('coste');
            $chartServiciosIngresos['labels'][$index] = $servicio->nombre;
        }

        // Graph 9: Margen de beneficio (Sincronizado con el Total superior)
        // El arreglo $chartMargen ya se pobló arriba con la serie de tiempo correspondiente a dataFeed[14]

        // Graph 16: Envíos recibidos por estado (o por oficina si hay estado filtrado)
        // Mismo criterio que Paquetes por Oficina: estatus_id 3 (Llegada desde OPT) y 4 (Llegada desde COP).
        // Tipos de oficina visibles: 1-3 = OPT (Pequeña, Mediana, Grande), 4 = COP.
        $tiposOficinaVisibles = [1, 2, 3, 4];

        if ($estadoId) {
            // Desglose por oficina del estado seleccionado.
            // Si además hay una oficina filtrada, solo se muestra esa.
            $oficinasEstado = Oficina::where('estado_id', $estadoId)
                ->whereIn('tipo_oficina_id', $tiposOficinaVisibles)
                ->when($oficinaId, fn($q) => $q->where('oficina_id', $oficinaId))
                ->orderBy('nombre')
                ->get(['oficina_id', 'nombre']);

            $conteoEnviosPorOficina = DB::table('envios_encaminamiento as ee')
                ->whereIn('ee.estatus_id', [3, 4])
                ->whereBetween('ee.created_at', [$start, $end])
                ->whereIn('ee.oficina_id', $oficinasEstado->pluck('oficina_id'))
                ->select('ee.oficina_id', DB::raw('COUNT(DISTINCT ee.envio_id) as total'))
                ->groupBy('ee.oficina_id')
                ->pluck('total', 'oficina_id');

            $chartEnviosPorEstado = ['labels' => [], 'data' => [], 'modo' => 'oficinas'];
            foreach ($oficinasEstado as $of) {
                $chartEnviosPorEstado['labels'][] = $of->nombre;
                $chartEnviosPorEstado['data'][] = (int) ($conteoEnviosPorOficina[$of->oficina_id] ?? 0);
            }
        } else {
            // Vista nacional: agregado por estado
            $estadosQuery = Estado::query();
            if (!empty($accessStates)) {
                $estadosQuery->whereIn('estado_id', $accessStates);
            }
            $estadosGrafica = $estadosQuery->orderBy('nombre')->get(['estado_id', 'nombre']);

            $conteoEnviosPorEstado = DB::table('envios_encaminamiento as ee')
                ->join('oficinas as o', 'o.oficina_id', '=', 'ee.oficina_id')
                ->whereIn('ee.estatus_id', [3, 4])
                ->whereBetween('ee.created_at', [$start, $end])
                ->whereIn('o.tipo_oficina_id', $tiposOficinaVisibles)
                ->when($oficinaId, fn($q) => $q->where('ee.oficina_id', $oficinaId))
                ->select('o.estado_id', DB::raw('COUNT(DISTINCT ee.envio_id) as total'))
                ->groupBy('o.estado_id')
                ->pluck('total', 'estado_id');

            $chartEnviosPorEstado = ['labels' => [], 'data' => [], 'modo' => 'estados'];
            foreach ($estadosGrafica as $estado) {
                $chartEnviosPorEstado['labels'][] = $estado->nombre;
                $chartEnviosPorEstado['data'][] = (int) ($conteoEnviosPorEstado[$estado->estado_id] ?? 0);
            }
        }

        return view('pages.dashboard.dashboard', [
            'dataFeed' => $dataFeed,
            'oficinas' => $oficinas,
            'oficinaId' => $oficinaId,
            'estadoId' => $estadoId,
            'estados' => $estados,
            'chartEnvios' => json_encode($chartEnvios),
            'chartEntregados' => json_encode($chartEntregados),
            'chartIngresos' => json_encode($chartIngresos),
            'chartServiciosEnvios' => json_encode($chartServiciosEnvios),
            'chartServiciosIngresos' => json_encode($chartServiciosIngresos),
            'chartMargen' => json_encode($chartMargen),
            'chartDevoluciones' => json_encode($chartDevoluciones),
            'chartIngresosEntregados' => json_encode($chartIngresosEntregados),
            'chartEnviosPorEstado' => json_encode($chartEnviosPorEstado),
            'fechaInicioStr' => $start->format('Y-m-d'),
            'fechaFinStr' => $end->format('Y-m-d'),
        ]);
    }


    public function totalEnvios()
    {
        $result = [];
        $resultn = [];
        $resulti = [];

        $fecha = now();
        $mes = $fecha->month;
        $anio = $fecha->year;

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin]);
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $envios = $query->count();

            $result['data'][$dia - 1] = $envios;
            $result['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin])
                ->where('tipo_envio', 'nacional');
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $envios = $query->count();

            $resultn['data'][$dia - 1] = $envios;
            $resultn['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin])
                ->where('tipo_envio', 'internacional');
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $envios = $query->count();

            $resulti['data'][$dia - 1] = $envios;
            $resulti['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        return (object) [
            "todos" => $result,
            "nacional" => $resultn,
            "internacional" => $resulti
        ];
    }

    public function ingresosEnvios()
    {
        $result = [];
        $resultn = [];
        $resulti = [];

        $fecha = now();
        $mes = $fecha->month;
        $anio = $fecha->year;

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin]);
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $total = $query->sum('coste');

            $result['data'][$dia - 1] = $total;
            $result['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin])
                ->where('tipo_envio', 'nacional');
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $total = $query->sum('coste');

            $resultn['data'][$dia - 1] = $total;
            $resultn['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin])
                ->where('tipo_envio', 'internacional');
            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }
            $total = $query->sum('coste');

            $resulti['data'][$dia - 1] = $total;
            $resulti['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        return (object) [
            "todos" => $result,
            "nacional" => $resultn,
            "internacional" => $resulti
        ];
    }

    public function totalServicios()
    {
        $result = ['data' => [], 'labels' => []];

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $servicios = Servicio::all();

        foreach ($servicios as $index => $servicio) {
            $query = Envio::whereBetween('created_at', [$inicioMes, $finMes])
                ->where('servicio_id', $servicio->servicio_id);

            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }

            $envios = $query->count();

            $result['data'][$index] = $envios;
            $result['labels'][$index] = $servicio->nombre;
        }

        return $result;
    }

    public function ingresosServicios()
    {
        $result = ['data' => [], 'labels' => []];

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $servicios = Servicio::all();

        foreach ($servicios as $index => $servicio) {
            $query = Envio::whereBetween('created_at', [$inicioMes, $finMes])
                ->where('servicio_id', $servicio->servicio_id);

            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }

            $total = $query->sum('coste');

            $result['data'][$index] = $total;
            $result['labels'][$index] = $servicio->nombre;
        }

        return $result;
    }

    public function tasaDevoluiciones()
    {
        $result = [];

        $fecha = now();
        $mes = $fecha->month;
        $anio = $fecha->year;

        for ($dia = 1; $dia <= $fecha->day; $dia++) {
            $fechaMes = now()->format('Y-m-');
            $diaInicio = $fechaMes . $dia . " 00:00:00";
            $diaFin = $fechaMes . $dia . " 23:59:59";

            $query = Envio::whereBetween('created_at', [$diaInicio, $diaFin])->where('devolucion', true);

            if (!is_null($this->oficinaId)) {
                $query->where('oficina_id', $this->oficinaId);
            }

            $envios = $query->count();

            $result['data'][$dia - 1] = $envios;
            $result['labels'][$dia - 1] = $dia . "-" . $mes;
        }

        return (object) [
            "todos" => $result,
        ];
    }

    public function margenBeneficio()
    {
        $result = [];

        if (!is_null($this->oficinaId)) {
            $totalIngresos = Envio::where('oficina_id', $this->oficinaId)->sum('coste');
            $totalIngresoscostos = Envio::where('oficina_id', $this->oficinaId)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('coste');
        } else {
            $totalIngresos = Envio::sum('coste');
            $totalIngresoscostos = Envio::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('coste');
        }

        $margenBeneficio = $totalIngresoscostos > 0
            ? ($totalIngresos - $totalIngresoscostos) / $totalIngresoscostos * 100
            : 0;

        $result['data'][0] = $margenBeneficio;
        $result['data'][1] = $totalIngresos;
        $result['data'][2] = $totalIngresoscostos;
        $result['labels'][0] = 'Margen de Beneficio';
        $result['labels'][1] = 'Ingresos Totales';
        $result['labels'][2] = 'Costos Totales';

        return $result;
    }

    /**
     * Agrega una serie temporal en UNA sola consulta con GROUP BY, en vez de un
     * query por rango (elimina el N+1 del dashboard).
     *
     * Agrupa las filas por su período (hora/día/semana/mes según la granularidad)
     * usando date_trunc de PostgreSQL, y mapea cada grupo al índice del rango cuyo
     * intervalo [inicio, fin] lo contiene. Devuelve un array numérico alineado con
     * $rangos (posiciones sin datos quedan en 0), idéntico a lo que producía el
     * bucle original.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $baseQuery  Query ya filtrado (fecha + permisos)
     * @param  array   $rangos      Rangos con claves inicio/fin (Carbon)
     * @param  string  $granularidad 'hour'|'day'|'week'|'month'
     * @param  string  $fechaCol    Columna de fecha a agrupar (calificada si hay joins)
     * @param  string  $op          'count' o 'sum'
     * @param  string|null $sumCol   Columna a sumar cuando $op = 'sum'
     * @return array  Valores numéricos indexados por posición de rango
     */
    private function serieAgrupada($baseQuery, array $rangos, string $granularidad, string $fechaCol = 'created_at', string $op = 'count', ?string $sumCol = null): array
    {
        $agg = $op === 'sum'
            ? DB::raw('SUM(' . $sumCol . ') as valor')
            : DB::raw('COUNT(*) as valor');

        // date_trunc normaliza cada fila al inicio de su período; agrupamos por eso.
        $grupos = (clone $baseQuery)
            ->select(DB::raw("date_trunc('{$granularidad}', {$fechaCol}) as periodo"), $agg)
            ->groupBy(DB::raw("date_trunc('{$granularidad}', {$fechaCol})"))
            ->pluck('valor', 'periodo');

        // Mapear cada grupo (por su timestamp de inicio de período) al rango que lo contiene.
        $resultado = array_fill(0, count($rangos), 0);
        foreach ($grupos as $periodo => $valor) {
            $ts = \Carbon\Carbon::parse($periodo);
            foreach ($rangos as $i => $rango) {
                if ($ts->betweenIncluded($rango['inicio'], $rango['fin'])) {
                    $resultado[$i] += $op === 'sum' ? (float) $valor : (int) $valor;
                    break;
                }
            }
        }

        return $resultado;
    }

    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
