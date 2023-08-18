<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lotes extends Model
{
    protected $connection = 'sqlsrv_dev';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_lotes";

    public static function getLotes($BODEGA,$ARTICULO)
    {  
        $query = Lotes::where('BODEGA',$BODEGA)->where('ARTICULO',$ARTICULO)->get();
        $i = 0;
        $json = array();
        foreach ($query as $fila) {
            $json["data"][$i]["ARTICULO"] = $fila["ARTICULO"];
            $json["data"][$i]["BODEGA"] = $fila["BODEGA"];
            $json["data"][$i]["CANT_DISPONIBLE"] = number_format($fila["CANT_DISPONIBLE"], 2);
            $json["data"][$i]["LOTE"] = $fila["LOTE"];
            $json["data"][$i]["FECHA_INGRESO"] = date('d/m/Y',strtotime($fila["FECHA_ENTR"]));
            $json["data"][$i]["CANTIDAD_INGRESADA"] = number_format($fila["CANTIDAD_INGRESADA"], 2);
            $json["data"][$i]["FECHA_ENTRADA"] = date('d/m/Y',strtotime($fila["FECHA_ENTRADA"]));
            $json["data"][$i]["FECHA_VENCIMIENTO"] = date('d/m/Y',strtotime($fila["FECHA_VENCIMIENTO"]));
            $i++;
        }
        return $json;
    }

    public static function nextExpiringLot($ARTICULO,$CANTIDAD)
    {  
        $json  = array();

        $qLote = Lotes::where('ARTICULO', $ARTICULO)->where('BODEGA', '002')->where('CANT_DISPONIBLE', '>=', $CANTIDAD)->orderBy('fecha_vencimiento', 'DESC')->first();

        $json  = array(
            'LOTE' => (isset($qLote)) ? $qLote->LOTE : 'null'  ,
            'BODEGA' =>(isset($qLote)) ? $qLote->BODEGA : '002'   ,
        );

        
        return $qLote;
    }
}
