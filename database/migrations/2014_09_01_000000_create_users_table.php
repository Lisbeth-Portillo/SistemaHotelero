<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Tabla rol
        Schema::create('rol', function (Blueprint $table) {
            $table->bigIncrements('idRol');
            $table->string('rol', 15);
        });

        //Tabla users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', ['0', '1'])->default(1);
            $table->string('foto', 100);
            $table->foreignId('rol_id')->references('idRol')->on('rol')//Llave foranea tabla rol-idRol
            ->onUpdate('cascade');//Actualizacion de datos en cascada
            $table->foreignId('empleado_id')->references('idEmpleado')->on('empleado')//Llave foranea tabla empleado-idEmpleado
            ->onUpdate('cascade');//Actualizacion de datos en cascada
            $table->rememberToken();
            $table->timestamps();
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
        Schema::dropIfExists('rol');
        Schema::dropIfExists('users');
    }
}
