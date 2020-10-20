<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Tabla identificacion
        Schema::create('identificacion', function (Blueprint $table) {
            $table->bigIncrements('idIdentificacion');
            $table->string('tipoIdentificacion', 15);
        });

         //Tabla persona
         Schema::create('persona', function (Blueprint $table) {
            $table->bigIncrements('idPersona');
            $table->foreignId('identificacion_id')->references('idIdentificacion')->on('identificacion')//Llave foranea tabla identificacion
            ->onUpdate('cascade');//Actualizacion de datos en cascada
            $table->string('nombres', 25);
            $table->string('apellidos', 25);
            $table->string('identificacion', 20)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('identificacion');
        Schema::dropIfExists('persona');
    }
}
