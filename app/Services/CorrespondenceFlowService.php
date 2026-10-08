<?php

namespace App\Services;

use App\Models\Comunicado;
use App\Models\ComunicadoDestinatario;
use App\Models\User;
use Carbon\Carbon;

class CorrespondenceFlowService
{
    /**
     * Actualiza el pivote MÁS RECIENTE del usuario a estatus 3 (Confirmación de Recibido).
     * Usa latest('id') para no afectar registros históricos cuando hay múltiples pivotes.
     */
    public function confirmarRecepcion(int $comunicadoId, int $userId): bool
    {
        $pivot = ComunicadoDestinatario::where('comunicado_id', $comunicadoId)
            ->where('usuario_id', $userId)
            ->latest('id')
            ->first();

        if (!$pivot) {
            return false;
        }

        $pivot->update(['estatus_id' => 3]);
        return true;
    }

    /**
     * Inserta un nuevo pivote reenviando el comunicado a un nuevo destinatario.
     * Registra emisor_id (quien remite) en el pivote para trazabilidad de cadena.
     *
     * @param int $estatusId  5 (Remitido) o 6 (Aprobado / Firmado)
     */
    public function remitir(int $comunicadoId, int $destinatarioId, int $remitenteId, int $estatusId): ComunicadoDestinatario
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);

        $datos = $comunicado->datos_json ?? [];
        $remitidoPor = $datos['remitido_por'] ?? [];

        $remitidoPor[] = [
            'usuario_id'   => $remitenteId,
            'user_name'    => User::find($remitenteId)?->name ?? 'Desconocido',
            'timestamp'    => Carbon::now()->toISOString(),
            'a_usuario_id' => $destinatarioId,
        ];

        $this->mergeDatosJson($comunicado, ['remitido_por' => $remitidoPor]);

        // Actualizar el estatus del remitente a 5 (Remitido) solo cuando es un forwarding real,
        // no cuando es un re-envío de corrección (estatusId = 1)
        if ($estatusId === 5 || $estatusId === 6) {
            $pivotRemitente = ComunicadoDestinatario::where('comunicado_id', $comunicadoId)
                ->where('usuario_id', $remitenteId)
                ->latest('id')
                ->first();

            if ($pivotRemitente) {
                $pivotRemitente->update(['estatus_id' => $estatusId]);
            }
        }

        // Se envía siempre como Pendiente (estatus = 1) y el motivo será el $estatusId original (ej: 5 para Remitido)
        return $this->createPivot($comunicadoId, $destinatarioId, 1, $remitenteId, null, $estatusId);
    }

    /**
     * Verifica que el destinatario_final_id guardado en datos_json exista como usuario activo.
     * Lanza excepción si no existe o no está en el sistema.
     */
    public function verificarDestinatarioFinal(int $comunicadoId): User
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);
        $datos = $comunicado->datos_json ?? [];

        $finalId = $datos['destinatario_final_id'] ?? null;

        if (!$finalId) {
            throw new \RuntimeException('Este documento no tiene un destinatario final asignado. No se puede firmar.');
        }

        $user = User::find($finalId);

        if (!$user) {
            throw new \RuntimeException('El destinatario final seleccionado ya no existe en el sistema. Debe corregir el documento.');
        }

        return $user;
    }

    /**
     * Aprueba y firma el comunicado.
     *
     * Determina automáticamente al Director destinatario usando el emisor_id
     * del pivote más reciente del Presidente (quien le envió el documento).
     * Si emisor_id es NULL, cae al autor original (comunicados.usuario_id).
     *
     * Inyecta metadata de firma en datos_json e inserta pivote estatus 6 hacia el director.
     */
    public function aprobarYFirmar(int $comunicadoId, int $presidenteId): bool
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);
        $presidente = User::findOrFail($presidenteId);

        // Solo actualizar el estatus del pivote del presidente a 6 (Aprobado/Firmado)
        // No se envía ni devuelve a nadie
        $pivotPresidente = ComunicadoDestinatario::where('comunicado_id', $comunicadoId)
            ->where('usuario_id', $presidenteId)
            ->latest('id')
            ->first();

        if (!$pivotPresidente) {
            throw new \RuntimeException('No se encontró el registro del Presidente para este documento.');
        }

        $pivotPresidente->update(['estatus_id' => 6]);

        $this->mergeDatosJson($comunicado, [
            'firmado'        => true,
            'firmado_por'    => $presidente->name,
            'firmado_por_id' => $presidenteId,
            'fecha_firma'    => Carbon::now()->toDateString(),
        ]);

        return true;
    }

    /**
     * Tras firmar, envía el comunicado al destinatario_final_id registrado en datos_json.
     * Crea pivote nuevo con estatus 1 (Pendiente) y motivo 6 (Aprobado/Firmado).
     */
    public function enviarAlDestinatarioFinal(int $comunicadoId, int $firmadorId): bool
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);
        $datos = $comunicado->datos_json ?? [];
        $finalId = $datos['destinatario_final_id'] ?? null;

        if (!$finalId) {
            throw new \RuntimeException('Este documento no tiene un destinatario final asignado.');
        }

        if (!User::find($finalId)) {
            throw new \RuntimeException('El destinatario final ya no existe en el sistema.');
        }

        $this->createPivot($comunicadoId, (int) $finalId, 1, $firmadorId, null, 6);

        $this->mergeDatosJson($comunicado, ['enviado_a_final' => true]);

        return true;
    }

    /**
     * Envía el comunicado a un userId específico (usado para circulares con múltiples destinatarios).
     * No modifica datos_json['enviado_a_final'] — el llamador lo hace tras el bucle completo.
     */
    public function enviarAlDestinatarioFinalId(int $comunicadoId, int $destinatarioId, int $firmadorId): void
    {
        if (!User::find($destinatarioId)) {
            return;
        }
        $this->createPivot($comunicadoId, $destinatarioId, 1, $firmadorId, null, 6);
    }

    /**
     * Expone mergeDatosJson públicamente para casos puntuales (ej: circular multi-envío).
     */
    public function mergeDatosJsonPublic(Comunicado $comunicado, array $patch): Comunicado
    {
        return $this->mergeDatosJson($comunicado, $patch);
    }

    /**
     * Delega instrucción a cualquier usuario.
     * Inyecta metadata de delegación e inserta pivote estatus 5.
     */
    public function delegar(int $comunicadoId, int $destinatarioId, int $remitenteId): ComunicadoDestinatario
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);

        $this->mergeDatosJson($comunicado, [
            'delegado_por_id'     => $remitenteId,
            'delegado_por_nombre' => User::find($remitenteId)?->name ?? 'Desconocido',
            'fecha_delegacion'    => Carbon::now()->toISOString(),
        ]);

        return $this->createPivot($comunicadoId, $destinatarioId, 1, $remitenteId, null, 5);
    }

    /**
     * Archiva el comunicado inyectando flag en datos_json.
     * No crea registro pivote — el PDF se genera al vuelo con estos datos.
     */
    public function archivar(int $comunicadoId, int $presidenteId): bool
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);
        $presidente = User::findOrFail($presidenteId);

        // Actualizar el estatus del pivote del presidente a 8 (Rechazado / Archivado)
        $pivotPresidente = ComunicadoDestinatario::where('comunicado_id', $comunicadoId)
            ->where('usuario_id', $presidenteId)
            ->latest('id')
            ->first();

        if (!$pivotPresidente) {
            throw new \RuntimeException('No se encontró el registro del Presidente para este documento.');
        }

        $pivotPresidente->update(['estatus_id' => 8]);

        $this->mergeDatosJson($comunicado, [
            'archivado'        => true,
            'archivado_por'    => $presidente->name,
            'archivado_por_id' => $presidenteId,
            'fecha_archivo'    => Carbon::now()->toDateString(),
        ]);

        return true;
    }

    /**
     * Devuelve un comunicado para corrección al paso anterior de la cadena.
     * Crea pivote con estatus 7 (Devuelto para corregir) y observación obligatoria.
     */
    public function devolver(int $comunicadoId, int $destinatarioId, int $remitenteId, string $observacion): ComunicadoDestinatario
    {
        $comunicado = Comunicado::findOrFail($comunicadoId);

        $datos = $comunicado->datos_json ?? [];
        $devueltoPor = $datos['devuelto_por'] ?? [];

        $devueltoPor[] = [
            'usuario_id'   => $remitenteId,
            'user_name'    => User::find($remitenteId)?->name ?? 'Desconocido',
            'timestamp'    => Carbon::now()->toISOString(),
            'a_usuario_id' => $destinatarioId,
            'observacion'  => $observacion,
        ];

        $this->mergeDatosJson($comunicado, ['devuelto_por' => $devueltoPor]);

        // Actualizar el estatus del remitente a 7 (Devuelto para corregir) en su pivote más reciente
        $pivotRemitente = ComunicadoDestinatario::where('comunicado_id', $comunicadoId)
            ->where('usuario_id', $remitenteId)
            ->latest('id')
            ->first();

        if ($pivotRemitente) {
            $pivotRemitente->update(['estatus_id' => 7]);
        }

        // Se envía siempre como Pendiente (estatus = 1) y el motivo será 7 (Devuelto)
        return $this->createPivot($comunicadoId, $destinatarioId, 1, $remitenteId, $observacion, 7);
    }

    // ── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Merge $patch en datos_json del comunicado.
     * Las claves con valor null se eliminan del JSON resultante.
     */
    private function mergeDatosJson(Comunicado $comunicado, array $patch): Comunicado
    {
        $current = $comunicado->datos_json ?? [];
        $merged  = array_merge($current, $patch);

        // Eliminar claves null para mantener datos_json limpio
        $merged = array_filter($merged, fn($v) => !\is_null($v));

        $comunicado->datos_json = $merged;
        $comunicado->save();

        return $comunicado;
    }

    /**
     * Crea e inserta un nuevo registro en comunicado_destinatario.
     * $emisorId registra quién pasó el documento en este movimiento.
     */
    private function createPivot(int $comunicadoId, int $usuarioId, int $estatusId, ?int $emisorId = null, ?string $observacion = null, ?int $motivoId = null): ComunicadoDestinatario
    {
        $data = [
            'comunicado_id' => $comunicadoId,
            'usuario_id'    => $usuarioId,
            'emisor_id'     => $emisorId,
            'estatus_id'    => $estatusId,
        ];

        if ($motivoId !== null) {
            $data['motivo_id'] = $motivoId;
        }

        if ($observacion !== null) {
            $data['observacion'] = $observacion;
        }

        return ComunicadoDestinatario::create($data);
    }
}
