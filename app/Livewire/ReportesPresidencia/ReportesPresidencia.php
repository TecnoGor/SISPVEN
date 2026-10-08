<?php

namespace App\Livewire\ReportesPresidencia;

use Carbon\Carbon;
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\EnvioAlmacen;
use App\Exports\DCLM23Export;
use Livewire\Attributes\Layout;
use App\Exports\CssEstadoExport;
use App\Exports\DevolucionesExport;
use Illuminate\Support\Facades\Log;
use App\Exports\EstadoAEstadoExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SemaforoPostalExport;
use App\Exports\ControlDeGastosExport;
use App\Models\TarifaInternacionalConcepto;
use App\Exports\EntregasReportePresidenciaExport;


#[Layout('layouts.app')]
class ReportesPresidencia extends Component
{
    public $desde, $hasta, $estado, $oficina;
    public $estados = [];
    public $oficinas = [];


    public function mount()
    {
        $this->estados = Estado::all();
    }


    public function updatedEstado()
    {
        if (!$this->estado) {
            $this->oficinas = [];
        } else {
            $this->oficinas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3])->where('estado_id', $this->estado)->get();
        }
    }
    public function estado_a_estado()
    {
        if (!$this->desde || !$this->hasta) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un rango de fechas para generar el reporte.');
            return;
        }

        $desde = \Carbon\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Carbon\Carbon::parse($this->hasta)->endOfDay();

        $query = \App\Models\Saca::whereHas('envios.envio_encaminamientos', function ($q) use ($desde, $hasta) {
            $q->whereHas('oficina_externa', function ($q2) {
                $q2->where('tipo_oficina_id', 4)
                    ->where('externa', true)
                    ->where('oficina_id', '!=', 218);
            })->whereBetween('created_at', [$desde, $hasta]);
        });

        if ($this->estado) {
            $query->whereHas('oficinaOrigen', function ($q) {
                $q->where('estado_id', $this->estado);
            });
        }

        if ($query->count() === 0) {
            $this->dispatch('alertSuccess2', message: 'No hay valijas enviadas hacia Aliados en las fechas dadas.');
            return;
        }

        return Excel::download(new EstadoAEstadoExport($this->desde, $this->hasta, $this->estado), 'master_de_estado.xlsx');
    }

    public function control_de_gastos()
    {
        // Construir la consulta base
        $query = Oficina::with([
            'pagos_servicios_publicos' => fn($q) => $q->where('estatus', false),
            'gasto_arrendamiento'      => fn($q) => $q->where('estatus', false),
        ])->orderBy('oficina_id', 'desc');

        if ($this->oficina) {
            $query->where('oficina_id', $this->oficina);
        } elseif ($this->estado) {
            $query->where('estado_id', $this->estado)
                ->whereIn('tipo_oficina_id', [1, 2, 3]);
        } else {
            $query->whereIn('tipo_oficina_id', [1, 2, 3]);
        }

        $oficinas = $query->get();

        return Excel::download(new ControlDeGastosExport($oficinas), 'control_de_gastos.xlsx');
    }


    public function dclm23()
    {
        try {
            // Validar fechas y oficina
            if (!$this->desde || !$this->hasta || !$this->oficina) {
                $this->dispatch('alertSuccess2', message: 'Debe seleccionar una oficina y un rango de fechas para generar el reporte.');
                return;
            }

            $from = Carbon::parse($this->desde)->startOfDay();
            $to   = Carbon::parse($this->hasta)->endOfDay();

            // Consulta principal
            $envios = Envio::with([
                'oficinaOrigen',
                'oficinaDestino',
                'estadoDestino',
                'servicio',
                'envio_encaminamientos.envio_estatus'
            ])
                ->whereBetween('created_at', [$from, $to])
                ->where(function ($q) {
                    $q->where('oficina_id', $this->oficina)
                        ->orWhere('oficina_dest_id', $this->oficina);
                })
                ->whereHas('servicio', fn($q) => $q->where('servicio_id', '!=', 3))
                ->orderBy('created_at', 'asc')
                ->get();

            if ($envios->isEmpty()) {
                $this->dispatch('alertSuccess2', message: 'No hay envíos para la oficina seleccionada.');
                return;
            }

            // Datos del usuario
            $oficinaUser = auth()->user()->oficina;
            $oficina = Oficina::with('estado.region')->where('oficina_id', $this->oficina)->first();
            $estadoNombre = $oficina->estado->nombre ?? '';
            $regionNombre = $oficina->estado->region->nombre ?? '';

            // Mes en español
            setlocale(LC_TIME, 'es_ES.UTF-8');
            $mesReporte = ucfirst(Carbon::now()->translatedFormat('F'));

            return Excel::download(
                new \App\Exports\DCLM23Export($envios, $estadoNombre, $oficina, $regionNombre, $mesReporte),
                'DCLM23.xlsx'
            );
        } catch (\Exception $e) {
            logger('Error DCLM23: ' . $e->getMessage());
            $this->dispatch('alertSuccess2', ['message' => $e->getMessage()]);
            return;
        }
    }


    public function semaforo_postal()
    {
        // semaforo_postal trae la condicion (Propia/Arrendada/Comodato) que se
        // marca con X en las columnas I, J y K del reporte. Sin el eager loading
        // se dispararia una consulta por oficina (N+1).
        $oficinas = Oficina::with(['estado.region', 'semaforo_postal', 'municipio', 'parroquia'])->when($this->estado, function ($query) {
            $query->where('estado_id', $this->estado);
        })->whereIn('tipo_oficina_id', [1, 2, 3])->get();

        return Excel::download(new SemaforoPostalExport($oficinas), 'semaforo_postal.xlsx');
    }

    public function devoluciones()
    {
        if (empty($this->oficina)) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar una oficina para consultar los envíos en devolución.');
            return;
        }
        $query = EnvioAlmacen::with(['envio.incidencia'])->where('Salida', null)->where('oficina_id', $this->oficina)
            ->whereHas('envio', function ($q) {
                $q->where('devolucion', true)->whereHas('incidencia');
            });

        $envios = $query->get();

        if ($envios->isEmpty()) {
            $this->dispatch('alertSuccess2', message: 'No hay envios en devolucion para la oficina seleccionada!');
            return;
        }
        return Excel::download(new DevolucionesExport($envios), 'devoluciones.xlsx');
    }

    public function css_estado()
    {
        if (!$this->desde || !$this->hasta) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un rango de fechas para generar el reporte.');
            return;
        }

        $desde = \Carbon\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Carbon\Carbon::parse($this->hasta)->endOfDay();

        $query = \App\Models\Saca::whereHas('envios.envio_encaminamientos', function ($q) use ($desde, $hasta) {
            $q->where('oficina_externa_id', 218) // MRW
                ->whereBetween('created_at', [$desde, $hasta]);
        });

        if ($this->estado) {
            $query->whereHas('oficinaOrigen', function ($q) {
                $q->where('estado_id', $this->estado);
            });
        }

        if ($query->count() === 0) {
            $this->dispatch('alertSuccess2', message: 'No hay valijas enviadas hacia MRW con esos filtros en las fechas dadas.');
            return;
        }

        return Excel::download(new CssEstadoExport($this->desde, $this->hasta, $this->estado), 'css_estado.xlsx');
    }

    public function entrega()
    {
        if (!$this->desde || !$this->hasta) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un rango de fechas para generar el reporte.');
            return;
        }

        if ($this->oficina) {
            $oficinas_filtradas = [$this->oficina];
        } elseif ($this->estado) {
            $oficinas_filtradas = Oficina::where('estado_id', $this->estado)
                ->whereIn('tipo_oficina_id', [1, 2, 3])
                ->pluck('oficina_id')
                ->toArray();
        } else {
            $oficinas_filtradas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3])
                ->pluck('oficina_id')
                ->toArray();
        }

        // Consulta principal
        $envios = EnvioAlmacen::with(['envio', 'registro_entrega', 'envio_internacional'])
            ->whereIn('oficina_id', $oficinas_filtradas)
            ->where('estatus', false)
            ->whereBetween('Salida', [$this->desde, $this->hasta])
            ->whereHas('registro_entrega')
            ->get();

        if ($envios->isEmpty()) {
            $this->dispatch('alertSuccess2', message: 'No hay envíos para los filtros seleccionados.');
            return;
        }

        return Excel::download(new EntregasReportePresidenciaExport($envios), 'entregas.xlsx');
    }


    public function envios_consignados()
    {
        if (!$this->desde || !$this->hasta) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un rango de fechas para generar el reporte de envíos.');
            return;
        }

        // Defensa en profundidad: el streaming mantiene la memoria plana, pero un
        // chunk puntual con muchas relaciones puede ser pesado. A nivel nacional
        // la generacion tambien puede tardar varios minutos.
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $desde = \Carbon\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Carbon\Carbon::parse($this->hasta)->endOfDay();

        $nivel = 'NIVEL NACIONAL';
        if ($this->oficina) {
            $oficinas_filtradas = [$this->oficina];
            $oficinaModel = Oficina::find($this->oficina);
            $nivel = 'OFICINA: ' . ($oficinaModel ? strtoupper($oficinaModel->nombre) : '');
        } elseif ($this->estado) {
            $oficinas_filtradas = Oficina::where('externa', false)
                ->where('estado_id', $this->estado)
                ->pluck('oficina_id')
                ->toArray();
            $estadoModel = Estado::find($this->estado);
            $nivel = 'ESTADO: ' . ($estadoModel ? strtoupper($estadoModel->nombre) : '');
        } else {
            $oficinas_filtradas = Oficina::where('externa', false)
                ->pluck('oficina_id')
                ->toArray();
        }

        // Query SIN ejecutar: el export la recorre por bloques (WithChunkReading).
        // El orderBy explícito es obligatorio — sin un orden estable la paginación
        // interna de los chunks puede repetir u omitir filas.
        $envios = Envio::query()
            ->select([
                'envios.envio_id',
                'envios.codigo_envio',
                'envios.contenido',
                'envios.peso',
                'envios.coste_sin_iva',
                'envios.tasa_bs',
                'envios.tipo_envio',
                'envios.oficina_id',
                'envios.servicio_id',
                'envios.estado_dest',
                'envios.devolucion',
                'envios.created_at',
            ])
            ->with([
                'oficinaOrigen:oficina_id,nombre,estado_id',
                'oficinaOrigen.estado:estado_id,nombre',
                'estadoDestino:estado_id,nombre',
                'servicio:servicio_id,nombre',
                'encaminamiento_actual',
                'encaminamiento_actual.envio_estatus:envios_estatus_id,estatus',
                // Pagos del envío (método y referencia) vía su facturación.
                'factura_envio.facturaciones.pagos.tipos_pagos:tipo_pago_id,nombre',
            ])
            ->whereIn('oficina_id', $oficinas_filtradas)
            ->whereBetween('created_at', [$desde, $hasta])
            ->orderBy('envios.envio_id');

        // exists() es un LIMIT 1: no cuenta filas ni materializa la colección.
        // Se clona para no mutar la query que recibe el export.
        if (! (clone $envios)->exists()) {
            $this->dispatch('alertSuccess2', message: 'No hay envíos para los filtros seleccionados.');
            return;
        }

        return Excel::download(new \App\Exports\EnviosPresidencia($envios, auth()->user(), $nivel, $desde->format('d/m/Y'), $hasta->format('d/m/Y')), 'envios_consignados.xlsx');
    }


    public function envios_recibidos()
    {
        if (!$this->desde || !$this->hasta) {
            $this->dispatch('alertSuccess2', message: 'Debe seleccionar un rango de fechas para generar el reporte de envíos recibidos.');
            return;
        }

        // Defensa en profundidad: el streaming mantiene la memoria plana, pero un
        // chunk puntual con muchas relaciones puede ser pesado. A nivel nacional
        // la generacion tambien puede tardar varios minutos.
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $desde = \Carbon\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Carbon\Carbon::parse($this->hasta)->endOfDay();

        $nivel = 'NIVEL NACIONAL';
        if ($this->oficina) {
            $oficinas_filtradas = [$this->oficina];
            $oficinaModel = Oficina::find($this->oficina);
            $nivel = 'OFICINA: ' . ($oficinaModel ? strtoupper($oficinaModel->nombre) : '');
        } elseif ($this->estado) {
            $oficinas_filtradas = Oficina::where('externa', false)
                ->where('estado_id', $this->estado)
                ->pluck('oficina_id')
                ->toArray();
            $estadoModel = Estado::find($this->estado);
            $nivel = 'ESTADO: ' . ($estadoModel ? strtoupper($estadoModel->nombre) : '');
        } else {
            $oficinas_filtradas = Oficina::where('externa', false)
                ->pluck('oficina_id')
                ->toArray();
        }

        // Query SIN ejecutar: el export la recorre por bloques (WithChunkReading).
        // El orderBy explícito es obligatorio — sin un orden estable la paginación
        // interna de los chunks puede repetir u omitir filas.
        $enviosAlmacen = EnvioAlmacen::query()->with([
            'oficina:oficina_id,nombre,estado_id',
            'oficina.estado:estado_id,nombre',
            'envio:envio_id,codigo_envio,contenido,peso,coste_sin_iva,tasa_bs,tipo_envio,oficina_id,servicio_id,estado_dest,devolucion,created_at',
            'envio.oficinaOrigen:oficina_id,nombre,estado_id',
            'envio.oficinaOrigen.estado:estado_id,nombre',
            'envio.estadoDestino:estado_id,nombre',
            'envio.servicio:servicio_id,nombre',
            'envio.encaminamiento_actual',
            'envio.encaminamiento_actual.envio_estatus:envios_estatus_id,estatus',
            // Pagos del envío (método y referencia) vía su facturación.
            'envio.factura_envio.facturaciones.pagos.tipos_pagos:tipo_pago_id,nombre',
        ])
            ->whereIn('oficina_id', $oficinas_filtradas)
            ->whereBetween('Entrada', [$desde, $hasta])
            ->whereHas('envio', function ($q) {
                $q->whereColumn('envios.oficina_id', '!=', 'envios_almacen.oficina_id');
            })
            ->orderBy('envios_almacen.envio_almacen_id');

        // exists() es un LIMIT 1: no cuenta filas ni materializa la colección.
        // Se clona para no mutar la query que recibe el export.
        if (! (clone $enviosAlmacen)->exists()) {
            $this->dispatch('alertSuccess2', message: 'No hay envíos recibidos para los filtros seleccionados.');
            return;
        }

        return Excel::download(
            new \App\Exports\EnviosRecibidoExport($enviosAlmacen, auth()->user(), $nivel, $desde->format('d/m/Y'), $hasta->format('d/m/Y')),
            'envios_recibidos.xlsx'
        );
    }


    public function render()
    {
        return view('livewire.reportes-presidencia.reportes-presidencia');
    }
}
