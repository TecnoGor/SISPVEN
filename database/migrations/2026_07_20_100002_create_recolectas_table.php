<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Solicitudes de recolecta Iposplus creadas desde la app de clientes.
     *
     * Tabla propia (no una fila prematura en `envios`): el solicitante vive
     * en sispven_app.usuarios (los ids de `users` y `sispven_app.usuarios`
     * son poblaciones distintas). Al procesarse en la oficina, la recolecta
     * se convierte en un envío real y se llena `envio_id`.
     */
    public function up(): void
    {
        Schema::create('recolectas', function (Blueprint $table) {
            $table->id('recolecta_id');
            // Nullable + nullOnDelete: el endpoint existente /api/app/eliminar-cuenta
            // hace borrado duro del usuario; la recolecta queda como historial sin dueño.
            $table->unsignedBigInteger('usuario_app_id')->nullable();
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id')->nullable();
            $table->unsignedBigInteger('recolecta_estatus_id');
            $table->string('codigo')->unique();

            // Remitente (dueño del paquete)
            $table->string('nombre_rem', 20);
            $table->string('apellido_rem', 20);
            $table->string('tipo_documento_rem');
            $table->string('documento_rem');
            $table->string('telefono_rem');
            $table->string('correo_rem');

            // Punto de recolecta (ubicación del cliente; sustituye al
            // "origen = oficina" del flujo de taquilla)
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->unsignedBigInteger('ciudad_id');
            $table->string('codigo_postal');
            $table->string('direccion', 200);
            $table->string('referencia')->nullable();

            // GPS del punto de recolecta (payload API en inglés, columnas en
            // español — convención de envio_evidencias / intentos_entregas)
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('precision_gps', 8, 2)->nullable();
            $table->boolean('gps_manual')->default(false);

            // Destinatario
            $table->string('nombre_dest', 20);
            $table->string('apellido_dest', 20);
            $table->string('tipo_documento_dest');
            $table->string('documento_dest');
            $table->string('telefono_dest');
            $table->string('correo_dest');
            $table->unsignedBigInteger('estado_dest_id');
            $table->unsignedBigInteger('municipio_dest_id');
            $table->unsignedBigInteger('parroquia_dest_id');
            $table->unsignedBigInteger('ciudad_dest_id');
            $table->string('codigo_postal_dest');
            $table->string('direccion_dest', 200);

            // Paquete
            $table->string('modo_peso'); // 'manual' | 'volumetrico'
            $table->decimal('peso', 8, 3); // kg (se multiplica x1000 al crear el Envio)
            $table->float('alto')->nullable();
            $table->float('ancho')->nullable();
            $table->float('largo')->nullable();
            $table->string('contenido', 300);

            // Snapshot de la cotización (se cobra lo cotizado al crear)
            $table->decimal('monto_envio', 9, 2);
            $table->decimal('monto_recoleccion', 9, 2);
            $table->decimal('iva', 9, 2);
            $table->decimal('total', 9, 2);
            $table->float('tasa_bs', 9, 2)->nullable();
            $table->boolean('excede_tarifa_max')->default(false);

            // Pago por adelantado (confirmación manual de la oficina;
            // hook para la verificación automática futura)
            $table->timestamp('pago_confirmado_en')->nullable();
            $table->unsignedBigInteger('pago_confirmado_por')->nullable();

            $table->string('motivo_rechazo')->nullable();
            $table->timestamps();

            $table->foreign('usuario_app_id')->references('usuario_id')->on('sispven_app.usuarios')->nullOnDelete();
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('recolecta_estatus_id')->references('recolecta_estatus_id')->on('recolecta_estatus');
            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');
            $table->foreign('ciudad_id')->references('ciudad_id')->on('ciudades');
            $table->foreign('estado_dest_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_dest_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_dest_id')->references('parroquia_id')->on('parroquias');
            $table->foreign('ciudad_dest_id')->references('ciudad_id')->on('ciudades');
            $table->foreign('pago_confirmado_por')->references('id')->on('users');

            $table->index('usuario_app_id');
            $table->index('oficina_id');
            $table->index('recolecta_estatus_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recolectas');
    }
};
