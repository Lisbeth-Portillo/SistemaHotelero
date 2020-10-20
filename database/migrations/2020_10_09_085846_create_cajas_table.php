<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->bigIncrements('idCaja');
            $table->foreignId('usuario_id')->references('id')->on('users')//Llave foranea
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->string('nombre', 15);
            $table->decimal('montoA', 5, 2);
            $table->decimal('ganancias', 5, 2);
            $table->decimal('perdidas', 5, 2);
            $table->decimal('total', 5, 2);
            $table->timestamp('fechaA')->useCurrent();
            $table->timestamp('fechaC')->nullable();
            $table->enum('estado', ['0', '1'])->default(0); // Caja activa = 0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caja');
    }
}
