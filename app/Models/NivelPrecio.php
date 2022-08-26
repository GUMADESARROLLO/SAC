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
        $filterNivelPrecio = array();
        $ShowListView = ['','FARMACIA','MAYORISTA','INST.PUB. C$','PUBLICO'];
        $NivelesPrecio = DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_iweb_precios '$IdArticulo' ");
        $i = 0;        
        foreach ($NivelesPrecio as $rec){
            if (array_search($rec->NIVEL_PRECIO, $ShowListView) > 0) {
                $filterNivelPrecio[$i]['NIVEL_PRECIO']  = $rec->NIVEL_PRECIO;
                $filterNivelPrecio[$i]['PRECIO']        = number_format($rec->PRECIO,2);
                $i++;
            }
        }

        return $filterNivelPrecio;
    }
}