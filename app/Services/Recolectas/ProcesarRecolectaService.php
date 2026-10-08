<?php

namespace App\Services\Recolectas;

use App\Models\Cliente;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioIposplus;
use App\Models\Facturacion;
use App\Models\FacturacionDetalle;
use App\Models\FacturacionEnvio;
use App\Models\FacturacionPago;
use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Models\RecolectaPago;
use App\Models\User;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;

/**
 * Convierte una recolecta pagada en un envío Iposplus real cuando la
 * oficina recibe/ingresa el paquete. Réplica de la transacción de
 * App\Livewire\Iposplus\Iposplus::finalizarLote() para UNA recolecta:
 * Cliente(s) + Envio + EnvioIposplus + EnvioEncaminamiento (estatus 1) +
 * EnvioAlmacen + Facturacion/Detalle/Pago/FacturacionEnvio.
 *
 * Sin encaminamiento y facturación el envío quedaría fuera del circuito
 * operativo y contable (precedente de los telegramas APP-): por eso todo
 * se crea aquí, en la misma transacción.
 */
class ProcesarRecolectaService
{
    /** Estatus inicial del tracking postal: "Envío en Proceso". */
    private const ESTATUS_ENVIO_EN_PROCESO = 1;

    public function __construct(
        private CodigoEnvioRecolectaGenerator $codigos,
        private RecolectaFlowService $flow,
    ) {}

    public function procesar(Recolecta $recolecta, User $usuarioWeb): Envio
    {
        if (!$recolecta->tieneEstatus(RecolectaEstatus::PAGO_CONFIRMADO)) {
            throw new TransicionRecolectaInvalidaException(
                'La recolecta no puede procesarse: el pago aún no está confirmado.'
            );
        }

        $envio = DB::transaction(function () use ($recolecta, $usuarioWeb) {
            $this->crearClienteSiNoExiste(
                $recolecta->documento_rem,
                $recolecta->nombre_rem,
                $recolecta->apellido_rem,
                $recolecta->tipo_documento_rem,
                $recolecta->telefono_rem,
                $recolecta->correo_rem
            );
            $this->crearClienteSiNoExiste(
                $recolecta->documento_dest,
                $recolecta->nombre_dest,
                $recolecta->apellido_dest,
                $recolecta->tipo_documento_dest,
                $recolecta->telefono_dest,
                $recolecta->correo_dest
            );

            $codigoEnvio = $this->codigos->generar($recolecta->oficina, (int) $recolecta->estado_dest_id);

            $baseSinIva = bcadd((string) $recolecta->monto_envio, (string) $recolecta->monto_recoleccion, 2);

            $envio = Envio::create([
                'servicio_id' => config('recolectas.servicio_iposplus_id'),
                'tipo_envio' => 'nacional',
                'oficina_id' => $recolecta->oficina_id,
                'usuario_id' => $usuarioWeb->id,
                'nombre_rem' => $recolecta->nombre_rem,
                'apellido_rem' => $recolecta->apellido_rem,
                'tipo_documento_rem' => $recolecta->tipo_documento_rem,
                'documento_rem' => $recolecta->documento_rem,
                // El bloque _rem se guarda como texto (ids de catálogo), igual
                // que hace la web con el origen copiado de la oficina.
                'codigo_postal_rem' => $recolecta->codigo_postal,
                'estado_rem' => (string) $recolecta->estado_id,
                'municipio_rem' => (string) $recolecta->municipio_id,
                'parroquia_rem' => (string) $recolecta->parroquia_id,
                'ciudad_rem' => (string) $recolecta->ciudad_id,
                'direccion_rem' => $recolecta->direccion,
                'correo_rem' => $recolecta->correo_rem,
                'telefono_rem' => $recolecta->telefono_rem,
                'nombre_dest' => $recolecta->nombre_dest,
                'apellido_dest' => $recolecta->apellido_dest,
                'tipo_documento_dest' => $recolecta->tipo_documento_dest,
                'documento_dest' => $recolecta->documento_dest,
                'codigo_postal_dest' => $recolecta->codigo_postal_dest,
                'continente_dest' => null,
                'pais_dest' => null,
                'estado_dest' => $recolecta->estado_dest_id,
                'municipio_dest' => $recolecta->municipio_dest_id,
                'parroquia_dest' => $recolecta->parroquia_dest_id,
                'ciudad_dest' => $recolecta->ciudad_dest_id,
                'direccion_dest' => $recolecta->direccion_dest,
                'tlf_dest' => $recolecta->telefono_dest,
                'correo_dest' => $recolecta->correo_dest,
                'servicio_expreso' => null,
                'peso' => $recolecta->peso * 1000, // kg → gramos, paridad taquilla
                'coste' => $recolecta->total,
                'contenido' => $recolecta->contenido,
                'apartado_postal' => null,
                'codigo_envio' => $codigoEnvio,
                'devolucion' => false,
                'descubierto' => false,
                'tipo_saca_id' => config('recolectas.tipo_saca_iposplus_id'),
                'coste_sin_iva' => $baseSinIva,
                'tasa_bs' => $recolecta->tasa_bs,
            ]);

            EnvioIposplus::create([
                'envio_id' => $envio->envio_id,
                'alto' => $recolecta->alto ?? 0,
                'largo' => $recolecta->largo ?? 0,
                'ancho' => $recolecta->ancho ?? 0,
            ]);

            EnvioEncaminamiento::create([
                'envio_id' => $envio->envio_id,
                'oficina_id' => $envio->oficina_id,
                'usuario_id' => $usuarioWeb->id,
                'estatus_id' => self::ESTATUS_ENVIO_EN_PROCESO,
                'devolucion' => false,
            ]);

            EnvioAlmacen::create([
                'oficina_id' => $recolecta->oficina_id,
                'envio_id' => $envio->envio_id,
                'codigo' => $envio->codigo_envio,
                'saca_id' => null,
                'estatus' => true,
                'Entrada' => now()->toDateTimeString(),
            ]);

            $facturacion = Facturacion::create([
                'oficina_id' => $recolecta->oficina_id,
                'nombre' => $recolecta->nombre_rem,
                'apellido' => $recolecta->apellido_rem,
                'tipo_documento' => $recolecta->tipo_documento_rem,
                'documento' => $recolecta->documento_rem,
                'direccion' => $recolecta->direccion,
                'monto_total' => $recolecta->total,
                'iva' => $recolecta->iva,
            ]);

            FacturacionDetalle::create([
                'servicio_id' => $envio->servicio_id,
                'facturacion_id' => $facturacion->facturacion_id,
                'monto' => $recolecta->total,
            ]);

            foreach ($recolecta->pagos()->where('estatus', RecolectaPago::ESTATUS_CONFIRMADO)->get() as $pago) {
                FacturacionPago::create([
                    'facturacion_id' => $facturacion->facturacion_id,
                    'tipo_pago_id' => $pago->tipo_pago_id,
                    'monto' => $pago->monto,
                    'numero_referencia' => $pago->numero_referencia,
                ]);
            }

            FacturacionEnvio::create([
                'facturacion_id' => $facturacion->facturacion_id,
                'envio_id' => $envio->envio_id,
                'oficina_id' => $recolecta->oficina_id,
                'usuario_id' => $usuarioWeb->id,
                'servicio_id' => $envio->servicio_id,
            ]);

            $recolecta->envio_id = $envio->envio_id;
            $recolecta->save();
            $this->flow->transicionar($recolecta, RecolectaEstatus::RECOLECTADA);

            return $envio;
        });

        UsuarioSeguimiento::create([
            'usuario_id' => $usuarioWeb->id,
            'accion' => 'create',
            'descripcion' => "Se procesó la recolecta {$recolecta->codigo} generando el envío {$envio->codigo_envio}",
        ]);

        return $envio;
    }

    private function crearClienteSiNoExiste(
        string $documento,
        string $nombre,
        string $apellido,
        string $tipoDocumento,
        string $telefono,
        string $correo
    ): void {
        if (!Cliente::where('numero_documento', $documento)->exists()) {
            Cliente::create([
                'numero_documento' => $documento,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'tipo_documento' => $tipoDocumento,
                'telefono' => $telefono,
                'correo' => $correo,
            ]);
        }
    }
}
