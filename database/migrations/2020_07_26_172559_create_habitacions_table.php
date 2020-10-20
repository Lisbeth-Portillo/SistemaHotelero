<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Tabla nivel
        Schema::create('nivel', function (Blueprint $table) {
            $table->bigIncrements('idNivel');
            $table->string('nombre', 15);
            $table->string('numeroNivel', 15);
            $table->enum('estado', ['0', '1'])->default(0);
        });

        //Tabla tipo habitacion
        Schema::create('tipoHabitacion', function (Blueprint $table) {
            $table->bigIncrements('idTipoHabitacion');
            $table->string('habitacion', 20);
            $table->string('descripcion', 100);
            $table->decimal('precio', 5, 2);
        });

        //Tabla habitacion
        Schema::create('habitacion', function (Blueprint $table) {
            $table->bigIncrements('idHabitacion');
            $table->string('nombre',5)->unique();
            $table->foreignId('nivel_id')->references('idNivel')->on('nivel') //Llave foranea tabla nivel-idNivel
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->foreignId('tipoHabitacion_id')->references('idTipoHabitacion')->on('tipoHabitacion')//Llave foranea tabla tipo habitacion-idTipoHabitacion
            ->onUpdate('cascade'); //Actualizacion de datos en cascada
            $table->enum('estado', ['0', '1','2','3'])->default(0); // 0 = habilidada, 1 = manteniminto, 2 = ocupado, 3 = limpieza


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nivel');
        Schema::dropIfExists('tipoHabitacion');
        Schema::dropIfExists('habitacion');
    }
}
