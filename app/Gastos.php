<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    //Definicion de los campos de la tabla
    public $table = "gastos";
    public $timestamps = false;
    protected $primaryKey = 'idGastos';
}
