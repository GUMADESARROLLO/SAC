<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Clientes extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb4_master_cliente";

    public static function getClientes()
    {

        return Clientes::all();
    }
    public static function getPlanCrecimiento($Cliente){

        $dta        = array(); 
        $dta_month  = array(); 
        $isPlan = false;

        $i=0;
        $ttOtal = 0;
        $isExists = PlanCrecimiento::where('CODIGO_CLIENTE',$Cliente)->count();
        if (Session::get('rol') == '1'  ){
            if ($isExists!=0) {
                $isPlan = true;
                $Q01="SELECT * FROM PRODUCCION.dbo.view_plan_crecimiento WHERE CLIENTE_CODIGO='".$Cliente ."'";
        
                $Q02="SELECT month(T0.Fecha_de_Factura) number_month,SUBSTRING(t0.MES,0,4) name_month,t0.[AÑO] annio,sum(T0.VentaNetaLocal) ttMonth 
                FROM Softland.dbo.ANA_VentasTotales_MOD_Contabilidad_UMK T0 WHERE t0.[AÑO] = YEAR(GETDATE())  AND  T0.Fecha_de_Factura >= '2022-07-01 00:00:00.000' 
                AND T0.CLIENTE_CODIGO= '".$Cliente ."' and T0.VentaNetaLocal  > 0
                GROUP BY month(T0.Fecha_de_Factura),t0.MES,t0.[AÑO] ORDER BY month(T0.Fecha_de_Factura)";
    
                $rCrecimiento = DB::connection('sqlsrv')->Select($Q01);
                foreach ($rCrecimiento as $key) {
                    $dta['EVALUADO']      = ceil($key->EVALUADO);
                    $dta['CRECIMIENTO']      = ceil($key->CRECIMIENTO);
                    $dta['COMPRA_MIN']      = ceil($key->COMPRA_MIN);
                    $dta['PROM_CUMP']      = ceil(number_format($key->PROM_CUMP,0));
                }
    
                $rCreciMeses = DB::connection('sqlsrv')->Select($Q02);
                foreach ($rCreciMeses as $key) {        
                    $dta_month[$i]['number_month']    = $key->number_month;      
                    $dta_month[$i]['name_month']    =$key->name_month;      
                    $dta_month[$i]['annio']    = $key->annio;      
                    $dta_month[$i]['ttMonth']      = ceil($key->ttMonth);

                    $ttOtal +=$key->ttMonth;
                    $i++;
                }
            }
        }
        

        
    
        $dtaBodega[] = array(
            'isPlan'        => $isPlan,
            'InfoCliente'   => $dta,
            'SalesMonths'   => $dta_month,
            'ttactual'      => ceil($ttOtal)
        );

        return $dtaBodega;

    }
}
