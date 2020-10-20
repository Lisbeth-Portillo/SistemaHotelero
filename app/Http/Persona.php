<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
     //Definicion de los campos de la tabla
     public $table = "persona";
     public $timestamps = false;
     protected $primaryKey = 'idPersona';
     protected $fillable = ['identificacion_id','nombres','apellidos', 'identificacion'];
}
