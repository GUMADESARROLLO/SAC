<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_bodegas";

    public static function getBodega($IdArticulo)
    {
        $Rol = 1;
        if ($Rol==1) {
            return Bodega::WHERE('ARTICULO',$IdArticulo)->get();
        } else {
            return Bodega::WHERE('ARTICULO',$IdArticulo)->whereIn('BODEGA',['002','005','006'])->get();
        }
        
        
        
    }
    
}
