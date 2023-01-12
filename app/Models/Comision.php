<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Comision extends Model{
    public static function getData($Ruta,$Mes,$Anno)
    {
        $i=0;

        $RutaArray  = array();

        $Vendedor   = Vendedor::getVendedor();
        
        foreach ($Vendedor as $v){
            
            $Salariobasico = 5000 ;

            $RutaArray[$i]['VENDEDOR']                   = $v->VENDEDOR;
            $RutaArray[$i]['NOMBRE']                     = $v->NOMBRE;
            $RutaArray[$i]['BASICO']                     = $Salariobasico;
            $RutaArray[$i]['DATARESULT']                 = Comision::CalculoCommision($v->VENDEDOR,$Mes,$Anno,$Salariobasico);
            
            $i++;
        }
        
        
        return $RutaArray;
    }

    public static function CalculoCommision($Ruta,$Mes,$Anno,$Salariobasico)
    {

        $data                       = array();
        $RutaArray  = array();
        $Comision_de_venta          = array();
        $recuperacion_de_credito    = array();
        $recuperacion_de_contado    = array();
        $i=0;
        
        $query = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.fn_comision_calc_8020 "'.$Mes.'","'.$Anno.'","'.$Ruta.'", "'.'N/D'.'" ');

        $qCobertura = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.fn_comision_calc_BonoCobertura "'.$Ruta.'"');



        $proncCUMple = (count($qCobertura )>0) ? $qCobertura[0]->CUMPLI : 0 ;
        $proncCUMple = number_format($proncCUMple,2);

        $qCobertura  = (count($qCobertura )>0) ? $qCobertura[0]->isCumple : 0 ;
        
        
        $Array_articulos_cumplen = array_filter($query,function($item){
            if($item->isCumpl=='SI'){
                return $item;
            }
        });


        $Array_articulos_lista80 = array_filter($query,function($item){
            if($item->Lista=='80'){
                return $item;
            }
        });

        $Array_articulos_lista20 = array_filter($Array_articulos_cumplen,function($item){
            if($item->Lista=='20'){
                return $item;
            }
        });

        $Array_countItem80 = array_filter($Array_articulos_cumplen,function($item){
            if($item->Lista=='80'){
                return $item;
            }
        });
        

        $count_articulos_lista80            = count($Array_articulos_lista80); 
        $sum_venta_articulos_lista80        = array_sum(array_column($Array_articulos_lista80,'VentaVAL'));
        $factor_comision_venta_lista80      = Comision::NivelFactorComision($count_articulos_lista80);
        
        $count_articulos_lista20            = count($Array_articulos_lista20); 
        $sum_venta_articulos_lista20        = array_sum(array_column($Array_articulos_lista20,'VentaVAL'));
        $factor_comision_venta_lista20      = Comision::NivelFactorComision($count_articulos_lista20);
    
        $Total_articulos_cumplen            = count($Array_articulos_cumplen); 
        $sum_venta_articulos_Total          = $sum_venta_articulos_lista80 + $sum_venta_articulos_lista20;
        $factor_comision_venta_Total        = $factor_comision_venta_lista80 + $factor_comision_venta_lista20;

        $Comision80 = ($sum_venta_articulos_lista80 * $factor_comision_venta_lista80) / 100;
        $Comision20 = ($sum_venta_articulos_lista20 * $factor_comision_venta_lista20) / 100;

        $ttComision = $Comision80 + $Comision20;

        
        
        $Comision_de_venta = [
            'Lista80' => [
                count($Array_countItem80),
                number_format($sum_venta_articulos_lista80, 2),
                $factor_comision_venta_lista80,
                number_format($Comision80,2)
            ],
            'Lista20' => [
                $count_articulos_lista20,
                number_format($sum_venta_articulos_lista20, 2),
                $factor_comision_venta_lista20,
                number_format($Comision20,2)
            ],
            'Total' => [
                $Total_articulos_cumplen,
                number_format($sum_venta_articulos_Total, 2),
                $factor_comision_venta_Total,
                number_format($ttComision,2)
            ]
        ];

        $recuperacion_credito = 850000;
        $factor_comision_recuperacion_credito = 1;
        $comision_credito = ($recuperacion_credito * $factor_comision_recuperacion_credito) / 100;

        $recuperacion_contado = 50000;
        $factor_comision_recuperacion_contado = 3;
        $comision_contado = ($recuperacion_contado * $factor_comision_recuperacion_contado) / 100;

        $Bono_de_cobertura = Comision::BonoCobertura($qCobertura);
        $ttComisiones = $ttComision + $comision_credito + $comision_contado ;
        $ComisionesMasBonos = ($Bono_de_cobertura + $ttComisiones);

        $recuperacion_de_credito = [
            number_format($recuperacion_credito,0),
            $factor_comision_recuperacion_credito,
            number_format($comision_credito,0)
        ];

        $recuperacion_de_contado = [
            number_format($recuperacion_contado,0),
            $factor_comision_recuperacion_contado,
            number_format($comision_contado,0)
        ];

        $Totales_finales = [
            number_format($Bono_de_cobertura,0),
            number_format($ttComisiones,0),
            number_format($ComisionesMasBonos,0),
            $proncCUMple
        ];

        $RutaArray['Comision_de_venta']          = $Comision_de_venta ;
        $RutaArray['recuperacion_de_credito']    = $recuperacion_de_credito ;
        $RutaArray['recuperacion_de_contado']    = $recuperacion_de_contado ;
        $RutaArray['Totales_finales']            = $Totales_finales ;
        $RutaArray['Total_Compensacion']         = number_format(($Salariobasico + $ComisionesMasBonos),0);

        
        
        return $RutaArray;
    }
    public static function BonoCobertura($cump)
    {
    

        $valor_pagar = 0;

        if ($cump >= 100) {
            $valor_pagar = 3500;
        } elseif ($cump >= 90 && $cump < 100) {
            $valor_pagar = 3150;
        } elseif ($cump >= 80 && $cump < 90) {
            $valor_pagar = 2800;
        } elseif ($cump < 80) {
            $valor_pagar = 0;
        }

        return $valor_pagar;

    }

    public static function NivelFactorComision($Valor)
    {
        $factor = Factor::where('min', '<=', $Valor)->where('max', '>=', $Valor)->first();

        if ($factor) {
            return $factor->valor;
        }

    }
}