<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    //Definicion de los campos de la tabla
    public $table = "hotel";
    public $timestamps = false;
    protected $primaryKey = 'idHotel';

}
