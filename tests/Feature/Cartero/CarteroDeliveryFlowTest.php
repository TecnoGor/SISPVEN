<?php

namespace Tests\Feature\Cartero;

use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioEvidencia;
use App\Models\IntentoEntrega;
use App\Models\User;
use Database\Seeders\MotivosDevolucionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Recorre el flujo completo: daily route → detalle → status → final/attempt → evidencia.
 */
class CarteroDeliveryFlowTest extends TestCase
{
    use CreatesCarteroSchema;

    private User $cartero;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createCarteroSchema();
        Role::firstOrCreate(['id' => 1, 'name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['id' => 7, 'name' => 'Repartidor Postal Telegrafico', 'guard_name' => 'web']);
        $this->seed(MotivosDevolucionSeeder::class);

        $this->cartero = User::create([
            'name' => 'Cartero Flow',
            'email' => 'flow@test.com',
            'cedula' => 'V77777777',
            'password' => Hash::make('Cartero2026'),
            'activo' => true,
        ]);
        $this->cartero->assignRole('Repartidor Postal Telegrafico');

        $token = $this->cartero->createToken('cartero-access', ['cartero:access'])->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}");
    }

    private function crearEnvioAsignado(): EnvioAlmacen
    {
        $envio = Envio::create([
            'codigo_envio' => 'IPOSTEL-' . random_int(1000, 9999),
            'nombre_dest' => 'Maria',
            'apellido_dest' => 'Pérez',
            'documento_dest' => 'V12345678',
            'tlf_dest' => '04141234567',
            'direccion_dest' => 'Av. Urdaneta, Caracas',
            'ciudad_dest' => 'Caracas',
            'estado_dest' => 'Miranda',
            'codigo_postal_dest' => '1010',
        ]);

        $almacen = EnvioAlmacen::create([
            'envio_id' => $envio->envio_id,
            'oficina_id' => 1,
            'codigo' => $envio->codigo_envio,
            'estatus' => true,
        ]);

        DB::table('asignacion_envio_cartero')->insert([
            'envio_almacen_id' => $almacen->envio_almacen_id,
            'user_id' => $this->cartero->id,
            'estatus' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        EnvioEncaminamiento::create([
            'envio_id' => $envio->envio_id,
            'oficina_id' => 1,
            'usuario_id' => $this->cartero->id,
            'estatus_id' => 16,
        ]);

        return $almacen;
    }

    public function test_daily_route_lista_solo_los_envios_del_cartero(): void
    {
        $mio = $this->crearEnvioAsignado();

        // Otro cartero
        $otro = User::create([
            'name' => 'Otro',
            'email' => 'otro2@test.com',
            'cedula' => 'V88888888',
            'password' => Hash::make('Pwd'),
            'activo' => true,
        ]);
        $otro->assignRole('Repartidor Postal Telegrafico');
        $ajeno = Envio::create(['codigo_envio' => 'AJENO']);
        $almAjeno = EnvioAlmacen::create([
            'envio_id' => $ajeno->envio_id,
            'codigo' => 'AJENO',
            'estatus' => true,
        ]);
        DB::table('asignacion_envio_cartero')->insert([
            'envio_almacen_id' => $almAjeno->envio_almacen_id,
            'user_id' => $otro->id,
            'estatus' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/cartero/v1/routes/daily');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains((string) $mio->envio_almacen_id, $ids);
        $this->assertNotContains((string) $almAjeno->envio_almacen_id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_show_devuelve_detalle_del_envio_propio(): void
    {
        $almacen = $this->crearEnvioAsignado();

        $response = $this->getJson("/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}");

        $response->assertStatus(200);
        $response->assertJsonPath('recipient.name', 'Maria Pérez');
        $response->assertJsonPath('recipient.document', 'V12345678');
    }

    public function test_show_envio_ajeno_devuelve_403(): void
    {
        $otro = User::create([
            'name' => 'Otro',
            'email' => 'otro3@test.com',
            'cedula' => 'V66666666',
            'password' => Hash::make('Pwd'),
            'activo' => true,
        ]);
        $envio = Envio::create(['codigo_envio' => 'AJ']);
        $alm = EnvioAlmacen::create([
            'envio_id' => $envio->envio_id,
            'codigo' => 'AJ',
            'estatus' => true,
        ]);
        DB::table('asignacion_envio_cartero')->insert([
            'envio_almacen_id' => $alm->envio_almacen_id,
            'user_id' => $otro->id,
            'estatus' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson("/api/cartero/v1/deliveries/{$alm->envio_almacen_id}");
        $response->assertStatus(403);
    }

    public function test_update_status_crea_encaminamiento(): void
    {
        $almacen = $this->crearEnvioAsignado();

        $response = $this->putJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/status",
            ['status' => 18]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('envios_encaminamiento', [
            'envio_id' => $almacen->envio_id,
            'estatus_id' => 18,
            'usuario_id' => $this->cartero->id,
        ]);
    }

    public function test_final_effective_marca_entregado(): void
    {
        $almacen = $this->crearEnvioAsignado();

        $response = $this->postJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/final",
            [
                'final_result' => 'effective',
                'recipient_data' => [
                    'first_name' => 'Maria',
                    'last_name' => 'Pérez',
                    'document' => 'V12345678',
                    'phone' => '04141234567',
                    'address' => 'Av. Urdaneta',
                ],
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['status' => 17, 'final_result' => 'effective']);
        $this->assertDatabaseHas('envios_encaminamiento', [
            'envio_id' => $almacen->envio_id,
            'estatus_id' => 17,
            'devolucion' => false,
        ]);
        $this->assertSame(0, IntentoEntrega::count());
    }

    public function test_final_returned_requiere_motivo(): void
    {
        $almacen = $this->crearEnvioAsignado();

        $response = $this->postJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/final",
            [
                'final_result' => 'returned',
                'recipient_data' => [
                    'first_name' => 'X',
                    'last_name' => 'Y',
                    'document' => '',
                    'phone' => '',
                    'address' => '',
                ],
            ]
        );

        $response->assertStatus(422);
    }

    public function test_final_returned_con_motivo_crea_intento(): void
    {
        $almacen = $this->crearEnvioAsignado();

        $response = $this->postJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/final",
            [
                'final_result' => 'returned',
                'return_reason' => 'closed_home',
                'recipient_data' => [
                    'first_name' => 'X',
                    'last_name' => 'Y',
                    'document' => '',
                    'phone' => '',
                    'address' => '',
                ],
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonPath('final_result', 'returned');
        $response->assertJsonPath('return_reason', 'closed_home');
        $this->assertSame(1, IntentoEntrega::count());
        $intento = IntentoEntrega::first();
        $this->assertNotNull($intento->motivo_id);
    }

    public function test_attempt_sugiere_devolucion_al_tercero(): void
    {
        $almacen = $this->crearEnvioAsignado();

        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson(
                "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/attempt",
                [
                    'return_reason' => 'not_claimed',
                    'latitude' => 10.5,
                    'longitude' => -66.9,
                ]
            );
            $response->assertStatus(200);
            $response->assertJsonPath('attempt_number', $i);
            $response->assertJsonPath('suggest_return', $i >= 3);
        }

        $this->assertSame(3, IntentoEntrega::count());
    }

    public function test_evidence_upload_guarda_archivo_y_registro(): void
    {
        Storage::fake('local');
        $almacen = $this->crearEnvioAsignado();

        $file = UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->postJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/evidence",
            [
                'evidence_file' => $file,
                'type' => 'photo',
                'hash' => str_repeat('a', 64),
                'transaction_id' => 'tx-123',
                'latitude' => 10.5,
                'longitude' => -66.9,
                'gps_accuracy' => 5.2,
                'captured_at' => now()->toIso8601String(),
            ]
        );

        $response->assertStatus(201);
        $response->assertJsonStructure(['evidencia_id', 'ruta_archivo', 'hash_sha256']);
        $this->assertSame(1, EnvioEvidencia::count());

        $evidencia = EnvioEvidencia::first();
        Storage::disk('local')->assertExists($evidencia->ruta_archivo);
    }

    public function test_device_register_es_idempotente(): void
    {
        $payload = [
            'fcm_token' => 'token-abc',
            'platform' => 'android',
            'app_version' => '1.0.0',
        ];

        $this->postJson('/api/cartero/v1/devices/register', $payload)->assertStatus(200);
        $this->postJson('/api/cartero/v1/devices/register', $payload)->assertStatus(200);

        $this->assertSame(1, DB::table('cartero_devices')->count());
    }

    public function test_stats_devuelve_totales_del_cartero(): void
    {
        $almacen = $this->crearEnvioAsignado();

        // Marcar como entregado.
        $this->postJson(
            "/api/cartero/v1/deliveries/{$almacen->envio_almacen_id}/final",
            [
                'final_result' => 'effective',
                'recipient_data' => [
                    'first_name' => 'X',
                    'last_name' => 'Y',
                    'document' => '',
                    'phone' => '',
                    'address' => '',
                ],
            ]
        );

        $response = $this->getJson('/api/cartero/v1/stats/me');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'range' => ['from', 'to'],
            'totals' => ['assigned', 'in_transit', 'delivered', 'total', 'success_rate'],
            'top_return_reasons',
        ]);
        $this->assertGreaterThanOrEqual(1, $response->json('totals.delivered'));
    }
}
