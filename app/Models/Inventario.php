<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Inventario extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_articulos";

    public static function getArticulos()
    {
        return Inventario::get();
    }
    
    public static function getArticulosSinFav()
    {
        $articulos_favoritos = array();
        $lista_negra = array();

        $Lista_articulos_Favoritos = ArticuloFavoritos::all();
        $getListaNegra = DB::connection('sqlsrv')->select('SELECT * FROM PRODUCCION.dbo.tbl_articulos_lista_negra');

        foreach ($Lista_articulos_Favoritos as $rec){
            $articulos_favoritos[] = $rec->Articulo;
        }
        foreach($getListaNegra as $lista){
            $lista_negra[] = $lista->articulo;
        }
        return Inventario::whereNotIn('ARTICULO',$articulos_favoritos)->whereNotIn('ARTICULO',$lista_negra)->get();
        
    }
    public static function getArticulosFavoritos()
    {
        $articulos_favoritos = array();
        $listaArticulos = $json =  array();
        $i = 0;
       

        $Lista_articulos_Favoritos = ArticuloFavoritos::all();
        
        foreach ($Lista_articulos_Favoritos as $rec){
            $articulos_favoritos[] = $rec->Articulo;
        }
         
        
        $listaArticulos = Inventario::whereIn('ARTICULO',$articulos_favoritos)->get();
        $Info_Articulo = DB::table('ecommerce_android_app.tbl_product')->whereIn('product_sku', $articulos_favoritos)->get();
        foreach($listaArticulos as $item){
            $img = "item.png";
            $json[$i]['ARTICULO'] = $item->ARTICULO;
            $json[$i]['CLASE_TERAPEUTICA'] = $item->CLASE_TERAPEUTICA;
            $json[$i]['DESCRIPCION'] = $item->DESCRIPCION;
            $json[$i]['total'] = $item->total;
            $json[$i]['UNIDADES'] = $item->UNIDADES;
            $json[$i]['LABORATORIO'] = $item->LABORATORIO;
            $json[$i]['UNIDAD_ALMACEN'] = $item->UNIDAD_ALMACEN;
            $json[$i]['006'] = $item['006'];
            $json[$i]['005'] = $item['005'];
            $json[$i]['PUNTOS'] = $item->PUNTOS;
            $json[$i]['PRECIO_FARMACIA'] = $item->PRECIO_FARMACIA;
            $json[$i]['IMPUESTO'] = $item->IMPUESTO;
            $json[$i]['REGLAS'] = $item->REGLAS;
            foreach($Info_Articulo as $row){
                if($item->ARTICULO == $row->product_sku){
                    $img = $row->product_image;
                    break;
                }
            }
            $json[$i]['IMG_URL'] = Storage::temporaryUrl('product/'.$img, now()->addMinutes(5));
           $i++;
        }
        //dd($json);
        return $json;
    }
}
