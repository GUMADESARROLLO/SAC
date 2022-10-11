<?php
namespace App\Http\Controllers;
use App\Models\GmvApi;

class GmvApiController extends Controller{

    public function Articulos()
    {  
        $Ruta = 'F22';
        
        $obj = GmvApi::Articulos($Ruta);
        
        return response()->json($obj);
    }

}