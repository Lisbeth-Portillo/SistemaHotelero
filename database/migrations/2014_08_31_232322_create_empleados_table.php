<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

         //Tabla puesto
         Schema::create('puesto', function (Blueprint $table) {
            $table->bigIncrements('idPuesto');
            $table->string('puesto', 25);
            $table->integer('salario');
        });

        //Tabla empleado
        Schema::create('empleado', function (Blueprint $table) {
            $table->bigIncrements('idEmpleado');
            $table->foreignId('persona_id')->references('idPersona')->on('persona')//Llave foranea tabla puesto
            ->onUpdate('cascade');//Actualizacion de datos en cascada
            $table->foreignId('puesto_id')->references('idPuesto')->on('puesto')//Llave foranea tabla puesto
            ->onUpdate('cascade');//Actualizacion de datos en cascada
            $table->string('profesion');
            $table->string('direccion', 60);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('telefono', 10);
            $table->date('fechaN');
            $table->enum('estado', ['0', '1'])->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('puesto');
        Schema::dropIfExists('empleado');
    }
}
