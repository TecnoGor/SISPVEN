<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Envio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ConfirmacionTelegramaController extends Controller
{
    public function reportePdf(Request $request)
    {
        $user = Auth::user();

        // Recoger parámetros con valores por defecto
        $seccion = $request->query('seccion', 1);
        $desde   = $request->query('desde', null);
        $hasta   = $request->query('hasta', null);
        $search  = $request->query('search', null);

        // Construir query igual que en tu Livewire
        if ($seccion == 1) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_dest_id', $user->oficina_id);
        } elseif ($seccion == 2) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_id', $user->oficina_id);
        } else {
            $telegramas = collect();
            $pdf = Pdf::loadView('pdf.telegramas', compact('telegramas', 'seccion'));
            return $pdf->stream('reporte_telegramas.pdf');
        }

        $telegramas = $query
            ->when($desde, function ($q) use ($desde) {
                $q->whereDate('created_at', '>=', $desde);
            })
            ->when($hasta, function ($q) use ($hasta) {
                $q->whereDate('created_at', '<=', $hasta);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subquery) use ($search) {
                    $subquery->where('documento_rem', 'like', '%' . $search . '%')
                             ->orWhere('documento_dest', 'like', '%' . $search . '%')
                             ->orWhere('codigo_envio', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Generar PDF con la misma vista que ya usas
        $pdf = Pdf::loadView('pdf.telegramas', compact('telegramas', 'seccion'));

        // stream() devuelve el PDF listo para mostrarse en el navegador (inline)
        return $pdf->stream('reporte_telegramas.pdf');
    }

    public function envioPdf($envioId)
    {
        $envio = Envio::with(['oficinas', 'telegrama_recibido'])->findOrFail($envioId);

        $telegramaRecibido = $envio->telegrama_recibido->first();

        // Generar PDF usando la misma vista confirmacion-telegrama.blade.php
        $pdf = Pdf::loadView('pdf.confirmacion-telegrama', compact('envio', 'telegramaRecibido'));

        return $pdf->stream("telegrama_{$envio->envio_id}.pdf");
    }
}