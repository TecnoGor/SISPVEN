<?php

namespace App\Livewire\Envios;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Envio;
use App\Models\Insumo;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Servicio;
use App\Models\TipoPago;
use App\Exports\Formato044;
use App\Exports\Formato047;
use App\Models\EnvioInsumo;
use App\Models\Facturacion;
use App\Models\AlmacenAviso;
use App\Models\InsumoUsuario;
use App\Models\UsuarioCierre;
use App\Models\FacturacionPago;
use App\Models\RegistroEntrega;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\InventarioInsumo;
use App\Models\RegistroApartado;
use App\Exports\Formato043Export;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\InsumoTransferencia;
use App\Models\FacturacionTarifaEnvio;
use App\Models\TarifaNacionalConcepto;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\InsumoUsuarioTransferencia;
use App\Models\RespaldoInventarioDiario;
use App\Models\RespaldoInventarioUsuario;
use App\Models\TarifaInternacionalConcepto;
use App\Models\UsuarioSeguimiento;
use Maatwebsite\Excel\Facades\Excel;  // Asegúrate de incluir esta línea

#[Layout('layouts.app')]
class CierreCajaGeneral extends Component
{

    public $servicio_id, $servicios, $cantidad, $usuario_id, $oficina_id, $desde, $hasta, $sobrante, $envio_base, $total_nacional, $total_internacional, 
    $fecha044, $total_total;
    public $metodos_pago = [];
    public $usuarios = [];
    public $total_pagar = [];
    public $total_pagado = [];
    public $iva = [];
    public $subservicios = [];
    public $registros = [];


    public function updatedOficinaId($value)
    {

        if ($value) {
            $this->usuarios = User::where('oficina_id', $value)->get();
            $this->usuario_id = null;
        }
        else {
            $this->usuarios = [];
            $this->usuario_id = null;
        }

    }

    public function selectedConfirmarCierre()
    {
        $query_cierre = UsuarioCierre::where('usuario_id', auth()->user()->id)
        ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->exists();

        if ($query_cierre) {
            toastr()->error('Ya se ha realizado un cierre de caja para el día de hoy');
        }
        else {
            $cierre = UsuarioCierre::create([
                'usuario_id' => auth()->user()->id,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se realizó el cierre de caja ({$cierre->usuario_cierre_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Cierre de caja realizado exitosamente!');

        }
    }

    public function render()
    {
        
        $this->usuarios = [];
        $this->total_pagar = [];
        $this->total_pagado = [];
        $this->iva = [];
        $this->subservicios = [];
        $this->registros = [];
        $this->metodos_pago = [];
        $this->total_internacional = 0;
        $this->total_nacional = 0;
        $this->total_total = 0;

        $oficinas = Oficina::where('oficina_relacionada_id', auth()->user()->oficina_id)->get();   
        $servicios = Servicio::all();

        $this->servicios = $servicios;

        $usuario_id = $this->usuario_id;
        $oficina_id = $this->oficina_id;

        if (auth()->user()->hasRole('Jefe de OPT')) {
            if(!$this->usuarios)
            {
                $this->usuarios = User::where('oficina_id', auth()->user()->oficina_id)->get();
            }
            if(!$this->oficina_id)
            {
                $this->oficina_id = Oficina::find(auth()->user()->oficina_id)->oficina_id;
            }
        }

        $fechaInicio = $this->desde;
        $fechaFin = $this->hasta;

        if ((!$fechaInicio)||(!$fechaFin)) {
            $fechaInicio = now()->startOfDay();
            $fechaFin = now()->endOfDay();

            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        }
        else {
            $fechaInicio = $fechaInicio."  00:00:00";
            $fechaFin = $fechaFin."  23:59:59";
        }

        foreach ($servicios as $servicio) {

        $registros[$servicio->servicio_id] = FacturacionEnvio::where('servicio_id', $servicio->servicio_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($oficina_id) {

            $registros[$servicio->servicio_id] = $registros[$servicio->servicio_id]->where('oficina_id', $oficina_id);

            if ($usuario_id) {

                $registros[$servicio->servicio_id] = $registros[$servicio->servicio_id]->where('usuario_id', $usuario_id);

            }

        }
        else {

            $registros[$servicio->servicio_id] = $registros[$servicio->servicio_id]->where('oficina_id', auth()->user()->oficina_id)
            ->where('usuario_id', auth()->user()->id);

        }
                
        $registros[$servicio->servicio_id] = $registros[$servicio->servicio_id]->get();

        $cantidad[$servicio->servicio_id] = FacturacionEnvio::where('servicio_id', $servicio->servicio_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($oficina_id) {

            $cantidad[$servicio->servicio_id] = $cantidad[$servicio->servicio_id]->where('oficina_id', $oficina_id);

            if ($usuario_id) {

                $cantidad[$servicio->servicio_id] = $cantidad[$servicio->servicio_id]->where('usuario_id', $usuario_id);

            }

        }
        else {

            $cantidad[$servicio->servicio_id] = $cantidad[$servicio->servicio_id]->where('oficina_id', auth()->user()->oficina_id)
            ->where('usuario_id', auth()->user()->id);

        }

        $cantidad[$servicio->servicio_id] = $cantidad[$servicio->servicio_id]->count();

        $this->registros[$servicio->servicio_id] = $registros[$servicio->servicio_id];
        $this->cantidad[$servicio->servicio_id] = $cantidad[$servicio->servicio_id];

        foreach($this->registros[$servicio->servicio_id] as $registro[$servicio->servicio_id])
        {

            $subservicios[$servicio->servicio_id] = FacturacionTarifaEnvio::where('envio_id', $registro[$servicio->servicio_id]->envio_id)->get();

            if(Servicio::find($servicio->servicio_id)->nacional)
            {
                foreach ($subservicios[$servicio->servicio_id] as $subservicio[$servicio->servicio_id]) {

                    $monto_subservicio[$servicio->servicio_id] = TarifaNacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->monto;

                    if (isset($this->subservicios[$servicio->servicio_id][TarifaNacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre])) {
                        $this->subservicios[$servicio->servicio_id][TarifaNacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre] += $monto_subservicio[$servicio->servicio_id];
                    }
                    else {
                        $this->subservicios[$servicio->servicio_id][TarifaNacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre] = $monto_subservicio[$servicio->servicio_id];
                    }
                }
            }
            else
            {
                foreach ($subservicios[$servicio->servicio_id] as $subservicio[$servicio->servicio_id]) {

                    $monto_subservicio[$servicio->servicio_id] = TarifaInternacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->monto;

                    if (isset($this->subservicios[$servicio->servicio_id][TarifaInternacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre])) {
                        $this->subservicios[$servicio->servicio_id][TarifaInternacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre] += $monto_subservicio[$servicio->servicio_id];
                    }
                    else {
                        $this->subservicios[$servicio->servicio_id][TarifaInternacionalConcepto::find($subservicio[$servicio->servicio_id]->tarifa_id)->nombre] = $monto_subservicio[$servicio->servicio_id];
                    }

                }
            }

            $facturacion[$servicio->servicio_id] = Facturacion::find($registro[$servicio->servicio_id]->facturacion_id);
            $facturacion_detalles[$servicio->servicio_id] = FacturacionDetalle::where('facturacion_id', $registro[$servicio->servicio_id]->facturacion_id)->first();

            $facturacion_pago = FacturacionPago::where('facturacion_id', $registro[$servicio->servicio_id]->facturacion_id)->get();

            foreach ($facturacion_pago as $fact_pago) {

                $tipo_pago = TipoPago::find($fact_pago->tipo_pago_id);

                if (isset($this->metodos_pago[$tipo_pago->nombre])) {

                    $this->metodos_pago[$tipo_pago->nombre] += $fact_pago->monto;

                }
                elseif (!isset($this->metodos_pago[$tipo_pago->nombre])) {

                    $this->metodos_pago[$tipo_pago->nombre] = $fact_pago->monto;

                }
            }
            
            $this->total_pagar[$servicio->servicio_id][$registro[$servicio->servicio_id]->facturacion_id] = $facturacion_detalles[$servicio->servicio_id]->monto;
            $this->total_pagado[$servicio->servicio_id][$registro[$servicio->servicio_id]->facturacion_id] = $facturacion[$servicio->servicio_id]->monto_total;
            $this->iva[$servicio->servicio_id][$registro[$servicio->servicio_id]->facturacion_id] = $facturacion[$servicio->servicio_id]->iva;

        }

        if ($registros[$servicio->servicio_id]->isNotEmpty()) {

            if (isset($this->subservicios[$servicio->servicio_id])) {
                $this->envio_base[$servicio->servicio_id] = array_sum($this->total_pagar[$servicio->servicio_id]) - array_sum($this->iva[$servicio->servicio_id]) - array_sum($this->subservicios[$servicio->servicio_id]) ?? 0;
            }
            else {
                $this->envio_base[$servicio->servicio_id] = array_sum($this->total_pagar[$servicio->servicio_id]) - array_sum($this->iva[$servicio->servicio_id]);
            }
            $this->sobrante[$servicio->servicio_id] = array_sum($this->total_pagado[$servicio->servicio_id]) - array_sum($this->total_pagar[$servicio->servicio_id]);
            $this->iva[$servicio->servicio_id] = array_sum($this->iva[$servicio->servicio_id]);
            $this->total_pagar[$servicio->servicio_id] = array_sum($this->total_pagar[$servicio->servicio_id]);
            $this->total_pagado[$servicio->servicio_id] = array_sum($this->total_pagado[$servicio->servicio_id]);

            if($servicio->nacional)
            {
                $this->total_nacional += $this->total_pagado[$servicio->servicio_id];
            }
            else {
                $this->total_internacional += $this->total_pagado[$servicio->servicio_id];
            }

            $this->total_total += $this->total_pagado[$servicio->servicio_id];

        }

    }

        // Obtener todos los nombres de subservicios únicos
        $allSubservicios = [];
        foreach ($this->subservicios as $servicioId => $subservicio) {
            $allSubservicios = array_merge($allSubservicios, array_keys($subservicio));
        }
        $allSubservicios = array_unique($allSubservicios);

        // Normalizar los subservicios para cada servicio
        foreach ($this->subservicios as $servicioId => &$subservicio) {
            foreach ($allSubservicios as $nombreSubservicio) {
                if (!isset($subservicio[$nombreSubservicio])) {
                    $subservicio[$nombreSubservicio] = 0;
                }
            }
        }

        $usuarioNombre = auth()->user()->name;

        return view('livewire.envios.cierre-caja-general', compact('oficinas', 'usuarioNombre'));
        
    }

    public function export043()
    {
        $usuario = auth()->user();
        $oficina = Oficina::where('oficina_id', $usuario->oficina_id)->first();
        $fecha = $this->desde;
        $envios = Envio::where('usuario_id', $usuario->id)->whereIn('servicio_id', [9, 21, 3])->whereDate('created_at', $fecha)->get();
        $envios_id = $envios->pluck('envio_id')->toArray();
        $insumos = EnvioInsumo::whereIn('envio_id', $envios_id)->get();
        $apartados = RegistroApartado::where('usuario_id', $usuario->id)->whereDate('created_at', $fecha)->get();
        $entregas = RegistroEntrega::where('usuario_id', $usuario->id)->whereDate('created_at', $fecha)->get();
        $entregas_id = $entregas->pluck('envio_id')->toArray();
        $cantidad_avisos = AlmacenAviso::where('envio_id', $entregas_id)->count();
        $insumos_final = InsumoUsuario::where('usuario_id', $usuario->id)->whereIn('insumo_id', [2,4,5])->get();
        $insumos_asignados = InsumoUsuarioTransferencia::where('usuario_id', $usuario->id)->whereDate('created_at', $fecha)->get();
        $insumos_precios = Insumo::whereIn('insumo_id', [2,4,5])->get();
        $ayer = Carbon::parse($this->desde)->subDay()->toDateString();
        $inventario_inicial = RespaldoInventarioUsuario::where('oficina_id', $oficina->oficina_id)->where('usuario_id', $usuario->id)
        ->whereDate('created_at', $ayer)->orderBy('insumo_id')->get();

        return Excel::download(new Formato043Export($usuario, $oficina, $fecha, $envios, $insumos, $apartados, $entregas, $cantidad_avisos, 
        $insumos_final, $insumos_asignados, $insumos_precios, $inventario_inicial), 'Planilla043.xlsx');
    }

    public function export044()
    {   
        if(!$this->fecha044){
            return;
        }else{

            [$year, $week] = explode('-W', $this->fecha044); // divide '2025-W42' en ['2025', '42']
            $lunes = Carbon::parse("{$year}-W{$week}")->startOfWeek();
            $viernes = $lunes->copy()->addDays(4);
            $desde = $lunes->format('Y-m-d');   
            $hasta = $viernes->format('Y-m-d');
            $semana = $lunes->format('W');
            $oficina = Oficina::where('oficina_id', auth()->user()->oficina_id)->first();
            $envios = Envio::whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->where('oficina_id', $oficina->oficina_id)->get();
            $envios_id = $envios->pluck('envio_id')->toArray();
            $insumos = EnvioInsumo::whereIn('envio_id', $envios_id)->get();
            $apartados = RegistroApartado::whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->where('oficina_id', $oficina->oficina_id)->get();
            $entregas = RegistroEntrega::whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->where('oficina_id', $oficina->oficina_id)->get();

            return Excel::download(new Formato044($desde, $hasta, $semana, $oficina, $envios, $insumos, $apartados, $entregas), 'Planilla044.xlsx');
        }  
    }

    public function export047()
    {
        if(!$this->fecha044){
            return;
        }else{
            [$year, $week] = explode('-W', $this->fecha044); // divide '2025-W42' en ['2025', '42']
            $lunes = Carbon::parse("{$year}-W{$week}")->startOfWeek();
            $viernes = $lunes->copy()->addDays(4);
            $desde = $lunes->format('Y-m-d');   
            $hasta = $viernes->format('Y-m-d');
            $semana = $lunes->format('W');
            $oficina = Oficina::where('oficina_id', auth()->user()->oficina_id)->first();
            $envios_id = Envio::whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->where('oficina_id', $oficina->oficina_id)->where('devolucion', false)->pluck('servicio_id', 'envio_id')->toArray();
            $insumos = EnvioInsumo::whereIn('envio_id', array_keys($envios_id))->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)->get();
            $inventario_final = InventarioInsumo::with('insumo')->where('oficina_id', $oficina->oficina_id)->orderBy('insumo_id')->get();
            $respaldo = $lunes->previous('Friday')->toDateString();
            $inventario_inicial = RespaldoInventarioDiario::with('insumo')->where('oficina_id', $oficina->oficina_id)
            ->orderBy('insumo_id')->whereDate('fecha', $respaldo)->get();
            $recibidos = InsumoTransferencia::whereDate('created_at', '>=', $desde)->whereDate('created_at', '<=', $hasta)
            ->where('oficina_destino', $oficina->oficina_id)->orderBy('insumo_id')->get();
            $envios_devolucion_id = Envio::whereDate('updated_at', '>=', $desde)->whereDate('updated_at', '<=', $hasta)
            ->where('oficina_id', $oficina->oficina_id)->where('devolucion', true)->pluck('servicio_id', 'envio_id')->toArray();
            $insumos_dev = EnvioInsumo::whereIn('envio_id', array_keys($envios_devolucion_id))->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)->get();


            return Excel::download(new Formato047($desde, $hasta, $semana, $oficina, $envios_id, $insumos, 
            $inventario_final, $inventario_inicial, $recibidos, $insumos_dev, $envios_devolucion_id), 'Planilla047.xlsx');
        }
    }

    public function exportExcel()
    {
        if (empty($this->registros)) {
            session()->flash('error', 'No hay datos disponibles para exportar.');
            return;
        }
    
        $data = $this->prepareDataForExport();
    
        $fecha = now()->format('Y-m-d');
        $usuario = auth()->user()->name;
        $usuario = preg_replace('/[^A-Za-z0-9_-]/', '_', $usuario); // Elimina caracteres especiales
        
        $fileName = "cierre_caja_{$fecha}_{$usuario}.xlsx";
        
        return Excel::download(new class($data) implements FromArray {
            protected $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
    
            public function array(): array
            {
                return $this->data;
            }
        }, $fileName);
    }
    

// Método que prepara los datos para exportar
// Método que prepara los datos para exportar
private function prepareDataForExport()
{
    $exportData = [];

    // Agregar encabezados
    $exportData[] = [
        'Servicio', 'Cantidad', 'Tarifa Base', 'IVA', 'Sobrante', 'Total', 'Fecha'
    ];

    // Recopilar los datos
    foreach ($this->servicios as $servicio) {
        if (isset($this->registros[$servicio->servicio_id])) {
            foreach ($this->registros[$servicio->servicio_id] as $registro) {

                // Obtener subservicios y calcular valores
                $subservicios = $this->subservicios[$servicio->servicio_id] ?? [];
                $cantidad = $this->cantidad[$servicio->servicio_id] ?? 0;

                // Asegurarse de que todos los valores estén presentes
                $envioBase = $this->envio_base[$servicio->servicio_id] ?? 0; // Valor por defecto 0 si no existe
                $iva = is_array($this->iva[$servicio->servicio_id] ?? null) 
                ? ($this->iva[$servicio->servicio_id][$registro->facturacion_id] ?? 0) 
                : ($this->iva[$servicio->servicio_id] ?? 0);
            
            $sobrante = $this->sobrante[$servicio->servicio_id] ?? 0;
            $totalPagado = is_array($this->total_pagado[$servicio->servicio_id] ?? null) 
                ? ($this->total_pagado[$servicio->servicio_id][$registro->facturacion_id] ?? 0) 
                : ($this->total_pagado[$servicio->servicio_id] ?? 0);
            
                $tarifaBase = 0;
                if (is_array($envioBase)) {
                    // Asegurarse de que la cantidad sea válida dentro del arreglo
                    $tarifaBase = $envioBase[$cantidad] ?? reset($envioBase); // Tomar el primer valor si no hay tarifa específica para la cantidad
                } else {
                    // Si no es un arreglo, simplemente asignar el valor directamente
                    $tarifaBase = $envioBase;
                }

                // Si no hay subservicios, agregar solo la fila
                if (empty($subservicios)) {
                    $exportData[] = [
                        'Servicio' => $servicio->nombre,
                        'Cantidad' => $cantidad,
                        'Tarifa Base' => $tarifaBase . " Bs", // Añadir "Bs" a la tarifa base
                        'IVA' => $iva . " Bs", // Añadir "Bs" al valor del IVA
                        'Sobrante' => $sobrante . " Bs", // Añadir "Bs" al sobrante
                        'Total' => $totalPagado . " Bs", // Añadir "Bs" al total
                        'Fecha' => $registro->created_at->format('Y-m-d H:i:s'),
                    ];
                }

                // Si hay subservicios, agregar filas adicionales
                foreach ($subservicios as $subservicioNombre => $montoSubservicio) {
                    $exportData[] = [
                        'Servicio' => $servicio->nombre . " - " . $subservicioNombre,
                        'Cantidad' => $cantidad,
                        'Tarifa Base' => $tarifaBase . " Bs",
                        'IVA' => $iva . " Bs",
                        'Sobrante' => $sobrante . " Bs",
                        'Total' => $totalPagado . " Bs",
                        'Fecha' => $registro->created_at->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }
    }

    // Verificar que no esté vacío
    if (empty($exportData)) {
        throw new \Exception('No hay datos disponibles para exportar.');
    }

    return $exportData;
}





}
