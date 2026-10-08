<?php

namespace Tests\Feature\Cartero;

use App\Models\User;
use Database\Seeders\MotivosDevolucionSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CarteroCatalogTest extends TestCase
{
    use CreatesCarteroSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createCarteroSchema();
        Role::firstOrCreate(['id' => 1, 'name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['id' => 7, 'name' => 'Repartidor Postal Telegrafico', 'guard_name' => 'web']);
    }

    private function autenticarCartero(bool $admin = false): User
    {
        $user = User::create([
            'name' => 'Cartero',
            'email' => 'cartero+' . uniqid() . '@test.com',
            'cedula' => 'V' . random_int(10000000, 99999999),
            'password' => Hash::make('Secret123'),
            'activo' => true,
        ]);
        $user->assignRole($admin ? 'SuperAdmin' : 'Repartidor Postal Telegrafico');

        $token = $user->createToken('cartero-access', ['cartero:access'])->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}");

        return $user;
    }

    public function test_catalogo_devuelve_los_10_motivos(): void
    {
        $this->autenticarCartero();
        $this->seed(MotivosDevolucionSeeder::class);

        $response = $this->getJson('/api/cartero/v1/catalogs/return-reasons');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));

        $codigos = collect($response->json('data'))->pluck('codigo')->all();
        $this->assertContains('closed_home', $codigos);
        $this->assertContains('insufficient_address', $codigos);
    }

    public function test_requirements_get_devuelve_5_defaults(): void
    {
        $this->autenticarCartero();

        $response = $this->getJson('/api/cartero/v1/settings/recipient-requirements');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json('data'));

        $keys = collect($response->json('data'))->pluck('field_key')->all();
        foreach (['firstName', 'lastName', 'document', 'phone', 'address'] as $expected) {
            $this->assertContains($expected, $keys);
        }
    }

    public function test_requirements_put_requiere_admin(): void
    {
        $this->autenticarCartero(admin: false);

        $response = $this->putJson('/api/cartero/v1/settings/recipient-requirements', [
            'fields' => [[
                'field_key' => 'phone',
                'label' => 'Teléfono',
                'is_required' => false,
            ]],
        ]);

        $response->assertStatus(403);
    }

    public function test_requirements_put_actualiza_si_es_admin(): void
    {
        $this->autenticarCartero(admin: true);

        $response = $this->putJson('/api/cartero/v1/settings/recipient-requirements', [
            'fields' => [[
                'field_key' => 'phone',
                'label' => 'Teléfono',
                'is_required' => false,
            ]],
        ]);

        $response->assertStatus(200);
        $phoneField = collect($response->json('data'))->firstWhere('field_key', 'phone');
        $this->assertFalse($phoneField['is_required']);
    }
}
