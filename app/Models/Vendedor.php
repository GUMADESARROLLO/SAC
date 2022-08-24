<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Vendedores extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.vtVS2_Vendedores";


    public static function getVendedor()
    {  
        return Vendedores::whereNotIn('VENDEDOR',['F01','F02','F04'])->get();;
    }
}
