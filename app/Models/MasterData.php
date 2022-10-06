<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterData extends Model{
    public static function getData($Ruta,$d1,$d2)
    {
        $data = array();
        $i=0;
        $Clasi = 0;

        $query = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.fn_calc_8020 "'.$d1.'","'.$d2.'","'.$Ruta.'"  ');

        foreach ($query as $key) {

            $Clasi +=$key->Aporte;
            $setList = ($Clasi<=80) ? 80 : 20 ;
            $data[$i]['ARTICULO'] = $key->ARTICULO;
            $data[$i]['DESCRIPCION'] = $key->DESCRIPCION;
            $data[$i]['Venta'] = $key->Venta;
            $data[$i]['Aporte'] = $key->Aporte;
            $data[$i]['Lista'] = $setList;

            $i++;
        }

        return $data;
    }

}