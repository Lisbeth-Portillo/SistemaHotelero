<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobante', function (Blueprint $table) {
            $table->bigIncrements('idComprobante');
            $table->foreignId('reserva_id')->references('idReserva')->on('reserva')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->decimal('subTotal', 5, 2);
            $table->decimal('total', 5, 2);
            $table->integer('mora')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobante');
    }
}
