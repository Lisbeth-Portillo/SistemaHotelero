<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
     //Definicion de los campos de la tabla
     public $table = "nivel";
     public $timestamps = false;
     protected $primaryKey = 'idNivel';
}
