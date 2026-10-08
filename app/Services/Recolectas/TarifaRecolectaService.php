<?php

namespace App\Services\Recolectas;

use App\Models\Oficina;
use App\Models\Parametro;
use App\Models\TarifaIposplus;

/**
 * Cotiza una recolecta Iposplus: tarifa del envío (réplica exacta del
 * cálculo de App\Livewire\Iposplus\Iposplus::CalcularPrecio() y
 * recalcularDesdeDimensiones(), que NO se modifica) más la tarifa fija
 * de recolección (parámetro "Recoleccion IposPlus").
 *
 * Los montos se truncan con bcdiv (no se redondean), igual que en taquilla.
 */
class TarifaRecolectaService
{
    public const MODO_MANUAL = 'manual';
    public const MODO_VOLUMETRICO = 'volumetrico';

    /** Divisor del peso volumétrico usado por taquilla (alto*ancho*largo / 5000). */
    private const DIVISOR_VOLUMETRICO = '5000';

    /**
     * @param  string      $modoPeso  'manual' | 'volumetrico'
     * @param  float|null  $peso      kg (solo modo manual)
     * @param  Oficina     $oficinaRecolectora  define la exención de IVA (zona económica especial)
     * @return array{
     *     modo_peso: string,
     *     peso_facturable: string,
     *     peso_volumetrico: string|null,
     *     monto_envio: string,
     *     monto_recoleccion: string,
     *     base: string,
     *     iva: string,
     *     total: string,
     *     tasa_bs: float|null,
     *     excede_tarifa_max: bool,
     * }
     *
     * @throws SinTarifaDisponibleException si no hay rango de tarifa activo aplicable
     */
    public function cotizar(
        string $modoPeso,
        ?float $peso,
        ?float $alto,
        ?float $ancho,
        ?float $largo,
        Oficina $oficinaRecolectora
    ): array {
        $pesoVolumetrico = null;

        if ($modoPeso === self::MODO_VOLUMETRICO) {
            // Réplica de recalcularDesdeDimensiones(): truncar a 3 decimales sin redondear
            if ($alto > 0 && $ancho > 0 && $largo > 0) {
                $pesoVolumetrico = bcdiv((string) ((float) $alto * (float) $ancho * (float) $largo), self::DIVISOR_VOLUMETRICO, 3);
                $peso = (float) $pesoVolumetrico;
            } else {
                $peso = 0.0;
            }
        }

        $peso = (float) $peso;

        if ($peso <= 0) {
            throw new SinTarifaDisponibleException('El peso del envío debe ser mayor a 0.');
        }

        $kiloMaxTarifa = (float) (TarifaIposplus::where('activo', true)->max('kilo_max') ?? 0);
        $excedeTarifaMax = $kiloMaxTarifa > 0 && $peso > $kiloMaxTarifa;

        if ($excedeTarifaMax) {
            $tarifa = TarifaIposplus::where('activo', true)
                ->orderBy('kilo_max', 'desc')
                ->pluck('precio')
                ->first();
        } else {
            $tarifa = TarifaIposplus::where('activo', true)
                ->where('kilo_min', '<=', $peso)
                ->where('kilo_max', '>=', $peso)
                ->orderBy('tarifa_iposplus_id', 'asc')
                ->pluck('precio')
                ->first();
        }

        if (!$tarifa) {
            throw new SinTarifaDisponibleException('No hay una tarifa Iposplus activa para el peso indicado.');
        }

        $cambio = $this->valorParametro(config('recolectas.parametro_iposplus'));
        $montoEnvio = bcdiv((string) ($tarifa * $cambio), '1', 2);

        $montoRecoleccion = bcdiv((string) $this->valorParametro(config('recolectas.parametro_recoleccion')), '1', 2);

        $base = bcadd($montoEnvio, $montoRecoleccion, 2);

        if (!$oficinaRecolectora->zona_economica_especial) {
            $iva = bcdiv((string) ((float) $base * 0.16), '1', 2);
        } else {
            $iva = '0.00';
        }

        $total = bcdiv((string) ((float) $base + (float) $iva), '1', 2);

        $tasaBs = Parametro::where('nombre', config('recolectas.parametro_tasa_bs'))->value('valor');

        return [
            'modo_peso' => $modoPeso,
            'peso_facturable' => bcdiv((string) $peso, '1', 3),
            'peso_volumetrico' => $pesoVolumetrico,
            'monto_envio' => $montoEnvio,
            'monto_recoleccion' => $montoRecoleccion,
            'base' => $base,
            'iva' => $iva,
            'total' => $total,
            'tasa_bs' => $tasaBs !== null ? (float) $tasaBs : null,
            'excede_tarifa_max' => $excedeTarifaMax,
        ];
    }

    private function valorParametro(string $nombre): float
    {
        $valor = Parametro::where('nombre', $nombre)->value('valor');

        if ($valor === null) {
            throw new \RuntimeException(
                "El parámetro de tarifa '{$nombre}' no existe en la tabla parametro. " .
                'Ejecute: php artisan db:seed --class=RecolectaParametroSeeder'
            );
        }

        return (float) $valor;
    }
}
