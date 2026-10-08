<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla pivote que asocia un envio "al descubierto" (suelto, sin valija) a un
     * despacho (numero_despacho_id). Un envio suelto viaja con el despacho como si
     * fuese una valija y debe reflejarse en la guia del despacho.
     *
     * Cada asociacion es una fila PERMANENTE por tramo: reasignar un envio a otro
     * despacho en una oficina posterior crea una fila NUEVA, nunca modifica ni borra
     * las anteriores. Asi la guia de la oficina que ya dio salida al despacho queda
     * inmutable (mismo patron que sacas.numero_despacho_id, que nunca cambia).
     *
     * La regla "un solo despacho abierto a la vez" por envio la garantiza la logica
     * de la aplicacion, no la BD, porque el historico exige varias filas por envio_id.
     */
    public function up(): void
    {
        Schema::create('envio_descubierto_despacho', function (Blueprint $table) {
            $table->id('envio_descubierto_despacho_id');
            $table->foreignId('envio_id')->references('envio_id')->on('envios');
            $table->foreignId('numero_despacho_id')->references('numero_despacho_id')->on('numeros_despacho_oficina');
            $table->timestamps();

            // Evita duplicar la MISMA asociacion (envio + despacho). No impide que un
            // envio tenga filas hacia despachos distintos a lo largo de su recorrido.
            $table->unique(['envio_id', 'numero_despacho_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_descubierto_despacho');
    }
};
