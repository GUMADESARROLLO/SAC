<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Logs extends Model {
    protected $connection = 'mysql_pedido';    
    protected $table = "tbl_logs";



    public static function dtaReporte($d1,$d2){

        $Visitas = Logs::select('RUTA', 
                DB::raw("count(CASE WHEN MODULO = 'Comisiones' THEN MODULO END) AS Comisiones"), 
                DB::raw("count(CASE WHEN MODULO = 'PlanCrecimiento' THEN MODULO END) AS PlanCrecimiento"), 
                DB::raw("count(MODULO) AS tTotal"))
            ->whereBetween('FECHA', [$d1, $d2])
            ->groupBy('RUTA')
            ->get();

    
        $json = array();
        $i = 0;

        if (count($Visitas) > 0) {
            foreach ($Visitas as $key => $value) {

                $name = Vendedor::where('VENDEDOR',$value->RUTA)->get()->pluck('NOMBRE')[0];
                $zona = Vendedor::getZonas($value->RUTA)[0]->Zona;

                $json[$i]['RUTA']     = $value->RUTA;
                $json[$i]['Nombre']     = $name;
                $json[$i]['zona']     = $zona;

                $json[$i]['Comisiones']   = $value->Comisiones;
                $json[$i]['PlanCrecimiento']   = $value->PlanCrecimiento;
                $json[$i]['tTotal']   = $value->tTotal;
                

                $i++;
            }
        }
        return $json;

    }
}