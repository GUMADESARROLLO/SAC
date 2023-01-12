<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

        $Lista_articulos_Favoritos = ArticuloFavoritos::all();
        
        foreach ($Lista_articulos_Favoritos as $rec){
            $articulos_favoritos[] = $rec->Articulo;
        }
        
        return Inventario::whereIn('ARTICULO',$articulos_favoritos)->get();
        
    }
}
