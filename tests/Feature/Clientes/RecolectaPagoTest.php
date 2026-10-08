<?php

namespace Tests\Feature\Clientes;

use App\Models\Recolecta;
use App\Models\TipoPago;
use App\Models\UsuarioAppMovil;
use Tests\TestCase;

/**
 * Estructura del pago por adelantado (la verificación está en stand-by):
 * reporte de pago, referencia obligatoria para transferencia/pago móvil,
 * monto suficiente y transición a pago_reportado.
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=RecolectaPagoTest
 */
class RecolectaPagoTest extends TestCase
{
    use CreatesRecolectaSchema;

    private UsuarioAppMovil $usuario;
    private string $token;
    private int $recolectaId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
        $this->seedParametros();
        $this->seedTarifas();
        $this->seedRecolectaEstatus();

        // tipos_pagos con ids 1..7 (Transferencia=3, Pago Movil=4, patrón seeder)
        foreach (['Tarjeta de Debito', 'Tarjeta de Credito', 'Transferencia Bancaria', 'Pago Movil', 'BioPago', 'Corporativo', 'Efectivo'] as $nombre) {
            TipoPago::create(['nombre' => $nombre, 'activo' => true]);
        }

        $origen = $this->seedUbicacion('A');
        $destino = $this->seedUbicacion('B');
        $this->crearOficina([
            'municipio_id' => $origen['municipio']->municipio_id,
            'parroquia_id' => $origen['parroquia']->parroquia_id,
        ]);
        $this->usuario = $this->crearUsuarioApp();
        $this->token = $this->tokenCliente($this->usuario);

        $this->postJson('/api/clientes/v1/recolectas', $this->payloadRecolecta($origen, $destino), [
            'Authorization' => "Bearer {$this->token}",
        ])->assertStatus(201);
        $this->recolectaId = Recolecta::first()->recolecta_id;
    }

    private function pagar(array $payload)
    {
        return $this->postJson("/api/clientes/v1/recolectas/{$this->recolectaId}/pago", $payload, [
            'Authorization' => "Bearer {$this->token}",
        ]);
    }

    public function test_reporta_pago_movil_y_transiciona_a_pago_reportado(): void
    {
        // total de la recolecta: 267.49 (ver RecolectaCotizacionTest)
        $response = $this->pagar([
            'tipo_pago_id' => 4,
            'monto' => 267.49,
            'numero_referencia' => '003512345678',
            'banco_codigo' => '0102',
            'telefono_pagador' => '04141234567',
            'fecha_pago' => '2026-07-20',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.estatus', 'reportado')
            ->assertJsonPath('data.recolecta_estatus', 'pago_reportado');

        $this->assertDatabaseHas('recolecta_pagos', [
            'recolecta_id' => $this->recolectaId,
            'tipo_pago_id' => 4,
            'numero_referencia' => '003512345678',
            'estatus' => 'reportado',
        ]);
    }

    public function test_referencia_obligatoria_para_transferencia_y_pago_movil(): void
    {
        $this->pagar(['tipo_pago_id' => 4, 'monto' => 267.49])->assertStatus(422);
        $this->pagar(['tipo_pago_id' => 3, 'monto' => 267.49])->assertStatus(422);

        // Efectivo (7) no exige referencia.
        $this->pagar(['tipo_pago_id' => 7, 'monto' => 267.49])->assertStatus(201);
    }

    public function test_monto_insuficiente_es_rechazado(): void
    {
        $this->pagar([
            'tipo_pago_id' => 4,
            'monto' => 100,
            'numero_referencia' => '003512345678',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'El monto reportado no cubre el total de la recolecta.');
    }

    public function test_no_admite_segundo_pago(): void
    {
        $this->pagar(['tipo_pago_id' => 7, 'monto' => 267.49])->assertStatus(201);

        // Ya en pago_reportado: no admite otro pago.
        $this->pagar(['tipo_pago_id' => 7, 'monto' => 267.49])->assertStatus(422);
    }
}
