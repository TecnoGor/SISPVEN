<?php

namespace App\Livewire\Manifiestos;

use App\Models\Manifiesto;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Guia de despacho HISTORICA, armada desde el manifiesto.
 *
 * Los despachos anteriores al 23-jun-2026 no tienen `oficina_destino_id`: en ese
 * modelo el correlativo era por oficina de origen y un mismo despacho acumulaba
 * valijas hacia rutas distintas. No hay forma de reconstruir su guia desde
 * `sacas` -- saldria mezclando envios que nunca viajaron juntos.
 *
 * La informacion real de esas salidas vive en `manifiestos`: un manifiesto es
 * una salida fisica concreta (un origen, un destino, una fecha) y su contenido
 * quedo congelado al despachar, con el peso y el servicio de cada bulto.
 *
 * Es de SOLO LECTURA: no hay despacho que cerrar, no se asocian sueltos y no
 * existe boton de confirmar. Por eso es un componente aparte y no un modo de
 * PaquetesManifiestos. Ver docs/notas/plan-guias-historicas-por-manifiesto.md
 */
#[Layout('layouts.app')]
class GuiaManifiesto extends Component
{
    public $manifiesto_id;

    public function render()
    {
        $manifiesto = Manifiesto::with([
            'oficina',
            'oficinaDestino',
            // El peso y el servicio se leen de la fila del manifiesto, no del
            // envio o la valija actuales: son la foto del momento del despacho.
            'paquetes.saca',
            'paquetes.envio',
            'paquetes.servicio',
        ])->findOrFail($this->manifiesto_id);

        // Una fila por bulto, ya resuelta para que la vista solo itere.
        $bultos = $manifiesto->paquetes->map(function ($paquete) {
            $esValija = $paquete->saca_id !== null;

            return [
                'tipo'     => $esValija ? 'Valija' : 'Envío',
                'codigo'   => $esValija
                    ? ($paquete->saca->codigo_saca ?? 'N/A')
                    : ($paquete->envio->codigo_envio ?? 'N/A'),
                'precinto' => $esValija
                    ? ($paquete->saca->numero_precinto ?? 'N/A')
                    : 'Al descubierto',
                'servicio' => $paquete->servicio->nombre ?? 'N/A',
                'peso'     => $paquete->peso,
            ];
        });

        return view('livewire.manifiestos.guia-manifiesto', [
            'manifiesto'   => $manifiesto,
            'bultos'       => $bultos,
            'totalBultos'  => $bultos->count(),
            'pesoTotal'    => $manifiesto->paquetes->sum('peso'),
        ]);
    }
}