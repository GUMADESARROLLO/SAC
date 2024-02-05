<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class uFACTURA_LINEA extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_sac_devoluciones";


    public static function getData(Request $request)
    {  
        $array = [];

        $FCT    = $request->input('FACTURA');

        $result = uFACTURA_LINEA::WHERE('FACTURA',$FCT)->get();

        foreach ($result as $key => $row) {
            $array[$key]['FACTURAS'] = $row->FACTURA;
            #$array[$key]['FCT_DATE'] = Date::parse($row->FECHA_FACTURA)->format('D, M d, Y');
            $array[$key]['FCT_DATE'] = $row->FECHA_FACTURA;
            $array[$key]['FCT_RUTA'] = $row->VENDEDOR;
            $array[$key]['FCT_NAME'] = $row->NOMBRE;
            $array[$key]['FCT_ARTI'] = $row->ARTICULO;
            $array[$key]['FCT_DESC'] = strtoupper($row->DESCRIPCION);
            $array[$key]['FCT_CANT'] = number_format($row->CANTIDAD,2);
            $array[$key]['FCT_LOTE'] = $row->LOTE;
        }

        return $array;
    }

}
