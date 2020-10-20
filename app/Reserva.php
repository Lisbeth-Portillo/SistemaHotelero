<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    //Definicion de los campos de la tabla
    public $table = "reserva";
    public $timestamps = false;
    protected $primaryKey = 'idReserva';
}
