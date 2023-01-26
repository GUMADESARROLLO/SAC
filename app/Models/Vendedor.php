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
        return Vendedor::whereNotIn('VENDEDOR',['F01','F12','F02','F18',"F15",'F24',"F21","F23","F22","F19"])->get();
        //return Vendedor::whereIn('VENDEDOR',['F05'])->get();
    }
}
