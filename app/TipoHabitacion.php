<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    //Definicion de los campos de la tabla

    public $timestamps = false;
    public $table = "tipohabitacion";
    protected $primaryKey = 'idTipoHabitacion';
}
