<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NivelPrecio extends Model{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_precio";

    public static function getNivelPrecio($IdArticulo)
    {
        $Rol = 1;
        if ($Rol==1) {
            return DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_iweb_precios '$IdArticulo' ");
        } else {
            return NivelPrecio::where('ARTICULO',$IdArticulo)->whereIn('NIVEL_PRECIO',['FARMACIA','PUBLICO','MAYORISTA'])->get();
        }
    }
}