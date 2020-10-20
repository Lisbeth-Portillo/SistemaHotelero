<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->bigIncrements('idGastos');
            $table->foreignId('usuario_id')->references('id')->on('users')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->foreignId('caja_id')->references('idCaja')->on('caja')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->string('nombre', 20);
            $table->string('descripcion', 35);
            $table->decimal('precio', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gastos');
    }
}
