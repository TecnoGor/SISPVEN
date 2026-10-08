<?php

namespace Tests\Feature\Clientes;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Oficina;
use App\Models\Parametro;
use App\Models\Parroquia;
use App\Models\RecolectaEstatus;
use App\Models\TarifaIposplus;
use App\Models\UsuarioAppMovil;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Esquema mínimo para los tests de la API de clientes (recolectas), sin
 * depender de las migraciones legacy (patrón CreatesCarteroSchema).
 *
 * El schema PostgreSQL `sispven_app` se simula en SQLite con
 * ATTACH DATABASE ':memory:' AS sispven_app, lo que hace válida la
 * referencia `sispven_app.usuarios` del modelo UsuarioAppMovil.
 */
trait CreatesRecolectaSchema
{
    protected function createRecolectaSchema(): void
    {
        DB::statement("ATTACH DATABASE ':memory:' AS sispven_app");

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

        // Spatie permission (User usa HasRoles)
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

        // Clientes de la app móvil
        // Nota: sin ->unique() — SQLite no admite crear índices sobre
        // "schema"."tabla" con la sintaxis que genera Laravel.
        Schema::create('sispven_app.usuarios', function (Blueprint $t) {
            $t->id('usuario_id');
            $t->string('nombre');
            $t->string('apellido');
            $t->string('correo')->nullable();
            $t->string('telefono')->nullable();
            $t->string('direccion')->nullable();
            $t->string('contraseña', 255);
            $t->date('fecha_nacimiento')->nullable();
            $t->integer('cedula');
            $t->string('tipo_documento');
            $t->string('rol')->default('usuario');
            $t->boolean('activo')->default(true);
            $t->unsignedBigInteger('estado_id')->nullable();
            $t->date('correo_verificado')->nullable();
            $t->string('otp')->nullable();
            $t->string('imagen_perfil')->nullable();
            $t->string('imagen_documento')->nullable();
            $t->boolean('identidad_verificada')->default(false);
            $t->timestamps();
        });

        // Usuarios web
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('cedula')->unique();
            $t->string('password');
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->boolean('activo')->default(true);
            $t->string('telefono')->nullable();
            $t->timestamps();
        });

        // Catálogos de ubicación
        Schema::create('estados', function (Blueprint $t) {
            $t->bigIncrements('estado_id');
            $t->unsignedBigInteger('region_id')->nullable();
            $t->unsignedBigInteger('pais_id')->nullable();
            $t->string('nombre');
            $t->string('codigo')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('municipios', function (Blueprint $t) {
            $t->bigIncrements('municipio_id');
            $t->unsignedBigInteger('estado_id');
            $t->string('nombre');
            $t->string('codigo')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('parroquias', function (Blueprint $t) {
            $t->bigIncrements('parroquia_id');
            $t->unsignedBigInteger('municipio_id');
            $t->string('nombre');
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('ciudades', function (Blueprint $t) {
            $t->bigIncrements('ciudad_id');
            $t->string('nombre');
            $t->unsignedBigInteger('estado_id');
            $t->unsignedBigInteger('municipio_id');
            $t->timestamps();
        });
        Schema::create('sectores', function (Blueprint $t) {
            $t->bigIncrements('sector_id');
            $t->string('codigo_postal');
            $t->string('nombre');
            $t->unsignedBigInteger('parroquia_id');
            $t->boolean('activo')->nullable();
            $t->timestamps();
        });

        // Oficinas
        Schema::create('oficinas', function (Blueprint $t) {
            $t->bigIncrements('oficina_id');
            $t->string('codigo')->nullable();
            $t->unsignedBigInteger('oficina_relacionada_id')->nullable();
            $t->string('nombre')->nullable();
            $t->unsignedBigInteger('tipo_oficina_id')->nullable();
            $t->string('direccion')->nullable();
            $t->string('codigo_ubicacion')->nullable();
            $t->unsignedBigInteger('estado_id')->nullable();
            $t->unsignedBigInteger('municipio_id')->nullable();
            $t->unsignedBigInteger('parroquia_id')->nullable();
            $t->boolean('zona_economica_especial')->default(false);
            $t->unsignedBigInteger('estatus_id')->nullable();
            $t->boolean('operaciones')->default(true);
            $t->boolean('externa')->default(false);
            $t->string('latitud')->nullable();
            $t->string('longitud')->nullable();
            $t->timestamps();
        });

        // Catálogos varios
        Schema::create('documentos', function (Blueprint $t) {
            $t->bigIncrements('documento_id');
            $t->string('tipo');
            $t->string('descripcion')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('tipos_pagos', function (Blueprint $t) {
            $t->bigIncrements('tipo_pago_id');
            $t->string('nombre');
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('entidades_bancarias', function (Blueprint $t) {
            $t->bigIncrements('entidad_bancaria_id');
            $t->string('nombre');
            $t->string('codigo');
            $t->timestamps();
        });
        Schema::create('parametro', function (Blueprint $t) {
            $t->bigIncrements('parametro_id');
            $t->string('nombre');
            $t->float('valor');
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        // Auditoría escrita por ParametroObserver en cada create/update
        Schema::create('parametros_historicos', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('parametro_id')->nullable();
            $t->float('valor_anterior')->nullable();
            $t->float('valor_nuevo')->nullable();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->timestamp('fecha_cambio')->nullable();
            $t->timestamps();
        });
        Schema::create('tarifas_iposplus', function (Blueprint $t) {
            $t->bigIncrements('tarifa_iposplus_id');
            $t->float('kilo_min');
            $t->float('kilo_max');
            $t->float('precio');
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });

        // Recolectas
        Schema::create('recolecta_estatus', function (Blueprint $t) {
            $t->id('recolecta_estatus_id');
            $t->string('nombre');
            $t->string('slug')->unique();
            $t->integer('orden');
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
        Schema::create('recolectas', function (Blueprint $t) {
            $t->id('recolecta_id');
            $t->unsignedBigInteger('usuario_app_id')->nullable();
            $t->unsignedBigInteger('oficina_id');
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->unsignedBigInteger('recolecta_estatus_id');
            $t->string('codigo')->unique();
            $t->string('nombre_rem', 20);
            $t->string('apellido_rem', 20);
            $t->string('tipo_documento_rem');
            $t->string('documento_rem');
            $t->string('telefono_rem');
            $t->string('correo_rem');
            $t->unsignedBigInteger('estado_id');
            $t->unsignedBigInteger('municipio_id');
            $t->unsignedBigInteger('parroquia_id');
            $t->unsignedBigInteger('ciudad_id');
            $t->string('codigo_postal');
            $t->string('direccion', 200);
            $t->string('referencia')->nullable();
            $t->decimal('latitud', 10, 7);
            $t->decimal('longitud', 10, 7);
            $t->decimal('precision_gps', 8, 2)->nullable();
            $t->boolean('gps_manual')->default(false);
            $t->string('nombre_dest', 20);
            $t->string('apellido_dest', 20);
            $t->string('tipo_documento_dest');
            $t->string('documento_dest');
            $t->string('telefono_dest');
            $t->string('correo_dest');
            $t->unsignedBigInteger('estado_dest_id');
            $t->unsignedBigInteger('municipio_dest_id');
            $t->unsignedBigInteger('parroquia_dest_id');
            $t->unsignedBigInteger('ciudad_dest_id');
            $t->string('codigo_postal_dest');
            $t->string('direccion_dest', 200);
            $t->string('modo_peso');
            $t->decimal('peso', 8, 3);
            $t->float('alto')->nullable();
            $t->float('ancho')->nullable();
            $t->float('largo')->nullable();
            $t->string('contenido', 300);
            $t->decimal('monto_envio', 9, 2);
            $t->decimal('monto_recoleccion', 9, 2);
            $t->decimal('iva', 9, 2);
            $t->decimal('total', 9, 2);
            $t->float('tasa_bs')->nullable();
            $t->boolean('excede_tarifa_max')->default(false);
            $t->timestamp('pago_confirmado_en')->nullable();
            $t->unsignedBigInteger('pago_confirmado_por')->nullable();
            $t->string('motivo_rechazo')->nullable();
            $t->timestamps();
        });
        Schema::create('recolecta_pagos', function (Blueprint $t) {
            $t->id('recolecta_pago_id');
            $t->unsignedBigInteger('recolecta_id');
            $t->unsignedBigInteger('tipo_pago_id');
            $t->decimal('monto', 9, 2);
            $t->string('numero_referencia', 40)->nullable();
            $t->string('banco_codigo')->nullable();
            $t->string('telefono_pagador')->nullable();
            $t->date('fecha_pago')->nullable();
            $t->string('estatus')->default('reportado');
            $t->unsignedBigInteger('confirmado_por')->nullable();
            $t->timestamp('confirmado_en')->nullable();
            $t->timestamps();
        });

        // Notificaciones de Laravel (payload text, como en producción)
        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('type');
            $t->morphs('notifiable');
            $t->text('data');
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        // Circuito de envíos (para ProcesarRecolectaService)
        Schema::create('envios', function (Blueprint $t) {
            $t->bigIncrements('envio_id');
            $t->unsignedBigInteger('servicio_id')->nullable();
            $t->string('tipo_envio')->nullable();
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->string('nombre_rem')->nullable();
            $t->string('apellido_rem')->nullable();
            $t->string('tipo_documento_rem')->nullable();
            $t->string('documento_rem')->nullable();
            $t->integer('codigo_postal_rem')->nullable();
            $t->string('estado_rem')->nullable();
            $t->string('municipio_rem')->nullable();
            $t->string('parroquia_rem')->nullable();
            $t->string('ciudad_rem')->nullable();
            $t->string('direccion_rem')->nullable();
            $t->string('correo_rem')->nullable();
            $t->string('telefono_rem')->nullable();
            $t->string('nombre_dest')->nullable();
            $t->string('apellido_dest')->nullable();
            $t->string('tipo_documento_dest')->nullable();
            $t->string('documento_dest')->nullable();
            $t->integer('codigo_postal_dest')->nullable();
            $t->unsignedBigInteger('continente_dest')->nullable();
            $t->unsignedBigInteger('pais_dest')->nullable();
            $t->unsignedBigInteger('estado_dest')->nullable();
            $t->unsignedBigInteger('municipio_dest')->nullable();
            $t->unsignedBigInteger('parroquia_dest')->nullable();
            $t->unsignedBigInteger('ciudad_dest')->nullable();
            $t->unsignedBigInteger('oficina_dest_id')->nullable();
            $t->string('direccion_dest')->nullable();
            $t->string('tlf_dest')->nullable();
            $t->string('correo_dest')->nullable();
            $t->string('servicio_expreso')->nullable();
            $t->string('peso')->nullable();
            $t->float('coste')->nullable();
            $t->string('contenido')->nullable();
            $t->unsignedBigInteger('apartado_postal')->nullable();
            $t->string('codigo_envio')->nullable();
            $t->unsignedBigInteger('tipo_saca_id')->nullable();
            $t->boolean('devolucion')->default(false);
            $t->boolean('descubierto')->default(false);
            $t->float('coste_sin_iva')->nullable();
            $t->float('tasa_bs')->nullable();
            $t->timestamps();
        });
        Schema::create('envios_iposplus', function (Blueprint $t) {
            $t->bigIncrements('envio_iposplus_id');
            $t->unsignedBigInteger('envio_id');
            $t->float('alto');
            $t->float('largo');
            $t->float('ancho');
            $t->timestamps();
        });
        Schema::create('envios_encaminamiento', function (Blueprint $t) {
            $t->bigIncrements('envios_encaminamiento_id');
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('oficina_externa_id')->nullable();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->unsignedBigInteger('viaje_id')->nullable();
            $t->unsignedBigInteger('estatus_id')->nullable();
            $t->boolean('devolucion')->default(false);
            $t->timestamps();
        });
        Schema::create('envios_almacen', function (Blueprint $t) {
            $t->bigIncrements('envio_almacen_id');
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->string('codigo')->nullable();
            $t->unsignedBigInteger('saca_id')->nullable();
            $t->boolean('estatus')->default(true);
            $t->dateTime('Entrada')->nullable();
            $t->dateTime('Salida')->nullable();
            $t->timestamps();
        });
        Schema::create('clientes', function (Blueprint $t) {
            $t->bigIncrements('cliente_id');
            $t->string('numero_documento')->nullable();
            $t->string('nombre')->nullable();
            $t->string('apellido')->nullable();
            $t->string('tipo_documento')->nullable();
            $t->string('telefono')->nullable();
            $t->string('correo')->nullable();
            $t->timestamps();
        });
        Schema::create('facturaciones', function (Blueprint $t) {
            $t->bigIncrements('facturacion_id');
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->string('nombre')->nullable();
            $t->string('apellido')->nullable();
            $t->string('tipo_documento')->nullable();
            $t->string('documento')->nullable();
            $t->string('direccion')->nullable();
            $t->float('monto_total')->nullable();
            $t->float('iva')->nullable();
            $t->timestamps();
        });
        Schema::create('facturacion_detalles', function (Blueprint $t) {
            $t->bigIncrements('facturacion_detalle_id');
            $t->unsignedBigInteger('servicio_id')->nullable();
            $t->unsignedBigInteger('facturacion_id')->nullable();
            $t->decimal('monto')->nullable();
            $t->timestamps();
        });
        Schema::create('facturacion_pagos', function (Blueprint $t) {
            $t->bigIncrements('facturacion_pago_id');
            $t->unsignedBigInteger('facturacion_id')->nullable();
            $t->unsignedBigInteger('tipo_pago_id')->nullable();
            $t->decimal('monto')->nullable();
            $t->string('numero_referencia', 40)->nullable();
            $t->timestamps();
        });
        Schema::create('facturacion_envio', function (Blueprint $t) {
            $t->bigIncrements('facturacion_envio_id');
            $t->unsignedBigInteger('facturacion_id')->nullable();
            $t->unsignedBigInteger('envio_id')->nullable();
            $t->unsignedBigInteger('oficina_id')->nullable();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->unsignedBigInteger('servicio_id')->nullable();
            $t->timestamps();
        });
        Schema::create('usuario_seguimiento', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('usuario_id')->nullable();
            $t->string('accion')->nullable();
            $t->string('descripcion')->nullable();
            $t->timestamps();
        });
    }

    // ==============================
    // Helpers de datos
    // ==============================

    protected function seedRecolectaEstatus(): void
    {
        $estados = [
            ['slug' => RecolectaEstatus::SOLICITADA, 'nombre' => 'Solicitada', 'orden' => 1],
            ['slug' => RecolectaEstatus::PAGO_REPORTADO, 'nombre' => 'Pago Reportado', 'orden' => 2],
            ['slug' => RecolectaEstatus::PAGO_CONFIRMADO, 'nombre' => 'Pago Confirmado', 'orden' => 3],
            ['slug' => RecolectaEstatus::RECOLECTADA, 'nombre' => 'Recolectada', 'orden' => 4],
            ['slug' => RecolectaEstatus::CANCELADA, 'nombre' => 'Cancelada', 'orden' => 5],
            ['slug' => RecolectaEstatus::RECHAZADA, 'nombre' => 'Rechazada', 'orden' => 6],
        ];
        foreach ($estados as $e) {
            RecolectaEstatus::firstOrCreate(['slug' => $e['slug']], $e);
        }
    }

    protected function seedParametros(float $iposplus = 60, float $recoleccion = 80, float $bcv = 41.13): void
    {
        Parametro::create(['nombre' => config('recolectas.parametro_tasa_bs'), 'valor' => $bcv, 'activo' => true]);
        Parametro::create(['nombre' => config('recolectas.parametro_iposplus'), 'valor' => $iposplus, 'activo' => true]);
        Parametro::create(['nombre' => config('recolectas.parametro_recoleccion'), 'valor' => $recoleccion, 'activo' => true]);
    }

    protected function seedTarifas(): void
    {
        foreach ([
            ['kilo_min' => 0.1, 'kilo_max' => 1, 'precio' => 2.51],
            ['kilo_min' => 1.01, 'kilo_max' => 2, 'precio' => 3.00],
        ] as $rango) {
            $tarifa = new TarifaIposplus();
            $tarifa->kilo_min = $rango['kilo_min'];
            $tarifa->kilo_max = $rango['kilo_max'];
            $tarifa->precio = $rango['precio'];
            $tarifa->activo = true;
            $tarifa->save();
        }
    }

    /**
     * Cadena estado→municipio→parroquia→ciudad→sector. Devuelve los modelos.
     */
    protected function seedUbicacion(string $sufijo = 'A', int $paisId = 90): array
    {
        $estado = Estado::create(['nombre' => "Estado {$sufijo}", 'pais_id' => $paisId, 'activo' => true]);
        $municipio = Municipio::create(['estado_id' => $estado->estado_id, 'nombre' => "Municipio {$sufijo}", 'activo' => true]);
        $parroquia = Parroquia::create(['municipio_id' => $municipio->municipio_id, 'nombre' => "Parroquia {$sufijo}", 'activo' => true]);
        $ciudad = new \App\Models\Ciudad();
        $ciudad->nombre = "Ciudad {$sufijo}";
        $ciudad->estado_id = $estado->estado_id;
        $ciudad->municipio_id = $municipio->municipio_id;
        $ciudad->save();
        $sector = new \App\Models\Sector();
        $sector->codigo_postal = '1010';
        $sector->nombre = "Sector {$sufijo}";
        $sector->parroquia_id = $parroquia->parroquia_id;
        $sector->activo = true;
        $sector->save();

        return compact('estado', 'municipio', 'parroquia', 'ciudad', 'sector');
    }

    protected function crearOficina(array $atributos = []): Oficina
    {
        return Oficina::create(array_merge([
            'codigo' => 'OP001',
            'nombre' => 'Oficina Test',
            'tipo_oficina_id' => 1,
            'direccion' => 'Dirección oficina',
            'codigo_ubicacion' => '1010',
            'zona_economica_especial' => false,
            'estatus_id' => 1,
            'operaciones' => true,
            'externa' => false,
        ], $atributos));
    }

    protected function crearUsuarioApp(array $atributos = []): UsuarioAppMovil
    {
        return UsuarioAppMovil::create(array_merge([
            'nombre' => 'Maria',
            'apellido' => 'Perez',
            'correo' => 'maria@example.com',
            'telefono' => '04141234567',
            'direccion' => 'Av. Principal, casa 1',
            'contraseña' => 'secreto123',
            'cedula' => 12345678,
            'tipo_documento' => 'V',
            'activo' => true,
        ], $atributos));
    }

    protected function tokenCliente(UsuarioAppMovil $usuario): string
    {
        return $usuario->createToken('cliente-access-test', ['cliente:access'], now()->addHour())->plainTextToken;
    }

    /**
     * Payload válido para POST /recolectas basado en una ubicación sembrada.
     */
    protected function payloadRecolecta(array $origen, array $destino, array $extra = []): array
    {
        return array_merge([
            'modo_peso' => 'manual',
            'peso' => 0.5,
            'contenido' => 'Documentos',

            'nombre_rem' => 'Maria',
            'apellido_rem' => 'Perez',
            'tipo_documento_rem' => 'V',
            'documento_rem' => '12345678',
            'telefono_rem' => '04141234567',
            'correo_rem' => 'maria@example.com',

            'estado_id' => $origen['estado']->estado_id,
            'municipio_id' => $origen['municipio']->municipio_id,
            'parroquia_id' => $origen['parroquia']->parroquia_id,
            'ciudad_id' => $origen['ciudad']->ciudad_id,
            'codigo_postal' => '1010',
            'direccion' => 'Calle 1, casa 2',
            'latitude' => 10.4806,
            'longitude' => -66.9036,
            'gps_accuracy' => 8.5,
            'gps_manual' => false,

            'nombre_dest' => 'Jose',
            'apellido_dest' => 'Gomez',
            'tipo_documento_dest' => 'V',
            'documento_dest' => '87654321',
            'telefono_dest' => '04241234567',
            'correo_dest' => 'jose@example.com',
            'estado_dest_id' => $destino['estado']->estado_id,
            'municipio_dest_id' => $destino['municipio']->municipio_id,
            'parroquia_dest_id' => $destino['parroquia']->parroquia_id,
            'ciudad_dest_id' => $destino['ciudad']->ciudad_id,
            'codigo_postal_dest' => '1010',
            'direccion_dest' => 'Calle 3, casa 4',
        ], $extra);
    }
}
