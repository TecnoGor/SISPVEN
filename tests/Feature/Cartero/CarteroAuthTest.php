<?php

namespace Tests\Feature\Cartero;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre login OK/KO, middleware "cartero" y profile.
 * Ejecuta con: php artisan test --configuration=phpunit.cartero.xml --filter=CarteroAuthTest
 */
class CarteroAuthTest extends TestCase
{
    use CreatesCarteroSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createCarteroSchema();
        Role::firstOrCreate(['id' => 1, 'name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['id' => 7, 'name' => 'Repartidor Postal Telegrafico', 'guard_name' => 'web']);
        Role::firstOrCreate(['id' => 99, 'name' => 'Otro', 'guard_name' => 'web']);
    }

    private function crearCartero(array $overrides = []): User
    {
        $user = User::create(array_merge([
            'name' => 'Cartero Test',
            'email' => 'cartero+' . uniqid() . '@test.com',
            'cedula' => 'V' . random_int(10000000, 99999999),
            'password' => Hash::make('Secret123'),
            'activo' => true,
        ], $overrides));
        $user->assignRole('Repartidor Postal Telegrafico');
        return $user;
    }

    public function test_login_rechaza_credenciales_invalidas(): void
    {
        $response = $this->postJson('/api/cartero/v1/auth/login', [
            'cedula' => 'V99999999',
            'password' => 'incorrecta',
        ]);
        $response->assertStatus(422);
    }

    public function test_login_rechaza_usuario_sin_rol_cartero(): void
    {
        $user = User::create([
            'name' => 'Otro Rol',
            'email' => 'otro@test.com',
            'cedula' => 'V11111111',
            'password' => Hash::make('Secret123'),
            'activo' => true,
        ]);
        $user->assignRole('Otro');

        $response = $this->postJson('/api/cartero/v1/auth/login', [
            'cedula' => 'V11111111',
            'password' => 'Secret123',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'No tiene rol de cartero']);
    }

    public function test_login_exitoso_devuelve_tokens(): void
    {
        $this->crearCartero(['cedula' => 'V12345678']);

        $response = $this->postJson('/api/cartero/v1/auth/login', [
            'cedula' => 'V12345678',
            'password' => 'Secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'refresh_token',
            'user' => ['id', 'name', 'cedula', 'is_admin', 'is_active', 'roles'],
        ]);
        $this->assertEquals('V12345678', $response->json('user.cedula'));
        $this->assertFalse($response->json('user.is_admin'));
    }

    public function test_login_admin_marca_is_admin_true(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'cedula' => 'V22222222',
            'password' => Hash::make('Admin123'),
            'activo' => true,
        ]);
        $user->assignRole('SuperAdmin');

        $response = $this->postJson('/api/cartero/v1/auth/login', [
            'cedula' => 'V22222222',
            'password' => 'Admin123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('user.is_admin'));
    }

    public function test_login_cuenta_desactivada(): void
    {
        $user = $this->crearCartero(['cedula' => 'V33333333']);
        $user->update(['activo' => false]);

        $response = $this->postJson('/api/cartero/v1/auth/login', [
            'cedula' => 'V33333333',
            'password' => 'Secret123',
        ]);
        $response->assertStatus(403);
    }

    public function test_profile_requiere_token(): void
    {
        $response = $this->getJson('/api/cartero/v1/profile');
        $response->assertStatus(401);
    }

    public function test_profile_devuelve_datos_autenticado(): void
    {
        $user = $this->crearCartero(['cedula' => 'V44444444']);
        $token = $user->createToken('cartero-access', ['cartero:access'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/cartero/v1/profile');

        $response->assertStatus(200);
        $response->assertJsonPath('user.cedula', 'V44444444');
    }

    public function test_logout_revoca_tokens(): void
    {
        $user = $this->crearCartero();
        $token = $user->createToken('cartero-access-uno', ['cartero:access'])->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cartero/v1/auth/logout')
            ->assertStatus(200);

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_refresh_rota_tokens(): void
    {
        $user = $this->crearCartero();
        $refresh = $user->createToken('cartero-refresh-uno', ['cartero:refresh'])->plainTextToken;

        $response = $this->postJson('/api/cartero/v1/auth/refresh', [
            'refresh_token' => $refresh,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'refresh_token']);

        // El refresh viejo queda invalidado.
        $invalid = $this->postJson('/api/cartero/v1/auth/refresh', [
            'refresh_token' => $refresh,
        ]);
        $invalid->assertStatus(401);
    }
}
