<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Bodega extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_bodegas";

    public static function getBodega($IdArticulo)
    {
        $Rol = Session::get('rol');

        if ($Rol == 1 || $Rol == 2) {
            return Bodega::WHERE('ARTICULO',$IdArticulo)->get();
        } elseif($Rol == 9) {
            return Bodega::WHERE('ARTICULO',$IdArticulo)->whereNotIn('BODEGA',['001','003','006'])->get();
        }elseif($Rol == 10){
            return Bodega::WHERE('ARTICULO',$IdArticulo)->whereIn('BODEGA',['002'])->get();
        }
        
        
        
    }
    
}
