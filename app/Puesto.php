<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
     //Definicion de los campos de la tabla
     public $table = "puesto";
     public $timestamps = false;
     protected $primaryKey = 'idPuesto';
}
