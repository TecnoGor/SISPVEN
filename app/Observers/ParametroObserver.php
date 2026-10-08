<?php

namespace App\Observers;

use App\Models\Parametro;
use App\Models\ParametroHistorico;

class ParametroObserver
{
    /**
     * Handle the Parametro "created" event.
     */
    public function created(Parametro $parametro): void
    {
        ParametroHistorico::create([
            'parametro_id'   => $parametro->parametro_id,
            'valor_anterior' => null,
            'valor_nuevo'    => $parametro->valor,
            'usuario_id'     => auth()->id(),
            'fecha_cambio'   => now(),
        ]);
    }

    /**
     * Handle the Parametro "updated" event.
     */
    public function updated(Parametro $parametro): void
    {
        if ($parametro->isDirty('valor')) {
            ParametroHistorico::create([
                'parametro_id'   => $parametro->parametro_id,
                'valor_anterior' => $parametro->getOriginal('valor'),
                'valor_nuevo'    => $parametro->valor,
                'usuario_id'     => auth()->id(),
                'fecha_cambio'   => now(),
            ]);
        }
    }

    /**
     * Handle the Parametro "deleted" event.
     */
    public function deleted(Parametro $parametro): void
    {
        //
    }

    /**
     * Handle the Parametro "restored" event.
     */
    public function restored(Parametro $parametro): void
    {
        //
    }

    /**
     * Handle the Parametro "force deleted" event.
     */
    public function forceDeleted(Parametro $parametro): void
    {
        //
    }
}
