<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserva', function (Blueprint $table) {
            $table->bigIncrements('idReserva');
            $table->foreignId('cliente_id')->references('idCliente')->on('cliente')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->foreignId('habitacion_id')->references('idHabitacion')->on('habitacion')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->foreignId('usuario_id')->references('id')->on('users')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->enum('estadopago', ['0', '1']);
            $table->enum('estadoreserva', ['0', '1']);
            $table->date('fregistro')->nullable();
            $table->time('hregistro')->nullable();
            $table->date('fllegada');
            $table->time('hllegada');
            $table->date('fsalida')->nullable();
            $table->time('hsalida')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserva');
    }
}
