<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ReciboConsignacionController extends Controller
{
    public function pdf(int $envioId)
    {
        $envio = Envio::with(['servicio', 'users', 'oficinaOrigen'])->findOrFail($envioId);
        $barcodeFilePath = null;
        $codigo = (string)($envio->codigo_envio ?? '');
        if ($codigo !== '') {
            $pngGen = new BarcodeGeneratorPNG();
            $barcodeData = $pngGen->getBarcode($codigo, $pngGen::TYPE_CODE_128, 2, 60);
            $dir = storage_path('app/barcodes');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $filename = preg_replace('/[^A-Za-z0-9_-]/', '_', $codigo) . '.png';
            $fullpath = $dir . DIRECTORY_SEPARATOR . $filename;
            @file_put_contents($fullpath, $barcodeData);
            $barcodeFilePath = $fullpath; // absolute path
        }

        $isExpreso = $envio->servicio->servicio_id;
        if ($isExpreso == 21) {
            $viewPdf = 'pdf.recibo-consignacion-ems';
        } else {
            $viewPdf = 'pdf.recibo-consignacion';
        }

        $pdf = Pdf::loadView($viewPdf, [
            'envio' => $envio,
            'barcodeFilePath' => $barcodeFilePath,
        ])
            ->setPaper('a4', 'portrait'); // 210mm x 148.5mm en puntos

        $dompdf = $pdf->getDomPDF();
        $dompdf->getOptions()->set('dpi', 150);
        $dompdf->getOptions()->set('isRemoteEnabled', true);

        return $pdf->stream('recibo-consignacion.pdf');
    }

    public function preview(int $envioId)
    {
        $envio = Envio::with(['servicio', 'users', 'oficinaOrigen'])->findOrFail($envioId);
        $attributes = [
            'sidebarVariant' => null,
            'headerVariant' => null,
            'background' => null,
        ];

        $generator = new BarcodeGeneratorPNG();
        $codigo = (string)($envio->codigo_envio ?? '');
        $barcode = null;
        if ($codigo !== '') {
            $barcodeData = $generator->getBarcode($codigo, $generator::TYPE_CODE_128, 2, 60);
            $barcode = 'data:image/png;base64,' . base64_encode($barcodeData);
        }

        $viewPreview = ($envio->servicio->servicio_id ?? null) === 1
            ? 'recibo-consignacion.preview-ems'
            : 'recibo-consignacion.preview';

        return view($viewPreview, compact('envio', 'attributes', 'barcode'));
    }
}
