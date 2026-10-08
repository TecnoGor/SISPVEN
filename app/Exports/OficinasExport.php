<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OficinasExport implements FromCollection, WithStyles, WithColumnWidths, WithEvents
{
    const COLOR_PRIMARY    = '6B1820';
    const COLOR_PRIMARY_DK = '4A1016';
    const COLOR_ROW_ALT    = 'FAF5F5';
    const COLOR_BORDER     = 'E5E0E0';
    const COLOR_META_BG    = 'F8F4F4';
    const COLOR_META_TEXT  = '6B7280';
    const COLOR_TEXT_DARK  = '374151';

    // Filas reservadas antes de los datos
    const ROW_TITLE   = 1;
    const ROW_META    = 2;
    const ROW_GAP     = 3;
    const ROW_HEADER  = 4;
    const ROW_DATA    = 5; // datos empiezan aquí

    protected $oficinas;
    protected $usuario;
    protected $fecha;
    protected $gerentesPorEstado = [];

    public function __construct($oficinas)
    {
        $this->oficinas = $oficinas;
        $this->usuario  = auth()->user()->name ?? 'Sistema';
        $this->fecha    = Carbon::now()->format('d/m/Y H:i');
    }

    public function collection()
    {
        return $this->oficinas->map(fn($o) => [
            $o->nombre,
            $o->codigo,
            $o->tipo_oficina ? $o->tipo_oficina->nombre : '—',
            $this->resolverJefe($o),
            $o->direccion ?? '—',
            $o->estado    ? $o->estado->nombre    : '—',
            $o->municipio ? $o->municipio->nombre : '—',
            $o->parroquia ? $o->parroquia->nombre : '—',
            $o->estatus   ? $o->estatus->estatus  : '—',
        ]);
    }

    protected function resolverJefe($oficina): string
    {
        $tipo = $oficina->tipo_oficina_id;

        if (in_array($tipo, [1, 2, 3])) {
            $jefe = User::where('oficina_id', $oficina->oficina_id)
                ->role('Jefe de OPT')
                ->first();
            return $jefe ? $jefe->name : 'Sin asignar';
        }

        if ($tipo == 4) {
            $estadoId = $oficina->estado_id;

            if (!$estadoId && $oficina->oficina_relacionada_id) {
                $rel = \App\Models\Oficina::find($oficina->oficina_relacionada_id);
                $estadoId = $rel->estado_id ?? null;
            }

            return $estadoId ? $this->gerentePorEstado($estadoId) : 'Sin asignar';
        }

        return '—';
    }

    protected function gerentePorEstado($estadoId): string
    {
        $gruposInversos = [
            2  => 1,
            24 => 1,
            20 => 21,
        ];

        $clave = $gruposInversos[$estadoId] ?? $estadoId;

        if (array_key_exists($clave, $this->gerentesPorEstado)) {
            return $this->gerentesPorEstado[$clave];
        }

        $userId = DB::table('usuario_estados')->where('id_estado', $clave)->value('id_user');

        if (!$userId) {
            return $this->gerentesPorEstado[$clave] = 'Sin asignar';
        }

        $user   = User::find($userId);
        $nombre = ($user && $user->hasRole('Gerente de Estado')) ? $user->name : 'Sin asignar';

        return $this->gerentesPorEstado[$clave] = $nombre;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32,
            'B' => 12,
            'C' => 20,
            'D' => 28,
            'E' => 42,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 14,
        ];
    }

    public function styles($sheet)
    {
        // Sin datos no hay nada que estilizar aquí; todo va en AfterSheet
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $count    = $this->oficinas->count();
                $lastData = self::ROW_DATA + $count - 1;   // última fila de datos
                $totalRow = $lastData + 1;                  // fila de total

                // ── Insertar 4 filas al inicio (los datos llegaron en fila 1) ──
                $sheet->insertNewRowBefore(1, self::ROW_DATA - 1); // inserta filas 1-4

                // ── Fila 1: Título ────────────────────────────────────────────
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'REPORTE DE OFICINAS · IPOSTEL');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                        'name'  => 'Calibri',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::COLOR_PRIMARY],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // ── Fila 2: Meta (usuario | fecha) ───────────────────────────
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', '  Generado por: ' . $this->usuario);
                $sheet->mergeCells('F2:I2');
                $sheet->setCellValue('F2', 'Fecha: ' . $this->fecha . '  ');

                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => [
                        'size'  => 9,
                        'color' => ['rgb' => self::COLOR_META_TEXT],
                        'name'  => 'Calibri',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::COLOR_META_BG],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => self::COLOR_BORDER],
                        ],
                    ],
                ]);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('F2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // ── Fila 3: Separador vacío ───────────────────────────────────
                $sheet->getRowDimension(3)->setRowHeight(8);

                // ── Fila 4: Encabezados de columna ───────────────────────────
                $headers = ['Nombre', 'Código', 'Tipo de Oficina', 'Jefe', 'Dirección', 'Estado', 'Municipio', 'Parroquia', 'Estatus'];
                foreach ($headers as $i => $h) {
                    $col = chr(65 + $i); // A, B, C…
                    $sheet->setCellValue("{$col}4", $h);
                }
                $sheet->getStyle('A4:I4')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 10,
                        'color' => ['rgb' => 'FFFFFF'],
                        'name'  => 'Calibri',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::COLOR_PRIMARY],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => self::COLOR_PRIMARY_DK],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(24);

                // ── Filas de datos ────────────────────────────────────────────
                if ($count > 0) {
                    $sheet->getStyle("A5:I{$lastData}")->applyFromArray([
                        'font' => [
                            'size'  => 9,
                            'name'  => 'Calibri',
                            'color' => ['rgb' => self::COLOR_TEXT_DARK],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => self::COLOR_BORDER],
                            ],
                        ],
                    ]);

                    // Centrar Código y Estatus
                    $sheet->getStyle("B5:B{$lastData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I5:I{$lastData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Filas alternas
                    for ($row = 5; $row <= $lastData; $row++) {
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:I{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB(self::COLOR_ROW_ALT);
                        }
                    }
                }

                // ── Fila total ────────────────────────────────────────────────
                $sheet->mergeCells("A{$totalRow}:H{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'Total de oficinas');
                $sheet->setCellValue("I{$totalRow}", $count);

                $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 10,
                        'color' => ['rgb' => self::COLOR_PRIMARY],
                        'name'  => 'Calibri',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::COLOR_META_BG],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => self::COLOR_PRIMARY],
                        ],
                    ],
                ]);
                $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($totalRow)->setRowHeight(22);

                // ── Freeze y gridlines ────────────────────────────────────────
                $sheet->freezePane('A5');
                $sheet->setShowGridlines(false);
            },
        ];
    }
}
