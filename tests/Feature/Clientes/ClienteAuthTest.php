<?php

namespace Tests\Feature\Clientes;

use Tests\TestCase;

/**
 * Auth de la API de clientes: login, refresh con rotación, logout,
 * profile y aislamiento de abilities.
 * Ejecuta con: php artisan test --configuration=phpunit.clientes.xml --filter=ClienteAuthTest
 */
class ClienteAuthTest extends TestCase
{
    use CreatesRecolectaSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRecolectaSchema();
    }

    public function test_login_exitoso_devuelve_tokens_y_usuario(): void
    {
        $this->crearUsuarioApp();

        $response = $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'secreto123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'refresh_token', 'user' => ['id', 'nombre', 'cedula']]);
        $this->assertSame('V', $response->json('user.tipo_documento'));
    }

    public function test_login_rechaza_credenciales_invalidas(): void
    {
        $this->crearUsuarioApp();

        $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'incorrecta',
        ])->assertStatus(422);
    }

    public function test_login_rechaza_cuenta_desactivada(): void
    {
        $this->crearUsuarioApp(['activo' => false]);

        $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'secreto123',
        ])->assertStatus(403);
    }

    public function test_profile_requiere_token(): void
    {
        $this->getJson('/api/clientes/v1/profile')->assertStatus(401);
    }

    public function test_profile_con_token_devuelve_usuario(): void
    {
        $usuario = $this->crearUsuarioApp();
        $token = $this->tokenCliente($usuario);

        $this->getJson('/api/clientes/v1/profile', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('user.correo', 'maria@example.com');
    }

    public function test_refresh_rota_el_refresh_token(): void
    {
        $this->crearUsuarioApp();

        $login = $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'secreto123',
        ]);
        $refreshViejo = $login->json('refresh_token');

        $refresh = $this->postJson('/api/clientes/v1/auth/refresh', ['refresh_token' => $refreshViejo]);
        $refresh->assertOk()->assertJsonStructure(['access_token', 'refresh_token']);

        // El refresh viejo quedó invalidado por la rotación.
        $this->postJson('/api/clientes/v1/auth/refresh', ['refresh_token' => $refreshViejo])
            ->assertStatus(401);
    }

    public function test_refresh_token_no_sirve_como_bearer(): void
    {
        $this->crearUsuarioApp();

        $login = $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'secreto123',
        ]);

        $this->getJson('/api/clientes/v1/profile', [
            'Authorization' => 'Bearer ' . $login->json('refresh_token'),
        ])->assertStatus(403);
    }

    public function test_logout_revoca_los_tokens_cliente(): void
    {
        $this->crearUsuarioApp();

        $login = $this->postJson('/api/clientes/v1/auth/login', [
            'correo' => 'maria@example.com',
            'password' => 'secreto123',
        ]);
        $access = $login->json('access_token');

        $this->postJson('/api/clientes/v1/auth/logout', [], ['Authorization' => "Bearer {$access}"])
            ->assertOk();

        // Limpiar el guard cacheado del request anterior (peculiaridad de
        // Sanctum en tests con múltiples requests autenticados).
        $this->app['auth']->forgetGuards();

        $this->getJson('/api/clientes/v1/profile', ['Authorization' => "Bearer {$access}"])
            ->assertStatus(401);
    }
}
