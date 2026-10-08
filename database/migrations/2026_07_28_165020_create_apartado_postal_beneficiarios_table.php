<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartado_postal_beneficiarios', function (Blueprint $table) {
            $table->id('apartado_postal_beneficiario_id');

            // Titular del apartado (registros_apartados). El beneficiario está
            // autorizado a recibir correspondencia en el mismo casillero.
            $table->unsignedBigInteger('registro_apartado_id');

            $table->string('nombre');
            $table->string('apellido');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Si se elimina el titular, sus beneficiarios se eliminan con él.
            $table->foreign('registro_apartado_id')
                ->references('registro_apartado_id')->on('registros_apartados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartado_postal_beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['registro_apartado_id']);
        });

        Schema::dropIfExists('apartado_postal_beneficiarios');
    }
};
