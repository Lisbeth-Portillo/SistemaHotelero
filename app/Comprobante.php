<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    //Definicion de los campos de la tabla
    public $table = "comprobante";
    public $timestamps = false;
    protected $primaryKey = 'idComprobante';
    protected $fillable = [
        'reserva_id',
        'caja_id',
        'subTotal',
        'total',
        'mora',
      ];
}
