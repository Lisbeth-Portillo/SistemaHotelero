<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


class Habitacion extends Model
{
    //Definicion de los campos de la tabla
    public $table = "habitacion";
    public $timestamps = false;
    protected $primaryKey = 'idHabitacion';
}
