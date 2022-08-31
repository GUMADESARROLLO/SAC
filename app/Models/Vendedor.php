<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Vendedor extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.vtVS2_Vendedores";


    public static function getVendedor()
    {  
        return Vendedor::whereNotIn('VENDEDOR',['F01','F02'])->get();
    }
}
