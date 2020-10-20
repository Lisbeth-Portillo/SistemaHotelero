<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
     //Definicion de los campos de la tabla
     public $table = "caja";
     public $timestamps = false;
     protected $primaryKey = 'idCaja';
}
