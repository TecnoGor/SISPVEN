<?php

namespace Tests\Feature\Cartero;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el esquema mínimo necesario para los tests de la API cartero
 * sin depender de las migraciones legacy del proyecto (muchas usan
 * dropForeign que sqlite no soporta).
 *
 * Esto desacopla los tests del estado de las migraciones existentes.
 */
trait CreatesCarteroSchema
{
    protected function createCarteroSchema(): void
    {
        // Sanctum
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id();
            $t->morphs('tokenable');
            $t->string('name');
            $t->string('token', 64)->unique();
            $t->text('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });

        // Spatie permission
        Schema::create('roles', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('permissions', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->primary(['role_id', 'model_id', 'model_type']);
        });
        Schema::create('model_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->primary(['permission_id', 'model_id', 'model_type']);
        });
        Schema::create('role_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id');
            $t->unsignedBigInteger('role_id');
            $t->primary(['permission_id', 'role_id']);
        });

        // Oficinas (mínimo)
        Schema::create('oficinas', function (Blueprint $t) {
            $t->bigIncrements('oficina_id');
            $t->string('nombre')->nullable();
            $t->string('codigo')->nullable();
            $t->timestamps();
        });

        // Users
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('cedula')->unique();
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->rememberToken();
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->boolean('activo')->default(true);
            $t->string('telefono')->nullable();
            $t->unsignedBigInteger('empleado_id')->nullable();
            $t->timestamps();
        });

        // Envios (subset)
        Schema::create('envios', function (Blueprint $t) {
            $t->bigIncrements('envio_id');
            $t->string('codigo_envio')->nullable();
            $t->string('nombre_dest')->nullable();
            $t->string('apellido_dest')->nullable();
            $t->string('tipo_documento_dest')->nullable();
            $t->string('documento_dest')->nullable();
            $t->string('codigo_postal_dest')->nullable();
            $t->string('estado_dest')->nullable();
            $t->string('ciudad_dest')->nullable();
            $t->string('direccion_dest')->nullable();
            $t->string('tlf_dest')->nullable();
            $t->timestamps();
        });

        // EnviosAlmacen
        Schema::create('envios_almacen', function (Blueprint $t) {
            $t->bigIncrements('envio_almacen_id');
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->string('codigo')->nullable();
            $t->boolean('estatus')->default(true);
            $t->timestamps();
        });

        // Asignación
        Schema::create('asignacion_envio_cartero', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('envio_almacen_id');
            $t->unsignedBigInteger('user_id');
            $t->boolean('estatus')->default(true);
            $t->timestamps();
        });

        // Encaminamientos
        Schema::create('envios_encaminamiento', function (Blueprint $t) {
            $t->bigIncrements('envios_encaminamiento_id');
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->unsignedBigInteger('estatus_id')->nullable();
            $t->boolean('devolucion')->default(false);
            $t->timestamps();
        });

        // Motivos de devolución
        Schema::create('motivos_devolucion', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 40)->unique();
            $t->string('nombre', 120);
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });

        // Intentos entregas (con las nuevas columnas)
        Schema::create('intentos_entregas', function (Blueprint $t) {
            $t->bigIncrements('intento_entrega_id');
            $t->unsignedBigInteger('envio_almacen_id');
            $t->unsignedBigInteger('envio_id');
            $t->unsignedBigInteger('usuario_id');
            $t->unsignedBigInteger('motivo_id')->nullable();
            $t->decimal('latitud', 10, 7)->nullable();
            $t->decimal('longitud', 10, 7)->nullable();
            $t->unsignedBigInteger('evidencia_id')->nullable();
            $t->timestamps();
        });

        // Evidencias
        Schema::create('envio_evidencias', function (Blueprint $t) {
            $t->bigIncrements('evidencia_id');
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->unsignedBigInteger('envio_almacen_id')->nullable();
            $t->unsignedBigInteger('user_id');
            $t->string('tipo', 20);
            $t->string('ruta_archivo', 500);
            $t->string('hash_sha256', 64);
            $t->string('transaction_id', 64)->nullable();
            $t->decimal('latitud', 10, 7)->nullable();
            $t->decimal('longitud', 10, 7)->nullable();
            $t->decimal('precision_gps', 8, 2)->nullable();
            $t->timestamp('capturada_en')->nullable();
            $t->timestamps();
        });

        // Configuración 7.4
        Schema::create('recipient_requirement_config', function (Blueprint $t) {
            $t->string('field_key', 40)->primary();
            $t->string('label', 60);
            $t->boolean('is_required')->default(true);
            $t->timestamp('updated_at')->nullable();
            $t->unsignedBigInteger('updated_by')->nullable();
        });

        // Dispositivos FCM
        Schema::create('cartero_devices', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('fcm_token', 255);
            $t->string('platform', 20);
            $t->string('app_version', 30)->nullable();
            $t->timestamp('last_seen')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'fcm_token']);
        });
    }
}
