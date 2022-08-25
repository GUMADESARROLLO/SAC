<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liquidacion_a_6meses extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.Vencimientos_6meses";

    public static function getArticulos()
    {

        return Liquidacion_a_6meses::all();
    }
}
