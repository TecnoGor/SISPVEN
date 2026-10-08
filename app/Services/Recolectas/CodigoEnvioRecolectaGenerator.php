<?php

namespace App\Services\Recolectas;

use App\Models\Envio;
use App\Models\Oficina;

/**
 * Genera el código de envío al convertir una recolecta en envío real.
 * Réplica exacta de App\Livewire\Iposplus\Iposplus::generarCodigoEnvio()
 * y generarCorrelativoSimplificado() (que NO se modifican), incluido el
 * mapa de excepciones de estados sin COP propia (2 y 24 usan la del
 * estado 1; 20 usa la del 21).
 */
class CodigoEnvioRecolectaGenerator
{
    public function generar(Oficina $oficinaOrigen, int $estadoDestId): string
    {
        $cod_origen = $oficinaOrigen->codigo;

        if ($estadoDestId == 2 || $estadoDestId == 24) {
            $office_dest = Oficina::where('estado_id', 1)->where('tipo_oficina_id', 4)->where('externa', false)->first();
        } elseif ($estadoDestId == 20) {
            $office_dest = Oficina::where('estado_id', 21)->where('tipo_oficina_id', 4)->where('externa', false)->first();
        } else {
            $office_dest = Oficina::where('estado_id', $estadoDestId)
                ->where('tipo_oficina_id', 4)
                ->where('externa', false)
                ->first();
        }

        if (!$office_dest) {
            // Sin oficina destino → correlativo simplificado
            return $cod_origen . $this->generarCorrelativoSimplificado() . 'SO';
        }

        $cod_destino = $office_dest->codigo;

        if (!preg_match('/^(OP|CP|CO|CI|EX)\d{3}$/', $cod_destino)) {
            $estado_nombre = $office_dest->estado->nombre ?? 'Desconocido';
            throw new \Exception("La oficina '{$office_dest->nombre}' (ID: {$office_dest->oficina_id}) del estado {$estado_nombre} tiene un código inválido ({$cod_destino}).");
        }

        $envio_existente = Envio::where('codigo_envio', 'like', $cod_origen . $cod_destino . '%')
            ->orderBy('envio_id', 'desc')
            ->first();

        if ($envio_existente) {
            preg_match('/' . preg_quote($cod_origen) . preg_quote($cod_destino) . '(\d+)(?=\b)/', $envio_existente->codigo_envio, $matches);
            $ultimo_correlativo = isset($matches[1]) ? (int) $matches[1] : 0;
            $nuevo_correlativo = str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo = '000000001';
        }

        return $cod_origen . $cod_destino . $nuevo_correlativo;
    }

    private function generarCorrelativoSimplificado(): string
    {
        $envio_existente = Envio::orderBy('envio_id', 'desc')->first();

        if ($envio_existente) {
            preg_match('/\d{9}/', $envio_existente->codigo_envio, $matches);
            $ultimo_correlativo = isset($matches[0]) ? (int) $matches[0] : 0;
        } else {
            $ultimo_correlativo = 0;
        }

        return str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);
    }
}
