<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Usuario extends Model {
    protected $table = "tbl_usuario";

    public function Detalles(){
        return $this->hasMany('App\Models\UsuarioRutas','id_usuario','id');
    }


    public static function getUsuarios()
    {
        return Usuario::where('activo','S')->get();
    }

    public static function SaveUsuario(Request $request) {
        if ($request->ajax()) {
            try {

                $usuario        = $request->input('usuario');
                $nombre         = $request->input('nombre');
                $passwprd       = $request->input('passwprd');
                $Estado         = $request->input('Estado');


                if ($Estado=="0") {
                    $obj = new Usuario();   
                    $obj->Usuario        = $usuario;                
                    $obj->nombre         = $nombre;
                    $obj->activo         = 'S';                 
                    $response = $obj->save();
                } else {
                    $response =   Usuario::where('id',  $Estado)->update([
                        "Usuario" => $usuario,
                        "nombre" => $nombre,
                    ]);
                }

                return response()->json($response);
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
    }
    public static function DeleteUsuario(Request $request)
    {
        if ($request->ajax()) {
            try {

                $id     = $request->input('id');
                
                $response =   Usuario::where('id',  $id)->update([
                    "activo" => 'N',
                ]);

                return response()->json($response);


            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }

    }
}