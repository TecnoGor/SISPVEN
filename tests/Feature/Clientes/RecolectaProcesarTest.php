<?php

namespace Tests\Feature\Clientes;

use App\Models\Oficina;
use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\UsuarioAppMovil;
use App\Services\Recolectas\ProcesarRecolectaService;
use App\Services\Recolectas\RecolectaFlowService;
use App\Services\Recolectas\TransicionRecolectaInvalidaException;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Conversión de la recolecta en envío real (gate de pago por adelantado):
 * confirmarPago (hook manual de oficina) y ProcesarRecolectaService, que
 * debe replicar el grafo completo de Iposplus::finalizarLote().
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=RecolectaProcesarTest
 */
class RecolectaProcesarTest extends TestCase
{
    use CreatesRecolectaSchema;

    private UsuarioAppMovil $usuario;
    private string $token;
    private Oficina $oficina;
    private User $operador;
    private array $origen;
    private array $destino;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
        $this->seedParametros();
        $this->seedTarifas();
        $this->seedRecolectaEstatus();
        TipoPago::create(['nombre' => 'Efectivo', 'activo' => true]);

        $this->origen = $this->seedUbicacion('A');
        $this->destino = $this->seedUbicacion('B');

        $this->oficina = $this->crearOficina([
            'codigo' => 'OP001',
            'estado_id' => $this->origen['estado']->estado_id,
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ]);

        // COP para el código de envío. El estado destino queda con id 2, y el
        // generador replica la excepción de taquilla "estado 2/24 → COP del
        // estado 1", así que la COP se crea en el estado 1 (este test cubre
        // esa excepción hardcodeada).
        $this->crearOficina([
            'codigo' => 'CP002',
            'nombre' => 'COP Regla Excepcion',
            'tipo_oficina_id' => 4,
            'estado_id' => 1,
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ]);

        $this->operador = User::create([
            'name' => 'Operador Test',
            'email' => 'operador@test.com',
            'cedula' => 'V11223344',
            'password' => Hash::make('Secret123'),
            'oficina_id' => $this->oficina->oficina_id,
            'activo' => true,
        ]);

        $this->usuario = $this->crearUsuarioApp();
        $this->token = $this->tokenCliente($this->usuario);
    }

    private function crearRecolectaPagada(): Recolecta
    {
        $this->postJson('/api/clientes/v1/recolectas', $this->payloadRecolecta($this->origen, $this->destino), [
            'Authorization' => "Bearer {$this->token}",
        ])->assertStatus(201);

        $recolecta = Recolecta::with('estatus')->first();

        $this->postJson("/api/clientes/v1/recolectas/{$recolecta->recolecta_id}/pago", [
            'tipo_pago_id' => 1,
            'monto' => (float) $recolecta->total,
            'numero_referencia' => 'REF-001',
        ], ['Authorization' => "Bearer {$this->token}"])->assertStatus(201);

        return $recolecta->refresh()->load('estatus');
    }

    public function test_no_puede_procesarse_sin_pago_confirmado(): void
    {
        $recolecta = $this->crearRecolectaPagada(); // pago_reportado, aún sin confirmar

        $this->expectException(TransicionRecolectaInvalidaException::class);
        app(ProcesarRecolectaService::class)->procesar($recolecta, $this->operador);
    }

    public function test_confirmar_pago_marca_pagos_y_habilita_el_procesamiento(): void
    {
        $recolecta = $this->crearRecolectaPagada();

        app(RecolectaFlowService::class)->confirmarPago($recolecta, $this->operador);

        $recolecta->refresh()->load('estatus');
        $this->assertSame(RecolectaEstatus::PAGO_CONFIRMADO, $recolecta->estatus->slug);
        $this->assertNotNull($recolecta->pago_confirmado_en);
        $this->assertDatabaseHas('recolecta_pagos', [
            'recolecta_id' => $recolecta->recolecta_id,
            'estatus' => 'confirmado',
            'confirmado_por' => $this->operador->id,
        ]);
    }

    public function test_procesar_crea_el_envio_completo_con_encaminamiento_y_facturacion(): void
    {
        $recolecta = $this->crearRecolectaPagada();
        app(RecolectaFlowService::class)->confirmarPago($recolecta, $this->operador);

        $envio = app(ProcesarRecolectaService::class)->procesar($recolecta->refresh()->load('estatus'), $this->operador);

        // Envío con paridad de taquilla Iposplus
        $this->assertSame('OP001CP002000000001', $envio->codigo_envio);
        $this->assertDatabaseHas('envios', [
            'envio_id' => $envio->envio_id,
            'servicio_id' => 10,
            'tipo_saca_id' => 14,
            'oficina_id' => $this->oficina->oficina_id,
            'usuario_id' => $this->operador->id,
            'peso' => 500, // 0.5 kg → gramos
        ]);

        $this->assertDatabaseHas('envios_iposplus', ['envio_id' => $envio->envio_id]);
        $this->assertDatabaseHas('envios_encaminamiento', [
            'envio_id' => $envio->envio_id,
            'oficina_id' => $this->oficina->oficina_id,
            'estatus_id' => 1,
            'devolucion' => false,
        ]);
        $this->assertDatabaseHas('envios_almacen', [
            'envio_id' => $envio->envio_id,
            'oficina_id' => $this->oficina->oficina_id,
            'codigo' => $envio->codigo_envio,
        ]);

        // Grafo contable
        $this->assertDatabaseHas('facturaciones', [
            'oficina_id' => $this->oficina->oficina_id,
            'documento' => '12345678',
        ]);
        $this->assertDatabaseHas('facturacion_pagos', ['numero_referencia' => 'REF-001']);
        $this->assertDatabaseHas('facturacion_envio', [
            'envio_id' => $envio->envio_id,
            'usuario_id' => $this->operador->id,
        ]);

        // Clientes CRM creados
        $this->assertDatabaseHas('clientes', ['numero_documento' => '12345678']);
        $this->assertDatabaseHas('clientes', ['numero_documento' => '87654321']);

        // La recolecta quedó enlazada y en estado terminal
        $recolecta->refresh()->load('estatus');
        $this->assertSame($envio->envio_id, $recolecta->envio_id);
        $this->assertSame(RecolectaEstatus::RECOLECTADA, $recolecta->estatus->slug);
    }

    public function test_transiciones_ilegales_son_rechazadas(): void
    {
        $recolecta = $this->crearRecolectaPagada(); // pago_reportado

        $this->expectException(TransicionRecolectaInvalidaException::class);
        app(RecolectaFlowService::class)->transicionar($recolecta, RecolectaEstatus::RECOLECTADA);
    }
}
