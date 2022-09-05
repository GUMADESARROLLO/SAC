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
        return Inventario::all();
    }
    public static function getArticulosSinFav()
    {
        $articulos_favoritos = array();

        $Lista_articulos_Favoritos = ArticuloFavoritos::all();
        
        foreach ($Lista_articulos_Favoritos as $rec){
            $articulos_favoritos[] = $rec->Articulo;
        }
        
        return Inventario::whereNotIn('ARTICULO',$articulos_favoritos)->get();
        
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
