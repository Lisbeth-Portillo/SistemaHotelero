<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
     //Definicion de los campos de la tabla
     public $table = "empleado";
     public $timestamps = false;
     protected $primaryKey = 'idEmpleado';
}
