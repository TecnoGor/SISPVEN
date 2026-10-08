<?php

namespace Tests\Feature\Clientes;

use Tests\TestCase;

/**
 * Cotización de recolectas: réplica del cálculo Iposplus de taquilla
 * (rango por peso × parámetro IposPlus, peso volumétrico /5000 truncado,
 * IVA condicional) más el parámetro de recolección, y resolución de la
 * oficina recolectora por municipio+parroquia con fallback a municipio.
 *
 * Parámetros sembrados: IposPlus=60, Recoleccion=80, BCV=41.13.
 * Tarifas: 0.1-1 kg → 2.51 · 1.01-2 kg → 3.00.
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=RecolectaCotizacionTest
 */
class RecolectaCotizacionTest extends TestCase
{
    use CreatesRecolectaSchema;

    private string $token;
    private array $origen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
        $this->seedParametros();
        $this->seedTarifas();
        $this->origen = $this->seedUbicacion('A');
        $this->token = $this->tokenCliente($this->crearUsuarioApp());
    }

    private function cotizar(array $payload)
    {
        return $this->postJson('/api/clientes/v1/recolectas/cotizar', array_merge([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ], $payload), ['Authorization' => "Bearer {$this->token}"]);
    }

    public function test_cotizacion_manual_replica_calculo_de_taquilla(): void
    {
        $this->crearOficina([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
            'estado_id' => $this->origen['estado']->estado_id,
        ]);

        $response = $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5]);

        // envío: 2.51 × 60 = 150.60 · recolección: 80.00 · base: 230.60
        // IVA 16% truncado bcdiv: 36.89 · total: 267.49
        $response->assertOk()
            ->assertJsonPath('data.monto_envio', '150.60')
            ->assertJsonPath('data.monto_recoleccion', '80.00')
            ->assertJsonPath('data.base', '230.60')
            ->assertJsonPath('data.iva', '36.89')
            ->assertJsonPath('data.total', '267.49')
            ->assertJsonPath('data.tasa_bs', 41.13)
            ->assertJsonPath('data.excede_tarifa_max', false);
    }

    public function test_cotizacion_volumetrica_trunca_a_3_decimales(): void
    {
        $this->crearOficina([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ]);

        $response = $this->cotizar([
            'modo_peso' => 'volumetrico',
            'alto' => 30, 'ancho' => 20, 'largo' => 15,
        ]);

        // 30×20×15 = 9000 / 5000 = 1.800 kg → rango 1.01-2 → 3.00 × 60 = 180.00
        $response->assertOk()
            ->assertJsonPath('data.peso_facturable', '1.800')
            ->assertJsonPath('data.peso_volumetrico', '1.800')
            ->assertJsonPath('data.monto_envio', '180.00');
    }

    public function test_suma_de_dimensiones_mayor_a_300_es_rechazada(): void
    {
        $this->crearOficina(['municipio_id' => $this->origen['municipio']->municipio_id, 'parroquia_id' => $this->origen['parroquia']->parroquia_id]);

        $this->cotizar(['modo_peso' => 'volumetrico', 'alto' => 150, 'ancho' => 100, 'largo' => 100])
            ->assertStatus(422);
    }

    public function test_peso_que_excede_tarifa_maxima_usa_tarifa_tope(): void
    {
        $this->crearOficina(['municipio_id' => $this->origen['municipio']->municipio_id, 'parroquia_id' => $this->origen['parroquia']->parroquia_id]);

        $response = $this->cotizar(['modo_peso' => 'manual', 'peso' => 5]);

        $response->assertOk()
            ->assertJsonPath('data.excede_tarifa_max', true)
            ->assertJsonPath('data.monto_envio', '180.00');
    }

    public function test_zona_economica_especial_exenta_de_iva(): void
    {
        $this->crearOficina([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
            'zona_economica_especial' => true,
        ]);

        $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5])
            ->assertOk()
            ->assertJsonPath('data.iva', '0.00')
            ->assertJsonPath('data.total', '230.60');
    }

    public function test_sin_oficina_en_el_municipio_devuelve_422(): void
    {
        // No se crea ninguna oficina.
        $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5])
            ->assertStatus(422)
            ->assertJsonPath('message', 'No hay oficina de Ipostel que atienda recolectas en tu municipio.');
    }

    public function test_prefiere_oficina_de_la_misma_parroquia(): void
    {
        $otraParroquia = \App\Models\Parroquia::create([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'nombre' => 'Parroquia Vecina',
            'activo' => true,
        ]);
        $soloMunicipio = $this->crearOficina([
            'nombre' => 'Solo Municipio',
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $otraParroquia->parroquia_id,
        ]);
        $exacta = $this->crearOficina([
            'nombre' => 'Municipio y Parroquia',
            'codigo' => 'OP002',
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
        ]);

        $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5])
            ->assertOk()
            ->assertJsonPath('data.oficina.id', (string) $exacta->oficina_id);
    }

    public function test_fallback_a_oficina_del_municipio(): void
    {
        $otraParroquia = \App\Models\Parroquia::create([
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'nombre' => 'Parroquia Vecina',
            'activo' => true,
        ]);
        $soloMunicipio = $this->crearOficina([
            'nombre' => 'Solo Municipio',
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $otraParroquia->parroquia_id,
        ]);

        $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5])
            ->assertOk()
            ->assertJsonPath('data.oficina.id', (string) $soloMunicipio->oficina_id);
    }

    public function test_excluye_oficinas_externas_e_inactivas(): void
    {
        $this->crearOficina([
            'nombre' => 'Externa',
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
            'externa' => true,
        ]);
        $this->crearOficina([
            'nombre' => 'Inactiva',
            'codigo' => 'OP003',
            'municipio_id' => $this->origen['municipio']->municipio_id,
            'parroquia_id' => $this->origen['parroquia']->parroquia_id,
            'estatus_id' => 3,
        ]);

        $this->cotizar(['modo_peso' => 'manual', 'peso' => 0.5])->assertStatus(422);
    }
}
