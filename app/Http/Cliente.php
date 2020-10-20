<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //Definicion de los campos de la tabla
    public $table = "cliente";
    public $timestamps = false;
    protected $primaryKey = 'idCliente';
}
