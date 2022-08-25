<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Http\Request;

class UsuarioRutas extends Model {
    protected $table = "tbl_rutas_asginadas";
    public static function AsignarRuta(Request $request) {
        if ($request->ajax()) {
            try {

                $Id         = $request->input('Id');
                $valor      = $request->input('valor');

                $obj = new UsuarioRutas();   
                $obj->RUTA        = $valor;                
                $obj->id_usuario  = $Id;
                
                $response = $obj->save();

                return response()->json($response);
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
    }
    public static function Remover(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id     = $request->input('id');
                
                $response = UsuarioRutas::where('id',$id)->delete();

                return response()->json($response);


            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
}
