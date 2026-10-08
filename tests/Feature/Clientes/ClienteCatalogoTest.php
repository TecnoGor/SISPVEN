<?php

namespace Tests\Feature\Clientes;

use App\Models\Estado;
use App\Models\Sector;
use App\Models\TipoPago;
use Tests\TestCase;

/**
 * Catálogos del formulario de recolecta: cascada de ubicación y catálogos
 * de documentos, métodos de pago, bancos y estados de recolecta.
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=ClienteCatalogoTest
 */
class ClienteCatalogoTest extends TestCase
{
    use CreatesRecolectaSchema;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
        $this->token = $this->tokenCliente($this->crearUsuarioApp());
    }

    private function get_(string $uri)
    {
        return $this->getJson($uri, ['Authorization' => "Bearer {$this->token}"]);
    }

    public function test_catalogos_requieren_token(): void
    {
        $this->getJson('/api/clientes/v1/catalogs/estados')->assertStatus(401);
    }

    public function test_estados_solo_venezuela_activos(): void
    {
        $this->seedUbicacion('A'); // pais 90
        Estado::create(['nombre' => 'Estado Inactivo', 'pais_id' => 90, 'activo' => false]);
        Estado::create(['nombre' => 'Estado Extranjero', 'pais_id' => 1, 'activo' => true]);

        $response = $this->get_('/api/clientes/v1/catalogs/estados');

        $response->assertOk();
        $this->assertSame(['Estado A'], collect($response->json('data'))->pluck('nombre')->all());
    }

    public function test_cascada_municipios_parroquias_y_ciudades(): void
    {
        $a = $this->seedUbicacion('A');
        $b = $this->seedUbicacion('B');

        $municipios = $this->get_("/api/clientes/v1/catalogs/estados/{$a['estado']->estado_id}/municipios");
        $this->assertSame(['Municipio A'], collect($municipios->json('data'))->pluck('nombre')->all());

        $parroquias = $this->get_("/api/clientes/v1/catalogs/municipios/{$a['municipio']->municipio_id}/parroquias");
        $this->assertSame(['Parroquia A'], collect($parroquias->json('data'))->pluck('nombre')->all());

        $ciudades = $this->get_("/api/clientes/v1/catalogs/municipios/{$b['municipio']->municipio_id}/ciudades");
        $this->assertSame(['Ciudad B'], collect($ciudades->json('data'))->pluck('nombre')->all());
    }

    public function test_codigos_postales_distintos_por_parroquia(): void
    {
        $a = $this->seedUbicacion('A'); // sector con CP 1010

        // Sector duplicado con el mismo CP y otro con CP distinto
        foreach (['1010', '1020'] as $cp) {
            $sector = new Sector();
            $sector->codigo_postal = $cp;
            $sector->nombre = "Sector extra {$cp}";
            $sector->parroquia_id = $a['parroquia']->parroquia_id;
            $sector->activo = true;
            $sector->save();
        }

        $response = $this->get_("/api/clientes/v1/catalogs/parroquias/{$a['parroquia']->parroquia_id}/codigos-postales");

        $response->assertOk();
        $this->assertSame(['1010', '1020'], $response->json('data'));
    }

    public function test_metodos_pago_excluye_corporativo_e_inactivos(): void
    {
        // ids por orden de inserción: 1..7 (Corporativo = 6, patrón seeder)
        foreach (['Tarjeta de Debito', 'Tarjeta de Credito', 'Transferencia Bancaria', 'Pago Movil', 'BioPago', 'Corporativo', 'Efectivo'] as $nombre) {
            TipoPago::create(['nombre' => $nombre, 'activo' => $nombre !== 'BioPago']);
        }

        $response = $this->get_('/api/clientes/v1/catalogs/metodos-pago');

        $nombres = collect($response->json('data'))->pluck('nombre');
        $this->assertFalse($nombres->contains('Corporativo'));
        $this->assertFalse($nombres->contains('BioPago'));
        $this->assertTrue($nombres->contains('Pago Movil'));
    }

    public function test_estados_recolecta_ordenados(): void
    {
        $this->seedRecolectaEstatus();

        $response = $this->get_('/api/clientes/v1/catalogs/estados-recolecta');

        $slugs = collect($response->json('data'))->pluck('slug')->all();
        $this->assertSame(
            ['solicitada', 'pago_reportado', 'pago_confirmado', 'recolectada', 'cancelada', 'rechazada'],
            $slugs
        );
    }
}
