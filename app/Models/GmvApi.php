<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GmvApi extends Model
{  
    public static function Articulos($CODIGO_RUTA)
    {
        $PROYECTO_B = array("F19", "F21", "F22", "F23");
        $Lista = (in_array($CODIGO_RUTA , $PROYECTO_B)) ? '20' : '80' ;

        $EXCENTOS   = array("F02", "F04", "F11", "F20","F18");
        $isExcentos = (in_array($CODIGO_RUTA , $EXCENTOS)) ? true : false;

        $json = array();
        $i=0;
        
        $Lista_Articulos = array();

        $Lotes ="  :0:N/D";

        

        if ($isExcentos) {
            $query = GmvArticulos::where('EXISTENCIA', '>' ,1)->orWhere('ARTICULO', 'like', 'VU%')->orderByRaw('CALIFICATIVO,DESCRIPCION ASC')->get();
            $RutaAsignada = $CODIGO_RUTA;
        } else {
            $Rutas = DB::table('gumadesk.tlb_rutas_asignadas')->where('Ruta', $CODIGO_RUTA)->get()->first();
            $RutaAsignada = $Rutas->Ruta_asignada;
            
            $Result_Articulos = DB::table('gumadesk.tbl_listas_articulos_rutas')->where('Ruta', $RutaAsignada)->where('Lista', $Lista)->get();
            
            foreach ($Result_Articulos as $rec){
                $Lista_Articulos[] = $rec->Articulo;
            }        

            $query = GmvArticulos::whereIn('ARTICULO',$Lista_Articulos)->get();            
        }
        

        
        foreach ($query as $fila){
            $set_img ="SinImagen.png";
            $set_des = "";

            $Info_Articulo = DB::table('ecommerce_android_app.tbl_product')->where('product_sku', $fila["ARTICULO"])->get();

            if ($Info_Articulo->count()) {
                $set_img = $Info_Articulo[0]->product_image;
                $set_des = $Info_Articulo[0]->product_description;
            }

            $qPromo = DB::table('ecommerce_android_app.tbl_news')->where('banner_sku', $fila["ARTICULO"])->get();
            $isPromo = ($qPromo->count() >= 1) ? "S" : "N" ;            
            $Precio_Articulo = (strpos($fila["ARTICULO"], "VU") !== false) ? 1 : $fila['PRECIO_IVA'] ;
            $Existe_Articulo = (strpos($fila["ARTICULO"], "VU") !== false) ? 999 : $fila['EXISTENCIA'] ;

            if (strpos($fila["ARTICULO"], "VU") !== false) {
                $set_des ='
                <!DOCTYPE html>
                    <html>
                    <head>
                        <style type="text/css">
                        .alert-box {
                            color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:11px;
                            padding:10px 36px;
                            margin:10px;
                        }
                        .alert-box span {
                            font-weight:bold;
                            text-transform:uppercase;
                        }
                        .error {
                            border:3px solid #f5aca6;
                        }
                        </style>
                    </head>
                    <body>
                        <div class="alert-box error"><span>Importante: </span>Los Valores de precio y Existencia son informativos.</div>
                    </body>
                </html>';
            }

            $val_viñeta = "C$ 40.00";
            $isPromo ="N";


            $json[$i]['product_id']               = $fila["ARTICULO"];
            $json[$i]['product_name']             = strtoupper($fila['DESCRIPCION']);
            $json[$i]['category_id']              = "20";
            $json[$i]['category_name']            = "Medicina";
            $json[$i]['product_price']            = number_format($Precio_Articulo,2,'.','');
            $json[$i]['product_status']           = "Available";
            $json[$i]['product_image']            = $set_img;
            $json[$i]['product_description']      = $set_des;
            $json[$i]['product_quantity']         = str_replace(',', '', number_format($Existe_Articulo,2));
            $json[$i]['currency_id']              = "105";
            $json[$i]['tax']                      = "0";
            $json[$i]['currency_code']            = "NIO";
            $json[$i]['currency_name']            = "Nicaraguan cordoba oro";
            $json[$i]['product_bonificado']       = $fila["REGLAS"];
            //$json[$i]['product_lotes']            = trim($fila["LOTES"]);
            $json[$i]['product_lotes']            = $Lotes;
            $json[$i]['product_und']              = $fila["UNIDAD_MEDIDA"];
            $json[$i]['CALIFICATIVO']             = $fila["CALIFICATIVO"];
            $json[$i]['ISPROMO']                  = $isPromo. ":" . $val_viñeta . ":" . $RutaAsignada;
            $json[$i]['LAB']                      = $fila["LABORATORIO"];

            $i++;
            
        }

        return $json;    
        
    }
}