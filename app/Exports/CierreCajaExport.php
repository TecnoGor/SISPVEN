<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CierreCajaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $oficina_id;
    protected $usuario_id;
    protected $desde;
    protected $hasta;
    protected $registros;
    protected $cantidad;
    protected $total_pagar;
    protected $total_pagado;
    protected $subservicios;
    protected $metodos_pago;

    public function __construct($oficina_id, $usuario_id, $desde, $hasta)
    {
        $this->oficina_id = $oficina_id;
        $this->usuario_id = $usuario_id;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->registros = app('App\Http\Controllers\CierreCajaGeneralController')->getRegistros($oficina_id, $usuario_id, $desde, $hasta); // Asumiendo que tienes un método para obtener los registros
        $this->cantidad = app('App\Http\Controllers\CierreCajaGeneralController')->getCantidad($oficina_id, $usuario_id, $desde, $hasta);
        $this->total_pagar = app('App\Http\Controllers\CierreCajaGeneralController')->getTotalPagar($oficina_id, $usuario_id, $desde, $hasta);
        $this->total_pagado = app('App\Http\Controllers\CierreCajaGeneralController')->getTotalPagado($oficina_id, $usuario_id, $desde, $hasta);
        $this->subservicios = app('App\Http\Controllers\CierreCajaGeneralController')->getSubservicios($oficina_id, $usuario_id, $desde, $hasta);
        $this->metodos_pago = app('App\Http\Controllers\CierreCajaGeneralController')->getMetodosPago($oficina_id, $usuario_id, $desde, $hasta);
    }

    public function collection()
    {
        // Reemplaza este código con la colección completa de datos que necesitas
        $data = [];
        foreach ($this->registros as $servicioId => $registros) {
            foreach ($registros as $registro) {
                $data[] = [
                    'servicio' => $registro->servicio->nombre,
                    'cantidad' => $this->cantidad[$servicioId],
                    'total_pagar' => $this->total_pagar[$servicioId],
                    'total_pagado' => $this->total_pagado[$servicioId],
                    'subservicios' => $this->subservicios[$servicioId],
                    'metodos_pago' => $this->metodos_pago,
                    'fecha' => $registro->created_at->format('Y-m-d'),
                    // Agrega más columnas según necesites
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Servicio',
            'Cantidad',
            'Total a Pagar',
            'Total Pagado',
            'Subservicios',
            'Métodos de Pago',
            'Fecha',
            // Agrega más encabezados según sea necesario
        ];
    }

    public function map($row): array
    {
        return [
            $row['servicio'],
            $row['cantidad'],
            $row['total_pagar'],
            $row['total_pagado'],
            $row['subservicios'],
            implode(', ', $row['metodos_pago']),
            $row['fecha'],
            // Mapea más columnas según sea necesario
        ];
    }
}

