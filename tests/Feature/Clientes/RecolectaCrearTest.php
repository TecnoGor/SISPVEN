<?php

namespace Tests\Feature\Clientes;

use App\Models\Oficina;
use App\Models\Recolecta;
use App\Models\UsuarioAppMovil;
use Tests\TestCase;

/**
 * Creación, listado, detalle, ownership y cancelación de recolectas.
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=RecolectaCrearTest
 */
class RecolectaCrearTest extends TestCase
{
    use CreatesRecolectaSchema;

    private UsuarioAppMovil $usuario;
    private string $token;
    private array $origen;
    private array $destino;
    private Oficina $oficina;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
        $this->seedParametros();
        $this->seedTarifas();
        $this->seedRecolectaEstatus();
        $this->origen = $this->seedUbicacion('A');
        $this->destino = $this->seedUbicacion('B');
        $this->oficina = $this->crearOficina([
            'estado_id' => $this->origen['estado']->estado_id,
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ]);
        $this->usuario = $this->crearUsuarioApp();
        $this->token = $this->tokenCliente($this->usuario);
    }

    private function post_(string $uri, array $payload = [])
    {
        return $this->postJson($uri, $payload, ['Authorization' => "Bearer {$this->token}"]);
    }

    public function test_crea_recolecta_con_cotizacion_del_servidor(): void
    {
        $payload = $this->payloadRecolecta($this->origen, $this->destino);

        $response = $this->post_('/api/clientes/v1/recolectas', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.codigo', 'REC-00000001')
            ->assertJsonPath('data.estatus', 'solicitada')
            // Montos calculados por el servidor (peso 0.5 → 150.60 + 80.00 + IVA 36.89)
            ->assertJsonPath('data.montos.total', '267.49');

        $this->assertDatabaseHas('recolectas', [
            'codigo' => 'REC-00000001',
            'usuario_app_id' => $this->usuario->usuario_id,
            'oficina_id' => $this->oficina->oficina_id,
        ]);
    }

    public function test_notifica_a_la_oficina_recolectora(): void
    {
        $this->post_('/api/clientes/v1/recolectas', $this->payloadRecolecta($this->origen, $this->destino))
            ->assertStatus(201);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Oficina::class,
            'notifiable_id' => $this->oficina->oficina_id,
        ]);
    }

    public function test_rechaza_sin_gps(): void
    {
        $payload = $this->payloadRecolecta($this->origen, $this->destino);
        unset($payload['latitude'], $payload['longitude']);

        $this->post_('/api/clientes/v1/recolectas', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_rechaza_telefono_invalido(): void
    {
        $payload = $this->payloadRecolecta($this->origen, $this->destino, ['telefono_rem' => '1234567']);

        $this->post_('/api/clientes/v1/recolectas', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['telefono_rem']);
    }

    public function test_rechaza_cascada_incoherente(): void
    {
        // Parroquia del municipio B con municipio A
        $payload = $this->payloadRecolecta($this->origen, $this->destino, [
            'parroquia_id' => $this->destino['parroquia']->parroquia_id,
        ]);

        $this->post_('/api/clientes/v1/recolectas', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['parroquia_id']);
    }

    public function test_rechaza_suma_de_dimensiones_mayor_a_300(): void
    {
        $payload = $this->payloadRecolecta($this->origen, $this->destino, [
            'modo_peso' => 'volumetrico',
            'peso' => null,
            'alto' => 150, 'ancho' => 100, 'largo' => 100,
        ]);

        $this->post_('/api/clientes/v1/recolectas', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['alto']);
    }

    public function test_index_y_show_solo_devuelven_recolectas_propias(): void
    {
        $this->post_('/api/clientes/v1/recolectas', $this->payloadRecolecta($this->origen, $this->destino))
            ->assertStatus(201);

        $otroUsuario = $this->crearUsuarioApp(['correo' => 'otro@example.com', 'cedula' => 99887766]);
        $tokenOtro = $this->tokenCliente($otroUsuario);

        // Limpiar el guard cacheado al cambiar de token dentro del test.
        $this->app['auth']->forgetGuards();

        $this->getJson('/api/clientes/v1/recolectas', ['Authorization' => "Bearer {$tokenOtro}"])
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $recolectaId = Recolecta::first()->recolecta_id;
        $this->getJson("/api/clientes/v1/recolectas/{$recolectaId}", ['Authorization' => "Bearer {$tokenOtro}"])
            ->assertStatus(404);

        $this->app['auth']->forgetGuards();

        $this->getJson("/api/clientes/v1/recolectas/{$recolectaId}", ['Authorization' => "Bearer {$this->token}"])
            ->assertOk()
            ->assertJsonPath('data.codigo', 'REC-00000001');
    }

    public function test_cancelar_solo_en_estados_permitidos(): void
    {
        $this->post_('/api/clientes/v1/recolectas', $this->payloadRecolecta($this->origen, $this->destino))
            ->assertStatus(201);
        $recolectaId = Recolecta::first()->recolecta_id;

        $this->post_("/api/clientes/v1/recolectas/{$recolectaId}/cancelar")
            ->assertOk()
            ->assertJsonPath('data.estatus', 'cancelada');

        // Ya cancelada: segunda cancelación es inválida.
        $this->post_("/api/clientes/v1/recolectas/{$recolectaId}/cancelar")
            ->assertStatus(422);
    }
}
